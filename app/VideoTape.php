<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Auth;

class VideoTape extends Model
{

    public $channel_details;
    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVideoResponse($query) {

        return $query->select(
            'video_tapes.id as admin_video_id' ,
            'channels.id as channel_id' ,
            'channels.name as channel_name',
            'video_tapes.title',
            'video_tapes.description',
            'video_tapes.default_image',
            \DB::raw('DATE_FORMAT(video_tapes.publish_time , "%e %b %y") as publish_time'),
            'video_tapes.created_at',
            'video_tapes.video',
            'video_tapes.channel_id',
            'video_tapes.is_approved',
            'video_tapes.status',
            'video_tapes.watch_count',
            'video_tapes.duration',
            'video_tapes.video_publish_type',
            'video_tapes.publish_status',
            'video_tapes.ratings',
            'video_tapes.compress_status',
            'video_tapes.ad_status',
            'video_tapes.reviews',
            'video_tapes.amount',
            'video_tapes.is_banner',
            'video_tapes.banner_image',
            'video_tapes.redeem_count',
            'video_tapes.video_resolutions',
            'video_tapes.video_path',
            'video_tapes.created_at as video_created_time',
            \DB::raw('DATE_FORMAT(video_tapes.created_at , "%e %b %y") as video_date')
        );
    }


    public function getUserRatings() {

         return $this->hasMany('App\UserRating', 'video_tape_id', 'id');

    }

    public function getScopeUserRatings() {

         return $this->hasMany('App\UserRating', 'video_tape_id', 'admin_video_id');

    }

    public function getScopeVideoAds() {

         return $this->hasOne('App\VideoAd', 'video_tape_id', 'admin_video_id');

    }

    public function getVideoAds() {

         return $this->hasOne('App\VideoAd', 'video_tape_id', 'id');

    }

    public function getChannel() {

         return $this->hasOne('App\Channel', 'id', 'channel_id');

    }

    public function getVideoTapeImages() {

         return $this->hasMany('App\VideoTapeImage', 'video_tape_id', 'id');

    }

    public function getScopeVideoTapeImages() {

         return $this->hasMany('App\VideoTapeImage', 'video_tape_id', 'admin_video_id');

    }


    public function getScopeUserFlags() {

         return $this->hasMany('App\Flag', 'video_tape_id', 'admin_video_id');

    }


    public function toArray()
    {
        $array = parent::toArray();

        $array['tape_images'] = $this->getVideoTapeImages;

        $array['channel_details'] = $this->getChannel;

        $array['user_details'] = $this->getChannel->getUser;

        return $array;
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
            if (count($model->getVideoTapeImages) > 0) {

                foreach ($model->getVideoTapeImages as $key => $value) {

                   $value->delete();    

                }               

            }

            if (count($model->getVideoAds) > 0) {

                if(!is_null($model->getVideoAds)) {

                    $model->getVideoAds->delete();   

                }             

            }

        }); 

    }

}
