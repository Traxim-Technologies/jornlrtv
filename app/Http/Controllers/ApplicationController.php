<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Requests;

use App\Helpers\Helper;

use App\Category;

use App\SubCategory;

use App\SubCategoryImage;

use App\Genre;

use App\AdminVideo;

use App\User;

use App\UserPayment;

use Validator;

use Hash;

use Mail;

use Auth;

use Redirect;

use Setting;

use Log;

use DB;

use Elasticsearch\ClientBuilder;

define('NO_INSTALL' , 0);

define('SYSTEM_CHECK' , 1);

define('THEME_CHECK' , 2);

define('INSTALL_COMPLETE' , 3);


class ApplicationController extends Controller {

    public $expiry_date = "";


    public function select_genre(Request $request) {
        
        $id = $request->option;

        $genres = Genre::where('sub_category_id', '=', $id)
                        ->where('is_approved' , 1)
                          ->orderBy('name', 'asc')
                          ->get();

        return response()->json($genres);
    }

    public function select_sub_category(Request $request) {
        
        $id = $request->option;

        $subcategories = Subcategory::where('category_id', '=', $id)
                        ->where('is_approved' , 1)
                          ->orderBy('name', 'asc')
                          ->get();

        return response()->json($subcategories);
    }

    public function cron_publish_video(Request $request) {
        
        Log::info('cron_publish_video');

        $videos = AdminVideo::where('publish_time' ,'<=' ,date('Y-m-d H:i:s'))
                        ->where('status' , 0)->get();
        foreach ($videos as $key => $video) {
            Log::info('Change the status');
            $video->status = 1;
            $video->save();
        }
    }

    public function send_notification_user_payment()
    {
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

    public function user_payment_expiry()
    {
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

    public function addIndex() {

        $params = array();

        $client = ClientBuilder::create()->build();

        $params['body']  = array(
          'id' => 0
        );

        $params['index'] = 'live-streaming';
        $params['type']  = 'live-streaming';
        $params['id'] = 'live-streaming';

        $result = $client->index($params);
    }

    public function add_value_index() {

        $client = ClientBuilder::create()->build();

        $params = array();

        $params['body']  = array(
          'video_id' => $video->id,
          'title' => $video->title,
          'description' => $video->description
        );

        $params['index'] = 'start_streaming';
        $params['type']  = 'streaming_type';
        $params['id'] = 'streaming_id';

        $result = $client->index($params);

        dd($result);
    }

    public function addAllVideoToEs() {

        $videos = AdminVideo::where('is_approved' , 1)->get();

        if(count($videos)) {

            foreach ($videos as $video) {

                $params = array();

                $client = ClientBuilder::create()->build();

                $params['body']  = array(
                  'id' => $video->id,
                  'title' => $video->title,
                  'description' => $video->description,
                );

                $params['index'] = 'live-streaming';
                $params['type']  = 'live-streaming';
                $params['id'] = $video->id;

                $result = $client->index($params);

                Log::info("Result Elasticsearch ".print_r($result ,true));

            }

        }
    }

    public function test() {

        return view('emails.test');

        $settings = Setting::get('installation_process');

        if( $settings == NO_INSTALL) {
            $title = "System Check";
            return view('install.system-check')->with('title' , $title);
        } 
        if ($settings == SYSTEM_CHECK) {
            $title = "Choose Theme";
            return view('install.install-theme')->with('title' , $title);
        } 

        if($settings == THEME_CHECK ) {
            $title = "Configure Site Settings";

            return view('install.install-others')->with('title' , $title);
        }

        if($settings == INSTALL_COMPLETE) {
            return \Redirect::route('user.dashboard');
        }
        
        return \Redirect::route('user.dashboard');    

    }

    public function search_video(Request $request) {

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
            
            $results = Helper::search_video($q);

            foreach ($results as $i => $key) {

                $check = $i+1;

                if($check <=10) {
 
                    array_push($items,$key->title);

                } if($check == 10 ) {
                    array_push($items,"View All" );
                }
            }

            return response()->json($items);
        }     
    }

    public function search_all(Request $request) {

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

            $videos = Helper::search_video($q,1);

            return view('user.search-result')->with('key' , $q)->with('videos' , $videos)->with('page' , "")->with('subPage' , "");
        }     
    }

}