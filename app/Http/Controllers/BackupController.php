<?php

namespace App\Http\Controllers;


class BackupController extends Controller {

    /**
     *
     * Route :     Route::get('/user/upgrade/{id}', 'AdminController@user_upgrade')->name('user.upgrade');
     *
     *
     *
     */
    public function user_upgrade($id) {

        if($user = User::find($id)) {

            // Check the user is exists in moderators table

            if(!$moderator = Moderator::where('email' , $user->email)->first()) {

                $moderator_user = new Moderator;

                $moderator_user->name = $user->name;

                $moderator_user->email = $user->email;

                if($user->login_by == "manual") {

                    $moderator_user->password = $user->password; 

                    $new_password = "Please use you user login Pasword.";

                } else {

                    $new_password = time();
                    $new_password .= rand();
                    $new_password = sha1($new_password);
                    $new_password = substr($new_password, 0, 8);
                    $moderator_user->password = \Hash::make($new_password);
                }

                $moderator_user->picture = $user->picture;
                $moderator_user->mobile = $user->mobile;
                $moderator_user->address = $user->address;
                $moderator_user->save();

                $email_data = array();

                $subject = tr('user_welcome_title' , Setting::get('site_name'));
                $page = "emails.moderator_welcome";
                $email = $user->email;
                $email_data['name'] = $moderator_user->name;
                $email_data['email'] = $moderator_user->email;
                $email_data['password'] = \Hash::make($new_password);

                Helper::send_email($page,$subject,$email,$email_data);

                $moderator = $moderator_user;

            }

            if($moderator) {

                $user->is_moderator = 1;
                $user->moderator_id = $moderator->id;
                $user->save();

                $moderator->is_user = 1;
                $moderator->save();

                return back()->with('flash_warning',tr('admin_user_upgrade'));

            } else  {
                return back()->with('flash_error',tr('admin_not_error'));    
            }

        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }

    }

    /**
     *
     *
     * Route :  Route::any('/upgrade/disable', 'AdminController@user_upgrade_disable')->name('user.upgrade.disable');
     *
     *
     */
    public function user_upgrade_disable(Request $request) {

        if($moderator = Moderator::find($request->moderator_id)) {

            if($user = User::find($request->id)) {
                $user->is_moderator = 0;
                $user->save();
            }

            $moderator->save();

            return back()->with('flash_success',tr('admin_user_upgrade_disable'));

        } else {

            return back()->with('flash_error',tr('admin_not_error'));
        }
    
    }


   /**
    * Function Name: email_settings_process()
    *
    * Description: Email Setting Process
    *
    *  Route : Route::post('settings/email' , 'AdminController@email_settings_process')->name('email.settings.save');
    *
    * @return Html view page with coupon detail
    */
    public function email_settings_process(Request $request) {

        $email_settings = ['MAIL_DRIVER' , 'MAIL_HOST' , 'MAIL_PORT' , 'MAIL_USERNAME' , 'MAIL_PASSWORD' , 'MAIL_ENCRYPTION'];

        $admin_id = \Auth::guard('admin')->user()->id;


        foreach ($email_settings as $key => $data) {

            \Enveditor::set($data,$request->$data);
            
        }

        return redirect(route('clear-cache'))->with('flash_success' , tr('email_settings_success'));

    }

    public function decline_video($id) {
        
        $video = VideoTape::find($id);

        $video->is_approved = DEFAULT_FALSE;

        $video->save();

        if($video->is_approved == DEFAULT_TRUE){
            $message = tr('admin_not_video_approve');
        } else {
            $message = tr('admin_not_video_decline');
        }

        return back()->with('flash_success', $message);
    
    }
}
