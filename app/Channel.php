<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    //

    public function videoTape() {
        return $this->hasMany('App\VideoTape')->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')->videoResponse();
    }

    /**
     * Get the video record associated with the flag.
     */
    public function getUser()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
    /**
     * Save the unique ID 
     *
     *
     */
    public function setUniqueIdAttribute($value){

        $this->attributes['unique_id'] = uniqid(str_replace(' ', '-', $value));

    }

    public function getVideos() {
        return $this->hasMany('App\VideoTape')->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')->videoResponse()->where('status', DEFAULT_TRUE)->where('is_approved', DEFAULT_TRUE);
    }

    public function getVideoTape() {
        return $this->hasMany('App\VideoTape');
    }


    public function getChannelSubscribers() {
        return $this->hasMany('App\ChannelSubscription');
    }

    /**
     * Boot function for using with User Events
     *
     * @return void
     */

    public static function boot()
    {
        //execute the parent's boot method 
        parent::boot();

        //delete your related models here, for example
        static::deleting(function($model)
        {
            if (count($model->getVideoTape) > 0) {

                foreach ($model->getVideoTape as $key => $value) {

                   $value->delete();    

                }

            }

        }); 

    }


}
