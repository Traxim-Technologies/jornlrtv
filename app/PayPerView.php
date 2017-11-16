<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PayPerView extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'video_id', 'payment_id', 'amount', 'expiry_date', 'status'
    ];

    /**
     * Get the video record associated with the flag.
     */
    public function videoTape()
    {
        return $this->hasOne('App\VideoTape', 'id', 'video_id');
    }

    /**
     * Get the video record associated with the flag.
     */
    public function userVideos()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
}
