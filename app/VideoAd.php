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

        return $this->hasMany('App\AdsDetail', 'ads_id', 'id')->orderBy('video_time', 'asc');

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

        $array['ads_types'] = self::getTypeOfAds($this->types_of_ad);

        return $array;
    }

    public function getTypeOfAds($ad_type) {

        $types = [];

        if ($ad_type) {

            $exp = explode(',', $ad_type);

            $ads = [];

            foreach ($exp as $key => $value) {

                if ($value == PRE_AD) {
                
                    $ads[] =  'Pre Ad';

                }

                if ($value == POST_AD) {
                
                    $ads[] =  'Post Ad';

                }


                if ($value == BETWEEN_AD) {
                
                    $ads[] =  'Between Ad';

                }

            }

            // $types = implode(',', $ads);

            $types = $ads;

        }

        return $types;
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
            if (count($model->getAdDetails) > 0) {

                foreach ($model->getAdDetails as $key => $value) {

                   $value->delete();    

                }               
             
            }

        }); 

    }

}
