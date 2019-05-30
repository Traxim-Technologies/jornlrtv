<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB, Log, Hash, Validator, Exception, Setting;

use App\Helpers\Helper, App\Helpers\CommonHelper, App\Helpers\VideoHelper;


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

use App\Page;

use App\Subscription, App\UserPayment;

use App\VideoTape, App\PayPerView, App\UserHistory;

use App\Redeem, App\RedeemRequest;

use App\Flag;

class NewUserApiController extends Controller
{
    protected $skip, $take, $loginUser;

	public function __construct(Request $request) {

        $this->loginUser = User::CommonResponse()->find($request->id);

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

                    $user_details->picture = Helper::normal_upload_picture($request->file('picture'), COMMON_FILE_PATH);

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

                    // Check the user type

                    user_type_check($user_details->id);

                    if($user_details->login_by == 'manual') {

                        $user_details->password = $request->password;

                        $subject = apitr('user_welcome_title').' '.Setting::get('site_name', 'StreamTube');

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

            $subject = apitr('user_forgot_email_title' , Setting::get('site_name'));

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

                Helper::delete_picture($user_details->picture, COMMON_FILE_PATH); // Delete the old pic

                $user_details->picture = Helper::normal_upload_picture($request->file('picture'), COMMON_FILE_PATH);

            }

            if($user_details->save()) {

            	$data = User::CommonResponse()->find($user_details->id);

                DB::commit();

                return $this->sendResponse($message = apitr('user_profile_update_success'), $success_code = 200, $data);

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

                $message = apitr('account_delete_success');

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

    /**
     * @method cards_list()
     *
     * @uses get the user payment mode and cards list
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param integer id
     * 
     * @return
     */

    public function cards_list(Request $request) {

        try {

            // @todo card_holder_name

            $user_cards = Card::where('user_id' , $request->id)->select('id as user_card_id' , 'customer_id' , 'last_four' ,'card_name', 'card_token' , 'is_default' )->get();

            // $data = $user_cards ? $user_cards : []; 

            $card_payment_mode = $payment_modes = [];

            $card_payment_mode['name'] = "Card";

            $card_payment_mode['payment_mode'] = "card";

            $card_payment_mode['is_default'] = YES;

            array_push($payment_modes , $card_payment_mode);

            $data['payment_modes'] = $payment_modes;   

            $data['cards'] = $user_cards ? $user_cards : []; 

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }
    
    /**
     * @method cards_add()
     *
     * @uses Update the selected payment mode 
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param Form data
     * 
     * @return JSON Response
     */

    public function cards_add(Request $request) {

        try {

            DB::beginTransaction();

            if(Setting::get('stripe_secret_key')) {

                \Stripe\Stripe::setApiKey(Setting::get('stripe_secret_key'));

            } else {

                throw new Exception(CommonHelper::error_message(133), 133);
            }
        
            $validator = Validator::make(
                    $request->all(),
                    [
                        'card_token' => 'required',
                    ]
                );

            if($validator->fails()) {

                $error = implode(',',$validator->messages()->all());
             
                throw new Exception($error , 101);

            } else {

                Log::info("INSIDE CARDS ADD");

                $user_details = User::find($request->id);

                if(!$user_details) {

                    throw new Exception(CommonHelper::error_message(1002), 1002);
                    
                }

                // Get the key from settings table
                
                $customer = \Stripe\Customer::create([
                        "card" => $request->card_token,
                        "email" => $user_details->email,
                        "description" => "Customer for ".Setting::get('site_name'),
                    ]);

                if($customer) {

                    $customer_id = $customer->id;

                    $card_details = new Card;

                    $card_details->user_id = $request->id;

                    $card_details->customer_id = $customer_id;

                    $card_details->card_token = $customer->sources->data ? $customer->sources->data[0]->id : "";

                    $card_details->card_name = $customer->sources->data ? $customer->sources->data[0]->brand : "";

                    $card_details->last_four = $customer->sources->data[0]->last4 ? $customer->sources->data[0]->last4 : "";

                    // Check is any default is available

                    $check_card_details = Card::where('user_id',$request->id)->count();

                    $card_details->is_default = $check_card_details ? 0 : 1;


                    if($card_details->save()) {

                        if($user_details) {

                            $user_details->card_id = $check_card_details ? $user_details->card_id : $card_details->id;

                            $user_details->save();
                        }

                        $data = Card::where('id' , $card_details->id)->select('id as user_card_id' , 'customer_id' , 'last_four' ,'card_name', 'card_token' , 'is_default' )->first();

                        DB::commit();

                        $response_array = ['success' => true , 'message' => CommonHelper::success_message(105) , 'data' => $data];

                    } else {

                        throw new Exception(CommonHelper::error_message(117), 117);
                        
                    }
               
                } else {

                    throw new Exception(CommonHelper::error_message(117) , 117);
                    
                }
            
            }

            

            return response()->json($response_array , 200);

        } catch(Stripe_CardError $e) {

            Log::info("error1");

            $error1 = $e->getMessage();

            $response_array = array('success' => false , 'error' => $error1 ,'error_code' => 903);

            return response()->json($response_array , 200);

        } catch (Stripe_InvalidRequestError $e) {

            // Invalid parameters were supplied to Stripe's API

            Log::info("error2");

            $error2 = $e->getMessage();

            $response_array = array('success' => false , 'error' => $error2 ,'error_code' => 903);

            return response()->json($response_array , 200);

        } catch (Stripe_AuthenticationError $e) {

            Log::info("error3");

            // Authentication with Stripe's API failed
            $error3 = $e->getMessage();

            $response_array = array('success' => false , 'error' => $error3 ,'error_code' => 903);

            return response()->json($response_array , 200);

        } catch (Stripe_ApiConnectionError $e) {
            Log::info("error4");

            // Network communication with Stripe failed
            $error4 = $e->getMessage();

            $response_array = array('success' => false , 'error' => $error4 ,'error_code' => 903);

            return response()->json($response_array , 200);

        } catch (Stripe_Error $e) {
            Log::info("error5");

            // Display a very generic error to the user, and maybe send
            // yourself an email
            $error5 = $e->getMessage();

            $response_array = array('success' => false , 'error' => $error5 ,'error_code' => 903);

            return response()->json($response_array , 200);

        } catch (\Stripe\StripeInvalidRequestError $e) {

            Log::info("error7");

            // Log::info(print_r($e,true));

            $response_array = array('success' => false , 'error' => CommonHelper::error_message(903) ,'error_code' => 903);

            return response()->json($response_array , 200);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
   
    }

    /**
     * @method cards_delete()
     *
     * @uses delete the selected card
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param integer user_card_id
     * 
     * @return JSON Response
     */

    public function cards_delete(Request $request) {

        // Log::info("cards_delete");

        DB::beginTransaction();

        try {
    
            $user_card_id = $request->user_card_id;

            $validator = Validator::make(
                $request->all(),
                array(
                    'user_card_id' => 'required|integer|exists:cards,id,user_id,'.$request->id,
                ),
                array(
                    'exists' => 'The :attribute doesn\'t belong to user:'.$this->loginUser->name
                )
            );

            if($validator->fails()) {

               $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);

            } else {

                $user_details = User::find($request->id);

                // No need to prevent the deafult card delete. We need to allow user to delete the all the cards

                // if($user_details->card_id == $user_card_id) {

                //     throw new Exception(tr('card_default_error'), 101);
                    
                // } else {

                    Card::where('id',$user_card_id)->delete();

                    if($user_details) {

                        if($user_details->payment_mode = CARD) {

                            // Check he added any other card

                            if($check_card = Card::where('user_id' , $request->id)->first()) {

                                $check_card->is_default =  DEFAULT_TRUE;

                                $user_details->card_id = $check_card->id;

                                $check_card->save();

                            } else { 

                                $user_details->payment_mode = COD;

                                $user_details->card_id = DEFAULT_FALSE;
                            
                            }
                       
                        }

                        // Check the deleting card and default card are same

                        if($user_details->card_id == $user_card_id) {

                            $user_details->card_id = DEFAULT_FALSE;

                            $user_details->save();
                        }
                        
                        $user_details->save();
                    
                    }

                    $response_array = ['success' => true , 'message' => CommonHelper::success_message(107) , 'code' => 107];

                // }

            }

            DB::commit();
    
            return response()->json($response_array , 200);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    }

    /**
     * @method cards_default()
     *
     * @uses update the selected card as default
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param integer id
     * 
     * @return JSON Response
     */
    public function cards_default(Request $request) {

        Log::info("cards_default");

        try {

            DB::beginTransaction();

            $validator = Validator::make(
                $request->all(),
                array(
                    'user_card_id' => 'required|integer|exists:cards,id,user_id,'.$request->id,
                ),
                array(
                    'exists' => 'The :attribute doesn\'t belong to user:'.$this->loginUser->name
                )
            );

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);
                   
            }

            $old_default_cards = Card::where('user_id' , $request->id)->where('is_default', DEFAULT_TRUE)->update(array('is_default' => DEFAULT_FALSE));

            $card = Card::where('id' , $request->user_card_id)->update(['is_default' => DEFAULT_TRUE]);

            $user_details = User::find($request->id);

            $user_details->card_id = $request->user_card_id;

            $user_details->save();           

            DB::commit();

            return $this->sendResponse($message = CommonHelper::success_message(108), $success_code = "108", $data = []);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    } 

    /**
     * @method notification_settings()
     *
     * @uses To enable/disable notifications of email / push notification
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param - 
     *
     * @return JSON Response
     */
    public function notification_settings(Request $request) {

        try {

            DB::beginTransaction();

            $validator = Validator::make(
                $request->all(),
                array(
                    'status' => 'required|numeric',
                    'type'=>'required|in:'.EMAIL_NOTIFICATION.','.PUSH_NOTIFICATION
                )
            );

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);

            }
                
            $user_details = User::find($request->id);

            if($request->type == EMAIL_NOTIFICATION) {

                $user_details->email_notification_status = $request->status;

            }

            if($request->type == PUSH_NOTIFICATION) {

                $user_details->push_notification_status = $request->status;

            }

            $user_details->save();

            $message = $request->status ? CommonHelper::success_message(206) : CommonHelper::success_message(207);

            $data = ['id' => $user_details->id , 'token' => $user_details->token];

            $response_array = [
                'success' => true ,'message' => $message, 
                'email_notification_status' => (int) $user_details->email_notification_status,  // Don't remove int (used ios)
                'push_notification_status' => (int) $user_details->push_notification_status,    // Don't remove int (used ios)
                'data' => $data
            ];
                
            
            DB::commit();

            return response()->json($response_array , 200);

        } catch (Exception $e) {

            DB::rollback();

            $error = $e->getMessage();

            $code = $e->getCode();

            $response_array = ['success'=>false, 'error'=>$error, 'error_code'=>$code];

            return response()->json($response_array);
        }

    }

    /**
     * @method configurations()
     *
     * @uses used to get the configurations for base products
     *
     * @created Vithya R Chandrasekar
     *
     * @updated - 
     *
     * @param - 
     *
     * @return JSON Response
     */
    public function configurations(Request $request) {

        try {

            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:users,id',
                'token' => 'required',

            ]);

            if($validator->fails()) {

                $error = implode(',',$validator->messages()->all());

                throw new Exception($error, 101);

            }

            $config_data = $data = [];

            $payment_data['is_stripe'] = 1;

            $payment_data['stripe_publishable_key'] = Setting::get('stripe_publishable_key') ?: "";

            $payment_data['stripe_secret_key'] = Setting::get('stripe_secret_key') ?: "";

            $payment_data['stripe_secret_key'] = Setting::get('stripe_secret_key') ?: "";

            $data['payments'] = $payment_data;

            $data['urls']  = [];

            $url_data['base_url'] = envfile("APP_URL") ?: "";

            $url_data['chat_socket_url'] = Setting::get("chat_socket_url") ?: "";

            $data['urls'] = $url_data;

            $notification_data['FCM_SENDER_ID'] = "";

            $notification_data['FCM_SERVER_KEY'] = $notification_data['FCM_API_KEY'] = "";

            $notification_data['FCM_PROTOCOL'] = "";

            $data['notification'] = $notification_data;

            $data['site_name'] = Setting::get('site_name');

            $data['site_logo'] = Setting::get('site_logo');

            $data['currency'] = Setting::get('currency');

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
   
    }

