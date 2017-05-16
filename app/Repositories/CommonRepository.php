<?php


namespace App\Repositories;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Validator;
use App\User;
use Hash;
use Log;
use App\Channel;

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



	public static function channel_save($request) {

		if($request->id != '') {
			
            $validator = Validator::make( $request->all(), array(
                        'name' => 'required|max:255',
                        'picture' => 'mimes:jpeg,jpg,bmp,png',
                    )
                );
        } else {
            $validator = Validator::make( $request->all(), array(
                    'name' => 'required|max:255',
                    'picture' => 'required|mimes:jpeg,jpg,bmp,png',
                )
            );
        
        }
       
        if($validator->fails()) {

            $error_messages = implode(',', $validator->messages()->all());

            $response_array = ['success'=> false, 'error'=>$error_messages];

            // return back()->with('flash_errors', $error_messages);

        } else {

            if($request->id != '') {

                $channel = Channel::find($request->id);

                $message = tr('admin_not_channel');

                if($request->hasFile('picture')) {
                    Helper::delete_picture($channel->picture, "/uploads/channel/picture/");
                }

                if($request->hasFile('cover')) {
                    Helper::delete_picture($channel->cover, "/uploads/channel/cover/");
                }

            } else {
                $message = tr('admin_add_channel');
                //Add New User
                $channel = new Channel;

                $channel->is_approved = DEFAULT_TRUE;

               //  $channel->created_by = ADMIN;
            }

            $channel->name = $request->has('name') ? $request->name : '';

            $channel->description = $request->has('description') ? $request->description : '';

            $channel->user_id = $request->has('user_id') ? $request->user_id : '';

            $channel->status = DEFAULT_TRUE;

            $channel->unique_id =  $channel->name;
            
            if($request->hasFile('picture') && $request->file('picture')->isValid()) {
                $channel->picture = Helper::normal_upload_picture($request->file('picture'), "/uploads/channel/picture/");
            }

            if($request->hasFile('cover') && $request->file('cover')->isValid()) {
                $channel->cover = Helper::normal_upload_picture($request->file('cover'), "/uploads/channel/cover/");
            }

            $channel->save();

            if($channel) {
                // return back()->with('flash_success', $message);
                $response_array = ['success'=>true, 'message'=>$message];
            } else {
                // return back()->with('flash_error', tr('admin_not_error'));
                $response_array = ['success'=>false, 'error'=>tr('admin_not_error')];
            }

        }

        return response()->json($response_array, 200);
    
	}

}