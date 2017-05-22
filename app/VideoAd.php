<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VideoAd extends Model
{
    //

    public function getVideoTape() {

        return $this->hasOne('App\VideoTape', 'id', 'video_tape_id');

    }


    public function getAdDetails() {

        return $this->hasMany('App\AdsDetail', 'ads_id', 'id');

    }


    public function getPreAdDetail() {

        return $this->hasOne('App\AdsDetail', 'ads_id', 'id')->where('ad_type', PRE_AD);

    }

    public function getPostAdDetail() {

        return $this->hasOne('App\AdsDetail', 'ads_id', 'id')->where('ad_type', POST_AD);

    }

    public function getBetweenAdDetails() {

        return $this->hasMany('App\AdsDetail', 'ads_id', 'id')->where('ad_type', BETWEEN_AD);

    }

    public function toArray()
    {
        $array = parent::toArray();

        $array['ad_details'] = $this->getAdDetails;

        $array['post_ad'] = $this->getPostAdDetail;

        $array['pre_ad'] = $this->getPreAdDetail;

        $array['between_ad'] = $this->getBetweenAdDetails;

        return $array;
    }
}
