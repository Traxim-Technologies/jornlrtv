<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    public function userDetails() {

        return $this->belongsTo('App\User', 'user_id');
    }

        /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeCommonResponse($query) {

        return $query->select(
            'referrals.id as referral_id' ,
            'referrals.user_id as user_id' ,
            'referrals.parent_user_id as parent_user_id' ,
            'referrals.user_referrer_id as user_referrer_id' ,
            'referrals.referral_code as referral_code' ,
            'referrals.status',          
            \DB::raw('DATE_FORMAT(referrals.created_at , "%e %b %y") as created_at'),
            \DB::raw('DATE_FORMAT(referrals.updated_at , "%e %b %y") as updated_at')
        );
    
    }

}
