<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Requests;

use App\Helpers\Helper;

use App\VideoTape;

use App\User;

use App\Settings;

use Log;

use DB;

use Validator;

use App\Page;

use App\Admin;

use Auth;

use App\ChatMessage;

use App\LiveVideo;

use Setting;

class ApplicationController extends Controller {

    /**
     * Used to generate index.php file to avoid uploads folder access
     *
     */

    public function generate_index(Request $request) {

        if($request->has('folder')) {

            Helper::generate_index_file($request->folder);

        }

        return response()->json(['success' => true , "message" => 'successfully']);

    }

    public function channel_create() {
        return view('ui.channels.create');
    }
    public function ui() {
        return view('ui.index');
    }

    public function channel_view() {
        return view('ui.channels.view');
    }

    public function subscriptions() {
        return view('ui.subscriptions.index');
    }

    public function subscription_view() {
        return view('ui.subscriptions.view');
    }

    public function video_create() {
        return view('ui.videos.create');
    } 

    public $expiry_date = "";

    public function test() {

        Log::info("MAIN VIDEO MOBILE");

        // $subject = tr('user_welcome_title');
        // $email_data = User::find(3);
        // $page = "emails.welcome";
        // $email = "test@mail.com";

        // return view($page)->with('email_data' , $email_data);
        // $result = Helper::send_email($page,$subject,$email,$email_data);

    }


    public function about(Request $request) {

        $about = Page::where('type', 'about')->first();

        // dd($about);

        return view('static.about-us')->with('about' , $about)
                        ->with('page' , 'about')
                        ->with('subPage' , '');

    }

    public function privacy(Request $request) {

        $page = Page::where('type', 'privacy')->first();;

        // dd($page);
        return view('static.privacy')->with('data' , $page)
                        ->with('page' , 'conact_page')
                        ->with('subPage' , '');

    }

    public function terms(Request $request) {

        $page = Page::where('type', 'terms')->first();

        // dd($page);
        return view('static.terms')->with('data' , $page)
                        ->with('page' , 'terms_and_condition')
                        ->with('subPage' , '');

    }


    public function cron_publish_video() {
        
        Log::info('cron_publish_video');

        $admin = Admin::first();
        
        $timezone = 'Asia/Kolkata';

        if($admin) {

            if ($admin->timezone) {

                $timezone = $admin->timezone;

            } 

        }

        $date = convertTimeToUSERzone(date('Y-m-d H:i:s'), $timezone);

        $videos = VideoTape::where('publish_time' ,'<=' ,$date)
                        ->where('publish_status' , 0)->get();
        foreach ($videos as $key => $video) {
            Log::info('Change the status');
            $video->publish_status = 1;
            $video->save();
        }
    
    }

    public function send_notification_user_payment(Request $request) {

        Log::info("Notification to User for Payment");

        $time = date("Y-m-d");
        // Get provious provider availability data
        $query = "SELECT *, TIMESTAMPDIFF(SECOND, '$time',expiry_date) AS date_difference
                  FROM user_payments";

        $payments = DB::select(DB::raw($query));

        Log::info(print_r($payments,true));

        if($payments) {
            foreach($payments as $payment){
                if($payment->date_difference <= 864000)
                {
                    // Delete provider availablity
                    Log::info('Send mail to user');

                    if($user = User::find($payment->user_id)) {

                        Log::info($user->email);


                        $email_data = array();
                        // Send welcome email to the new user:
                        $subject = tr('payment_notification');
                        $email_data['id'] = $user->id;
                        $email_data['name'] = $user->name;
                        $email_data['expiry_date'] = $payment->expiry_date;
                        $email_data['status'] = 0;
                        $page = "emails.payment-expiry";
                        $email = $user->email;
                        $result = Helper::send_email($page,$subject,$email,$email_data);

                        \Log::info("Email".$result);
                    }
                }
            }
            Log::info("Notification to the User successfully....:-)");
        } else {
            Log::info(" records not found ....:-(");
        }
    
    }

