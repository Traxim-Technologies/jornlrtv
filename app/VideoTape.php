<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Auth;

use App\Helpers\Helper;

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
            'video_tapes.id as video_tape_id' ,
            'channels.id as channel_id' ,
            'channels.user_id as channel_created_by',
            'channels.name as channel_name',
            'channels.picture as channel_picture',
            'video_tapes.title',
            'video_tapes.description',
            'video_tapes.default_image',
            \DB::raw('DATE_FORMAT(video_tapes.publish_time , "%e %b %y") as publish_time'),
            'video_tapes.created_at',
            'video_tapes.video',
            'video_tapes.is_approved',
            'video_tapes.status',
            'video_tapes.watch_count',
            'video_tapes.unique_id',
            'video_tapes.duration',
            'video_tapes.video_publish_type',
            'video_tapes.publish_status',
            'video_tapes.compress_status',
            'video_tapes.ad_status',
            'video_tapes.reviews',
            'video_tapes.amount',
            'video_tapes.type_of_user',
            'video_tapes.type_of_subscription',
            'video_tapes.is_banner',
            'video_tapes.banner_image',
            'video_tapes.redeem_count',
            'video_tapes.video_resolutions',
            'video_tapes.video_path',
            'video_tapes.created_at as video_created_time',
            'video_tapes.subtitle',
            'video_tapes.age_limit',
            'video_tapes.user_ratings',
            'video_tapes.video_type',
            \DB::raw('DATE_FORMAT(video_tapes.created_at , "%e %b %y") as video_date'),
            \DB::raw('(CASE WHEN (user_ratings = 0) THEN ratings ELSE user_ratings END) as ratings')
        );
    
    }


    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeShortVideoResponse($query) {

        return $query->select(
            'video_tapes.default_image as video_image',
            'channels.picture as channel_image',
            'video_tapes.title',
            'channels.name as channel_name',
            'video_tapes.watch_count',
            'video_tapes.duration',
            'video_tapes.video',
            'video_tapes.id as video_tape_id' ,
            'channels.id as channel_id' ,
            'video_tapes.description',
            'video_tapes.age_limit',
            'video_tapes.is_approved',
            'video_tapes.status',
            'video_tapes.subtitle',
            \DB::raw('DATE_FORMAT(video_tapes.created_at , "%e %b %y") as publish_time')
            
        );
    
    }

    public function setUniqueIdAttribute($value){

        $this->attributes['unique_id'] = uniqid(str_replace(' ', '-', $value));

    }


    public function getUserRatings() {

         return $this->hasMany('App\UserRating', 'video_tape_id', 'id');

    }

    public function getScopeUserRatings() {

         return $this->hasMany('App\UserRating', 'video_tape_id', 'video_tape_id');

    }

    public function getScopeVideoAds() {

         return $this->hasOne('App\VideoAd', 'video_tape_id', 'video_tape_id');

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

         return $this->hasMany('App\VideoTapeImage', 'video_tape_id', 'video_tape_id');

    }


    public function getScopeLikeCount() {

        return $this->hasMany('App\LikeDislikeVideo', 'video_tape_id', 'video_tape_id')->where('like_status', DEFAULT_TRUE);

    }

    public function getScopeDisLikeCount() {

        return $this->hasMany('App\LikeDislikeVideo', 'video_tape_id', 'video_tape_id')->where('dislike_status', DEFAULT_TRUE);

    }

    public function getLikeCount() {

        return $this->hasMany('App\LikeDislikeVideo', 'video_tape_id', 'id')->where('like_status', DEFAULT_TRUE);

    }

    public function getDisLikeCount() {

        return $this->hasMany('App\LikeDislikeVideo', 'video_tape_id', 'id')->where('dislike_status', DEFAULT_TRUE);

    }
    
     public function getUserFlags() {

         return $this->hasMany('App\Flag', 'video_tape_id', 'id');


    }


    public function getScopeUserFlags() {

         return $this->hasMany('App\Flag', 'video_tape_id', 'video_tape_id');

    }


    public function toArray()
    {
        $array = parent::toArray();

        /*$array['tape_images'] = $this->getVideoTapeImages;

        $array['channel_details'] = $this->getChannel;

        $array['user_details'] = ($this->getChannel) ? $this->getChannel->getUser : [];*/

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

                    if ($value->image) {

                        Helper::delete_picture($value->image, "/uploads/images/");

                    }

                   $value->delete();    

                }               

            }

            if (count($model->getVideoAds) > 0) {

                if(!is_null($model->getVideoAds)) {

                    $model->getVideoAds->delete();   

                }             

            }

             if (count($model->getUserFlags) > 0) {

                foreach ($model->getUserFlags as $key => $value) {

                   $value->delete();    

                }               
            

            }

             if (count($model->getUserRatings) > 0) {

                foreach ($model->getUserRatings as $key => $value) {

                   $value->delete();    

                }               
            

            }

        }); 

    }

}
