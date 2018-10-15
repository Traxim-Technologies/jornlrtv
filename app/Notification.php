<?php

namespace App;

use App\User;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    //

    public static function save_notification($video_id, $user_id) {

        \Log::info("Notification Inside "+$user_id);

    	//$users = User::where('is_verified', 1)->get();

    	// foreach ($users as $key => $value) {
    		
    		$model = new Notification;

    		$model->user_id = $user_id;

    		$model->video_tape_id = $video_id;

    		$model->status = 0;

    		$model->save();

    	//}

    }


    public function videoTape() {
        return $this->hasOne('App\VideoTape', 'id', 'video_tape_id');
    }
}
