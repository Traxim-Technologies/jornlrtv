<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\VideoTapeRepository as VideoRepo;

use App\Http\Requests;

use App\Helpers\Helper;

use App\Settings;

use App\User;

use App\Wishlist;

use App\Page;

use App\Flag;

use Auth;

use Validator;

use Setting;

use Exception;

use Log;

use App\Subscription;

use App\Channel;

use App\VideoTape;

use App\Repositories\CommonRepository as CommonRepo;

class UserController extends Controller {

    protected $UserAPI;

    protected $Paypal;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserApiController $API)
    {
        $this->UserAPI = $API;
        
        $this->middleware('auth', ['except' => ['index','single_video','all_categories' ,'category_videos' , 'sub_category_videos' , 'contact','trending', 'channel_videos', 'add_history']]);
    }

    /**
     * Show the user dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index(Request $request) {

        $database = config('database.connections.mysql.database');
        
        $username = config('database.connections.mysql.username');

        if($database && $username && Setting::get('installation_process') == 3) {

            counter('home');

            $id = Auth::check() ? Auth::user()->id : null;

            $watch_lists = $wishlists = array();

            if($id){

                $wishlists  =  VideoRepo::wishlist($id,WEB);

                $watch_lists = VideoRepo::watch_list($id,WEB);  
            }
            
            $recent_videos = VideoRepo::recently_added(WEB);

            $trendings = VideoRepo::trending(WEB);
            
            $suggestions  = VideoRepo::suggestion_videos(WEB);

            $channels = getChannels(WEB);


            return view('user.index')
                        ->with('page' , 'home')
                        ->with('subPage' , 'home')
                        ->with('wishlists' , $wishlists)
                        ->with('recent_videos' , $recent_videos)
                        ->with('trendings' , $trendings)
                        ->with('watch_lists' , $watch_lists)
                        ->with('suggestions' , $suggestions)
                        ->with('channels' , $channels);
        } else {
            return redirect()->route('installTheme');
        }
        
    }

    public function single_video($id) {


        $response = $this->UserAPI->getSingleVideo($id)->getData();

        // dd($response);
        
        return view('user.single-video')
                    ->with('page' , '')
                    ->with('subPage' , '')
                    ->with('video' , $response->video)
                    ->with('recent_videos' , $response->recent_videos)
                    ->with('trendings' , $response->trendings)
                    ->with('comments' , $response->comments)
                    ->with('suggestions',$response->suggestions)
                    ->with('wishlist_status' , $response->wishlist_status)
                    ->with('history_status' , $response->history_status)
                    ->with('main_video' , $response->main_video)
                    ->with('url' , $response->main_video)
                    ->with('channels' , $response->channels)
                    ->with('report_video', $response->report_video)
                    ->with('videoPath', $response->videoPath)
                    ->with('video_pixels', $response->video_pixels)
                    ->with('videoStreamUrl', $response->videoStreamUrl)
                    ->with('hls_video' , $response->hls_video)
                    ->with('flaggedVideo', $response->flaggedVideo)
                    ->with('ads', $response->ads);
    }

    /**
     * Show the profile list.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {

        $wishlist = $this->UserAPI->wishlist(Auth::user()->id, null, 0)->getData();

        return view('user.account.profile')
                    ->with('page' , 'profile')
                    ->with('subPage' , 'user-profile')->with('wishlist', $wishlist);
    }

    /**
     * Show the profile list.
     *
     * @return \Illuminate\Http\Response
     */
    public function update_profile()
    {

        $wishlist = $this->UserAPI->wishlist(Auth::user()->id)->getData();

        return view('user.account.edit-profile')->with('page' , 'profile')
                    ->with('subPage' , 'user-update-profile')->with('wishlist', $wishlist);
    }

    /**
     * Save any changes to the users profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile_save(Request $request)
    {
        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token,
        ]);

        $response = $this->UserAPI->update_profile($request)->getData();

        if($response->success) {

            return redirect(route('user.profile'))->with('flash_success' , tr('profile_updated'));

        } else {

            $message = $response->error." ".$response->error_messages;
            return back()->with('flash_error' , $message);
        }
    }

    /**
     * Save changed password.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile_save_password(Request $request)
    {
        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token,
        ]);

        $response = $this->UserAPI->change_password($request)->getData();

        if($response->success) {
            return back()->with('flash_success' , tr('password_success'));
        } else {
            $message = $response->error." ".$response->error_messages;
            return back()->with('flash_error' , $message);
        }

        return back()->with('response', $response);
    }

    public function profile_change_password(Request $request) {

        return view('user.account.change-password')->with('page' , 'profile')
                    ->with('subPage' , 'user-change-password');

    }

    public function add_history(Request $request) {

        if(Auth::check()) {
            $request->request->add([ 
                'id' => \Auth::user()->id,
                'token' => \Auth::user()->token,
                'device_token' => \Auth::user()->device_token
            ]);
        }

        $response = $this->UserAPI->add_history($request)->getData();

        if($response->success) {
            $response->message = Helper::get_message(118);
        } else {
            $response->success = false;
            $response->message = "Something Went Wrong";
        }

        $response->status = $request->status;


        return response()->json($response);
    
    }

    public function delete_history(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token
        ]);

        $response = $this->UserAPI->delete_history($request)->getData();

        if($response->success) {
            return back()->with('flash_success' , Helper::get_message(121));

        } else {
            return back()->with('flash_error' , tr('admin_not_error'));
        }
    }

    public function history(Request $request) {

        $histories = $this->UserAPI->history(\Auth::user()->id, 12)->getData();

        return view('user.account.history')
                        ->with('page' , 'profile')
                        ->with('subPage' , 'user-history')
                        ->with('histories' , $histories);
    }

    public function add_wishlist(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token
        ]);

        if($request->status == 1) {
            $response = $this->UserAPI->add_wishlist($request)->getData();
        } else {
            $response = $this->UserAPI->delete_wishlist($request)->getData();
        }

        if($response->success) {
            $response->message = Helper::get_message(118);
        } else {
            $response->success = false;
            $response->message = "Something Went Wrong";
        }

        $response->status = $request->status;

        return response()->json($response);
    }

    public function delete_wishlist(Request $request) {


        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token
        ]);

        $response = $this->UserAPI->delete_wishlist($request)->getData();

        if($response->success) {
            return back()->with('flash_success',tr('wishlist_removed'));
        } else {
            return back()->with('flash_error', "Something Went Wrong");
        }
    } 

    public function wishlist(Request $request) {
        
        $videos = $this->UserAPI->wishlist(\Auth::user()->id, 12)->getData();

        return view('user.account.wishlist')
                    ->with('page' , 'profile')
                    ->with('subPage' , 'user-wishlist')
                    ->with('videos' , $videos);
    }

    public function add_comment(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token
        ]);

        $response = $this->UserAPI->user_rating($request)->getData();

        if($response->success) {
            $response->message = Helper::get_message(118);
        } else {
            $response->success = false;
            $response->message = "Something Went Wrong";
        }

        return response()->json($response);
    
    }

    public function comments(Request $request) {

        $videos = Helper::get_user_comments(\Auth::user()->id,WEB);

        return view('user.comments')
                    ->with('page' , 'profile')
                    ->with('subPage' , 'user-comments')
                    ->with('videos' , $videos);
    }

    public function all_categories(Request $request) {

        $categories = get_categories();

        $recent_videos = Helper::recently_added(WEB);

        $trendings = Helper::trending(WEB);

        $suggestions = Helper::suggestion_videos(WEB);

        return view('user.all-categories')
                    ->with('page' , 'categories')
                    ->with('subPage' , 'categories')
                    ->with('categories' , $categories)
                    ->with('trendings' , $trendings)
                    ->with('suggestions' , $suggestions)
                    ->with('recent_videos' , $recent_videos);
    }

    public function channel_videos($id) {

        $channel = Channel::find($id);

        if ($channel) {

            $videos = VideoRepo::channel_videos($id, WEB);

            $trending_videos = VideoRepo::channel_trending($id, WEB, null, 5);

            $payment_videos = VideoRepo::payment_videos($id, WEB, null);

            return view('user.channels.index')
                        ->with('page' , 'channels')
                        ->with('subPage' , 'channels')
                        ->with('channel' , $channel)
                        ->with('videos' , $videos)->with('trending_videos', $trending_videos)
                        ->with('payment_videos', $payment_videos);
        } else {

            return back()->with('flash_error', tr('something_error'));

        }
    }

    public function channel_create() {

        $model = new Channel;

        $channels = getChannels(Auth::user()->id);

        if(count($channels) == 0 || Setting::get('multi_channel_status')) {

            return view('user.channels.create')->with('page', 'channels')
                    ->with('subPage', 'create_channel')->with('model', $model);

        } else {

            return redirect(route('user.dashboard'))->with('flash_error', tr('channel_create_error'));
        }

    }

    public function save_channel(Request $request) {

        $response = CommonRepo::channel_save($request)->getData();

        if($response->success) {
            // $response->message = Helper::get_message(118);
            return redirect(route('user.channel', ['id'=>$response->data->id]))
                ->with('flash_success', $response->message);
        } else {
            
            return back()->with('flash_error', $response->error);
        }

    }

    public function channel_edit($id) {

        $model = Channel::find($id);

        return view('user.channels.edit')->with('page', 'channels')
                    ->with('subPage', 'edit_channel')->with('model', $model);

    }

    public function channel_delete(Request $request) {

        $channel = Channel::where('id' , $request->id)->first();

        if($channel) {       

            $channel->delete();

            return redirect(route('user.dashboard'))->with('flash_success',tr('channel_delete_success'));

        } else {

            return back()->with('flash_error',tr('something_error'));

        }

    }

    public function contact(Request $request) {

        $contact = Page::where('type', 'contact')->first();

        return view('contact')->with('contact' , $contact)
                        ->with('page' , 'contact')
                        ->with('subPage' , '');

    }

    /**
     * Trending Videos
     *
     */

    public function trending(Request $request) {

        $trending = VideoRepo::trending(WEB);

        return view('user.trending')->with('page', 'trending')
                                    ->with('videos',$trending);
    }

    public function delete_account(Request $request) {

        if(\Auth::user()->login_by == 'manual') {

            return view('user.account.delete-account')
                    ->with('page' , 'profile')
                    ->with('subPage' , 'delete-account');
        } else {

            $request->request->add([ 
                'id' => \Auth::user()->id,
                'token' => \Auth::user()->token,
                'device_token' => \Auth::user()->device_token
            ]);

            $response = $this->UserAPI->delete_account($request)->getData();

            if($response->success) {
                return back()->with('flash_success', tr('user_account_delete_success'));
            } else {
                if($response->error == 101)
                    return back()->with('flash_error', $response->error_messages);
                else
                    return back()->with('flash_error', $response->error);
            }

            return back()->with('flash_error', Helper::get_error_message(146));

        }
        
    }

    public function delete_account_process(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token
        ]);

        $response = $this->UserAPI->delete_account($request)->getData();

        if($response->success) {
            return back()->with('flash_success', tr('user_account_delete_success'));
        } else {
            if($response->error == 101)
                return back()->with('flash_error', $response->error_messages);
            else
                return back()->with('flash_error', $response->error);
        }

        return back()->with('flash_error', Helper::get_error_message(146));

    }

    /**
     * Function Name : save_report_videos
     * Save report videos based on user based
     *
     * @param object $request - Post Attributes
     *
     * @return flash message
     */
    public function save_report_video(Request $request) {
       //  try {
            // Validate the coming post values
        $validator = Validator::make($request->all(), [
            'video_tape_id' => 'required',
            'reason' => 'required',
        ]);
        // If validator Fails, redirect same with error values
        if ($validator->fails()) {
             //throw new Exception("error", tr('admin_published_video_failure'));
            return back()->with('flash_error', tr('admin_published_video_failure'));
        }
        // Assign Post request values into Data variable
        $data = $request->all();

        // include user_id index into the data varaible  "Auth::user()->id" -> Logged In user id
        $data['user_id'] = \Auth::user()->id;
        $data['status'] = DEFAULT_TRUE;
        // Save the values in DB
        if (Flag::create($data)) {
            return redirect('/')->with('flash_success', tr('report_video_success_msg'));
        } else {
            //throw new Exception("error", tr('admin_published_video_failure'));
            return back()->with('flash_error', tr('admin_published_video_failure'));
        }
        /*} catch (Exception $e) {
            return back()->with('flash_error', $e);
        }*/
    }

    /**
     * Function Name : remove_report_video()
     * Remove the video from spam folder and make it as unspam
     *
     * @param integer $id Flag id
     *
     * @return flash error/flash success
     */
    public function remove_report_video($id) {
        // Load Spam Video from flag section
        $model = Flag::where('id', $id)->first();
        Log::info("Loaded Values : ".print_r($model, true));
        // If the flag model exists then delete the row
        if ($model) {
            Log::info("Loaded Values 1 : ".print_r($model, true));
            Log::info("Delete values :". print_r($model->delete()));
            $model->delete();
            return back()->with('flash_success', tr('unmark_report_video_success_msg'));
        } else {
            // throw new Exception("error", tr('admin_published_video_failure'));
            return back()->with('flash_error', tr('admin_published_video_failure'));
        }
    }

    /**
     * Function Name : spam_videos()
     * Based on logged in user load spam videos
     *
     * @return spam videos
     */
    public function spam_videos() {
        // Get logged in user id

        $model = $this->UserAPI->spam_videos(\Auth::user()->id, 12)->getData();

        // Return array of values
        return view('user.account.spam_videos')->with('model' , $model)
                        ->with('page' , 'Profile')
                        ->with('subPage' , 'Spam Videos');
    }   


    public function subscriptions() {

        $query = Subscription::where('status', DEFAULT_TRUE);

        if(Auth::check()) {
            if(Auth::user()->zero_subscription_status) {

                $query->whereNotIn('amount', [0]);

            }

        }

        $model = $query->get();

        return view('user.account.subscriptions')->with('subscriptions', $model)->with('page', 'Profile')->with('subPage', 'Subscriptions');
    }

    public function ad_request(Request $request) {

        if($data = VideoTape::find($request->id)) {

            $data->ad_status  = $data->ad_status ? 0 : 1;

            if($data->save()) {

                if($data->getVideoAds) {

                    $data->getVideoAds->status = $data->ad_status;

                    $data->getVideoAds->save();
                }
            }

            return response()->json(true, 200);

        } else {

            return response()->json(false, 200);
            
        }
    }

    public function video_upload(Request $request) {

        $model = new VideoTape;

        $id = $request->id;

        return view('user.videos.create')->with('model', $model)->with('page', 'videos')
            ->with('subPage', 'upload_video')->with('id', $id);
    }


    public function video_edit($id) {

        $model = VideoTape::find($id);

        $model->publish_time = $model->publish_time ? (($model->publish_time != '0000-00-00 00:00:00') ? date('d-m-Y H:i:s', strtotime($model->publish_time)) : null) : null;

        return view('user.videos.edit')->with('model', $model)->with('page', 'videos')
            ->with('subPage', 'upload_video');
    }


    public function video_save(Request $request) {

        $response = CommonRepo::video_save($request)->getData();

        if ($response->success) {

            $view = \View::make('user.videos.select_image')->with('model', $response)->render();

            return response()->json(['path'=>$view, 'data'=>$response->data], 200);

        } else {

            return response()->json(['message'=>$response->message], 400);

        }

    }   

    public function video_delete($id) {

        if($video = VideoTape::where('id' , $id)->first())  {

            $video->delete();
        }

        return back()->with('flash_success', tr('video_delete_success'));
    }


    public function save_default_img(Request $request) {

        $response = CommonRepo::set_default_image($request)->getData();

        return response()->json($response);

    }

    public function upload_video_image(Request $request) {


        $response = CommonRepo::upload_video_image($request)->getData();

        return response()->json(['id'=>$response]);

    }


    public function user_subscription_save($s_id, $u_id) {

        $response = CommonRepo::save_subscription($s_id, $u_id)->getData();

        if($response->success) {

            return back()->with('flash_success', $response->message);

        } else {

            return back()->with('flash_error', $response->message);

        }

    }

    public function get_images($id) {

        $response = CommonRepo::get_video_tape_images($id)->getData();

        $view = \View::make('user.videos.select_image')->with('model', $response)->render();

        return response()->json(['path'=>$view, 'data'=>$response->data]);

    }  

    /**
     * Used to get the redeems
     *
     */

    public function redeems(Request $request) {

        return view('user.redeems.index');

    }

    /**
     * Send Request to admin
     *
     */

    public function send_redeem_request(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token
        ]);

        $response = $this->UserAPI->send_redeem_request($request)->getData();

        if($response->success) {

            return back()->with('flash_success', tr('send_redeem_request_success'));

        } else {

            return back()->with('flash_error', $response->error);
        }

        return back()->with('flash_error', Helper::get_error_message(146));

    }

    /**
     * Send Request to admin
     *
     */

    public function redeem_request_cancel($id , Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token,
            'redeem_request_id' => $id,
        ]);

        $response = $this->UserAPI->redeem_request_cancel($request)->getData();

        if($response->success) {

            return back()->with('flash_success', tr('send_redeem_request_success'));

        } else {

            return back()->with('flash_error', $response->error);
        }

        return back()->with('flash_error', Helper::get_error_message(146));

    }

}