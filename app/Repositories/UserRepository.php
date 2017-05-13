<?php

namespace App\Repositories;

use App\Repositories\CommonRepository as CommonRepo;

use App\User;

use App\Helpers\Helper;

use Log;

use Validator;

class UserRepository {

    public static function request_validation($data = [] , &$errors = [] , $user) {

        $validator = Validator::make($data,
            array(
                'request_id' => 'required|integer|exists:requests,id,user_id,'.$user->id,
            ),
            array(
                'exists' => 'The :attribute doesn\'t belong to User:'.$user->name
            )
        );

        if($validator->fails()) {

            $errors = implode(',' ,$validator->messages()->all());

            return false;

        }

        return true;

    }

	public static function all() {

		return User::orderBy('created_at' , 'desc')->get();
		
	}

	public static function login($request) {

        $user = User::where('email', '=', $request->email)->first();

        // Validate the user credentials

        if($user->status) {

            if(\Hash::check($request->password, $user->password)) {

            	// Generate new tokens
                $user->token = Helper::generate_token();
                $user->token_expiry = Helper::generate_token_expiry();

                // Save device details
                $user->device_token = $request->device_token;
                $user->device_type = $request->device_type;
                $user->login_by = $request->login_by;

                $user->save();

                $data = User::userResponse($user->id)->first();

                if($data) {
                    $data = $data->toArray();
                }

                $response_array = ['success' => true , 'data' => $data];

                \Auth::loginUsingId($user->id);

            } else {
                $response_array = array( 'success' => false, 'error' => tr('invalid_email_password'), 'error_code' => 105 );
            }
        
        } else {
            $response_array = array('success' => false , 'error' => tr('account_disabled'),'error_code' => 144);
        }

        return $response_array;
	}

	public static function forgot_password($data = [] , &$errors = []) {

		$action = User::where('email' , $data->email)->first();

		if($action) {

			$new_password = Helper::generate_password();

	        $action->password = \Hash::make($new_password);

	        // $email_data = array();
	        // $subject = tr('forgot_email_title');
	        // $email_data['user']  = $action;
	        // $email_data['password'] = $new_password;
	        // $page = "emails.forgot-password";
	        // $email_send = Helper::send_email($page,$subject,$action->email,$email_data);

            $response_array = ['success' => true , 'message' => tr('mail_sent_success')];

	        $action->save();

		} else {
			$response_array = ['success' => false , 'error' => tr('mail_not_found') , 'error_code' => 101];
		}
        return $response_array;
	}

	public static function store($request) {

		$user = new User;

        $user->name = $request->has('name') ? $request->name : "";

        
		$user->email = $request->has('email') ? $request->email : "";
		$user->password = $request->has('password') ? \Hash::make($request->password) : "";
        
		$user->gender = $request->has('gender') ? $request->gender : "male";
		$user->mobile = $request->has('mobile') ? $request->mobile : "";
        $user->address = $request->has('address') ? $request->address : "";
        $user->description = $request->has('description') ? $request->description : "";

		$user->token = Helper::generate_token();
        $user->token_expiry = Helper::generate_token_expiry();

        $check_device_exist = User::where('device_token', $request->device_token)->first();

        if($check_device_exist){
            $check_device_exist->device_token = "";
            $check_device_exist->save();
        }

        if($request->has('timezone')) {
            $user->timezone = $request->timezone;
        }

        // $user->device_token = $request->has('device_token') ? $request->device_token : "";
        $user->device_type = $request->has('device_type') ? $request->device_type : "";
        $user->login_by = $request->has('login_by') ? $request->login_by : "";
		$user->social_unique_id = $request->has('social_unique_id') ? $request->social_unique_id : "";

        $user->picture = Helper::web_url().'/uploads/users/default-profile.jpg';

        $user->payment_mode = $request->payment_mode ? $request->payment_mode : 'cod';

        // Upload picture
        if($request->login_by == "manual") {
            if($request->hasFile('picture')) {
                $user->picture = Helper::upload_avatar('uploads/users',$request->file('picture'));
            }
        } else {
            if($request->has('picture')) {
                $user->picture = $request->picture;
            }

        }
        $user->login_by = $request->login_by ?  $request->login_by : "manual";

        $user->status = 1;
        $user->payment_mode = 'cod';

        $user->save();

        $name = $request->has('name') ? str_replace(' ', '-', $request->name) : "";
        
        $user->unique_id = uniqid($name);

       // $user->login_status = "user";
        $user->register_type = "user";

        $user->save();

        $response = [];

        if($user) {

            $response = User::userResponse($user->id)->first();

            if($response) {
                $response = $response->toArray();
            }

            /*if($admin_mail = get_admin_mail()) {

                $subject = tr('new_user_signup');
                $page = "emails.admin.user-welcome";
                $email = $admin_mail->email;
                
                Helper::send_email($page,$subject,$email,$user);
            }

            $subject = tr('new_user_signup');
            $page = "emails.user.welcome";
            $email = $user->email;
            
            Helper::send_email($page,$subject,$email,$user);*/
        }

        // Send welcome email to the new user:

        return $response;
	}

	public static function update($request , $user_id) {

        if($request->id) {
            $user_id = $request->id;
        }

		$user = User::find($user_id);

        $response = [];

        if($user) {

            if($request->has('name')) {
                $user->name = $request->name;
            }

            if($request->has('email')) {
                $user->email = $request->email;
            }

            if($request->has('description')) {
                $user->description = $request->description;
            }

            if($request->has('gender')) {
                $user->gender = $request->gender;
            }

            if($request->has('mobile')) {
                $user->mobile = $request->mobile;
            }

            if($request->has('timezone')) {
                $user->timezone = $request->timezone;
            }

            // Upload picture
            if ($request->hasFile('picture')) {
                Helper::delete_avatar('uploads/users',$user->picture); // Delete the old pic
                $user->picture = Helper::upload_avatar('uploads/users',$request->file('picture'));
            }

            // Upload picture
            if ($request->hasFile('cover')) {
                Helper::delete_avatar('uploads/users',$user->cover); // Delete the old pic
                $user->cover = Helper::upload_avatar('uploads/users',$request->file('cover'));
            }

            $user->login_by = $request->login_by ?  $request->login_by : $user->login_by;
            $user->save();

            $response = $user->userResponse($user->id)->first()->toArray();
        }

        return $response;

	}

	public static function delete($data = []) {

	}

	public static function find($data = []) {

	}

	public static function findBy($field , $value) {

	}

	public static function paginate($take , $skip) {

	}

	
}