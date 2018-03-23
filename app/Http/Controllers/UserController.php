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

use DB;

use Validator;

use View;

use Setting;

use Exception;

use Log;

use App\PayPerView;

use App\Card;

use App\BannerAd;

use App\Subscription;

use App\Channel;

use App\VideoTape;

use App\VideoTapeImage;

use App\Repositories\CommonRepository as CommonRepo;

use App\ChannelSubscription;

use App\UserPayment;

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
        
        $this->middleware('auth', ['except' => [
                'master_login',
                'index',
                'single_video',
                'contact',
                'trending', 
                'channel_videos', 
                'add_history', 
                'page_view', 
                'channel_list', 
                'watch_count', 
                'partialVideos', 
                'payment_mgmt_videos', 
                'forgot_password' 
        ]]);
    }


    /**
     * Function Name : master_login()
     *
     * To Activate Super user by admin
     *
     * @param Object $request - User Details
     *
     * @return with Success/Failure Message
     */
    public function master_login(Request $request) {

        try {

            DB::beginTransaction();

            if (Auth::guard('admin')->check()) {

                // Get current login admin details

                $master_user_id = Auth::guard('admin')->user()->user_id;

                // Check the admin has logged in

                if(!$master_user_id) {

                    // Check already record exists

                    $check_admin_user_details = User::where('email' , Auth::guard('admin')->user()->email)->first();

                    if($check_admin_user_details) {

                        $check_admin_user_details->is_master_user = 1;

                        if ($check_admin_user_details->save()) {


                        } else {

                            throw new Exception(tr('user_details_not_saved'));
                            
                        }

                    } else {

                        $check_admin_user_details = new User;

                        $check_admin_user_details->name = "Master User";

                        $check_admin_user_details->email = Auth::guard('admin')->user()->email;

                        $check_admin_user_details->password = \Hash::make("123456");

                        $check_admin_user_details->user_type = $check_admin_user_details->is_master_user = $check_admin_user_details->is_verified = $check_admin_user_details->status = 1;

                        $check_admin_user_details->device_type = WEB;

                        if ($check_admin_user_details->save()) {

                                $admin = Admin::where('email',  Auth::guard('admin')->user()->email)->first();

                                if ($admin) {

                                    $admin->user_id = $check_admin_user_details->id;

                                    $admin->save();
                                }   

                        } else {

                            throw new Exception(tr('user_details_not_saved'));
                        }

                    }

                    $master_user_id = $check_admin_user_details->id;

                }

                $master_user_details = User::find($master_user_id);

                // If master user details is not empty -> Login the admin as user

                if($master_user_details) {

                    Auth::loginUsingId($master_user_id, true);

                } else {

                    throw new Exception(tr('user_not_found'));

                }

            } else {

                throw new Exception(tr('admin_not_logged_in'));

            }

            DB::commit();

            return redirect()->to('/')->with('flash_success', tr('master_login_success'));

        } catch(Exception $e) {

            DB::rollback();

            $e = $e->getMessage();

            return back()->with('flash_error', $e);

        }

    }


    /**
     * Function Name : index()
     *
     * Show the user dashboard.
     * 
     * @param Object $request - User Details
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index(Request $request) {

        $database = config('database.connections.mysql.database');
        
        $username = config('database.connections.mysql.username');

        if($database && $username && Setting::get('installation_process') == 2) {

            counter('home');

            $watch_lists = $wishlists = array();

            if (Auth::check()) {
                
                $request->request->add([ 
                    'id'=>\Auth::user()->id,
                    'age' => \Auth::user()->age_limit,
                ]);   
            }

            if($request->has('id')){

                $wishlists = $this->UserAPI->wishlist_list($request)->getData();

                $watch_lists = $this->UserAPI->watch_list($request)->getData();  
            }


            $recent_videos = $this->UserAPI->recently_added($request)->getData();

            $trendings = $this->UserAPI->trending_list($request)->getData();
            
            $suggestions  = $this->UserAPI->suggestion_videos($request)->getData();

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

                $banner_ads = BannerAd::select('id as banner_id', 'file as image', 'title as video_title', 'description as content', 'link')
                            ->where('banner_ads.status', DEFAULT_TRUE)
                            ->orderBy('banner_ads.created_at' , 'desc')
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


    /**
     * Function Name : trending()
     *
     * To list out videos based on the watching count
     *
     * @param object $request - User Details
     *
     * @return video details
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

        $trending = $this->UserAPI->trending_list($request)->getData();

        return view('user.trending')->with('page', 'trending')
                                    ->with('videos',$trending);
    }

    /**
     * Function Name : channel_list()
     *
     * To list out channels which is created by all the users
     *
     * @param object $request - User Details
     *
     * @return channel details details
     */
    public function channel_list(Request $request){

        if(Auth::check()) {

            $request->request->add([ 
                'id' => \Auth::user()->id,
                'token' => \Auth::user()->token,
                'device_token' => \Auth::user()->device_token,
                'age'=>\Auth::user()->age_limit,
            ]);

        }


        $response = $this->UserAPI->channel_list($request)->getData();


        return view('user.channels.list')->with('page', 'channels')
                ->with('subPage', 'channel_list')
                ->with('response', $response);

    }

    /**
     * Function Name : history()
     *
     * To list out history of user based
     *
     * @param object $request - User Details
     *
     * @return array of history 
     */
    public function history(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token,
            'age'=>\Auth::user()->age_limit,
        ]);

        $histories = $this->UserAPI->watch_list($request)->getData();

        return view('user.account.history')
                        ->with('page' , 'history')
                        ->with('subPage' , 'user-history')
                        ->with('histories' , $histories);
    
    }


    /**
     * Function Name : wishlist()
     *
     * To list out wishlist of user based
     *
     * @param object $request - User Details
     *
     * @return array of wishlist 
     */
    public function wishlist(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token,
            'age'=>\Auth::user()->age_limit,
        ]);
        
        $videos = $this->UserAPI->wishlist_list($request)->getData();

        return view('user.account.wishlist')
                    ->with('page' , 'wishlist')
                    ->with('subPage' , 'user-wishlist')
                    ->with('videos' , $videos);
    
    }

    /**
     * Function Name : channel_videos()
     *
     * Based on the channel id , channel related videos will display
     *
     * @param integer $id : Channel Id
     *
     * @return channel videos list
     */
    public function channel_videos($id , Request $request) {

        $channel = Channel::where('channels.is_approved', DEFAULT_TRUE)
                ->where('id', $id)
                ->first();

        if ($channel) {

            $request->request->add([ 
                'age' => \Auth::check() ? \Auth::user()->age_limit : "",
            ]);

            $videos = $this->UserAPI->channel_videos($id, 0 , $request)->getData();

            $channel_owner_id = Auth::check() ? ($channel->user_id == Auth::user()->id ? $channel->user_id : "") : "";

            $trending_videos = $this->UserAPI->channel_trending($id, 5 , $channel_owner_id , $request)->getData();

            $payment_videos = $this->UserAPI->payment_videos($id, 0)->getData();

            $user_id = Auth::check() ? Auth::user()->id : '';

            $subscribe_status = false;

            if ($user_id) {

                $subscribe_status = check_channel_status($user_id, $id);

            }

            $subscriberscnt = subscriberscnt($channel->id);

            return view('user.channels.index')
                        ->with('page' , 'channels_'.$id)
                        ->with('subPage' , 'channels')
                        ->with('channel' , $channel)
                        ->with('videos' , $videos)->with('trending_videos', $trending_videos)
                        ->with('payment_videos', $payment_videos)
                        ->with('subscribe_status', $subscribe_status)
                        ->with('subscriberscnt', $subscriberscnt);
        } else {

            return back()->with('flash_error', tr('channel_not_found'));

        }
    }

    /**
     * Function Name : single_video()
     * 
     * To view single video based on video id
     *
     * @param integer $request - Video id
     *
     * @return based on video displayed all the details'
     */
    public function single_video(Request $request) {

        $request->request->add([ 
                'video_tape_id' => $request->id,
        ]);

        if (Auth::check()) {

            $request->request->add([ 
                'id'=>Auth::user()->id,
                'age_limit'=>Auth::user()->age_limit,
            ]);

        } else {
             $request->request->add([ 
                'id'=> '',
            ]);
        }

        $data = $this->UserAPI->video_detail($request)->getData();

        if (isset($data->url)) {

            return redirect($data->url);
        }

        if ($data->success) {

            $response = $data->response_array;
        
            return view('user.single-video')
                        ->with('page' , '')
                        ->with('subPage' , '')
                        ->with('video' , $response->video)
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
                        ->with('comment_rating_status', $response->comment_rating_status)
                        ->with('embed_link', $response->embed_link);
       
        } else {

            $error_message = isset($data->error_messages) ? $data->error_messages : tr('something_error');

            return back()->with('flash_error', $error_message);
            
        } 
    }


    /**
     * Function Name : profile()
     *
     * Show the profile list.
     *
     * @param object $request - User Details
     *
     * @return \Illuminate\Http\Response
     */
    public function profile(Request $request) {
        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token,
            'age'=>\Auth::user()->age_limit,
        ]);

        $wishlist = $this->UserAPI->wishlist_list($request)->getData();

        return view('user.account.profile')
                    ->with('page' , 'profile')
                    ->with('subPage' , 'user-profile')->with('wishlist', $wishlist);
    }

    /**
     * Function Name : update_profile() 
     *
     * Edit profile user details
     * 
     * @param object $request - User Details
     *
     * @return \Illuminate\Http\Response
     */
    public function update_profile(Request $request){

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token,
            'age'=>\Auth::user()->age_limit,
        ]);

        $wishlist = $this->UserAPI->wishlist_list($request)->getData();

        return view('user.account.edit-profile')->with('page' , 'profile')
                    ->with('subPage' , 'user-update-profile')
                    ->with('wishlist', $wishlist);
    
    }

    /**
     * Function Name : update_profile() 
     *
     * Save any changes to the users profile.
     * 
     * @param object $request - User Details
     *
     * @return \Illuminate\Http\Response
     */
    public function profile_save(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token,
        ]);

        $response = $this->UserAPI->update_profile($request)->getData();

        if($response->success) {

            return redirect(route('user.profile'))->with('flash_success' , tr('profile_updated'));

        } else {

            $message = isset($response->error) ? $response->error : " "." ".$response->error_messages;

            return back()->with('flash_error' , $message);
        }
    
    }

    /**
     * Function Name : profile_save_password() 
     * 
     * Save changed password.
     * 
     * @param object $request - User Details
     *
     * @return \Illuminate\Http\Response
     */
    public function profile_save_password(Request $request) {
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
    
    }


    /**
     * Function Name : profile_change_password() 
     * 
     * Display only password change form
     * 
     * @param object $request - User Details
     *
     * @return \Illuminate\Http\Response
     */
    public function profile_change_password(Request $request) {

        return view('user.account.change-password')->with('page' , 'profile')
                    ->with('subPage' , 'user-change-password');

    }

    /**
     * Function Name : add_history()
     *
     * To Add in history based on user, once he complete the video , the video will save
     *
     * @param Integer $request - Video Id
     *
     * @return response of Boolean with message
     */
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

            $response->message = tr('something_error');

        }

        $response->status = $request->status;

        return response()->json($response);
    
    }
 
    /**
     * Function Name : watch_count()
     *
     * To save watch count when ever user see the video
     *
     * @param Integer $request - Video Tape Id
     *
     * @return response of boolean
     */
    public function watch_count(Request $request) {

        if($video = VideoTape::where('id',$request->video_tape_id)
                ->where('status',1)
                ->where('video_tapes.is_approved' , 1)
                ->first()) {

            \Log::info("ADD History - Watch Count Start");

            if($video->getVideoAds) {

                \Log::info("getVideoAds Relation Checked");

                if ($video->getVideoAds->status) {

                    \Log::info("getVideoAds Status Checked");

                    // Check the video view count reached admin viewers count, to add amount for each view

                    if ($video->user_id != Auth::user()->id) {

                        if($video->watch_count >= Setting::get('viewers_count_per_video') && $video->ad_status) {

                            \Log::info("Check the video view count reached admin viewers count, to add amount for each view");

                            $video_amount = Setting::get('amount_per_video');

                            // $video->redeem_count = 1;

                            // $video->watch_count = $video->watch_count + 1;

                            $video->amount += $video_amount;

                            add_to_redeem($video->user_id , $video_amount);

                            \Log::info("ADD History - add_to_redeem");


                        } else {

                            \Log::info("ADD History - NO REDEEM");

                            // $video->redeem_count += 1;

                            // $video->watch_count = $video->watch_count + 1;
                        }

                    }

                }
            }

            $video->watch_count += 1;

            $video->save();

            \Log::info("ADD History - Watch Count Start");

            return response()->json(['success'=>true, 
                    'data'=>['watch_count'=>number_format_short($video->watch_count)]]);

        } else {

            return response()->json(['success'=>false]);
        }

    }

    /**
     * Function Name : delete_history()
     *
     * To delete a history based on logged in user id
     *
     * @param integer $request - Video Tape Id
     *
     * @return response of success/falure message
     */
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


    /**
     * Function Name : add_wishlist()
     *
     * Add a wishlist based on logged in user id
     *
     * @param integer $request - Video Tape Id
     *
     * @return response of success/falure message
     */
    public function add_wishlist(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token,
            'video_tape_id' => $request->video_tape_id
        ]);

        $response = $this->UserAPI->add_wishlist($request)->getData();

        if($response->success) {

            $response->message = Helper::get_message(118);

        } else {

            $response->success = false;

            $response->message = tr('something_error');
        }

        $response->status = $request->status;

        return response()->json($response);
    }

    /**
     * Function Name : delete_wishlist()
     *
     * To delete wishlist based on user id
     * 
     * @param intger $request - Video tape id
     *
     * @return response of success/failure message
     */
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

            return back()->with('flash_error', tr('something_error'));
        }
    } 

    /**
     * Function Name : add_comment()
     * 
     * To Add comment based on single video
     *
     * @param integer $video_tape_id - Video Tape ID
     *
     * @return response of success/failure message
     */
    public function add_comment(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token,
            'video_tape_id'=>$request->video_tape_id
        ]);

        $response = $this->UserAPI->user_rating($request)->getData();

        if($response->success) {

            $response->message = Helper::get_message(118);

        } else {

            $response->success = false;

            $response->message = tr('something_error');
        }

        return response()->json($response);
    
    }


    /**
     * Function Name : channel_create()
     *
     * To create a channel based on logged in user id  (Form Rendering)
     *
     * @return respnse with flash message
     */
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

    /**
     * Function Name : save_channel()
     *
     * To create a channel based on logged in user id
     *
     * @param Object $request - Channel Details
     *
     * @return respnse with flash message
     */
    public function save_channel(Request $request) {

         $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'channel_id' =>$request->id,
        ]);

        $response = CommonRepo::channel_save($request)->getData();

        if($response->success) {
            // $response->message = Helper::get_message(118);
            return redirect(route('user.channel', ['id'=>$response->data->id]))
                ->with('flash_success', $response->message);
        } else {
            
            return back()->with('flash_error', $response->error);
        }

    }


    /**
     * Function Name : channel_edit()
     *
     * To edit a channel based on logged in user id (Form Rendering)
     *
     * @param integer $id - Channel Id
     *
     * @return respnse with Html Page
     */
    public function channel_edit($id) {

        $model = Channel::find($id);

        return view('user.channels.edit')->with('page', 'channels')
                    ->with('subPage', 'edit_channel')->with('model', $model);

    }

    /**
     * Function Name : channel_delete()
     *
     * To delete a channel based on logged in user id & channel id (Form Rendering)
     *
     * @param integer $request - Channel Id
     *
     * @return response with flash message
     */
    public function channel_delete(Request $request) {

        $channel = Channel::where('id' , $request->id)->first();

        if($channel) {       

            $channel->delete();

            return redirect(route('user.dashboard'))->with('flash_success',tr('channel_delete_success'));

        } else {

            return back()->with('flash_error',tr('something_error'));

        }

    }

    /**
     * Function Name : delete_account()
     *
     * To delete account , based on the user (Form Rendering)
     *
     * @param object $request - User Details
     *
     * @return response of success/failure message
     */
    public function delete_account(Request $request) {

        if(\Auth::user()->login_by == 'manual') {

            return view('user.account.delete-account')
                    ->with('page' , 'profile')
                    ->with('subPage' , 'delete-account');
        } else {

            return $this->delete_account_process($request);

        }
        
    }

    /**
     * Function Name : delete_account()
     *
     * To delete account , based on the user
     *
     * @param object $request - User Details
     *
     * @return response of success/failure message
     */
    public function delete_account_process(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
            'token' => \Auth::user()->token,
            'device_token' => \Auth::user()->device_token
        ]);

        $response = $this->UserAPI->delete_account($request)->getData();

        if($response->success) {
            
            return redirect(route('user.dashboard'))->with('flash_success', tr('user_account_delete_success'));

        } else {

            return back()->with('flash_error', $response->error_messages);
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
        $model = Flag::where('video_tape_id', $id)->where('user_id', Auth::user()->id)->first();

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

            return response()->json(['status'=>$data->ad_status, 'success'=>true], 200);

        } else {

            return response()->json(['success'=>false], 200);
            
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

        if (!$page) {

            return back()->with('flash_error', tr('no_page_found'));

        }

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
                                'users.picture as user_image',
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
            
            $response->message = tr('adding_cards_not_enabled_application');

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

                $cards->cvv = $request->cvv;

                $cards->card_name = $request->card_name;

                $cards->month = $request->month;

                $cards->year = $request->year;

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
        
        return back()->with('flash_success', tr('successfully_created'));
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

                if ($subscription->amount <= 0) {

                    return back()->with('flash_error', tr('cannot_pay_zero_amount'));

                }

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


                            $last_payment = UserPayment::where('user_id' , $request->id)
                                    ->where('status', DEFAULT_TRUE)
                                    ->orderBy('created_at', 'desc')
                                    ->first();

                            $user_payment = new UserPayment;

                            if($last_payment) {

                                if (strtotime($last_payment->expiry_date) >= strtotime(date('Y-m-d H:i:s'))) {

                                    $user_payment->expiry_date = date('Y-m-d H:i:s', strtotime("+{$subscription->plan} months", strtotime($last_payment->expiry_date)));

                                } else {

                                    $user_payment->expiry_date = date('Y-m-d H:i:s',strtotime("+{$subscription->plan} months"));
                                }    

                            } else {
                                
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

                    return back()->with('flash_error', Helper::get_error_message(901).tr('default_card_add_message').'  <a href='.route('user.card.card_details').'>Add Card</a>');
                    
                    //return response()->json($response_array , 200);
                }

            } else {

                // $response_array = array('success' => false, 'error' => Helper::get_error_message(901) , 'error_code' => 901);
                return back()->with('flash_error', Helper::get_error_message(901).'. '.tr('default_card_add_message').'  <a href='.route('user.card.card_details').'>Add Card</a>');
                
                // return response()->json($response_array , 200);
            }



        }

    }

    public function subscribed_channels(Request $request) {

        $request->request->add([ 
            'id' => \Auth::user()->id,
        ]);        

        if ($request->id) {

            $channel_id = ChannelSubscription::where('user_id', $request->id)->pluck('channel_id')->toArray();

            $request->request->add([ 
                'channel_id' => $channel_id,
            ]);        
        }

        $response = $this->UserAPI->channel_list($request)->getData();

        // dd($response);

        return view('user.channels.list')->with('page', 'channels')
                ->with('subPage', 'channel_list')
                ->with('response', $response);

    }


    public function partialVideos(Request $request) {

        // Get Videos

        $videos = $this->UserAPI->channel_videos($request->channel_id, $request->skip)->getData();

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

       // $payment_videos = VideoRepo::payment_videos($request->channel_id, null, $request->skip);

        $payment_videos = $this->UserAPI->payment_videos($request->channel_id, $request->skip)->getData();


        $view = View::make('user.videos.partial_payment_videos')
                    ->with('payment_videos', $payment_videos)->render();

        return response()->json(['view'=>$view, 'length'=>$payment_videos->count]);
    }

    public function invoice(Request $request) {

        $model = $request->all();

        if (!$request->s_id) {

            return back()->with('flash_error', tr('something_error'));

        }

        $subscription = Subscription::find($request->s_id);

        if(!count($subscription)) {
            return redirect(route('user.dashboard'))->with('flash_error', tr('no_subscription_found'));
        }

        return view('user.invoice')->with('page', 'invoice')->with('subPage', 'invoice')->with('model', $model)->with('subscription',$subscription)->with('model',$model);
    }

    public function ppv_invoice($id) {

        $video = VideoTape::find($id);

        if ($video) {

            return view('user.ppv_invoice')
                ->with('page', 'ppv-invoice')
                ->with('video',$video)
                ->with('subPage', 'ppv-invoice');
                
        } else {

            return back()->with('flash_error', tr('video_not_found'));
        }
    }

    public function pay_per_view($id) {

        $video = VideoTape::find($id);

        if(!$video) {


            return back()->with('flash_error', tr('video_not_found'));

        }
        return view('user.pay_per_view')
                ->with('page', 'pay_per_view')
                ->with('subPage', 'pay_per_view')->with('video', $video);

    }


    /**
     * Function Name: payper_videos()
     * To load all the paper views
     *
     * @return view page
     */
    public function payper_videos(Request $request) {
        // Get Logged in user id
        $id = Auth::user()->id;

        $request->request->add([ 
            'id'=>\Auth::user()->id,
            'age' => \Auth::user()->age_limit,
        ]);  

        $model = $this->UserAPI->pay_per_videos($request)->getData();

        // Return the view page
        return view('user.payperview')->with('model' , $model)
                        ->with('page' , 'Profile')
                        ->with('subPage' , 'Payper Videos');
    }


    public function payment_type($id, Request $request) {

        if($request->payment_type == 1) {

            return redirect(route('user.ppv-video-payment', ['id'=>$id]));

        } else {

            return redirect(route('user.card.ppv-stripe-payment', ['video_tape_id'=>$id]));
        }
    }

    public function subscription_payment(Request $request) {

        if($request->payment_type == 1) {

            return redirect(route('user.paypal' , $request->s_id));

        } else {

            return redirect(route('user.card.stripe_payment' , ['subscription_id' => $request->s_id]));
        }
    }

    public function ppv_stripe_payment(Request $request) {

        $request->request->add([
            'id'=>Auth::user()->id,
            ]);

        $payment = $this->UserAPI->stripe_ppv($request)->getData();


        if ($payment->success) {

            return redirect(route('user.video.success',$request->video_tape_id))->with('flash_success', $payment->message);

        } else {


            if ($payment->error_code == 901) {

                return back()->with('flash_error', $payment->error_messages.'. '.tr('default_card_add_message').'  <a href='.route('user.card.card_details').'>'.tr('add_card').'</a>');

            }

            return back()->with('flash_error', $payment->error_messages);
        }
    }

    public function payment_success() {

        return view('user.subscription');
    }

    public function video_success($id = "") {

        if(!$id) {
            return redirect()->to('/')->with('flash_error' , tr('something_error'));
        }

        return view('user.video_subscription')->with('id', $id);
    }

    /**
     * Function Name : save_video_payment
     * Brief : To save the payment details
     *
     * @param integer $id Video Id
     * @param object  $request Object (Post Attributes)
     *
     * @return flash message
     */
    public function save_video_payment($id, Request $request) {

        // Load Video Model
        $model = VideoTape::find($id);

        // Get post attribute values and save the values
        if ($model) {

            $request->request->add([ 
                'ppv_created_by'=> Auth::user()->id ,
            ]); 

            if ($data = $request->all()) {

                // Update the post
                if (VideoTape::where('id', $id)->update($data)) {
                    // Redirect into particular value
                    return back()->with('flash_success', tr('payment_added'));       
                } 
            }
        }
        return back()->with('flash_error', tr('admin_published_video_failure'));
    }

    /**
     * Function Name : remove_payper_view()
     * To remove pay per view
     * 
     * @return falsh success
     */
    public function remove_payper_view($id) {
        
        // Load video model using auto increment id of the table
        $model = VideoTape::find($id);
        if ($model) {
            $model->ppv_amount = 0;
            $model->type_of_subscription = 0;
            $model->type_of_user = 0;
            $model->save();
            if ($model) {
                return back()->with('flash_success' , tr('removed_pay_per_view'));
            }
        }
        return back()->with('flash_error' , tr('admin_published_video_failure'));
    }

    public function my_channels(Request $request) {

        $request->request->add([
            'id'=>Auth::user()->id,
        ]);

        $response = $this->UserAPI->user_channel_list($request)->getData();


        return view('user.channels.list')->with('page', 'my_channel')
                ->with('subPage', 'channel_list')
                ->with('response', $response);
    }


    public function forgot_password(Request $request) {

        $response = $this->UserAPI->forgot_password($request)->getData();

        if ($response->success) {

            return back()->with('flash_success', $response->message);

        } else {

            return back()->with('flash_error', $response->error_messages);

        }
    }


    public function subscription_history(Request $request) {

        $request->request->add([ 
            'id'=>Auth::user()->id,
            'token'=>Auth::user()->token,
            'device_type'=>DEVICE_WEB,
        ]); 

        $response = $this->UserAPI->subscribedPlans($request)->getData();

        if ($response->success) {

            return view('user.history.subscription_history')->with('page', 'history')
                ->with('subPage', 'subscription_history')
                ->with('response', $response);

        } else {

            return back()->with('flash_error', $response->error_messages);

        }

    }


    public function ppv_history(Request $request) {

        $request->request->add([ 
            'id'=>Auth::user()->id,
            'token'=>Auth::user()->token,
            'device_type'=>DEVICE_WEB,
        ]); 

        $response = $this->UserAPI->ppv_list($request)->getData();

        if ($response->success) {

            return view('user.history.ppv_history')->with('page', 'history')
                ->with('subPage', 'ppv_history')
                ->with('response', $response);

        } else {

            return back()->with('flash_error', $response->error_messages);

        }

    }

}