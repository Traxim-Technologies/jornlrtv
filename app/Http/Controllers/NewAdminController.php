<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Helpers\Helper;

use App\Helpers\EnvEditorHelper;

use App\Jobs\sendPushNotification;

use App\Jobs\NormalPushNotification;

use App\Jobs\CompressVideo;

use App\Repositories\CommonRepository as CommonRepo;

use App\Repositories\AdminRepository as AdminRepo;

use App\Repositories\VideoTapeRepository as VideoRepo;

use Auth;

use DB;

use Exception;

use Log;

use Setting;

use Validator;

use App\User;

use App\UserPayment;

use App\UserHistory;

use App\UserRating;

use App\Wishlist;

use App\Channel;

use App\ChannelSubscription; 

use App\Category;

use App\Tag;

use App\Admin;

use App\AdminVideoImage;

use App\VideoTape;

use App\VideoAd;

use App\VideoTapeTag;

use App\CustomLiveVideo;

use App\Moderator;

use App\Redeem;

use App\Coupon;

use App\Subscription;

use App\Flag;

use App\Page;

use App\Settings;

use App\BannerAd;

use App\AssignVideoAd;

use App\VideoTapeImage;

use App\AdsDetail;

use App\RedeemRequest;

use App\PayPerView;

class NewAdminController extends Controller {
   
    public function dashboard() {

        $admin = Admin::first();

        $admin->token = Helper::generate_token();

        $admin->token_expiry = Helper::generate_token_expiry();

        $admin->save();
        
        $user_count = User::count();

        $channel_count = Channel::count();

        $video_count = VideoTape::count();
 
        $recent_videos = VideoRepo::admin_recently_added();

        $get_registers = get_register_count();

        $recent_users = get_recent_users();

        $total_revenue = total_revenue();

        $view = last_days(10);

        return view('new_admin.dashboard.dashboard')
        			->withPage('dashboard')
                    ->with('sub_page','')
                    ->with('user_count' , $user_count)
                    ->with('video_count' , $video_count)
                    ->with('channel_count' , $channel_count)
                    ->with('get_registers' , $get_registers)
                    ->with('view' , $view)
                    ->with('total_revenue' , $total_revenue)
                    ->with('recent_users' , $recent_users)
                    ->with('recent_videos' , $recent_videos);
    }

    /**
     * Function Name : users_index()
     *
     * @uses To list out users object details
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param
     *
     * @return View page
     */
    public function users_index() {

        $users = User::orderBy('created_at','desc')
                    ->withCount('getChannel')
                    ->withCount('getChannelVideos')
                    ->get();

        return view('new_admin.users.index')
                    ->withPage('users')
                    ->with('sub_page','users-view')
                    ->with('users' , $users);
    }

    /**
     * Function Name : users_create()
     *
     * @uses To create a user object details
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param 
     *
     * @return View page
     */
    public function users_create(Request $request) {

        $user_details = new User;

        return view('new_admin.users.create')
                    ->with('page' , 'users')
                    ->with('sub_page','add-user')
                    ->with('user_details', $user_details);
    }

    /**
     * Function Name : users_edit
     *
     * @uses To edit a user based on their id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - user_id
     * 
     * @return response of new User object
     *`
     */
    public function users_edit(Request $request) {
        
        try {
          
            $user_details = User::find($request->user_id);

            if( count($user_details) == 0 ) {

                throw new Exception( tr('admin_user_not_found'), 101);

            } else {

                $user_details->dob = ($user_details->dob) ? date('d-m-Y', strtotime($user_details->dob)) : '';

                return view('new_admin.users.edit')
                        ->with('page' , 'users')
                        ->with('sub_page','users-view')
                        ->with('user_details',$user_details);
            }

        } catch( Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->route('admin.users.index')->with('flash_error',$error);
        }
    
    }

