<?php


namespace App\Repositories;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Validator;
use App\User;
use Hash;
use Log;

class CommonRepository {


	/**
	 * Usage : Register api - validation for the basic register fields 
	 *
	 */

	public static function basic_validation($data = [], &$errors = []) {

		$validator = Validator::make( $data,array(
                'device_type' => 'required|in:'.DEVICE_ANDROID.','.DEVICE_IOS.','.DEVICE_WEB,
                'device_token' => 'required',
                'login_by' => 'required|in:manual,facebook,google',
            )
        );
        
	    if($validator->fails()) {
	        $errors = implode(',', $validator->messages()->all());
	        return false;
	    }

	    return true;

	}

		/**
	 * Usage : Register api - validation for the social register or login 
	 *
	 */

	public static function social_validation($data = [] , &$errors = []) {

		$validator = Validator::make( $data,array(
                'social_unique_id' => 'required',
                'name' => 'required|max:255',
                'email' => 'required|email|max:255',
                'mobile' => 'digits_between:6,13',
                'picture' => '',
                'gender' => 'in:male,female,others',
            )
        );
        
	    if($validator->fails()) {
	        $errors = implode(',', $validator->messages()->all());
	        return false;
	    }

	    return true;

	}

	/**
	 * Usage : Register api - validation for the manual register fields 
	 *
	 */

	public static function manual_validation($data = [] , &$errors = []) {

		$validator = Validator::make( $data,array(
                'name' => 'required|max:255',
                'email' => 'required|email',
                'password' => 'required|min:6',
                'mobile' => 'digits_between:6,13',
                'picture' => 'mimes:jpeg,jpg,bmp,png',
            )
        );
        
	    if($validator->fails()) {
	        $errors = implode(',', $validator->messages()->all());
	        return false;
	    }

	    return true;

	}

	/**
	 * Usage : Login api - validation for the manual login fields 
	 *
	 */

	public static function email_validation($data = [] , &$errors = [] , $table = "users") {

		$validator = Validator::make( $data,[
                'email' => 'required|email|exists:'.$table.',email',
            ],
            [
            	'exists' => tr('email_id_not_found')
            ]
        );
        
	    if($validator->fails()) {
	        $errors = implode(',', $validator->messages()->all());
	        return false;
	    }

	    return true;

	}

	/**
	 * Usage : Login api - validation for the manual login fields 
	 *
	 */

	public static function login_validation($data = [] , &$errors = [] , $table = "users") {

		$validator = Validator::make( $data,[
                'email' => 'required|email|exists:'.$table.',email',
                'password' => 'required',
            ]
        );
        
	    if($validator->fails()) {
	        $errors = implode(',', $validator->messages()->all());
	        return false;
	    }

	    return true;

	}

	public static function change_password_validation($data = [] , &$errors = [] , $table = "users") {

		$validator = Validator::make( $data,[
                'password' => 'required|confirmed',
                'old_password' => 'required',
            ]
        );
        
	    if($validator->fails()) {
	        $errors = implode(',', $validator->messages()->all());
	        return false;
	    }

	    return true;

	}

}