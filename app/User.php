<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Helpers\Helper;

class User extends Authenticatable
{

    public $expiry_date;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','user_type','device_type','login_by',
        'picture','is_activated', 'timezone', 'verification_code' , 
        'verification_code_expiry','is_verified','age_limit', 'dob'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

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

    public function userPayment()
    {
        return $this->hasMany('App\UserPayment');
    }

    /**
     * Get the flag record associated with the user.
     */
    public function userFlag()
    {
        return $this->hasMany('App\Flag', 'user_id', 'id');
    }

    /**
     * Get the flag record associated with the user.
     */
    public function getChannel()
    {
        return $this->hasMany('App\Channel', 'user_id', 'id');
    }


    /**
     * Get the Redeems
     */

    public function userRedeem() {
        return $this->hasOne('App\Redeem' , 'user_id' , 'id');
    }

    /**
     * Get the Redeems
     */
    
    public function userRedeemRequests() {
        return $this->hasMany('App\RedeemRequest')->orderBy('status' , 'asc');
    }

    /**
     * Save the unique ID 
     *
     *
     */
    public function setUniqueIdAttribute($value){

        $this->attributes['unique_id'] = uniqid(str_replace(' ', '-', $value));

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
        static::deleting(function($user)
        {
            if (count($user->userHistory) > 0) {

                foreach($user->userHistory as $history)
                {
                    $history->delete();
                } 

            }

            if (count($user->userRating) > 0) {

                foreach($user->userRating as $rating)
                {
                    $rating->delete();
                } 

            }

            if (count($user->userWishlist) > 0) {

                foreach($user->userWishlist as $wishlist)
                {
                    $wishlist->delete();
                } 

            }

            if (count($user->userFlag) > 0) {

                foreach($user->userFlag as $flag)
                {
                    $flag->delete();
                }

            }


            if (count($user->userPayment) > 0) {

                foreach($user->userPayment as $payment)
                {
                    $payment->delete();
                } 
            }
        

            if (count($user->userHistory) > 0) {

                foreach($user->userHistory as $history)
                {
                    $history->delete();
                } 

            }


            if (count($user->getChannel) > 0) {

                foreach($user->getChannel as $channel) {
                    $channel->delete();
                } 

            }

            if (count($user->userRedeem) > 0) {

                $user->userRedeem()->delete();

            }

            if (count($user->userRedeemRequests) > 0) {

                $user->userRedeemRequests()->delete();

            }
        }); 

        static::creating(function ($model) {

            $model->generateEmailCode();

            $model->generateToken();

        });
    }


    /**
     * Generates Token and Token Expiry
     * 
     * @return bool returns true if successful. false on failure.
     */

    protected function generateEmailCode() {

        $this->attributes['verification_code'] = Helper::generate_email_code();

        $this->attributes['verification_code_expiry'] = Helper::generate_email_expiry();

        // $this->attributes['is_verified'] = 0;

        $this->attributes['status'] = DEFAULT_TRUE;

        return true;
    }

    /**
     * Generates Token and Token Expiry
     * 
     * @return bool returns true if successful. false on failure.
     */

    protected function generateToken() {

        $this->attributes['token'] = Helper::generate_token();

        $this->attributes['token_expiry'] = Helper::generate_token_expiry();

        return true;
    }

}
