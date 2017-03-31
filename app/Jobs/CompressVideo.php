<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use File;

use App\AdminVideo;

use Log; 

class CompressVideo extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $inputFile;
    protected $local_url;
    protected $videoId;
    protected $video_type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($inputFile, $local_url, $video_type, $videoId)
    {
        Log::info("Inside Construct");
       $this->inputFile = $inputFile;
       $this->local_url = $local_url;
       $this->videoId = $videoId;
       $this->video_type = $video_type;
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
        $video = AdminVideo::where('id', $this->videoId)->first();
        $attributes = readFileName($this->inputFile); 
        Log::info("attributes : ". print_r($attributes, true));
        if($attributes) {
            // Get Video Resolutions
            $resolutions = getVideoResolutions();
            $array_resolutions = [];
            foreach ($resolutions as $key => $solution) {
                $exp = explode('x', $solution->value);
                // Explode $solution value
                $getwidth = (count($exp) == 2) ? $exp[0] : 0;
                if ($getwidth < $attributes['width']) {
                    $dirPath = base_path('public/uploads/videos/'.$solution->value);
                    Log::info("Compressing Queue Videos : ".$dirPath);
                    $FFmpeg = new \FFmpeg;
                    $FFmpeg
                    ->input($this->inputFile)
                    ->size($solution->value)
                    ->vcodec('h264')
                    ->constantRateFactor('28')
                    ->output(base_path('public/uploads/videos/'.$solution->value.'/'.$this->local_url))
                    ->ready();

                    Log::info('Output'.base_path('public/uploads/videos/'.$solution->value.'/'.$this->local_url));
                    $array_resolutions[] = $solution->value;
                }
            }
            Log::info("Before saving Compress Video : ".$this->video_type);
            if ($this->video_type == MAIN_VIDEO) {
                $video->compress_status = 1;
                $video->video_resolutions = ($array_resolutions) ? implode(',', $array_resolutions) : null;
            } else {
                $video->trailer_compress_status = 1;
                $video->trailer_video_resolutions = ($array_resolutions) ? implode(',', $array_resolutions) : null;
            }
            if ($video->compress_status == 1 && $video->trailer_compress_status == 1) {
                $video->is_approved = DEFAULT_TRUE; 
            }
            Log::info("Compress Video : ".$this->video_type);
            Log::info("Compress Status : ".$video->compress_status);
            Log::info("Trailer Compress Status : ".$video->trailer_compress_status);
            $video->save();
        }
    }
}
