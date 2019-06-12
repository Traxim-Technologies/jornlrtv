<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Helpers\Helper;

use App\Repositories\VideoTapeRepository as VideoRepo;

use App\Repositories\CommonRepository as CommonRepo;

use App\Repositories\PaymentRepository as PaymentRepo;

use App\Repositories\UserRepository as UserRepo;

use App\Repositories\V5Repository as V5Repo;

use App\Jobs\sendPushNotification;

use App\Jobs\BellNotificationJob;

use Log;

use Hash;

use Validator;

use File;

use DB;

use Auth;

use Setting;

use App\Flag;

use App\User;

use App\UserRating;

use App\Wishlist;

use App\UserHistory;

use App\ChannelSubscription;

use App\Page;

use App\Jobs\NormalPushNotification;

use App\VideoTape;

use App\VideoTapeImage;

use App\Redeem;

use App\RedeemRequest;

use App\Channel;

use App\LikeDislikeVideo;

use App\Card;

use App\Subscription;

use App\UserPayment;

use Exception;

use App\PayPerView;

use App\Category;

use App\Tag;

use App\VideoTapeTag;

use App\Coupon;

use App\UserCoupon;

use App\CustomLiveVideo;

use App\Playlist;

use App\PlaylistVideo;

use App\BellNotification;

use App\UserReferrer;

use App\Referral;

class UserApiController extends Controller {

    protected $skip, $take;

    public function __construct(Request $request) {

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

        $this->middleware('UserApiVal' , array('except' => [
                'register' , 
                'login' , 
                'forgot_password',
                'search_video' , 
                'privacy',
                'about' , 
                'terms',
                'contact', 
                'home', 
                'trending' , 
                'getSingleVideo', 
                'get_channel_videos' ,  
                'help', 
                'single_video', 
                'reasons' ,
                'search_video', 
                'video_detail',
                'categories_videos',
                'tags_videos',
                'video_tapes_youtube_grapper_save',
                'categories_channels_list',
                'referrals_check',
                'categories_list',
                'categories_view',
                'categories_channels_list',
                'playlists_view',
                'playlists'
            ]));

        $this->middleware('ChannelOwner' , ['only' => ['video_tapes_status', 'video_tapes_delete', 'video_tapes_ppv_status','video_tapes_publish_status']]);

    }

    /**
     * Function Name : update_profile()
     * 
     * @usage_place : MOBILE & WEB
     * 
     * Save any changes to the users profile.
     * 
     * @param object $request - User Details
     *
     * @return \Illuminate\Http\Response
     */
    public function update_profile(Request $request) {
        
        $validator = Validator::make(
            $request->all(),
            array(
                'name' => 'required|max:255',
                'email' => 'email|unique:users,email,'.$request->id.'|max:255',
                'mobile' => 'digits_between:6,13',
                'picture' => 'mimes:jpeg,bmp,png',
                'gender' => 'in:male,female,others',
                'device_token' => '',
                'dob'=>'required',
            ));

        if ($validator->fails()) {
            // Error messages added in response for debugging
            $error_messages = implode(',',$validator->messages()->all());
            $response_array = array(
                    'success' => false,
                    'error' => Helper::get_error_message(101),
                    'error_code' => 101,
                    'error_messages' => $error_messages
            );
        } else {

            $user = User::find($request->id);

            if($user) {
                
                $user->name = $request->name ? $request->name : $user->name;
                
                if($request->has('email')) {
                    $user->email = $request->email;
                }

                $user->mobile = $request->mobile ? $request->mobile : $user->mobile;
                $user->gender = $request->gender ? $request->gender : $user->gender;
                $user->address = $request->address ? $request->address : $user->address;
                $user->description = $request->description ? $request->description : $user->address;


                if ($request->dob) {

                    $user->dob = date('Y-m-d', strtotime($request->dob));

                }

                if ($user->dob) {

                    $from = new \DateTime($user->dob);
                    $to   = new \DateTime('today');

                    $user->age_limit = $from->diff($to)->y;

                }

                if ($user->age_limit < 10) {

                    $response_array = ['success' => false , 'error_messages' => tr('min_age_error')];

                    return response()->json($response_array , 200);

                }


                // Upload picture

                if ($request->hasFile('picture') != "") {

                    Helper::delete_picture($user->picture, "/uploads/images/"); // Delete the old pic

                    $user->picture = Helper::normal_upload_picture($request->file('picture'), "/uploads/images/");
                }

                $user->save();
            }

            $payment_mode_status = $user->payment_mode ? $user->payment_mode : "";

            if (!empty($user->dob) && $user->dob != "0000-00-00") {

                $user->dob = date('d-m-Y', strtotime($user->dob));

            } else {

                $user->dob = "";
            }

            $response_array = array(
                'success' => true,
                'message' => tr('profile_updated'),
                'id' => $user->id,
                'name' => $user->name,
                'description' => $user->description,
                'mobile' => $user->mobile,
                'gender' => $user->gender,
                'email' => $user->email,
                'dob'=> $user->dob,
                'age'=>$user->age_limit,
                'picture' => $user->picture,
                'chat_picture' => $user->picture,
                'token' => $user->token,
                'token_expiry' => $user->token_expiry,
                'login_by' => $user->login_by,
                'social_unique_id' => $user->social_unique_id,
                'push_status' => $user->push_status,
                
            );

            $response_array = Helper::null_safe($response_array);
        
        }

        return response()->json($response_array, 200);
    
    }

    /**
     * Function Name : change_password
     *
     * @usage_place : MOBILE & WEB
     *
     * To change the password who has logged in user
     *
     * @param Object $request - User PAssword Details
     *
     * @return response of success/failure message
     */
    public function change_password(Request $request) {

        $validator = Validator::make($request->all(), [
                'password' => 'required|confirmed',
                'old_password' => 'required',
            ]);

        if($validator->fails()) {
            
            $error_messages = implode(',',$validator->messages()->all());
           
            $response_array = array('success' => false, 'error' => tr('invalid_input'), 'error_code' => 101, 'error_messages' => $error_messages );
       
        } else {

            $user = User::find($request->id);

            if(Hash::check($request->old_password,$user->password)) {

                $user->password = \Hash::make($request->password);
                
                $user->save();

                $response_array = Helper::null_safe(array('success' => true , 'message' => Helper::get_error_message(102)));

            } else {

                $response_array = array('success' => false , 'error' => '','error_messages' => Helper::get_error_message(131) ,'error_code' => 131);
            }

        }

        $response = response()->json($response_array,200);

        return $response;

    }

    /**
     * Function Name : add_history()
     *
     * @usage_place : MOBILE & WEB
     *
     * To Add in history based on user, once he complete the video , the video will save
     *
     * @param Integer $request - Video Id
     *
     * @return response of Boolean with message
     */
    public function add_history(Request $request)  {

        $validator = Validator::make(
            $request->all(),
            array(
                'video_tape_id' => 'required|integer|exists:video_tapes,id',
            ),
            array(
                'exists' => 'The :attribute doesn\'t exists please provide correct video id',
                'unique' => 'The :attribute already added in history.'
            )
        );

        if ($validator->fails()) {

            $error = implode(',', $validator->messages()->all());

            $response_array = array('success' => false, 'error_messages' => $error, 'error_code' => 101);

        } else {

            $payperview = PayPerView::where('user_id', $request->id)
                            ->where('video_id',$request->video_tape_id)
                            ->where('is_watched', '!=', WATCHED)
                            ->orderBy('ppv_date', 'desc')
                            ->where('status', PAID_STATUS)
                            ->first();

            if ($payperview) {

                $payperview->is_watched = WATCHED;

                $payperview->save();

            }

            if($history = UserHistory::where('user_histories.user_id' , $request->id)->where('video_tape_id' ,$request->video_tape_id)->first()) {

                // $response_array = array('success' => true , 'error_messages' => Helper::get_error_message(145) , 'error_code' => 145);

            } else {

                // Save Wishlist

                if($request->id) {

                    $rev_user = new UserHistory();
                    $rev_user->user_id = $request->id;
                    $rev_user->video_tape_id = $request->video_tape_id;
                    $rev_user->status = DEFAULT_TRUE;
                    $rev_user->save();

                }

           
            }

            $video = VideoTape::find($request->video_tape_id);

            $navigateback = 0;

            if ($request->id != $video->user_id) {

                if ($video->type_of_subscription == RECURRING_PAYMENT) {

                    $navigateback = 1;

                }
            }

            // navigateback = used to handle the replay in mobile for recurring payments

            $response_array = array('success' => true , 'navigateback' => $navigateback);
           

        }
        return response()->json($response_array, 200);
    
    }

    /**
     * Function Name : delete_history()
     *
     * @usage_place : MOBILE & WEB
     *
     * To Delete a history based on user
     *
     * @param Integer $request - Video Id
     *
     * @return response of Boolean with message
     */
    public function delete_history(Request $request) {

        $validator = Validator::make(
            $request->all(),
            array(
                'video_tape_id' =>$request->has('status') ?  'integer|exists:video_tapes,id' : 'required|integer|exists:video_tapes,id'
            ),
            array(
                'exists' => 'The :attribute doesn\'t exists please add to history',
            )
        );

        if ($validator->fails()) {

            $error = implode(',', $validator->messages()->all());

            $response_array = array('success' => false, 'error_messages' => $error, 'error_code' => 101);

        } else {


            if($request->has('status')) {

                $history = UserHistory::where('user_id',$request->id)->delete();

            } else {

                $history = UserHistory::where('user_id',$request->id)->where('video_tape_id' , $request->video_tape_id)->delete();

            }

            $response_array = array('success' => true);
        }

        return response()->json($response_array, 200);
    
    }

    /**
     * Function Name : wishlist_create()     
     *
     * @uses To add a video to wishlist based on details
     *
     * cretaed Anjana H 
     *   
     * updated Anjana H  
     *
     * @param Integer $request - Video Id, Id (user_id)
     *
     * @return success/failure message
     */
    public function wishlist_create(Request $request) {

        try {
            
            DB::beginTransaction();

            $validator = Validator::make(
                $request->all(),
                array(
                    'video_tape_id' => 'required|integer|exists:video_tapes,id',
                ),
                array(
                    'exists' => 'The :attribute doesn\'t exists please provide correct video id'
                )
            );

            if ($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);
            } 

            $wishlist_details = Wishlist::where('user_id' , $request->id)->where('video_tape_id' , $request->video_tape_id)->first();
            
            if( count($wishlist_details) > 0 ) {

                throw new Exception(Helper::get_error_message(505), 505);    
            } 

            $wishlist_details = new Wishlist();

            $wishlist_details->user_id = $request->id;

            $wishlist_details->video_tape_id = $request->video_tape_id;

            $wishlist_details->status = DEFAULT_TRUE;

            if($wishlist_details->save()) {
               
                DB::commit();

                $message = tr('user_wishlist_success');

                $response_array = array('success' => true , 'wishlist_id' => $wishlist_details->id , 'wishlist_status' => $wishlist_details->status, 'message' => $message);

                return response()->json($response_array, 200);

            } else {

                throw new Exception(tr('user_wishlist_save_error'), 101);
            }                   

        } catch (Exception $e) {

            DB::rollback();

            $message = $e->getMessage();

            $code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $message, 'error_code' => $code];

