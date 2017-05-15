<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserHistory extends Model
{
    public function videoTape() {
        return $this->belongsTo('App\VideoTape')->videoResponse();
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['video_tape'] = $this->videoTape;
        return $array;
    }
}
