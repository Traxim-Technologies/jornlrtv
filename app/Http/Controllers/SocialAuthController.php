<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Socialite;
use App\User;
use Hash;
use App\Helpers\Helper;

use App\Helpers\AppJwt;


class SocialAuthController extends Controller
{
    public function redirect(Request $request)
    {
        return Socialite::driver($request->provider)->redirect();
    }

    public function callback(Request $request ,$provider) {

    	if($provider == "twitter") {
    		
    		if($request->has('denied')) {
		    	
		    	return redirect('/')->with('flash_error' , 'Permission Denied');

    		}

    	} else {

	    	if(!$request->has('code') || $request->has('denied')) {
			    return redirect('/')->with('flash_error' , 'Permission Denied');
			}

		}

		$social_user = \Socialite::driver($provider)->stateless()->user();

		$user = new User;

		$check_user = [];

		$user->email = "social".uniqid()."@streamhash.com";

		// Check the social login has email 

		if($social_user->email) {

			// Check the record exists

			$check_user = User::where('email',$social_user->email)->first();

			if(!$check_user) {
				$user->email = $social_user->email;
			}

		} else {
			
			// Check social unique ID Already exists
			
			// $user = User::where('social_unique_id' , $social_user->id)->where('login_by' , $provider)->first();

			$check_user = User::where('social_unique_id' , $social_user->id)->first();

			if($social_user->email && !User::where('email',$social_user->email)->first()) {

				$user->email = $social_user->email;

			}
		}

		if($check_user) {

			$user = $check_user;
		}

		$user->social_unique_id = $social_user->id;

		$user->login_by = $provider;

		if($social_user->name) {
			$user->name = $social_user->name;
		} else {
			$user->name = "Dummy";
		}

		// Save Dummy details

		$user->picture =  asset('placeholder.png');

		if(in_array($provider, array('facebook','twitter'))) {

			if($social_user->avatar_original) {
				$user->picture = $social_user->avatar_original;
			}

		}

		$user->password = Hash::make($social_user->id);

        // $user->token = Helper::generate_token();
        $user->token_expiry = Helper::generate_token_expiry();

        $user->is_verified = 1;

		$user->save();

		if($user) {

        	// $user->token = AppJwt::create(['id' => $user->id, 'email' => $user->email, 'role' => "model"]);

	    	auth()->login($user);
		}

		$user->save();


	    return redirect()->route('user.dashboard');
	}
}
