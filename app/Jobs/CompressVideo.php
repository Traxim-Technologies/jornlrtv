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
        Log::info("Inside Queue Videos : ". 'Success');
        // Load Video Model
        $video = VideoTape::where('id', $this->videoId)->first();
        $attributes = readFileName($this->inputFile); 
        Log::info("attributes : ". print_r($attributes, true));
        if($attributes) {
            // Get Video Resolutions
            $resolutions = getVideoResolutions();
            $array_resolutions = $video_resize_path = $pathnames = [];
            foreach ($resolutions as $key => $solution) {
                $exp = explode('x', $solution->value);
                Log::info("Resoltuion : ". print_r($exp, true));
                // Explode $solution value
                $getwidth = (count($exp) == 2) ? $exp[0] : 0;
                if ($getwidth < $attributes['width']) {
                    $FFmpeg = new \FFmpeg;
                    $FFmpeg
                    ->input($this->inputFile)
                    ->size($solution->value)
                    ->vcodec('h264')
                    ->constantRateFactor('28')
                    ->output(public_path().'/uploads/videos/original/'.$solution->value.$this->local_url)
                    ->ready();

                    Log::info('Output'.public_path().'/uploads/videos/'.$solution->value.$this->local_url);
                    $array_resolutions[] = $solution->value;
                    Log::info('Url'.Helper::web_url().'/uploads/videos/'.$solution->value.$this->local_url);
                    $video_resize_path[] = Helper::web_url().'/uploads/videos/'.$solution->value.$this->local_url;
                    $pathnames[] = $solution->value.$this->local_url;
                }
            }

            $video->video_resolutions = ($array_resolutions) ? implode(',', $array_resolutions) : null;
            $video->video_path = ($video_resize_path) ? implode(',', $video_resize_path) : null;

            $video->status = DEFAULT_TRUE;

            $video->compress_status = DEFAULT_TRUE; 

            Log::info("Array Resolutions : ".print_r($array_resolutions, true));
            if ($array_resolutions) {
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
            }

            $video->save();
        }
    }
}
