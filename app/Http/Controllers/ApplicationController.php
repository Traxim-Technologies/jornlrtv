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

use Setting;

class ApplicationController extends Controller {

    protected $UserAPI;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserApiController $API)
    {
        $this->UserAPI = $API;
        
    }

    /**
     * Function Name : payment_failture()
     * 
     * Created By: vidhya R
     * 
     * Usage : used to show thw view page, whenever the payment failed.
     *
     */

    public function payment_failure($error = "") {

        $paypal_error = \Session::get("paypal_error") ? \Session::get('paypal_error') : "";

        \Session::forget("paypal_error");

        return view('payment_failure')->with('paypal_error' , $paypal_error);

    }

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


    public $expiry_date = "";

    public function test() {

        Log::info("MAIN VIDEO MOBILE");

        // $subject = tr('user_welcome_title' , Setting::get('site_name'));
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
                        $email_data['content'] = "Your subscription will expire soon. Our records indicate that no payment method has been associated with this subscripton account. Go to the subscription plans and provide the required payment information to renew your subscription for channel creation & uploading video and continue using your profile uninterrupted."; 
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
                        $email_data['name'] = $user->name;
                        $email_data['expiry_date'] = $payment->expiry_date;
                        $email_data['status'] = 1;
                        $page = "emails.payment-expiry";
                        $email = $user->email;
                        $email_data['content'] = "Your notification has expired. To keep using channel creation  & upload video without interruption, subscribe any one of our plans and continue to upload";
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

            if($results) {

                foreach ($results as $i => $key) {

                    $check = $i+1;

                    if($check <=10) {
     
                        array_push($items,$key->title);

                    } if($check == 10 ) {
                        array_push($items,"View All" );
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

            $videos = $this->UserAPI->search_list($request, $q,1)->getData();

            return view('user.search-result')->with('key' , $q)->with('videos' , $videos)->with('page' , "")->with('subPage' , "");
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

        if (Auth::guard('admin')->check()) {

            return view('admin.settings.control')->with('page', tr('admin_control'));

        } else {

            return back();
        }
        
    }

    public function save_admin_control(Request $request) {

        $model = Settings::get();
        
        foreach ($model as $key => $value) {

            if ($value->key == 'admin_delete_control') {
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
            } else if ($value->key == 'create_channel_by_user') {
                $value->value = $request->create_channel_by_user;
            } else if ($value->key == 'admin_language_control') {
                $value->value = $request->admin_language_control;
            
            } else if ($value->key == 'email_verify_control') {

                if ($request->email_verify_control == 1) {

                    if(config('mail.username') &&  config('mail.password')) {

                        $value->value = $request->email_verify_control;

                    } else {

                        return back()->with('flash_error', tr('configure_smtp'));
                    }

                }else {

                    $value->value = $request->email_verify_control;
                }
            } 
            
            $value->save();
        }
        return back()->with('flash_success' , tr('settings_success'));
    }


    public function embed_video(Request $request) {

        $model = VideoTape::videoResponse()
                ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id') 
                ->where('video_tapes.status' , 1)
                ->where('video_tapes.publish_status' , 1)
                ->where('video_tapes.is_approved' , 1)
                ->where('video_tapes.unique_id', $request->u_id)->first();

        if (Setting::get('is_payper_view')) {

            $user_id = "";

            if (Auth::check()) {

                $user_id = Auth::user()->id;

            }

            if ($user_id != $model->channel_created_by) {

                $user = User::find($user_id);

                if ($model->ppv_amount > 0) {

                    $ppv_status = $user ? watchFullVideo($user->id, $user->user_type, $model) : false;

                    if ($ppv_status) {
                        

                    } else {

                        if ($user_id) {

                            if ($user->user_type) {        
                                
                                return redirect(route('user.subscription.ppv_invoice', $model->video_tape_id));

                            } else {

                                return redirect(route('user.subscription.pay_per_view', $model->video_tape_id));
                            }

                        } else {

                            return redirect(route('user.subscription.pay_per_view', $model->video_tape_id));

                        }

                  
                    }

                }

            }

        } 

        if($user_id) {

            $channel = $model->getChannel ? $model->getChannel : '';

            if ($channel) { 

                if ($channel->user_id != $user_id) {

                    $age = $user->age_limit ? ($user->age_limit >= Setting::get('age_limit') ? 1 : 0) : 0;

                    if ($model->age_limit > $age) {

                        return redirect(route('user.dashboard'))->with('flash_error', tr('age_error'));

                    }

                } 

            }

        } else {

            if ($model->age_limit == 1) {

                return redirect(route('user.dashboard'))->with('flash_error', tr('age_error'));

            }

        }

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
}