    /**
     * @method static_pages_api()
     *
     * @uses used to get the pages
     *
     * @created Vidhya R 
     *
     * @edited Vidhya R
     *
     * @param - 
     *
     * @return JSON Response
     */

    public function static_pages_api(Request $request) {

        if($request->page_type) {

            $static_page = Page::where('type' , $request->page_type)
                                ->where('status' , APPROVED)
                                ->select('id as page_id' , 'title' , 'description','type as page_type', 'status' , 'created_at' , 'updated_at')
                                ->first();

            $response_array = ['success' => true , 'data' => $static_page];

        } else {

            $static_pages = Page::where('status' , APPROVED)->orderBy('id' , 'asc')
                                ->select('id as page_id' , 'title' , 'description','type as page_type', 'status' , 'created_at' , 'updated_at')
                                ->orderBy('title', 'asc')
                                ->get();

            $response_array = ['success' => true , 'data' => $static_pages ? $static_pages->toArray(): []];

        }

        return response()->json($response_array , 200);

    }

    /**
     * @method subscriptions() 
     *
     * @uses used to get the list of subscriptions
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param
     *
     * @return json repsonse
     */     

    public function subscriptions(Request $request) {

        try {

            $base_query = Subscription::where('subscriptions.status', APPROVED)->CommonResponse();

            $subscriptions = $base_query->skip($this->skip)->take($this->take)->orderBy('updated_at', 'desc')->get();

            foreach ($subscriptions as $key => $subscription_details) {

                $subscription_details->amount_formatted = formatted_amount($subscription_details->amount);
            }

            return $this->sendResponse($message = "", $code = 200, $subscriptions);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method subscriptions_payment_by_stripe() 
     *
     * @uses used to deduct amount for selected subscription
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param
     *
     * @return json repsonse
     */     

    public function subscriptions_payment_by_stripe(Request $request) {

        try {

            $validator = Validator::make($request->all(), [
                'subscription_id' => 'required|exists:subscriptions,id',
                'coupon_code'=>'exists:coupons,coupon_code',
            ],
            [
                'subscription_id' => CommonHelper::error_message(203),
                'coupon_code' => CommonHelper::error_message(205)
            ]
            );

            if ($validator->fails()) {

                // Error messages added in response for debugging

                $error = implode(',',$validator->messages()->all());

                throw new Exception($error, 101);

            }

            DB::beginTransaction();

            // Check Subscriptions

            $subscription_details = Subscription::where('id', $request->subscription_id)->where('status', APPROVED)->first();

            if (!$subscription_details) {

                throw new Exception(CommonHelper::error_message(203), 203);
            }

            $user_details  = User::find($request->id);

            // Initial detault values

            $total = $subscription_details->amount; 

            $coupon_amount = 0.00;
           
            $coupon_reason = ""; 

            $is_coupon_applied = COUPON_NOT_APPLIED;

            // Check the coupon code

            if($request->coupon_code) {
                
                $coupon_code_response = PaymentRepo::check_coupon_code($request, $user_details, $subscription_details->amount);

                $coupon_amount = $coupon_code_response['coupon_amount'];

                $coupon_reason = $coupon_code_response['coupon_reason'];

                $is_coupon_applied = $coupon_code_response['is_coupon_applied'];

                $total = $coupon_code_response['total'];

            }

            // Update the coupon details and total to the request

            $request->coupon_amount = $coupon_amount ?: 0.00;

            $request->coupon_reason = $coupon_reason ?: "";

            $request->is_coupon_applied = $is_coupon_applied;

            $request->total = $total ?: 0.00;

            $request->payment_mode = CARD;

            // If total greater than zero, do the stripe payment

            if($request->total > 0) {

                // Check provider card details

                $card_details = Card::where('user_id', $request->id)->where('is_default', YES)->first();

                if (!$card_details) {

                    throw new Exception(CommonHelper::error_message(111), 111);
                }

                $customer_id = $card_details->customer_id;

                // Check stripe configuration
            
                $stripe_secret_key = Setting::get('stripe_secret_key');

                if(!$stripe_secret_key) {

                    throw new Exception(CommonHelper::error_message(107), 107);

                } 

                try {

                    \Stripe\Stripe::setApiKey($stripe_secret_key);

                    $total = $subscription_details->amount;

                    $currency_code = Setting::get('currency_code', 'USD') ?: "USD";

                    $charge_array = [
                                        "amount" => $total * 100,
                                        "currency" => $currency_code,
                                        "customer" => $customer_id,
                                    ];

                    $stripe_payment_response =  \Stripe\Charge::create($charge_array);

                    $payment_id = $stripe_payment_response->id;

                    $amount = $stripe_payment_response->amount/100;

                    $paid_status = $stripe_payment_response->paid;

                } catch(Stripe_CardError | Stripe_InvalidRequestError | Stripe_AuthenticationError | Stripe_ApiConnectionError | Stripe_Error $e) {

                    $error_message = $e->getMessage();

                    $error_code = $e->getCode();

                    // Payment failure function

                    DB::commit();

                    // @todo changes

                    $response_array = ['success' => false, 'error'=> $error_message , 'error_code' => 205];

                    return response()->json($response_array);

                } 

            }

            $response_array = PaymentRepo::subscriptions_payment_save($request, $subscription_details, $user_details);

            DB::commit();

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            // Something else happened, completely unrelated to Stripe

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method subscriptions_payment_by_paypal() 
     *
     * @uses used to deduct amount for selected subscription
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param
     *
     * @return json repsonse
     */     

    public function subscriptions_payment_by_paypal(Request $request) {

        try {

            $validator = Validator::make($request->all(), [
                'subscription_id' => 'required|exists:subscriptions,id',
                'coupon_code'=>'exists:coupons,coupon_code',
            ],
            [
                'subscription_id' => CommonHelper::error_message(203),
                'coupon_code' => CommonHelper::error_message(205)
            ]
            );

            if ($validator->fails()) {

                // Error messages added in response for debugging

                $error = implode(',',$validator->messages()->all());

                throw new Exception($error, 101);

            }

            DB::beginTransaction();

            // Check Subscriptions

            $subscription_details = Subscription::where('id', $request->subscription_id)->where('status', APPROVED)->first();

            if (!$subscription_details) {

                throw new Exception(CommonHelper::error_message(203), 203);
            }

            $user_details  = User::find($request->id);

            // Initial detault values

            $total = $subscription_details->amount; 

            $coupon_amount = 0.00;
           
            $coupon_reason = ""; 

            $is_coupon_applied = COUPON_NOT_APPLIED;

            // Check the coupon code

            if($request->coupon_code) {
                
                $coupon_code_response = PaymentRepo::check_coupon_code($request, $user_details, $subscription_details->amount);

                $coupon_amount = $coupon_code_response['coupon_amount'];

                $coupon_reason = $coupon_code_response['coupon_reason'];

                $is_coupon_applied = $coupon_code_response['is_coupon_applied'];

                $total = $coupon_code_response['total'];

            }

            // Update the coupon details and total to the request

            $request->coupon_amount = $coupon_amount ?: 0.00;

            $request->coupon_reason = $coupon_reason ?: "";

            $request->is_coupon_applied = $is_coupon_applied;

            $request->total = $total ?: 0.00;

            $request->payment_mode = CARD;

            $request->payment_id = $request->payment_id ?: generate_payment_id($request->id, $subscription_details->id, $total);

            $response_array = PaymentRepo::subscriptions_payment_save($request, $subscription_details, $user_details);

            DB::commit();

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            // Something else happened, completely unrelated to Stripe

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method subscriptions_history() 
     *
     * @uses List of subscription payments
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param
     *
     * @return json repsonse
     */     

    public function subscriptions_history(Request $request) {

        try {

            $base_query = UserPayment::where('user_id', $request->id)->select('user_payments.id as provider_subscription_payment_id', 'user_payments.*');

            $user_payments = $base_query->skip($this->skip)->take($this->take)->orderBy('updated_at', 'desc')->get();

            foreach ($user_payments as $key => $payment_details) {

                $payment_details->title = $payment_details->description = "";

                $subscription_details = Subscription::find($payment_details->subscription_id);

                if($subscription_details) {

                    $payment_details->title = $subscription_details->title ?: "";

                    $payment_details->description = $subscription_details->description ?: "";
                }

                unset($payment_details->id);
            }

            return $this->sendResponse($message = "", $code = 200, $user_payments);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method wishlist_list()
     *
     * @uses Get the user saved the hosts
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */
    public function wishlist_list(Request $request) {

        try {

            $video_tapes = VideoHelper::wishlist_videos($request);

            return $this->sendResponse($message = "", $success_code = "", $video_tapes);

        } catch(Exception  $e) {
            
            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method wishlist_operations()
     *
     * @uses To add/Remove by using this operation favorite
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id, host_id
     *
     * @return response of details
     */
    public function wishlist_operations(Request $request) {

        try {

            DB::beginTransaction();

            $validator = Validator::make($request->all(),
                [
                    'clear_all_status' => 'in:'.YES.','.NO,
                    'video_tape_id' => $request->clear_all_status == NO ? 'required|exists:video_tapes,id,status,'.APPROVED : '', 
                ], 
                [
                    'required' => CommonHelper::error_message(200)
                ]
            );

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);

            }

            if($request->clear_all_status == YES) {

                Wishlist::where('user_id', $request->id)->delete();
                
                DB::commit();

                return $this->sendResponse($message = CommonHelper::success_message(202), $code = 202, $data = []);


            } else {

                $wishlist_details = Wishlist::where('video_tape_id', $request->video_tape_id)->where('user_id', $request->id)->first();

                if($wishlist_details) {

                    if($wishlist_details->delete()) {

                        DB::commit();

                        return $this->sendResponse($message = CommonHelper::success_message(201), $code = 201, $data = []);

                    } else {

                        throw new Exception(CommonHelper::error_message(209), 209);
                        
                    }

                } else {

                    $wishlist_details = new Wishlist;

                    $wishlist_details->user_id = $request->id;

                    $wishlist_details->video_tape_id = $request->video_tape_id;

                    $wishlist_details->status = APPROVED;

                    $wishlist_details->save();

                    DB::commit();

                    $data = ['wishlist_id' => $wishlist_details->id];
               
                    return $this->sendResponse(CommonHelper::success_message(200), 200, $data = ['wishlist_id' => $wishlist_details->id]);

                }

            }

        } catch (Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method video_tapes_history()
     *
     * @uses get the logged in user video history details
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */
    public function video_tapes_history(Request $request) {

        try {

            // @todo wishlist videos common

            $video_tapes = VideoHelper::history_videos($request);

            return $this->sendResponse($message = "", $success_code = "", $video_tapes);

        } catch(Exception  $e) {
            
            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method video_tapes_history_add()
     *
     * @uses add video to user history and update the PPV details
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */
    public function video_tapes_history_add(Request $request) {

        try {

            $validator = Validator::make($request->all(),[
                    'video_tape_id' => 'required|integer|exists:video_tapes,id'
                ]
            );

            if ($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);

            }

            DB::beginTransaction();

            $ppv_details = PayPerView::where('user_id', $request->id)
                                ->where('video_id', $request->video_tape_id)
                                ->where('is_watched', '!=', WATCHED)
                                ->where('status', PAID_STATUS)
                                ->orderBy('ppv_date', 'desc')
                                ->first();

            if ($ppv_details) {

                $ppv_details->is_watched = WATCHED;

                $ppv_details->save();

            }

            $user_history_details = UserHistory::where('user_histories.user_id' , $request->id)->where('video_tape_id' ,$request->video_tape_id)->first();

            if(!$user_history_details) {

                $user_history_details = new UserHistory();

                $user_history_details->user_id = $request->id;

                $user_history_details->video_tape_id = $request->video_tape_id;

                $user_history_details->status = DEFAULT_TRUE;

                $user_history_details->save();

            }

            $video_tape_details = VideoTape::find($request->video_tape_id);

            $navigateback = NO;

            if ($request->id != $video_tape_details->user_id) {

                if ($video_tape_details->type_of_subscription == RECURRING_PAYMENT) {

                    $navigateback = YES;

                }
            }

            DB::commit();

            // navigateback = used to handle the replay in mobile for recurring payments

            $data['navigateback'] = $navigateback;
           
            return $this->sendResponse($message = CommonHelper::success_message(208), $success_code = 208, $data);

        } catch(Exception  $e) {

            DB::rollback();
            
            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method video_tapes_history_remove()
     *
     * @uses remove video from user history and update the PPV details
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */
    public function video_tapes_history_remove(Request $request) {

        try {

            $validator = Validator::make($request->all(),[
                    'video_tape_id' =>$request->clear_all_status ? 'integer|exists:video_tapes,id' : 'required|integer|exists:video_tapes,id'
                ]
            );

            if ($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);

            }

            DB::beginTransaction();

            if($request->clear_all_status) {

                $history = UserHistory::where('user_id',$request->id)->delete();

                $message = CommonHelper::success_message(210); $success_code = 210;

            } else {

                $history = UserHistory::where('user_id',$request->id)->where('video_tape_id' , $request->video_tape_id)->delete();

                $message = CommonHelper::success_message(209); $success_code = 209;

            }

            DB::commit();

            return $this->sendResponse($message, $success_code);

        } catch(Exception  $e) {
            
            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method home()
     *
     * @uses home page videos
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */
    
    public function home(Request $request) {

        try {
            
            $video_tapes = VideoHelper::mobile_home($request);

            return $this->sendResponse($message = "", $success_code = "", $video_tapes);


        } catch(Exception  $e) {
            
            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method trending()
     *
     * @uses trending videos
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */
    
    public function trending(Request $request) {

        try {
            
            $video_tapes = VideoHelper::trending($request);

            return $this->sendResponse($message = "", $success_code = "", $video_tapes);


        } catch(Exception  $e) {
            
            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method spam_videos()
     *
     * @uses list of videos spammed by logged in user
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */

    public function spam_videos(Request $request) {

        try {
            
            $video_tapes = VideoHelper::spam_videos($request);

            return $this->sendResponse($message = "", $success_code = "", $video_tapes);


        } catch(Exception  $e) {
            
            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /**
     * @method spam_videos()
     *
     * @uses add video to spam
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */

    public function spam_videos_add(Request $request) {

        try {

            $validator = Validator::make($request->all(), [
                'video_tape_id' => 'required|exists:video_tapes,id',
                'reason' => 'required',
            ]);

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);
            }

            DB::beginTransaction();

            $spam_video = Flag::where('video_tape_id', $request->video_tape_id)->where('user_id', $request->id)->first();

            // If already exists remove

            if(!$spam_video) {

                $spam_video = new Flag;

                $spam_video->user_id = $request->id;

                $spam_video->video_tape_id = $request->video_tape_id;

                $spam_video->reason = $request->reason ?: "";

            }

            $spam_video->status = DEFAULT_TRUE;

            $spam_video->save();

            DB::commit();

            $data['video_tape_id'] = $request->video_tape_id;

            $data['flag_id'] = $spam_video->id;

            $message = CommonHelper::success_message(213);

            return $this->sendResponse($message, $code = 213, $data);

        } catch(Exception  $e) {

            DB::rollback();
            
            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method spam_videos_remove()
     *
     * @uses Remove | clear the video from spams 
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */

    public function spam_videos_remove(Request $request) {

        try {

            $validator = Validator::make($request->all(), [
                'video_tape_id' => $request->clear_all_status ? '' : 'required|exists:video_tapes,id',
            ]);

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);
            }

            DB::beginTransaction();

            if($request->clear_all_status) {

                $flag = Flag::where('user_id', $request->id)->delete();

                $message = CommonHelper::success_message(215); $code = 215;

            } else {

                $flag = Flag::where('user_id',$request->id)->where('video_tape_id' , $request->video_tape_id)->delete();

                $message = CommonHelper::success_message(214); $code = 214;

            }

            DB::commit();

            return $this->sendResponse($message, $code);

        } catch(Exception  $e) {

            DB::rollback();
            
            return $this->sendError($e->getMessage(), $e->getCode());
        }
    
    }

    /**
     * @method redeems()
     *
     * @uses redeems details for the loggedin user
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */

    public function redeems(Request $request) {

        try {

            if($request->skip == 0) {

                $redeem_details = Redeem::where('user_id' , $request->id)->select('total' , 'paid' , 'remaining' , 'status')->first();

                // If no record, create and send the empty details 

                if(!$redeem_details) {

                    $redeem_details = new Redeem;

                    $redeem_details->user_id = $request->id;

                    $redeem_details->total = $redeem_details->paid = $redeem_details->remaining = 0.00;

                    $redeem_details->status = DEFAULT_TRUE;

                    $redeem_details->save();

                }

                $redeem_details->minimum_redeem = intval(Setting::get('minimum_redeem', 1));

                $redeem_details->currency = Setting::get('currency', '$');

                $data['redeems'] = $redeem_details;
            }

            $redeems_history = RedeemRequest::where('user_id' , $request->id)
                    ->CommonResponse()
                    ->orderBy('created_at', 'desc')
                    ->get();

            foreach ($redeems_history as $key => $details) {

                $details->request_amount_formatted = formatted_amount($details->request_amount);

                $details->paid_amount_formatted = formatted_amount($details->paid_amount);

                $details->status_text = redeem_request_status($details->status);
            }

            $data['redeems_history'] = $redeems_history;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception  $e) {
            
            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method redeems_request_send()
     *
     * @uses send redeem request to admin
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */
    public function redeems_request_send(Request $request) {

        try {

            // Get admin configured - Minimum Provider Credit

            $minimum_redeem = Setting::get('minimum_redeem' , 1);

            // Get the user redeems details 

            $redeem_details = Redeem::where('user_id' , $request->id)->first();

            if(!$redeem_details) {

                throw new Exception(CommonHelper::error_message(211), 211);
            }

            $remaining = $redeem_details->remaining;

            // check the provider have more than minimum credits

            if($remaining < $minimum_redeem) {
                throw new Exception(CommonHelper::error_message(213), 213);

            }

            $redeem_amount = abs(intval($remaining - $minimum_redeem));

            // Check the redeems is not empty

            if(!$redeem_amount) {

                throw new Exception(CommonHelper::error_message(212), 212);
            }

            DB::beginTransaction();

            // Save Redeem Request

            $redeem_request = new RedeemRequest;

            $redeem_request->user_id = $request->id;

            $redeem_request->request_amount = $redeem_amount;

            $redeem_request->status = DEFAULT_FALSE;

            $redeem_request->save();

            // Update Redeems details 

            $redeem_details->remaining = abs($redeem_details->remaining-$redeem_amount);

            $redeem_details->save();

            $data['redeem_request_id'] = $redeem_request->id;

            DB::commit();

            return $this->sendResponse($message = CommonHelper::success_message(212), 212, $data);
        
        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method redeems_request_cancel()
     *
     * @uses redeems details for the loggedin user
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */
    public function redeems_request_cancel(Request $request) {

        try {

            $validator = Validator::make($request->all() , [
                'redeem_request_id' => 'required|exists:redeem_requests,id,user_id,'.$request->id,
                ]);

             if ($validator->fails()) {
                
                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);
            }

            // check the record exists

            $redeem_details = Redeem::where('user_id' , $request->id)->first();

            $redeem_request_details = RedeemRequest::find($request->redeem_request_id);

            if(!$redeem_details || !$redeem_request_details) {

                throw new Exception(CommonHelper::error_message(211), 211);
                
            }

            DB::beginTransaction();

            // Check status to cancel the redeem request

            if(in_array($redeem_request_details->status, [REDEEM_REQUEST_SENT , REDEEM_REQUEST_PROCESSING])) {

                // Update the redeem record

                $redeem_details->remaining = $redeem_details->remaining + abs($redeem_request_details->request_amount);

                $redeem_details->save();

                // Update the redeem request Status

                $redeem_request_details->status = REDEEM_REQUEST_CANCEL;

                $redeem_request_details->save();

                DB::commit();

                $data['redeem_request_id'] = $redeem_request_details->id;

                return $this->sendResponse(CommonHelper::success_message(211), 211, $data);

            } else {

                throw new Exception(CommonHelper::error_message(214), 214);
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method ppv_videos()
     *
     * @uses get the logged in user paid videos
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */
    public function ppv_videos(Request $request) {

        try {

            $video_tapes = VideoHelper::ppv_videos($request);

            return $this->sendResponse($message = "", $success_code = "", $video_tapes);

        } catch(Exception  $e) {
            
            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method ppv_payment_by_stripe() 
     *
     * @uses used to deduct amount for selected video
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param @todo not yet completed
     *
     * @return json repsonse
     */     

    public function ppv_payment_by_stripe(Request $request) {

        try {

            $validator = Validator::make($request->all(), [
                'video_tape_id'=>'required|exists:video_tapes,id,status,'.USER_VIDEO_APPROVED.',is_approved,'.ADMIN_VIDEO_APPROVED.',publish_status,'.VIDEO_PUBLISHED,
                'payment_id'=>'',
                'coupon_code'=>'exists:coupons,coupon_code',
            ],
            [
                'video_tape_id' => CommonHelper::error_message(200),
                'coupon_code' => CommonHelper::error_message(205)
            ]
            );

            if ($validator->fails()) {

                $error = implode(',',$validator->messages()->all());

                throw new Exception($error, 101);

            }

            DB::beginTransaction();

            $video_tape_details = VideoTape::find($request->video_tape_id);

            $user_details = User::find($request->id);

            // Check video record

            if (!$video_tape_details) {

                throw new Exception(CommonHelper::error_message(200), 200);
            }

            // check ppv enabled for this video

            if($video_tape_details->is_pay_per_view == NO) {

                $message = CommonHelper::success_message(216); $code = 216;

                return $this->sendResponse($message, $code);
    
            }

            // Check the logged in user as channel owner

            if($video_tape_details->user_id == $request->id) {

                $message = CommonHelper::success_message(217); $code = 217;

                return $this->sendResponse($message, $code);

            }

            // Check the whether the user needs to pay or directly allow the user to watch video

            $is_user_can_watch_now = PaymentRepo::is_user_can_watch_now($request->id, $video_tape_details);

            if($is_user_can_watch_now == YES) {

                $message = CommonHelper::success_message(218); $code = 218;

                return $this->sendResponse($message, $code);
            }

            // Initial detault values

            $total = $video_tape_details->ppv_amount; 

            $coupon_amount = 0.00;
           
            $coupon_reason = ""; 

            $is_coupon_applied = COUPON_NOT_APPLIED;

            // Check the coupon code

            if($request->coupon_code) {
                
                $coupon_code_response = PaymentRepo::check_coupon_code($request, $user_details, $video_tape_details->ppv_amount);

                $coupon_amount = $coupon_code_response['coupon_amount'];

                $coupon_reason = $coupon_code_response['coupon_reason'];

                $is_coupon_applied = $coupon_code_response['is_coupon_applied'];

                $total = $coupon_code_response['total'];

            }

            // Update the coupon details and total to the request

            $request->coupon_amount = $coupon_amount ?: 0.00;

            $request->coupon_reason = $coupon_reason ?: "";

            $request->is_coupon_applied = $is_coupon_applied;

            $request->total = $total ?: 0.00;

            $request->payment_mode = CARD;

            // If total greater than zero, do the stripe payment

            if($request->total > 0) {

                // Check provider card details

                $card_details = Card::where('user_id', $request->id)->where('is_default', YES)->first();

                if (!$card_details) {

                    throw new Exception(CommonHelper::error_message(111), 111);
                }

                $customer_id = $card_details->customer_id;

                // Check stripe configuration
            
                $stripe_secret_key = Setting::get('stripe_secret_key');

                if(!$stripe_secret_key) {

                    throw new Exception(CommonHelper::error_message(107), 107);

                } 

                try {

                    \Stripe\Stripe::setApiKey($stripe_secret_key);

                    $total = $video_tape_details->ppv_amount;

                    $currency_code = Setting::get('currency_code', 'USD') ?: "USD";

                    $charge_array = [
                                        "amount" => $total * 100,
                                        "currency" => $currency_code,
                                        "customer" => $customer_id,
                                    ];

                    $stripe_payment_response =  \Stripe\Charge::create($charge_array);

                    $payment_id = $stripe_payment_response->id;

                    $amount = $stripe_payment_response->amount/100;

                    $paid_status = $stripe_payment_response->paid;

                    $request->payment_id = $payment_id;

                } catch(Stripe_CardError | Stripe_InvalidRequestError | Stripe_AuthenticationError | Stripe_ApiConnectionError | Stripe_Error $e) {

                    $error_message = $e->getMessage();

                    $error_code = $e->getCode();

                    // Payment failure function

                    DB::commit();

                    // @todo changes

                    $response_array = ['success' => false, 'error'=> $error_message , 'error_code' => 205];

                    return response()->json($response_array);

                } 

            }

            $response_array = PaymentRepo::ppv_payment_save($request, $video_tape_details, $user_details);

            DB::commit();

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            // Something else happened, completely unrelated to Stripe

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method ppv_payment_by_paypal() 
     *
     * @uses used to deduct amount for selected video
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param @todo not yet completed
     *
     * @return json repsonse
     */     
    public function ppv_payment_by_paypal(Request $request) {

        try {

            $validator = Validator::make($request->all(), [
                'video_tape_id'=>'required|exists:video_tapes,id,status,'.USER_VIDEO_APPROVED.',is_approved,'.ADMIN_VIDEO_APPROVED.',publish_status,'.VIDEO_PUBLISHED,
                'payment_id'=>'required',
                'coupon_code'=>'exists:coupons,coupon_code',
            ],
            [
                'video_tape_id' => CommonHelper::error_message(200),
                'coupon_code' => CommonHelper::error_message(205)
            ]
            );

            if ($validator->fails()) {

                $error = implode(',',$validator->messages()->all());

                throw new Exception($error, 101);

            }

            DB::beginTransaction();

            $video_tape_details = VideoTape::find($request->video_tape_id);

            $user_details = User::find($request->id);

            // Check video record

            if (!$video_tape_details) {

                throw new Exception(CommonHelper::error_message(200), 200);
            }

            // check ppv enabled for this video

            if($video_tape_details->is_pay_per_view == NO) {

                $message = CommonHelper::success_message(216); $code = 216;

                return $this->sendResponse($message, $code);
    
            }

            // Check the logged in user as channel owner

            if($video_tape_details->user_id == $request->id) {

                $message = CommonHelper::success_message(217); $code = 217;

                return $this->sendResponse($message, $code);

            }

            // Check the whether the user needs to pay or directly allow the user to watch video

            $is_user_can_watch_now = PaymentRepo::is_user_can_watch_now($request->id, $video_tape_details);

            if($is_user_can_watch_now == YES) {

                $message = CommonHelper::success_message(218); $code = 218;

                return $this->sendResponse($message, $code);
            }

            // Initial detault values

            $total = $video_tape_details->ppv_amount; 

            $coupon_amount = 0.00;
           
            $coupon_reason = ""; 

            $is_coupon_applied = COUPON_NOT_APPLIED;

            // Check the coupon code

            if($request->coupon_code) {
                
                $coupon_code_response = PaymentRepo::check_coupon_code($request, $user_details, $video_tape_details->ppv_amount);

                $coupon_amount = $coupon_code_response['coupon_amount'];

                $coupon_reason = $coupon_code_response['coupon_reason'];

                $is_coupon_applied = $coupon_code_response['is_coupon_applied'];

                $total = $coupon_code_response['total'];

            }

            // Update the coupon details and total to the request

            $request->coupon_amount = $coupon_amount ?: 0.00;

            $request->coupon_reason = $coupon_reason ?: "";

            $request->is_coupon_applied = $is_coupon_applied;

            $request->total = $total ?: 0.00;

            $request->payment_mode = PAYPAL;

            $response_array = PaymentRepo::ppv_payment_save($request, $video_tape_details, $user_details);

            DB::commit();

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            // Something else happened, completely unrelated to Stripe

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

}
