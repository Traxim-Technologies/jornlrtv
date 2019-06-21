<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Redeem extends Model
{
// 	public function getTotalAttribute($value)
//     {
// // return floatval(number_format((float)$value, 2, '.', ''));

//         // return floatval(number_format($value, 2));
//     }
    public function user() {
    	return $this->belongsTo('App\User');
    }
}