    public function user_payment_expiry(Request $request) {

        Log::info("user_payment_expiry");

        $time = date("Y-m-d");
        // Get provious provider availability data
        $query = "SELECT *, TIMESTAMPDIFF(SECOND, '$time',expiry_date) AS date_difference
                  FROM user_payments";

        $payments = DB::select(DB::raw($query));

        Log::info(print_r($payments));

        if($payments) {
            foreach($payments as $payment){
                if($payment->date_difference < 0)
                {
                    // Delete provider availablity
                    Log::info('Send mail to user');

                    $email_data = array();
                    
                    if($user = User::find($payment->user_id)) {
                        $user->user_type = 0;
                        $user->save();
                        // Send welcome email to the new user:
                        $subject = tr('payment_notification');
                        $email_data['id'] = $user->id;
                        $email_data['username'] = $user->name;
                        $email_data['expiry_date'] = $payment->expiry_date;
                        $email_data['status'] = 1;
                        $page = "emails.payment-expiry";
                        $email = $user->email;
                        $result = Helper::send_email($page,$subject,$email,$email_data);

                        \Log::info("Email".$result);
                    }
                }
            }
            Log::info("Notification to the User successfully....:-)");
        } else {
            Log::info(" records not found ....:-(");
        }
    
    }

    public function search_video(Request $request) {

        if (Auth::check()) {
            $request->request->add([ 
                    'id' => \Auth::user()->id,
                    'token' => \Auth::user()->token,
                    'device_token' => \Auth::user()->device_token,
                    'age'=>\Auth::user()->age_limit,
                ]);
        }

        $validator = Validator::make(
            $request->all(),
            array(
                'term' => 'required',
            ),
            array(
                'exists' => 'The :attribute doesn\'t exists',
            )
        );
    
        if ($validator->fails()) {

            $error_messages = implode(',', $validator->messages()->all());
            $response_array = array('success' => false, 'error' => Helper::get_error_message(101), 'error_code' => 101, 'error_messages'=>$error_messages);

            return false;
        
        } else {

            $q = $request->term;

            \Session::set('user_search_key' , $q);

            $items = array();
            
            $results = Helper::search_video($request, $q);

            $live_videos = Helper::live_video_search($request, $q);

            if($results) {

                foreach ($results as $i => $key) {

                    $check = $i+1;

                    if($check <=10) {

                        //live_video_title
     
                        array_push($items,['label'=>$key->title,'category'=>tr('uploaded_video')]);

                    } if($check == 10 ) {

                        array_push($items,['label'=>"View All" ,'category'=>tr('uploaded_video')]);

                    }
                
                }

            }

            if($live_videos) {

                foreach ($live_videos as $idx => $value) {

                    $check = $idx+1;

                    if($check <=10) {

                        //live_video_title
     
                        array_push($items,['label'=>$value->title,'category'=>tr('live_video_title')]);

                    } if($check == 10 ) {

                        array_push($items,['label'=>"View All" ,'category'=>tr('live_video_title')]);

                    }
                
                }

            }

            return response()->json($items);
        }     
    
    }

    public function search_all(Request $request) {

         if (Auth::check()) {
            $request->request->add([ 
                    'id' => \Auth::user()->id,
                    'token' => \Auth::user()->token,
                    'device_token' => \Auth::user()->device_token,
                    'age'=>\Auth::user()->age_limit,
                ]);
        }

        $validator = Validator::make(
            $request->all(),
            array(
                'key' => '',
            ),
            array(
                'exists' => 'The :attribute doesn\'t exists',
            )
        );
    
        if ($validator->fails()) {

            $error_messages = implode(',', $validator->messages()->all());
            $response_array = array('success' => false, 'error' => Helper::get_error_message(101), 'error_code' => 101, 'error_messages'=>$error_messages);
        
        } else {

            if($request->has('key')) {
                $q = $request->key;    
            } else {
                $q = \Session::get('user_search_key');
            }

            if($q == "all") {
                $q = \Session::get('user_search_key');
            }

            $videos = Helper::search_video($request, $q,1);

            $live_videos = Helper::live_video_search($request, $q,1);

            return view('user.search-result')->with('key' , $q)
                    ->with('videos' , $videos)
                    ->with('live_videos', $live_videos)
                    ->with('page' , "")
                    ->with('subPage' , "");
        }     
    
    }

    /**
     * To verify the email from user
     *
     */

