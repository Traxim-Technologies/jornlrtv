<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
	 /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = array('title', 'description', 'plan' , 'amount');

    /**
	 * Save the unique ID 
	 *
	 *
	 */
    public function setUniqueIdAttribute($value){

		$this->attributes['unique_id'] = uniqid(str_replace(' ', '-', $value));

	}

}
