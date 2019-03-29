<?php

namespace App\Jobs;

use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Jobs\Job;
use App\Helpers\Helper;
use App\User;
use App\ChannelSubscription;
use Setting;
use Log;

class sendPushNotification extends Job implements ShouldQueue {

    use InteractsWithQueue, SerializesModels;

    protected $user_id;
    protected $push_message;
    protected $video_tape_id;
    protected $channel_id;
    protected $push_redirect_type; 
    protected $additional_data;
    protected $push_all_type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_id = PUSH_TO_ALL , $push_message ,$push_redirect_type = PUSH_REDIRECT_HOME , $video_tape_id = 0 , $channel_id = 0 , $additional_data = [] , $push_all_type = PUSH_TO_ALL) {

        $this->user_id = $user_id;
        $this->video_tape_id = $video_tape_id;
        $this->push_message = $push_message;
        $this->video_tape_id = $video_tape_id;
        $this->channel_id = $channel_id;
        $this->push_redirect_type = $push_redirect_type;
        $this->additional_data = $additional_data;
        $this->push_all_type = $push_all_type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        // Check the push notifictaion is enabled

        $push_notification = Setting::get('push_notification'); 

        if ($push_notification) {

            // Check whether Browser Key Set or Not

            if(Setting::get('browser_key')) {

                Log::info("Request Push Notification Queue Started");

                $data = ['video_tape_id' => $this->video_tape_id , 'channel_id' => $this->channel_id];

                $push_notification_data = ['success' => true , 'title' => $this->push_message,'type' => $this->push_redirect_type,'data' => $data];

                Log::info("***************************************************************");
                Log::info("*");
                Log::info("*");
                Log::info("*");
                Log::info("Push Data".print_r($push_notification_data , true));
                Log::info("*");
                Log::info("*");
                Log::info("*");
                Log::info("***************************************************************");

                // Check the push message is direct to user or all users 

                if($this->user_id == 0 ) {

                    if($this->push_all_type == PUSH_TO_CHANNEL_SUBSCRIBERS) {

                        $subscribers = ChannelSubscription::where('channel_id', $this->channel_id)->get();

                        foreach ($subscribers as $key => $subscriber) {
        
                            if($subscriber->getUser) {

                                $user_details = $subscriber->getUser;

                                Helper::send_notification($this->push_message , $user_details ,$push_notification_data);

                            }
                        
                        }

                    } else {

                        $user_list = User::where('push_status' , 1)->where('device_token' , '!=' , "")->get();

                        $user_details = array();

                        foreach ($user_list as $key => $user_details) {

                            Helper::send_notification($this->push_message , $user_details ,$push_notification_data);
                        }

                    }

                } else {
                    
                    $user_details = User::find($this->user_id);

                    if(count($user_details) > 0) {
                    
                        Helper::send_notification($this->push_message , $user_details ,$push_notification_data);

                    }

                }

            } else {
                Log::info("browser_key empty");
            }

        } else {
            Log::info('Push notifictaion is not enabled. Please contact admin');
        }
           
    }
}
