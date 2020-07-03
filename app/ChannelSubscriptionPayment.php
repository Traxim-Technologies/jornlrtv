<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChannelSubscriptionPayment extends Model
{
    //

     /**
     * Get the user record associated with the flag.
     */
    public function userDetails()
    {
        return $this->belongsTo('App\User','id');
    }

    /**
     * Get the channel record associated with the flag.
     */
    public function channelDetails() {
    	
        return $this->belongsTo('App\Channel', 'channel_id');
    }
}
