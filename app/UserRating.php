<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRating extends Model
{
    public function adminVideo() {
        return $this->belongsTo('App\VideoTape');
    }

     public function toArray()
    {
        $array = parent::toArray();

        $array['diff_human_time'] = ($this->created_at) ? $this->created_at->diffForHumans() : 0;

        return $array;
    }



}
