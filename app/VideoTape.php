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
            'video_tapes.ratings'
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

    public function getChannel() {

         return $this->hasOne('App\Channel', 'id', 'channel_id');

    }

    public function getVideoTapeImages() {

         return $this->hasMany('App\VideoTapeImage', 'video_tape_id', 'id');

    }

    public function getScopeVideoTapeImages() {

         return $this->hasMany('App\VideoTapeImage', 'video_tape_id', 'admin_video_id');

    }


    public function toArray()
    {
        $array = parent::toArray();

        $array['tape_images'] = $this->getVideoTapeImages;

        $array['channel_details'] = $this->getChannel;

        $array['user_details'] = $this->getChannel->getUser;

        return $array;
    }

}
