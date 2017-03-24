<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminVideo extends Model
{
	public function videoImage()
    {
        return $this->hasMany('App\AdminVideoImage');
    }

    public function userHistory()
    {
        return $this->hasMany('App\UserHistory');
    }

    public function userRating()
    {
        return $this->hasMany('App\UserRating');
    }

    public function userWishlist()
    {
        return $this->hasMany('App\Wishlist');
    }


    public function category() {
        return $this->belongsTo('App\Category');
    }

    public function subCategory() {
        return $this->belongsTo('App\SubCategory');
    }

    public function genreName() {
        return $this->belongsTo('App\Genre');
    }

    /**
     * Get the flag record associated with the video.
     */
    public function userFlags()
    {
        return $this->hasMany('App\Flag', 'video_id', 'id');
    }

    /**
     * Get the pay per view record associated with the video.
     */
    public function userVideoSubscription()
    {
        return $this->hasMany('App\PayPerView', 'video_id', 'id');
    }

    public static function boot()
    {
        //execute the parent's boot method 
        parent::boot();

        //delete your related models here, for example
        static::deleting(function($video)
        {
            foreach($video->videoImage as $image)
            {
                $image->delete();
            } 

            foreach($video->userHistory as $history)
            {
                $history->delete();
            } 

            foreach($video->userRating as $rating)
            {
                $rating->delete();
            } 

            foreach($video->userWishlist as $wishlist)
            {
                $wishlist->delete();
            } 
        });	

    }
}