            return response()->json($response_array);
        }

    }

    /**
     * Function Name : wishlist_delete()     
     *
     * @uses To delete wishlist(s) based on details
     *
     * cretaed Anjana H 
     *   
     * updated Anjana H  
     *
     * @param Integer (request) - video tape id, id (user_id)
     *
     * @return success/failure message
     */
    public function wishlist_delete(Request $request) {

        try {
            
            DB::beginTransaction();

            $validator = Validator::make(
                $request->all(),
                array(
                    'video_tape_id' => 'required|integer|exists:video_tapes,id',
                ),
                array(
                    'exists' => 'The :attribute doesn\'t exists please provide correct video id'
                )
            );

            if ($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);  
            } 

            /** Clear All wishlist of the loggedin user */
            
            if($request->status == WISHLIST_DELETE_ALL) {

                $wishlist_details = Wishlist::where('user_id',$request->id)->delete();

            } else {  /** Clear particularv wishlist of the loggedin user */

                $wishlist_details = Wishlist::where('user_id',$request->id)->where('video_tape_id' , $request->video_tape_id)->delete();
            }
            
            DB::commit();

            if(!$wishlist_details) {                    

                throw new Exception(Helper::get_error_message(506), 506);                  
            }
            
            $message = tr('user_wishlist_delete_success');

            $response_array = array('success' => true, 'message' => $message);

            return response()->json($response_array, 200);       

        } catch (Exception $e) {
            
            DB::rollback();

            $message = $e->getMessage();

            $code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $message, 'error_code' => $code];

            return response()->json($response_array);
        }

    
    }

    /**
     * Function Name : add_comment()
     *
     * @usage_place : MOBILE & WEB
     * 
     * To Add comment based on single video
     *
     * @param integer $video_tape_id - Video Tape ID
     *
     * @return response of success/failure message
     */
    public function user_rating(Request $request) {

        $validator = Validator::make(
            $request->all(),
            array(
                'video_tape_id' => 'required|integer|exists:video_tapes,id',
                'ratings' => 'integer|in:'.RATINGS,
                'comments' => '',
            ),
            array(
                'exists' => 'The :attribute doesn\'t exists please provide correct video id',
                'unique' => 'The :attribute already rated.'
            )
        );

        if ($validator->fails()) {
            $error_messages = implode(',', $validator->messages()->all());
            $response_array = array('success' => false, 'error' => Helper::get_error_message(101), 'error_code' => 101, 'error_messages'=>$error_messages);

        } else {

            //Save Rating
            $rating = new UserRating();
            $rating->user_id = $request->id;
            $rating->video_tape_id = $request->video_tape_id;
            $rating->rating = $request->has('ratings') ? $request->ratings : 0;
            $rating->comment = $request->comments ? $request->comments: '';
            $rating->save();

            $ratings = UserRating::select(
                    'rating', 'video_tape_id',DB::raw('sum(rating) as total_rating'))
                    ->where('video_tape_id', $request->video_tape_id)
                    ->groupBy('video_tape_id')
                    ->avg('rating');

            if ($rating->adminVideo) {

                $rating->adminVideo->user_ratings = $ratings;

                $rating->adminVideo->save();

            }

            $response_array = array('success' => true , 'comment' => $rating->toArray() , 'date' => $rating->created_at->diffForHumans(),'message' => tr('comment_success') );
        }

        $response = response()->json($response_array, 200);
        return $response;
    
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
    public function delete_account(Request $request) {

        $validator = Validator::make(
            $request->all(),
            array(
                'password' => '',
            ));

        if ($validator->fails()) {
            $error_messages = implode(',',$validator->messages()->all());
            $response_array = array('success' => false,'error' => Helper::get_error_message(101),'error_code' => 101,'error_messages' => $error_messages
            );
        } else {

            $user = User::find($request->id);

            if($user->login_by != 'manual') {
                $allow = 1;
            } else {

                if(Hash::check($request->password, $user->password)) {
                    $allow = 1;
                } else {
                    $allow = 0 ;

                    $response_array = array('success' => false , 'error_messages' => Helper::get_error_message(108) ,'error_code' => 108);
                }

            }

            if($allow) {

                $user = User::where('id',$request->id)->first();

                if($user) {
                    $user->delete();
                    $response_array = array('success' => true , 'message' => tr('user_account_delete_success'));
                } else {
                    $response_array = array('success' =>false , 'error_messages' => Helper::get_error_message(146), 'error_code' => 146);
                }

            }

        }

        return response()->json($response_array,200);

    }

    /**
     * User manual and social register save 
     *
     *
     */
    public function register(Request $request) {

        $response_array = array();

        $operation = false;

        $new_user = DEFAULT_TRUE;

        // validate basic field

        $basicValidator = Validator::make(
            $request->all(),
            array(
                'device_type' => 'required|in:'.DEVICE_ANDROID.','.DEVICE_IOS,
                'device_token' => 'required',
                'login_by' => 'required|in:manual,facebook,google',
            )
        );

        if($basicValidator->fails()) {

            $errors = implode(',', $basicValidator->messages()->all());
            
            $response_array = ['success' => false, 'error' => Helper::get_error_message(101), 'error_code' => 101, 'error_messages'=> $errors];

            Log::info('Registration basic validation failed');

        } else {

            $login_by = $request->login_by;
            $allowedSocialLogin = array('facebook','google');

            // check login-by

            if(in_array($login_by,$allowedSocialLogin)) {

                // validate social registration fields

                $socialValidator = Validator::make(
                            $request->all(),
                            array(
                                'social_unique_id' => 'required',
                                'name' => 'required|max:255',
                                'email' => 'required|email|max:255',
                                'mobile' => 'digits_between:6,13',
                                'picture' => '',
                                'gender' => 'in:male,female,others',
                            )
                        );

                if($socialValidator->fails()) {

                    $error_messages = implode(',', $socialValidator->messages()->all());
                    $response_array = array('success' => false, 'error' => Helper::get_error_message(101), 'error_code' => 101, 'error_messages'=> $error_messages);

                    Log::info('Registration social validation failed');

                } else {

                    $check_social_user = User::where('email' , $request->email)->first();

                    if($check_social_user) {
                        $new_user = DEFAULT_FALSE;
                    }

                    Log::info('Registration passed social validation');
                    $operation = true;
               
                }

            } else {

                // Validate manual registration fields

                $manualValidator = Validator::make(
                    $request->all(),
                    array(
                        'name' => 'required|max:255',
                        'email' => 'required|email|max:255',
                        'mobile' => 'digits_between:6,13',
                        'password' => 'required|min:6',
                        'picture' => 'mimes:jpeg,jpg,bmp,png',
                    )
                );

                // validate email existence

                $emailValidator = Validator::make(
                    $request->all(),
                    array(
                        'email' => 'unique:users,email',
                    )
                );

                if($manualValidator->fails()) {

                    $errors = implode(',', $manualValidator->messages()->all());
                    
                    $response_array = ['success' => false, 'error' => Helper::get_error_message(101), 'error_code' => 101, 'error_messages'=> $errors];

                    Log::info('Registration manual validation failed');

                } elseif($emailValidator->fails()) {

                    $errors = implode(',', $emailValidator->messages()->all());

                    $response_array = ['success' => false, 'error' => Helper::get_error_message(101), 'error_code' => 101, 'error_messages'=> $errors];

                    Log::info('Registration manual email validation failed');

                } else {
                    Log::info('Registration passed manual validation');
                    $operation = true;
                }

            }

            if($operation) {

                // Creating the user
                if($new_user) {
                    $user = new User;
                    register_mobile($request->device_type);
                } else {
                    $user = $check_social_user;
                }

                if($request->has('name')) {
                    $user->name = $request->name;
                }

                if($request->has('email')) {
                    $user->email = $request->email;
                }

                if($request->has('dob')) {
                    $user->dob = date("Y-m-d" , strtotime($request->dob));
                }

                 if ($user->dob) {

                    if ($user->dob != '0000-00-00') {

                        $from = new \DateTime($user->dob);
                        $to   = new \DateTime('today');

                        $user->age_limit = $from->diff($to)->y;

                    }

                }

                if($request->has('mobile')) {
                    $user->mobile = $request->mobile;
                }

                if($request->has('password'))
                    $user->password = Hash::make($request->password);

                $user->gender = $request->has('gender') ? $request->gender : "male";

                $user->token = Helper::generate_token();
                $user->token_expiry = Helper::generate_token_expiry();

                $check_device_exist = User::where('device_token', $request->device_token)->first();

                if($check_device_exist){
                    $check_device_exist->device_token = "";
                    $check_device_exist->save();
                }

                Log::info("Device Token - ".$request->device_token);
                $user->device_token = $request->device_token;
                $user->device_type = $request->has('device_type') ? $request->device_type : "";
                $user->login_by = $request->has('login_by') ? $request->login_by : "";
                $user->social_unique_id = $request->has('social_unique_id') ? $request->social_unique_id : '';

                $user->picture = asset('placeholder.png');

                // Upload picture
                if($request->login_by == "manual") {

                    if($request->hasFile('picture')) {
                        $user->picture = Helper::normal_upload_picture($request->file('picture'), "/uploads/images/");
                    }
                } else {
                    if($request->has('picture')) {
                        $user->picture = $request->picture;
                    }

                    $user->is_verified = 1;
                }

                // $user->is_activated = 1;

                $user->save();

                // Send welcome email to the new user:

                if($new_user) {

                    // Check the default subscription and save the user type 

                    if($request->referral_code) {

                        UserRepo::referral_register($request->referral_code, $user);
                    }

                    user_type_check($user->id);

                    $subject = tr('user_welcome_title' , Setting::get('site_name'));
                    $email_data = $user;
                    $page = "emails.welcome";
                    $email = $user->email;
                    Helper::send_email($page,$subject,$email,$email_data);
                }

                if($user->is_verified == USER_EMAIL_NOT_VERIFIED) {

                    if(Setting::get('email_verify_control') && !in_array($user->login_by, ['facebook' , 'google'])) {

                        // Check the verification code expiry

                        Helper::check_email_verification("" , $user, $error, USER);
                    
                        $response = array('success' => false , 'error_messages' => Helper::get_error_message(503) , 'error_code' => 503);

                        return response()->json($response, 200);

                    }
                
                }

                if($user->status == USER_DECLINED) {
                    
                    $response = array('success' => false , 'error_messages' => Helper::get_error_message(502) , 'error_code' => 502);

                    return response()->json($response, 200);
                
                }

                // Response with registered user details:

                $response_array = array(
                    'success' => true,
                    'id' => $user->id,
                    'name' => $user->name,
                    'mobile' => $user->mobile,
                    'gender' => $user->gender,
                    'email' => $user->email,
                    'picture' => $user->picture,
                    'token' => $user->token,
                    'token_expiry' => $user->token_expiry,
                    'login_by' => $user->login_by,
                    'user_type' => $user->user_type,
                    'social_unique_id' => $user->social_unique_id,
                    'push_status' => $user->push_status,
                    'payment_subscription' => Setting::get('ios_payment_subscription_status')

                );

                $response_array = Helper::null_safe($response_array);

                Log::info('Registration completed');

            }
        }

        return response()->json($response_array, 200);
    
    }

    /**
     * User manual and social login 
     *
     *
     */

    public function login(Request $request) {

        $response_array = [];

        $operation = false;

        $basicValidator = Validator::make(
            $request->all(),
            array(
                'device_token' => 'required',
                'device_type' => 'required|in:'.DEVICE_ANDROID.','.DEVICE_IOS,
                'login_by' => 'required|in:manual,facebook,google',
            )
        );

        if($basicValidator->fails()){
            
            $errors = implode(',',$basicValidator->messages()->all());
            
            $response_array = ['success' => false, 'error' => Helper::get_error_message(101), 'error_code' => 101, 'error_messages'=> $errors];
        
        } else {

            $login_by = $request->login_by;
            /*validate manual login fields*/
            $manualValidator = Validator::make(
                $request->all(),
                array(
                    'email' => 'required|email',
                    'password' => 'required',
                )
            );

            if ($manualValidator->fails()) {

                $errors = implode(',',$manualValidator->messages()->all());

                $response_array = ['success' => false, 'error' => Helper::get_error_message(101), 'error_code' => 101, 'error_messages'=> $errors];
            
            } else {

                // Validate the user credentials

                if($user = User::where('email', '=', $request->email)->first()) {

                    if($user->is_verified == USER_EMAIL_NOT_VERIFIED) {

                        if(Setting::get('email_verify_control') && !in_array($user->login_by, ['facebook' , 'google'])) {

                            // Check the verification code expiry

                            Helper::check_email_verification("" , $user, $error, USER);
                        
                            $response = array('success' => false , 'error_messages' => Helper::get_error_message(503) , 'error_code' => 503);

                            return response()->json($response, 200);

                        }
                    
                    }

                    if($user->status == USER_DECLINED) {
                        
                        $response = array('success' => false , 'error_messages' => Helper::get_error_message(502) , 'error_code' => 502);

                        return response()->json($response, 200);
                    
                    }

                    if(Hash::check($request->password, $user->password)){

                        /* manual login success */
                        $operation = true;

                    } else {
                        $response_array = [ 'success' => false, 'error_messages' => Helper::get_error_message(105), 'error_code' => 105 ];
                    }
                    

                } else {
                    $response_array = [ 'success' => false, 'error_messages' => Helper::get_error_message(105), 'error_code' => 105 ];
                }
            
            }

            if($operation) {

                // Generate new tokens
                $user->token = Helper::generate_token();
                $user->token_expiry = Helper::generate_token_expiry();

                // Save device details
                $user->device_token = $request->device_token;
                $user->device_type = $request->device_type;
                $user->login_by = $request->login_by;

                $user->save();

                $payment_mode_status = $user->payment_mode ? $user->payment_mode : 0;

                // Respond with user details

                $response_array = array(
                    'success' => true,
                    'id' => $user->id,
                    'name' => $user->name,
                    'mobile' => $user->mobile,
                    'email' => $user->email,
                    'gender' => $user->gender,
                    'picture' => $user->picture,
                    'chat_picture' => $user->picture,
                    'token' => $user->token,
                    'token_expiry' => $user->token_expiry,
                    'login_by' => $user->login_by,
                    'user_type' => $user->user_type,
                    'social_unique_id' => $user->social_unique_id,
                    'push_status' => $user->push_status,
                    'dob'=> $user->dob,
                    'description'=> $user->description,
                    'payment_subscription' => Setting::get('ios_payment_subscription_status')

                );

                $response_array = Helper::null_safe($response_array);
            }
        }

        return response()->json($response_array, 200);
    }

    public function forgot_password(Request $request) {

        $email =$request->email;
        // Validate the email field
        $validator = Validator::make(
            $request->all(),
            array(
                'email' => 'required|email|exists:users,email',
            )
        );

        if ($validator->fails()) {
            
            $error_messages = implode(',',$validator->messages()->all());
            
            $response_array = array('success' => false, 'error' => Helper::get_error_message(101), 'error_code' => 101, 'error_messages'=> $error_messages);
        
        } else {

            $user = User::where('email' , $email)->first();

            if($user) {

                if ($user->login_by == 'manual') {

                    $new_password = Helper::generate_password();
                    $user->password = \Hash::make($new_password);

                    $email_data = array();
                    $subject = tr('user_forgot_email_title');
                    $email = $user->email;
                    $email_data['user']  = $user;
                    $email_data['password'] = $new_password;
                    $page = "emails.forgot-password";
                    $email_send = Helper::send_email($page,$subject,$user->email,$email_data);

                    $response_array['success'] = true;
                    $response_array['message'] = Helper::get_message(106);
                    $user->save();

                } else {

                    $response_array = ['success'=>false, 'error_messages'=>tr('only_manual_can_access')];

                }

            }

        }

        $response = response()->json($response_array, 200);

        return $response;
    }



    public function user_details(Request $request) {

        $user = User::find($request->id);

        if (!empty($user->dob) && $user->dob != "0000-00-00") {

            $user->dob = date('d-m-Y', strtotime($user->dob));

        } else {

            $user->dob = "";
        }

        // $user->dob = date('d-m-Y', strtotime($user->dob));

        $response_array = array(
            'success' => true,
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'description'=>$user->description,
            'dob'=> $user->dob,
            'age'=>$user->age_limit,
            'picture' => $user->picture,
            'chat_picture' => $user->picture,
            'mobile' => $user->mobile,
            'gender' => $user->gender,
            'token' => $user->token,
            'token_expiry' => $user->token_expiry,
            'login_by' => $user->login_by,
            'social_unique_id' => $user->social_unique_id,
            'push_status' => $user->push_status,
            'user_type'=>$user->user_type ? $user->user_type : 0
        );
        $response = response()->json(Helper::null_safe($response_array), 200);
        return $response;
    }






    /**
     *
     * Get wishlists
     *
     */
    public function get_wishlist(Request $request)  {

        // Get wishlist 

        $video_tape_ids = Helper::wishlists($request->id);

        $total = get_wishlist_count($request->id);

        $data = [];

        if($video_tape_ids) {

            $base_query = VideoTape::whereIn('video_tapes.id' , $video_tape_ids)   
                                ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id') 
                                ->leftJoin('categories' , 'categories.id' , '=' , 'video_tapes.category_id') 
                                ->where('video_tapes.status' , 1)
                                ->where('video_tapes.publish_status' , 1)
                                ->where('video_tapes.is_approved' , 1)
                                ->where('channels.is_approved', 1)
                                ->where('channels.status', 1)
                                ->where('categories.status', CATEGORY_APPROVE_STATUS)
                               // ->orderby('video_tapes.publish_time' , 'desc')
                                ->videoResponse();

            if ($request->id) {

                // Check any flagged videos are present

                $flag_videos = flag_videos($request->id);

                if($flag_videos) {

                    $base_query->whereNotIn('video_tapes.id',$flag_videos);

                }
            
            }

            $base_query->where('video_tapes.age_limit','<=', checkAge($request));

            $videos = $base_query->skip($request->skip)->take(Setting::get('admin_take_count' ,12))->get();

            if(count($videos) > 0) {

                foreach ($videos as $key => $value) {

                    $user_details = '';

                    $is_ppv_status = DEFAULT_TRUE;

                    if($request->id) {

                        if($user_details = User::find($request->id)) {

                            $value['user_type'] = $user_details->user_type;

                            $is_ppv_status = ($value->type_of_user == NORMAL_USER || $value->type_of_user == BOTH_USERS) ? ( ( $user_details->user_type == 0 ) ? DEFAULT_TRUE : DEFAULT_FALSE ) : DEFAULT_FALSE; 

                        }
                    }

                    $value['is_ppv_subscribe_page'] = $is_ppv_status;

                    $value['pay_per_view_status'] = VideoRepo::pay_per_views_status_check($user_details ? $user_details->id : '', $user_details ? $user_details->user_type : '', $value)->getData()->success;

                    $value['currency'] = Setting::get('currency');

                    $value['watch_count'] = number_format_short($value->watch_count);

                    $value['wishlist_status'] = $request->id ? (Helper::check_wishlist_status($request->id,$value->video_tape_id) ? DEFAULT_TRUE : DEFAULT_FALSE): 0;

                    $value['share_url'] = route('user.single' , $value->video_tape_id);

                    array_push($data, $value->toArray());
                }
            
            }

        }

        $response_array = array('success' => true, 'data' => $data , 'total' => $total);

        return response()->json($response_array, 200);
    
    }


    public function spam_videos($request, $count = null, $skip = 0) {

        $query = Flag::where('flags.user_id', $request->id)->select('flags.*')
                    ->where('flags.status', DEFAULT_TRUE)
                    ->leftJoin('video_tapes', 'flags.video_tape_id', '=', 'video_tapes.id')
                    ->where('video_tapes.is_approved' , 1)
                    ->where('video_tapes.status' , 1)
                    ->where('video_tapes.age_limit','<=', checkAge($request))
                    ->orderBy('flags.created_at', 'desc');

        if($count) {

            $paginate = $query->paginate($count);

            $model = array('data' => $paginate->items(), 'pagination' => (string) $paginate->links());


        } else if($skip) {

            $paginate = $query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();

            $model = array('data' => $paginate, 'pagination' => '');

        } else {

            $paginate = $query->get();

            $model = array('data' => $paginate, 'pagination' => '');

        }

        $items = [];

        foreach ($model['data'] as $key => $value) {
            
            $items[] = displayVideoDetails($value->videoTape, $request->id);

        }

        return response()->json(['items'=>$items, 'pagination'=>isset($model['pagination']) ? $model['pagination'] : 0]);
    }





    /**
     * Get History videos of the user
     *
     */

    public function get_history(Request $request) {

        // Get History 

        $video_tape_ids = Helper::history($request->id);

        $total = get_history_count($request->id);

        $data = [];

        if($video_tape_ids) {

            $base_query = VideoTape::whereIn('video_tapes.id' , $video_tape_ids)   
                                ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id') 
                                ->where('video_tapes.status' , 1)
                                ->where('video_tapes.publish_status' , 1)
                                ->where('video_tapes.is_approved' , 1)
                                ->orderby('video_tapes.publish_time' , 'desc')
                                ->videoResponse();

            if ($request->id) {

                // Check any flagged videos are present

                $flag_videos = flag_videos($request->id);

                if($flag_videos) {

                    $base_query->whereNotIn('video_tapes.id',$flag_videos);

                }
            
            }

            $base_query->where('video_tapes.age_limit','<=', checkAge($request));

            $videos = $base_query->skip($request->skip)->take(Setting::get('admin_take_count' ,12))->get();

            if(count($videos) > 0) {

                foreach ($videos as $key => $value) {

                    $user_details = '';

                    $is_ppv_status = DEFAULT_TRUE;

                    if($request->id) {

                        if($user_details = User::find($request->id)) {

                            $value['user_type'] = $user_details->user_type;

                            $is_ppv_status = ($value->type_of_user == NORMAL_USER || $value->type_of_user == BOTH_USERS) ? ( ( $user_details->user_type == 0 ) ? DEFAULT_TRUE : DEFAULT_FALSE ) : DEFAULT_FALSE; 

                        }
                    }


                    $value['is_ppv_subscribe_page'] = $is_ppv_status;

                    $value['currency'] = Setting::get('currency');

                    $value['pay_per_view_status'] = VideoRepo::pay_per_views_status_check($user_details ? $user_details->id : '', $user_details ? $user_details->user_type : '', $value)->getData()->success;

                    $value['watch_count'] = number_format_short($value->watch_count);

                    $value['wishlist_status'] = $request->id ? (Helper::check_wishlist_status($request->id,$value->video_tape_id) ? DEFAULT_TRUE : DEFAULT_FALSE): 0;

                    $value['history_status'] = $request->id ? Helper::history_status($value->id,$value->video_tape_id) : 0;

                    $value['share_url'] = route('user.single' , $value->video_tape_id);

                    array_push($data, $value->toArray());
                }
            
            }

        }

		//get wishlist

        // $history = VideoRepo::watch_list($request,NULL,$request->skip);


		$response_array = array('success' => true, 'data' => $data , 'total' => $total);

        return response()->json($response_array, 200);
    
    }

    public function get_channels(Request $request) {

        $channels = getChannels();

        if($channels) {

            $response_array = array('success' => true , 'categories' => $channels->toArray());

        } else {
            $response_array = array('success' => false,'error_messages' => Helper::get_error_message(135),'error_code' => 135);
        }

        $response = response()->json($response_array, 200);
        return $response;
    }


    public function get_videos(Request $request) {

        $channels = VideoRepo::all_videos(WEB);

        if($channels) {

            $response_array = array('success' => true , 'channels' => $channels->toArray());

        } else {
            $response_array = array('success' => false,'error_messages' => Helper::get_error_message(135),'error_code' => 135);
        }

        $response = response()->json($response_array, 200);
        
        return $response;
    }

    /** 
     * home()
     *
     * return list of videos 
     */

    public function home(Request $request) {

        $data = [];

        $base_query = VideoTape::where('video_tapes.is_approved' , 1)   
                            ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id') 
                            ->leftJoin('categories' , 'categories.id' , '=' , 'video_tapes.category_id') 
                            ->where('video_tapes.status' , 1)
                            ->where('video_tapes.publish_status' , 1)
                            ->orderby('video_tapes.created_at' , 'desc')
                            ->where('channels.is_approved', 1)
                            ->where('channels.status', 1)
                            ->where('categories.status', CATEGORY_APPROVE_STATUS)
                            ->videoResponse();

        if ($request->id) {

            // Check any flagged videos are present

            $flag_videos = flag_videos($request->id);

            if($flag_videos) {

                $base_query->whereNotIn('video_tapes.id',$flag_videos);

            }

        }

        $base_query->where('video_tapes.age_limit','<=', checkAge($request));

        $videos = $base_query->skip($request->skip)->take(Setting::get('admin_take_count' ,12))->get();

        if(count($videos) > 0) {

            foreach ($videos as $key => $value) {

                $data[] = displayVideoDetails($value, $request->id);

            }
        }

        $response_array = array('success' => true , 'data' => $data);

        return response()->json($response_array , 200);

    }

    /** 
     * trending()
     *
     * return list of videos 
     */

    public function trending(Request $request) {

        $data = [];

        $base_query = VideoTape::where('video_tapes.is_approved' , 1)   
                            ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                            ->leftJoin('categories' , 'categories.id' , '=' , 'video_tapes.category_id') 
                            ->where('video_tapes.status' , 1)
                            ->where('video_tapes.publish_status' , 1)
                            ->orderby('video_tapes.watch_count' , 'desc')
                            ->where('categories.status', CATEGORY_APPROVE_STATUS)
                            ->videoResponse();

        if ($request->id) {

            // Check any flagged videos are present

            $flag_videos = flag_videos($request->id);

            if($flag_videos) {

                $base_query->whereNotIn('video_tapes.id',$flag_videos);

            }
        
        }

        $base_query->where('video_tapes.age_limit','<=', checkAge($request));

        $videos = $base_query->skip($request->skip)->take(Setting::get('admin_take_count' ,12))->get();

        if(count($videos) > 0) {

            foreach ($videos as $key => $value) {

                $data[] = displayVideoDetails($value, $request->id);
                
            }
        }

        $response_array = array('success' => true , 'data' => $data);

        return response()->json($response_array , 200);

    }


    public function get_channel_videos(Request $request) {

        $validator = Validator::make(
            $request->all(),
            array(
                'channel_id' => 'required|integer|exists:channels,id',
            ),
            array(
                'exists' => 'The :attribute doesn\'t exists',
            )
        );

        if ($validator->fails()) {
            $error_messages = implode(',', $validator->messages()->all());
            $response_array = array('success' => false, 'error' => Helper::get_error_message(101), 'error_code' => 101, 'error_messages'=>$error_messages);

        } else {

            $data = array();

            $channels = Channel::where('status', 1)->where('id', $request->channel_id)->first();

            if($channels) {

                $videos = VideoRepo::channelVideos($request, $channels->id, '', $request->skip);

                if(count($videos) > 0) {

                    $data = $videos;
                }

                $channel_status = DEFAULT_FALSE;

                if($request->id) {

                    $channel_status = check_channel_status($request->id, $channels->id);

                }

                $subscriberscnt = subscriberscnt($channels->id);
                
            }

            $is_mychannel = DEFAULT_FALSE;

            $my_channel = Channel::where('user_id', $request->id)->where('id', $request->channel_id)->first();

            if ($my_channel) {

                $is_mychannel = DEFAULT_TRUE;

            }

            $response_array = array('success' => true, 'channel_id'=>$channels->id, 
                        'channel_name'=>$channels->name, 'channel_image'=>$channels->picture,
                        'channel_cover'=>$channels->cover, 
                        'channel_description'=>$channels->description,
                        'is_subscribed'=>$channel_status,
                        'subscribers_count'=>$subscriberscnt,
                        'is_mychannel'=>$is_mychannel,
                        'data' => $data);
        }

        $response = response()->json($response_array, 200);

        return $response;

    }

    /**
     * Function single_video()
     *
     * Return particular video details 
     *
     */

    public function single_video(Request $request) {

        $validator = Validator::make(
            $request->all(),
            array(
                'video_tape_id' => 'required|integer|exists:video_tapes,id',
            ),
            array(
                'exists' => 'The :attribute doesn\'t exists',
            )
        );

        if ($validator->fails()) {

            $error = implode(',', $validator->messages()->all());

            $response_array = array('success' => false, 'error_messages' => $error, 'error_code' => 101);

        } else {

            $login_by = $request->login_by ? $request->login_by : 'android';

            $data = array();

            // Check the video is in flg lists

            $check_flag_video = Flag::where('video_tape_id' , $request->video_tape_id)->where('user_id' ,$request->id)->count();

            if(!$check_flag_video) {

                $data = VideoRepo::single_response($request->video_tape_id , $request->id , $login_by);

                if(count($data) > 0) {

                    if($data['is_approved'] == ADMIN_VIDEO_DECLINED_STATUS || $data['status'] == USER_VIDEO_DECLINED_STATUS || $data['channel_approved_status'] == ADMIN_CHANNEL_DECLINED || $data['channel_status'] == USER_CHANNEL_DECLINED) {

                        return response()->json(['success'=>false, 'error_messages'=>tr('video_is_declined')]);

                    }

                    // Video if not published

                    if ($data['publish_status'] != PUBLISH_NOW) {

                        return response()->json(['success'=>false, 'error_messages'=>tr('video_not_yet_publish')]);
                    }


                    // Comments Section

                    $comments = [];

                    if($comments = Helper::video_ratings($request->video_tape_id,0)) {

                        $comments = $comments->toArray();

                    }

                    $data['comments'] = $comments;

                    $data['suggestions'] = VideoRepo::suggestions($request);
                    
                    $response_array = ['success' => true , 'data' => $data];

                } else {
                    $response_array = ['success' => false , 'error_messages' => Helper::get_error_message(1001) , 'error_code' => 1001];
                }

            } else {

                $response_array = ['success' => false , 'error_messages' => Helper::get_error_message(1000) ,  'error_code' => 1000];
            }

        }

        return response()->json($response_array, 200);

    }

    public function search_video(Request $request) {

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

            $error = implode(',', $validator->messages()->all());
            $response_array = array('success' => false, 'error_messages' => $error, 'error_code' => 101);

        } else {


            $data = [];

            $base_query = VideoTape::where('video_tapes.is_approved' , 1)   
                                ->videoResponse()
                                ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                                ->leftJoin('categories' , 'categories.id' , '=' , 'video_tapes.category_id')
                                ->where('video_tapes.status' , 1)
                                ->where('video_tapes.publish_status' , 1)
                                ->where('channels.is_approved', 1)
                                ->where('channels.status', 1)
                                ->where('categories.status', CATEGORY_APPROVE_STATUS)
                                ->where('title', 'like', "%".$request->key."%")
                                ->orderby('video_tapes.watch_count' , 'desc');
                               //  ->select('video_tapes.id as video_tape_id' , 'video_tapes.title');
            $user_details = '';


            

            if ($request->id) {

                // Check any flagged videos are present

                $flag_videos = flag_videos($request->id);

                if($flag_videos) {

                    $base_query->whereNotIn('video_tapes.id',$flag_videos);

                }

                $user_details = User::find($request->id);

            
            }

            $base_query->where('video_tapes.age_limit','<=', checkAge($request));

            $data = $base_query->skip($request->skip)->take(Setting::get('admin_take_count' ,12))->get();

            $items = [];


            if (count($data) > 0) {

                foreach ($data as $key => $value) {

                    $is_ppv_status = DEFAULT_TRUE;

                    if ($request->id) {

                        $is_ppv_status = ($value->type_of_user == NORMAL_USER || $value->type_of_user == BOTH_USERS) ? ( ( $user_details->user_type == 0 ) ? DEFAULT_TRUE : DEFAULT_FALSE ) : DEFAULT_FALSE; 

                    }
                   
                    $currency = Setting::get('currency');

                    $is_ppv_subscribe_page = $is_ppv_status;

                    $pay_per_view_status = VideoRepo::pay_per_views_status_check($user_details ? $user_details->id : '', $user_details ? $user_details->user_type : '', $value)->getData()->success;

                    $amount = $value->ppv_amount;

                    $ppv_notes = !$pay_per_view_status ? ($value->type_of_user == 1 ? tr('normal_user_note') : tr('paid_user_note')) : ''; 

                    $items[] = [
                        'video_tape_id'=>$value->video_tape_id,
                            'title'=>$value->title,
                            'currency'=>$currency,
                            'is_ppv_subscribe_page'=>$is_ppv_subscribe_page,
                            'pay_per_view_status'=>$pay_per_view_status,
                            'ppv_amount'=>$amount,
                            'ppv_notes'=>$ppv_notes
                            ];


                }

            }


            $response_array = array('success' => true, 'data' => $items);
        }

        return response()->json($response_array, 200);

    }

    public function privacy(Request $request) {

        $page_data['type'] = $page_data['heading'] = $page_data['content'] = "";

        $page = Page::where('type', 'privacy')->first();

        if($page) {

            $page_data['type'] = "privacy";
            $page_data['heading'] = $page->heading;
            $page_data ['content'] = $page->description;
        }

        $response_array = array('success' => true , 'page' => $page_data);

        return response()->json($response_array,200);

    }

    public function about(Request $request) {

        $page_data['type'] = $page_data['heading'] = $page_data['content'] = "";

        $page = Page::where('type', 'about')->first();

        if($page) {

            $page_data['type'] = 'about';

            $page_data['heading'] = $page->heading;

            $page_data ['content'] = $page->description;
        }

        $response_array = array('success' => true , 'page' => $page_data);
        return response()->json($response_array,200);

    }

    public function terms(Request $request) {

        $page_data['type'] = $page_data['heading'] = $page_data['content'] = "";

        $page = Page::where('type', 'terms')->first();

        if($page) {

            $page_data['type'] = "Terms";

            $page_data['heading'] = $page->heading;

            $page_data ['content'] = $page->description;
        }

        $response_array = array('success' => true , 'page' => $page_data);
        return response()->json($response_array,200);

    }

    public function help(Request $request) {

        $page_data['type'] = $page_data['heading'] = $page_data['content'] = "";

        $page = Page::where('type', 'help')->first();

        if($page) {

            $page_data['type'] = "help";

            $page_data['heading'] = $page->heading;

            $page_data ['content'] = $page->description;
        }

        $response_array = ['success' => true , 'page' => $page_data];

        return response()->json($response_array,200);

    }

    public function settings(Request $request) {

        $validator = Validator::make(
            $request->all(),
            array(
                'status' => 'required',
            )
        );

        if ($validator->fails()) {

            $error_messages = implode(',', $validator->messages()->all());
            $response_array = array('success' => false, 'error' => Helper::get_error_message(101), 'error_code' => 101, 'error_messages'=>$error_messages);

        } else {

            $user = User::find($request->id);
            $user->push_status = $request->status;
            $user->save();

            if($request->status) {
                $message = tr('push_notification_enable');
            } else {
                $message = tr('push_notification_disable');
            }

            $response_array = array('success' => true, 'message' => $message , 'push_status' => $user->push_status, 'data'=>['id'=>$user->id, 'token'=>$user->token]);
        }

        $response = response()->json($response_array, 200);
        return $response;
   
    }


    /** 
     *  Provider Send Redeem request to Admin
     *
     */

    public function send_redeem_request(Request $request) {

        if(Setting::get('redeem_control') == REDEEM_OPTION_ENABLED) {

            //  Get admin configured - Minimum Provider Credit

            $minimum_redeem = Setting::get('minimum_redeem' , 1);

            // Get Provider Remaining Credits 

            $redeem_details = Redeem::where('user_id' , $request->id)->first();

            if($redeem_details) {

                $remaining = $redeem_details->remaining;

                // check the provider have more than minimum credits

                if($remaining > $minimum_redeem) {

                    $redeem_amount = abs(intval($remaining - $minimum_redeem));

                    // Check the redeems is not empty

                    if($redeem_amount) {

                        // Save Redeem Request

                        $redeem_request = new RedeemRequest;

                        $redeem_request->user_id = $request->id;

                        $redeem_request->request_amount = $redeem_amount;

                        $redeem_request->status = false;

                        $redeem_request->save();

                        // Update Redeems details 

                        $redeem_details->remaining = abs($redeem_details->remaining-$redeem_amount);

                        $redeem_details->save();


                        $response_array = ['success' => true];

                    } else {

                        $response_array = ['success' => false , 'error_messages' => Helper::get_error_message(149) , 'error_code' => 149];
                    }

                } else {
                    $response_array = ['success' => false , 'error_messages' => Helper::get_error_message(148) ,'error_code' => 148];
                }

            } else {
                $response_array = ['success' => false , 'error_messages' => Helper::get_error_message(151) , 'error_code' => 151];
            }
        } else {
            $response_array = ['success' => false , 'error_messages' => Helper::get_error_message(147) , 'error_code' => 147];
        }

        return response()->json($response_array , 200);

    }

    /**
     * Get redeem requests
     * 
     *
     */

    public function redeems(Request $request) {

        if(Setting::get('redeem_control') == REDEEM_OPTION_ENABLED) {

            $data = Redeem::where('user_id' , $request->id)->select('total' , 'paid' , 'remaining' , 'status')->get()->toArray();

            $response_array = ['success' => true , 'data' => $data];

        } else {
            $response_array = ['success' => false , 'error_messages' => Helper::get_error_message(147) , 'error_code' => 147];
        }

        return response()->json($response_array , 200);
    
    }

    public function redeem_request_cancel(Request $request) {

        $validator = Validator::make($request->all() , [
            'redeem_request_id' => 'required|exists:redeem_requests,id,user_id,'.$request->id,
            ]);

         if ($validator->fails()) {
            $error_messages = implode(',', $validator->messages()->all());
            $response_array = array('success' => false, 'error' => Helper::get_error_message(101), 'error_code' => 101, 'error_messages'=>$error_messages);

        } else {

            if($redeem_details = Redeem::where('user_id' , $request->id)->first()) {

                if($redeem_request_details = RedeemRequest::find($request->redeem_request_id)) {

                    // Check status to cancel the redeem request

                    if(in_array($redeem_request_details->status, [REDEEM_REQUEST_SENT , REDEEM_REQUEST_PROCESSING])) {
                        // Update the redeeem 

                        $redeem_details->remaining = $redeem_details->remaining + abs($redeem_request_details->request_amount);

                        $redeem_details->save();

                        // Update the redeeem request Status

                        $redeem_request_details->status = REDEEM_REQUEST_CANCEL;

                        $redeem_request_details->save();

                        $response_array = ['success' => true];


                    } else {
                        $response_array = ['success' => false ,  'error_messages' => Helper::get_error_message(150) , 'error_code' => 150];
                    }

                } else {
                    $response_array = ['success' => false ,  'error_messages' => Helper::get_error_message(151) , 'error_code' => 151];
                }

            } else {

                $response_array = ['success' => false ,  'error_messages' => Helper::get_error_message(151) , 'error_code' =>151 ];
            }

        }

        return response()->json($response_array , 200);

    }


    /**
     * Function Name : redeem_request_list()
     * 
     * List of redeem requests based on logged in user id 
     *
     * @param object $request - User id ,token
     *
     * @return redeem list wih boolean response
     */
    public function redeem_request_list(Request $request) {

        $currency = Setting::get('currency');

        $model = RedeemRequest::where('user_id' , $request->id)
                ->select('request_amount' , 
                     DB::raw("'$currency' as currency"),
                     DB::raw('DATE_FORMAT(created_at , "%e %b %y") as requested_date'),
                     'paid_amount',
                     DB::raw('DATE_FORMAT(updated_at , "%e %b %y") as paid_date'),
                     'status',
                     'id as redeem_request_id'
                 )
                ->orderBy('created_at', 'desc')
                ->get();

        $redeem_details = Redeem::where('user_id' , $request->id)
                ->select('total' , 'paid' , 'remaining' , 'status', DB::raw("'$currency' as currency"))
                ->first();

        if(!$redeem_details) {

            // To avoid <null> value (http://prntscr.com/jm33cq), created dummy object with empty values

            $redeem_details = new Redeem;

            $redeem_details->total = $redeem_details->paid = $redeem_details->remaining = 0;

            $redeem_details->status = 0;
            
            $redeem_details->currency = $currency;

            // NO NEED TO SAVE THE DETAILS
        }

        $data = [];

        foreach ($model as $key => $value) {

            $redeem_status = redeem_request_status($value->status);

            $redeem_cancel_status = in_array($value->status, [REDEEM_REQUEST_SENT , REDEEM_REQUEST_PROCESSING]) ? 1 : 0;
            
            $data[] = [
                    'redeem_request_id'=>$value->redeem_request_id,
                    'request_amount' => $value->request_amount,
                      'redeem_status'=>$redeem_status,
                      'currency'=>$value->currency,
                      'requested_date'=>$value->requested_date,
                      'paid_amount'=>$value->paid_amount,
                      'paid_date'=>$value->paid_date,
                      'redeem_cancel_status'=>$redeem_cancel_status,
                      'status'=>$value->status
            ];

        }

        $response_array = ['success' => true , 'data' => $data, 'redeem_amount'=> $redeem_details];

        return response()->json($response_array , 200);
    
    }
   

    public function user_channel_list(Request $request) {

        $age = 0;

        $channel_id = [];

        $query = Channel::select('channels.*', 'video_tapes.id as video_tape_id', 'video_tapes.is_approved',
                    'video_tapes.status', 'video_tapes.channel_id')
                ->leftJoin('video_tapes', 'video_tapes.channel_id', '=', 'channels.id')
                // ->where('channels.status', DEFAULT_TRUE)
                ->groupBy('channels.id')
                ->where('channels.user_id',$request->id);

        /*

        where('channels.is_approved', DEFAULT_TRUE)

        if($request->id) {

            $user = User::find($request->id);

            $age = $user->age_limit;

            $age = $age ? ($age >= Setting::get('age_limit') ? 1 : 0) : 0;

            if ($request->id) {

                $channel_id = ChannelSubscription::where('user_id', $request->id)->pluck('channel_id')->toArray();

                $query->whereIn('channels.id', $channel_id);
            }


            $query->where('video_tapes.age_limit','<=', $age);

        }*/

        if ($request->device_type == DEVICE_ANDROID || $request->device_type == DEVICE_IOS) {

            $channels = $query->skip($request->skip)->take(Setting::get('admin_take_count', 12))
                ->get();


        } else {

            $channels = $query->paginate(16);

            $items = $channels->items();

        }   

        $lists = [];

        foreach ($channels as $key => $value) {
            $lists[] = ['channel_id'=>$value->id, 
                    'user_id'=>$value->user_id,
                    'picture'=> $value->picture, 
                    'title'=>$value->name,
                    'description'=>$value->description, 
                    'created_at'=>$value->created_at->diffForHumans(),
                    'no_of_videos'=>videos_count($value->id, MY_CHANNEL),
                    'subscribe_status'=>$request->id ? check_channel_status($request->id, $value->id) : '',
                    'no_of_subscribers'=>$value->getChannelSubscribers()->count(),
            ];

        }

        if ($request->device_type == DEVICE_ANDROID || $request->device_type == DEVICE_IOS) {

            $response_array = ['success'=>true, 'data'=>$lists];

        } else {

            $pagination = (string) $channels->links();

            $response_array = ['success'=>true, 'channels'=>$lists, 'pagination'=>$pagination];
        }

        return response()->json($response_array);
    }

    /**
     * Like Videos
     *
     * @return JSON Response
     */

    public function likevideo(Request $request) {

        $validator = Validator::make($request->all() , [
            'video_tape_id' => 'required|exists:video_tapes,id',
        ]);

        if ($validator->fails()) {
            
            $error_messages = implode(',', $validator->messages()->all());
            
            $response_array = array('success' => false, 'error' => Helper::get_error_message(101), 
                    'error_code' => 101, 'error_messages'=>$error_messages);

        } else {

            $model = LikeDislikeVideo::where('video_tape_id', $request->video_tape_id)
                    ->where('user_id',$request->id)->first();

            $like_count = LikeDislikeVideo::where('video_tape_id', $request->video_tape_id)
                ->where('like_status', DEFAULT_TRUE)
                ->count();

            $dislike_count = LikeDislikeVideo::where('video_tape_id', $request->video_tape_id)
                ->where('dislike_status', DEFAULT_TRUE)
                ->count();

            if (!$model) {

                $model = new LikeDislikeVideo;

                $model->video_tape_id = $request->video_tape_id;

                $model->user_id = $request->id;

                $model->like_status = DEFAULT_TRUE;

                $model->dislike_status = DEFAULT_FALSE;

                $model->save();

                $response_array = ['success'=>true, 'like_count'=>number_format_short($like_count+1), 'dislike_count'=>number_format_short($dislike_count)];

            } else {

                if($model->dislike_status) {

                    $model->like_status = DEFAULT_TRUE;

                    $model->dislike_status = DEFAULT_FALSE;

                    $model->save();

                    $response_array = ['success'=>true, 'like_count'=>number_format_short($like_count+1), 'dislike_count'=>number_format_short($dislike_count-1)];


                } else {

                    $model->delete();

                    $response_array = ['success'=>true, 'like_count'=>number_format_short($like_count-1), 'dislike_count'=>number_format_short($dislike_count)];

                }

            }

        }

        return response()->json($response_array);

    }

    /**
     * Dis Like Videos
     *
     * @return JSON Response
     */

    public function dislikevideo(Request $request) {

        $validator = Validator::make($request->all() , [
            'video_tape_id' => 'required|exists:video_tapes,id',
        ]);

        if ($validator->fails()) {
            
            $error_messages = implode(',', $validator->messages()->all());
            
            $response_array = array('success' => false, 'error' => Helper::get_error_message(101), 
                    'error_code' => 101, 'error_messages'=>$error_messages);

        } else {

            $model = LikeDislikeVideo::where('video_tape_id', $request->video_tape_id)
                    ->where('user_id',$request->id)->first();

            $like_count = LikeDislikeVideo::where('video_tape_id', $request->video_tape_id)
                ->where('like_status', DEFAULT_TRUE)
                ->count();

            $dislike_count = LikeDislikeVideo::where('video_tape_id', $request->video_tape_id)
                ->where('dislike_status', DEFAULT_TRUE)
                ->count();

            if (!$model) {

                $model = new LikeDislikeVideo;

                $model->video_tape_id = $request->video_tape_id;

                $model->user_id = $request->id;

                $model->like_status = DEFAULT_FALSE;

                $model->dislike_status = DEFAULT_TRUE;

                $model->save();

                $response_array = ['success'=>true, 'like_count'=>number_format_short($like_count), 'dislike_count'=>number_format_short($dislike_count+1)];

            } else {

                if($model->like_status) {

                    $model->like_status = DEFAULT_FALSE;

                    $model->dislike_status = DEFAULT_TRUE;

                    $model->save();

                    $response_array = ['success'=>true, 'like_count'=>number_format_short($like_count-1), 'dislike_count'=>number_format_short($dislike_count+1)];

                } else {

                    $model->delete();

                    $response_array = ['success'=>true, 'like_count'=>number_format_short($like_count), 'dislike_count'=>number_format_short($dislike_count-1)];

                }

            }

        }

        return response()->json($response_array);

    }

    public function default_card(Request $request) {

        $validator = Validator::make(
            $request->all(),
            array(
                'card_id' => 'required|integer|exists:cards,id,user_id,'.$request->id,
            ),
            array(
                'exists' => 'The :attribute doesn\'t belong to user:'.$request->id
            )
        );

        if($validator->fails()) {

            $error_messages = implode(',', $validator->messages()->all());
            $response_array = array('success' => false, 'error_messages' => $error_messages, 'error_code' => 101);

        } else {

            $user = User::find($request->id);
            
            $old_default = Card::where('user_id' , $request->id)->where('is_default', DEFAULT_TRUE)->update(array('is_default' => DEFAULT_FALSE));

            $card = Card::where('id' , $request->card_id)->update(array('is_default' => DEFAULT_TRUE));

            if($card) {

                if($user) {
                    $user->card_id = $request->card_id;
                    $user->save();
                }

                $response_array = Helper::null_safe(array('success' => true, 'data'=>['id'=>$request->id,'token'=>$user->token]));

            } else {
                $response_array = array('success' => false , 'error_messages' => tr('something_error'));
            }
        }
        return response()->json($response_array , 200);
    
    }

    public function delete_card(Request $request) {
    
        $card_id = $request->card_id;

        $validator = Validator::make(
            $request->all(),
            array(
                'card_id' => 'required|integer|exists:cards,id,user_id,'.$request->id,
            ),
            array(
                'exists' => 'The :attribute doesn\'t belong to user:'.$request->id
            )
        );

        if ($validator->fails()) {
            
            $error_messages = implode(',', $validator->messages()->all());
            
            $response_array = array('success' => false , 'error_messages' => $error_messages , 'error_code' => 101);
        
        } else {

            $user = User::find($request->id);

            if ($user->card_id == $card_id) {

                $response_array = array('success' => false, 'error_messages'=> tr('card_default_error'));

            } else {

                Card::where('id',$card_id)->delete();

                if($user) {

                    // if($user->payment_mode = CARD) {

                        // Check he added any other card
                        
                        if($check_card = Card::where('user_id' , $request->id)->first()) {

                            $check_card->is_default =  DEFAULT_TRUE;

                            $user->card_id = $check_card->id;

                            $check_card->save();

                        } else { 

                            $user->payment_mode = COD;
                            $user->card_id = DEFAULT_FALSE;
                        }
                    // }
                    
                    $user->save();
                }

                $response_array = array('success' => true, 'message'=>tr('card_deleted'), 'data'=> ['id'=>$request->id,'token'=>$user->token]);

            }
            
        }
    
        return response()->json($response_array , 200);
    }

    public function subscription_plans(Request $request) {

        $query = Subscription::select('id as subscription_id',
                'title', 'description', 'plan','amount', 'status', 'created_at' , DB::raw("'$' as currency"))
                ->where('status' , DEFAULT_TRUE);

        if ($request->id) {

            $user = User::find($request->id);

            if ($user) {

               if ($user->zero_subscription_status == DEFAULT_TRUE) {

                   $query->where('amount','>', 0);

               }

            } 

        }

        $model = $query->orderBy('amount' , 'asc')->get();

        $response_array = ['success'=>true, 'data'=>$model];

        return response()->json($response_array, 200);

    }

    public function pay_now(Request $request) {

        Log::info("Pay Now");
        
        try {

            DB::beginTransaction();

            $validator = Validator::make(
                $request->all(),
                array(
                    'subscription_id'=>'required|exists:subscriptions,id',
                    'payment_id'=>'required',
                    'coupon_code'=>'exists:coupons,coupon_code',
                ), array(
                    'coupon_code.exists' => tr('coupon_code_not_exists'),
                    'subscription_id.exists' => tr('subscription_not_exists'),
            ));

            if ($validator->fails()) {
                // Error messages added in response for debugging
                $errors = implode(',',$validator->messages()->all());

                throw new Exception($errors, 101);

            } else {

                $user = User::find($request->id);

                $subscription = Subscription::find($request->subscription_id);

                $total = $subscription->amount;

                $coupon_amount = 0;

                $coupon_reason = '';

                $is_coupon_applied = COUPON_NOT_APPLIED;

                if ($request->coupon_code) {

                    $coupon = Coupon::where('coupon_code', $request->coupon_code)->first();

                    if ($coupon) {
                        
                        if ($coupon->status == COUPON_INACTIVE) {

                            $coupon_reason = tr('coupon_inactive_reason');

                        } else {

                            $check_coupon = $this->check_coupon_applicable_to_user($user, $coupon)->getData();

                            if ($check_coupon->success) {

                                $is_coupon_applied = COUPON_APPLIED;

                                $amount_convertion = $coupon->amount;

                                if ($coupon->amount_type == PERCENTAGE) {

                                    $amount_convertion = amount_convertion($coupon->amount, $subscription->amount);

                                }


                                if ($amount_convertion < $subscription->amount) {

                                    $total = $subscription->amount - $amount_convertion;

                                    $coupon_amount = $amount_convertion;

                                } else {

                                    // throw new Exception(Helper::get_error_message(156),156);

                                    $total = 0;

                                    $coupon_amount = $amount_convertion;
                                    
                                }

                                // Create user applied coupon

                                if($check_coupon->code == 2002) {

                                    $user_coupon = UserCoupon::where('user_id', $user->id)
                                            ->where('coupon_code', $request->coupon_code)
                                            ->first();

                                    // If user coupon not exists, create a new row

                                    if ($user_coupon) {

                                        if ($user_coupon->no_of_times_used < $coupon->per_users_limit) {

                                            $user_coupon->no_of_times_used += 1;

                                            $user_coupon->save();

                                        }

                                    }

                                } else {

                                    $user_coupon = new UserCoupon;

                                    $user_coupon->user_id = $user->id;

                                    $user_coupon->coupon_code = $request->coupon_code;

                                    $user_coupon->no_of_times_used = 1;

                                    $user_coupon->save();

                                }

                            } else {

                                $coupon_reason = $check_coupon->error_messages;
                                
                            }

                        }

                    } else {

                        $coupon_reason = tr('coupon_delete_reason');
                    }
                }

                $model = UserPayment::where('user_id' , $request->id)
                            ->where('status', DEFAULT_TRUE)
                            ->orderBy('id', 'desc')->first();

                $user_payment = new UserPayment();

                if ($model) {

                    if (strtotime($model->expiry_date) >= strtotime(date('Y-m-d H:i:s'))) {

                         $user_payment->expiry_date = date('Y-m-d H:i:s', strtotime("+{$subscription->plan} months", strtotime($model->expiry_date)));

                    } else {

                        $user_payment->expiry_date = date('Y-m-d H:i:s',strtotime("+{$subscription->plan} months"));

                    }

                } else {

                    $user_payment->expiry_date = date('Y-m-d H:i:s',strtotime("+{$subscription->plan} months"));

                }

                $user_payment->payment_id  = $request->payment_id;
                $user_payment->user_id = $request->id;
                $user_payment->subscription_id = $request->subscription_id;

                $user_payment->status = PAID_STATUS;

                $user_payment->payment_mode = PAYPAL;

                // Coupon details

                $user_payment->is_coupon_applied = $is_coupon_applied;

                $user_payment->coupon_code = $request->coupon_code  ? $request->coupon_code  :'';

                $user_payment->coupon_amount = $coupon_amount;

                $user_payment->subscription_amount = $subscription->amount;

                $user_payment->amount = $total;

                $user_payment->coupon_reason = $is_coupon_applied == COUPON_APPLIED ? '' : $coupon_reason;
 
                if($user_payment->save()) {

                    if ($user) {

                        $user->user_type = 1;

                        /*$user->amount_paid += $total;*/

                        $user->expiry_date = $user_payment->expiry_date;

                        /*$now = time(); // or your date as well

                        $end_date = strtotime($user->expiry_date);

                        $datediff =  $end_date - $now;

                        $user->no_of_days = ($user->expiry_date) ? floor($datediff / (60 * 60 * 24)) + 1 : 0;*/

                        if ($user_payment->amount <= 0) {

                            $user->zero_subscription_status = 1;
                        }

                        if ($user->save()) {

                            $response_array = ['success'=>true, 
                                    'message'=>tr('payment_success'), 
                                    'data'=>[
                                        'id'=>$request->id,
                                        'token'=>$user_payment->user ? $user_payment->user->token : '',
                                ]];

                        } else {


                            throw new Exception(tr('user_details_not_saved'));
                            
                        }

                    } else {

                        throw new Exception(tr('user_not_found'));
                        
                    }
                }

            }

            DB::commit();

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            DB::rollback();

            $message = $e->getMessage();

            $code = $e->getCode();

            $response_array = ['success'=>false, 'error_messages'=>$message, 'error_code'=>$code];

            return response()->json($response_array);

        }

    }

    public function subscribedPlans(Request $request){

        $validator = Validator::make(
            $request->all(),
            array(
                'skip'=>($request->device_type == DEVICE_WEB) ? '' : 'required|numeric',
            ));

        if ($validator->fails()) {

            // Error messages added in response for debugging
            
            $errors = implode(',',$validator->messages()->all());

            $response_array = ['success' => false,'error_messages' => $errors,'error_code' => 101];

        } else {

            $query = UserPayment::where('user_id' , $request->id)
                        ->leftJoin('subscriptions', 'subscriptions.id', '=', 'subscription_id')
                        ->select('user_id as id',
                                'subscription_id',
                                'user_payments.id as user_subscription_id',
                                'subscriptions.title as title',
                                'subscriptions.description as description',
                                'subscriptions.plan',
                                'subscriptions.amount as current_subscription_amount',
                                'user_payments.amount as amount',
                                'user_payments.status as status',
                                // 'user_payments.expiry_date as expiry_date',
                                \DB::raw('DATE_FORMAT(user_payments.expiry_date , "%e %b %Y") as expiry_date'),
                                'user_payments.created_at as created_at',
                                DB::raw("'$' as currency"),
                                'user_payments.payment_mode',
                                'user_payments.is_coupon_applied',
                                'user_payments.coupon_code',
                                'user_payments.coupon_amount',
                                'user_payments.subscription_amount',
                                'user_payments.coupon_reason',
                                'user_payments.is_cancelled',
                                'user_payments.payment_id',
                                'user_payments.cancel_reason')
                        ->orderBy('user_payments.updated_at', 'desc');
                        
            if ($request->device_type == DEVICE_WEB) {

                $model = $query->paginate(16);

                $response_array = array('success'=>true, 'data' => $model->items(), 'pagination' => (string) $model->links());

            } else {

                $model = $query->skip($request->skip)
                        ->take(Setting::get('admin_take_count' ,12))
                        ->get();

                $data = [];

                foreach ($model as $key => $value) { 

                    $data[] = [

                        'id'=>$value->id,
                        'subscription_id'=>$value->subscription_id,
                        'user_subscription_id'=>$value->user_subscription_id,
                        'title'=>$value->title,
                        'description'=>$value->description,
                        'plan'=>$value->plan,
                        'amount'=>$value->amount,
                        'status'=>$value->status,
                        'expiry_date'=>$value->expiry_date,
                        'created_at'=>$value->created_at->diffForHumans(),
                        'currency'=>$value->currency,
                        'payment_mode'=>$value->payment_mode,
                        'is_coupon_applied'=>$value->is_coupon_applied,
                        'coupon_code'=>$value->coupon_code,
                        'coupon_amount'=>$value->coupon_amount,
                        'subscription_amount'=>$value->subscription_amount,
                        'coupon_reason'=>$value->coupon_reason,
                        'is_cancelled'=>$value->is_cancelled,
                        'payment_id'=>$value->payment_id,
                        'cancel_reason'=>$value->cancel_reason,
                        'active_plan'=>($key == 0 && $value->status) ? ACTIVE_PLAN : NOT_ACTIVE_PLAN,
                    ];


                }

                $response_array = ['success'=>true, 'data'=>$data];
            }

        }

        return response()->json($response_array);

    }


    public function card_details(Request $request) {

        $cards = Card::select('user_id as id','id as card_id','customer_id',
                'last_four', 'card_token', 'is_default', 
            \DB::raw('DATE_FORMAT(created_at , "%e %b %y") as created_date'))->where('user_id', $request->id)->get();

        $response_array = ['success'=>true, 'data'=>$cards];

        return response()->json($response_array, 200);
    }

    /**
     * Show the payment methods.
     *
     * @return \Illuminate\Http\Response
     */
    public function payment_card_add(Request $request) {

        $validator = Validator::make($request->all(), 
            array(
                'card_token'=> 'required',
                'card_holder_name'=>'',
            )
            );

        if($validator->fails()) {

            $errors = implode(',', $validator->messages()->all());
            
            $response_array = ['success' => false, 'error_messages' => $errors, 'error_code' => 101];

            return response()->json($response_array);

        } else {

            $userModel = User::find($request->id);

            $last_four = substr($request->number, -4);

            $stripe_secret_key = Setting::get('stripe_secret_key');

            $response = json_decode('{}');

            if($stripe_secret_key) {

                \Stripe\Stripe::setApiKey($stripe_secret_key);

            } else {

                $response_array = ['success'=>false, 'error_messages'=>tr('add_card_is_not_enabled')];

                return response()->json($response_array);
            }

            try {

                // Get the key from settings table
                
                $customer = \Stripe\Customer::create([
                        "card" => $request->card_token,
                        "email" => $userModel->email
                    ]);

                if($customer) {

                    $customer_id = $customer->id;

                    $cards = new Card;
                    $cards->user_id = $userModel->id;
                    $cards->customer_id = $customer_id;
                    $cards->last_four = $last_four;
                    $cards->card_token = $customer->sources->data ? $customer->sources->data[0]->id : "";

                    $cards->cvv = $request->cvv;

                    $cards->card_name = $request->card_holder_name;

                    $cards->month = $request->month;

                    $cards->year = $request->year;

                    // Check is any default is available
                    $check_card = Card::where('user_id', $userModel->id)->first();

                    if($check_card)
                        $cards->is_default = 0;
                    else
                        $cards->is_default = 1;
                    
                    $cards->save();

                    if($userModel && $cards->is_default) {

                        $userModel->payment_mode = 'card';

                        $userModel->card_id = $cards->id;

                        $userModel->save();
                    }

                    $data = [
                            'user_id'=>$request->id, 
                            'id'=>$request->id, 
                            'token'=>$userModel->token,
                            'card_id'=>$cards->id,
                            'customer_id'=>$cards->customer_id,
                            'last_four'=>$cards->last_four, 
                            'card_token'=>$cards->card_token, 
                            'is_default'=>$cards->is_default
                            ];

                    $response_array = array('success' => true,'message'=>tr('add_card_success'), 
                        'data'=> $data);

                    return response()->json($response_array);

                } else {

                    $response_array = ['success'=>false, 'error_messages'=>tr('Could not create client ID')];

                    return response()->json($response_array);

                }
            
            } catch(Exception $e) {

                $response_array = ['success'=>false, 'error_messages'=>$e->getMessage()];

                return response()->json($response_array);

            }

        }

    }    


    public function my_channels(Request $request) {

       $model = Channel::select('id as channel_id', 'name as channel_name')->where('is_approved', DEFAULT_TRUE)->where('status', DEFAULT_TRUE)
            ->where('user_id', $request->id)->get();

        if($model) {

            $response_array = array('success' => true , 'data' => $model);

        } else {
            $response_array = array('success' => false,'error_messages' => Helper::get_error_message(135),'error_code' => 135);
        }

        $response = response()->json($response_array, 200);
        
        return $response;
    }

    public function stripe_payment(Request $request) {

        try {

            DB::beginTransaction();

            $validator = Validator::make($request->all(), 
                array(
                    'subscription_id' => 'required|exists:subscriptions,id',
                    'coupon_code'=>'exists:coupons,coupon_code',
                ), array(
                    'coupon_code.exists' => tr('coupon_code_not_exists'),
                    'subscription_id.exists' => tr('subscription_not_exists'),
            ));

            if($validator->fails()) {

                $error_messages = implode(',', $validator->messages()->all());

                throw new Exception($error_messages, 101);

            } else {

                $subscription = Subscription::find($request->subscription_id);

                $user = User::find($request->id);

                if ($subscription) {

                    $total = $subscription->amount;

                    $coupon_amount = 0;

                    $coupon_reason = '';

                    $is_coupon_applied = COUPON_NOT_APPLIED;

                    if ($request->coupon_code) {

                        $coupon = Coupon::where('coupon_code', $request->coupon_code)->first();

                        if ($coupon) {
                            
                            if ($coupon->status == COUPON_INACTIVE) {

                                $coupon_reason = tr('coupon_inactive_reason');

                            } else {

                                $check_coupon = $this->check_coupon_applicable_to_user($user, $coupon)->getData();

                                if ($check_coupon->success) {

                                    $is_coupon_applied = COUPON_APPLIED;

                                    $amount_convertion = $coupon->amount;

                                    if ($coupon->amount_type == PERCENTAGE) {

                                        $amount_convertion = amount_convertion($coupon->amount, $subscription->amount);

                                    }


                                    if ($amount_convertion < $subscription->amount) {

                                        $total = $subscription->amount - $amount_convertion;

                                        $coupon_amount = $amount_convertion;

                                    } else {

                                        // throw new Exception(Helper::get_error_message(156),156);

                                        $total = 0;

                                        $coupon_amount = $amount_convertion;
                                        
                                    }

                                    // Create user applied coupon

                                    if($check_coupon->code == 2002) {

                                        $user_coupon = UserCoupon::where('user_id', $user->id)
                                                ->where('coupon_code', $request->coupon_code)
                                                ->first();

                                        // If user coupon not exists, create a new row

                                        if ($user_coupon) {

                                            if ($user_coupon->no_of_times_used < $coupon->per_users_limit) {

                                                $user_coupon->no_of_times_used += 1;

                                                $user_coupon->save();

                                            }

                                        }

                                    } else {

                                        $user_coupon = new UserCoupon;

                                        $user_coupon->user_id = $user->id;

                                        $user_coupon->coupon_code = $request->coupon_code;

                                        $user_coupon->no_of_times_used = 1;

                                        $user_coupon->save();

                                    }

                                } else {

                                    $coupon_reason = $check_coupon->error_messages;
                                    
                                }

                            }

                        } else {

                            $coupon_reason = tr('coupon_delete_reason');
                        }
                    }

                    if ($user) {

                        $check_card_exists = User::where('users.id' , $request->id)
                                        ->leftJoin('cards' , 'users.id','=','cards.user_id')
                                        ->where('cards.id' , $user->card_id)
                                        ->where('cards.is_default' , DEFAULT_TRUE);

                        if($check_card_exists->count() != 0) {

                            $user_card = $check_card_exists->first();

                            if ($total <= 0) {

                                
                                $previous_payment = UserPayment::where('user_id' , $request->id)
                                            ->where('status', DEFAULT_TRUE)->orderBy('created_at', 'desc')->first();


                                $user_payment = new UserPayment;

                                if($previous_payment) {

                                    if (strtotime($previous_payment->expiry_date) >= strtotime(date('Y-m-d H:i:s'))) {

                                     $user_payment->expiry_date = date('Y-m-d H:i:s', strtotime("+{$subscription->plan} months", strtotime($previous_payment->expiry_date)));

                                    } else {

                                        $user_payment->expiry_date = date('Y-m-d H:i:s',strtotime("+{$subscription->plan} months"));

                                    }


                                } else {
                                   
                                    $user_payment->expiry_date = date('Y-m-d H:i:s',strtotime("+".$subscription->plan." months"));
                                }


                                $user_payment->payment_id = "free plan";

                                $user_payment->user_id = $request->id;

                                $user_payment->subscription_id = $request->subscription_id;

                                $user_payment->status = 1;

                                $user_payment->amount = $total;

                                $user_payment->payment_mode = CARD;

                                // Coupon details

                                $user_payment->is_coupon_applied = $is_coupon_applied;

                                $user_payment->coupon_code = $request->coupon_code  ? $request->coupon_code  :'';

                                $user_payment->coupon_amount = $coupon_amount;

                                $user_payment->subscription_amount = $subscription->amount;

                                $user_payment->amount = $total;

                                $user_payment->coupon_reason = $is_coupon_applied == COUPON_APPLIED ? '' : $coupon_reason;


                                if ($user_payment->save()) {

                                
                                    if ($user) {

                                        $user->user_type = 1;

                                        // $user->amount_paid += $total;

                                        $user->expiry_date = $user_payment->expiry_date;

                                        /*$now = time(); // or your date as well

                                        $end_date = strtotime($user->expiry_date);

                                        $datediff =  $end_date - $now;

                                        $user->no_of_days = ($user->expiry_date) ? floor($datediff / (60 * 60 * 24)) + 1 : 0;*/

                                        if ($user_payment->amount <= 0) {

                                            $user->zero_subscription_status = 1;
                                        }

                                        if ($user->save()) {

                                             $data = ['id' => $user->id , 'token' => $user->token, 'payment_id' => $user_payment->payment_id];

                                            $response_array = ['success' => true, 'message'=>tr('payment_success') , 'data' => $data];

                                        } else {


                                            throw new Exception(tr('user_details_not_saved'));
                                            
                                        }

                                    } else {

                                        throw new Exception(tr('user_not_found'));
                                        
                                    }
                                    
                                   
                                } else {

                                    throw new Exception(tr(Helper::get_error_message(902)), 902);

                                }


                            } else {

                                $stripe_secret_key = Setting::get('stripe_secret_key');

                                $customer_id = $user_card->customer_id;

                                if($stripe_secret_key) {

                                    \Stripe\Stripe::setApiKey($stripe_secret_key);

                                } else {

                                    throw new Exception(Helper::get_error_message(902), 902);

                                }

                                try{

                                   $user_charge =  \Stripe\Charge::create(array(
                                      "amount" => $total * 100,
                                      "currency" => "usd",
                                      "customer" => $customer_id,
                                    ));

                                   $payment_id = $user_charge->id;
                                   $amount = $user_charge->amount/100;
                                   $paid_status = $user_charge->paid;

                                    if($paid_status) {

                                        $previous_payment = UserPayment::where('user_id' , $request->id)
                                            ->where('status', PAID_STATUS)->orderBy('created_at', 'desc')->first();

                                        $user_payment = new UserPayment;

                                        if($previous_payment) {

                                            $expiry_date = $previous_payment->expiry_date;
                                            $user_payment->expiry_date = date('Y-m-d H:i:s', strtotime($expiry_date. "+".$subscription->plan." months"));

                                        } else {
                                            
                                            $user_payment->expiry_date = date('Y-m-d H:i:s',strtotime("+".$subscription->plan." months"));
                                        }


                                        $user_payment->payment_id  = $payment_id;

                                        $user_payment->user_id = $request->id;

                                        $user_payment->subscription_id = $request->subscription_id;

                                        $user_payment->status = PAID_STATUS;

                                        $user_payment->payment_mode = CARD;


                                        // Coupon details

                                        $user_payment->is_coupon_applied = $is_coupon_applied;

                                        $user_payment->coupon_code = $request->coupon_code  ? $request->coupon_code  :'';

                                        $user_payment->coupon_amount = $coupon_amount;

                                        $user_payment->subscription_amount = $subscription->amount;

                                        $user_payment->amount = $total;

                                        $user_payment->coupon_reason = $is_coupon_applied == COUPON_APPLIED ? '' : $coupon_reason;


                                        if ($user_payment->save()) {

                                            if ($user) {

                                                $user->user_type = SUBSCRIBED_USER;

                                                $user->expiry_date = $user_payment->expiry_date;
                            /*
                                                $now = time(); // or your date as well

                                                $end_date = strtotime($user->expiry_date);

                                                $datediff =  $end_date - $now;

                                                $user->no_of_days = ($user->expiry_date) ? floor($datediff / (60 * 60 * 24)) + 1 : 0;*/

                                                if ($user_payment->amount <= 0) {

                                                    $user->zero_subscription_status = 1;
                                                }

                                                if ($user->save()) {

                                                     $data = ['id' => $user->id , 'token' => $user->token,'payment_id' => $user_payment->payment_id];

                                                    $response_array = ['success' => true, 'message'=>tr('payment_success') , 'data' => $data];

                                                } else {


                                                    throw new Exception(tr('user_details_not_saved'));
                                                    
                                                }

                                            } else {

                                                throw new Exception(tr('user_not_found'));
                                                
                                            }

                                        

                                        } else {

                                             throw new Exception(tr(Helper::get_error_message(902)), 902);

                                        }


                                    } else {

                                        $response_array = array('success' => false, 'error_messages' => Helper::get_error_message(903) , 'error_code' => 903);

                                        throw new Exception(Helper::get_error_message(903), 903);

                                    }

                                
                                } catch(\Stripe\Error\RateLimit $e) {

                                    throw new Exception($e->getMessage(), 903);

                                } catch(\Stripe\Error\Card $e) {

                                    throw new Exception($e->getMessage(), 903);

                                } catch (\Stripe\Error\InvalidRequest $e) {
                                    // Invalid parameters were supplied to Stripe's API
                                   
                                    throw new Exception($e->getMessage(), 903);

                                } catch (\Stripe\Error\Authentication $e) {

                                    // Authentication with Stripe's API failed

                                    throw new Exception($e->getMessage(), 903);

                                } catch (\Stripe\Error\ApiConnection $e) {

                                    // Network communication with Stripe failed

                                    throw new Exception($e->getMessage(), 903);

                                } catch (\Stripe\Error\Base $e) {
                                  // Display a very generic error to the user, and maybe send
                                    
                                    throw new Exception($e->getMessage(), 903);

                                } catch (Exception $e) {
                                    // Something else happened, completely unrelated to Stripe

                                    throw new Exception($e->getMessage(), 903);

                                } catch (\Stripe\StripeInvalidRequestError $e) {

                                        Log::info(print_r($e,true));

                                    throw new Exception($e->getMessage(), 903);
                                    
                                
                                }


                            }

                        } else {
     
                            throw new Exception(Helper::get_error_message(901), 901);
                            
                        }

                    } else {

                        throw new Exception(tr('no_user_detail_found'));
                        
                    }

                } else {

                    throw new Exception(Helper::get_error_message(901), 901);

                }         

                
            }

            DB::commit();

            return response()->json($response_array , 200);

        } catch (Exception $e) {

            DB::rollback();

            $error = $e->getMessage();

            $code = $e->getCode();

            $response_array = ['success'=>false, 'error_messages'=>$error, 'error_code'=>$code];

            return response()->json($response_array);
        }
    }

    public function subscribe_channel(Request $request) {

        $validator = Validator::make( $request->all(), array(
                'channel_id'     => 'required|exists:channels,id,status,'.DEFAULT_TRUE.',is_approved,'.DEFAULT_TRUE,
                ));


        if ($validator->fails()) {

            $error_messages = implode(',', $validator->messages()->all());

            $response_array = ['success'=>false, 'error_messages'=>$error_messages];

        } else {

            $model = ChannelSubscription::where('user_id', $request->id)
                        ->where('channel_id',$request->channel_id)
                        ->first();

            $channel_details = Channel::find($request->channel_id);

            if (!$model) {

                $model = new ChannelSubscription;

                $model->user_id = $request->id;

                $model->channel_id = $request->channel_id;

                $model->status = DEFAULT_TRUE;

                $model->save();

                $notification_data['from_user_id'] = $request->id; 

                $notification_data['to_user_id'] = $channel_details->user_id;

                $notification_data['notification_type'] = BELL_NOTIFICATION_NEW_SUBSCRIBER;

                $notification_data['channel_id'] = $channel_details->id;

                dispatch(new BellNotificationJob(json_decode(json_encode($notification_data))));

                $response_array = ['success'=>true, 'message'=>tr('channel_subscribed')];

            } else {

                $response_array = ['success'=>false, 'message'=>tr('already_channel_subscribed')];

            }
        }

        return response()->json($response_array);
   
    }

    public function unsubscribe_channel(Request $request) {

        $validator = Validator::make( $request->all(), array(
                'channel_id'     => 'required|exists:channels,id',
                ));


        if ($validator->fails()) {

            $error_messages = implode(',', $validator->messages()->all());

            $response_array = ['success'=>false, 'error_messages'=>$error_messages];

        } else {

            $model = ChannelSubscription::where('user_id', $request->id)->where('channel_id',$request->channel_id)->first();

            if ($model) {

                $model->delete();

                $response_array = ['success'=>true, 'message'=>tr('channel_unsubscribed')];

            } else {

                $response_array = ['success'=>false, 'message'=>tr('not_found')];

            }
        }

        return response()->json($response_array);

    }


    public function singleVideoResponse($request) {

        $data = [];

        $video_tape_details = VideoTape::where('video_tapes.id' , $request->video_tape_id)
                                    ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id') 
                                    ->where('video_tapes.status' , 1)
                                    ->where('video_tapes.publish_status' , 1)
                                    ->where('video_tapes.is_approved' , 1)
                                    ->videoResponse()
                                    ->first();
        if($video_tape_details) {

            $data = $video_tape_details->toArray();

            $data['wishlist_status'] = $data['history_status'] = $data['is_subscribed'] = $data['is_liked'] = $data['pay_per_view_status'] = $data['user_type'] = $data['flaggedVideo'] = 0;

            $data['comment_rating_status'] = 1;

            if($request->id) {

                $data['wishlist_status'] = Helper::check_wishlist_status($request->id,$request->video_tape_id) ? 1 : 0;

                $data['history_status'] = count(Helper::history_status($request->id,$request->video_tape_id)) > 0? 1 : 0;

                $data['is_subscribed'] = check_channel_status($request->id, $video_tape_details->channel_id);

                $data['is_liked'] = Helper::like_status($request->id,$request->video_tape_id);

                $mycomment = UserRating::where('user_id', $request->id)->where('video_tape_id', $request->video_tape_id)->first();

                if ($mycomment) {

                    $data['comment_rating_status'] = DEFAULT_FALSE;
                }

                $user_details = '';

                $is_ppv_status = DEFAULT_TRUE;

                if($user_details = User::find($request->id)) {

                    $data['user_type'] = $user_details->user_type;

                    $is_ppv_status = ($video_tape_details->type_of_user == NORMAL_USER || $video_tape_details->type_of_user == BOTH_USERS) ? ( ( $user_details->user_type == 0 ) ? DEFAULT_TRUE : DEFAULT_FALSE ) : DEFAULT_FALSE; 

                }

                $data['is_ppv_subscribe_page'] = $is_ppv_status;
                
                $data['pay_per_view_status'] = VideoRepo::pay_per_views_status_check($user_details ? $user_details->id : '', $user_details ? $user_details->user_type : '', $video_tape_details)->getData()->success;


            }

            $data['currency'] = Setting::get('currency');

            $data['subscriberscnt'] = subscriberscnt($video_tape_details->channel_id);

            $data['share_url'] = route('user.single' , $request->video_tape_id);

            $data['embed_link'] = route('embed_video', array('u_id'=>$video_tape_details->unique_id));

            $video_url = $video_tape_details->video;

            if($request->login_by == DEVICE_ANDROID) {

                $video_url = Setting::get('streaming_url') ? Setting::get('streaming_url').get_video_end($data['video']) : $video_url;

            }

            if($request->login_by == DEVICE_IOS) {

                $video_url = Setting::get('HLS_STREAMING_URL') ? Setting::get('HLS_STREAMING_URL').get_video_end($data['video']) : $video_url;

            }

            $data['video'] = $video_url;


        }

        // Comments Section

        $comments = [];

        if($comments = Helper::video_ratings($request->video_tape_id,0)) {

            $comments = $comments->toArray();

        }

        $data['comments'] = $comments;

        // $data['suggestions'] = VideoRepo::suggestions($request);
        
        return $data;
    }

    public function spam_videos_list(Request $request) {

        $skip = $this->skip ?: 0;

        $take = $request->take ?: (Setting::get('admin_take_count', 12));

        // Load Flag videos based on logged in user id
        $model = Flag::where('flags.user_id', $request->id)
            ->leftJoin('video_tapes' , 'flags.video_tape_id' , '=' , 'video_tapes.id')
            ->where('video_tapes.is_approved' , 1)
            ->where('video_tapes.status' , 1)
            ->skip($skip)
            ->take($take)
            ->get();

        $flag_video = [];

        foreach ($model as $key => $value) {

            $request->request->add(['video_tape_id'=>$value->video_tape_id, 'login_by'=>DEVICE_ANDROID]);
            
            $flag_video[] = $this->singleVideoResponse($request);

        }

        $response_array = ['success'=>true, 'data'=>$flag_video];
        

        return response()->json($response_array);

    }

    public function add_spam(Request $request) {

        $validator = Validator::make($request->all(), [
            'video_tape_id' => 'required|exists:video_tapes,id',
            'reason' => 'required',
        ]);
        // If validator Fails, redirect same with error values
        if ($validator->fails()) {
             //throw new Exception("error", tr('admin_published_video_failure'));

            $error_messages = implode(',', $validator->messages()->all());

            $response_array = array('success' => false, 'error_messages'=>$error_messages , 'error_code' => 101);

            return response()->json(['success'=>false , 'message'=>$error_messages]);
        }
        // Assign Post request values into Data variable
        $data = $request->all();

        // include user_id index into the data varaible  "Auth::user()->id" -> Logged In user id
        $data['user_id'] = $request->id;
        $data['video_id'] =$request->video_tape_id;

        $data['status'] = DEFAULT_TRUE;

        // Save the values in DB
        if (Flag::create($data)) {
            return response()->json(['success'=>true, 'message'=>tr('report_video_success_msg')]);
        } else {
            //throw new Exception("error", tr('admin_published_video_failure'));
            return response()->json(['success'=>true, 'message'=>tr('admin_published_video_failure')]);
        }
    }

    public function reasons() {

        $reasons = getReportVideoTypes();

        return response()->json(['success'=>true, 'data'=>$reasons]);
    }


    public function remove_spam(Request $request) {

        $validator = Validator::make($request->all(), [
            'video_tape_id' => $request->status ? '' : 'required|exists:video_tapes,id',
        ]);
        // If validator Fails, redirect same with error values
        if ($validator->fails()) {
             //throw new Exception("error", tr('admin_published_video_failure'));

            $error_messages = implode(',', $validator->messages()->all());

            $response_array = array('success' => false, 'error_messages'=>$error_messages , 'error_code' => 101);

            return response()->json($response_array , 200);

            // COMMANDED BY VIDHYA. why we need message key in error response

            // return response()->json(['success'=>false , 'message'=>$error_messages]);
        }

        if ($request->status) {

            Flag::where('user_id', $request->id)->delete();

            return response()->json(['success'=>true, 'message'=>tr('unmark_report_video_success_msg')]);

        } else {
            // Load Spam Video from flag section
            $model = Flag::where('user_id', $request->id)
                ->where('video_tape_id', $request->video_tape_id)
                ->first();

            if ($model) {

                $model->delete();

                return response()->json(['success'=>true, 'message'=>tr('unmark_report_video_success_msg')]);
            } else {
                // throw new Exception("error", tr('admin_published_video_failure'));
                return response()->json(['success'=>true, 'message'=>tr('admin_published_video_failure')]);
            }

        }
    }


    /******************************** API's ******************************/


    public function pay_per_videos(Request $request) {

                // Load all the paper view videos based on logged in user id
        $model = PayPerView::where('pay_per_views.user_id', $request->id)
        ->select('pay_per_views.id as id', 'pay_per_views.video_id', 'pay_per_views.amount as pay_per_view_amount',
            'video_tapes.*', 'pay_per_views.created_at')
             ->leftJoin('video_tapes' ,'pay_per_views.video_id' , '=' , 'video_tapes.id')
            ->where('video_tapes.is_approved' , 1)
            ->where('video_tapes.status' , 1)
            ->where('pay_per_views.amount', '>', 0)
            ->where('video_tapes.age_limit','<=', checkAge($request))
            ->orderby('pay_per_views.created_at' , 'desc')
            ->paginate(16);

        $video = array('data' => $model->items(), 'pagination' => (string) $model->links());
      
        $items = [];

        foreach ($video['data'] as $key => $value) {

        
            $items[] = displayVideoDetails($value->videoTapeResponse, $request->id);

            $items[$key]['paid_amount'] = $value->pay_per_view_amount;


        }

        return response()->json(['items'=>$items, 'pagination'=>isset($model['pagination']) ? $model['pagination'] : 0]);
    }

    public function search_list($request,$key,$web = NULL,$skip = 0) {

        $base_query = VideoTape::where('video_tapes.is_approved' ,'=', 1)
                    ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                    ->leftJoin('categories' , 'categories.id' , '=' , 'video_tapes.category_id')
                    ->where('title','like', '%'.$key.'%')
                    ->where('video_tapes.status' , 1)
                    ->where('video_tapes.publish_status' , 1)
                    ->videoResponse()
                    ->where('channels.is_approved', 1)
                    ->where('channels.status', 1)
                    ->where('video_tapes.age_limit','<=', checkAge($request))
                    ->where('categories.status', CATEGORY_APPROVE_STATUS)
                    ->orderBy('video_tapes.created_at' , 'desc');
        if($web) {

            $videos = $base_query->paginate(16);

            $model = array('data' => $videos->items(), 'pagination' => (string) $videos->links());


        } else {

            $videos = $base_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();

            $model = ['data'=>$videos];

        }

        $items = [];

        foreach ($model['data'] as $key => $value) {
            
            $items[] = displayVideoDetails($value, $request->id);

        }

        return response()->json(['items'=>$items, 'pagination'=>isset($model['pagination']) ? $model['pagination'] : 0]);

    }


    /**
     * Function Name : search_channels_list
     *
     * @usage_place : WEB
     *
     * To list out all the channels which based on search
     *
     * @param Object $request - USer Details
     *
     * @return array of channel list
     */
    public function search_channels_list(Request $request) {

        $age = 0;

        $channel_id = [];

        $query = Channel::where('channels.is_approved', DEFAULT_TRUE)
                ->select('channels.*', 'video_tapes.id as video_tape_id', 'video_tapes.is_approved',
                    'video_tapes.status', 'video_tapes.channel_id')
                ->leftJoin('video_tapes', 'video_tapes.channel_id', '=', 'channels.id')
                ->where('channels.status', DEFAULT_TRUE)
                ->where('name','like', '%'.$request->key.'%')
                ->where('video_tapes.is_approved', DEFAULT_TRUE)
                ->where('video_tapes.status', DEFAULT_TRUE)
                ->groupBy('video_tapes.channel_id');

        if($request->id) {

            $user = User::find($request->id);

            $age = $user->age_limit;

            $age = $age ? ($age >= Setting::get('age_limit') ? 1 : 0) : 0;

            if ($request->has('channel_id')) {

                $query->whereIn('channels.id', $request->channel_id);
            }


            $query->where('video_tapes.age_limit','<=', $age);

        }

        if ($request->device_type == DEVICE_ANDROID || $request->device_type == DEVICE_IOS) {

            $channels = $query->skip($request->skip)->take(Setting::get('admin_take_count', 12))->get();

        } else {

            $channels = $query->paginate(6);

        }

        $lists = [];

        $pagination = 0;

        if(count($channels) > 0) {

            foreach ($channels as $key => $value) {
                $lists[] = ['channel_id'=>$value->id, 
                        'user_id'=>$value->user_id,
                        'picture'=> $value->picture, 
                        'title'=>$value->name,
                        'description'=>$value->description, 
                        'created_at'=>$value->created_at->diffForHumans(),
                        'no_of_videos'=>videos_count($value->id),
                        'subscribe_status'=>$request->id ? check_channel_status($request->id, $value->id) : '',
                        'no_of_subscribers'=>$value->getChannelSubscribers()->count(),
                ];

            }

            if ($request->device_type != DEVICE_ANDROID && $request->device_type != DEVICE_IOS) {

                $pagination = (string) $channels->links();

            }

        }

        if ($request->device_type == DEVICE_ANDROID || $request->device_type == DEVICE_IOS) {

            $response_array = ['success'=>true, 'data'=>$lists];

        } else {

            $response_array = ['success'=>true, 'channels'=>$lists, 'pagination'=>$pagination];

        }

        return response()->json($response_array);
    }


    /**
     * Function Name : stripe_ppv()
     * 
     * Pay the payment for Pay per view through stripe
     *
     * @param object $request - Admin video id
     * 
     * @return response of success/failure message
     */
    public function stripe_ppv(Request $request) {

        try {

            DB::beginTransaction();

             $validator = Validator::make($request->all(), [
                'coupon_code' => 'exists:coupons,coupon_code,status,'.COUPON_ACTIVE,  
                'video_tape_id'=>'required|exists:video_tapes,id,publish_status,'.VIDEO_PUBLISHED.',is_approved,'.ADMIN_VIDEO_APPROVED_STATUS.',status,'.USER_VIDEO_APPROVED_STATUS          
            ], array(
                    'coupon_code.exists' => tr('coupon_code_not_exists'),
                    'video_id.exists' => tr('video_not_exists'),
                ));

            if($validator->fails()) {

                $errors = implode(',', $validator->messages()->all());
                
                $response_array = ['success' => false, 'error_messages' => $errors, 'error_code' => 101];

                throw new Exception($errors);

            } else {

                $userModel = User::find($request->id);

                if ($userModel) {

                    if ($userModel->card_id) {

                        $user_card = Card::find($userModel->card_id);

                        if ($user_card && $user_card->is_default) {

                            $video = VideoTape::find($request->video_tape_id);

                            if($video) {

                                $total = $video->ppv_amount;

                                $coupon_amount = 0;

                                $coupon_reason = '';

                                $is_coupon_applied = COUPON_NOT_APPLIED;

                                if ($request->coupon_code) {

                                    $coupon = Coupon::where('coupon_code', $request->coupon_code)->first();

                                    if ($coupon) {
                                        
                                        if ($coupon->status == COUPON_INACTIVE) {

                                            $coupon_reason = tr('coupon_inactive_reason');

                                        } else {

                                            $check_coupon = $this->check_coupon_applicable_to_user($userModel, $coupon)->getData();

                                            if ($check_coupon->success) {

                                                $is_coupon_applied = COUPON_APPLIED;

                                                $amount_convertion = $coupon->amount;

                                                if ($coupon->amount_type == PERCENTAGE) {

                                                    $amount_convertion = amount_convertion($coupon->amount, $video->ppv_amount);

                                                }

                                                if ($amount_convertion < $video->ppv_amount  && $amount_convertion > 0) {

                                                    $total = $video->ppv_amount - $amount_convertion;

                                                    $coupon_amount = $amount_convertion;

                                                } else {

                                                    // throw new Exception(Helper::get_error_message(156),156);

                                                    $total = 0;

                                                    $coupon_amount = $amount_convertion;
                                                    
                                                }

                                                // Create user applied coupon

                                                if($check_coupon->code == 2002) {

                                                    $user_coupon = UserCoupon::where('user_id', $userModel->id)
                                                            ->where('coupon_code', $request->coupon_code)
                                                            ->first();

                                                    // If user coupon not exists, create a new row

                                                    if ($user_coupon) {

                                                        if ($user_coupon->no_of_times_used < $coupon->per_users_limit) {

                                                            $user_coupon->no_of_times_used += 1;

                                                            $user_coupon->save();

                                                        }

                                                    }

                                                } else {

                                                    $user_coupon = new UserCoupon;

                                                    $user_coupon->user_id = $userModel->id;

                                                    $user_coupon->coupon_code = $request->coupon_code;

                                                    $user_coupon->no_of_times_used = 1;

                                                    $user_coupon->save();

                                                }

                                            } else {

                                                $coupon_reason = $check_coupon->error_messages;
                                                
                                            }

                                        }

                                    } else {

                                        $coupon_reason = tr('coupon_delete_reason');
                                    }
                                
                                }

                                if ($total <= 0) {

                                    $user_payment = new PayPerView;

                                    $user_payment->payment_id = $is_coupon_applied ? 'COUPON-DISCOUNT' : FREE_PLAN;

                                    $user_payment->user_id = $request->id;
                                    $user_payment->video_id = $request->video_tape_id;

                                    $user_payment->status = PAID_STATUS;

                                    $user_payment->is_watched = NOT_YET_WATCHED;

                                    $user_payment->ppv_date = date('Y-m-d H:i:s');

                                    if ($video->type_of_user == NORMAL_USER) {

                                        $user_payment->type_of_user = tr('normal_users');

                                    } else if($video->type_of_user == PAID_USER) {

                                        $user_payment->type_of_user = tr('paid_users');

                                    } else if($video->type_of_user == BOTH_USERS) {

                                        $user_payment->type_of_user = tr('both_users');
                                    }


                                    if ($video->type_of_subscription == ONE_TIME_PAYMENT) {

                                        $user_payment->type_of_subscription = tr('one_time_payment');

                                    } else if($video->type_of_subscription == RECURRING_PAYMENT) {

                                        $user_payment->type_of_subscription = tr('recurring_payment');

                                    }

                                    $user_payment->payment_mode = CARD;

                                    // Coupon details

                                    $user_payment->is_coupon_applied = $is_coupon_applied;

                                    $user_payment->coupon_code = $request->coupon_code ? $request->coupon_code : '';

                                    $user_payment->coupon_amount = $coupon_amount;

                                    $user_payment->ppv_amount = $video->ppv_amount;

                                    $user_payment->amount = $total;

                                    $user_payment->coupon_reason = $is_coupon_applied == COUPON_APPLIED ? '' : $coupon_reason;

                                    $user_payment->save();

                                    // Commission Spilit 

                                    if($video->amount > 0) { 

                                        // Do Commission spilit  and redeems for moderator

                                        Log::info("ppv_commission_spilit started");

                                        UserRepo::ppv_commission_split($video->id , $user_payment->id , "");

                                        Log::info("ppv_commission_spilit END"); 
                                        
                                    }

                                    \Log::info("ADD History - add_to_redeem");

                                    $data = ['id'=> $request->id, 'token'=> $userModel->token , 'payment_id' => $user_payment->payment_id];

                                    $response_array = array('success' => true, 'message'=>tr('payment_success'),'data'=> $data);

                                } else {

                                    // Get the key from settings table

                                    $stripe_secret_key = Setting::get('stripe_secret_key');

                                    $customer_id = $user_card->customer_id;
                                    
                                    if($stripe_secret_key) {

                                        \Stripe\Stripe::setApiKey($stripe_secret_key);

                                    } else {

                                        $response_array = array('success' => false, 'error_messages' => Helper::get_error_message(902) , 'error_code' => 902);

                                        throw new Exception(Helper::get_error_message(902));
                                        
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

                                            $user_payment = new PayPerView;
                                            $user_payment->payment_id  = $payment_id;
                                            $user_payment->user_id = $request->id;
                                            $user_payment->video_id = $request->video_tape_id;
                                            $user_payment->payment_mode = CARD;
                                        

                                            $user_payment->status = PAID_STATUS;

                                            $user_payment->is_watched = NOT_YET_WATCHED;

                                            $user_payment->ppv_date = date('Y-m-d H:i:s');

                                            if ($video->type_of_user == NORMAL_USER) {

                                                $user_payment->type_of_user = tr('normal_users');

                                            } else if($video->type_of_user == PAID_USER) {

                                                $user_payment->type_of_user = tr('paid_users');

                                            } else if($video->type_of_user == BOTH_USERS) {

                                                $user_payment->type_of_user = tr('both_users');
                                            }


                                            if ($video->type_of_subscription == ONE_TIME_PAYMENT) {

                                                $user_payment->type_of_subscription = tr('one_time_payment');

                                            } else if($video->type_of_subscription == RECURRING_PAYMENT) {

                                                $user_payment->type_of_subscription = tr('recurring_payment');

                                            }

                                            // Coupon details

                                            $user_payment->is_coupon_applied = $is_coupon_applied;

                                            $user_payment->coupon_code = $request->coupon_code ? $request->coupon_code : '';

                                            $user_payment->coupon_amount = $coupon_amount;

                                            $user_payment->ppv_amount = $video->ppv_amount;

                                            $user_payment->amount = $total;

                                            $user_payment->coupon_reason = $is_coupon_applied == COUPON_APPLIED ? '' : $coupon_reason;
                                                                  
                                            $user_payment->save();

                                            // Commission Spilit 

                                            if($video->ppv_amount > 0) { 

                                                // Do Commission spilit  and redeems for moderator

                                                Log::info("ppv_commission_spilit started");

                                                PaymentRepo::ppv_commission_split($video->id , $user_payment->id , "");

                                                Log::info("ppv_commission_spilit END");
                                                
                                            }

                                        
                                            $data = ['id'=> $request->id, 'token'=> $userModel->token , 'payment_id' => $payment_id];

                                            $response_array = array('success' => true, 'message'=>tr('payment_success'),'data'=> $data);

                                        } else {

                                            $response_array = array('success' => false, 'error_messages' => Helper::get_error_message(902) , 'error_code' => 902);

                                            throw new Exception(tr('no_vod_video_found'));

                                        }
                                    
                                    } catch(\Stripe\Error\RateLimit $e) {

                                        throw new Exception($e->getMessage(), 903);

                                    } catch(\Stripe\Error\Card $e) {

                                        throw new Exception($e->getMessage(), 903);

                                    } catch (\Stripe\Error\InvalidRequest $e) {
                                        // Invalid parameters were supplied to Stripe's API
                                       
                                        throw new Exception($e->getMessage(), 903);

                                    } catch (\Stripe\Error\Authentication $e) {

                                        // Authentication with Stripe's API failed

                                        throw new Exception($e->getMessage(), 903);

                                    } catch (\Stripe\Error\ApiConnection $e) {

                                        // Network communication with Stripe failed

                                        throw new Exception($e->getMessage(), 903);

                                    } catch (\Stripe\Error\Base $e) {
                                      // Display a very generic error to the user, and maybe send
                                        
                                        throw new Exception($e->getMessage(), 903);

                                    } catch (Exception $e) {
                                        // Something else happened, completely unrelated to Stripe

                                        throw new Exception($e->getMessage(), 903);

                                    } catch (\Stripe\StripeInvalidRequestError $e) {

                                            Log::info(print_r($e,true));

                                        throw new Exception($e->getMessage(), 903);
                                        
                                    
                                    }


                                }

                            
                            } else {

                                $response_array = array('success' => false , 'error_messages' => tr('no_video_found'));

                                throw new Exception(tr('no_video_found'));
                                
                            }

                        } else {

                        
                            throw new Exception(tr('no_default_card_available'), 901);

                        }

                    } else {


                        throw new Exception(tr('no_default_card_available'), 901);

                    }

                } else {

                    throw new Exception(tr('no_user_detail_found'));
                    

                }

            }

            DB::commit();

            return response()->json($response_array,200);

        } catch (Exception $e) {

            DB::rollback();

            $message = $e->getMessage();

            $code = $e->getCode();

            $response_array = ['success'=>false, 'error_messages'=>$message, 'error_code'=>$code];

            return response()->json($response_array);

        }
        
    }


    /**
     * Function Name :  wishlist_list()
     * 
     * @usage_place : WEB
     * 
     * List of wishlist based on the logged in user
     *
     * @param object $request - User Details
     * 
     * @return response of wishlist
     */
    public function wishlist_list($request) {

        $base_query = Wishlist::where('wishlists.user_id' , $request->id)
                            ->leftJoin('video_tapes' ,'wishlists.video_tape_id' , '=' , 'video_tapes.id')
                            ->leftJoin('channels' ,'video_tapes.channel_id' , '=' , 'channels.id')
                            ->leftJoin('categories' , 'categories.id' , '=' , 'video_tapes.category_id')
                            ->where('video_tapes.is_approved' , 1)
                            ->where('video_tapes.status' , 1)
                            ->where('wishlists.status' , 1)
                            ->where('channels.status', 1)
                            ->where('channels.is_approved', 1)
                            ->select(
                                    'wishlists.id as wishlist_id',
                                    'video_tapes.id as video_tape_id' ,
                                    'video_tapes.title',
                                    'video_tapes.description' ,
                                    'video_tapes.ppv_amount',
                                    'channels.status as channel_status',
                                    'video_tapes.amount',
                                    'default_image',
                                    'video_tapes.watch_count',
                                    'video_tapes.ratings',
                                    'video_tapes.duration',
                                    'video_tapes.channel_id',
                                    'video_tapes.type_of_user',
                                    'channels.user_id as channel_created_by',
                                    'video_tapes.ad_status',
                                    DB::raw('DATE_FORMAT(video_tapes.publish_time , "%e %b %y") as publish_time') , 
                                    'channels.name as channel_name', 
                                    'video_tapes.type_of_subscription',
                                    'wishlists.created_at')
                            ->where('video_tapes.age_limit','<=', checkAge($request))
                            ->where('categories.status', CATEGORY_APPROVE_STATUS)
                            ->orderby('wishlists.created_at' , 'desc');

        if ($request->id) {

            // Check any flagged videos are present

            $flag_videos = flag_videos($request->id);

            if($flag_videos) {

                $base_query->whereNotIn('video_tapes.id',$flag_videos);

            }
        
        }

        $videos = $base_query->paginate(16);

        $items = [];

        $pagination = 0;

        if (count($videos) > 0) {

            foreach ($videos->items() as $key => $value) {
                
                $items[] = displayVideoDetails($value, $request->id);

            }

            $pagination = (string) $videos->links();

        }

        return response()->json(['items'=>$items, 'pagination'=>$pagination]);
    
    }


    /**
     * Function Name : watch_list()
     * 
     * @usage_place : WEB
     *
     * User History - User watched videos display here
     *
     * @param Object $request - User Details
     *
     * @return response of videos list
     */
    public function watch_list($request) {

        $base_query = UserHistory::where('user_histories.user_id' , $request->id)
                            ->leftJoin('video_tapes' ,'user_histories.video_tape_id' , '=' , 'video_tapes.id')
                            ->leftJoin('categories' , 'categories.id' , '=' , 'video_tapes.category_id') 
                            ->leftJoin('channels' ,'video_tapes.channel_id' , '=' , 'channels.id')
                            ->where('video_tapes.is_approved' , 1)
                            ->where('video_tapes.status' , 1)
                            ->where('channels.status', 1)
                            ->where('channels.is_approved', 1)
                            ->select('user_histories.id as history_id',
                                    'video_tapes.id as video_tape_id' ,
                                    'video_tapes.title',
                                    'video_tapes.description' , 
                                    'video_tapes.duration',
                                    'default_image',
                                    'channels.status as channel_status',
                                    'video_tapes.watch_count',
                                    'video_tapes.ratings',
                                    'video_tapes.ppv_amount', 
                                    'video_tapes.amount',
                                    'video_tapes.type_of_user',
                                    'video_tapes.type_of_subscription',
                                    'channels.user_id as channel_created_by',
                                    DB::raw('DATE_FORMAT(video_tapes.publish_time , "%e %b %y") as publish_time'), 
                                    'video_tapes.channel_id',
                                    'channels.name as channel_name', 
                                    'user_histories.created_at')
                            ->where('video_tapes.age_limit','<=', checkAge($request))
                            ->where('categories.status', CATEGORY_APPROVE_STATUS)
                            ->orderby('user_histories.created_at' , 'desc');
        
        if ($request->id) {

            // Check any flagged videos are present

            $flag_videos = flag_videos($request->id);

            if($flag_videos) {

                $base_query->whereNotIn('video_tapes.id',$flag_videos);

            }
        
        }


        $videos = $base_query->paginate(16);

        $items = [];

        $pagination = 0;

        if (count($videos) > 0) {

            foreach ($videos->items() as $key => $value) {
                
                $items[] = displayVideoDetails($value, $request->id);

            }

            $pagination = (string) $videos->links();

        }

        return response()->json(['items'=>$items, 'pagination'=>$pagination]);

    }

    /**
     * Function Name : recently_added()
     *
     * @usage_place : WEB
     *
     * Displayed recently added videos by user/admin , the video displayed based on created date
     *
     * @param object $request - User Details
     *
     * @return list of videos
     */
    public function recently_added($request) {

        $base_query = VideoTape::where('video_tapes.is_approved' , 1)
                            ->where('video_tapes.status' , 1)
                            ->where('video_tapes.publish_status' , 1)
                            ->where('channels.status', 1)
                            ->where('channels.is_approved', 1)
                            ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                            ->leftJoin('categories' , 'categories.id' , '=' , 'video_tapes.category_id') 
                            ->orderby('video_tapes.created_at' , 'desc')
                            ->where('categories.status', CATEGORY_APPROVE_STATUS)
                            ->videoResponse();

        if ($request->id) {

            // Check any flagged videos are present

            $flag_videos = flag_videos($request->id);

            if($flag_videos) {

                $base_query->whereNotIn('video_tapes.id',$flag_videos);

            }

            $base_query = $base_query->where('video_tapes.age_limit','<=', checkAge($request));

        } else {

            $base_query = $base_query->where('video_tapes.age_limit', '=' , 0);
        }

    
        $videos = $base_query->paginate(16);

        $items = [];

        $pagination = 0;

        if (count($videos)) {

            foreach ($videos->items() as $key => $value) {
                
                $items[] = displayVideoDetails($value, $request->id);

            }

            $pagination  = (string) $videos->links();

        }


        return response()->json(['items'=>$items, 'pagination'=>$pagination]);
    
    }


    /**
     * Function Name : trending_list()
     *
     * @usage_place : WEB
     *
     * To display based on watch count, no of users seen videos
     *
     * @param object $request - User Details
     *
     * @return Response of videos list
     */
    public function trending_list($request) {

        $base_query = VideoTape::where('watch_count' , '>' , 0)
                        ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                        ->where('video_tapes.publish_status' , 1)
                        ->where('video_tapes.status' , 1)
                        ->where('video_tapes.is_approved' , 1)
                        ->where('channels.status', 1)
                        ->where('channels.is_approved', 1)
                        ->leftJoin('categories' , 'categories.id' , '=' , 'video_tapes.category_id') 
                        ->where('categories.status', CATEGORY_APPROVE_STATUS)
                        ->videoResponse()
                        
                        ->orderby('watch_count' , 'desc');

        if ($request->id) {

            // Check any flagged videos are present

            $flag_videos = flag_videos($request->id);

            if($flag_videos) {
                
                $base_query->whereNotIn('video_tapes.id',$flag_videos);
            }

            $base_query = $base_query->where('video_tapes.age_limit','<=', checkAge($request));

        } else {

            $base_query = $base_query->where('video_tapes.age_limit','=', 0);
        }

        $videos = $base_query->paginate(16);

        $items = [];

        $pagination = 0;

        if (count($videos) > 0) {

            foreach ($videos->items() as $key => $value) {
                
                $items[] = displayVideoDetails($value, $request->id);

            }

            $pagination = (string) $videos->links();

        }

        return response()->json(['items'=>$items, 'pagination'=>$pagination]);
    
    }


    /**
     * Function Name : suggestion_videos()
     *
     * @usage_place : WEB
     *
     * To get suggestion video to see the user
     *
     * @param object $request - User Details
     *
     * @return response of array videos
     */ 
    public function suggestion_videos($request) {

        $base_query = VideoTape::where('video_tapes.is_approved' , 1)   
                            ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                            ->leftJoin('categories' , 'categories.id' , '=' , 'video_tapes.category_id') 
                            ->where('video_tapes.status' , 1)
                            ->where('video_tapes.publish_status' , 1)
                            ->orderby('video_tapes.created_at' , 'desc')
                            ->videoResponse()
                            ->where('channels.is_approved', 1)
                            ->where('channels.status', 1)
                            ->where('categories.status', CATEGORY_APPROVE_STATUS)
                            ->orderByRaw('RAND()');

        if($request->video_tape_id) {

            $base_query->whereNotIn('video_tapes.id', [$request->video_tape_id]);
        }

        if ($request->id) {

            // Check any flagged videos are present

            $flag_videos = flag_videos($request->id);

            if($flag_videos) {

                $base_query->whereNotIn('video_tapes.id',$flag_videos);
            }

            $base_query = $base_query->where('video_tapes.age_limit','<=', checkAge($request));

        } else {

            $base_query = $base_query->where('video_tapes.age_limit','=', 0);
        }

    
        $videos = $base_query->paginate(16);
        
        $items = [];

        $pagination = 0;

        if (count($videos) > 0) {

            foreach ($videos->items() as $key => $value) {
                
                $items[] = displayVideoDetails($value, $request->id);

            }

            $pagination = (string) $videos->links();

        }

        return response()->json(['items'=>$items, 'pagination'=>$pagination]);
    
    }


    /**
     * Function Name : channel_list
     *
     * @usage_place : WEB
     *
     * To list out all the channels which is in active status
     *
     * @param Object $request - USer Details
     *
     * @return array of channel list
     */
    public function channel_list(Request $request) {

        $age = 0;

        $channel_id = [];

        $query = Channel::where('channels.is_approved', DEFAULT_TRUE)
                ->select('channels.*', 'video_tapes.id as video_tape_id', 'video_tapes.is_approved',
                    'video_tapes.status', 'video_tapes.channel_id')
                ->leftJoin('video_tapes', 'video_tapes.channel_id', '=', 'channels.id')
                ->where('channels.status', DEFAULT_TRUE)
                ->where('video_tapes.is_approved', DEFAULT_TRUE)
                ->where('video_tapes.status', DEFAULT_TRUE)
                ->groupBy('video_tapes.channel_id');

        if($request->id) {

            $user = User::find($request->id);

            $age = $user->age_limit;

            $age = $age ? ($age >= Setting::get('age_limit') ? 1 : 0) : 0;

            if ($request->has('channel_id')) {

                $query->whereIn('channels.id', $request->channel_id);
            }


            $query->where('video_tapes.age_limit','<=', $age);

        }

        if ($request->device_type == DEVICE_ANDROID || $request->device_type == DEVICE_IOS) {

            $channels = $query->skip($request->skip)->take(Setting::get('admin_take_count', 12))->get();

        } else {

            $channels = $query->paginate(16);

        }

        $lists = [];

        $pagination = 0;

        if(count($channels) > 0) {

            foreach ($channels as $key => $value) {
                $lists[] = ['channel_id'=>$value->id, 
                        'user_id'=>$value->user_id,
                        'picture'=> $value->picture, 
                        'title'=>$value->name,
                        'description'=>$value->description, 
                        'created_at'=>$value->created_at->diffForHumans(),
                        'no_of_videos'=>videos_count($value->id),
                        'subscribe_status'=>$request->id ? check_channel_status($request->id, $value->id) : '',
                        'no_of_subscribers'=>$value->getChannelSubscribers()->count(),
                ];

            }

            if ($request->device_type != DEVICE_ANDROID && $request->device_type != DEVICE_IOS) {

                $pagination = (string) $channels->links();

            }

        }

        if ($request->device_type == DEVICE_ANDROID || $request->device_type == DEVICE_IOS) {

            $response_array = ['success'=>true, 'data'=>$lists];

        } else {

            $response_array = ['success'=>true, 'channels'=>$lists, 'pagination'=>$pagination];

        }

        return response()->json($response_array);
    }


    /**
     * Function Name : channel_list
     *
     * @usage_place : MOBILE
     *
     * To list out all the channels which is subscribed the logged in user
     *
     * @param Object $request - Subscribed plan Details
     *
     * @return array of channel subscribed plans
     */
    public function subscribed_channels(Request $request) {

        $validator = Validator::make($request->all(), 
                array(
                    'skip' => 'required',
                ));

        if($validator->fails()) {

            $errors = implode(',', $validator->messages()->all());
            
            $response_array = ['success' => false, 'error_messages' => $errors, 'error_code' => 101];


        } else {

            if ($request->id) {

                $channel_id = ChannelSubscription::where('user_id', $request->id)->pluck('channel_id')->toArray();

                $request->request->add([ 
                    'channel_id' => $channel_id,
                ]);        
            }

            $response_array = $this->channel_list($request)->getData();


        }

        return response()->json($response_array);

    }
    /**
     * Function Name : channel_videos()
     *
     * @usage_place : WEB
     *
     * To list out all the videos based on the channel id
     *
     * @param integer $channel_id - Channel Id
     * 
     * @return list out all the videos, and status of the subscribers
     */
    public function channel_videos($channel_id, $skip , $request = null) {

        $videos_query = VideoTape::leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                    ->leftJoin('categories' , 'categories.id' , '=' , 'video_tapes.category_id')
                    ->where('video_tapes.channel_id' , $channel_id)
                    ->videoResponse()
                    ->orderby('video_tapes.created_at' , 'desc');

        $u_id = $request->id;

        $channel = Channel::find($channel_id);

        if ($channel) {

            if ($u_id == $channel->user_id) {

                if ($u_id) {

                    $videos_query->where('video_tapes.age_limit','<=', checkAge($request)); 
                }

            } else {

                $videos_query->where('video_tapes.status' , USER_VIDEO_APPROVED_STATUS)
                    ->where('video_tapes.is_approved', ADMIN_VIDEO_APPROVED_STATUS)
                        ->where('video_tapes.publish_status' , 1)   
                        ->where('channels.status', 1)
                        ->where('channels.is_approved', 1)
                        ->where('categories.status', CATEGORY_APPROVE_STATUS);

            }

        } else {

            $videos_query->where('video_tapes.status' , USER_VIDEO_APPROVED_STATUS)
                ->where('video_tapes.is_approved', ADMIN_VIDEO_APPROVED_STATUS)
                        ->where('video_tapes.publish_status' , 1)
                        ->where('channels.status', 1)
                        ->where('channels.is_approved', 1)
                        ->where('categories.status', CATEGORY_APPROVE_STATUS);

            
            
        }

        if ($u_id) {

            // Check any flagged videos are present
            $flagVideos = getFlagVideos($u_id);

            if($flagVideos) {

                $videos_query->whereNotIn('video_tapes.id', $flagVideos);

            }

        }

        if ($skip >= 0) {

            //Setting::get('admin_take_count', 12)
            $videos = $videos_query->skip($skip)->take(Setting::get('admin_take_count', 12))->get();

        } else {

            $videos = $videos_query->paginate(16);
        }


        $items = [];

        if (count($videos) > 0) { 

            foreach ($videos as $key => $value) {
                
                $items[] = displayVideoDetails($value, $u_id);

            }

        }

        return response()->json($items);

    }

    /**
     * Function Name : channel_trending()
     *
     * @usage_place : WEB
     *
     * To list out channel trending videos 
     *
     * @param integer $id - Channel Id
     *
     * @return channel videos
     */
    public function channel_trending($id, $count = 5 , $channel_owner_id = "" , $request) {

        $items = [];

        if(!$id) {

            return response()->json($items , 200);

        }

        $base_query = VideoTape::where('watch_count' , '>' , 0)
                        ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                        ->leftJoin('categories' , 'categories.id' , '=' , 'video_tapes.category_id')
                        ->videoResponse()
                        ->where('channel_id', $id)
                        ->orderby('watch_count' , 'desc');

        if(!$channel_owner_id) {

            $base_query = $base_query->where('video_tapes.status' , 1)
                        ->where('video_tapes.is_approved' , 1)
                        ->where('video_tapes.publish_status' , 1)
                        ->where('channels.status', 1)
                        ->where('channels.is_approved', 1)
                        ->where('categories.status', CATEGORY_APPROVE_STATUS);

        }

        $u_id = "";

        if (Auth::check()) {

            // Check Age Limit 

            // Check any flagged videos are present

            $u_id = Auth::user()->id;
                            
            $base_query->where('video_tapes.age_limit','<=', checkAge($request));

            $flag_videos = flag_videos($u_id);

            if($flag_videos) {
                
                $base_query->whereNotIn('video_tapes.id',$flag_videos);
            }
        }

        if($count > 0){

            $videos = $base_query->skip(0)->take($count)->get();

        } else {

            $videos = $base_query->paginate(16);
            
        }


        if (count($videos) > 0) { 

            foreach ($videos as $key => $value) {
                
                $items[] = displayVideoDetails($value, $u_id);

            }

        }

        return response()->json($items);

    }

    /**
     * Function Name : payment_videos()
     *
     * @usage_place : WEB
     *
     * To list out payment videos 
     *
     * @param integer $id - Channel Id
     *
     * @return channel videos
     */
    public function payment_videos($id, $skip) {

        $u_id = Auth::check() ? Auth::user()->id : '';    

        $base_query = VideoTape::leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                        ->videoResponse()
                        ->orderby('amount' , 'desc')
                        ->whereRaw("channel_id = '{$id}' and channels.user_id = '{$u_id}' and (user_ppv_amount > 0 or amount > 0)");

        if($skip >= 0) {

            $videos = $base_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();

        } else {

            $videos = $base_query->paginate(16);
            
        }

        $items = [];

        foreach ($videos as $key => $value) {

            $items[] = displayVideoDetails($value, $u_id);

        }

        return response()->json(['data'=>$items, 'count'=>count($items)]);

    
    }

    /**
     * Function Name : single_video()
     *
     * @usage_place : WEB
     * 
     * To view single video based on video id
     *
     * @param integer $request - Video id
     *
     * @return based on video displayed all the details'
     */
    public function video_detail(Request $request) {

        $video = VideoTape::where('video_tapes.id' , $request->video_tape_id)
                    ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                    ->leftJoin('categories' , 'categories.id' , '=' , 'video_tapes.category_id')
                    ->videoResponse()
                    // ->where('video_tapes.status' , 1)
                    // ->where('video_tapes.is_approved' , 1)
                    // ->where('video_tapes.publish_status' , 1)
                    // ->where('channels.is_approved', 1)
                    // ->where('channels.status', 1)
                    ->first();
        if ($video) {

            if ($request->id != $video->channel_created_by) {

                // Channel / video is declined by admin /user

                if($video->is_approved == ADMIN_VIDEO_DECLINED_STATUS || $video->status == USER_VIDEO_DECLINED_STATUS || $video->channel_approved_status == ADMIN_CHANNEL_DECLINED || $video->channel_status == USER_CHANNEL_DECLINED) {

                    return response()->json(['success'=>false, 'error_messages'=>tr('video_is_declined')]);

                }

                // Video if not published

                if ($video->publish_status != PUBLISH_NOW) {

                    return response()->json(['success'=>false, 'error_messages'=>tr('video_not_yet_publish')]);
                }

                if ($video->getCategory) {

                    if ($video->getCategory->status == CATEGORY_DECLINE_STATUS) {

                        return response()->json(['success'=>false, 'error_messages'=>tr('category_declined_by_admin')]);

                    } 
                }

            }

            if (Setting::get('is_payper_view')) {

                if ($request->id != $video->channel_created_by) {

                    $user = User::find($request->id);

                    if ($video->ppv_amount > 0) {

                        $ppv_status = $user ? VideoRepo::pay_per_views_status_check($user->id, $user->user_type, $video)->getData()->success : false;


                        if ($ppv_status) {
                            

                        } else {

                            if ($request->id) {

                                if ($user->user_type) {        
                                    
                                    return response()->json(['url'=>route('user.subscription.ppv_invoice', $video->video_tape_id)]);

                                } else {

                                    return response()->json(['url'=>route('user.subscription.pay_per_view', $video->video_tape_id)]);
                                }

                            } else {

                                return response()->json(['url'=>route('user.subscription.pay_per_view', $video->video_tape_id)]);

                            }

                      
                        }

                    }

                }

            } 

            if($request->id) {

                if ($video->getChannel->user_id != $request->id) {

                    $age = $request->age_limit ? ($request->age_limit >= Setting::get('age_limit') ? 1 : 0) : 0;

                    if ($video->age_limit > $age) {

                        return response()->json(['success'=>false, 'error_messages'=>tr('age_error')]);

                    }

                } 
            } else {

                if ($video->age_limit == 1) {

                    return response()->json(['success'=>false, 'error_messages'=>tr('age_error')]);

                }

            }

            if($comments = Helper::video_ratings($request->video_tape_id,0)) {
                $comments = $comments->toArray();
            }

            $ads = $video->getScopeVideoAds ? ($video->getScopeVideoAds->status ? $video->getScopeVideoAds  : '') : '';

            $channels = [];

            $suggestions = $this->suggestion_videos($request,'', '', $request->video_tape_id)->getData();

            $wishlist_status = $history_status = WISHLIST_EMPTY;

            $report_video = getReportVideoTypes();

             // Load the user flag

            $flaggedVideo = ($request->id) ? Flag::where('video_tape_id',$request->video_tape_id)->where('user_id', $request->id)->first() : '';

            $videoPath = $video_pixels = $videoStreamUrl = '';

            $hls_video = "";

            $main_video = $video->video; 

            if ($video->video_type == VIDEO_TYPE_UPLOAD) {

                if ($video->publish_status == 1) {

                    $hls_video = Helper::convert_hls_to_secure(get_video_end($video->video) , $video->video);


                    if (\Setting::get('streaming_url')) {

                        if ($video->is_approved == 1) {

                            if ($video->video_resolutions) {

                                $videoStreamUrl = Helper::web_url().'/uploads/smil/'.get_video_end_smil($video->video).'.smil';

                                \Log::info("video Stream url".$videoStreamUrl);

                                \Log::info("Empty Stream url".empty($videoStreamUrl));

                                \Log::info("File Exists Stream url".!file_exists($videoStreamUrl));

                                if(empty($videoStreamUrl) || !file_exists($videoStreamUrl)) {

                                    $videos = $video->video_path ? $video->video.','.$video->video_path : $video->video;

                                    $video_pixels = $video->video_resolutions ? 'original,'.$video->video_resolutions : 'original';

                                    $videoPath = [];

                                    $videos = $videos ? explode(',', $videos) : [];

                                    $video_pixels = $video_pixels ? explode(',', $video_pixels) : [];

                                    foreach ($videos as $key => $value) {

                                        $videoPath[] = ['file' => Helper::convert_rtmp_to_secure(get_video_end($value) , $value), 'label' => $video_pixels[$key]];

                                    }

                                    $videoPath = json_decode(json_encode($videoPath));

                                }

                            } else {
     
                                $videoStreamUrl = Helper::convert_rtmp_to_secure(get_video_end($video->video) , $video->video);

                            }
                        }

                    } else {

                        $videos = $video->video_path ? $video->video.','.$video->video_path : [$video->video];

                        $video_pixels = $video->video_resolutions ? 'original,'.$video->video_resolutions : ['original'];

                        $videoPath = [];

                        Log::info("VIDEOS LIST".print_r($videos , true));

                        if(count($videos) > 0) {

                            $videos = is_array($videos) ? $videos : explode(',', $videos);

                            $video_pixels = is_array($video_pixels) ? $video_pixels : explode(',', $video_pixels);

                            foreach ($videos as $key => $value) {

                                $videoPathData = ['file' => Helper::convert_rtmp_to_secure(get_video_end($value) , $value), 'label' => isset($video_pixels[$key]) ? $video_pixels[$key] : "HD"];


                                $videoPath[] = $videoPathData;
                           
                            }
                        }

                        $videoPath =  json_decode(json_encode($videoPath));
                        
                    }

                } else {

                    $videoStreamUrl = $video->video;

                    $hls_video = $video->video;
                }

            } else {

                $videoStreamUrl = $video->video;

                $hls_video = $video->video;

            }

            $subscribe_status = DEFAULT_FALSE;

            $comment_rating_status = DEFAULT_TRUE;

            if($request->id) {

                $wishlist_status = $request->id ? Helper::check_wishlist_status($request->id,$request->video_tape_id): 0;

                $history_status = Helper::history_status($request->id,$request->video_tape_id);

                $subscribe_status = check_channel_status($request->id, $video->channel_id);

                $mycomment = UserRating::where('user_id', $request->id)->where('rating', '>', 0)->where('video_tape_id', $request->video_tape_id)->first();

                if ($mycomment) {

                    $comment_rating_status = DEFAULT_FALSE;
                }

            }

            $share_link = route('user.single' , $request->video_tape_id);

            $like_count = LikeDislikeVideo::where('video_tape_id', $request->video_tape_id)
                ->where('like_status', DEFAULT_TRUE)
                ->count();

            $dislike_count = LikeDislikeVideo::where('video_tape_id', $request->video_tape_id)
                ->where('dislike_status', DEFAULT_TRUE)
                ->count();

            $subscriberscnt = subscriberscnt($video->channel_id);

            $embed_link  = "<iframe width='560' height='315' src='".route('embed_video', array('u_id'=>$video->unique_id))."' frameborder='0' allowfullscreen></iframe>";

            $tags = VideoTapeTag::select('tag_id', 'tags.name as tag_name')
                ->leftJoin('tags', 'tags.id', '=', 'video_tape_tags.tag_id')
                ->where('video_tape_id', $request->video_tape_id)
                ->where('video_tape_tags.status', TAG_APPROVE_STATUS)
                ->get()->toArray();

            $category = Category::find($video->category_id);

            $video['category_unique_id'] = $category ? $category->unique_id : '';

            $video['category_name'] = $category ? $category->name : '';

            $response_array = [
                'tags'=>$tags,
                'video'=>$video, 'comments'=>$comments, 
                'channels' => $channels, 'suggestions'=>$suggestions,
                'wishlist_status'=> $wishlist_status, 'history_status' => $history_status, 'main_video'=>$main_video,
                'report_video'=>$report_video, 'flaggedVideo'=>$flaggedVideo , 'videoPath'=>$videoPath,
                'video_pixels'=>$video_pixels, 'videoStreamUrl'=>$videoStreamUrl, 'hls_video'=>$hls_video,
                'like_count'=>$like_count,'dislike_count'=>$dislike_count,
                'ads'=>$ads, 'subscribe_status'=>$subscribe_status,
                'subscriberscnt'=>$subscriberscnt,'comment_rating_status'=>$comment_rating_status,
                'embed_link' => $embed_link,
                ];

            return response()->json(['success'=>true, 'response_array'=>$response_array], 200);

        } else {

            return response()->json(['success'=>false, 'error_messages'=>tr('video_not_found')]);
        }

    }

    /**
     * Function Name : create_channel()
     *
     * To create a channel based on the logged in user
     *
     * @param object $request - User id, token
     *
     * @return success/failure message of boolean 
     */ 
    public function create_channel(Request $request) {

        $channels = getChannels($request->id);

        $user = User::find($request->id);

        if((count($channels) == 0 || Setting::get('multi_channel_status'))) {

            if ($user->user_type) {

                $response = CommonRepo::channel_save($request)->getData();

                if($response->success) {

                    $response_array = ['success'=>true, 'data'=>$response->data, 'message'=>$response->message];
                   
                } else {
                    
                    $response_array = ['success'=>false, 'error_messages'=>$response->error];

                }

            } else {

                $response_array = ['success'=>false,'error_messages'=>Helper::get_error_message(164), 'error_code'=>164];

            }

        } else {

            $response_array = ['success'=>false, 'error_messages'=>Helper::get_error_message(163), 'error_code'=>163];
        }

        return response()->json($response_array);

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
    public function channel_edit(Request $request) {

        $validator = Validator::make( $request->all(), array(
                            'channel_id' => 'required|exists:channels,id',
                        ));

         if($validator->fails()) {

                $error_messages = implode(',', $validator->messages()->all());

                $response_array = ['success'=> false, 'error_messages'=>$error_messages];

                // return back()->with('flash_errors', $error_messages);

        } else {

            $channel = Channel::where('user_id', $request->id)->where('id', $request->channel_id)->first();

            if ($channel) {

                $response = CommonRepo::channel_save($request)->getData();

                if($response->success) {

                    $response_array = ['success'=>true, 'data'=>$response->data, 'message'=>$response->message];
                   
                } else {
                    
                    $response_array = ['success'=>false, 'error_messages'=>$response->error];

                }

            } else {

                $response_array = ['success'=>false, 'error_messages'=>tr('not_your_channel')];

            }

        }
        return response()->json($response_array);

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


        $validator = Validator::make( $request->all(), array(
                            'channel_id' => 'required|exists:channels,id',
                        ));

        if($validator->fails()) {

                $error_messages = implode(',', $validator->messages()->all());

                $response_array = ['success'=> false, 'error_messages'=>$error_messages];

                // return back()->with('flash_errors', $error_messages);

        } else {

            $channel = Channel::where('user_id', $request->id)->where('id', $request->channel_id)->first();

            if($channel) {       

                $channel->delete();

                $response_array = ['success'=>true, 'message'=>tr('channel_delete_success')];

            } else {

                $response_array = ['success'=> false, 'error_messages'=>tr('not_your_channel')];


            }

        }

        return response()->json($response_array);


    }

    /**
     * Function Nmae : ppv_list()
     * 
     * to list out  all the paid videos by logged in user using PPV
     *
     * @param object $request - User id, token 
     *
     * @return response of array with message
     */
    public function ppv_list(Request $request) {

        $validator = Validator::make(
            $request->all(),
            array(
                'skip'=>($request->device_type == DEVICE_WEB) ? '' : 'required|numeric',
            ));

        if ($validator->fails()) {
            // Error messages added in response for debugging
            $error_messages = implode(',',$validator->messages()->all());

            $response_array = array(
                    'success' => false,
                    'error_messages' => $error_messages
            );
            return response()->json($response_array);
        } else {

            $currency = Setting::get('currency');

            $query = PayPerView::select('pay_per_views.id as pay_per_view_id',
                    'video_id',
                    'video_tapes.title',
                    'pay_per_views.amount',
                    'pay_per_views.status as video_status',
                    'video_tapes.default_image as picture',
                    'pay_per_views.type_of_subscription',
                    'pay_per_views.is_coupon_applied',
                    'pay_per_views.coupon_reason',
                    'pay_per_views.type_of_user',
                    'pay_per_views.payment_id',
                    'pay_per_views.ppv_amount',
                    'pay_per_views.coupon_amount',
                    'pay_per_views.coupon_code',
                    'pay_per_views.payment_mode',
                     DB::raw('DATE_FORMAT(pay_per_views.created_at , "%e %b %y") as paid_date')
                     )
                    ->leftJoin('video_tapes', 'video_tapes.id', '=', 'pay_per_views.video_id')
                    ->where('pay_per_views.user_id', $request->id)
                    ->where('pay_per_views.amount', '>', 0)
                    ->orderby('pay_per_views.created_at', 'desc');

            $user = User::find($request->id);

            if ($request->device_type == DEVICE_WEB) {

                $model = $query->paginate(16);

                $data = [];

            
                foreach ($model->items() as $key => $value) {

                    $is_ppv_status = DEFAULT_TRUE;

                    if ($user) {

                        $is_ppv_status = ($value->type_of_user == NORMAL_USER || $value->type_of_user == BOTH_USERS) ? ( ( $user->user_type == 0 ) ? DEFAULT_TRUE : DEFAULT_FALSE ) : DEFAULT_FALSE; 

                    } 

                    $videoDetails = $value->videoTapeResponse ? $value->videoTapeResponse : '';

                    $pay_per_view_status = $videoDetails ? (VideoRepo::pay_per_views_status_check($user ? $user->id : '', $user ? $user->user_type : '', $videoDetails)->getData()->success) : true;

                    $ppv_notes = !$pay_per_view_status ? ($value->type_of_user == 1 ? tr('normal_user_note') : tr('paid_user_note')) : ''; 
                    
                    $data[] = [
                            'pay_per_view_id'=>$value->pay_per_view_id,
                            'video_tape_id'=>$value->video_id,
                            'title'=>$value->title,
                            'amount'=>$value->amount,
                            'video_status'=>$value->video_status,
                            'paid_date'=>$value->paid_date,
                            'currency'=>Setting::get('currency'),
                            'picture'=>$value->picture,
                            'type_of_subscription'=>$value->type_of_subscription,
                            'type_of_user'=>$value->type_of_user,
                            'payment_id'=>$value->payment_id,
                            'pay_per_view_status'=>$pay_per_view_status,
                            'is_ppv_subscribe_page'=>$is_ppv_status, // 0 - Dont shwo subscribe+ppv_ page 1- Means show ppv subscribe page
                            'ppv_notes'=>$ppv_notes,
                            'coupon_code'=>$value->coupon_code,
                            'payment_mode'=>$value->payment_mode,
                            'coupon_amount'=>$value->coupon_amount,
                            'ppv_amount'=>$value->ppv_amount,
                            'is_coupon_applied'=>$value->is_coupon_applied,
                            'coupon_reason'=>$value->coupon_reason,
                            ];

                }

                $response_array = array('success'=>true, 'data' => $data, 'pagination' => (string) $model->links());

            } else {

                $model = $query->skip($request->skip)
                        ->take(Setting::get('admin_take_count' ,12))
                        ->get();

                $data = [];

                foreach ($model as $key => $value) {

                    $is_ppv_status = DEFAULT_TRUE;

                    if ($user) {

                        $is_ppv_status = ($value->type_of_user == NORMAL_USER || $value->type_of_user == BOTH_USERS) ? ( ( $user->user_type == 0 ) ? DEFAULT_TRUE : DEFAULT_FALSE ) : DEFAULT_FALSE; 

                    } 

                    $videoDetails = $value->videoTapeResponse ? $value->videoTapeResponse : '';

                    $pay_per_view_status = $videoDetails ? (VideoRepo::pay_per_views_status_check($user ? $user->id : '', $user ? $user->user_type : '', $videoDetails)->getData()->success) : true;

                    $spam = Flag::where('video_tape_id', $value->video_id)
                            ->where('user_id', $user->id)
                            ->first();

                    $spam_status = $spam ? true : false;
    
                    $data[] = ['pay_per_view_id'=>$value->pay_per_view_id,
                            'video_tape_id'=>$value->video_id,
                            'title'=>$value->title,
                            'amount'=>$value->amount,
                            'video_status'=>$value->video_status,
                            'paid_date'=>$value->paid_date,
                            'currency'=>Setting::get('currency'),
                            'picture'=>$value->picture,
                            'type_of_subscription'=>$value->type_of_subscription,
                            'type_of_user'=>$value->type_of_user,
                            'payment_id'=>$value->payment_id,
                            'pay_per_view_status'=>$pay_per_view_status,
                            'is_ppv_subscribe_page'=>$is_ppv_status, // 0 - Dont shwo subscribe+ppv_ 
                            'is_spam'=>$spam_status,
                            'coupon_code'=>$value->coupon_code,
                            'payment_mode'=>$value->payment_mode,
                            'coupon_amount'=>$value->coupon_amount,
                            'ppv_amount'=>$value->ppv_amount,
                            'is_coupon_applied'=>$value->is_coupon_applied,
                            'coupon_reason'=>$value->coupon_reason,
                            ];

                }

                $response_array = ['success'=>true, 'data'=>$data];
            }

            return response()->json($response_array);

        }

    } 


    /**
     * Function Name : paypal_ppv()
     * 
     * Pay the payment for Pay per view through paypal
     *
     * @param object $request - video tape id
     * 
     * @return response of success/failure message
     */
    public function paypal_ppv(Request $request) {

        try {

            DB::beginTransaction();

            $validator = Validator::make(
                $request->all(),
                array(
                    'video_tape_id'=>'required|exists:video_tapes,id,status,'.USER_VIDEO_APPROVED_STATUS.',is_approved,'.ADMIN_VIDEO_APPROVED_STATUS.',publish_status,'.VIDEO_PUBLISHED,
                    'payment_id'=>'required',
                    'coupon_code'=>'exists:coupons,coupon_code',
                ),  array(
                    'coupon_code.exists' => tr('coupon_code_not_exists'),
                    'video_tape_id.exists' => tr('livevideo_not_exists'),
                ));


            if ($validator->fails()) {
                // Error messages added in response for debugging
                $errors = implode(',',$validator->messages()->all());

                $response_array = ['success' => false,'error_messages' => $errors,'error_code' => 101];

                throw new Exception($errors);

            } else {

                $video = VideoTape::find($request->video_tape_id);

                $user = User::find($request->id);

                $total = $video->ppv_amount;

                $coupon_amount = 0;

                $coupon_reason = '';

                $is_coupon_applied = COUPON_NOT_APPLIED;

                if ($request->coupon_code) {

                    $coupon = Coupon::where('coupon_code', $request->coupon_code)->first();

                    if ($coupon) {
                        
                        if ($coupon->status == COUPON_INACTIVE) {

                            $coupon_reason = tr('coupon_inactive_reason');

                        } else {

                            $check_coupon = $this->check_coupon_applicable_to_user($user, $coupon)->getData();

                            if ($check_coupon->success) {

                                $is_coupon_applied = COUPON_APPLIED;

                                $amount_convertion = $coupon->amount;

                                if ($coupon->amount_type == PERCENTAGE) {

                                    $amount_convertion = amount_convertion($coupon->amount, $video->ppv_amount);

                                }

                                if ($amount_convertion < $video->ppv_amount  && $amount_convertion > 0) {

                                    $total = $video->ppv_amount - $amount_convertion;

                                    $coupon_amount = $amount_convertion;

                                } else {

                                    // throw new Exception(Helper::get_error_message(156),156);

                                    $total = 0;

                                    $coupon_amount = $amount_convertion;
                                    
                                }

                                // Create user applied coupon

                                if($check_coupon->code == 2002) {

                                    $user_coupon = UserCoupon::where('user_id', $user->id)
                                            ->where('coupon_code', $request->coupon_code)
                                            ->first();

                                    // If user coupon not exists, create a new row

                                    if ($user_coupon) {

                                        if ($user_coupon->no_of_times_used < $coupon->per_users_limit) {

                                            $user_coupon->no_of_times_used += 1;

                                            $user_coupon->save();

                                        }

                                    }

                                } else {

                                    $user_coupon = new UserCoupon;

                                    $user_coupon->user_id = $user->id;

                                    $user_coupon->coupon_code = $request->coupon_code;

                                    $user_coupon->no_of_times_used = 1;

                                    $user_coupon->save();

                                }

                            } else {

                                $coupon_reason = $check_coupon->error_messages;
                                
                            }

                        }

                    } else {

                        $coupon_reason = tr('coupon_delete_reason');
                    }
                }

                $payment = PayPerView::where('user_id', $request->id)
                            ->where('video_id', $request->video_tape_id)
                            ->where('status', PAID_STATUS)
                            ->orderBy('ppv_date', 'desc')
                            ->first();

                $payment_status = DEFAULT_FALSE;

                if ($payment) {

                    if ($video->type_of_subscription == RECURRING_PAYMENT && $payment->is_watched == WATCHED) {

                        $payment_status = DEFAULT_FALSE;

                    } else {

                        $payment_status = DEFAULT_TRUE;

                    }

                } else {

                    $payment_status = DEFAULT_FALSE;

                }

                if ($video->is_pay_per_view == PPV_ENABLED) {

                    if ($payment_status) {

                        throw new Exception(tr('already_paid_amount_to_video'));

                    }

                    $user_payment = new PayPerView;
                    
                    $user_payment->payment_id  = $request->payment_id;

                    $user_payment->user_id = $request->id;

                    $user_payment->video_id = $request->video_tape_id;

                    $user_payment->status = PAID_STATUS;

                    $user_payment->is_watched = NOT_YET_WATCHED;

                    $user_payment->payment_mode = PAYPAL;

                    $user_payment->ppv_date = date('Y-m-d H:i:s');

                    if ($video->type_of_user == NORMAL_USER) {

                        $user_payment->type_of_user = tr('normal_users');

                    } else if($video->type_of_user == PAID_USER) {

                        $user_payment->type_of_user = tr('paid_users');

                    } else if($video->type_of_user == BOTH_USERS) {

                        $user_payment->type_of_user = tr('both_users');
                    }


                    if ($video->type_of_subscription == ONE_TIME_PAYMENT) {

                        $user_payment->type_of_subscription = tr('one_time_payment');

                    } else if($video->type_of_subscription == RECURRING_PAYMENT) {

                        $user_payment->type_of_subscription = tr('recurring_payment');

                    }
                    // Coupon details

                    $user_payment->is_coupon_applied = $is_coupon_applied;

                    $user_payment->coupon_code = $request->coupon_code ? $request->coupon_code : '';

                    $user_payment->coupon_amount = $coupon_amount;

                    $user_payment->ppv_amount = $video->ppv_amount;

                    $user_payment->amount = $total;

                    $user_payment->coupon_reason = $is_coupon_applied == COUPON_APPLIED ? '' : $coupon_reason;

                    $user_payment->save();

                    if($user_payment) {

                        // Do Commission spilit  and redeems for moderator

                        Log::info("ppv_commission_spilit started");

                        PaymentRepo::ppv_commission_split($video->id , $user_payment->id , "");

                        Log::info("ppv_commission_spilit END"); 
   

                    } 


                    $user = User::find($request->id);

                    $response_array = ['success'=>true, 'message'=>tr('payment_success'),
                            'data'=>['id'=>$user->id ,'token'=>$user->token]];

                } else {

                    throw new Exception(tr('ppv_not_set'));
                    
                }

            }

            DB::commit();

            return response()->json($response_array, 200);

        } catch (Exception $e) {

            DB::rollback();

            $e = $e->getMessage();

            $response_array = ['success'=>false, 'error_messages'=>$e];

            return response()->json($response_array);
        }
    

    }
        /**
     * FOR MOBILE APP WE ARE USING THIS
     *  
     * Function Name: cards_add()
     *
     * Description: add card using stripe payment
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param 
     * 
     * @return
     */
    public function cards_add(Request $request) {

        $stripe_secret_key = \Setting::get('stripe_secret_key');

        if($stripe_secret_key) {

            \Stripe\Stripe::setApiKey($stripe_secret_key);

        } else {

            $response_array = ['success' => false, 'error_messages' => tr('add_card_is_not_enabled')];

            return response()->json($response_array);
        }

        try {

            $validator = Validator::make(
                    $request->all(),
                    [
                        'last_four' => '',
                        'card_token' => 'required',
                        'customer_id' => '',
                        'card_type' => '',
                    ]
                );

            if ($validator->fails()) {

                $error = implode(',',$validator->messages()->all());
             
                throw new Exception($error , 101);

            } else {

                $user_details = User::find($request->id);

                if(!$user_details) {

                    throw new Exception(Helper::get_error_message(133), 133);
                    
                }

                $stripe_gateway_details = [
                    
                    "card" => $request->card_token,
                    
                    "email" => $user_details->email,
                    
                    "description" => "Customer for ".Setting::get('site_name'),
                    
                ];


                // Get the key from settings table
                
                $customer = \Stripe\Customer::create($stripe_gateway_details);

                if($customer) {

                    Log::info('Customer'.print_r($customer , true));

                    $customer_id = $customer->id;

                    $card_details = new Card;

                    $card_details->user_id = $request->id;

                    $card_details->customer_id = $customer->id;

                    $card_details->card_token = $customer->sources->data ? $customer->sources->data[0]->id : "";

                    $card_details->card_name = $customer->sources->data ? $customer->sources->data[0]->brand : "";

                    $card_details->last_four = $customer->sources->data[0]->last4 ? $customer->sources->data[0]->last4 : "";

                    $card_details->cvv = "001";

                    // Check is any default is available

                     // check the user having any cards 

                    $check_user_cards = Card::where('user_id',$request->id)->count();

                    $card_details->is_default = $check_user_cards ? 0 : 1;

                    if($card_details->save()) {

                        if($user_details) {

                            $user_details->card_id = $check_user_cards ? $user_details->card_id : $card_details->id;

                            $user_details->save();
                        }

                        $data = [
                                'user_id' => $request->id, 
                                'card_id' => $card_details->id,
                                'customer_id' => $card_details->customer_id,
                                'last_four' => $card_details->last_four, 
                                'card_token' => $card_details->card_token, 
                                'is_default' => $card_details->is_default
                                ];

                        $response_array = ['success' => true, 'message' => tr('add_card_success'), 
                            'data'=> $data];

                            return response()->json($response_array , 200);

                    } else {

                        throw new Exception(Helper::get_error_message(123), 123);
                        
                    }
               
                } else {

                    throw new Exception(tr('cards_add_failed'));
                    
                }

            }

        } catch(Exception $e) {

            $error_message = $e->getMessage();

            $error_code = $e->getCode();

            $response_array = ['success'=>false, 'error_messages'=> $error_message , 'error_code' => $error_code];

            return response()->json($response_array , 200);
        }
   
    }


    /**
     * Function Name : tags_list
     *
     * To list out all active tags
     *
     * @created Vithya
     *
     * @updated -
     *
     * @param object $request - ''
     *
     * @return response of array values
     */
    public function tags_list(Request $request) {

        $take = $request->take ? $request->take : Setting::get('admin_take_count');

        $query = Tag::select('tags.id as tag_id', 'name as tag_name', 'search_count as count')
                    ->where('status', TAG_APPROVE_STATUS)
                    ->orderBy('created_at', 'desc');

        if ($request->skip){
            $query->skip($request->skip)
                    ->take($take);
        }

        $tags = $query->get();

        return response()->json(['success'=>true, 'data'=>$tags]);
    }


    /**
     * Function Name : tags_view
     *
     * To get any one of the tag details
     *
     * @created Vithya
     *
     * @updated -
     *
     * @param object $request - tag id
     *
     * @return response of object values
     */
    public function tags_view(Request $request) {

        $model = Tag::select('tags.id as tag_id', 'name as tag_name', 'search_count as count')->where('tags.status', TAG_APPROVE_STATUS)
                ->where('id', $request->tag_id)
                ->first();

        if ($model) {

            return response()->json(['success'=>true, 'data'=>$model]);

        } else {

            return response()->json(['success'=>false, 'error_messages'=>tr('tag_not_found')]);
        }

    }

    /**
     * Function Name : tags_videos()
     *
     * @created Vithya
     *
     * @updated -
     *
     * To display based on tag
     *
     * @param object $request - User Details
     *
     * @return Response of videos list
     */
    public function tags_videos(Request $request) {

        $basicValidator = Validator::make(
                $request->all(),
                array(
                    'tag_id' => 'required|exists:tags,id,status,'.TAG_APPROVE_STATUS,
                )
        );

        if($basicValidator->fails()) {

            $error_messages = implode(',', $basicValidator->messages()->all());

            $response_array = ['success'=>false, 'error_messages'=>$error_messages];

        } else {

            $tag = Tag::find($request->tag_id);

            $base_query = VideoTape::leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                            ->leftJoin('categories' , 'video_tapes.category_id' , '=' , 'categories.id')
                            ->leftJoin('video_tape_tags' , 'video_tape_tags.video_tape_id' , '=' , 'video_tapes.id')
                            ->where('video_tapes.publish_status' , 1)
                            ->where('video_tapes.status' , 1)
                            ->where('video_tapes.is_approved' , 1)
                            ->where('channels.status', 1)
                            ->where('channels.is_approved', 1)
                            ->videoResponse()
                            ->where('video_tapes.age_limit','<=', checkAge($request))
                            ->where('video_tape_tags.tag_id', $request->tag_id)
                            ->orderby('video_tapes.updated_at' , 'desc');

            if ($request->id) {

                // Check any flagged videos are present

                $flag_videos = flag_videos($request->id);

                if($flag_videos) {
                    
                    $base_query->whereNotIn('video_tapes.id',$flag_videos);
                }
                
            }

            if ($request->device_type == DEVICE_WEB) { 

                $videos = $base_query->paginate(16);

                $items = [];

                $pagination = 0;

                if (count($videos) > 0) {

                    foreach ($videos->items() as $key => $value) {
                        
                        $items[] = displayVideoDetails($value, $request->id);

                    }

                    $pagination = (string) $videos->links();

                }

                $response_array = ['success'=>true, 'items'=>$items, 'pagination'=>$pagination];

            } else {

                $videos = $base_query->skip($request->skip)->take(Setting::get('admin_take_count'))->get();

                $data = [];

                if (count($videos) > 0) {

                    foreach ($videos as $key => $value) {
                        
                        $data[] = displayVideoDetails($value, $request->id);

                    }

                }

                $response_array = ['success'=>true, 'data'=>$data];
            }

        }

        return response()->json($response_array);        
    
    }

    /**
     * Function Name : categories_list()
     *
     * Load all the active categories
     *
     * @created Vithya
     *
     * @updated -
     *
     * @param -
     *
     * @return response of json
     */
    public function categories_list(Request $request) {

        $model = Category::select('id as category_id', 'name as category_name')->where('status', CATEGORY_APPROVE_STATUS)->orderBy('created_at', 'desc')
                ->get();

        return response()->json($model);
    
    }

    /**
     * Function Name : categories_view()
     *
     * category details based on id
     *
     * @created Vithya
     *
     * @updated -
     *
     * @param - 
     * 
     * @return response of json
     */
    public function categories_view(Request $request) {

        $basicValidator = Validator::make(
                $request->all(),
                array(
                    'category_id' => 'required|exists:categories,id,status,'.CATEGORY_APPROVE_STATUS,
                )
        );

        if($basicValidator->fails()) {

            $error_messages = implode(',', $basicValidator->messages()->all());

            $response_array = ['success'=>false, 'error_messages'=>$error_messages];              

        } else {

            $model = Category::select('id as category_id', 'name as category_name', 'image as category_image', 'description')->where('status', CATEGORY_APPROVE_STATUS)
                ->where('id', $request->category_id)
                ->first();

            $channels_list = $this->categories_channels_list($request)->getData();

            $channels = [];

            if ($channels_list->success) {

                $channels = $channels_list->data;

            }

            $category_list = $this->categories_videos($request)->getData();

            $categories = [];

            if ($category_list->success) {

                $categories = $category_list->data;

            }

            $response_array = ['success'=>true, 'category'=>$model, 'category_videos'=>$categories,'channels_list'=>$channels];

        }

        return response()->json($response_array);
    }


    /**
     * Function Name : categories_videos()
     *
     * @created Vithya
     *
     * @updated -
     *
     * To display based on category
     *
     * @param object $request - User Details
     *
     * @return Response of videos list
     */
    public function categories_videos(Request $request) {


        $basicValidator = Validator::make(
                $request->all(),
                array(
                    'category_id' => 'required|exists:categories,id,status,'.CATEGORY_APPROVE_STATUS,
                )
        );

        if($basicValidator->fails()) {

            $error_messages = implode(',', $basicValidator->messages()->all());

            $response_array = ['success'=>false, 'error_messages'=>$error_messages];

        } else {

            $base_query = VideoTape::leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                            ->leftJoin('categories' , 'video_tapes.category_id' , '=' , 'categories.id')
                            ->where('video_tapes.publish_status' , 1)
                            ->where('video_tapes.status' , 1)
                            ->where('video_tapes.is_approved' , 1)
                            ->where('channels.status', 1)
                            ->where('channels.is_approved', 1)
                            ->videoResponse()
                            ->where('video_tapes.age_limit','<=', checkAge($request))
                            ->where('category_id', $request->category_id)
                            ->where('categories.status', CATEGORY_APPROVE_STATUS)
                            ->orderby('video_tapes.updated_at' , 'desc');

            if ($request->id) {

                // Check any flagged videos are present

                $flag_videos = flag_videos($request->id);

                if($flag_videos) {
                    
                    $base_query->whereNotIn('video_tapes.id',$flag_videos);
                }
                
            }

            if ($request->device_type == DEVICE_WEB) { 

                $videos = $base_query->paginate(16);

                $items = [];

                $pagination = 0;

                if (count($videos) > 0) {

                    foreach ($videos->items() as $key => $value) {
                        
                        $items[] = displayVideoDetails($value, $request->id);

                    }

                    $pagination = (string) $videos->links();

                }

                $response_array = ['success'=>true, 'items'=>$items, 'pagination'=>$pagination];

            } else {

                $videos = $base_query->skip($request->skip)->take(Setting::get('admin_take_count'))->get();

                $data = [];

                if (count($videos) > 0) {

                    foreach ($videos as $key => $value) {
                        
                        $data[] = displayVideoDetails($value, $request->id);

                    }

                }
                
                $response_array = ['success'=>true, 'data'=>$data];

            }

        }

        return response()->json($response_array);        
    
    }


    /**
     * Function Name : categories_channels_list
     *
     * To list out all the channels which is in active status
     *
     * @created Vithya 
     *
     * @updated 
     *
     * @param Object $request - USer Details
     *
     * @return array of channel list
     */
    public function categories_channels_list(Request $request) {

        $basicValidator = Validator::make(
                $request->all(),
                array(
                    'category_id' => 'required|exists:categories,id,status,'.CATEGORY_APPROVE_STATUS,
                )
        );

        if($basicValidator->fails()) {

            $error_messages = implode(',', $basicValidator->messages()->all());

            return $response_array = ['success'=>false, 'error_messages'=>$error_messages];

        } else {

            $age = 0;

            $channel_id = [];

            $query = Channel::where('channels.is_approved', DEFAULT_TRUE)
                    ->select('channels.*', 'video_tapes.id as video_tape_id', 'video_tapes.is_approved',
                        'video_tapes.status', 'video_tapes.channel_id')
                    ->leftJoin('video_tapes', 'video_tapes.channel_id', '=', 'channels.id')
                    ->where('video_tapes.category_id', $request->category_id)
                    ->where('channels.status', DEFAULT_TRUE)
                    ->where('video_tapes.is_approved', DEFAULT_TRUE)
                    ->where('video_tapes.publish_status', DEFAULT_TRUE)
                    ->where('video_tapes.status', DEFAULT_TRUE)
                    ->groupBy('video_tapes.channel_id');


            if($request->id) {

                $user = User::find($request->id);

                $age = $user->age_limit;

                $age = $age ? ($age >= Setting::get('age_limit') ? 1 : 0) : 0;

                if ($request->has('channel_id')) {

                    $query->whereIn('channels.id', $request->channel_id);
                }


                $query->where('video_tapes.age_limit','<=', $age);

            }

            if ($request->device_type == DEVICE_ANDROID || $request->device_type == DEVICE_IOS) {

                $channels = $query->skip($request->skip)->take(Setting::get('admin_take_count', 12))->get();

            } else {

                $channels = $query->paginate(16);

            }

            $lists = [];

            $pagination = 0;

            if(count($channels) > 0) {

                foreach ($channels as $key => $value) {
                    $lists[] = ['channel_id'=>$value->id, 
                            'user_id'=>$value->user_id,
                            'picture'=> $value->picture, 
                            'title'=>$value->name,
                            'description'=>$value->description, 
                            'created_at'=>$value->created_at->diffForHumans(),
                            'no_of_videos'=>videos_count($value->id),
                            'subscribe_status'=>$request->id ? check_channel_status($request->id, $value->id) : '',
                            'no_of_subscribers'=>$value->getChannelSubscribers()->count(),
                    ];

                }

                if ($request->device_type != DEVICE_ANDROID && $request->device_type != DEVICE_IOS) {

                    $pagination = (string) $channels->links();

                }

            }

            if ($request->device_type == DEVICE_ANDROID || $request->device_type == DEVICE_IOS) {

                $response_array = ['success'=>true, 'data'=>$lists];

            } else {

                $response_array = ['success'=>true, 'channels'=>$lists, 'pagination'=>$pagination];

            }

            return response()->json($response_array);

        }
    }


   /**
    * Function Name : autorenewal_cancel
    *
    * To cancel automatic subscription
    *
    * @created Vithya
    *
    * @updated -
    *
    * @param object $request - USer details & payment details
    *
    * @return boolean response with message
    */
    public function autorenewal_cancel(Request $request) {

        $basicValidator = Validator::make(
                $request->all(),
                array(
                    'cancel_reason' => 'required',
                ),
                [
                    'cancel_reason' => tr('cancel_reason_required')
                ]
        );

        if($basicValidator->fails()) {

            $error_messages = implode(',', $basicValidator->messages()->all());

            $response_array = ['success'=>false, 'error_messages'=>$error_messages];

        } else {

            $user_payment = UserPayment::where('user_id', $request->id)
                    ->where('status', PAID_STATUS)
                    ->orderBy('created_at', 'desc')->first();

            if($user_payment) {

                // Check the subscription is already cancelled

                if($user_payment->is_cancelled == AUTORENEWAL_CANCELLED) {

                    $response_array = ['success' => false , 'error_messages' => Helper::get_error_message(165) , 'error_code' => 165];

                    return response()->json($response_array , 200);

                }

                $user_payment->is_cancelled = AUTORENEWAL_CANCELLED;

                $user_payment->cancel_reason = $request->cancel_reason;

                $user_payment->save();

                $subscription = $user_payment->subscription;

                $data = ['id'=>$request->id, 
                    'subscription_id'=>$user_payment->subscription_id,
                    'user_subscription_id'=>$user_payment->id,
                    'title'=>$subscription ? $subscription->title : '',
                    'description'=>$subscription ? $subscription->description : '',
                    'plan'=>$subscription ? $subscription->plan : '',
                    'amount'=>$user_payment->amount,
                    'status'=>$user_payment->status,
                    'expiry_date'=>date('d M Y', strtotime($user_payment->expiry_date)),
                    'created_at'=>$user_payment->created_at->diffForHumans(),
                    'currency'=>Setting::get('currency'),
                    'payment_mode'=>$user_payment->payment_mode,
                    'is_coupon_applied'=>$user_payment->is_coupon_applied,
                    'coupon_code'=>$user_payment->coupon_code,
                    'coupon_amount'=>$user_payment->coupon_amount,
                    'subscription_amount'=>$user_payment->subscription_amount,
                    'coupon_reason'=>$user_payment->coupon_reason,
                    'is_cancelled'=>$user_payment->is_cancelled,
                    'cancel_reason'=>$user_payment->cancel_reason
                ];

                $response_array = ['success'=> true, 'message'=>tr('cancel_subscription_success'), 'data'=>$data];

            } else {

                $response_array = ['success'=> false, 'error_messages'=>Helper::get_error_message(167), 'error_code'=>167];

            }

        }

        return response()->json($response_array);

    }

   /**
    * Function Name : autorenewal_enable
    *
    * To enable automatic subscription
    *
    * @created Vithya
    *
    * @updated -
    *
    * @param object $request - USer details & payment details
    *
    * @return boolean response with message
    */
    public function autorenewal_enable(Request $request) {

        $user_payment = UserPayment::where('user_id', $request->id)
                ->where('status', PAID_STATUS)
                ->orderBy('created_at', 'desc')
                ->first();

        if($user_payment) {

            // Check the subscription is already cancelled

            if($user_payment->is_cancelled == AUTORENEWAL_ENABLED) {
        
                $response_array = ['success' => 'false' , 'error_messages' => Helper::get_error_message(166) , 'error_code' => 166];

                return response()->json($response_array , 200);
            
            }

            $user_payment->is_cancelled = AUTORENEWAL_ENABLED;
          
            $user_payment->save();

            $subscription = $user_payment->subscription;

            $data = ['id'=>$request->id, 
                'subscription_id'=>$user_payment->subscription_id,
                'user_subscription_id'=>$user_payment->id,
                'title'=>$subscription ? $subscription->title : '',
                'description'=>$subscription ? $subscription->description : '',
                'popular_status'=>$subscription ? $subscription->popular_status : '',
                'plan'=>$subscription ? $subscription->plan : '',
                'amount'=>$user_payment->amount,
                'status'=>$user_payment->status,
                'expiry_date'=>date('d M Y', strtotime($user_payment->expiry_date)),
                'created_at'=>$user_payment->created_at->diffForHumans(),
                'currency'=>Setting::get('currency'),
                'payment_mode'=>$user_payment->payment_mode,
                'is_coupon_applied'=>$user_payment->is_coupon_applied,
                'coupon_code'=>$user_payment->coupon_code,
                'coupon_amount'=>$user_payment->coupon_amount,
                'subscription_amount'=>$user_payment->subscription_amount,
                'coupon_reason'=>$user_payment->coupon_reason,
                'is_cancelled'=>$user_payment->is_cancelled,
                'cancel_reason'=>$user_payment->cancel_reason
            ];

            $response_array = ['success'=> true, 'data'=>$data, 'message'=>tr('autorenewal_enable_success')];

        } else {

            $response_array = ['success'=> false, 'error_messages'=>Helper::get_error_message(167), 'error_code'=>167];

        }

        return response()->json($response_array);

    }

    /**
     * Function Name : check_coupon_applicable_to_user()
     *
     * To check the coupon code applicable to the user or not
     *
     * @created_by Vithya
     *
     * @updated - 
     *
     * @param objects $coupon - Coupon details
     *
     * @param objects $user - User details
     *
     * @return response of success/failure message
     */
    public function check_coupon_applicable_to_user($user, $coupon) {

        try {

            $sum_of_users = UserCoupon::where('coupon_code', $coupon->coupon_code)->sum('no_of_times_used');

            if ($sum_of_users < $coupon->no_of_users_limit) {


            } else {

                throw new Exception(tr('total_no_of_users_maximum_limit_reached'));
                
            }

            $user_coupon = UserCoupon::where('user_id', $user->id)
                ->where('coupon_code', $coupon->coupon_code)
                ->first();

            // If user coupon not exists, create a new row

            if ($user_coupon) {

                if ($user_coupon->no_of_times_used < $coupon->per_users_limit) {

                   // $user_coupon->no_of_times_used += 1;

                   // $user_coupon->save();

                    $response_array = ['success'=>true, 'message'=>tr('add_no_of_times_used_coupon'), 'code'=>2002];

                } else {

                    throw new Exception(tr('per_users_limit_exceed'));
                }

            } else {

                $response_array = ['success'=>true, 'message'=>tr('create_a_new_coupon_row'), 'code'=>2001];

            }

            return response()->json($response_array);

        } catch (Exception $e) {

            $response_array = ['success'=>false, 'error_messages'=>$e->getMessage()];

            return response()->json($response_array);
        }

    }

    /**
     * Function Name : apply_coupon_subscription()
     *
     * Apply coupon to subscription if the user having coupon codes
     *
     * @created Vithya
     *
     * @updated - -
     *
     * @param object $request - User details, subscription details
     *
     * @return response of coupon details with amount
     *
     */
    public function apply_coupon_subscription(Request $request) {

        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required|exists:coupons,coupon_code',  
            'subscription_id'=>'required|exists:subscriptions,id'          
        ], array(
            'coupon_code.exists' => tr('coupon_code_not_exists'),
            'subscription_id.exists' => tr('subscription_not_exists'),
        ));
        
        if ($validator->fails()) {

            $error_messages = implode(',', $validator->messages()->all());

            $response_array = array('success' => false, 'error_messages'=>$error_messages , 'error_code' => 101);

            return response()->json($response_array);
        }
        

        $model = Coupon::where('coupon_code', $request->coupon_code)->first();

        if ($model) {

            if ($model->status) {

                $user = User::find($request->id);

                $check_coupon = $this->check_coupon_applicable_to_user($user, $model)->getData();

                if ($check_coupon->success) {

                    if(strtotime($model->expiry_date) >= strtotime(date('Y-m-d'))) {

                        $subscription = Subscription::find($request->subscription_id);

                        if($subscription) {

                            if($subscription->status) {

                                $amount_convertion = $model->amount;

                                if ($model->amount_type == PERCENTAGE) {

                                    $amount_convertion = amount_convertion($model->amount, $subscription->amount);

                                }

                                if ($subscription->amount >= $amount_convertion && $amount_convertion > 0) {

                                    $amount = $subscription->amount - $amount_convertion;

                                    $response_array = ['success'=> true, 'data'=>['remaining_amount'=>$amount,
                                    'coupon_amount'=>$amount_convertion,
                                    'coupon_code'=>$model->coupon_code,
                                    'original_coupon_amount'=>$model->amount_type == PERCENTAGE ? $model->amount.'%' : Setting::get('currency').$model->amount]];

                                } else {

                                    // $response_array = ['success'=> false, 'error_messages'=>Helper::get_error_message(156), 'error_code'=>156];
                                    $amount = 0;
                                    $response_array = ['success'=> true, 'data'=>['remaining_amount'=>$amount,
                                    'coupon_amount'=>$amount_convertion,
                                    'coupon_code'=>$model->coupon_code,
                                    'original_coupon_amount'=> $model->amount_type == PERCENTAGE ? $model->amount.'%' : Setting::get('currency').$model->amount]];

                                }

                            } else {

                                $response_array = ['success'=> false, 'error_messages'=>Helper::get_error_message(170), 'error_code'=>170];

                            }

                        } else {

                            $response_array = ['success'=> false, 'error_messages'=>Helper::get_error_message(173), 'error_code'=>173];
                        }

                    } else {

                        $response_array = ['success'=> false, 'error_messages'=>Helper::get_error_message(173), 'error_code'=>173];

                    }

                } else {

                    $response_array = ['success'=> false, 'error_messages'=>$check_coupon->error_messages];
                }

            } else {

                $response_array = ['success'=> false, 'error_messages'=>Helper::get_error_message(168), 'error_code'=>168];
            }



        } else {

            $response_array = ['success'=> false, 'error_messages'=>Helper::get_error_message(174), 'error_code'=>174];

        }

        return response()->json($response_array);

    }

    /**
     * Function Name : apply_coupon_video_tapes()
     *
     * Apply coupon to PPV if the user having coupon codes
     *
     * @created Vithya
     *
     * @updated - -
     *
     * @param object $request - User details, ppv video details
     *
     * @return response of coupon details with amount
     *
     */
    public function apply_coupon_video_tapes(Request $request) {

        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required|exists:coupons,coupon_code',  
            'video_tape_id'=>'required|exists:video_tapes,id,publish_status,'.VIDEO_PUBLISHED.',is_approved,'.ADMIN_VIDEO_APPROVED_STATUS.',status,'.USER_VIDEO_APPROVED_STATUS,     
        ], array(
                'coupon_code.exists' => tr('coupon_code_not_exists'),
                'video_id.exists' => tr('video_not_exists'),
            ));
        
        if ($validator->fails()) {

            $error_messages = implode(',', $validator->messages()->all());

            $response_array = array('success' => false, 'error_messages'=>$error_messages , 'error_code' => 101);

            return response()->json($response_array);
        }
        
        $model = Coupon::where('coupon_code', $request->coupon_code)->first();

        if ($model) {

            if ($model->status) {

                $user = User::find($request->id);

                $vod_video = VideoTape::where('id', $request->video_tape_id)->first();

                $check_coupon = $this->check_coupon_applicable_to_user($user, $model)->getData();

                if ($check_coupon->success) {

                    if(strtotime($model->expiry_date) >= strtotime(date('Y-m-d'))) {

                        $amount_convertion = $model->amount;

                        if ($model->amount_type == PERCENTAGE) {

                            $amount_convertion = amount_convertion($model->amount, $vod_video->ppv_amount);

                        }

                        if ($vod_video->ppv_amount >= $amount_convertion && $amount_convertion > 0) {

                            $amount = $vod_video->ppv_amount - $amount_convertion;

                            $response_array = ['success'=> true, 'data'=>[
                                'remaining_amount'=>$amount,
                                'coupon_amount'=>$amount_convertion,
                                'coupon_code'=>$model->coupon_code,
                                'original_coupon_amount'=> $model->amount_type == PERCENTAGE ? $model->amount.'%' : Setting::get('currency').$model->amount
                                ]];

                        } else {

                            $amount = $vod_video->ppv_amount - $amount_convertion;

                            $response_array = ['success'=> true, 'data'=>[
                                'remaining_amount'=>0,
                                'coupon_amount'=>$amount_convertion,
                                'coupon_code'=>$model->coupon_code,
                                'original_coupon_amount'=> $model->amount_type == PERCENTAGE ? $model->amount.'%' : Setting::get('currency').$model->amount
                                ]];

                        }
                       

                    } else {

                        $response_array = ['success'=> false, 'error_messages'=>Helper::get_error_message(173), 'error_code'=>173];

                    }

                } else {

                    $response_array = ['success'=> false, 'error_messages'=>$check_coupon->error_messages];

                }

            } else {

                $response_array = ['success'=> false, 'error_messages'=>Helper::get_error_message(168), 'error_code'=>168];
            }            

        } else {

            $response_array = ['success'=> false, 'error_messages'=>Helper::get_error_message(174), 'error_code'=>174];

        }

        return response()->json($response_array);

    }


    /**
     * Function : custom_live_videos()
     *
     * @created Vithya R 
     *
     * @updated 
     *
     * @usage used to return list of live videos
     */
    public function custom_live_videos(Request $request) {

        try {

            $base_query = CustomLiveVideo::liveVideoResponse()->where('status', APPROVED)->orderBy('created_at', 'desc');

            if ($request->has('custom_live_video_id')) {

                $base_query->whereNotIn('id', [$request->custom_live_video_id]);

            }

            $take = $request->take ?: Setting::get('admin_take_count' ,12);

            $custom_live_videos = $base_query->skip($request->skip)->take($take)->get();

            $response_array = ['success' => true , 'live' => $custom_live_videos];

            return response()->json($response_array , 200);

        } catch(Exception $e) {

            $error_messages = $e->getMessage();

            $code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $code];

            return response()->json($response_array);

        }  

    }

    /**
     *
     * Function name: custom_live_videos_view()
     *
     * @uses get the details of the selected custom video (Live TV)
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param integer custom_live_video_id
     *
     * @return JSON Response
     */

    public function custom_live_videos_view(Request $request) {

        try {

            $custom_live_video_details = CustomLiveVideo::where('id', $request->custom_live_video_id)
                                            ->where('status' , APPROVED)
                                            ->liveVideoResponse()
                                            ->first();

            $suggestions = CustomLiveVideo::where('id','!=', $request->custom_live_video_id)
                                    ->where('status' , APPROVED)
                                    ->liveVideoResponse()
                                    ->get();

            if (!$custom_live_video_details) {

                throw new Exception(tr('custom_live_video_not_found'), 101);  

            }

            $response_array = ['success' => true, 'model' => $custom_live_video_details , 'suggestions' => $suggestions];

            return response()->json($response_array,200);

        } catch(Exception $e) {

            $error_messages = $e->getMessage();

            $code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $code];

            return response()->json($response_array);

        }  
    
    } 

    /**
     *
     * Function name: playlists()
     *
     * @uses get the playlists
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param integer channel_id (Optional)
     *
     * @return JSON Response
     */

    public function playlists(Request $request) {

        try {

            $validator = Validator::make($request->all(), [
                'skip' => 'numeric',
                'channel_id' => 'exists:channels,id',
                'view_type' => 'required'
            ]);

            if($validator->fails()) {

                $error_messages = implode(',', $validator->messages()->all());
                
                throw new Exception($error_messages, 101);
                
            }

            // Guests can access only channel playlists  - Required channel_id

            // Logged in users playlists - required - Required viewer_type 

            // Logged in user access other channel playlist  - required channel_id

            // Logged in user access owned channel playlist - required channel_id

            if(($request->id && $request->view_type == VIEW_TYPE_VIEWER) || (!$request->id && $request->view_type == VIEW_TYPE_VIEWER)) {

                if(!$request->channel_id) {

                    throw new Exception("Channel ID is required", 101);
                    
                }
                
            }

            $base_query = Playlist::where('playlists.status', APPROVED)
                                ->orderBy('playlists.updated_at', 'desc');

            // While owner access the users playlists

            if($request->view_type == VIEW_TYPE_OWNER && !$request->channel_id) {

                $base_query = $base_query->where('playlists.user_id', $request->id)->where('channel_id', 0);

            }

            // While owner access the channel playlists

            if($request->view_type == VIEW_TYPE_OWNER && $request->channel_id) {

                $base_query = $base_query->where('playlists.user_id', $request->id);
            }

            if($request->channel_id) {

                $base_query = $base_query->where('playlists.channel_id', $request->channel_id);
            }          

            $skip = $this->skip ?: 0;

            $take = $this->take ?: TAKE_COUNT;

            $playlists = $base_query->CommonResponse()->skip($skip)->take($take)->get();

            foreach ($playlists as $key => $playlist_details) {

                $first_video_from_playlist = PlaylistVideo::where('playlist_videos.playlist_id', $playlist_details->playlist_id)
                                            ->leftJoin('video_tapes', 'video_tapes.id', '=', 'playlist_videos.video_tape_id')
                                            ->select('video_tapes.id as video_tape_id', 'video_tapes.default_image as picture')
                                            ->first();

                $playlist_details->picture = $first_video_from_playlist ? $first_video_from_playlist->picture : asset('images/playlist.png');

                $check_video = PlaylistVideo::where('playlist_id', $playlist_details->playlist_id)->where('video_tape_id', $request->video_tape_id)->count();


                $playlist_details->is_selected = $check_video ? YES : NO;

                 // Total Video count start

                $total_video_query = PlaylistVideo::where('playlist_id', $playlist_details->playlist_id);

                if($request->id) {

                    $flag_video_ids = flag_videos($request->id);

                    if($flag_video_ids) {

                        $playlist_details->total_videos = $total_video_query->whereNotIn('playlist_videos.video_tape_id', $flag_video_ids);

                    }

                }

                $playlist_details->total_videos = $total_video_query->count();
               
                // Total Video count end

                $playlist_details->share_link = url('/');
            
            }

            $response_array = ['success' => true, 'data' => $playlists];

            return response()->json($response_array);

        } catch(Exception $e) {

            $error_messages = $e->getMessage();

            $code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $code];

            return response()->json($response_array);

        }
    
    }

    /**
     *
     * Function name: playlists_save()
     *
     * @uses get the playlists
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param integer channel_id (Optional)
     *
     * @return JSON Response
     */

    public function playlists_save(Request $request) {

        try {

            DB::beginTransaction();

            $validator = Validator::make($request->all(),[
                'title' => 'required|max:255',
                'playlist_id' => 'exists:playlists,id,user_id,'.$request->id,
                'channel_id' => 'exists:channels,id'
            ],
            [
                'exists' => Helper::get_error_message(175)
            ]);

            if($validator->fails()) {

                $error_messages = implode(',',$validator->messages()->all());

                throw new Exception($error_messages, 101);
                
            }

            $playlist_details = Playlist::where('id', $request->playlist_id)->first();

            $message = Helper::get_message(129);

            if(!$playlist_details) {

                $message = Helper::get_message(128);

                $playlist_details = new Playlist;
    
                $playlist_details->status = APPROVED;

                $playlist_details->playlist_display_type = PLAYLIST_DISPLAY_PRIVATE;

                $playlist_details->playlist_type = PLAYLIST_TYPE_USER;

            }

            $playlist_details->user_id = $request->id;

            $playlist_details->channel_id = $request->channel_id ?: "";

            $playlist_details->title = $playlist_details->description = $request->title ?: "";

            if($playlist_details->save()) {

                DB::commit();

                $playlist_details = $playlist_details->where('id', $playlist_details->id)->CommonResponse()->first();

                $response_array = ['success' => true, 'message' => $message, 'data' => $playlist_details];

                return response()->json($response_array, 200);

            } else {

                throw new Exception(Helper::get_error_message(179), 179);

            }

        } catch(Exception $e) {

            DB::rollback();

            $error_messages = $e->getMessage();

            $code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $code];

            return response()->json($response_array);

        }
    
    }

    /**
     *
     * Function name: playlists_add_video()
     *
     * @uses get the playlists
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param integer channel_id (Optional)
     *
     * @return JSON Response
     */

    public function playlists_video_status(Request $request) {
       
        try {

            DB::beginTransaction();

            $playlist_video_details = PlaylistVideo::where('video_tape_id', $request->video_tape_id)
                                        ->where('user_id', $request->id)
                                        ->first();
            // if($playlist_video_details) {

            //     $message = Helper::get_message(127); $code = 127;

            //     $playlist_video_details->delete();

            // } else {

                $validator = Validator::make($request->all(),[
                    'playlist_id' => 'required',
                    'video_tape_id' => 'required|exists:video_tapes,id,status,'.APPROVED,
                ]);

                if($validator->fails()) {

                    $error_messages = implode(',',$validator->messages()->all());

                    throw new Exception($error_messages, 101);
                    
                }

                // check the video added in spams (For Viewer)

                if(!$request->channel_id && $request->id) {

                    $flagged_videos = getFlagVideos($request->id);

                    if(in_array($request->video_tape_id, $flagged_videos)) {

                        throw new Exception(tr('video_in_spam_list'), 101);
                        
                    }
                }

                // Spam check end

                $playlist_ids = explode(',', $request->playlist_id);

                PlaylistVideo::whereNotIn('playlist_id', $playlist_ids)->where('video_tape_id', $request->video_tape_id)
                                ->where('user_id', $request->id)
                                ->delete();

                $total_playlists_update = 0;

                foreach ($playlist_ids as $key => $playlist_id) {

                    // Check the playlist id belongs to the logged user

                    $playlist_details = Playlist::where('id', $playlist_id)->where('user_id', $request->id)->count();

                    if($playlist_details) {

                        $playlist_video_details = PlaylistVideo::where('video_tape_id', $request->video_tape_id)
                                            ->where('user_id', $request->id)
                                            ->where('playlist_id', $playlist_id)
                                            ->first();
                        if(!$playlist_video_details) {

                            $playlist_video_details = new PlaylistVideo;
     
                        }

                        $playlist_video_details->user_id = $request->id;

                        $playlist_video_details->playlist_id = $playlist_id;

                        $playlist_video_details->video_tape_id = $request->video_tape_id;

                        $playlist_video_details->status = APPROVED;

                        $playlist_video_details->save();

                        $total_playlists_update++;

                    }
                
                }

            // }

            DB::commit();

            $code = $total_playlists_update > 0 ? 126 : 132;

            $message = Helper::get_message($code);

            $response_array = ['success' => true, 'message' => $message, 'code' => $code];

            return response()->json($response_array);

        } catch(Exception $e) {

            DB::rollback();

            $error_messages = $e->getMessage();

            $error_code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $error_code];

            return response()->json($response_array);

        }
    
    }


    /**
     *
     * Function name: playlists_view()
     *
     * @uses get the playlists
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param integer channel_id (Optional)
     *
     * @return JSON Response
     */

    public function playlists_view(Request $request) {

        try {

            // Check the playlist record based on the view type

            $playlist_base_query = Playlist::where('playlists.status', APPROVED)
                                ->where('playlists.id', $request->playlist_id);

            // check the playlist belongs to owner

            if($request->view_type == VIEW_TYPE_OWNER) {

                $playlist_base_query = $playlist_base_query->where('playlists.user_id', $request->id);
            }

            $playlist_details = $playlist_base_query->CommonResponse()->first();

            if(!$playlist_details) {

                throw new Exception(Helper::get_error_message(175), 175);
                
            }

            $skip = $this->skip ?: 0; $take = $this->take ?: TAKE_COUNT;

            $video_tape_base_query = PlaylistVideo::where('playlist_videos.playlist_id', $request->playlist_id);

            // Check the flag videos

            if($request->id) {

                // Check any flagged videos are present
                $flagged_videos = getFlagVideos($request->id);

                if($flagged_videos) {

                    $video_tape_base_query->whereNotIn('playlist_videos.video_tape_id', $flagged_videos);

                }

            }

            $video_tape_ids = $video_tape_base_query->skip($skip)
                                ->take($take)
                                ->pluck('playlist_videos.video_tape_id')
                                ->toArray();

            $video_tapes = V5Repo::video_list_response($video_tape_ids, $request->id);

            $playlist_details->picture = asset('images/playlist.png');

            $playlist_details->share_link = url('/');

            $playlist_details->is_my_channel = NO;

            if($playlist_details->channel_id) {

                if($channel_details = Channel::find($playlist_details->channel_id)) {

                    $playlist_details->is_my_channel = $request->id == $channel_details->user_id ? YES : NO;
                }
            }

            $playlist_details->total_videos = count($video_tapes);

            $playlist_details->video_tapes = $video_tapes;

            $data = $playlist_details;

            $data['video_tapes'] = $video_tapes;

            $response_array = ['success' => true, 'data' => $data];

            return response()->json($response_array);

        } catch(Exception $e) {

            $error_messages = $e->getMessage();

            $error_code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $error_code];

            return response()->json($response_array);

        }
    
    }

    /**
     *
     * Function name: playlists_video_remove()
     *
     * @uses Remove the video from playlist
     *
     * @created Aravinth R
     *
     * @updated vithya R
     *
     * @param integer video_tape_id (Optional)
     *
     * @return JSON Response
     */

    public function playlists_video_remove(Request $request) {

        try {

            DB::beginTransaction();

            $validator = Validator::make($request->all(),[
                    'playlist_id' =>'required|exists:playlists,id',
                    'video_tape_id' => 'required|exists:video_tapes,id',
                ],
                [
                    'exists' => 'The :attribute doesn\'t exists please add to playlist',
                ]
            );

            if ($validator->fails()) {

                $error_messages = implode(',', $validator->messages()->all());

                throw new Exception($error_messages, 101);
                
            }

            $playlist_video_details = PlaylistVideo::where('playlist_id',$request->playlist_id)->where('user_id', $request->id)->where('video_tape_id',$request->video_tape_id)->first();

            if(!$playlist_video_details) {

                throw new Exception(Helper::get_error_message(180), 180);

            }

            $playlist_video_details->delete();

            DB::commit();

            $response_array = ['success' => true, 'message' => Helper::get_message(127), 'code' => 127];

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            $error_messages = $e->getMessage();

            $error_code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $error_code];

            return response()->json($response_array);

        }
    
    }

    /**
     *
     * Function name: playlists_video_save()
     *
     * @uses add the video to playlist
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param integer video_tape_id, playlistid
     *
     * @return JSON Response
     */

    public function playlists_video_save(Request $request) {

        try {

            DB::beginTransaction();

            $validator = Validator::make($request->all(),[
                    'playlist_id' =>'required|exists:playlists,id',
                    'video_tape_id' => 'required|exists:video_tapes,id',
                ],
                [
                    'exists' => 'The :attribute doesn\'t exists please add to playlist',
                ]
            );

            if ($validator->fails()) {

                $error_messages = implode(',', $validator->messages()->all());

                throw new Exception($error_messages, 101);
                
            }

            // check the video added in spams (For Viewer)

            if(!$request->channel_id && $request->id) {

                $flagged_videos = getFlagVideos($request->id);

                if(in_array($request->video_tape_id, $flagged_videos)) {

                    throw new Exception(tr('video_in_spam_list'), 101);
                    
                }
            }

            // Spam check end
                
            $playlist_video_details = new PlaylistVideo;

            $playlist_video_details->user_id = $request->id;

            $playlist_video_details->playlist_id = $request->playlist_id;

            $playlist_video_details->video_tape_id = $request->video_tape_id;

            $playlist_video_details->status = APPROVED;

            $playlist_video_details->save();

            DB::commit();

            $response_array = ['success' => true, 'message' => Helper::get_message(126), 'code' => 126];

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            $error_messages = $e->getMessage();

            $error_code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $error_code];

            return response()->json($response_array);

        }
    
    }

    /**
     * Function Name : playlists_delete()
     *
     * @uses used to delete the user selected playlist
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param integer $playlist_id
     *
     * @return JSON Response
     *
     */
    public function playlists_delete(Request $request) {

        try {

            DB::beginTransaction();

            $validator = Validator::make($request->all(),[
                    'playlist_id' =>'required|exists:playlists,id',
                ],
                [
                    'exists' => 'The :attribute doesn\'t exists please add to playlist',
                ]
            );

            if ($validator->fails()) {

                $error_messages = implode(',', $validator->messages()->all());

                throw new Exception($error_messages, 101);
                
            }

            $playlist_details = Playlist::where('id',$request->playlist_id)->where('user_id', $request->id)->first();

            if(!$playlist_details) {

                throw new Exception(Helper::get_error_message(180), 180);

            }

            $playlist_details->delete();

            DB::commit();

            $response_array = ['success' => true, 'message' => Helper::get_message(131), 'code' => 131];

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            DB::rollback();

            $error_messages = $e->getMessage();

            $error_code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $error_code];

            return response()->json($response_array);
        }
    }

    /**
     * Function Name : bell_notifications()
     *
     * @uses list of notifications for user
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param integer $id
     *
     * @return JSON Response
     */

    public function bell_notifications(Request $request) {

        try {

            $skip = $this->skip ?: 0; $take = $this->take ?: TAKE_COUNT;

            $bell_notifications = BellNotification::where('to_user_id', $request->id)
                                        ->select('notification_type', 'channel_id', 'video_tape_id', 'message', 'status as notification_status', 'from_user_id', 'to_user_id', 'created_at')
                                        ->skip($skip)
                                        ->take($take)
                                        ->orderBy('bell_notifications.created_at', 'desc')
                                        ->get();

            foreach ($bell_notifications as $key => $bell_notification_details) {

                $picture = asset('placeholder.png');

                if($bell_notification_details->notification_type == BELL_NOTIFICATION_NEW_SUBSCRIBER) {

                    $user_details = User::find($bell_notification_details->from_user_id);

                    $picture = $user_details ? $user_details->picture : $picture;

                } else {

                    $video_tape_details = VideoTape::find($bell_notification_details->video_tape_id);

                    $picture = $video_tape_details ? $video_tape_details->default_image : $picture;

                }

                $bell_notification_details->picture = $picture;

                unset($bell_notification_details->from_user_id);

                unset($bell_notification_details->to_user_id);
            }

            $response_array = ['success' => true, 'data' => $bell_notifications];

            return response()->json($response_array);

        } catch(Exception $e) {

            $error_messages = $e->getMessage();

            $error_code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $error_code];

            return response()->json($response_array);

        }   
    
    }

    /**
     * Function Name : bell_notifications_update()
     *
     * @uses list of notifications for user
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param integer $id
     *
     * @return JSON Response
     */

    public function bell_notifications_update(Request $request) {

        try {

            DB::beginTransaction();

            $bell_notifications = BellNotification::where('to_user_id', $request->id)->update(['status' => BELL_NOTIFICATION_STATUS_READ]);

            DB::commit();

            $response_array = ['success' => true, 'message' => Helper::get_message(130), 'code' => 130];

            return response()->json($response_array, 200);


        } catch(Exception $e) {

            DB::rollback();

            $error_messages = $e->getMessage();

            $error_code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $error_code];

            return response()->json($response_array);

        } 
    
    }

    /**
     * Function Name : bell_notifications_count()
     * 
     * @uses Get the notification count
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param object $request - As of no attribute
     * 
     * @return response of boolean
     */
    public function bell_notifications_count(Request $request) {

        // TODO
            
        $bell_notifications_count = BellNotification::where('status', BELL_NOTIFICATION_STATUS_UNREAD)->where('to_user_id', $request->id)->count();

        $response_array = ['success' => true, 'count' => $bell_notifications_count];

        return response()->json($response_array);

    }

    /**
     * Function Name : video_tapes_youtube_grapper_save()
     * 
     * Get the videos based on the channel ID from youtube API 
     *
     * @created Vidhya R 
     *
     * @updated Vidhya R
     *
     * @param form details
     * 
     * @return redirect to page with success or error
     *
     */

    public function video_tapes_youtube_grapper_save(Request $request) {

        // Log::info("Request".print_r($request->all(), true));

        try {

            DB::beginTransaction();

            $validator = Validator::make($request->all(), [

                'youtube_channel_id' => 'required',
                'channel_id' => 'required|integer|exists:channels,id,user_id,'.$request->id,
            ],
            [
                'youtube_channel_id' => tr('youtube_grabber_channel_id_not_found')
            ]);

            if($validator->fails()) {
            
                $error_messages = implode(',',$validator->messages()->all());

                throw new Exception($error_messages, 101);

            }

            // Check the channel is exists in YouTube

            $youtube_channel = \Youtube::getChannelById($request->youtube_channel_id);

            if($youtube_channel == false) {

                throw new Exception(tr('youtube_grabber_channel_id_not_found'), 101);
                
            }

            $channel_details = Channel::where('id', $request->channel_id)->where('is_approved' , APPROVED)->first();

            if(!$channel_details) {

                throw new Exception(tr('channel_not_found'), 101);                
            }

            $channel_details->youtube_channel_id = $request->youtube_channel_id;

            $channel_details->youtube_channel_updated_at = date('Y-m-d H:i:s');

            $channel_details->save();

            $user_details = User::where('id', $request->id)->first();

            if(!$user_details) {

                throw new Exception(tr('user_not_found'), 101);
                
            }

            $youtube_videos = \Youtube::listChannelVideos($request->youtube_channel_id, 40); 

            foreach ($youtube_videos as $key => $youtube_video_details) {

                $youtube_video_details = \Youtube::getVideoInfo($youtube_video_details->id->videoId);

                if($youtube_video_details) {

                    // check the youtube video already exists

                    $check_video_tape_details = $video_tape_details = VideoTape::where('youtube_video_id' , $youtube_video_details->id)->first();

                    if(count($check_video_tape_details) == 0) {

                        $video_tape_details = new VideoTape;

                        $video_tape_details->publish_time = date('Y-m-d H:i:s');

                        $video_tape_details->duration = "00:00:10";

                        $video_tape_details->reviews = "YOUTUBE";

                        $video_tape_details->video = "https://youtu.be/".$youtube_video_details->id;

                        $video_tape_details->ratings = 5;

                        $video_tape_details->video_publish_type = PUBLISH_NOW;

                        $video_tape_details->publish_status = VIDEO_PUBLISHED;

                        $video_tape_details->is_approved = ADMIN_VIDEO_APPROVED_STATUS;
                        
                        $video_tape_details->status = USER_VIDEO_APPROVED_STATUS;

                        $video_tape_details->user_id = $request->id;

                        $video_tape_details->channel_id = $request->channel_id;

                        $category_details = Category::where('status', APPROVED)->first();

                        $category_id = $request->category_id ?: ($category_details ? $category_details->id : 0);

                        $video_tape_details->category_id = $category_id;

                    }

                    $video_tape_details->title = $youtube_video_details->snippet->title;

                    $video_tape_details->description = $youtube_video_details->snippet->description;

                    $video_tape_details->youtube_channel_id = $youtube_video_details->snippet->channelId;

                    $video_tape_details->youtube_video_id = $youtube_video_details->id;

                    $default_image = isset($youtube_video_details->snippet->thumbnails->maxres) ? $youtube_video_details->snippet->thumbnails->maxres->url : $youtube_video_details->snippet->thumbnails->default->url;


                    $video_tape_details->default_image = $default_image;

                    $video_tape_details->compress_status = DEFAULT_TRUE;

                    $video_tape_details->watch_count = $youtube_video_details->statistics->viewCount;

                    $video_tape_details->save();


                    $second_image = $youtube_video_details->snippet->thumbnails->default->url;

                    $third_image = $youtube_video_details->snippet->thumbnails->high->url;

                    $check_video_image_2 = $video_image_2 = VideoTapeImage::where('position' , 2)->where('video_tape_id' , $video_tape_details->id)->first();

                    if(!$check_video_image_2) {

                        $video_image_2 = new VideoTapeImage;

                    }

                    $video_image_2->image = $second_image;

                    $video_image_2->is_default = 0;

                    $video_image_2->position = 2;

                    $video_image_2->save();

                    $check_video_image_3 = $video_image_3 = VideoTapeImage::where('position' , 3)->where('video_tape_id' , $video_tape_details->id)->first();

                    if(!$check_video_image_3) {

                        $video_image_3 = new VideoTapeImage;

                    }

                    $video_image_3->image = $second_image;

                    $video_image_3->is_default = 0;

                    $video_image_3->position = 3;

                    $video_image_3->save();

                }
                
            }

            DB::commit();

            $response_array = ['success' => true, 'count' => count($youtube_videos)];

            return response()->json($response_array, 200);


        } catch(Exception $e) {

            DB::rollBack();

            $error_messages = $e->getMessage();

            $error_code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $error_code];

            return response()->json($response_array, 200);

        }
    
    }

    /**
     * Function Name : referrals()
     *
     * @uses signup user through referrals
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param string referral_code 
     *
     * @return redirect signup page
     */
    public function referrals(Request $request){

        try {

            $user_details =  User::find($request->id);

            $user_referrer_details = UserReferrer::where('user_id', $user_details->id)->first();

            if(!$user_referrer_details) {

                $user_referrer_details = new UserReferrer;

                $user_referrer_details->user_id = $user_details->id;

                $user_referrer_details->referral_code = uniqid();

                $user_referrer_details->total_referrals = $user_referrer_details->total_referrals_earnings = 0 ;

                $user_referrer_details->save();

            }

            unset($user_referrer_details->id);

            $referrals = Referral::where('parent_user_id', $user_details->id)->CommonResponse()->orderBy('created_at', 'desc')->get();

            foreach ($referrals as $key => $referral_details) {

                $user_details = User::find($referral_details->user_id);

                $referral_details->username = $referral_details->picture = "";

                if($user_details) {

                    $referral_details->username = $user_details->name ?: "";

                    $referral_details->picture = $user_details->picture ?: "";

                }

            }

            $user_referrer_details->currency = Setting::get('currency', '$');

            $user_referrer_details->referrals = $referrals;

            $response_array = ['success' => true, 'data' => $user_referrer_details];

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            $error_messages = $e->getMessage();

            $error_code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $error_code];

            return response()->json($response_array);

        }

    }

    /**
     * Function Name : referrals_check()
     *
     * @uses check valid referral
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param string referral_code 
     *
     * @return redirect signup page
     */
    public function referrals_check(Request $request){

        try {

            $validator = Validator::make($request->all(),[
                    'referral_code' =>'required|exists:user_referrers,referral_code',
                ],
                [
                    'exists' => Helper::get_error_message(50101),
                ]
            );

            if ($validator->fails()) {

                $error_messages = implode(',', $validator->messages()->all());

                throw new Exception($error_messages, 101);
                
            }

            $check_referral_code =  UserReferrer::where('referral_code', $request->referral_code)->where('status', APPROVED)->first();

            if(!$check_referral_code) {

                throw new Exception(Helper::get_error_message(50101), 50101);
                
            }

            $response_array = ['success' => true, 'message' => Helper::get_message(50001), 'code' => 50001];

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            $error_messages = $e->getMessage();

            $error_code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $error_code];

            return response()->json($response_array, 200);

        }
    }

    /**
     *
     * Function name: video_tapes_revenues()
     *
     * @uses Video revenue details
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param integer channel_id 
     *
     * @return JSON Response
     */

    public function video_tapes_revenues(Request $request) {

        try {

            $video_tape_details = VideoTape::where('id', $request->video_tape_id)->first();

            if(!$video_tape_details) {

                throw new Exception(Helper::get_error_message(50104), 50104);
                
            }

            $data = new \stdClass;

            $data->currency = Setting::get('currency');
            
            $data->is_pay_per_view = $video_tape_details->is_pay_per_view;

            $data->ppv_revenue = $video_tape_details->user_ppv_amount ?: 0.00;

            $data->ads_revenue = $video_tape_details->amount ?: 0.00;

            $data->total_revenue = $video_tape_details->ads_revenue + $video_tape_details->user_ppv_amount;

            $data->watch_count = $video_tape_details->watch_count;

            
            $response_array = ['success' => true, 'data' => $data];

            return response()->json($response_array, 200);


        } catch(Exception $e) {

            $error_messages = $e->getMessage(); $error_code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $error_code];

            return response()->json($response_array);

        }
    
    }

    /**
     *
     * Function name: video_tapes_status()
     *
     * @uses upate the ppv status
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param integer channel_id 
     *
     * @return JSON Response
     */

    public function video_tapes_status(Request $request) {

        try {

            DB::beginTransaction();

            $video_tape_details = VideoTape::where('id', $request->video_tape_id)->first();

            $channel_details = Channel::find($request->channel_id);

            if(!$channel_details || !$video_tape_details) {

                throw new Exception(Helper::get_error_message(50104), 50104);
                
            }

            $video_tape_details->is_approved = $video_tape_details->is_approved ? USER_VIDEO_DECLINED_STATUS : USER_VIDEO_APPROVED_STATUS;

            if($video_tape_details->save()) {

                DB::commit();

                $code = $video_tape_details->is_approved == USER_VIDEO_APPROVED_STATUS ? 50002 : 50003;

                $message = Helper::get_message($code);

                $response_array = ['success' => true, 'message' => $message, 'code' => $code];

                return response()->json($response_array, 200);
   
            }

            throw new Exception(Helper::get_error_message(50105), 50105);
            

        } catch(Exception $e) {

            DB::rollback();

            $error_messages = $e->getMessage(); $error_code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $error_code];

            return response()->json($response_array);

        }
    
    }


    /**
     *
     * Function name: video_tapes_ppv_status()
     *
     * @uses upate the ppv status
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param integer channel_id 
     *
     * @return JSON Response
     */

    public function video_tapes_ppv_status(Request $request) {

        try {

            DB::beginTransaction();

            $video_tape_details = VideoTape::where('id', $request->video_tape_id)->first();

            $channel_details = Channel::find($request->channel_id);

            if(!$channel_details || !$video_tape_details) {

                throw new Exception(Helper::get_error_message(50104), 50104);
                
            }

            $video_tape_details->ppv_created_by = $request->id;

            $video_tape_details->ppv_amount = $request->ppv_amount ?: 0.00;

            $video_tape_details->type_of_subscription = $request->type_of_subscription;

            $video_tape_details->is_pay_per_view = $request->status ? PPV_ENABLED : PPV_DISABLED;

            if($video_tape_details->save()) {

                DB::commit();

                $code = $video_tape_details->is_pay_per_view == PPV_ENABLED ? 50004 : 50005;

                $message = Helper::get_message($code);

                $response_array = ['success' => true, 'message' => $message, 'code' => $code];

                return response()->json($response_array, 200);
   
            }

            throw new Exception(Helper::get_error_message(50106), 50106);

        } catch(Exception $e) {

            DB::rollback();

            $error_messages = $e->getMessage(); $error_code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $error_code];

            return response()->json($response_array);

        }
    
    }

    /**
     *
     * Function name: video_tapes_delete()
     *
     * @uses delete video
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param integer video_tape_id 
     *
     * @return json response
     */

    public function video_tapes_delete(Request $request) {

        try {

            DB::beginTransaction();

            $video_tape_details = VideoTape::where('id', $request->video_tape_id)->first();

            $channel_details = Channel::find($request->channel_id);

            if(!$channel_details || !$video_tape_details) {

                throw new Exception(Helper::get_error_message(50104), 50104);
                
            }

            if($video_tape_details->delete()) {

                DB::commit();

                $code = 50006;

                $message = Helper::get_message($code);

                $response_array = ['success' => true, 'message' => $message, 'code' => $code];

                return response()->json($response_array, 200);
   
            }

            throw new Exception(Helper::get_error_message(50107), 50107);

        } catch(Exception $e) {

            DB::rollback();

            $error_messages = $e->getMessage(); $error_code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $error_code];

            return response()->json($response_array);

        }
    
    }

}