    public function email_verify(Request $request) {

        // Check the request have user ID

        if($request->id) {

            // Check the user record exists

            if($user = User::find($request->id)) {

                // dd($user->is_verified);

                // Check the user already verified

                if(!$user->is_verified) {

                    // Check the verification code and expiry of the code

                    $response = Helper::check_email_verification($request->verification_code , $user, $error);

                    if($response) {

                        $user->is_verified = true;
                        $user->save();

                        \Auth::loginUsingId($request->id);

                        return redirect(route('user.profile'))->with('flash_success' , "Email verified successfully!!!");

                    } else {

                        return redirect(route('user.login.form'))->with('flash_error' , $error);
                    }

                } else {

                    \Log::info('User Already verified');

                    \Auth::loginUsingId($request->id);

                    return redirect(route('user.dashboard'));
                }

            } else {
                return redirect(route('user.login.form'))->with('flash_error' , "User Record Not Found");
            }

        } else {

            return redirect(route('user.login.form'))->with('flash_error' , "Something Missing From Email verification");
        }
    
    }

    public function admin_control() {

        return view('admin.settings.control')->with('page', tr('admin_control'));
        
    }

    public function save_admin_control(Request $request) {

        $model = Settings::get();
        
        foreach ($model as $key => $value) {

            if($value->key == 'admin_theme_control') {
                $value->value = $request->admin_theme_control;
            } else if ($value->key == 'admin_delete_control') {
                $value->value = $request->admin_delete_control;
            } else if ($value->key == 'is_spam') {
                $value->value = $request->is_spam;
            } else if ($value->key == 'is_subscription') {
                $value->value = $request->is_subscription;
            } else if ($value->key == 'redeem_control') {
                $value->value = $request->redeem_control;
            } else if ($value->key == 'is_banner_video') {
                $value->value = $request->is_banner_video;
            } else if ($value->key == 'is_banner_ad') {
                $value->value = $request->is_banner_ad;
            } else if ($value->key == 'is_vod') {
                $value->value = $request->is_vod;
            } else if ($value->key == 'create_channel_by_user') {
                $value->value = $request->create_channel_by_user;
            } else if ($value->key == 'is_default_paid_user') {
                $value->value = $request->is_default_paid_user;
            } 
            
            $value->save();
        }
        return back()->with('flash_success' , tr('settings_success'));
    }


    public function embed_video(Request $request) {

        $model = VideoTape::where('unique_id', $request->u_id)->first();

        if ($model) {

            return view('embed_video')->with('model', $model);

        } else {

            return response()->view('errors.404', [], 404);

        }

    }

    public function set_session_language($lang) {

        $locale = \Session::put('locale', $lang);

        return back()->with('flash_success' , tr('session_success'));
    }


    public function message_save(Request $request) {

        \Log::info("message data".print_r($request->all() , true));

        $validator = \Validator::make($request->all(), [
                "live_video_id" => "required|integer",
                "user_id" => "required|integer",
                "live_video_viewer_id" => "",
                "type" => "required|in:uv,vu",
                "message" => "required",
            ]);

        if($validator->fails()) {
            $error = implode(',', $validator->messages()->all());
            return response()->json(['success' => false , 'error' => $error]);
        }

        ChatMessage::create($request->all());

        return response()->json(['success' => 'true']);
    
    }


    public function cron_delete_video() {
        
        Log::info('cron_delete_video');

        $admin = Admin::first();
        
        $timezone = 'Asia/Kolkata';

        if($admin) {

            if ($admin->timezone) {

                $timezone = $admin->timezone;

            } 

        }

        $date = convertTimeToUSERzone(date('Y-m-d H:i:s'), $timezone);

        $delete_hour = Setting::get('delete_video_hour');

        $less_than_date = date('Y-m-d H:i:s', strtotime($date." -{$delete_hour} hour"));

        $videos = LiveVideo::where('is_streaming' ,'=' ,DEFAULT_TRUE)
                        ->where('status' , 0)
                        ->where('created_at', '<=', $less_than_date)
                        ->get();

        foreach ($videos as $key => $video) {
            Log::info('Change the status');
            $video->status = 1;
            $video->save();
        }
    
    }
}