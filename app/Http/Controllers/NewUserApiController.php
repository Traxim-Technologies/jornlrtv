<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB, Log, Hash, Validator, Exception, Setting;

use App\Helpers\Helper, App\Helpers\CommonHelper;


use App\Repositories\VideoTapeRepository as VideoRepo;

use App\Repositories\CommonRepository as CommonRepo;

use App\Repositories\PaymentRepository as PaymentRepo;

use App\Repositories\UserRepository as UserRepo;

use App\Repositories\V5Repository as V5Repo;


use App\Jobs\sendPushNotification;

use App\Jobs\BellNotificationJob;


use App\User, App\Card, App\Wishlist;

use App\BellNotification;

use App\Category, App\SubCategory;

use App\StaticPage;

class NewUserApiController extends Controller
{
    protected $skip, $take;

	public function __construct(Request $request) {

        $this->middleware('ChannelOwner' , ['only' => ['video_tapes_status', 'video_tapes_delete', 'video_tapes_ppv_status','video_tapes_publish_status']]);

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);
    }

    /**
     * @method register()
     *
     * @uses Registered user can register through manual or social login
     * 
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param Form data
     *
     * @return Json response with user details
     */
    public function register(Request $request) {

        try {

            DB::beginTransaction();

            // Validate the common and basic fields

            $basic_validator = Validator::make($request->all(),
                [
                    'device_type' => 'required|in:'.DEVICE_ANDROID.','.DEVICE_IOS.','.DEVICE_WEB,
                    'device_token' => 'required',
                    'login_by' => 'required|in:manual,facebook,google',
                ]
            );

            if($basic_validator->fails()) {

                $error = implode(',', $basic_validator->messages()->all());

                throw new Exception($error , 101);

            }

            $allowed_social_logins = ['facebook','google'];

            if(in_array($request->login_by,$allowed_social_logins)) {

                // validate social registration fields

                $social_validator = Validator::make($request->all(),
                            [
                                'social_unique_id' => 'required',
                                'name' => 'required|max:255|min:2',
                                'email' => 'required|email|max:255',
                                'mobile' => 'digits_between:6,13',
                                'picture' => '',
                                'gender' => 'in:male,female,others',
                            ]
                        );

                if($social_validator->fails()) {

                    $error = implode(',', $social_validator->messages()->all());

                    throw new Exception($error , 101);

                }

            } else {

                // Validate manual registration fields

                $manual_validator = Validator::make($request->all(),
                    [
                        'name' => 'required|max:255',
                        'email' => 'required|email|max:255|min:2',
                        'password' => 'required|min:6',
                        'picture' => 'mimes:jpeg,jpg,bmp,png',
                    ]
                );

                // validate email existence

                $email_validator = Validator::make($request->all(),
                    [
                        'email' => 'unique:users,email',
                    ]
                );

                if($manual_validator->fails()) {

                    $error = implode(',', $manual_validator->messages()->all());

                    throw new Exception($error , 101);
                    
                } else if($email_validator->fails()) {

                	$error = implode(',', $email_validator->messages()->all());

                    throw new Exception($error , 101);

                } 

            }

            $user_details = User::where('email' , $request->email)->first();

            $send_email = DEFAULT_FALSE;

            // Creating the user

            if(!$user_details) {

                $user_details = new User;

                register_mobile($request->device_type);

                $send_email = DEFAULT_TRUE;

                $user_details->picture = asset('placeholder.jpg');

                // $user_details->registration_steps = 1;

            } else {

                if(in_array($user_details->status , [USER_PENDING , USER_DECLINED])) {

                    throw new Exception(CommonHelper::error_message(502) , 502);
                
                }

            }

            if($request->has('name')) {

                $user_details->name = $request->name;

            }

            if($request->has('email')) {

                $user_details->email = $request->email;

            }

            if($request->has('mobile')) {

                $user_details->mobile = $request->mobile;

            }

            if($request->has('password')) {

                $user_details->password = Hash::make($request->password ?: "123456");

            }

            if($request->has('dob')) {

                $user_details->dob = date("Y-m-d" , strtotime($request->dob));
            }

            if ($user_details->dob) {

                if ($user_details->dob != '0000-00-00') {

                    $from = new \DateTime($user_details->dob);

                    $to   = new \DateTime('today');

                    $user_details->age_limit = $from->diff($to)->y;

                }

            }

            $user_details->gender = $request->gender ?: "male";

            // $user_details->payment_mode = COD;

            $user_details->token = Helper::generate_token();

            $user_details->token_expiry = Helper::generate_token_expiry();

            $check_device_exist = User::where('device_token', $request->device_token)->first();

            if($check_device_exist) {

                $check_device_exist->device_token = "";

                $check_device_exist->save();
            }

            $user_details->device_token = $request->device_token ?: "";

            $user_details->device_type = $request->device_type ?: DEVICE_WEB;

            $user_details->login_by = $request->login_by ?: 'manual';

            $user_details->social_unique_id = $request->social_unique_id ?: '';

            // Upload picture

            if($request->login_by == "manual") {

                if($request->hasFile('picture')) {

                    $user_details->picture = Helper::upload_file($request->file('picture') , PROFILE_PATH_USER);

                }

            } else {

                $user_details->is_verified = USER_EMAIL_VERIFIED; // Social login

                $user_details->picture = $request->picture ?: $user_details->picture;

            }   


            if($user_details->save()) {

                // Send welcome email to the new user:

                if($send_email) {

                	// Check the default subscription and save the user type 

                    if($request->referral_code) {

                        UserRepo::referral_register($request->referral_code, $user_details);
                    }

                    user_type_check($user_details->id);


                    if($user_details->login_by == 'manual') {

                        $user_details->password = $request->password;

                        $subject = tr('user_welcome_title').' '.Setting::get('site_name', 'StreamTube');

                        $email_data = $user_details;

                        $page = "emails.welcome";

                        $email = $user_details->email;

                        $email_send_response = CommonHelper::send_email($page,$subject,$email,$email_data);

                        // No need to throw error. For forgot password we need handle the error response

                        if($email_send_response) {

                            if($email_send_response->success) {

                            } else {

                                $error = $email_send_response->error;

                                Log::info("Registered EMAIL Error".print_r($error , true));
                                
                            }

                        }

                    }

                }

                if(in_array($user_details->status , [USER_DECLINED , USER_PENDING])) {
                
                    $response = ['success' => false , 'error' => CommonHelper::error_message(1000) , 'error_code' => 1000];

                    DB::commit();

                    return response()->json($response, 200);
               
                }

                if($user_details->is_verified == USER_EMAIL_VERIFIED) {

                	$data = User::CommonResponse()->find($user_details->id);

                    $response_array = ['success' => true, 'data' => $data];

                } else {

                    $response_array = ['success'=>false, 'error' => CommonHelper::error_message(1001), 'error_code'=>1001];

                    DB::commit();

                    return response()->json($response_array, 200);

                }

            } else {

                throw new Exception(CommonHelper::error_message(103), 103);

            }

            DB::commit();

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
   
    }

    /**
     * @method login()
     *
     * @uses Registered user can login using their email & password
     * 
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request - User Email & Password
     *
     * @return Json response with user details
     */
    public function login(Request $request) {

        try {

            DB::beginTransaction();

            $basic_validator = Validator::make($request->all(),
                [
                    'device_token' => 'required',
                    'device_type' => 'required|in:'.DEVICE_ANDROID.','.DEVICE_IOS.','.DEVICE_WEB,
                    'login_by' => 'required|in:manual,facebook,google',
                ]
            );

            if($basic_validator->fails()){

                $error = implode(',', $basic_validator->messages()->all());

                throw new Exception($error , 101);

            }

            /** Validate manual login fields */

            $manual_validator = Validator::make($request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required',
                ]
            );

            if($manual_validator->fails()) {

                $error = implode(',', $manual_validator->messages()->all());

            	throw new Exception($error , 101);

            }

            $user_details = User::where('email', '=', $request->email)->first();

            $email_active = DEFAULT_TRUE;

            // Check the user details 

            if(!$user_details) {

            	throw new Exception(CommonHelper::error_message(1002), 1002);

            }

            // check the user approved status

            if($user_details->status != USER_APPROVED) {

            	throw new Exception(CommonHelper::error_message(1000), 1000);

            }

            if(Setting::get('is_account_email_verification') == YES) {

                if(!$user_details->is_verified) {

                    Helper::check_email_verification("" , $user_details->id, $error);

                    $email_active = DEFAULT_FALSE;

                }

            }

            if(!$email_active) {

    			throw new Exception(CommonHelper::error_message(1001), 1001);
            }

            if(Hash::check($request->password, $user_details->password)) {

                // Generate new tokens
                
                // $user_details->token = Helper::generate_token();

                $user_details->token_expiry = Helper::generate_token_expiry();
                
                // Save device details

                $check_device_exist = User::where('device_token', $request->device_token)->first();

                if($check_device_exist) {

                    $check_device_exist->device_token = "";
                    
                    $check_device_exist->save();
                }

                $user_details->device_token = $request->device_token ? $request->device_token : $user_details->device_token;

                $user_details->device_type = $request->device_type ? $request->device_type : $user_details->device_type;

                $user_details->login_by = $request->login_by ? $request->login_by : $user_details->login_by;

                $user_details->save();

                $data = User::CommonResponse()->find($user_details->id);

                $response_array = ['success' => true, 'message' => CommonHelper::success_message(101) , 'data' => $data];

            } else {

				throw new Exception(CommonHelper::error_message(102), 102);
                
            }

            DB::commit();

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }
 
    /**
     * @method forgot_password()
     *
     * @uses If the user forgot his/her password he can hange it over here
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request - Email id
     *
     * @return send mail to the valid user
     */
    
    public function forgot_password(Request $request) {

        try {

            DB::beginTransaction();

            // Check email configuration and email notification enabled by admin

            if(Setting::get('is_email_notification') != YES || envfile('MAIL_USERNAME') == "" || envfile('MAIL_PASSWORD') == "" ) {

                throw new Exception(CommonHelper::error_message(106), 106);
                
            }
            
            $validator = Validator::make($request->all(),
                [
                    'email' => 'required|email|exists:users,email',
                ],
                [
                    'exists' => 'The :attribute doesn\'t exists',
                ]
            );

            if($validator->fails()) {
                
                $error = implode(',',$validator->messages()->all());
                
                throw new Exception($error , 101);
            
            }

            $user_details = User::where('email' , $request->email)->first();

            if(!$user_details) {

                throw new Exception(CommonHelper::error_message(1002), 1002);
            }

            if($user_details->login_by != "manual") {

                throw new Exception(CommonHelper::error_message(119), 119);
                
            }

            // check email verification

            if($user_details->is_verified == USER_EMAIL_NOT_VERIFIED) {

                throw new Exception(CommonHelper::error_message(120), 120);
            }

            // Check the user approve status

            if(in_array($user_details->status , [USER_DECLINED , USER_PENDING])) {
                throw new Exception(CommonHelper::error_message(121), 121);
            }

            $new_password = Helper::generate_password();

            $user_details->password = Hash::make($new_password);

            $email_data = array();

            $subject = tr('user_forgot_email_title' , Setting::get('site_name'));

            $email_data['email']  = $user_details->email;

            $email_data['password'] = $new_password;

            $page = "emails.users.forgot-password";

            $email_send_response = Helper::send_email($page,$subject,$user_details->email,$email_data);

            if($email_send_response->success) {

                if(!$user_details->save()) {

                    throw new Exception(CommonHelper::error_message(103), 103);

                }

                $response_array = ['success' => true , 'message' => CommonHelper::success_message(102), 'code' => 102];

            } else {

                $error = $email_send_response->error;

                throw new Exception($error, $email_send_response->error_code);
            }

            DB::commit();

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    
    }

    /**
     * @method change_password()
     *
     * @uses To change the password of the user
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request - Password & confirm Password
     *
     * @return json response of the user
     */
    public function change_password(Request $request) {

        try {

            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                    'password' => 'required|confirmed|min:6',
                    'old_password' => 'required|min:6',
                ]);

            if($validator->fails()) {
                
                $error = implode(',',$validator->messages()->all());
               
                throw new Exception($error , 101);
           
            }

            $user_details = User::find($request->id);

            if(!$user_details) {

                throw new Exception(CommonHelper::error_message(1002), 1002);
            }

            if($user_details->login_by != "manual") {

                throw new Exception(CommonHelper::error_message(121), 121);
                
            }

            if(Hash::check($request->old_password,$user_details->password)) {

                $user_details->password = Hash::make($request->password);
                
                if($user_details->save()) {

                    DB::commit();

                    return $this->sendResponse(CommonHelper::success_message(104), $success_code = 104, $data = []);
                
                } else {

                    throw new Exception(CommonHelper::error_message(103), 103);   
                }

            } else {

                throw new Exception(CommonHelper::error_message(108) , 108);
            }

            

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /** 
     * @method profile()
     *
     * @uses To display the user details based on user  id
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function profile(Request $request) {

        try {

            $user_details = User::where('id' , $request->id)->CommonResponse()->first();

            if(!$user_details) { 

                throw new Exception(CommonHelper::error_message(1002) , 1002);
            }

            $card_last_four_number = "";

            if($user_details->user_card_id) {

                $card = Card::find($user_details->user_card_id);

                if($card) {

                    $card_last_four_number = $card->last_four;

                }

            }

            $data = $user_details->toArray();

            $data['card_last_four_number'] = $card_last_four_number;

            //$overall_rating = ProviderRating::where('user_id', $request->id)->avg('rating');

            // $data['overall_rating'] =   $overall_rating ? intval($overall_rating) : 0;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }
 
    /**
     * @method update_profile()
     *
     * @uses To update the user details
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param objecct $request : User details
     *
     * @return json response with user details
     */
    public function update_profile(Request $request) {

        try {

            DB::beginTransaction();
            
            $validator = Validator::make($request->all(),
                [
                    'name' => 'required|max:255',
                    'email' => 'email|unique:users,email,'.$request->id.'|max:255',
                    'mobile' => 'digits_between:6,13',
                    'picture' => 'mimes:jpeg,bmp,png',
                    'gender' => 'in:male,female,others',
                    'device_token' => '',
                    'description' => ''
                ]);

            if($validator->fails()) {

                // Error messages added in response for debugging

                $error = implode(',',$validator->messages()->all());
             
                throw new Exception($error , 101);
                
            }

            $user_details = User::find($request->id);

            if(!$user_details) { 

                throw new Exception(CommonHelper::error_message(1002) , 1002);
            }

            $user_details->name = $request->name ? $request->name : $user_details->name;
            
            if($request->has('email')) {

                $user_details->email = $request->email;
            }

            $user_details->mobile = $request->mobile ?: $user_details->mobile;

            $user_details->gender = $request->gender ?: $user_details->gender;

            $user_details->description = $request->description ?: '';

            // Upload picture
            if($request->hasFile('picture') != "") {

                Helper::delete_file($user_details->picture, COMMON_FILE_PATH); // Delete the old pic

                $user_details->picture = Helper::upload_file($request->file('picture') , COMMON_FILE_PATH);

            }

            if($user_details->save()) {

            	$data = User::CommonResponse()->find($user_details->id);

                DB::commit();

                return $this->sendResponse($message = tr('user_profile_update_success'), $success_code = 200, Helper::null_safe($data));

            } else {    

        		throw new Exception(CommonHelper::error_message(103) , 103);
            }

        } catch (Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
   
    }

    /**
     * @method delete_account()
     * 
     * @uses Delete user account based on user id
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request - Password and user id
     *
     * @return json with boolean output
     */

    public function delete_account(Request $request) {

        try {

            DB::beginTransaction();

            $request->request->add([ 
                'login_by' => $this->loginUser ? $this->loginUser->login_by : "manual",
            ]);

            $validator = Validator::make($request->all(),
                [
                    'password' => 'required_if:login_by,manual',
                ]);

            if($validator->fails()) {

                $error = implode(',',$validator->messages()->all());
             
                throw new Exception($error , 101);
                
            }

            $user_details = User::find($request->id);

            if(!$user_details) {

            	throw new Exception(CommonHelper::error_message(1002), 1002);
                
            }

            // The password is not required when the user is login from social. If manual means the password is required

            if($user_details->login_by == 'manual') {

                if(!Hash::check($request->password, $user_details->password)) {

                    $is_delete_allow = NO ;

                    $error = CommonHelper::error_message(104);
         
                    throw new Exception($error , 104);
                    
                }
            
            }

            if($user_details->delete()) {

                DB::commit();

                // @todo 

                $message = tr('account_delete_success');

                return $this->sendResponse($message, $success_code = 200, $data = []);

            } else {

                // @todo 

            	throw new Exception("Error Processing Request", 101);
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

	}

    /**
     * @method logout()
     *
     * @uses Logout the user
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param 
     * 
     * @return
     */
    public function logout(Request $request) {

        // @later no logic for logout

        return $this->sendResponse(CommonHelper::success_message(106), 106);

    }
}
