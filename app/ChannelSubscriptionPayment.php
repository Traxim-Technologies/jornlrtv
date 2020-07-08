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
        return $this->belongsTo('App\User','user_id');
    }

    /**
     * Get the channel record associated with the flag.
     */
    public function channelDetails() {
    	
        return $this->belongsTo('App\Channel', 'channel_id');
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeCommonResponse($query) {

        return $query->leftJoin('channels', 'channel_subscription_payments.channel_id', '=', 'channels.id')
                    ->leftJoin('users', 'channel_subscription_payments.user_id', '=', 'users.id')
                    ->select(
                        'channels.id as channel_id', 
                        'channels.name as channel_name', 
                        'channels.picture as channel_image', 
                        'users.name as username',
                        'users.picture as user_picture',
                        'channel_subscription_payments.id as channel_subscription_payment_id', 
                        'channel_subscription_payments.*'
                    );

    }
}
