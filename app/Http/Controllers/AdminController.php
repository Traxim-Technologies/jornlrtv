<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Requests;

use App\Admin;

use App\Moderator;

use App\VideoTape;

use App\AdminVideoImage;

use App\User;

use App\Subscription;

use App\UserPayment;

use App\UserHistory;

use App\Wishlist;

use App\Flag;

use App\UserRating;

use App\Settings;

use App\Page;

use App\Helpers\Helper;

use App\Helpers\EnvEditorHelper;

use Validator;

use Auth;

use Setting;

use Log;

use App\Jobs\CompressVideo;

use App\VideoAd;

use App\AdsDetail;

use App\Channel;

use App\Repositories\CommonRepository as CommonRepo;


use App\Jobs\NormalPushNotification;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');  
    }

    public function login() {
        return view('admin.login')->withPage('admin-login')->with('sub_page','');
    }

    public function dashboard() {

        $admin = Admin::first();

        $admin->token = Helper::generate_token();
        $admin->token_expiry = Helper::generate_token_expiry();

        $admin->save();
        
        $user_count = User::count();

        $provider_count = Moderator::count();

        $video_count = VideoTape::count();
 
        $recent_videos = Helper::recently_added();

        $get_registers = get_register_count();
        $recent_users = get_recent_users();
        $total_revenue = total_revenue();

        $view = last_days(10);

        // user_track();

        return view('admin.dashboard.dashboard')->withPage('dashboard')
                    ->with('sub_page','')
                    ->with('user_count' , $user_count)
                    ->with('video_count' , $video_count)
                    ->with('provider_count' , $provider_count)
                    ->with('get_registers' , $get_registers)
                    ->with('view' , $view)
                    ->with('total_revenue' , $total_revenue)
                    ->with('recent_users' , $recent_users)
                    ->with('recent_videos' , $recent_videos);
    }

    public function profile() {

        $admin = Admin::first();
        return view('admin.account.profile')->with('admin' , $admin)->withPage('profile')->with('sub_page','');
    }

    public function profile_process(Request $request) {

        $validator = Validator::make( $request->all(),array(
                'name' => 'max:255',
                'email' => 'email|max:255',
                'mobile' => 'digits_between:6,13',
                'address' => 'max:300',
                'id' => 'required|exists:admins,id',
                'picture' => 'mimes:jpeg,jpg,png'
            )
        );
        
        if($validator->fails()) {
            $error_messages = implode(',', $validator->messages()->all());
            return back()->with('flash_errors', $error_messages);
        } else {
            
            $admin = Admin::find($request->id);
            
            $admin->name = $request->has('name') ? $request->name : $admin->name;

            $admin->email = $request->has('email') ? $request->email : $admin->email;

            $admin->mobile = $request->has('mobile') ? $request->mobile : $admin->mobile;

            $admin->gender = $request->has('gender') ? $request->gender : $admin->gender;

            $admin->address = $request->has('address') ? $request->address : $admin->address;

            if($request->hasFile('picture')) {
                Helper::delete_picture($admin->picture, "/uploads/images/");
                $admin->picture = Helper::normal_upload_picture($request->picture, "/uploads/images/");
            }
                
            $admin->remember_token = Helper::generate_token();
           // @ $admin->is_activated = 1;
            $admin->save();

            return back()->with('flash_success', tr('admin_not_profile'));
            
        }
    
    }

    public function change_password(Request $request) {

        $old_password = $request->old_password;
        $new_password = $request->password;
        $confirm_password = $request->confirm_password;
        
        $validator = Validator::make($request->all(), [              
                'password' => 'required|min:6',
                'old_password' => 'required',
                'confirm_password' => 'required|min:6',
                'id' => 'required|exists:admins,id'
            ]);

        if($validator->fails()) {

            $error_messages = implode(',',$validator->messages()->all());

            return back()->with('flash_errors', $error_messages);

        } else {

            $admin = Admin::find($request->id);

            if(\Hash::check($old_password,$admin->password))
            {
                $admin->password = \Hash::make($new_password);
                $admin->save();

                return back()->with('flash_success', tr('password_change_success'));
                
            } else {
                return back()->with('flash_error', tr('password_mismatch'));
            }
        }

        $response = response()->json($response_array,$response_code);

        return $response;
    }

    public function users() {

        $users = User::orderBy('created_at','desc')->get();

        return view('admin.users.users')->withPage('users')
                        ->with('users' , $users)
                        ->with('sub_page','view-user');
    }

    public function add_user() {
        return view('admin.users.add-user')->with('page' , 'users')->with('sub_page','add-user');
    }

    public function edit_user(Request $request) {

        $user = User::find($request->id);
        return view('admin.users.edit-user')->withUser($user)->with('sub_page','view-user')->with('page' , 'users');
    }

    public function add_user_process(Request $request) {

        if($request->id != '') {

            $validator = Validator::make( $request->all(), array(
                        'name' => 'required|max:255',
                        'email' => 'required|email|max:255',
                        'mobile' => 'required|digits_between:6,13',
                    )
                );
        
        } else {
            $validator = Validator::make( $request->all(), array(
                    'name' => 'required|max:255',
                    'email' => 'required|email|max:255|unique:users,email',
                    'mobile' => 'required|digits_between:6,13',
                )
            );
        
        }
       
        if($validator->fails())
        {
            $error_messages = implode(',', $validator->messages()->all());
            return back()->with('flash_errors', $error_messages);
        } else {

            if($request->id != '') {
                $user = User::find($request->id);
                $message = tr('admin_not_user');
            } else {
                //Add New User
                $user = new User;
                $new_password = time();
                $new_password .= rand();
                $new_password = sha1($new_password);
                $new_password = substr($new_password, 0, 8);
                $user->password = \Hash::make($new_password);
                $message = tr('admin_add_user');
                $user->login_by = 'manual';
                $user->device_type = 'web';
            }

            $user->timezone = $request->has('timezone') ? $request->timezone : '';

            $user->name = $request->has('name') ? $request->name : '';
            $user->email = $request->has('email') ? $request->email: '';
            $user->mobile = $request->has('mobile') ? $request->mobile : '';
            
            $user->token = Helper::generate_token();
            $user->token_expiry = Helper::generate_token_expiry();
            // $user->is_activated = 1;                   

            if($request->id == ''){
                $email_data['name'] = $user->name;
                $email_data['password'] = $new_password;
                $email_data['email'] = $user->email;

                $subject = tr('user_welcome_title');
                $page = "emails.admin_user_welcome";
                $email = $user->email;
                Helper::send_email($page,$subject,$email,$email_data);
            }

            $user->save();

            // Check the default subscription and save the user type 

            user_type_check($user->id);

            if($user) {
                register_mobile('web');
                return redirect('/admin/view/user/'.$user->id)->with('flash_success', $message);
            } else {
                return back()->with('flash_error', tr('admin_not_error'));
            }

        }
    
    }

    public function delete_user(Request $request) {
        if($user = User::where('id',$request->id)->first()) {
            // Check User Exists or not
            if ($user) {
                if ($user->device_type) {
                    // Load Mobile Registers
                    subtract_count($user->device_type);
                }
                // After reduce the count from mobile register model delete the user
                if ($user->delete()) {
                    return back()->with('flash_success',tr('admin_not_user_del'));   
                }
            }
        }
        return back()->with('flash_error',tr('admin_not_error'));
    }

    public function view_user($id) {

        if($user = User::find($id)) {

            return view('admin.users.user-details')
                        ->with('user' , $user)
                        ->withPage('users')
                        ->with('sub_page','users');

        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }
    }

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

                $subject = tr('user_welcome_title');
                $page = "emails.moderator_welcome";
                $email = $user->email;
                $email_data['name'] = $moderator_user->name;
                $email_data['email'] = $moderator_user->email;
                $email_data['password'] = $new_password;

                Helper::send_email($page,$subject,$email,$email_data);

                $moderator = $moderator_user;

            }

            if($moderator) {
                $user->is_moderator = 1;
                $user->moderator_id = $moderator->id;
                $user->save();

                // $moderator->is_activated = 1;
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

    public function user_upgrade_disable(Request $request) {

        if($moderator = Moderator::find($request->moderator_id)) {

            if($user = User::find($request->id)) {
                $user->is_moderator = 0;
                $user->save();
            }

            // $moderator->is_activated = 0;

            $moderator->save();

            return back()->with('flash_success',tr('admin_user_upgrade_disable'));

        } else {

            return back()->with('flash_error',tr('admin_not_error'));
        }
    }

    public function view_history($id) {

        if($user = User::find($id)) {

            $user_history = UserHistory::where('user_id' , $id)
                            ->leftJoin('users' , 'user_histories.user_id' , '=' , 'users.id')
                            ->leftJoin('video_tapes' , 'user_histories.video_tape_id' , '=' , 'video_tapes.id')
                            ->select(
                                'users.name as username' , 
                                'users.id as user_id' , 
                                'user_histories.video_tape_id',
                                'user_histories.id as user_history_id',
                                'video_tapes.title',
                                'user_histories.created_at as date'
                                )
                            ->get();

            return view('admin.users.user-history')
                        ->with('data' , $user_history)
                        ->withPage('users')
                        ->with('sub_page','users');

        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }
    }

    public function delete_history($id) {

        if($user_history = UserHistory::find($id)) {

            $user_history->delete();

            return back()->with('flash_success',tr('admin_not_history_del'));

        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }
    }

    public function view_wishlist($id) {

        if($user = User::find($id)) {

            $user_wishlist = Wishlist::where('user_id' , $id)
                            ->leftJoin('users' , 'wishlists.user_id' , '=' , 'users.id')
                            ->leftJoin('video_tapes' , 'wishlists.video_tape_id' , '=' , 'video_tapes.id')
                            ->select(
                                'users.name as username' , 
                                'users.id as user_id' , 
                                'wishlists.video_tape_id',
                                'wishlists.id as wishlist_id',
                                'video_tapes.title',
                                'wishlists.created_at as date'
                                )
                            ->get();

            return view('admin.users.user-wishlist')
                        ->with('data' , $user_wishlist)
                        ->withPage('users')
                        ->with('sub_page','users');

        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }
    }

    public function delete_wishlist($id) {

        if($user_wishlist = Wishlist::find($id)) {

            $user_wishlist->delete();

            return back()->with('flash_success',tr('admin_not_wishlist_del'));

        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }
    }

    public function moderators() {

        $moderators = Moderator::orderBy('created_at','desc')->get();

        return view('admin.moderators.moderators')->with('moderators' , $moderators)->withPage('moderators')->with('sub_page','view-moderator');
    
    }

    public function add_moderator() {
        return view('admin.moderators.add-moderator')->with('page' ,'moderators')->with('sub_page' ,'add-moderator');
    }

    public function edit_moderator($id) {

        $moderator = Moderator::find($id);

        return view('admin.moderators.edit-moderator')->with('moderator' , $moderator)->with('page' ,'moderators')->with('sub_page' ,'edit-moderator');
    }

    public function add_moderator_process(Request $request) {

        if($request->id != '') {
            $validator = Validator::make( $request->all(), array(
                        'name' => 'required|max:255',
                        'email' => 'required|email|max:255',
                        'mobile' => 'required|digits_between:6,13',
                    )
                );
        } else {
            $validator = Validator::make( $request->all(), array(
                    'name' => 'required|max:255',
                    'email' => 'required|email|max:255|unique:moderators,email',
                    'mobile' => 'required|digits_between:6,13',
                )
            );
        
        }
       
        if($validator->fails()) {
            $error_messages = implode(',', $validator->messages()->all());
            return back()->with('flash_errors', $error_messages);

        } else {

            if($request->id != '') {
                $user = Moderator::find($request->id);
                $message = tr('admin_not_moderator');
            } else {
                $message = tr('admin_add_moderator');
                //Add New User
                $user = new Moderator;
                $new_password = time();
                $new_password .= rand();
                $new_password = sha1($new_password);
                $new_password = substr($new_password, 0, 8);
                $user->password = \Hash::make($new_password);
                $user->is_activated = 1;

            }

            $user->timezone = $request->has('timezone') ? $request->timezone : '';
            $user->name = $request->has('name') ? $request->name : '';
            $user->email = $request->has('email') ? $request->email: '';
            $user->mobile = $request->has('mobile') ? $request->mobile : '';
            
            $user->token = Helper::generate_token();
            $user->token_expiry = Helper::generate_token_expiry();
                               

            if($request->id == ''){
                $email_data['name'] = $user->name;
                $email_data['password'] = $new_password;
                $email_data['email'] = $user->email;

                $subject = tr('user_welcome_title');
                $page = "emails.moderator_welcome";
                $email = $user->email;
                Helper::send_email($page,$subject,$email,$email_data);
            }

            $user->save();

            if($user) {
                return redirect('/admin/view/moderator/'.$user->id)->with('flash_success', $message);
            } else {
                return back()->with('flash_error', tr('admin_not_error'));
            }

        }
    
    }

    public function delete_moderator(Request $request) {

        if($moderator = Moderator::find($request->id)) {

            $moderator->delete();

            return back()->with('flash_success',tr('admin_not_moderator_del'));

        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }
    
    }

    public function moderator_approve(Request $request) {

        $moderator = Moderator::find($request->id);

        $moderator->is_activated = 1;

        $moderator->save();

        if($moderator->is_activated ==1) {
            $message = tr('admin_not_moderator_approve');
        } else {
            $message = tr('admin_not_moderator_decline');
        }

        return back()->with('flash_success', $message);
    
    }

    public function moderator_decline(Request $request) {
        
        if($moderator = Moderator::find($request->id)) {
            
            $moderator->is_activated = 0;

            $moderator->save(); 

            $message = tr('admin_not_moderator_decline');
        
            return back()->with('flash_success', $message);  
        } else {
            return back()->with('flash_error' , tr('admin_not_error'));
        }
            
    }

    public function moderator_view_details($id) {

        if($moderator = Moderator::find($id)) {
            return view('admin.moderators.moderator-details')->with('moderator' , $moderator)
                        ->withPage('moderator')
                        ->with('sub_page','view-moderators');
        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }
    
    }

    public function videos(Request $request) {

        $videos = VideoTape::select('video_tapes.id as video_id' ,
                                'video_tapes.title' , 
                             'video_tapes.description' , 'video_tapes.ratings' , 
                             'video_tapes.reviews' , 'video_tapes.created_at as video_date' ,
                             'video_tapes.default_image',
                             // 'video_tapes.banner_image',
                             // 'video_tapes.amount',
                            /* 'video_tapes.type_of_user',
                             'video_tapes.type_of_subscription',*/
                             
                             //'video_tapes.is_home_slider',
                             'video_tapes.status',
                             // 'video_tapes.uploaded_by',
                             // 'video_tapes.edited_by',
                             'video_tapes.is_approved')
                    ->orderBy('video_tapes.created_at' , 'desc')
                    ->get();

        return view('admin.videos.videos')->with('videos' , $videos)
                    ->withPage('videos')
                    ->with('sub_page','view-videos');
   
    }

    public function add_video(Request $request) {

        $channels = loadChannels();

         return view('admin.videos.video_upload')
                ->with('channels' , $channels)
                ->with('page' ,'videos')
                ->with('sub_page' ,'add-video');

    }

    public function edit_video(Request $request) {

        Log::info("Queue Driver ".env('QUEUE_DRIVER'));

        $categories =  [];

        $video = VideoTape::where('video_tapes.id' , $request->id)
                    ->leftJoin('categories' , 'video_tapes.category_id' , '=' , 'categories.id')
                    ->leftJoin('sub_categories' , 'video_tapes.sub_category_id' , '=' , 'sub_categories.id')
                    ->leftJoin('genres' , 'video_tapes.genre_id' , '=' , 'genres.id')
                    ->select('video_tapes.id as video_id' ,'video_tapes.title' , 
                             'video_tapes.description' , 'video_tapes.ratings' , 
                             'video_tapes.reviews' , 'video_tapes.created_at as video_date' ,'video_tapes.is_banner','video_tapes.banner_image',
                             'video_tapes.video','video_tapes.trailer_video',
                             'video_tapes.video_type','video_tapes.video_upload_type',
                             'video_tapes.publish_time','video_tapes.duration',

                             'video_tapes.category_id as category_id',
                             'video_tapes.sub_category_id',
                             'video_tapes.genre_id',
                             'video_tapes.default_image',
                             'categories.name as category_name' , 'categories.is_series',
                             'sub_categories.name as sub_category_name' ,
                             'genres.name as genre_name')
                    ->orderBy('video_tapes.created_at' , 'desc')
                    ->first();

        $page = 'videos';
        $sub_page = 'add-video';

        $subcategories = [];

        if($video->category_id) {
            $subcategories = get_sub_categories($video->category_id);
        }

        if($video->is_banner == 1) {
            $page = 'banner-videos';
            $sub_page = 'banner-videos';
        }

         return view('admin.videos.edit-video')
                ->with('categories' , $categories)
                ->with('video' ,$video)
                ->with('page' ,$page)
                ->with('sub_page' ,$sub_page)->with('subCategories',$subcategories);
    }

    public function add_video_process(Request $request) {

        Log::info("Initiaization Add Process : ".print_r($request->all(),true));

        Log::info("Max Upload Size : ".print_r(ini_get('upload_max_filesize'),true));

        Log::info("Post Max Size : ".print_r(ini_get('post_max_size'),true));

        if($request->has('video_type') && $request->video_type == VIDEO_TYPE_UPLOAD) {

            $video_validator = Validator::make( $request->all(), array(
                        'video'     => 'required|mimes:mkv,mp4,qt',
                        'trailer_video'  => 'required|mimes:mkv,mp4,qt',
                        )
                    );

            $video_link = $request->file('video');
            $trailer_video = $request->file('trailer_video');

            Log::info("Inside Main Video".$video_link);
            Log::info("Inside Trailer Video".$trailer_video);

        } else {

            $video_validator = Validator::make( $request->all(), array(
                        'other_video'     => 'required',
                        'other_trailer_video'  => 'required',
                        )
                    );

            $video_link = $request->other_video;
            $trailer_video = $request->other_trailer_video;

        }

        if($video_validator) {

             Log::info("Inside Video Validator");

             if($video_validator->fails()) {

                Log::info("Fails Validator 2");

                $error_messages = implode(',', $video_validator->messages()->all());

                Log::info("Errors :".print_r($error_messages, true));

                if ($request->has('ajax_key')) {
                    return $error_messages;
                } else {
                    return back()->with('flash_errors', $error_messages);
                }
            }
        }
        $validator = Validator::make( $request->all(), array(
                    'title'         => 'required|max:255',
                    'description'   => 'required',
                    'category_id'   => 'required|integer|exists:categories,id',
                    'sub_category_id' => 'required|integer|exists:sub_categories,id,category_id,'.$request->category_id,
                    'genre'     => 'exists:genres,id,sub_category_id,'.$request->sub_category_id,
                    'default_image' => 'required|mimes:jpeg,jpg,bmp,png',
                    'banner_image' => 'mimes:jpeg,jpg,bmp,png',
                    'other_image1' => 'required|mimes:jpeg,jpg,bmp,png',
                    'other_image2' => 'required|mimes:jpeg,jpg,bmp,png',
                    'ratings' => 'required',
                    'reviews' => 'required',
                    'duration'=>'required',
                    )
                );
        if($validator->fails()) {

            Log::info("Fails Validator 1");

            $error_messages = implode(',', $validator->messages()->all());

            if ($request->has('ajax_key')) {
                return $error_messages;
            } else  {
                return back()->with('flash_errors', $error_messages);
            }

        } else {

            Log::info("Success validation and navigated to create new object");

            $video = new VideoTape;
            $video->title = $request->title;
            $video->description = $request->description;
            $video->category_id = $request->category_id;
            $video->sub_category_id = $request->sub_category_id;
            $video->genre_id = $request->has('genre_id') ? $request->genre_id : 0;

            if($request->has('duration')) {
                $video->duration = $request->duration;
            }

            $main_video_duration = null;
            $trailer_video_duration = null;

            if($request->video_type == VIDEO_TYPE_UPLOAD) {

                $video->video_upload_type = $request->video_upload_type;

                if($request->video_upload_type == VIDEO_UPLOAD_TYPE_s3) {

                    $video->video = Helper::upload_picture($video_link);
                    $video->trailer_video = Helper::upload_picture($trailer_video);

                } else {
                    // if(ini_get('upload_max_size') > )
                    $main_video_duration = Helper::video_upload($video_link, $request->compress_video);
                    $video->video = $main_video_duration['db_url'];
                    $trailer_video_duration = Helper::video_upload($trailer_video, $request->compress_video);
                    $video->trailer_video = $trailer_video_duration['db_url'];  
                    
                    $video->video_resolutions = ($request->video_resolutions) ? implode(',', $request->video_resolutions) : '';
                    $video->trailer_video_resolutions = ($request->video_resolutions) ? implode(',', $request->video_resolutions) : '';
                    /* $getDuration = readFileName($main_video_duration['baseUrl']);
                    if ($getDuration) {
                        $video->duration = $getDuration['hours'].':'.$getDuration['mins'].':'.$getDuration['secs'];
                    }     
                    $getTrailerDuration = readFileName($trailer_video_duration['baseUrl']);
                    if ($getTrailerDuration) {
                        $video->trailer_duration = $getTrailerDuration['hours'].':'.$getTrailerDuration['mins'].':'.$getTrailerDuration['secs'];
                    }  */  
                }     

            } elseif($request->video_type == VIDEO_TYPE_YOUTUBE) {

                $video->video = get_youtube_embed_link($video_link);
                // $video->duration = getYoutubeDuration($video->video);
                $video->trailer_video = get_youtube_embed_link($trailer_video);

                // $video->trailer_duration = getYoutubeDuration($video->trailer_video);
            } else {
                $video->video = $video_link;
                $video->trailer_video = $trailer_video;
            }

            $video->video_type = $request->video_type;


            $video->publish_time = date('Y-m-d H:i:s', strtotime($request->publish_time));
            
            $video->default_image = Helper::normal_upload_picture($request->file('default_image'), "/uploads/images/");

            if($request->is_banner) {
                $video->is_banner = 1;
                $video->banner_image = Helper::normal_upload_picture($request->file('banner_image'), "/uploads/images/");
            }

            $video->ratings = $request->ratings;
            $video->reviews = $request->reviews;             

            if(strtotime($request->publish_time) < strtotime(date('Y-m-d H:i:s'))) {
                $video->status = DEFAULT_TRUE;
            } else {
                $video->status = DEFAULT_FALSE;
            }

            if (empty($video->video_resolutions)) {
                $video->compress_status = DEFAULT_TRUE;
                $video->trailer_compress_status = DEFAULT_TRUE;
                $video->is_approved = DEFAULT_TRUE;
            }
            
            $video->uploaded_by = ADMIN;

            // dd($video);
            Log::info("Approved : ".$video->is_approved);

            $video->save();

            Log::info("saved Video Object : ".'Success');


            if($video) {

                if($video->video_resolutions) {
                    if ($main_video_duration) {
                        $inputFile = $main_video_duration['baseUrl'];
                        $local_url = $main_video_duration['local_url'];
                        $file_name = $main_video_duration['file_name'];
                        if (file_exists($inputFile)) {
                            Log::info("Main queue Videos : ".'Success');
                            dispatch(new CompressVideo($inputFile, $local_url, MAIN_VIDEO, $video->id, $file_name));
                            Log::info("Main Compress Status : ".$video->compress_status);
                            Log::info("Main queue completed : ".'Success');
                        }
                    }
                    if ($trailer_video_duration) {
                        $inputFile = $trailer_video_duration['baseUrl'];
                        $local_url = $trailer_video_duration['local_url'];
                        $file_name = $trailer_video_duration['file_name'];
                        if (file_exists($inputFile)) {
                            Log::info("Trailer queue Videos : ".'Success');
                            dispatch(new CompressVideo($inputFile, $local_url, TRAILER_VIDEO, $video->id,$file_name));
                            Log::info("Trailer Compress Status : ".$video->compress_status);
                            Log::info("Trailer queue completed : ".'Success');
                        }
                    }
                }
                
                Helper::upload_video_image($request->file('other_image1'),$video->id,2);

                Helper::upload_video_image($request->file('other_image2'),$video->id,3);

                if (env('QUEUE_DRIVER') != 'redis') {

                    \Log::info("Queue Driver : ".env('QUEUE_DRIVER'));

                    $video->compress_status = DEFAULT_TRUE;

                    $video->trailer_compress_status = DEFAULT_TRUE;

                    $video->save();
                }
                /*if($video->is_banner)
                    return redirect(route('admin.banner.videos'));
                else*/
                if ($request->has('ajax_key')) {
                    Log::info('Video Id Ajax : '.$video->id);
                    return ['id'=>route('admin.view.video', array('id'=>$video->id))];
                } else  {
                    Log::info('Video Id : '.$video->id);
                    return redirect(route('admin.view.video', array('id'=>$video->id)));
                }
            } else {
                if($request->has('ajax_key')) {
                    
                    return tr('admin_not_error');
                } else { 
                    return back()->with('flash_error', tr('admin_not_error'));
                }
            }
        }
    
    }

    public function edit_video_process(Request $request) {

        Log::info("Initiaization Edit Process : ".print_r($request->all(),true));


        $video = VideoTape::find($request->id);

        $video_validator = array();

        $video_link = $video->video;

        $trailer_video = $video->trailer_video;

        // dd($request->all());

        if($request->has('video_type') && $request->video_type == VIDEO_TYPE_UPLOAD) {

            Log::info("Video Type : ".$request->has('video_type'));

            if (isset($request->video)) {
                if ($request->video != '') {

                    $video_validator = Validator::make( $request->all(), array(
                            'video'     => 'required|mimes:mkv,mp4,qt',
                            // 'trailer_video'  => 'required|mimes:mkv,mp4,qt',
                            )
                        );

                    $video_link = $request->hasFile('video') ? $request->file('video') : array();   

                }
            }

            if (isset($request->trailer_video)) {
                if ($request->trailer_video != '') {
                    $video_validator = Validator::make( $request->all(), array(
                            // 'video'     => 'required|mimes:mkv,mp4,qt',
                            'trailer_video'  => 'required|mimes:mkv,mp4,qt',
                            )
                        );

                    $trailer_video = $request->hasFile('trailer_video') ? $request->file('trailer_video') : array();
                }
            }
        

        } elseif($request->has('video_type') && in_array($request->video_type , array(VIDEO_TYPE_YOUTUBE,VIDEO_TYPE_OTHER))) {

            $video_validator = Validator::make( $request->all(), array(
                        'other_video'     => 'required',
                        'other_trailer_video'  => 'required',
                        )
                    );

            $video_link = $request->has('other_video') ? $request->other_video : array();

            $trailer_video = $request->has('other_trailer_video') ? $request->other_trailer_video : array();
        }

        if($video_validator) {

             if($video_validator->fails()) {
                $error_messages = implode(',', $video_validator->messages()->all());
                if ($request->has('ajax_key')) {
                    return $error_messages;
                } else {
                    return back()->with('flash_errors', $error_messages);
                }
            }
        }

        $validator = Validator::make( $request->all(), array(
                    'id' => 'required|integer|exists:admin_videos,id',
                    'title'         => 'max:255',
                    'description'   => '',
                    'category_id'   => 'required|integer|exists:categories,id',
                    'sub_category_id' => 'required|integer|exists:sub_categories,id,category_id,'.$request->category_id,
                    'genre'     => 'exists:genres,id,sub_category_id,'.$request->sub_category_id,
                    // 'video'     => 'mimes:mkv,mp4,qt',
                    // 'trailer_video'  => 'mimes:mkv,mp4,qt',
                    'default_image' => 'mimes:jpeg,jpg,bmp,png',
                    'other_image1' => 'mimes:jpeg,jpg,bmp,png',
                    'other_image2' => 'mimes:jpeg,jpg,bmp,png',
                    'ratings' => 'required',
                    'reviews' => 'required',
                    )
                );

        if($validator->fails()) {
            $error_messages = implode(',', $validator->messages()->all());
            if ($request->has('ajax_key')) {
                return $error_messages;
            } else {
                return back()->with('flash_errors', $error_messages);
            }

        } else {

            Log::info("Success validation checking : Success");

            $video->title = $request->has('title') ? $request->title : $video->title;

            $video->description = $request->has('description') ? $request->description : $video->description;

            $video->category_id = $request->has('category_id') ? $request->category_id : $video->category_id;

            $video->sub_category_id = $request->has('sub_category_id') ? $request->sub_category_id : $video->sub_category_id;

            $video->genre_id = $request->has('genre_id') ? $request->genre_id : $video->genre_id;

            if($request->has('duration')) {
                $video->duration = $request->duration;
            }

            if(strtotime($request->publish_time) < strtotime(date('Y-m-d H:i:s'))) {
                $video->status = DEFAULT_TRUE;
            } else {
                $video->status = DEFAULT_FALSE;
            }

            $main_video_url = null;
            $trailer_video_url = null;

            if($request->video_type == VIDEO_TYPE_UPLOAD && $video_link && $trailer_video) {

                 Log::info("To Be upload videos : ".'Success');

                // Check Previous Video Upload Type, to delete the videos

                if($video->video_upload_type == VIDEO_UPLOAD_TYPE_s3) {
                    Helper::s3_delete_picture($video->video);   
                    Helper::s3_delete_picture($video->trailer_video);  
                } else {
                    $videopath = '/uploads/videos/original/';

                    // dd($request->all());

                    if ($request->hasFile('video')) {
                        Helper::delete_picture($video->video, $videopath); 
                        // @TODO
                        $splitVideos = ($video->video_resolutions) 
                                    ? explode(',', $video->video_resolutions)
                                    : [];
                        foreach ($splitVideos as $key => $value) {
                           Helper::delete_picture($video->video, $videopath.$value.'/');
                        }
                        Log::info("Deleted Main Video : ".'Success');   
                    }
                    if ($request->hasFile('trailer_video')) {
                        Helper::delete_picture($video->trailer_video, $videopath);
                        // @TODO
                        $splitTrailer = ($video->trailer_video_resolutions) 
                                    ? explode(',', $video->trailer_video_resolutions)
                                    : [];
                        foreach ($splitTrailer as $key => $value) {
                           Helper::delete_picture($video->trailer_video, $videopath.$value.'/');
                        }
                        Log::info("Deleted Trailer Video : ".'Success');
                    }
                }

                if($request->video_upload_type == VIDEO_UPLOAD_TYPE_s3) {
                    $video->video = Helper::upload_picture($video_link);
                    $video->trailer_video = Helper::upload_picture($trailer_video); 

                } else {
                    if ($request->hasFile('video')) {
                        $video->compress_status = DEFAULT_FALSE;
                        $video->is_approved = DEFAULT_FALSE;
                        $main_video_url = Helper::video_upload($video_link, $request->compress_video);
                        Log::info("New Video Uploaded ( Main Video ) : ".'Success');
                        $video->video = $main_video_url['db_url'];
                        $video->video_resolutions = ($request->video_resolutions) ? implode(',', $request->video_resolutions) : null;
                    } else {
                        $video->video = $video_link;
                    }
                    // dd($request->hasFile('trailer_video'));
                    if ($request->hasFile('trailer_video')) {
                        $video->trailer_compress_status = DEFAULT_FALSE;
                        $video->is_approved = DEFAULT_FALSE;
                        $trailer_video_url = Helper::video_upload($trailer_video, $request->compress_video);
                        Log::info("New Video Uploaded ( Trailer Video ) : ".'Success');
                        $video->trailer_video = $trailer_video_url['db_url']; 
                        $video->trailer_video_resolutions = ($request->video_resolutions) ? implode(',', $request->video_resolutions) : null; 
                    } else {
                        $video->trailer_video = $trailer_video;
                    }
                
                    Log::info("Video Resoltuions : ".print_r($video->video_resolutions, true));
                    Log::info("Trailer Video Resoltuions : ".print_r($video->trailer_video_resolutions, true));
                }                

            } elseif($request->video_type == VIDEO_TYPE_YOUTUBE && $video_link && $trailer_video) {

                $video->video = get_youtube_embed_link($video_link);
                $video->trailer_video = get_youtube_embed_link($trailer_video);
            } else {
                $video->video = $video_link ? $video_link : $video->video;
                $video->trailer_video = $trailer_video ? $trailer_video : $video->trailer_video;
            }

            if($request->hasFile('default_image')) {
                Helper::delete_picture($video->default_image, "/uploads/images/");
                $video->default_image = Helper::normal_upload_picture($request->file('default_image'), "/uploads/images/");
            }

            if($video->is_banner == 1) {
                if($request->hasFile('banner_image')) {
                    Helper::delete_picture($video->banner_image, "/uploads/images/");
                    $video->banner_image = Helper::normal_upload_picture($request->file('banner_image'), "/uploads/images/");
                }
            }

            $video->video_type = $request->video_type ? $request->video_type : $video->video_type;

            $video->video_upload_type = $request->video_upload_type ? $request->video_upload_type : $video->video_upload_type;

            $video->ratings = $request->has('ratings') ? $request->ratings : $video->ratings;

            $video->reviews = $request->has('reviews') ? $request->reviews : $video->reviews;

            $video->edited_by = ADMIN;

            if($video->video_type != VIDEO_TYPE_UPLOAD) {
                $video->trailer_resize_path = null;
                $video->video_resize_path = null;
                $video->trailer_video_resolutions = null;
                $video->video_resolutions = null;
            }

            if (empty($video->video_resolutions)) {
                $video->compress_status = DEFAULT_TRUE;
                $video->trailer_compress_status = DEFAULT_TRUE;
                $video->is_approved = DEFAULT_TRUE;
                Log::info("Empty Resoltuions");
            }

            Log::info("Approved : ".$video->is_approved);


            $video->save();

            Log::info("saved Video Object : ".'Success');

            if($video) {
                if ($request->hasFile('video') && $video->video_resolutions) {
                    if ($main_video_url) {
                        $inputFile = $main_video_url['baseUrl'];
                        $local_url = $main_video_url['local_url'];
                        $file_name = $main_video_url['file_name'];
                        if (file_exists($inputFile)) {
                            Log::info("Main queue Videos : ".'Success');
                            dispatch(new CompressVideo($inputFile, $local_url, MAIN_VIDEO, $video->id,$file_name));
                            Log::info("Main Compress Status : ".$video->compress_status);
                            Log::info("Main queue completed : ".'Success');
                        }
                    }
                }

                if($request->hasFile('trailer_video') && $video->trailer_video_resolutions) {
                    if ($trailer_video_url) {
                        $inputFile = $trailer_video_url['baseUrl'];
                        $local_url = $trailer_video_url['local_url'];
                        $file_name = $trailer_video_url['file_name'];
                        if (file_exists($inputFile)) {
                            Log::info("Trailer queue Videos : ".'Success');
                            dispatch(new CompressVideo($inputFile, $local_url, TRAILER_VIDEO, $video->id, $file_name));
                            Log::info("Trailer Compress Status : ".$video->compress_status);
                            Log::info("Trailer queue completed : ".'Success');
                        }
                    }
                }

                if($request->hasFile('other_image1')) {
                    Helper::upload_video_image($request->file('other_image1'),$video->id,2);  
                }

                if($request->hasFile('other_image2')) {
                   Helper::upload_video_image($request->file('other_image2'),$video->id,3); 
                }


                if (env('QUEUE_DRIVER') != 'redis') {

                    \Log::info("Queue Driver : ".env('QUEUE_DRIVER'));

                    $video->compress_status = DEFAULT_TRUE;

                    $video->trailer_compress_status = DEFAULT_TRUE;

                    $video->save();
                }

                if ($request->has('ajax_key')) {
                    return ['id'=>route('admin.view.video', array('id'=>$video->id))];
                } else {
                    return redirect(route('admin.view.video', array('id'=>$video->id)));
                }

            } else {
                if ($request->has('ajax_key')) {
                    return tr('admin_not_error');
                } else {
                    return back()->with('flash_error', tr('admin_not_error'));
                }
            }
        }
    
    }

    public function view_video(Request $request) {

        $validator = Validator::make($request->all() , [
                'id' => 'required|exists:admin_videos,id'
            ]);

        if($validator->fails()) {
            $error_messages = implode(',', $validator->messages()->all());
            return back()->with('flash_errors', $error_messages);
        } else {
            $videos = VideoTape::where('video_tapes.id' , $request->id)
                    ->leftJoin('categories' , 'video_tapes.category_id' , '=' , 'categories.id')
                    ->leftJoin('sub_categories' , 'video_tapes.sub_category_id' , '=' , 'sub_categories.id')
                    ->leftJoin('genres' , 'video_tapes.genre_id' , '=' , 'genres.id')
                    ->select('video_tapes.id as video_id' ,'video_tapes.title' , 
                             'video_tapes.description' , 'video_tapes.ratings' , 
                             'video_tapes.reviews' , 'video_tapes.created_at as video_date' ,
                             'video_tapes.video','video_tapes.trailer_video',
                             'video_tapes.default_image','video_tapes.banner_image','video_tapes.is_banner','video_tapes.video_type',
                             'video_tapes.video_upload_type',
                             'video_tapes.amount',
                             'video_tapes.type_of_user',
                             'video_tapes.type_of_subscription',
                             'video_tapes.category_id as category_id',
                             'video_tapes.sub_category_id',
                             'video_tapes.genre_id',
                             'video_tapes.video_type',
                             'video_tapes.video_upload_type',
                             'video_tapes.duration',
                             'video_tapes.compress_status',
                             'video_tapes.trailer_compress_status',
                             'video_tapes.video_resolutions',
                             'video_tapes.video_resize_path',
                             'video_tapes.trailer_resize_path',
                             'video_tapes.is_approved',
                             'video_tapes.trailer_video_resolutions',
                             'categories.name as category_name' , 'sub_categories.name as sub_category_name' ,
                             'genres.name as genre_name')
                    ->orderBy('video_tapes.created_at' , 'desc')
                    ->first();

        $videoPath = $video_pixels = $trailer_video_path = $trailer_pixels = $trailerstreamUrl = $videoStreamUrl = '';
        if ($videos->video_type == 1) {
            if (\Setting::get('streaming_url')) {
                $trailerstreamUrl = \Setting::get('streaming_url').get_video_end($videos->trailer_video);
                $videoStreamUrl = \Setting::get('streaming_url').get_video_end($videos->video);
                if ($videos->is_approved == 1) {
                    if($videos->trailer_video_resolutions) {
                        $trailerstreamUrl = Helper::web_url().'/uploads/smil/'.get_video_end_smil($videos->trailer_video).'.smil';
                    } 
                    if ($videos->video_resolutions) {
                        $videoStreamUrl = Helper::web_url().'/uploads/smil/'.get_video_end_smil($videos->video).'.smil';
                    }
                }
            } else {

                $videoPath = $videos->video_resize_path ? $videos->video.','.$videos->video_resize_path : $videos->video;
                $video_pixels = $videos->video_resolutions ? 'original,'.$videos->video_resolutions : 'original';
                $trailer_video_path = $videos->trailer_video_path ? $videos->trailer_video.','.$videos->trailer_video_path : $videos->trailer_video;
                $trailer_pixels = $videos->trailer_video_resolutions ? 'original'.$videos->trailer_video_resolutions : 'original';

                /*$trailerResolution = getResolutionsPath($videos->trailer_video, $videos->trailer_video_resolutions,);

                $trailer_re_path = $trailerResolution['video_resolutions'];
                $trailer_pixels = $trailerResolution['pixels'];
                
                $videoResolution = getResolutionsPath($videos->video, $videos->video_resolutions,\Setting::get('streaming_url'));

                $video_re_path = $videoResolution['video_resolutions'];
                $video_pixels = $videoResolution['pixels'];*/

            }
        } else {
            $trailerstreamUrl = $videos->trailer_video;
            $videoStreamUrl = $videos->video;
        }
        
        $admin_video_images = AdminVideoImage::where('admin_video_id' , $request->id)
                                ->orderBy('is_default' , 'desc')
                                ->get();

        $page = 'videos';
        $sub_page = 'add-video';

        if($videos->is_banner == 1) {
            $page = 'banner-videos';
            $sub_page = 'banner-videos';
        }

        return view('admin.videos.view-video')->with('video' , $videos)
                    ->with('video_images' , $admin_video_images)
                    ->withPage($page)
                    ->with('sub_page',$sub_page)
                    ->with('videoPath', $videoPath)
                    ->with('video_pixels', $video_pixels)
                    ->with('trailer_video_path', $trailer_video_path)
                    ->with('trailer_pixels', $trailer_pixels)
                    ->with('videoStreamUrl', $videoStreamUrl)
                    ->with('trailerstreamUrl', $trailerstreamUrl);
        }
    }

    public function approve_video($id) {

        $video = VideoTape::find($id);

        $video->is_approved = DEFAULT_TRUE;

        $video->save();

        if($video->is_approved == DEFAULT_TRUE)
        {
            $message = tr('admin_not_video_approve');
        }
        else
        {
            $message = tr('admin_not_video_decline');
        }
        return back()->with('flash_success', $message);
    }


    /**
     * Function Name : publish_video()
     * To Publish the video for user
     *
     * @param int $id : Video id
     *
     * @return Flash Message
     */
    public function publish_video($id) {
        // Load video based on Auto increment id
        $video = VideoTape::find($id);
        // Check the video present or not
        if ($video) {
            $video->status = DEFAULT_TRUE;
            $video->publish_time = date('Y-m-d H:i:s');
            // Save the values in DB
            if ($video->save()) {
                return back()->with('flash_success', tr('admin_published_video_success'));
            }
        }
        return back()->with('flash_error', tr('admin_published_video_failure'));
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

    public function delete_video($id) {

        if($video = VideoTape::where('id' , $id)->first())  {
            $video->delete();
        }

        return back()->with('flash_success', 'Video deleted successfully');
    }

    public function slider_video($id) {

        $video = VideoTape::where('is_home_slider' , 1 )->update(['is_home_slider' => 0]); 

        $video = VideoTape::where('id' , $id)->update(['is_home_slider' => 1] );

        return back()->with('flash_success', tr('slider_success'));
    
    }

    public function banner_videos(Request $request) {

        $videos = VideoTape::leftJoin('categories' , 'video_tapes.category_id' , '=' , 'categories.id')
                    ->leftJoin('sub_categories' , 'video_tapes.sub_category_id' , '=' , 'sub_categories.id')
                    ->leftJoin('genres' , 'video_tapes.genre_id' , '=' , 'genres.id')
                    ->where('video_tapes.is_banner' , 1 )
                    ->select('video_tapes.id as video_id' ,'video_tapes.title' , 
                             'video_tapes.description' , 'video_tapes.ratings' , 
                             'video_tapes.reviews' , 'video_tapes.created_at as video_date' ,
                             'video_tapes.default_image',
                             'video_tapes.banner_image',

                             'video_tapes.category_id as category_id',
                             'video_tapes.sub_category_id',
                             'video_tapes.genre_id',
                             'video_tapes.is_home_slider',

                             'video_tapes.status','video_tapes.uploaded_by',
                             'video_tapes.edited_by','video_tapes.is_approved',

                             'categories.name as category_name' , 'sub_categories.name as sub_category_name' ,
                             'genres.name as genre_name')
                    ->orderBy('video_tapes.created_at' , 'desc')
                    ->get();

        return view('admin.banner_videos.banner-videos')->with('videos' , $videos)
                    ->withPage('banner-videos')
                    ->with('sub_page','view-banner-videos');
   
    }

    public function add_banner_video(Request $request) {

        $categories = Category::where('categories.is_approved' , 1)
                        ->select('categories.id as id' , 'categories.name' , 'categories.picture' ,
                            'categories.is_series' ,'categories.status' , 'categories.is_approved')
                        ->leftJoin('sub_categories' , 'categories.id' , '=' , 'sub_categories.category_id')
                        ->groupBy('sub_categories.category_id')
                        ->havingRaw("COUNT(sub_categories.id) > 0")
                        ->orderBy('categories.name' , 'asc')
                        ->get();

        return view('admin.banner_videos.banner-video-upload')
                ->with('categories' , $categories)
                ->with('page' ,'banner-videos')
                ->with('sub_page' ,'add-banner-video');

    }

    public function change_banner_video($id) {

        $video = VideoTape::find($id);

        $video->is_banner = 0 ;

        $video->save();

        $message = tr('change_banner_video_success');
       
        return back()->with('flash_success', $message);
    }

    public function user_ratings() {
            
            $user_reviews = UserRating::leftJoin('users', 'user_ratings.user_id', '=', 'users.id')
                ->select('user_ratings.id as rating_id', 'user_ratings.rating', 
                         'user_ratings.comment', 
                         'users.first_name as user_first_name', 
                         'users.last_name as user_last_name', 
                         'users.id as user_id', 'user_ratings.created_at')
                ->orderBy('user_ratings.id', 'ASC')
                ->get();
            return view('admin.reviews')->with('name', 'User')->with('reviews', $user_reviews);
    }

    public function delete_user_ratings(Request $request) {

        if($user = UserRating::find($request->id)) {
            $user->delete();
        }

        return back()->with('flash_success', tr('admin_not_ur_del'));
    }

    public function user_payments() {

        $payments = UserPayment::orderBy('created_at' , 'desc')->get();

        return view('admin.payments.user-payments')->with('data' , $payments)->with('page','payments')->with('sub_page','user-payments'); 
    }

    public function email_settings() {

        $admin_id = \Auth::guard('admin')->user()->id;

        $result = EnvEditorHelper::getEnvValues();

        \Auth::guard('admin')->loginUsingId($admin_id);

        return view('admin.email-settings')->with('result',$result)->withPage('email-settings')->with('sub_page',''); 
    }


    public function email_settings_process(Request $request) {

        $email_settings = ['MAIL_DRIVER' , 'MAIL_HOST' , 'MAIL_PORT' , 'MAIL_USERNAME' , 'MAIL_PASSWORD' , 'MAIL_ENCRYPTION'];

        $admin_id = \Auth::guard('admin')->user()->id;

        foreach ($email_settings as $key => $data) {

            if($request->has($data)) {
                \Enveditor::set($data,$request->$data);
            }
        }

        \Artisan::call('config:cache');

        Auth::guard('admin')->loginUsingId($admin_id);

        $result = EnvEditorHelper::getEnvValues();

        return back()->with('result' , $result)->with('flash_success' , tr('email_settings_success'));

    }

    public function settings() {

        $settings = array();

        $result = EnvEditorHelper::getEnvValues();

        return view('admin.settings.settings')->with('settings' , $settings)->with('result', $result)->withPage('settings')->with('sub_page',''); 
    }

    public function payment_settings() {

        $settings = array();

        return view('admin.payment-settings')->with('settings' , $settings)->withPage('payment-settings')->with('sub_page',''); 
    }

    public function settings_process(Request $request) {

        $settings = Settings::all();

        $check_streaming_url = "";

        if($settings) {

            foreach ($settings as $setting) {

                $key = $setting->key;
               
                if($setting->key == 'site_icon') {

                    if($request->hasFile('site_icon')) {
                        
                        if($setting->value) {
                            Helper::delete_picture($setting->value, "/uploads/images/");
                        }

                        $setting->value = Helper::normal_upload_picture($request->file('site_icon'), "/uploads/images/");
                    
                    }
                    
                } else if($setting->key == 'site_logo') {

                    if($request->hasFile('site_logo')) {

                        if($setting->value) {

                            Helper::delete_picture($setting->value, "/uploads/images/");
                        }

                        $setting->value = Helper::normal_upload_picture($request->file('site_logo'),"/uploads/images/");
                    }

                } else if($setting->key == 'streaming_url') {

                    if($request->has('streaming_url') && $request->streaming_url != $setting->value) {

                        if(check_nginx_configure()) {
                            $setting->value = $request->streaming_url;
                        } else {
                            $check_streaming_url = " !! ====> Please Configure the Nginx Streaming Server.";
                        }
                    }  

                } else if($request->$key!='') {

                    $setting->value = $request->$key;

                }

                $setting->save();
            
            }

        }
        
        
        $message = "Settings Updated Successfully"." ".$check_streaming_url;
        
        return back()->with('setting', $settings)->with('flash_success', $message);    
    
    }

    public function help() {
        return view('admin.static.help')->withPage('help')->with('sub_page' , "");
    }

    public function viewPages() {

        $all_pages = Page::all();

        return view('admin.pages.viewpages')->with('page','pages_id')->with('sub_page',"view_pages")->with('data',$all_pages);
    }

    public function add_page() {

        $pages = Page::all();
        return view('admin.pages.add-page')->with('page','pages_id')->with('sub_page',"add_page")->with('view_pages',$pages);
    }

    public function editPage($id) {
        $page = Page::find($id);

        if($page) {
            return view('admin.pages.editPage')->withPage('viewpage')->with('sub_page',"view_pages")->with('pages',$page);
        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }
    }

    public function pagesProcess(Request $request) {

        $type = $request->type;
        $id = $request->id;
        $heading = $request->heading;
        $description = $request->description;

        $validator = Validator::make($request->all(),
            array('heading' => 'required',
                'description' => 'required'));

        if($validator->fails()) {
            $error = $validator->messages()->all();
            return back()->with('flash_errors',$error);
        } else {

            if($request->has('id')) {

                $pages = Page::find($id);
                $pages->heading = $heading;
                $pages->description = $description;
                $pages->save();

            } else {

                $check_page = Page::where('type',$type)->first();
                
                if(!$check_page) {
                    $pages = new Page;
                    $pages->type = $type;
                    $pages->heading = $heading;
                    $pages->description = $description;
                    $pages->save();
                } else {
                    return back()->with('flash_error',tr('page_already_alert'));
                }
            }
            if($pages) {
                return back()->with('flash_success',tr('page_create_success'));
            } else {
                return back()->with('flash_error',tr('admin_not_error'));
            }
        }
    }

    public function deletePage($id) {

        $page = Page::where('id',$id)->delete();

        if($page) {
            return back()->with('flash_success',tr('page_delete_success'));
        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }
    }

    public function custom_push() {

        return view('admin.static.push')->with('title' , "Custom Push")->with('page' , "custom-push");

    }

    public function custom_push_process(Request $request) {

        $validator = Validator::make(
            $request->all(),
            array( 'message' => 'required')
        );

        if($validator->fails()) {

            $error = $validator->messages()->all();

            return back()->with('flash_errors',$error);

        } else {

            $message = $request->message;
            $title = Setting::get('site_name');
            $message = $message;
            
            \Log::info($message);

            $id = 'all';

            Helper::send_notification($id,$title,$message);

            return back()->with('flash_success' , tr('push_send_success'));
        }
    }

    /**
     * Function Name : spam_videos()
     * Load all the videos from flag table
     *
     * @return all the spam videos
     */
    public function spam_videos() {
        // Load all the videos from flag table
        $model = Flag::groupBy('video_tape_id')->get();
        // Return array of values
        return view('admin.spam_videos.spam_videos')->with('model' , $model)
                        ->with('page' , 'Spam Videos')
                        ->with('subPage' , '');
    }

    /**
     * Function Name : view_users()
     * Load all the flags based on the video id
     *
     * @param integer $id Video id
     *
     * @return all the spam videos
     */
    public function view_users($id) {
        // Load all the users
        $model = Flag::where('video_id', $id)->get();
        // Return array of values
        return view('admin.spam_videos.user_report')->with('model' , $model)
                        ->with('page' , 'Spam Videos')
                        ->with('subPage' , 'User Reports');   
    }

    /**
     * Function Name : video_payments()
     * To get payments based on the video subscription
     *
     * @return array of payments
     */
    public function video_payments() {
        $payments = [];

        return view('admin.payments.video-payments')->with('data' , $payments)->withPage('payments')->with('sub_page','video-subscription'); 
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
     * Function Name : save_common_settings
     * Save the values in env file
     *
     * @param object $request Post Attribute values
     * 
     * @return settings values
     */
    
    public function save_common_settings(Request $request) {

        $admin_id = \Auth::guard('admin')->user()->id;

        foreach ($request->all() as $key => $data) {

            if($request->has($key)) {
                \Enveditor::set($key,$data);
            }
        }

        \Artisan::call('config:clear');

        \Artisan::call('config:cache');

        \Auth::guard('admin')->loginUsingId($admin_id);

        $result = EnvEditorHelper::getEnvValues();

        return back()->with('result' , $result)->with('flash_success' , tr('common_settings_success'));
    }




    public function channels() {

        $channels = Channel::orderBy('channels.created_at', 'desc')
                        ->distinct('channels.id')
                        ->get();

        return view('admin.channels.channels')->with('channels' , $channels)->withPage('channels')->with('sub_page','view-channels');
    }

    public function add_channel() {

        $users = User::where('is_verified', DEFAULT_TRUE)->where('status', DEFAULT_TRUE)->get();

        return view('admin.channels.add-channel')->with('users', $users)->with('page' ,'channels')->with('sub_page' ,'add-channel');
    }

    public function edit_channel($id) {

        $channel = Channel::find($id);

        $users = User::where('is_verified', DEFAULT_TRUE)->where('status', DEFAULT_TRUE)->get();

        return view('admin.channels.edit-channel')->with('channel' , $channel)->with('page' ,'channels')->with('sub_page' ,'edit-channel')->with('users', $users);
    }

    public function add_channel_process(Request $request) {

        $response = CommonRepo::channel_save($request)->getData();

        if($response->success) {
            // $response->message = Helper::get_message(118);

            return back()->with('flash_success', $response->message);
        } else {
            
            return back()->with('flash_error', $response->error);
        }
        
    }

    public function approve_channel(Request $request) {

        $channel = Channel::find($request->id);

        $channel->is_approved = $request->status;

        $channel->save();

        if ($request->status == 0) {
           
            foreach($channel->videoTape as $video)
            {                
                $video->is_approved = $request->status;

                $video->save();
            } 

        }

        $message = tr('admin_not_channel_decline');

        if($channel->is_approved == DEFAULT_TRUE){

            $message = tr('admin_not_channel_approve');
        }

        return back()->with('flash_success', $message);
    
    }

    public function delete_channel(Request $request) {
        
        $channel = Channel::where('id' , $request->channel_id)->first();

        if($channel) {       

            $channel->delete();

            return back()->with('flash_success',tr('admin_not_channel_del'));

        } else {

            return back()->with('flash_error',tr('admin_not_error'));

        }
    }


     public function subscriptions() {

        $data = Subscription::orderBy('created_at','desc')->get();

        return view('admin.subscriptions.index')->withPage('hotels')
                        ->with('data' , $data)
                        ->with('sub_page','view-subscription');        

    }

    public function subscription_create() {

        return view('admin.subscriptions.create')->with('page' , 'subscriptions')
                    ->with('sub_page','subscriptions-add');
    }

    public function subscription_edit($unique_id) {

        $data = Subscription::where('unique_id' ,$unique_id)->first();

        return view('admin.subscriptions.edit')->withData($data)
                    ->with('sub_page','subscriptions-view')
                    ->with('page' , 'subscriptions ');

    }

    public function subscription_save(Request $request) {

        $validator = Validator::make($request->all(),[
                'title' => 'required|max:255',
                'plan' => 'required',
                'amount' => 'required',
                'picture' => 'mimes:jpeg,png,jpg'
        ]);
        
        if($validator->fails()) {

            $error_messages = implode(',', $validator->messages()->all());

            return back()->with('flash_errors', $error_messages);

        } else {

            if($request->id != '') {

                $model = Subscription::find($request->id);


                if($request->hasFile('image')) {

                    $model->picture ? Helper::delete_picture('subscriptions' , $model->picture) : "";

                    $picture = Helper::upload_avatar('subscriptions' , $request->file('image'));
                    
                    $request->request->add(['picture' => $picture , 'image' => '']);

                }

                $model->update($request->all());

            } else {

                if($request->hasFile('picture')) {

                    $picture = Helper::upload_avatar('subscriptions' , $request->file('image'));

                    $request->request->add(['picture' => $picture , 'image'=> '']);
                }

                $model = Subscription::create($request->all());

                $model->status = 1;

                $model->unique_id = $request->title;

                $model->save();
            }
        
            if($model) {
                return redirect(route('admin.subscriptions.view', $model->unique_id))->with('flash_success', $request->id ? tr('subscription_update_success') : tr('subscription_create_success'));

            } else {
                return back()->with('flash_error',tr('admin_not_error'));
            }
        }
    
        
    }

    /** 
     * 
     * Subscription View
     *
     */

    public function subscription_view($unique_id) {

        if($data = Subscription::where('unique_id' , $unique_id)->first()) {

            return view('admin.subscriptions.view')
                        ->with('data' , $data)
                        ->withPage('subscriptions')
                        ->with('sub_page','subscriptions-view');

        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }
   
    }


    public function subscription_delete($id) {

        if($data = Subscription::where('id',$id)->first()->delete()) {

            return back()->with('flash_success',tr('subscription_delete_success'));

        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }
        
    }

    /** 
     * Subscription status change
     * 
     *
     */

    public function subscription_status($unique_id) {

        if($data = Subscription::where('unique_id' , $unique_id)->first()) {

            $data->status  = $data->status ? 0 : 1;

            $data->save();

            return back()->with('flash_success' , $data->status ? tr('subscription_approve_success') : tr('subscription_decline_success'));

        } else {

            return back()->with('flash_error',tr('admin_not_error'));
            
        }
    }

    public function user_subscription_payments($id = "") {

        $page = "user-payments";

        $base_query = UserPayment::orderBy('created_at' , 'desc');

        $subscription = [];

        if($id) {

            $subscription = Subscription::find($id);

            $base_query = $base_query->where('subscription_id' , $id);

            $page = "user-payments";
        }

        $payments = $base_query->get();

        return view('admin.users.subscription_payments')->with('data' , $payments)->withPage($page)->with('sub_page','')->with('subscription' , $subscription); 
    
    }


    public function add_between_ads(Request $request) {

        $index = $request->index + 1;
        
        return view('admin.ads._sub_form')->with('index' , $index);
    }


    public function ads_create(Request $request) {

        $vModel = VideoTape::find($request->video_tape_id);

        $videoPath = '';

        $video_pixels = '';

        if ($vModel) {

            $videoPath = $vModel->video_resize_path ? $vModel->video.','.$vModel->video_resize_path : $vModel->video;
            $video_pixels = $vModel->video_resolutions ? 'original,'.$vModel->video_resolutions : 'original';

        }

        $model = new VideoAd();

        $adDetail = new AdsDetail();

        $index = 0;

        return view('admin.ads.create')->with('vModel', $vModel)->with('videoPath', $videoPath)->with('video_pixels', $video_pixels)->with('page', 'video_ads')->with('sub_page', 'create_ad')->with('model', $model)->with('adDetail', $adDetail)->with('index', $index);
    }
}
