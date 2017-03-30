<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use File;

class CompressVideo extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $inputFile;
    protected $local_url;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($inputFile, $local_url)
    {
       $this->inputFile = $inputFile;
       $this->local_url = $local_url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("Inside Queue Videos : ". 'Success');
        $attributes = readFileName($this->inputFile); 
        if($attributes) {
            // Get Video Resolutions
            $resolutions = getVideoResolutions();

            foreach ($resolutions as $key => $solution) {
                $exp = explode('x', $solution->value);
                // Explode $solution value
                $getwidth = (count($exp) == 2) ? $exp[0] : 0;
                if ($getwidth <= $attributes['width']) {
                    $dirPath = base_path('public/uploads/videos/'.$solution->value);
                    if (!is_dir($dirPath)) {
                        File::makeDirectory($dirPath, $mode = 0777, true, true);
                    }
                    Log::info("Compressing Queue Videos : ".$solution->value);
                    $FFmpeg = new \FFmpeg;
                    $FFmpeg
                    ->input($this->inputFile)
                    ->size($solution->value)
                    ->vcodec('h264')
                    ->constantRateFactor('28')
                    ->output(base_path('public/uploads/videos/'.$solution->value.'/'.$this->local_url))
                    ->ready();
                }
            }
        }
    }
}
