<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    public function videoTape() {
        return $this->belongsTo('App\VideoTape')->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')->videoResponse();
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['video_tape'] = $this->videoTape;
        return $array;
    }
}
