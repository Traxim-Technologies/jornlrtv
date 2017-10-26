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

use View;

use Setting;

use Exception;

use App\ChatMessage;

use Log;

use App\Card;

use App\BannerAd;

use App\Subscription;

use App\Channel;

use App\VideoTape;

use App\VideoTapeImage;

use App\Repositories\CommonRepository as CommonRepo;

use App\ChannelSubscription;

use App\UserPayment;

use App\LiveVideo;

use App\Viewer;

use App\LiveVideoPayment;

class UserController extends Controller {

    protected $UserAPI;

    protected $Paypal;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserApiController $API, Request $request)
    {   

        // print_r(route('user.live_video.start_broadcasting'));

        $this->UserAPI = $API;

        $this->middleware('auth', ['except' => ['index','single_video','all_categories' ,'category_videos' , 'sub_category_videos' , 'contact','trending', 'channel_videos', 'add_history', 'page_view', 'channel_list', 'live_videos','broadcasting', 'get_viewer_cnt', 'stop_streaming', 'watch_count', 'partialVideos', 'payment_mgmt_videos','master_login']]);

        if (Auth::check()) {

            if (strpos(url()->previous(),route('user.live_video.start_broadcasting')) === false) {

            } else {

                Log::info("starts ".Auth::user()->id);

               // $this->deleteStreaming();

            }

        }

    }


    public function deleteStreaming() {

        $model = LiveVideo::where('user_id',Auth::user()->id)->where('status', 0)->get();

        if (count($model) > 0) {

            Log::info("Logged In user id".Auth::user()->id);

             // Log::info("Model".print_r($model, true));

            foreach ($model as $key => $value) {

                Log::info("Usr Id".print_r($value->user_id,true));

                    
                if ($value->is_streaming) { 

                    Log::info("deleteStreaming");

                    // $value->status = DEFAULT_TRUE;

                    $value->save();

                } else {

                    $value->delete();

                }

            }

        }
    }


    /** 
     * Used to do login activity for master login
     * 
     *
     */

    public function master_login(Request $request) {

        // Get current login admin details

        $master_user_id = Auth::guard('admin')->user()->user_id;

        // Check the admin has logged in

        if(!$master_user_id) {

            // Check already record exists

            $check_admin_user_details = User::where('email' , Auth::guard('admin')->user()->email)->first();

            if($check_admin_user_details) {

                $check_admin_user_details->is_master_user = 1;

                $check_admin_user_details->save();

            } else {

                $check_admin_user_details = new User;

                $check_admin_user_details->name = "Master User";

                $check_admin_user_details->email = Auth::guard('admin')->user()->email;

                $check_admin_user_details->password = \Hash::make("123456");

                $check_admin_user_details->user_type = $check_admin_user_details->is_master_user = $check_admin_user_details->is_verified = $check_admin_user_details->status = 1;

                $check_admin_user_details->device_type = WEB;

                $check_admin_user_details->save();

            }

            $master_user_id = $check_admin_user_details->id;

        }

        $master_user_details = User::find($master_user_id);

        // If master user details is not empty -> Login the admin as user

        if($master_user_details) {

            Auth::loginUsingId($master_user_id, true);

            return redirect()->to('/')->with('flash_success' , tr('master_login_success'));

        } else {

            return back()->with("flash_error" , tr('something_error'));

        }

    }

    /**
     * Show the user dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index(Request $request) {


        Log::info("Timezone ".print_r(date('Y-m-d H:i:s'), true));

        Log::info("Convert Timezone ".print_r(convertTimeToUSERzone(date('Y-m-d H:i:s'), 'Europe/London', 'Y-m-d H:i:s'), true));


        $database = config('database.connections.mysql.database');
        
        $username = config('database.connections.mysql.username');

        if (Auth::check()) {
            
            $request->request->add([ 
                'id'=>\Auth::user()->id,
                'age' => \Auth::user()->age_limit,
            ]);   
        }

        if($database && $username && Setting::get('installation_process') == 2) {

            counter('home');

            $id = Auth::check() ? Auth::user()->id : null;

            $watch_lists = $wishlists = array();

            if($id){

                $wishlists  =  VideoRepo::wishlist($request,WEB);

                $watch_lists = VideoRepo::watch_list($request,WEB);  
            }

            //dd($watch_lists);
            
            $recent_videos = VideoRepo::recently_added($request, WEB);

            $trendings = VideoRepo::trending($request, WEB);
            
            $suggestions  = VideoRepo::suggestion_videos($request, WEB);

            $channels = getChannels(WEB);

            $banner_videos = [];

            if (Setting::get('is_banner_video')) {

                $banner_videos = VideoTape::select('id as video_tape_id', 'banner_image as image', 'title as video_title', 'description as content')
                                ->where('video_tapes.is_banner' , 1 )
                                ->where('video_tapes.status', DEFAULT_TRUE)
                                ->orderBy('video_tapes.created_at' , 'desc')
                                ->get();
            }

            $banner_ads = [];

            if(Setting::get('is_banner_ad')) {

                $banner_ads = BannerAd::select('id as banner_id', 'file as image', 'title as video_title', 'description as content', 'link')->where('banner_ads.status', DEFAULT_TRUE)->orderBy('banner_ads.created_at' , 'desc')
                                ->get();

            }

            return view('user.index')
                        ->with('page' , 'home')
                        ->with('subPage' , 'home')
                        ->with('wishlists' , $wishlists)
                        ->with('recent_videos' , $recent_videos)
                        ->with('trendings' , $trendings)
                        ->with('watch_lists' , $watch_lists)
                        ->with('suggestions' , $suggestions)
                        ->with('channels' , $channels)
                        ->with('banner_videos', $banner_videos)
                        ->with('banner_ads', $banner_ads);
        } else {
            return redirect()->route('installTheme');
        }
        
    }

    public function single_video(Request $request) {

        $request->request->add([ 
                'admin_video_id' => $request->id,
        ]);

        if (Auth::check()) {

            $request->request->add([ 
                'id'=>Auth::user()->id,
                'age_limit'=>Auth::user()->age_limit,
            ]);

        }

        $data = $this->UserAPI->getSingleVideo($request)->getData();

        if ($data->success) {

            $response = $data->response_array;
        
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
                        ->with('ads', $response->ads)
                        ->with('subscribe_status', $response->subscribe_status)
                        ->with('like_count',$response->like_count)
                        ->with('dislike_count',$response->dislike_count)
                        ->with('subscriberscnt', $response->subscriberscnt)
                        ->with('comment_rating_status', $response->comment_rating_status);
       
        } else {

            $error_message = isset($data->message) ? $data->message : tr('something_error');

            return back()->with('flash_error', $error_message);
            
        } 
    }

    /**
     * Show the profile list.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile(Request $request)
    {

        if ($request->id) {

            $id = $request->id;
           

        } else {

            $id = Auth::user()->id;
            
        }

         $user = User::find($id);

         $request->request->add([ 
                'id' => $user->id,
                'token' => $user->token,
                'device_token' => $user->device_token,
                'age'=>$user->age_limit,
            ]);

        $wishlist = VideoRepo::wishlist($request,WEB);

        return view('user.account.profile')
                    ->with('page' , 'profile')
                    ->with('user', $user)
                    ->with('subPage' , 'user-profile')->with('wishlist', $wishlist);
    }

    /**
     * Show the profile list.
     *
     * @return \Illuminate\Http\Response
     */
    public function update_profile(Request $request)
    {

         $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token,
            'age'=>\Auth::user()->age_limit,
        ]);

        $wishlist = VideoRepo::wishlist($request,WEB);

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
                'device_token' => \Auth::user()->device_token,
                'video_tape_id' => $request->video_tape_id
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

    public function watch_count(Request $request) {

        if($video = VideoTape::where('id',$request->video_tape_id)
                ->where('status',1)->where('publish_status' , 1)->where('video_tapes.is_approved' , 1)->first()) {

            \Log::info("ADD History - Watch Count Start");

            if($video->getVideoAds) {

                \Log::info("getVideoAds Relation Checked");

                if ($video->getVideoAds->status) {

                    \Log::info("getVideoAds Status Checked");

                    // Check the video view count reached admin viewers count, to add amount for each view

                    if($video->redeem_count >= Setting::get('viewers_count_per_video') && $video->ad_status) {

                        \Log::info("Check the video view count reached admin viewers count, to add amount for each view");

                        $video_amount = Setting::get('amount_per_video');

                        $video->redeem_count = 1;

                        $video->amount += $video_amount;

                        add_to_redeem($video->user_id , $video_amount);

                        \Log::info("ADD History - add_to_redeem");


                    } else {

                        \Log::info("ADD History - NO REDEEM");

                        $video->redeem_count += 1;

                    }

                }
            }

            $video->watch_count += 1;

            $video->save();

            \Log::info("ADD History - Watch Count Start");

            return response()->json(true);

        } else {

            return response()->json(false);
        }

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

         $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token,
            'age'=>\Auth::user()->age_limit,
        ]);

        $histories = VideoRepo::watch_list($request,WEB);

        return view('user.account.history')
                        ->with('page' , 'profile')
                        ->with('subPage' , 'user-history')
                        ->with('histories' , $histories);
    }

    public function add_wishlist(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token,
            'video_tape_id' => $request->admin_video_id
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

         $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token,
            'age'=>\Auth::user()->age_limit,
        ]);
        
        $videos = VideoRepo::wishlist($request,WEB);

        return view('user.account.wishlist')
                    ->with('page' , 'profile')
                    ->with('subPage' , 'user-wishlist')
                    ->with('videos' , $videos);
    }

    public function add_comment(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token,
            'video_tape_id'=>$request->admin_video_id
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


    public function channel_videos($id) {

        $channel = Channel::where('channels.is_approved', DEFAULT_TRUE)
                /*->select('channels.*', 'video_tapes.id as admin_video_id', 'video_tapes.is_approved',
                    'video_tapes.status', 'video_tapes.channel_id')
                ->leftJoin('video_tapes', 'video_tapes.channel_id', '=', 'channels.id')
                ->where('channels.status', DEFAULT_TRUE)
                ->where('video_tapes.is_approved', DEFAULT_TRUE)
                ->where('video_tapes.status', DEFAULT_TRUE)
                ->groupBy('video_tapes.channel_id')*/
                ->where('id', $id)
                ->first();


       /* if ($channel) {

            if(Auth::check()) {

                if ($channel->user_id != Auth::user()->id) {

                    $age = Auth::user()->age_limit ? (Auth::user()->age_limit >= Setting::get('age_limit') ? 1 : 0) : 0;

                    if ($video->age_limit > $age) {

                        return response()->json(['success'=>false, 'message'=>tr('age_error')]);

                    }

                } else {

                    if ($video->age_limit != 0) {

                        return response()->json(['success'=>false, 'message'=>tr('age_error')]);

                    }
                }
            } else {

                if ($video->age_limit != 0) {

                    return response()->json(['success'=>false, 'message'=>tr('age_error')]);

                }

            }

        }*/


        if ($channel) {

            $videos = VideoRepo::channel_videos($id);

            $trending_videos = VideoRepo::channel_trending($id, WEB, null, 5);

            $payment_videos = VideoRepo::payment_videos($id, WEB, null);

            $live_videos = VideoRepo::live_videos_list($id, WEB, null);

            $user_id = Auth::check() ? Auth::user()->id : '';

            $subscribe_status = false;

            if ($user_id) {

                $subscribe_status = check_channel_status($user_id, $id);

            }

            $subscriberscnt = subscriberscnt($channel->id);

            return view('user.channels.index')
                        ->with('page' , 'channels')
                        ->with('subPage' , 'channels')
                        ->with('channel' , $channel)
                        ->with('live_videos', $live_videos)
                        ->with('videos' , $videos)->with('trending_videos', $trending_videos)
                        ->with('payment_videos', $payment_videos)
                        ->with('subscribe_status', $subscribe_status)
                        ->with('subscriberscnt', $subscriberscnt);
        } else {

            return back()->with('flash_error', tr('something_error'));

        }
    }

    public function channel_create() {
        
        $model = new Channel;

        $channels = getChannels(Auth::user()->id);

        if((count($channels) == 0 || Setting::get('multi_channel_status'))) {

            if (Auth::user()->user_type) {

                return view('user.channels.create')->with('page', 'channels')
                    ->with('subPage', 'create_channel')->with('model', $model);

            } else {

                return redirect(route('user.dashboard'))->with('flash_error', tr('subscription_error'));

            }

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

        if (Auth::check()) {

            $request->request->add([ 
                'id' => \Auth::user()->id,
                'token' => \Auth::user()->token,
                'device_token' => \Auth::user()->device_token,
                'age'=>\Auth::user()->age_limit,
            ]);
        }

        $trending = VideoRepo::trending($request, WEB);

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
    public function spam_videos(Request $request) {

         $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token,
            'age'=>\Auth::user()->age_limit,
        ]);
        // Get logged in user id

        $model = $this->UserAPI->spam_videos($request, 12)->getData();

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

            $tape_images = VideoTapeImage::where('video_tape_id', $response->data->id)->get();

            $view = \View::make('user.videos.select_image')->with('model', $response)->with('tape_images', $tape_images)->render();

            return response()->json(['path'=>$view, 'data'=>$response->data], 200);

        } else {

            return response()->json(['message'=>$response->message], 400);

        }

    }   

    public function video_delete($id) {

        if($video = VideoTape::where('id' , $id)->first())  {

            Helper::delete_picture($video->video, "/uploads/videos/");

            Helper::delete_picture($video->subtitle, "/uploads/subtitles/"); 

            if ($video->banner_image) {

                Helper::delete_picture($video->banner_image, "/uploads/images/");
            }

            Helper::delete_picture($video->default_image, "/uploads/images/");

            if ($video->video_path) {

                $explode = explode(',', $video->video_path);

                if (count($explode) > 0) {


                    foreach ($explode as $key => $exp) {


                        Helper::delete_picture($exp, "/uploads/videos/");

                    }

                }

                

            }

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

        $tape_images = VideoTapeImage::where('video_tape_id', $id)->get();

        $view = \View::make('user.videos.select_image')->with('model', $response)
            ->with('tape_images', $tape_images)->render();

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

    public function page_view($id) {

        $page = Page::find($id);

        return view('static.common')->with('model' , $page)
                        ->with('page' , $page->type)
                        ->with('subPage' , '');

    }

    public function subscribe_channel(Request $request) {

        $validator = Validator::make( $request->all(), array(
                'user_id'     => 'required|exists:users,id',
                'channel_id'     => 'required|exists:channels,id',
                ));


        if ($validator->fails()) {

            $error_messages = implode(',', $validator->messages()->all());

            return back()->with('flash_error', $error_messages);

        } else {

            $model = ChannelSubscription::where('user_id', $request->user_id)->where('channel_id',$request->channel_id)->first();

            if (!$model) {

                $model = new ChannelSubscription;

                $model->user_id = $request->user_id;

                $model->channel_id = $request->channel_id;

                $model->status = DEFAULT_TRUE;

                $model->save();

                return back()->with('flash_success', tr('channel_subscribed'));

            } else {

                return back()->with('flash_error', tr('already_channel_subscribed'));

            }
        }
   
    }

    public function unsubscribe_channel(Request $request) {

        $validator = Validator::make( $request->all(), array(
                'subscribe_id'     => 'required|exists:channel_subscriptions,id',
                ));


        if ($validator->fails()) {

            $error_messages = implode(',', $validator->messages()->all());

            return back()->with('flash_error', $error_messages);

        } else {

            $model = ChannelSubscription::find($request->subscribe_id);

            if ($model) {

                $model->delete();

                return back()->with('flash_success', tr('channel_unsubscribed'));

            } else {

                return back()->with('flash_error', tr('not_found'));

            }
        }

    }

    public function channel_list(Request $request){

        $response = $this->UserAPI->channel_list($request)->getData();

        // dd($response);

        return view('user.channels.list')->with('page', 'channels')
                ->with('subPage', 'channel_list')
                ->with('response', $response);

    }

    public function likeVideo(Request $request)  {
        $request->request->add([
            'id' => Auth::user()->id,
            'token'=>Auth::user()->token
        ]);

        $response = $this->UserAPI->likevideo($request)->getData();

        // dd($response);
        return response()->json($response);

    }

    public function disLikeVideo(Request $request) {

        $request->request->add([ 
            'id' => Auth::user()->id,
            'token'=>Auth::user()->token
        ]);

        $response = $this->UserAPI->dislikevideo($request)->getData();

        return response()->json($response);

    }

    public function channel_subscribers(Request $request) {

        $list = [];

        $channel_id = $request->channel_id ? $request->channel_id : '';

        $channel = null;

        if ($channel_id) {

            $list[] = $request->channel_id;

            $channel = Channel::find($channel_id);

        } else {

            $channels = getChannels(Auth::user()->id);

            foreach ($channels as $key => $value) {
                $list[] = $value->id;
            }
        }

        $subscribers = ChannelSubscription::whereIn('channel_subscriptions.channel_id', $list)
                        ->select('channel_subscriptions.channel_id as channel_id',
                                'channels.name as channel_name',
                                'users.id as user_id',
                                'users.name as user_name',
                                'channel_subscriptions.id as subscriber_id',
                                'channel_subscriptions.created_at as created_at')
                        ->leftJoin('channels', 'channels.id', '=', 'channel_subscriptions.channel_id')
                        ->leftJoin('users', 'users.id', '=', 'channel_subscriptions.user_id')
                        ->orderBy('created_at', 'desc')
                        ->paginate();

        return view('user.channels.subscribers')->with('page', 'channels')->with('subPage', 'subscribers')->with('subscribers', $subscribers)->with('channel_id', $channel_id)->with('channel', $channel);

    }

    public function card_details(Request $request) {

        $cards = Card::where('user_id', Auth::user()->id)->get();

        return view('user.account.cards')->with('page', 'account')->with('subPage', 'cards')->with('cards', $cards);
    }




    /**
     * Show the payment methods.
     *
     * @return \Illuminate\Http\Response
     */
    public function payment_card_add(Request $request) {

        $last_four = substr($request->number, -4);

        $stripe_secret_key = \Setting::get('stripe_secret_key');

        $response = json_decode('{}');

        if($stripe_secret_key) {

            \Stripe\Stripe::setApiKey($stripe_secret_key);

        } else {

            $response->success = false;
            $response->message = 'Adding cards is not enabled on this application. Please contact administrator';

            return back()->with('flash_errors', $response);
        }

        try {

            // Get the key from settings table
            
            $customer = \Stripe\Customer::create([
                    "card" => $request->stripeToken,
                    "email" => \Auth::user()->email
                ]);

            if($customer) {

                $customer_id = $customer->id;


                $cards = new Card;
                $cards->user_id = \Auth::user()->id;
                $cards->customer_id = $customer_id;
                $cards->last_four = $last_four;
                $cards->card_token = $customer->sources->data ? $customer->sources->data[0]->id : "";

                // Check is any default is available
                $check_card = Card::where('user_id', \Auth::user()->id)->first();

                if($check_card)
                    $cards->is_default = 0;
                else
                    $cards->is_default = 1;
                
                $cards->save();

                $user = User::find(\Auth::user()->id);

                if($user && $cards->is_default) {

                    $user->payment_mode = 'card';
                    $user->card_id = $cards->id;
                    $user->save();

                }

                $response_array = array('success' => true);

                $response_code = 200;

            } else {
                $response->message('Could not create client ID');
            }
        
        } catch(Exception $e) {

            return back()->with('flash_error' , $e->getMessage());

        }
        
        return back()->with('flash_success', 'Successfully Created');
    }



    public function payment_card_default(Request $request)
    {
        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
        ]);

        $response = $this->UserAPI->default_card($request)->getData();

        if($response->success) {
            $message = tr('card_default_success');
            $type = "flash_success";
        } else {
            $message = tr('unkown_error');
            $type = "flash_error";
        }

        return back()->with($type, $message);
    }

    /**
     * Show the payment methods.
     *
     * @return \Illuminate\Http\Response
     */
    public function payment_card_delete(Request $request)
    {
        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
        ]);

        $response = $this->UserAPI->delete_card($request)->getData();
        
        if($response->success) {

            $message = $response->message;

            $type = "flash_success";

        } else {
            $message = $response->error_messages;
            $type = "flash_error";
        }

        return back()->with($type, $message);
    }

    /**
     * Show the payment methods.
     *
     * @return \Illuminate\Http\Response
     */
    public function payment_update_default(Request $request) {

        $this->validate($request, [
                'payment_mode' => 'required',
            ]);

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
        ]);        

        $response = $this->UserAPI->payment_mode_update($request)->getData();

        if($response->success) {
            $message = tr('card_default_success');
            $type = "flash_success";
        } else {
            $message = tr('unkown_error');
            $type = "flash_error";
        }

        return back()->with($type, $message);
    }

    public function stripe_payment(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'subscription_id' => $request->subscription_id
        ]);        

        /*$response = $this->UserAPI->pay_video($request)->getData();*/


        $validator = Validator::make($request->all(), [
           // 'tour_id' => 'required|exists:tours,id',

            'subscription_id' => 'required|exists:subscriptions,id',
            ]);

        if($validator->fails()) {

            $error_messages = implode(',', $validator->messages()->all());

            // $response_array = array('success' => false , 'error' => $error_messages , 'error_code' => 101);

             return back()->with('flash_errors', $error_messages);

        } else {

            $subscription = Subscription::find($request->subscription_id);

            if($subscription) {

                $total = $subscription->amount;

                $user = User::find($request->id);

                $check_card_exists = User::where('users.id' , $request->id)
                                    ->leftJoin('cards' , 'users.id','=','cards.user_id')
                                    ->where('cards.id' , $user->card_id)
                                    ->where('cards.is_default' , DEFAULT_TRUE);

                if($check_card_exists->count() != 0) {

                    $user_card = $check_card_exists->first();

                    // Get the key from settings table
                    $stripe_secret_key = Setting::get('stripe_secret_key');

                    $customer_id = $user_card->customer_id;
                
                    if($stripe_secret_key) {

                        \Stripe\Stripe::setApiKey($stripe_secret_key);
                    } else {

                        // $response_array = array('success' => false, 'error' => Helper::error_message(902) , 'error_code' => 902);

                       // return response()->json($response_array , 200);

                        return back()->with('flash_error', Helper::get_error_message(902));
                    }

                    try {

                       $user_charge =  \Stripe\Charge::create(array(
                          "amount" => $total * 100,
                          "currency" => "usd",
                          "customer" => $customer_id,
                        ));

                       $payment_id = $user_charge->id;
                       $amount = $user_charge->amount/100;
                       $paid_status = $user_charge->paid;

                       if($paid_status) {


                            $user_payment = UserPayment::where('user_id' , $request->id)->first();

                            if($user_payment) {

                                $expiry_date = $user_payment->expiry_date;
                                $user_payment->expiry_date = date('Y-m-d H:i:s', strtotime($expiry_date. "+".$subscription->plan." months"));

                            } else {
                                $user_payment = new UserPayment;
                                $user_payment->expiry_date = date('Y-m-d H:i:s',strtotime("+".$subscription->plan." months"));
                            }


                            $user_payment->payment_id  = $payment_id;
                            $user_payment->user_id = $request->id;
                            $user_payment->subscription_id = $request->subscription_id;
                            $user_payment->status = 1;
                            $user_payment->amount = $amount;
                            $user_payment->save();


                            $user->user_type = 1;
                            $user->save();
                            

                            // $response_array = ['success' => true, 'message'=>tr('payment_success')];

                            return back()->with('flash_success',tr('payment_success'));

                        } else {

                            // $response_array = array('success' => false, 'error' => Helper::get_error_message(903) , 'error_code' => 903);

                            // return response()->json($response_array , 200);

                            return back()->with('flash_error', Helper::get_error_message(903));

                        }
                    
                    } catch (\Stripe\StripeInvalidRequestError $e) {

                        Log::info(print_r($e,true));

                        /*$response_array = array('success' => false , 'error' => Helper::get_error_message(903) ,'error_code' => 903);*/

                        return back()->with('flash_error', Helper::get_error_message(903));

                       // return response()->json($response_array , 200);
                    
                    }

                } else {

                    // $response_array = array('success' => false, 'error' => Helper::get_error_message(901) , 'error_code' => 901);

                    return back()->with('flash_error', Helper::get_error_message(901));
                    
                    //return response()->json($response_array , 200);
                }

            } else {

                // $response_array = array('success' => false, 'error' => Helper::get_error_message(901) , 'error_code' => 901);
                return back()->with('flash_error', Helper::get_error_message(901));
                
                // return response()->json($response_array , 200);
            }



        }

    }

    public function subscribed_channels(Request $request) {

        $request->request->add([ 
            'user_id' => \Auth::user()->id,
        ]);        

        $response = $this->UserAPI->channel_list($request)->getData();

        // dd($response);

        return view('user.channels.list')->with('page', 'channels')
                ->with('subPage', 'channel_list')
                ->with('response', $response);

    }


    public function broadcast(Request $request) 
    {

        $request->request->add([ 
            'id' => \Auth::user()->id,
        ]);        

        $response = $this->UserAPI->broadcast($request)->getData();


        if ($response->success) {

            if (Setting::get('wowza_server_url')) {


                if (!file_exists(public_path()."/uploads/sdp_files/".$response->data->user_id.'-'.$response->data->id.".sdp")) {

                    $myfile = fopen(public_path()."/uploads/sdp_files/".$response->data->user_id.'-'.$response->data->id.".sdp", "w") or die("Unable to open file!");

                    $destination_ip = Setting::get('wowza_ip_address');

                    // $destination_port = time();

                    $destination_port = $response->data->port_no;

                    $data = "v=0\n"
                            ."o=- 0 0 IN IP4 " . $destination_ip . "\n"
                            . "s=Kurento\n"
                            . "c=IN IP4 " . $destination_ip . "\n"
                            . "t=0 0\n"
                            . "m=video " . $destination_port . " RTP/AVP 100\n"
                            . "a=rtpmap:100 H264/90000\n";

                    fwrite($myfile, $data);

                    fclose($myfile);

                    $filepath = public_path()."/uploads/sdp_files/".$response->data->user_id.'-'.$response->data->id.".sdp";

                    shell_exec("mv $filepath /usr/local/WowzaStreamingEngine/content/");

                    $this->connectStream($response->data->user_id.'-'.$response->data->id);

                }

            }

            return redirect(route('user.live_video.start_broadcasting', array('id'=>$response->data->unique_id,'c_id'=>$response->data->channel_id)))->with('flash_success', $response->message);

        } else {

            return back()->with('flash_error', $response->error_messages);
        }

    }


    public function broadcasting(Request $request) {


        if ($request->id) {


            $model = LiveVideo::where('unique_id', $request->id)
                        ->where('status', '!=', DEFAULT_TRUE)
                       // ->where('user_id', Auth::user()->id)
                        ->first();
    
            if ($model) {

                // $delete_videos = LiveVideo::

                $videoPayment = null;


                if (Auth::check()) {

                    // $usrModel

                    $userModel = User::find(Auth::user()->id);

                    if ($model->user_id != $userModel->id) {

                            // Load Viewers model

                            $viewer = Viewer::where('video_id', $model->id)->where('user_id', Auth::user()->id)->first();

                            if(!$viewer) {

                                $viewer = new Viewer;

                                $viewer->video_id = $model->id;

                                $viewer->user_id = Auth::user()->id;

                            }

                            $viewer->count = ($viewer->count) ? $viewer->count + 1 : 1;

                            $viewer->save();

                            if ($viewer) {

                                $model->viewer_cnt += 1;

                                $model->save();

                            }
                            // video payment 

                            $videoPayment = LiveVideoPayment::where('live_video_id', $model->id)
                                ->where('live_video_viewer_id', Auth::user()->id)
                                ->where('status',DEFAULT_TRUE)->first();
                        
                    }

                    $appSettings = json_encode([
                        'SOCKET_URL' => Setting::get('SOCKET_URL'),
                        'CHAT_ROOM_ID' => isset($model) ? $model->id : null,
                        'BASE_URL' => Setting::get('BASE_URL'),
                        'TURN_CONFIG' => [],
                        'TOKEN' =>  ($model->user_id == $userModel->id) ? Auth::user()->token : null,
                        'USER_PICTURE'=>$userModel->chat_picture,
                        'NAME'=>$userModel->name,
                        'CLASS'=>'left',
                        'USER' => ($model->user_id == $userModel->id) ? ['id' => $userModel->id, 'role' => "model"] : null,
                        'VIDEO_PAYMENT'=>($videoPayment) ? $videoPayment : null,
                    ]);

                    $comments = ChatMessage::where('live_video_id', $model->id)->get();

                } else {

                    $model->viewer_cnt += 1;

                    $model->save();

                    $appSettings = json_encode([
                        'SOCKET_URL' => Setting::get('SOCKET_URL'),
                        'CHAT_ROOM_ID' => isset($model) ? $model->id : null,
                        'BASE_URL' => Setting::get('BASE_URL'),
                        'TURN_CONFIG' => [],
                        'TOKEN' =>  null,
                        'USER_PICTURE'=>$model->user->chat_picture,
                        'NAME'=>$model->user->name,
                        'CLASS'=>'left',
                        'USER' => null,
                        'VIDEO_PAYMENT'=>($videoPayment) ? $videoPayment : null,
                    ]);

                    $comments = null;

                }


                $query = LiveVideo::where('is_streaming', DEFAULT_TRUE)
                    ->where('status', 0)->whereNotIn('id', [$model->id]);

                if (Auth::check()) {

                    $query->whereNotIn('user_id', [Auth::user()->id]);

                }

                $videos = $query->paginate(15);


                return view('user.videos.live-video')->with('page', 'live-video')
                    ->with('subPage', 'broadcast')
                    ->with('data', $model)->with('appSettings', $appSettings)->with('comments',$comments)->with('videos', $videos);


            } else {

                return redirect(route('user.channel', ['id'=>$request->c_id]))->with('flash_error', tr('no_live_video_found'));

            }

        } else {

            if ($request->c_id) {

                return redirect(route('user.channel', ['id'=>$request->c_id]))->with('flash_error', tr('id_not_matching'));

            } else {

                return redirect(route('user.dashboard'))->with('flash_error', tr('something_error'));

            }


        }

    }


    public function stop_streaming(Request $request) {

        $model = LiveVideo::find($request->id);

        $model->status = DEFAULT_TRUE;

        if(Auth::check()) {

            if ($model->user_id == Auth::user()->id) {

                $model->end_time = getUserTime(date('H:i:s'), ($model->user) ? $model->user->timezone : '', "H:i:s");

                $message =  tr('streaming_stopped_success');

                $route = route('user.channel', ['id'=>$model->channel_id]);

            } else {

                $message = tr('no_more_video_available');

                $route = route('user.live_videos');
            }

        } else {

            $message = tr('no_more_video_available');

            $route = route('user.live_videos');

        }

        if ($model->save()) {

            if ( Auth::check()) {

                if ($model->user_id == Auth::user()->id) {  

                    if (Setting::get('wowza_server_url')) {

                        $this->disConnectStream($model->user->id.'-'.$model->id);

                    }

                }

            }

        }

        return redirect($route)->with('flash_success',$message);
    }


    public function live_videos(Request $request) {

        $query = LiveVideo::where('is_streaming', DEFAULT_TRUE)
                    ->where('status', 0);

        if (Auth::check()) {

            $query->whereNotIn('user_id', [Auth::user()->id]);

        }

        $videos = $query->paginate(15);

        return view('user.videos.live_videos_list')
                ->with('videos', $videos);

    }

    public function setCaptureImage(Request $req, $roomId) {
        //TODO - allow model of this room only

        $data = explode(',', $req->get('base64'));

        if ($data[1] != '') {
            file_put_contents(join(DIRECTORY_SEPARATOR, [public_path(), 'uploads', 'rooms', $roomId . '.png']), base64_decode($data[1]));
            $model = LiveVideo::find($roomId);
            $model->snapshot = Helper::web_url()."/uploads/rooms/".$roomId . '.png';
            $model->save();

            if ($model->save()) {
                return response()->json(true,200);
            } else {
                return response()->json(false,200);
            }
        }
         
    }


    public function get_viewer_cnt(Request $request) {

        $model = LiveVideo::find($request->id);

        if ($model) {

            $viewer_cnt = $model->viewer_cnt;

        } else {

            $viewer_cnt = 0;

        }

        return response()->json(['viewer_cnt'=>$viewer_cnt, 'model'=>$model]);

    }

    public function connectStream($file = null)
    {

        try {
            $client = new \GuzzleHttp\Client();

            $url  = Setting::get('wowza_server_url')."/v2/servers/_defaultServer_/vhosts/_defaultVHost_/sdpfiles/$file/actions/connect?connectAppName=live&appInstance=_definst_&mediaCasterType=rtp";

            $request = new \GuzzleHttp\Psr7\Request('PUT', $url);
            $promise = $client->sendAsync($request)->then(function ($response) {
                     echo 'I completed! ' . $response->getBody();
            });
            $promise->wait();
        } catch(\GuzzleHttp\Exception\ClientException $e) {
            dd($e->getResponse()->getBody()->getContents());
        }

    }


    // Disconnect Stream
    public function disConnectStream($file = null) {


        try {
            $client = new \GuzzleHttp\Client();

            $sdp = $file.".sdp";

            $url  = Setting::get('wowza_server_url')."/v2/servers/_defaultServer_/vhosts/_defaultVHost_/applications/live/instances/_definst_/incomingstreams/$sdp/actions/disconnectStream";

            $request = new \GuzzleHttp\Psr7\Request('PUT', $url);
            $promise = $client->sendAsync($request)->then(function ($response) {
                     echo 'I completed! ' . $response->getBody();
            });
            $promise->wait();

            $this->deleteStream($file);

        } catch(\GuzzleHttp\Exception\ClientException $e) {
            dd($e->getResponse()->getBody()->getContents());
        }


    }

    // Delete Stream

    public function deleteStream($file = null) {


        try {
            $client = new \GuzzleHttp\Client();

            $url  = Setting::get('wowza_server_url')."/v2/servers/_defaultServer_/vhosts/_defaultVHost_/sdpfiles/$file";

            $request = new \GuzzleHttp\Psr7\Request('DELETE', $url);
            $promise = $client->sendAsync($request)->then(function ($response) {
                     echo 'I completed! ' . $response->getBody();
            });
            $promise->wait();
        } catch(\GuzzleHttp\Exception\ClientException $e) {
            dd($e->getResponse()->getBody()->getContents());
        }


    }

    public function payment_url(Request $request) {

        $id = $request->id;

        $user_id = $request->user_id;

        if (!Auth::check() || !$user_id) {

            return redirect(route('user.login.form'));

        } else {

            $video_payment = LiveVideoPayment::where('live_video_viewer_id' , $user_id)->where('live_video_id' , $id)->first();

            if ($video_payment) {


                return redirect(route('user.live_video.start_broadcasting', array('id'=>$video_payment->getVideo->unique_id, 'c_id'=>$video_payment->getVideo->channel_id)));



            }

            if (Setting::get('payment_type') == 'stripe') {

                return redirect(route('user.stripe_payment_video', array('id'=>$id, 'user_id'=>$user_id)));

            } else {

                return redirect(route('user.live_video_paypal', array('id'=>$id, 'user_id'=>$user_id)));
            }
        }

    }

    public function stripe_payment_video(Request $request) {

        if (\Auth::user()->card_id) {

            $user_card = Card::find(Auth::user()->card_id);

            if ($user_card && $user_card->is_default) {

                $video = LiveVideo::find($request->id);

                if($video && !$video->status && $video->is_streaming) {

                    $total = $video->amount;

                    // Get the key from settings table
                    $stripe_secret_key = Setting::get('stripe_secret_key');

                    $customer_id = $user_card->customer_id;
                    
                    if($stripe_secret_key) {

                        \Stripe\Stripe::setApiKey($stripe_secret_key);
                    } else {

                        // $response_array = array('success' => false, 'error' => Helper::error_message(902) , 'error_code' => 902);

                       // return response()->json($response_array , 200);

                        return back()->with('flash_error', Helper::get_error_message(902));
                    }

                    try {

                       $user_charge =  \Stripe\Charge::create(array(
                          "amount" => $total * 100,
                          "currency" => "usd",
                          "customer" => $customer_id,
                        ));

                       $payment_id = $user_charge->id;
                       $amount = $user_charge->amount/100;
                       $paid_status = $user_charge->paid;

                       if($paid_status) {
                            $user_payment = new LiveVideoPayment;
                            $user_payment->payment_id  = $payment_id;
                            $user_payment->live_video_viewer_id = Auth::user()->id;
                            $user_payment->user_id = $video->user_id;
                            $user_payment->live_video_id = $video->id;
                            $user_payment->status = 1;
                            $user_payment->amount = $amount;
                            // $user_payment->save();

                            // Commission Spilit 

                            $admin_commission = Setting::get('admin_commission')/100;

                            $admin_amount = $amount * $admin_commission;

                            $user_amount = $amount - $admin_amount;

                            $user_payment->admin_amount = $admin_amount;

                            $user_payment->user_amount = $user_amount;

                            $user_payment->save();

                            // Commission Spilit Completed

                            if($user = User::find($user_payment->user_id)) {

                                $user->total_admin_amount = $user->total_admin_amount + $admin_amount;

                                $user->total_user_amount = $user->total_user_amount + $user_amount;

                                $user->remaining_amount = $user->remaining_amount + $user_amount;

                                $user->total = $user->total + $total;

                                $user->save();

                                add_to_redeem($user->id, $user_amount);
                            
                            }



                            return redirect(route('user.live_video.start_broadcasting',array('id'=>$video->unique_id, 'c_id'=>$video->channel_id)));

                        } else {

                            return back()->with('flash_error', Helper::get_error_message(903));

                        }
                    
                    } catch (\Stripe\StripeInvalidRequestError $e) {

                        Log::info(print_r($e,true));

                        /*$response_array = array('success' => false , 'error' => Helper::get_error_message(903) ,'error_code' => 903);*/

                        return back()->with('flash_error', Helper::get_error_message(903));

                       // return response()->json($response_array , 200);
                    
                    }

                
                } else {

                    return back()->with('flash_error', tr('no_live_video_found'));
                    
                }


            } else {

                return back()->with('flash_error', tr('no_default_card_available'));

            }

        } else {

            return back()->with('flash_error', tr('no_default_card_available'));

        }
        

    }



    public function partialVideos(Request $request) {

        // Get Videos

        $videos = VideoRepo::channel_videos($request->channel_id, null, $request->skip);

        $channel = Channel::find($request->channel_id);

        $view = View::make('user.videos.partial_videos')
                    ->with('videos',$videos)
                    ->with('channel',$channel)
                    ->render();

        return response()->json(['view'=>$view, 'length'=>count($videos)]);
    }


    public function payment_mgmt_videos(Request $request) {

        // Get Videos

        // $videos = VideoRepo::channel_videos($request->channel_id, null, $request->skip);

        $payment_videos = VideoRepo::payment_videos($request->channel_id, null, $request->skip);

        $view = View::make('user.videos.partial_payment_videos')
                    ->with('payment_videos', $payment_videos)->render();

        return response()->json(['view'=>$view, 'length'=>count($payment_videos)]);
    }


    public function delete_video($id, $user_id) {

        // Load Model
        $model = LiveVideo::find($id);

        if ($model) {

            if ($model->user_id == $user_id) {

                if ($model->is_streaming) {

                    $model->status = DEFAULT_TRUE;

                    $model->end_time = getUserTime(date('H:i:s'), ($model->user) ? $model->user->timezone : '', "H:i:s");

                    // $model->no_of_

                    if ($model->save()) {

                        if (Setting::get('wowza_server_url')) {

                            $this->disConnectStream($model->user->id.'-'.$mid);

                        }

                    } else {

                        $response_array = ['success'=>false, 'error_messages'=>tr('went_wrong')];

                    }

                    $response_array = ['success'=>true];

                }

            } else {

                $response_array = ['success'=>false, 'error_messages'=> tr('not_authorized_person')];

            }
            
        } else {

            $response_array = ['success'=>false, 'error_messages'=> tr('no_live_video_present')];

        }

        return response()->json($response_array);

    }
}