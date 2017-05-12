<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    public function videoTape() {
        return $this->belongsTo('App\VideoTape')->videoResponse();
    }
}
