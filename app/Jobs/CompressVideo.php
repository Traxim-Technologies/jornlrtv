<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use File;

use App\VideoTape;

use App\Helpers\Helper;

use Log; 

use App\Jobs\SubscriptionMail;

use App\Jobs\sendPushNotification;

class CompressVideo extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $inputFile;
    protected $local_url;
    protected $videoId;
    protected $video_type;
    protected $file_name;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($inputFile, $local_url, $videoId, $file_name)
    {
        Log::info("Inside Construct");
       $this->inputFile = $inputFile;
       $this->local_url = $local_url;
       $this->videoId = $videoId;
       $this->file_name = $file_name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Load Video Model
        $video = VideoTape::where('id', $this->videoId)->first();

        if ($video) {

          $attributes = readFileName($this->inputFile); 
        
          if($attributes) {

              // Get Video Resolutions
              $resolutions = getVideoResolutions();

              $array_resolutions = $video_resize_path = $pathnames = [];

              foreach ($resolutions as $key => $solution) {

                  $exp = explode('x', $solution->value);

                  // Explode $solution value
                  $getwidth = (count($exp) == 2) ? $exp[0] : 0;

                  if ($getwidth < $attributes['width']) {

                      $FFmpeg = new \FFmpeg;
                      $FFmpeg
                      ->input($this->inputFile)
                      ->size($solution->value)
                      ->vcodec('h264')
                      ->constantRateFactor('28')
                      ->output(public_path().'/uploads/videos/'.$solution->value.$this->local_url)
                      ->ready();

                      
                      $array_resolutions[] = $solution->value;
                      
                      $video_resize_path[] = Helper::web_url().'/uploads/videos/'.$solution->value.$this->local_url;

                      $pathnames[] = $solution->value.$this->local_url;

                  }
              
              }

              $video->video_resolutions = ($array_resolutions) ? implode(',', $array_resolutions) : null;

              $video->video_path = ($video_resize_path) ? implode(',', $video_resize_path) : null;

              $video->status = DEFAULT_TRUE;

              $video->compress_status = DEFAULT_TRUE; 

              if ($array_resolutions) {

                  File::isDirectory(public_path().'/uploads/smil') or File::makeDirectory(public_path().'/uploads/smil', 0777, true, true);

                  if (\Setting::get('streaming_url')) {

                    $myfile = fopen(public_path().'/uploads/smil/'.$this->file_name.'.smil', "w");
                    $txt = '<smil>
                      <head>
                        <meta base="'.\Setting::get('streaming_url').'" />
                      </head>
                      <body>
                        <switch>';
                        $txt .= '<video src="'.$this->local_url.'" height="'.$attributes['height'].'" width="'.$attributes['width'].'" />';
                        foreach ($pathnames as $i => $value) {
                            $resoltionsplit = explode('x', $array_resolutions[$i]);
                            if (count($resoltionsplit))
                            $txt .= '<video src="'.$value.'" height="'.$resoltionsplit[1].'" width="'.$resoltionsplit[0].'" />';
                        }
                     $txt .= '</switch>
                      </body>
                    </smil>';
                    fwrite($myfile, $txt);
                    fclose($myfile);

                  } else {

                      Log::info("Streaming file url not configured...!");

                  }

              }

              $video->save();

              if ($video) {

                   // Channel Subscription email

                    dispatch(new SubscriptionMail($video->channel_id, $video->id));

                    $push_message = $video->title;

                    // dispatch(new sendPushNotification(PUSH_TO_ALL , $push_message , PUSH_REDIRECT_SINGLE_VIDEO , $video->id, $video->channel_id, [] , PUSH_TO_CHANNEL_SUBSCRIBERS));

                    if(Setting::get('push_notification')) {

                      $subscribers = ChannelSubscription::where('channel_id', $video->channel_id)->get();

                      foreach ($subscribers as $key => $subscriber) {
      
                          if($subscriber->getUser) {

                              $user_details = $subscriber->getUser;


                              if($user_details->push_status == YES && ($user_details->device_token != '')) {

                                  $push_data = ['type' => PUSH_REDIRECT_SINGLE_VIDEO, 'video_id' => $video->id];

                                  PushRepo::push_notification($user_details->device_token, $title, $content, $push_data, $user_details->device_type, $is_user = YES);
                              }

                          }
                      
                      }

                  }

              }

              Log::info("Video status saved..!");

          } else {

              Log::info("Atttributes not present...!".print_r($attributes, true));

          }

        } else {

            Log::info("Video not found...!".$this->videoId);
        }
    }
}
