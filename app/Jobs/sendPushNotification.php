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

use App\Repositories\PushNotificationRepository as PushRepo;

class sendPushNotification extends Job implements ShouldQueue {

    use InteractsWithQueue, SerializesModels;

    protected $register_ids;
    protected $title;
    protected $message;
    protected $push_data;
    protected $device_type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($register_ids , $title , $message , $push_data = [], $device_type = DEVICE_ANDROID) {

        $this->register_ids = $register_ids;
        $this->title = $title;
        $this->message = $message;
        $this->push_data = $push_data;
        $this->device_type = $device_type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        PushRepo::push_notification($this->register_ids, $this->title, $this->message, $this->push_data, $this->device_type);
           
    }
}