    /**
     * Function Name : users_save
     *
     * @uses To save/update user object based on user id or details
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - user_id, (request) details
     * 
     * @return success/failure message.
     *
     */
    public function users_save(Request $request) {

        try {
 
            $validator = Validator::make( $request->all(), [
                    'user_id' => 'exists:users,id',
                    'name' => 'required|max:255',
                    'email' => $request->user_id ? 'required|email|max:255|unique:users,email,'.$request->user_id.',id' : 'required|email|max:255|unique:users,email,NULL,id',
                    'mobile' => 'digits_between:6,13',
                    'password' => $request->user_id ? '' :'required|min:6|confirmed',
                    'dob' => 'required',
                    'description' => 'max:255',
                    'picture' => 'mimes:jpg,png,jpeg',
                ]
            );
            
            if ($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);

            } else {

                DB::beginTransaction(); 

                $user_details = $request->user_id ? User::find($request->user_id) : new User;

                $new_user = NEW_USER;

                if ($user_details->id) {

                    $new_user = EXISTING_USER;

                    $message = tr('admin_user_update_success');

                } else {

                    $user_details->password = ($request->password) ? \Hash::make($request->password) : null;

                    $message = tr('admin_user_create_success');

                    $user_details->login_by = 'manual';

                    $user_details->device_type = 'web';

                    $user_details->picture = asset('placeholder.png');

                    $user_details->timezone = $request->has('timezone') ? $request->timezone : '';
                }
                
                $user_details->name = $request->has('name') ? $request->name : '';

                $user_details->email = $request->has('email') ? $request->email: '';

                $user_details->mobile = $request->has('mobile') ? $request->mobile : '';

                $user_details->description = $request->has('description') ? $request->description : '';
                
                $user_details->token = Helper::generate_token();

                $user_details->token_expiry = Helper::generate_token_expiry();

                $user_details->dob = $request->dob ? date('Y-m-d', strtotime($request->dob)) : $user_details->dob;

                if ($user_details->dob) {

                    $from = new \DateTime($user_details->dob);

                    $to   = new \DateTime('today');

                    $user_details->age_limit = $from->diff($to)->y;
                }

                if ($user_details->age_limit < 10) {

                    throw new Exception(tr('admin_user_min_age_error'), 101);
                }

                if ($new_user) {

                    $email_data['name'] = $user_details->name;

                    $email_data['password'] = $request->password;

                    $email_data['email'] = $user_details->email;

                    $subject = tr('user_welcome_title' , Setting::get('site_name'));

                    $page = "emails.admin_user_welcome";

                    $email = $user_details->email;

                    $user_details->is_verified = USER_EMAIL_VERIFIED;

                    Helper::send_email($page,$subject,$email,$email_data);

                    register_mobile('web');
                }

                // Upload picture
                if ($request->hasFile('picture') != "") {

                    if ($request->user_id) {

                        Helper::delete_picture($user_details->picture, "/uploads/images/users"); // Delete the old pic
                    }

                    $user_details->picture = Helper::normal_upload_picture($request->file('picture'), "/uploads/images/users");
                }

                if ($user_details->save()) {

                    DB::commit();

                    // Check the default subscription and save the user type

                    if ($request->user_id == '') {
                        
                        user_type_check($user_details->id);
                    }
                    
                    if ($user_details) {
                        
                        return redirect()->route('admin.users.view', ['user_id' => $user_details->id] )->with('flash_success', $message);

                    } else {

                        throw new Exception( tr('admin_user_save_error'), 101);
                    }

                } else {

                    throw new Exception(tr('admin_user_save_error'), 101); 
                }
            }

        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->withInput()->with('flash_error',$error);
        }    
    }

    /**
     * Function Name : users_view
     *
     * @uses To view user details based on user id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - user_id
     * 
     * @return success/failure message.
     *
     */
    public function users_view(Request $request) {

        try {
               
            $user_details = User::find($request->user_id) ;
            
            if( count($user_details) == 0 ) {

                throw new Exception(tr('admin_user_not_found'), 101);
            } 

            $user_details = User::where('id', $request->user_id)
                                    ->withCount('getChannel')
                                    ->withCount('getChannelVideos')
                                    ->withCount('userWishlist')
                                    ->withCount('userHistory')
                                    ->withCount('userRating')
                                    ->withCount('userFlag')
                                    ->first();

            if ($user_details) {

                $channels = Channel::where('user_id', $request->user_id)
                            ->orderBy('created_at', 'desc')
                            ->withCount('getVideoTape')
                            ->withCount('getChannelSubscribers')
                            ->paginate(12);

                $channel_datas = [];

                foreach ($channels as $key => $value) {

                    $earnings = 0;

                    if ($value->getVideoTape) {

                        foreach ($value->getVideoTape as $key => $video) {

                            $earnings += $video->user_ppv_amount;
                        }
                    }
                    
                    $channel_datas[] = [

                        'channel_id' => $value->id,

                        'channel_name' => $value->name,

                        'picture' => $value->picture,

                        'cover' => $value->cover,

                        'subscribers' => $value->get_channel_subscribers_count,

                        'videos' => $value->get_video_tape_count,

                        'earnings' => $earnings,

                        'currency' => Setting::get('currency')
                    ];
                }

                // Without below condition the output of $channel_datas will be array f index value
                $channel_datas = json_encode($channel_datas);

                $channel_datas = json_decode($channel_datas);

                $videos = $user_details->getChannelVideos;

                $wishlists = Wishlist::select('wishlists.*', 'video_tapes.title as title')
                        ->where('wishlists.user_id', $request->user_id)
                        ->leftJoin('video_tapes', 'video_tapes.id', '=', 'wishlists.video_tape_id')
                        ->orderBy('wishlists.created_at', 'desc')
                        ->paginate(12);

                $history = UserHistory::select('user_histories.*', 'video_tapes.title as title')
                        ->where('user_histories.user_id', $request->user_id)
                        ->leftJoin('video_tapes', 'video_tapes.id', '=', 'user_histories.video_tape_id')
                        ->orderBy('user_histories.created_at', 'desc')
                        ->paginate(12);

                $spam_reports = Flag::select('flags.*', 'video_tapes.title as title')
                        ->where('flags.user_id', $request->user_id)
                        ->leftJoin('video_tapes', 'video_tapes.id', '=', 'flags.video_tape_id')
                        ->orderBy('flags.created_at', 'desc')
                        ->paginate(12);

                $user_ratings = UserRating::select('user_ratings.*', 'video_tapes.title as title')
                        ->where('user_ratings.user_id', $request->user_id)
                        ->leftJoin('video_tapes', 'video_tapes.id', '=', 'user_ratings.video_tape_id')
                        ->orderBy('user_ratings.created_at', 'desc')
                        ->paginate(12);

                return view('new_admin.users.view')
                            ->withPage('users')
                            ->with('user_details' , $user_details)
                            ->with('sub_page','users')
                            ->with('channels', $channel_datas)
                            ->with('videos', $videos)
                            ->with('wishlists', $wishlists)
                            ->with('histories', $history)
                            ->with('spam_reports', $spam_reports)
                            ->with('user_ratings', $user_ratings);
            } else {

                throw new Exception(tr('user_not_found'), 101);                
            }

        } catch( Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->route('admin.users.index')->with('flash_error',$error);
        }
    
    }


    /**
     * Function Name : users_delete
     *
     * @uses To delete user details based on user id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - user_id
     * 
     * @return success/failure message.
     *
     */
    public function users_delete(Request $request) {

        try {

            $users_details = User::find($request->user_id);

            if (count($users_details) == 0 ) {

               throw new Exception(tr('admin_user_not_found'), 101);                
            }

            DB::beginTransaction();

            Helper::delete_picture($users_details->picture, "/uploads/images/users"); 

            if ($users_details->device_type) {

                subtract_count($users_details->device_type);            
            }

            // delete the user After reduce the count from mobile register model 
            if ($users_details->delete()) {
                
                DB::commit();

                return redirect()->route('admin.users.index')->with('flash_success',tr('admin_user_delete_success'));
            }

         } catch( Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return redirect()->route('admin.users.index')->with('flash_error',$error);
        }
    
    }

    /**
     * Function Name : users_status_change
     *
     * @uses To update the user status to APPROVE/DECLINE based on user id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - user_id
     * 
     * @return success/failure message.
     *
     */
    public function users_status_change(Request $request) {

        try {

            $users_details = User::find($request->user_id);

            if ( count($users_details) == 0 ) {

               throw new Exception(tr('admin_user_not_found'), 101);                
            }

            DB::beginTransaction();

            $users_details->status =$users_details->status == APPROVED ? DECLINED : APPROVED ;

            if( $users_details->save() ) {
                
                $message = $users_details->status == APPROVED ? tr('admin_user_approve_success') : tr('admin_user_declined_success') ;

                if ($users_details->status == DECLINED) {
                    
                    Channel::where('user_id', $users_details->id)->update(['is_approved'=>ADMIN_CHANNEL_DECLINED_STATUS]);

                    VideoTape::where('user_id', $users_details->id)->update(['is_approved'=>ADMIN_VIDEO_DECLINED_STATUS]);

                }

                DB::commit();

                return back()->with('flash_success',$message );

            } else {
                
                throw new Exception(tr('admin_user_status_error'), 101);
            }
            
        } catch (Exception $e) {

            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    }

    /**
     * Function: users_verify_status()
     * 
     * @uses To verify for the user Email 
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer (request) $user_id
     *
     * @return success/error message
     */
    public function users_verify_status(Request $request) {

        try {   

            DB::beginTransaction();
       
            $user_details = User::find($request->user_id);

            if( count( $user_details) == 0) {
                
                throw new Exception(tr('admin_user_not_found'), 101);
            } 
            
            $user_details->is_verified = $user_details->is_verified == USER_EMAIL_VERIFIED ? USER_EMAIL_NOT_VERIFIED : USER_EMAIL_VERIFIED;

            $message = $user_details->is_verified == USER_EMAIL_VERIFIED ? tr('admin_user_verification_success') : tr('admin_user_unverification_success');

            if( $user_details->save() ) {

                DB::commit();

                return back()->with('flash_success',$message);

            } else {

                throw new Exception(tr('admin_user_verification_save_error'), 101);
            }
            
        } catch (Exception $e) {
            
            DB::rollback();
            
            $error = $e->getMessage();

            return redirect()->route('admin.users.index')->with('flash_error',$error);
        }
    }


    /**
     * Function Name : users_wishlist_delete
     *
     * @uses To delete the user wishlist based on wishlist id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer ($request) wishlist_id
     * 
     * @return success/failure message
     *
     */
    public function users_wishlist_delete(Request $request) {

        try {

            $user_wishlist = Wishlist::find($request->wishlist_id);

            if(count($user_wishlist) == 0) {

                throw new Exception(tr('admin_user_wishlist_not_found'), 101);
            }
            
            DB::beginTransaction();

            if ($user_wishlist->delete()) {

                DB::commit();
                
                return back()->with('flash_success',tr('admin_user_wishlist_success'));

            } else {
                
                throw new Exception(tr('admin_user_wishlist_delete_error'), 101);
            }          
        
        } catch (Exception $e) {

            DB::rollback();

            $error = $e->getMessage();

            return redirect()->route('admin.users.index')->with('flash_error',$error);
        }
    
    }    

    /**
     * Function Name : users_history_delete
     *
     * @uses To delete the user history based on history id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer ($request) history_id
     * 
     * @return success/failure message
     *
     */
    public function users_history_delete(Request $request) {

        try {

            $user_history = UserHistory::find($request->history_id);

            if(count($user_history) == 0) {

                throw new Exception(tr('admin_user_history_not_found'), 101);
            }

            DB::beginTransaction();

            if ($user_history->delete()) {

                DB::commit();
                
                return back()->with('flash_success',tr('admin_user_history_delete_success'));

            } else {
                
                throw new Exception(tr('admin_user_history_delete_error'), 101);
            }          
        
        } catch (Exception $e) {

            DB::rollback();

            $error = $e->getMessage();

            return redirect()->route('admin.users.index')->with('flash_error',$error);
        }
    
    }


    /**
     * Function Name : users_subscriptions
     *
     * To subscribe a new plans based on users
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param integer $id - User id (Optional)
     * 
     * @return - response of array of subscription details
     *
     */
    public function users_subscriptions(Request $request) {

        try {

            $subscriptions = Subscription::orderBy('created_at','desc')->get();

            $payments = []; 

            if($request->userpayment_id) {

                $payments = UserPayment::select('user_payments.*', 'subscriptions.title')
                            ->leftjoin('subscriptions', 'subscriptions.id', '=', 'user_payments.subscription_id')
                            ->orderBy('user_payments.created_at' , 'desc')
                            ->where('user_payments.user_id' , $request->userpayment_id)->get();
            }

            return view('new_admin.subscriptions.user_plans')
                        ->withPage('users')
                        ->with('sub_page','users')
                        ->with('subscriptions' , $subscriptions)
                        ->with('id', $request->userpayment_id)->with('payments', $payments); 
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }       

    }


    /**
     * Function Name : users_subscription_save
     *
     * To save subscription details based on user id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer (request) $subscription_id, $user_id
     * 
     * @return success/failure message.
     *
     */
    public function users_subscription_save(Request $request) {

        try {

            if(!$subscriptions = Subscription::find($request->subscription_id)) {

                throw new Exception(tr('admin_subscription_not_found'), 101);
            }

            if(!$users_details = User::find($request->user_id)) {

                throw new Exception(tr('admin_user_not_found'), 101);
            }
            
            DB::beginTransaction();

            $response = CommonRepo::save_subscription($request->subscription_id,$request->user_id)->getData();

            if($response->success) {
                
                DB::commit();

                return back()->with('flash_success', $response->message);

            } else {

                throw new Exception($response->message, 101);                
            }
            
        } catch (Exception $e) {

            DB::rollback();
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }

    }


    /**
     * Function Name : users_channels
     *
     * To list out all the channels based on users id
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param integer $user_id - User id
     * 
     * @return response of user channel details
     *
     */
    public function users_channels(Request $request) {

        try {

            $user_details = User::find($request->user_id);

            if(count($user_details) == 0) {

                throw new Exception(tr('admin_user_not_found'), 101);
            }

            $channels = Channel::orderBy('channels.created_at', 'desc')
                                ->where('user_id' , $request->user_id)
                                ->distinct('channels.id')
                                ->get();

            return view('new_admin.channels.index')
                        ->withPage('channels')
                        ->with('sub_page','view-channels')
                        ->with('channels' , $channels)
                        ->with('user_details' , $user_details);
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }  
    
    }

    /**
     * Function Name : channels_index()
     *
     * @uses To list out channels object details
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param
     *
     * @return View page
     */
    public function channels_index() {

        $channels = Channel::orderBy('channels.created_at', 'desc')
                        ->distinct('channels.id')
                        ->withCount('getChannelSubscribers')
                        ->withCount('getVideoTape')
                        ->get();

        return view('new_admin.channels.index')
                    ->withPage('channels')
                    ->with('sub_page','view-channels')
                    ->with('channels' , $channels);    
    }

    /**
     * Function Name : channels_create
     *
     * @uses To create a new channel
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param
     * 
     * @return view page
     */
    public function channels_create() {

        // Check the create channel option is enabled from admin settings
        if(Setting::get('create_channel_by_user') == CREATE_CHANNEL_BY_USER_ENABLED) {

            $users = User::where('is_verified', DEFAULT_TRUE)
                    ->where('status', DEFAULT_TRUE)
                    ->where('user_type', SUBSCRIBED_USER)
                    ->orderBy('created_at', 'desc')
                    ->get();

        } else {

            // Load master user
            $users = User::where('is_verified', DEFAULT_TRUE)
                        ->where('is_master_user' , 1)
                        ->where('status', DEFAULT_TRUE)
                        ->orderBy('created_at', 'desc')
                        ->get();
        }

        $channel_details = new Channel;
         
        return view('new_admin.channels.create')
                ->with('page' ,'channels')
                ->with('sub_page' ,'channels-create')
                ->with('users', $users)
                ->with('channel_details', $channel_details);
    }

    /**
     * Function Name : channels_edit
     *
     * @uses To edit the channel based on the channel id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer (request) $channel_id
     * 
     * @return view page
     */
    public function channels_edit(Request $request) {
        
        try {

            $channel_details = Channel::find($request->channel_id);

            if (count($channel_details) == 0) {

                throw new Exception(tr('admin_channel_not_found'), 101);
            }

            $users = User::where('is_verified', DEFAULT_TRUE)
                        ->where('status', DEFAULT_TRUE)
                        ->where('user_type', SUBSCRIBED_USER)
                        ->get();

            return view('new_admin.channels.edit')
                        ->with('page' ,'channels')
                        ->with('sub_page' ,'channels-edit')
                        ->with('channel_details' , $channel_details)
                        ->with('users', $users);
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
   
    }

    /**
     * Function Name : channels_save
     *
     * @uses To save the channel video object details
     *
     * @created 
     *
     * @updated 
     *
     * @param Integer (request) $channel_id
     * 
     * @return view page
     *
     */
    public function channels_save(Request $request) {

        $response = CommonRepo::channel_save($request)->getData();
       
        if($response->success) {

            return redirect()->route('admin.channels.view',['channel_id' => $response->data->id])->with('flash_success', $response->message);

        } else {
            
            return back()->with('flash_error', $response->error_messages);
        }
        
    }


    /**
     * Function Name : channels_view
     *
     * @uses To view the channel based on the channel id
     *
     * @created 
     *
     * @updated 
     *
     * @param Integer (request) $channel_id
     * 
     * @return view page
     *
     */
    public function channels_view(Request $request) {

        try {

            $channel_details = Channel::select('channels.*', 'users.name as user_name', 'users.picture as user_picture')
                        ->leftjoin('users', 'users.id', '=', 'channels.user_id')
                        ->withCount('getVideoTape')
                        ->withCount('getChannelSubscribers')
                        ->where('channels.id', $request->channel_id)
                        ->first();

            if (count($channel_details) == 0) {

                throw new Exception(tr('admin_channel_not_found'), 101);
            }

            // Load videos and subscribrs based on the channel
            $channel_earnings = getAmountBasedChannel($channel_details->id);

            $videos = VideoTape::select('video_tapes.title', 'video_tapes.default_image', 'video_tapes.id', 'video_tapes.description', 'video_tapes.created_at')
                        ->where('channel_id', $channel_details->id)
                        ->paginate(12);

            $channel_subscriptions = ChannelSubscription::select('users.name as user_name', 'users.id as user_id', 'users.picture as user_picture', 'users.description', 'users.created_at', 'users.email')->where('channel_id', $channel_details->id)
                        ->leftjoin('users', 'users.id', '=', 'channel_subscriptions.user_id')
                        ->paginate(12);

            return view('new_admin.channels.view')
                    ->with('page' ,'channels')
                    ->with('sub_page' ,'edit-channel')
                    ->with('channel_details' , $channel_details)
                    ->with('channel_earnings', $channel_earnings)
                    ->with('videos', $videos)
                    ->with('channel_subscriptions', $channel_subscriptions);          
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    
    }

    /**
     * Function Name : channels_delete
     *
     * @uses To delete the channel based on channel id
     *
     * @created 
     *
     * @updated 
     *
     * @param integer (request) $channel_id
     * 
     * @return response of channel edit
     *
     */
    public function channels_delete(Request $request) {

        try {
        
            $channel_details = Channel::find($request->channel_id);

            if (count($channel_details) == 0) {

                throw new Exception(tr('admin_channel_not_found'), 101);
            }

            DB::beginTransaction();
            
             if ($channel_details->delete()) {  

                DB::commit();
                
                return back()->with('flash_success',tr('admin_channel_delete_success'));

            } else {

                throw new Exception(tr('admin_channel_delete_success'), 101);
            }
            
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }    
    }

    /**
     * Function Name : channels_status_change
     *
     * @uses To change the channel status of approve and decline 
     *
     * @created 
     *
     * @updated 
     *
     * @param integer (request) $channel_id
     * 
     * @return success/failure message
     */
    public function channels_status_change(Request $request) {
        
        try {

            DB::beginTransaction();

            $channel_details = Channel::find($request->channel_id);

            if ( count($channel_details) == 0) {

                throw new Exception(tr('admin_channel_not_found'), 101);
            }

            $channel_details->is_approved = $channel_details->is_approved == APPROVED ? DECLINED : APPROVED;

            $message = $channel_details->is_approved == APPROVED ? tr('admin_channel_approve_success') :  tr('admin_channel_decline_success') ;

            if ($channel_details->save() ) {

                if ( $channel_details->is_approved == ADMIN_CHANNEL_DECLINED_STATUS) {

                    VideoTape::where('channel_id', $channel_details->id)
                                ->update(['is_approved' => ADMIN_CHANNEL_DECLINED_STATUS]);                
                }

                DB::commit();

                return back()->with('flash_success', $message);            

            } else {

                throw new Exception(tr('admin_channel_status_error'), 101);
            }
            
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    
    }

    /**
     * Function Name : channels_videos
     *
     * @uses To list out particular channel videos based on channel id
     *
     * @created 
     *
     * @updated 
     *
     * @param Integer (request) $channel_id
     * 
     * @return view page
     */
    public function channels_videos(Request $request) {

        try {

            $channel_details = Channel::find($request->channel_id);

            if(count($channel_details) == 0) {

                throw new Exception(tr('admin_channel_not_found'), 101);
            }

            $videos = VideoTape::leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                        ->where('channel_id' , $request->channel_id)
                        ->videoResponse()
                        ->orderBy('video_tapes.created_at' , 'desc')
                        ->get();

            return view('new_admin.videos.videos')
                        ->withPage('videos')
                        ->with('sub_page','view-videos')
                        ->with('videos' , $videos)
                        ->with('channel' , $channel_details);

        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }  
   
    }

    /**
     * Function Name : channels_subscribers
     *
     * @uses To list channel subscribers based on channel id
     *
     * @created 
     *
     * @updated 
     *
     * @param integer (request) $channel_id (optional)
     * 
     * @return view page
     *
     */
    public function channels_subscribers(Request $request) {

        try {
            
            $channel_subscriptions = ChannelSubscription::orderBy('created_at', 'desc')->get();

            if ($request->channel_id) {

                $channel_subscriptions = ChannelSubscription::where('channel_id', $request->channel_id)->orderBy('created_at', 'desc')->get();
            }            

            return view('new_admin.channels.subscribers')
                        ->withPage('channels')
                        ->with('sub_page','subscribers')
                        ->with('channel_subscriptions' , $channel_subscriptions);
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    
    }

    /**
     * Function Name : categories_index()
     *
     * @uses To list out categories object details
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param
     *
     * @return View page
     */
    public function categories_index() {

        $categories = Category::orderBy('created_at','desc')
                        ->orderBy('updated_at', 'desc')
                        ->withCount('getVideos')->get();

        return view('new_admin.categories.index')
                    ->withPage('categories')
                    ->with('sub_page','categories-view')
                    ->with('categories' , $categories);
    }

    /**
     * Function Name : categories_create()
     *
     * @uses To create a category object details
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param 
     *
     * @return View page
     */
    public function categories_create(Request $request) {

        $category_details = new Category;

        return view('new_admin.categories.create')
                    ->with('page' , 'categories')
                    ->with('sub_page','add-category')
                    ->with('category_details', $category_details);
    }

    /**
     * Function Name : categories_edit
     *
     * @uses To edit a category based on their id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - category_id
     * 
     * @return response of new category object
     *
     */
    public function categories_edit(Request $request) {
        
        try {
          
            $category_details = Category::find($request->category_id);

            if( count($category_details) == 0 ) {

                throw new Exception( tr('admin_category_not_found'), 101);

            } else {

                $category_details->dob = ($category_details->dob) ? date('d-m-Y', strtotime($category_details->dob)) : '';

                return view('new_admin.categories.edit')
                        ->with('page' , 'categories')
                        ->with('sub_page','categories-view')
                        ->with('category_details',$category_details);
            }

        } catch( Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->route('admin.categories.index')->with('flash_error',$error);
        }
    
    }

    /**
     * Function Name ; categories_view()
     *
     * category details based on id
     *
     * @created
     *
     * @updated 
     *
     * @param
     * 
     * @return 
     */
    public function categories_view(Request $request) {

        try {

            $category_details = Category::where('id', $request->category_id)
                                            ->withCount('getVideos')
                                            ->first();
            
            if (count($category_details) == 0 ) {

                throw new Exception(tr('admin_category_not_found'), 101);
            } 

            // No of videos count
            $no_of_channels = Channel::leftJoin('video_tapes', 'video_tapes.channel_id', '=', 'channels.id')
                    ->where('video_tapes.category_id', $request->category_id)
                    ->groupBy('video_tapes.channel_id')
                    ->get();

            $channels = Channel::select('channels.*', 'video_tapes.id as video_tape_id', 'video_tapes.is_approved',
                            'video_tapes.status', 'video_tapes.channel_id')
                        ->leftJoin('video_tapes', 'video_tapes.channel_id', '=', 'channels.id')
                        ->where('video_tapes.category_id', $request->category_id)
                        ->groupBy('video_tapes.channel_id')
                        ->skip(0)->take(Setting::get('admin_take_count', 12))->get();

            $channel_lists = [];

            foreach ($channels as $key => $value) {

                $channel_lists[] = [
                        'channel_id' => $value->id, 
                        'user_id' => $value->user_id,
                        'picture' =>  $value->picture, 
                        'title' => $value->name,
                        'description' => $value->description, 
                        'created_at' => $value->created_at->diffForHumans(),
                        'no_of_videos' => videos_count($value->id),
                        'subscribe_status' => $request->category_id ? check_channel_status($request->category_id, $value->id) : '',
                        'no_of_subscribers' => $value->getChannelSubscribers()->count(),
                ];

            }

            $channel_lists = json_decode(json_encode($channel_lists));

            $category_videos = VideoTape::leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                            ->leftJoin('categories' , 'video_tapes.category_id' , '=' , 'categories.id')
                            ->videoResponse()
                            ->where('category_id', $request->category_id)
                            ->orderby('video_tapes.updated_at' , 'desc')
                            ->skip(0)->take(Setting::get('admin_take_count', 12))->get();

            $category_earnings = getAmountBasedChannel($category_details->id);

            return view('new_admin.categories.view')
                        ->with('page', 'categories')
                        ->with('sub_page', 'categories')
                        ->with('category_videos', $category_videos)
                        ->with('channel_lists', $channel_lists)
                        ->with('category', $category_details)
                        ->with('category_earnings', $category_earnings)
                        ->with('no_of_channels', count($no_of_channels));           
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->route('admin.categories.index')->with('flash_error',$error);
        }

        
    }
    
    /**
     * Function Name : categories_save
     *
     * @uses To save/update category object based on category id or details
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - category_id, (request) details
     * 
     * @return success/failure message.
     *
     */
    public function categories_save(Request $request) {
        
        try {

            $validator = Validator::make($request->all() , [
                'name' => $request->id ? 'required|unique:categories,name,'.$request->category_id.',id|max:128|min:2' : 'required|unique:categories,name,NULL,id|max:128|min:2',
                'id' => 'exists:categories,id', 
                'image' => $request->category_id ? 'mimes:jpeg,jpg,bmp,png' : 'required|mimes:jpeg,jpg,bmp,png',
                    'description'=>'required',
            ]);

            if($validator->fails()) {

                $error = implode(',',$validator->messages()->all()); 

                throw new Exception($error, 101);

            } else {

                $category_details = $request->id ? Category::find($request->id) : new Category;

                $category_details->name = $request->name;

                $category_details->unique_id = seoUrl($category_details->name);

                $category_details->description = $request->description;

                $category_details->status = DEFAULT_TRUE;

                if ($request->hasFile('image')) {

                    if ($request->category_id) {

                        Helper::delete_avatar('uploads/categories' , $category_details->image);
                    }

                    $category_details->image = Helper::upload_avatar('uploads/categories', $request->file('image'), 0); 
                }

                if ($category_details->save()) {

                    DB::commit();

                    return redirect()->route('admin.categories.view', ['category_id' => $category_details->id])->with('flash_success',tr('admin_category_update_success'));
              
                } else {

                    throw new Exception(tr('admin_category_save_error'), 101);
                }
            }
            
        } catch (Exception $e) {

            DB::rollback();
            
            $error = $e->getMessage();

            return back()->withInput()->with('flash_error',$error);
        }

    }

    /**
     * Function Name : categories_videos()
     *
     * @uses To display based on category
     *
     * @created
     *
     * @updated
     *
     * @param object $request - User Details
     *
     * @return view page
     */
    public function categories_videos(Request $request) {

        try {

            $category_details = Category::find($request->category_id);

            if (count($category_details) == 0) {

                throw new Exception(tr('admin_category_not_found'), 101);
            }

            $videos = VideoTape::leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                    ->videoResponse()
                    ->where('category_id', $request->category_id)
                    ->get();

            $users = User::where('is_verified', DEFAULT_TRUE)
                            ->where('status', DEFAULT_TRUE)
                            ->where('user_type', SUBSCRIBED_USER)
                            ->get();

            return view('new_admin.categories.videos')
                        ->with('page', 'categories')
                        ->with('sub_page', 'categories-view')
                        ->with('videos', $videos)
                        ->with('category_details', $category_details);
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }

    }

    /**
     * Function Name : categories_channels
     *
     * @uses To list out channels based on category
     *
     * @created 
     *
     * @updated 
     *
     * @param
     * 
     * @return response of user channel details
     *
     */
    public function categories_channels(Request $request) {

        try {

            $category_details = Category::find($request->category_id);

            if (count($category_details) == 0) {

                throw new Exception(tr('admin_category_not_found'), 101);
            }
            
            $channels_id = Channel::leftJoin('video_tapes', 'video_tapes.channel_id', '=', 'channels.id')
                        ->where('video_tapes.category_id', $request->category_id)->get()
                        ->pluck('channel_id')
                        ->toArray();

            $channels = Channel::orderBy('channels.created_at', 'desc')
                            ->distinct('channels.id')
                            ->withCount('getChannelSubscribers')
                            ->withCount('getVideoTape')
                            ->whereIn('id', $channels_id)
                            ->get();

            return view('new_admin.categories.channels')
                        ->withPage('categories')
                        ->with('sub_page','categories-view')
                        ->with('channels' , $channels)
                        ->with('category_details', $category_details);
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    
    }


    /**
     * Function Name : categories_status()
     *
     * To change the category status approve/decline
     *
     * @param integer $request - category id 
     *
     * @return response of success/failure message
     */
    public function categories_status(Request $request) {

        try {
            
            $category_details = Category::find($request->category_id);

            if (count( $category_details) == 0 ) {

                throw new Exception(tr('admin_category_not_found'), 101);
            }

            DB::beginTransaction();

            $category_details->status = $category_details->status == APPROVED ? DECLINED: APPROVED;
            
            if ($category_details->status == DECLINED) {

                VideoTape::where('category_id', $category_details->id)->update(['is_approved'=>ADMIN_VIDEO_DECLINED_STATUS]);
            }

            if ($category_details->save()) {
                
                DB::commit();

                $message = $category_details->status == APPROVED ? tr('admin_category_approve_success') : tr('admin_category_decline_success');  
                return back()->with('flash_success',$message );

            } else {

                throw new Exception(tr('admin_category_status_error'), 101);
            }

        } catch (Exception $e) {
            
            DB::commit();

            $error = $e->getMessage();

            return redirect()->route('admin.categories.index')->with('flash_error',$error);
        }
    }

    /**
     * Function Name : tags_index()
     *
     * @uses To list out tags object details
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param Integer (request) tag_id
     *
     * @return View page
     */
    public function tags_index(Request $request) {

        $tag_details = $request->tag_id ? Tag::find($request->tag_id) : new Tag;

        if (!$tag_details) {

            $tag_details = new Tag;
        }

        $tags = Tag::orderBy('created_at', 'desc')->get();

        return view('new_admin.tags.index')
                    ->with('page', 'tags')
                    ->with('sub_page', '')
                    ->with('tag_details', $tag_details)
                    ->with('tags', $tags);
    }
    
    /**
     * Function Name : tags_save
     *
     * @uses To save/update tag object based on tag id or details
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - tag_id, (request) details
     * 
     * @return success/failure message.
     *
     */
    public function tags_save(Request $request) {
        
        try {

            $validator = Validator::make($request->all() , [
                'name' => $request->tag_id ? 'required|max:128|min:2|unique:tags,name,'.$request->tag_id.',id' : 'required|max:128|min:2|unique:tags,name,NULL,id',
                'tag_id' => 'exists:tags,id',
            ]);

            if ($validator->fails()) {
                
                $error= implode(',',$validator->messages()->all()); 

                throw new Exception($error, 101);
                
            } else {

                $tag_details = $request->id ? Tag::find($request->id) : new Tag;

                $tag_details->name = $request->name;

                $tag_details->status = DEFAULT_TRUE;

                $tag_details->search_count = 0;

                if ($tag_details->save()) {

                    DB::commit();

                    $message =  $request->tag_id ? tr('admin_tag_update_success') : tr('admin_tag_create_success'); 

                    return redirect(route('admin.tags'))->with('flash_success',$message);

                } else {

                    throw new Exception(tr('admin_tag_save_error'), 101);
                }

            }
            
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->withInput()->with('flash_error',$error);       
        }

    }

    /**
     * Function Name : tags_delete
     *
     * @uses To delete tag details based on tag id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - tag_id
     * 
     * @return success/failure message.
     *
     */
    public function tags_delete(Request $request) {

        try {
        
            $tag_details = Tag::find($request->tag_id);

            if (count($tag_details) == 0) {

                throw new Exception(tr('admin_tag_not_found'), 101);
            }
            
            DB::beginTransaction();

            if ($tag_details->delete()) {  

                DB::commit();
                
                return back()->with('flash_success',tr('admin_tag_delete_success'));

            } else {

                throw new Exception(tr('admin_tag_delete_success'), 101);
            }
            
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }

    }

        /**
     * Function Name : tags_status_change
     *
     * @uses To change the tag status of approve and decline 
     *
     * @created 
     *
     * @updated 
     *
     * @param integer (request) $tag_id
     * 
     * @return success/failure message
     */
    public function tags_status_change(Request $request) {
        
        try {

            DB::beginTransaction();

            $tag_details = Tag::find($request->tag_id);

            if ( count($tag_details) == 0) {

                throw new Exception(tr('admin_tag_not_found'), 101);
            }

            $tag_details->status = $tag_details->status == APPROVED ? DECLINED : APPROVED;

            $message = $tag_details->status == APPROVED ? tr('admin_tag_approved_success') :  tr('admin_tag_declined_success') ;

            if ($tag_details->save() ) {

                DB::commit();

                if ($tag_details->status == DECLINED) {

                    VideoTapeTag::where('tag_id', $tag_details->id)->update(['status' =>DECLINED]);
                } else {

                    VideoTapeTag::where('tag_id', $tag_details->id)->update(['status' =>APPROVED]);
                }
            
                return back()->with('flash_success', $message);            

            } else {

                throw new Exception(tr('admin_tag_status_error'), 101);
            }
            
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    
    }


    /**
     * Function Name : tags_videos
     *
     * @uses List of videos displayed based on tags
     *
     * @created 
     *
     * @updated 
     *
     * @param
     * 
     * @return response of videos details
     *
     */
    public function tags_videos(Request $request) {

        try {
            
            $tag_details = Tag::find($request->tag_id);

            if (count($tag_details) == 0) {

                throw new Exception(tr('admin_tag_not_found'), 101);
            }

            $videos = VideoTape::leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                        ->videoResponse()
                        ->leftjoin('video_tape_tags', 'video_tape_tags.video_tape_id', '=', 'video_tapes.id')
                        ->where('video_tape_tags.tag_id', $request->tag_id)
                        ->orderBy('video_tapes.created_at' , 'desc')
                        ->groupBy('video_tape_tags.video_tape_id')
                        ->get();


            return view('new_admin.tags.videos')
                        ->withPage('tags')
                        ->with('sub_page','tags')
                        ->with('videos' , $videos)
                        ->with('tag_details', $tag_details);

        } catch (Exception $e) {

            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
   
    }

    /**
     * Function Name : coupons_index()
     *
     * @uses To list out coupons object details
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param
     *
     * @return View page
     */
    public function coupons_index() {

        $coupons = Coupon::orderBy('created_at','desc')->get();

        return view('new_admin.coupons.index')
                    ->withPage('coupons')
                    ->with('sub_page','coupons-view')
                    ->with('coupons' , $coupons);
    }

    /**
     * Function Name : coupons_create()
     *
     * @uses To create a coupon object details
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param 
     *
     * @return View page
     */
    public function coupons_create(Request $request) {

        $coupon_details = new Coupon;

        return view('new_admin.coupons.create')
                    ->with('page' , 'coupons')
                    ->with('sub_page','coupons-create')
                    ->with('coupon_details', $coupon_details);
    }

    /**
     * Function Name : coupons_edit
     *
     * @uses To edit a coupon based on their id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - coupon_id
     * 
     * @return response of new coupon object
     *`
     */
    public function coupons_edit(Request $request) {
        
        try {
          
            $coupon_details = Coupon::find($request->coupon_id);

            if( count($coupon_details) == 0 ) {

                throw new Exception( tr('admin_coupon_not_found'), 101);

            } else {

                $coupon_details->dob = ($coupon_details->dob) ? date('d-m-Y', strtotime($coupon_details->dob)) : '';

                return view('new_admin.coupons.edit')
                        ->with('page' , 'coupons')
                        ->with('sub_page','coupons-view')
                        ->with('coupon_details',$coupon_details);
            }

        } catch( Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->route('admin.coupons.index')->with('flash_error',$error);
        }
    
    }

    /**
     * Function Name: coupons_save()
     *
     * @uses save/Update the coupon details
     *
     * @created 
     *
     * @edited 
     *
     * @param Integer (request) coupon_id, (request) details
     *
     * @return success/failure message
     */
    public function coupons_save(Request $request) {

        try {
        
            $validator = Validator::make($request->all(),[
                'coupon_id'=>'exists:coupons,id',
                'title'=>'required',
                'coupon_code'=>$request->coupon_id ? 'required|max:10|min:1|unique:coupons,coupon_code,'.$request->coupon_id : 'required|unique:coupons,coupon_code|min:1|max:10',
                'amount'=>'required|numeric|min:1|max:5000',
                'amount_type'=>'required',
                'expiry_date'=>'required|date_format:d-m-Y|after:today',
                'no_of_users_limit'=>'required|numeric|min:1|max:1000',
                'per_users_limit'=>'required|numeric|min:1|max:100',
            ]);

            if($validator->fails()){

                $error = implode(',',$validator->messages()->all());

                throw new Exception( $error, 101);
            }

            DB::beginTransaction();
            
            if($request->coupon_id !=''){
                                       
                $coupon_detail = Coupon::find($request->coupon_id); 

                $message=tr('admin_coupon_update_success');

            } else {

                $coupon_detail = new Coupon;

                $coupon_detail->status = DEFAULT_TRUE;

                $message = tr('admin_coupon_create_success');
            }

            // Check the condition amount type equal zero mean percentage
            if($request->amount_type == PERCENTAGE) {

                // Amount type zero must should be amount less than or equal 100 only
                if($request->amount <= 100){

                    $coupon_detail->amount_type = $request->has('amount_type') ? $request->amount_type :0;
     
                    $coupon_detail->amount = $request->has('amount') ?  $request->amount : '';

                } else {

                    throw new Exception(tr('admin_coupon_amount_lessthan_100'), 101);
                }

            } else {

                // This else condition is absoulte amount 

                // Amount type one must should be amount less than or equal 5000 only
                if($request->amount <= 5000){

                    $coupon_detail->amount_type=$request->has('amount_type') ? $request->amount_type : 1;

                    $coupon_detail->amount=$request->has('amount') ?  $request->amount : '';

                } else {

                    throw new Exception(tr('admin_coupon_amount_lessthan_5000'), 101);
                }
            }

            $coupon_detail->title=ucfirst($request->title);

            // Remove the string space and special characters
            $coupon_code_format  = preg_replace("/[^A-Za-z0-9\-]+/", "", $request->coupon_code);

            // Replace the string uppercase format
            $coupon_detail->coupon_code = strtoupper($coupon_code_format);

            // Convert date format year,month,date purpose of database storing
            $coupon_detail->expiry_date = date('Y-m-d',strtotime($request->expiry_date));
          
            $coupon_detail->description = $request->has('description')? $request->description : '' ;

            // Based no users limit need to apply coupons
            
            $coupon_detail->no_of_users_limit = $request->no_of_users_limit;

            $coupon_detail->per_users_limit = $request->per_users_limit;

            if($coupon_detail->save()) {

                DB::commit();

                return back()->with('flash_success',$message);

            } else {

                throw new Exception(tr('admin_coupon_save_error'), 101);            
            }
            
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return redirect()->route('admin.coupons.index')->with('flash_error',$error);
        }
        
    }

    /**
     * Function Name : coupons_view
     *
     * @uses To view the coupon based on the coupon id
     *
     * @created 
     *
     * @updated 
     *
     * @param Integer (request) $coupon_id
     * 
     * @return view page
     *
     */
    public function coupons_view(Request $request) {

        try {

            $coupon_details = Coupon::find($request->coupon_id);

            if (count($coupon_details) == 0) {

                throw new Exception(tr('admin_coupon_not_found'), 101);
            }

            return view('new_admin.coupons.view')
                    ->with('page','coupons')
                    ->with('sub_page','coupons-view')
                    ->with('coupon_details',$coupon_details);        
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    
    }

    /**
     * Function Name : coupons_delete
     *
     * @uses To delete coupons details based on coupons id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - coupons_id
     * 
     * @return success/failure message.
     *
     */
    public function coupons_delet(eRequest $request) {

        try {
        
            $coupon_details = Coupon::find($request->coupon_id);

            if (count($coupon_details) == 0) {

                throw new Exception(tr('admin_coupon_not_found'), 101);
            }
            
            DB::beginTransaction();

            if ($coupon_details->delete()) {  

                DB::commit();
                
                return back()->with('flash_success',tr('admin_coupon_delete_success'));

            } else {

                throw new Exception(tr('admin_coupon_delete_success'), 101);
            }
            
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }

    }
    /**
     * Function Name : coupons_status_change
     *
     * @uses To update the coupon status to APPROVE/DECLINE based on coupon id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - coupon_id
     * 
     * @return success/failure message.
     *
     */
    public function coupons_status_change(Request $request) {

        try {

            $coupons_details = Coupon::find($request->coupon_id);

            if ( count($coupons_details) == 0 ) {

               throw new Exception(tr('admin_coupon_not_found'), 101);                
            }

            DB::beginTransaction();

            $coupons_details->status = $coupons_details->status == APPROVED ? DECLINED : APPROVED ;

            if( $coupons_details->save() ) {
                
                $message = $coupons_details->status == APPROVED ? tr('admin_coupon_approve_success') : tr('admin_coupon_declined_success') ;
                
                DB::commit();                

                return back()->with('flash_success',$message );

            } else {
                
                throw new Exception(tr('admin_coupon_status_error'), 101);
            }
            
        } catch (Exception $e) {

            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    }

    /**
     * Function Name : ads_details_index()
     *
     * @uses To list out ads_details object details
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param
     *
     * @return View page
     */
    public function ads_details_index() {

        $ads_details = AdminRepo::ads_details_index()->getData();

        return view('new_admin.ads_details.index')
                    ->withPage('ads_details')
                    // ->with('sub_page','ads_details-view')
                    ->with('sub_page','videos_ads')
                    ->with('ads_details' , $ads_details);
    }

    /**
     * Function Name : ads_details_create()
     *
     * @uses To create a ads_detail object details
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param 
     *
     * @return View page
     */
    public function ads_details_create(Request $request) {

        $ads_detail_details = new AdsDetail;

        return view('new_admin.ads_details.create')
                    ->with('page' , 'ads_details')
                    ->with('sub_page','videos_ads')
                    ->with('ads_detail_details', $ads_detail_details);
    }

    /**
     * Function Name : ads_details_view
     *
     * @uses To view the ads_detail based on the ads_detail id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer (request) $ads_detail_id
     * 
     * @return view page
     *
     */
    public function ads_details_view(Request $request) {

        try {

            $ads_detail_details = AdsDetail::find($request->ads_detail_id);

            if (count($ads_detail_details) == 0) {

                throw new Exception(tr('admin_ads_detail_not_found'), 101);
            }

            return view('new_admin.ads_details.view')
                    ->with('page','ads_details')
                    ->with('sub_page','ads_details-view')
                    ->with('ads_detail_details',$ads_detail_details);        
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    
    }

    public function ads_details_save(Request $request) {

        try {

            $response = AdminRepo::ads_details_save($request)->getData();

            if($response->success) {

                return redirect()->route('admin.ads-details.view', ['ads_detail_id'=>$response->data->id])->with('flash_success', $response->message);

            } else {

                throw new Exception($response->message, 101);                
            }
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }

    }

    /**
     * Function Name : ads_details_edit
     *
     * @uses To edit a ads_detail based on their id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer $request - ads_detail_id
     * 
     * @return response of new ads_detail object
     *`
     */
    public function ads_details_edit(Request $request) {
        
        try {
          
            $ads_detail_details = AdsDetail::find($request->ads_detail_id);

            if( count($ads_detail_details) == 0 ) {

                throw new Exception( tr('admin_ads_detail_not_found'), 101);

            } else {

                $ads_detail_details->dob = ($ads_detail_details->dob) ? date('d-m-Y', strtotime($ads_detail_details->dob)) : '';

                return view('new_admin.ads_details.edit')
                            ->with('page' , 'ads_details')
                            ->with('sub_page','ads_details-view')
                            ->with('ads_detail_details',$ads_detail_details);
            }

        } catch( Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->route('admin.users.index')->with('flash_error',$error);
        }
    
    }


    /**
     * Function Name : ads_details_delete
     *
     * @uses To delete the ads_details based on ads_details id
     *
     * @created 
     *
     * @updated 
     *
     * @param integer (request) $ads_details_id
     * 
     * @return response of ads_details edit
     *
     */
    public function ads_details_delete(Request $request) {

        try {
        
            $ads_detail_details = AdsDetail::find($request->ads_detail_id);

            if (count($ads_detail_details) == 0) {

                throw new Exception(tr('admin_ads_detail_not_found'), 101);
            }

            DB::beginTransaction();

            foreach ($ads_detail_details->getAssignedVideo as $key => $value) {

                if ($value->videoAd) {

                    if ($value->videoAd->delete()) {  
                        // do nothing
                    } else {

                        throw new Exception(tr('admin_video_ad_delete_error'), 101);
                    }
                }

                if ($value->delete()) {  
                    // do nothing
                } else {

                    throw new Exception(tr('admin_ads_detail_delete_error'), 101);
                } 
            } 
        
            if ($ads_detail_details->delete()) {  

                DB::commit();
                
                return back()->with('flash_success',tr('admin_ads_detail_delete_success'));

            } else {

                throw new Exception(tr('admin_ads_detail_delete_error'), 101);
            }
            
        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }    
    }

    /**
     * Function Name : ads_details_status
     *
     * @uses To delete the ads_details based on ads_details id
     *
     * @created 
     *
     * @updated 
     *
     * @param integer (request) $ads_details_id
     * 
     * @return response of ads_details edit
     *
     */
    public function ads_details_status(Request $request) {
        
        try {

            $ads_detail_details = AdsDetail::find($request->ads_detail_id);

            if (count($ads_detail_details) == 0) {

                throw new Exception(tr('admin_ads_detail_not_found'), 101);
            }

            DB::beginTransaction();

            $ads_detail_details->status = $ads_detail_details->status == DEFAULT_TRUE ? DEFAULT_FALSE : DEFAULT_TRUE ;

            $message = $ads_detail_details->status == DEFAULT_TRUE ?  tr('admin_ads_detail_approved_success') : tr('admin_ads_detail_declined_success') ;

            if( $ads_detail_details->save()) {

                DB::commit();
                
            } else {

                throw new Exception(tr('admin_ad_status_save_error'), 101);                
            }

            // Load Assigned video ads

            $assigned_video_ad = AssignVideoAd::where('ad_id', $ads_detail_details->id)->get();

            foreach ($assigned_video_ad as $key => $value) {
               
                // Load video ad
                $video_ad = VideoAd::find($value->video_ad_id);

                $ad_type = $value->ad_type;

                if($video_ad) {

                    $exp_video_ad = explode(',', $video_ad->types_of_ad);

                    if (count($exp_video_ad) == 1) {

                        $video_ad->delete();

                    } else {

                        $type_of_ad = [];

                        foreach ($exp_video_ad as $key => $exp_ad) {
                                
                            if ($exp_ad == $ad_type) {

                            } else {

                                $type_of_ad[] = $exp_ad;
                            }     
                        }

                        $video_ad->types_of_ad = is_array($type_of_ad) ? implode(',', $type_of_ad) : '';

                        $video_ad->save();

                    }
                }
            }

            return back()->with('flash_success', $message);
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    
    }

    /**
    * Function Name: help()
    *
    * @uses To delete the ads_details based on ads_details id
    *
    * @created
    *
    * @edited
    *
    * @param 
    *
    * @return view page
    */
    public function help() {

        return view('new_admin.static_pages.help')
                ->withPage('help')
                ->with('sub_page' , "");

    }

    /**
    * Function Name: profile()
    *
    * @uses To display Admin details 
    *
    * @created
    *
    * @edited
    *
    * @param
    *
    * @return view page
    */
    public function profile() {

        $admin = Admin::first();

        return view('new_admin.account.profile')
                ->withPage('profile')
                ->with('sub_page','')
                ->with('admin' , $admin);
    
    }

    /**
     * Function Name: profile_process()
     *
     * @uses To save admin account datails  
     *
     * @created
     *
     * @edited
     *
     * @param 
     *
     * @return view page
     */
    public function profile_process(Request $request) {
        try {

            $validator = Validator::make( $request->all(),array(
                    'name' => 'max:255',
                    'email' => $request->id ? 'email|max:255|unique:admins,email,'.$request->id : 'email|max:255|unique:admins,email,NULL',
                    'mobile' => 'digits_between:6,13',
                    'address' => 'max:300',
                    'id' => 'required|exists:admins,id',
                    'picture' => 'mimes:jpeg,jpg,png'
                )
            );
            
            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                return back()->with('flash_errors', $error);

            } else {
                
                $admin_details = Admin::find($request->id);
                
                $admin_details->name = $request->has('name') ? $request->name : $admin_details->name;

                $admin_details->email = $request->has('email') ? $request->email : $admin_details->email;

                $admin_details->mobile = $request->has('mobile') ? $request->mobile : $admin_details->mobile;

                $admin_details->gender = $request->has('gender') ? $request->gender : $admin_details->gender;

                $admin_details->address = $request->has('address') ? $request->address : $admin_details->address;

                if($request->hasFile('picture')) {

                    Helper::delete_picture($admin_details->picture, "/uploads/images/");

                    $admin_details->picture = Helper::normal_upload_picture($request->picture, "/uploads/images/");
                }
                    
                $admin_details->remember_token = Helper::generate_token();
                
                $admin_details->save();

                return back()->with('flash_success', tr('admin_not_profile'));
                
            }
            
        } catch (Exception $e) {
            
            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    
    }

    /**
     * Function Name: profile_save()
     *
     * @uses To save Admin profile details 
     *
     * @created Anjana H
     *
     * @edited Anjan H
     *
     * @param Integer (request) $id
     *
     * @return view page
     */
    public function profile_save(Request $request) {

        try {

            DB::beginTransaction();

            $validator = Validator::make( $request->all(),array(
                    'name' => 'max:255',
                    'email' => $request->id ? 'email|max:255|unique:admins,email,'.$request->id : 'email|max:255|unique:admins,email,NULL',
                    'mobile' => 'digits_between:6,13',
                    'address' => 'max:300',
                    'id' => 'required|exists:admins,id',
                    'picture' => 'mimes:jpeg,jpg,png'
                )
            );
            
            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                return back()->with('flash_errors', $error);

            } else {
                
                $admin_details = Admin::find($request->id);
                
                $admin_details->name = $request->has('name') ? $request->name : $admin_details->name;

                $admin_details->email = $request->has('email') ? $request->email : $admin_details->email;

                $admin_details->mobile = $request->has('mobile') ? $request->mobile : $admin_details->mobile;

                $admin_details->gender = $request->has('gender') ? $request->gender : $admin_details->gender;

                $admin_details->address = $request->has('address') ? $request->address : $admin_details->address;

                if($request->hasFile('picture')) {

                    Helper::delete_picture($admin_details->picture, "/uploads/images/");

                    $admin_details->picture = Helper::normal_upload_picture($request->picture, "/uploads/images/");
                }
                    
                $admin_details->remember_token = Helper::generate_token();
                
                if ($admin_details->save()) {
                            
                    DB::commit();

                    return back()->with('flash_success', tr('admin_not_profile'));

                } else {

                    throw new Exception(tr('admin_profile_save_error'), 101);
                }                
            }

        } catch (Exception $e) {

            DB::rollback();

            $error = $e->getMessage();

            return redirect()->route('admin.categories.index')->with('flash_error',$error);
        }
    
    }


    /**
     * Function: change_password()
     * 
     * @uses change the admin password 
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param - 
     *
     * @return redirect with success/ error message
     */
    public function change_password(Request $request) {
        
        try {
 
            $validator = Validator::make($request->all(), [ 
                    'id' => 'required|exists:admins,id',             
                    'old_password' => 'required',
                    'password' => 'required|confirmed|min:6',
                    'confirm_password' => 'required|min:6'
            ]);
           
            if( $validator->fails() ) {

                $error = implode(',',$validator->messages()->all());

                throw new Exception($error, 101);

            } else {

                $old_password = $request->old_password;

                $new_password = $request->password;

                $confirm_password = $request->confirm_password;

                $admin_details = Admin::find($request->id);

                if( Hash::check($old_password,$admin_details->password) ) {
                    
                    $admin_details->password = Hash::make( $new_password );
                   
                    if( $admin_details->save() ) {

                        return back()->with('flash_success', tr('admin_password_change_success'));
                    
                    } else {
                    
                        throw new Exception(tr('admin_password_save_error'), 101);
                    }
                    
                } else {

                    throw new Exception(tr('admin_password_mismatch'), 101);
                }
            }

            $response = response()->json($response_array,$response_code);

            return $response;
            
        } catch (Exception $e) {  
            
            DB::rollback();
            
            $error = $e->getMessage();

            return redirect()->route('admin.profile')->with('flash_error',$error);
        }
    
    }

      /**
     * Function: pages_index()
     * 
     * @uses To list the static_pages
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param
     *
     * @return view page
     */
    public function pages_index() {

        $pages = Page::orderBy('created_at' , 'desc')->paginate(10);

        return view('new_admin.pages.index')
                    ->with('page','pages')
                    ->with('sub_page','pages-view')
                    ->with('pages',$pages);
    }

    /**
     * Function Name : pages_create()
     *
     * @uses To list out pages object details
     *
     * @created Anjana H 
     *
     * @updated Anjana H
     *
     * @param
     *
     * @return View page
     */
    public function pages_create() {

        $page_details = new Page;

        return view('new_admin.pages.create')
                    ->with('page' , 'pages')
                    ->with('sub_page',"pages-create")
                    ->with('page_details', $page_details);
    }
      
    /**
     * Function Name : pages_edit()
     *
     * @uses To display and update pages object details based on the pages id
     *
     * @created  Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer (request) $static_page_id
     *
     * @return View page
     */
    public function pages_edit(Request $request) {

        try {
          
            $page_details = Page::find($request->page_id);

            if( count($page_details) == 0 ) {

                throw new Exception( tr('admin_page_not_found'), 101);

            } else {

                return view('new_admin.pages.edit')
                        ->with('page' , 'pages')
                        ->with('sub_page','pages-view')
                        ->with('page_details',$page_details);
            }

        } catch( Exception $e) {
            
            $error = $e->getMessage();

            return redirect()->route('admin.pages.index')->with('flash_error',$error);
        }
    }

    /**
     * Function Name : pages_save()
     *
     * @uses To save the page object details of new/existing based on details
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer (request) $page_id , (request) page details
     *
     * @return success/error message
     */
    public function pages_save(Request $request) {

        try {

            $validator = Validator::make($request->all() , array(
                'type' => $request->page_id ? '' : 'required',
                'heading' => 'required|max:255',
                'description' => 'required',
            ));

            if( $validator->fails() ) {

                $error = implode(',',$validator->messages()->all());

                throw new Exception($error, 101);
                
            } else {

                if( $request->has('page_id') ) {

                    $page_details = Page::find($request->page_id);

                } else {

                    if(Page::count() < Setting::get('no_of_static_pages')) {

                        if( $request->type != 'others' ) {

                            $check_page_type = Page::where('type',$request->type)->first();
                            
                            if($check_page_type){

                                throw new Exception(tr('admin_page_exists').$request->type , 101);
                            }
                        }
                        
                        $page_details = new Page;
                        
                    } else {

                        throw new Exception(tr('admin_page_exists').$request->type , 101);
                    }                    
                }

                if( $page_details ) {

                    $page_details->type = $request->type ? $request->type : $page_details->type;

                    $page_details->heading = $request->heading ? $request->heading : $page_details->heading;

                    $page_details->description = $request->description ? $request->description : $page_details->description;

                    if( $page_details->save() ) {

                        DB::commit();

                        return back()->with('flash_success',tr('admin_page_create_success'));

                    } else {

                        throw new Exception(tr('admin_page_save_error'), 101);
                    }
                }
            }            
                
        } catch (Exception $e) {

            $error = $e->getMessage();

            return redirect()->route('admin.pages.index')->with('flash_error',$error);
        }

    }

    /**
     * Function: pages_view()
     * 
     * @uses To display pages details based on pages id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer (request) $page_id
     *
     * @return view page
     */
    public function pages_view(Request $request) {

        try {

            $page_details = Page::find($request->page_id);
            
            if( count($page_details) == 0 ) {

                throw new Exception(tr('admin_page_not_found'), 101);
            }

            return view('new_admin.pages.view')
                    ->with('page' ,'pages')
                    ->with('sub_page' ,'pages-view')
                    ->with('page_details' ,$page_details);

        } catch (Exception $e) {

            $error = $e->getMessage();

            return redirect()->route('admin.pages.index')->with('flash_error',$error);
        }
    }

    /**
     * Function: pages_delete()
     * 
     * @uses To delete the page object based on page id
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param 
     *
     * @return success/failure message
     */
    public function pages_delete(Request $request) {

        try {
            DB::beginTransaction();
            
            $page_details = Page::where('id' , $request->page_id)->first();

            if( count($page_details) == 0 ) {  

                throw new Exception(tr('admin_page_not_found'), 101);
            }

            Helper::delete_picture($page_details->picture, "/uploads/images/pages/");
            
            if( $page_details->delete() ) {

                DB::commit();

                return redirect()->route('admin.pages.index')->with('flash_success',tr('admin_page_delete_success'));

            } else {

                throw new Exception(tr('admin_page_delete_error'), 101);               
            }

        } catch (Exception $e) {
            
            DB::rollback();

            $error = $e->getMessage();

            return back()->with('flash_error',$error);
        }
    }









}
