<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    //

    public function videoTape() {
        return $this->hasMany('App\VideoTape')->videoResponse();
    }

    /**
     * Get the video record associated with the flag.
     */
    public function getUser()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
    /**
     * Save the unique ID 
     *
     *
     */
    public function setUniqueIdAttribute($value){

        $this->attributes['unique_id'] = uniqid(str_replace(' ', '-', $value));

    }

}
