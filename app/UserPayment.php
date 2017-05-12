<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPayment extends Model
{
    public function adminVideo() {
        return $this->belongsTo('App\VideoTape');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function getSubscription() {
        return $this->hasOne('App\Subscription', 'id', 'subscription_id');
    }
}
