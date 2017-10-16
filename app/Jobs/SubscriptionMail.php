<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


use App\ChannelSubscription;

use File;

use App\VideoTape;

use App\Helpers\Helper;

use Log; 

class SubscriptionMail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $channel_id;
    protected $video_id;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($channel_id,$video_id)
    {
        Log::info("Inside Construct");
       $this->channel_id = $channel_id;
       $this->video_id = $video_id;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("Inside Queue Videos : ". 'Success');
        
        $subscribers = ChannelSubscription::where('channel_id', $this->channel_id)->get();

        foreach ($subscribers as $key => $subscriber) {
            
            if($subscriber->getUser) {

                $user = $subscriber->getUser;


                $subject = tr('uploaded_new_video');
                $email_data['subscriber'] = $subscriber;
                $email_data['video_id'] = $this->video_id;
                $page = "emails.subscription_mail";
                $email = $user ? $user->email : '';

                if ($user) {

                    Helper::send_email($page,$subject,$email,$email_data);

                }   
            }
        }
    }
}