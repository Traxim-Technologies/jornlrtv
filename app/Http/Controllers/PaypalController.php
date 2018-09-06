<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Helpers\Helper;

use App\Repositories\PaymentRepository as PaymentRepo;

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\ExecutePayment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;
use PayPal\Exception\PayPalConnectionException;

use Setting;

use Log;

use Session;

use Auth;

use App\UserPayment;

use App\User;

use App\VideoTape;

use App\PayPerView;

use App\Subscription;

 use App\Coupon;
use App\UserCoupon;

class PaypalController extends Controller {
   
    private $_api_context;

    protected $UserAPI;
 
    public function __construct(UserApiController $API) {

        $this->middleware('PaypalCheck');

        $this->UserAPI = $API;
       
        // setup PayPal api context

        $paypal_conf = config('paypal');

        $paypal_conf['client_id'] = envfile('PAYPAL_ID') ?  envfile('PAYPAL_ID') : $paypal_conf['client_id'];
        
        $paypal_conf['secret'] = envfile('PAYPAL_SECRET') ?  envfile('PAYPAL_SECRET') : $paypal_conf['secret'];
        $paypal_conf['settings']['mode'] = envfile('PAYPAL_MODE') ?  envfile('PAYPAL_MODE') : $paypal_conf['settings']['mode'];

        Log::info("PAYPAL CONFIGURATION".print_r($paypal_conf,true));
        
        $this->_api_context = new ApiContext(new OAuthTokenCredential($paypal_conf['client_id'], $paypal_conf['secret']));

        $this->_api_context->setConfig($paypal_conf['settings']);
   
    }

    /** 
     *
     *
     *
     *
     */
    public function pay(Request $request) {

        $subscription = Subscription::find($request->id);

        if(count($subscription) == 0) {

            Log::info("Subscription Details Not Found");

            $error_message = tr('subscription_details_not_found');

            return back()->with('flash_error' , $error_message);

        }

        if ($subscription->amount <= 0) {

            return back()->with('flash_error', tr('cannot_pay_zero_amount'));

        }

        $total = $subscription ? $subscription->amount : "1.00" ;

        $coupon_amount = 0;

        $coupon_reason = '';

        $is_coupon_applied = COUPON_NOT_APPLIED;

        if ($request->coupon_code) {

            $coupon = Coupon::where('coupon_code', $request->coupon_code)->first();

            if ($coupon) {

                $is_coupon_applied = DEFAULT_TRUE;

                if ($coupon->status == COUPON_INACTIVE) {

                    $coupon_reason = tr('coupon_code_declined');

                } else {

                    $check_coupon = $this->UserAPI->check_coupon_applicable_to_user($user, $coupon)->getData();

                    if ($check_coupon->success) {

                        $amount_convertion = $coupon->amount;

                        if ($coupon->amount_type == PERCENTAGE) {

                            $amount_convertion = amount_convertion($coupon->amount, $subscription->amount);

                        }

                        if ($amount_convertion <= $subscription->amount) {

                            $total = $subscription->amount - $amount_convertion;

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

                $coupon_reason = tr('coupon_code_not_exists');

            }

        }

		$item = new Item();

		$item->setName(Setting::get('site_name')) // item name
				   ->setCurrency('USD')
			   ->setQuantity('1')
               ->setPrice($total);
	 
        $payer = new Payer();
        
        $payer->setPaymentMethod('paypal');

        // add item to list
        $item_list = new ItemList();
        $item_list->setItems(array($item));
        $total = $total;
        $details = new Details();
        $details->setShipping('0.00')
            ->setTax('0.00')
            ->setSubtotal($total);


        $amount = new Amount();
        $amount->setCurrency('USD')
            ->setTotal($total)
        	->setDetails($details);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription('Payment for the Request');

        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(url('/user/payment/status'))
                    ->setCancelUrl(url('/'));

        $payment = new Payment();
        $payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));

        try {
            $payment->create($this->_api_context);

        } catch (\PayPal\Exception\PayPalConnectionException $ex) {

            // Log::info("Exception: " . $ex->getMessage() . PHP_EOL);

            $error_data = json_decode($ex->getData(), true);

            $error_message = tr('payment_failed_error');

            if(is_array($error_data)) {

                Log::info(print_r($error_data , true));

                $error_message = array_key_exists('error', $error_data) ? $error_data['error']." " : "";
                
                $error_message .= array_key_exists('error_description', $error_data) ? $error_data['error_description']." " : "";

                $error_message .= array_key_exists('message', $error_data) ? $error_data['message'] : "";


            } else {

                $error_message = $ex->getMessage() . PHP_EOL;
            }

            Log::info("Pay API catch METHOD");

            PaymentRepo::subscription_payment_failure_save(Auth::user()->id, $subscription->id, $error_message);

            return redirect()->route('payment.failure')->with('flash_error' , $error_message);

        }

        foreach($payment->getLinks() as $link) {

            if($link->getRel() == 'approval_url') {

                $redirect_url = $link->getHref();
                break;
            }
        }

        // add payment ID to session

        Session::put('paypal_payment_id', $payment->getId());

        Session::put('subscription_id' , $subscription->id);

        if(isset($redirect_url)) {

            $last_payment = UserPayment::where('user_id' , Auth::user()->id)
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

            $user_payment->payment_id  = $payment->getId();
            $user_payment->subscription_id  = $subscription->id;
            $user_payment->user_id = Auth::user()->id;

            $user_payment->payment_mode = PAYPAL;

            // Coupon details

            $user_payment->is_coupon_applied = $is_coupon_applied;

            $user_payment->coupon_code = $request->coupon_code  ? $request->coupon_code  :'';

            $user_payment->coupon_amount = $coupon_amount;

            $user_payment->subscription_amount = $subscription->amount;

            $user_payment->coupon_reason = $is_coupon_applied == COUPON_APPLIED ? '' : $coupon_reason;


            $user_payment->save();

            $response_array = array('success' => true); 

            return redirect()->away($redirect_url);
        
        }

        return response()->json(Helper::null_safe($response_array) , 200);
                    
    }
    
    /**
     * @uses to store user payment details from the paypal response
     *
     * @param paypal ID
     *
     * @param paypal Token
     *
     * @return redirect to angular pages, depends on the response
     * 
     * @author vidhyar2612
     *
     * @edited : 
     */

    public function getPaymentStatus(Request $request) {

        $paypal_payment_id = Session::get('paypal_payment_id');
        
        // clear the session payment ID
     
        if (empty($request->paymentId) || empty($request->token) || empty($paypal_payment_id)) {
        	
            $error_message = tr('payment_id_session_not_found');

            $subscription_id = Session::get('subscription_id');

            PaymentRepo::subscription_payment_failure_save(Auth::user()->id, $subscription_id , $error_message , "");

            Session::forget('subscription_id');

            Session::forget('paypal_payment_id');

            return redirect()->route('payment.failure')->with('flash_error' , $error_message);

		} 

        try { 
            
            $payment = Payment::get($paypal_payment_id, $this->_api_context);
         
            // PaymentExecution object includes information necessary
            // to execute a PayPal account payment.
            // The payer_id is added to the request query parameters
            // when the user is redirected from paypal back to your site
            
            $execution = new PaymentExecution();

            $execution->setPayerId($request->PayerID);
         
            //Execute the payment

            $result = $payment->execute($execution, $this->_api_context);

        } catch(\PayPal\Exception\PayPalConnectionException $ex){

            $error_data = json_decode($ex->getData(), true);

            $error_message = tr('payment_failed_error');

            if(is_array($error_data)) {

                Log::info(print_r($error_data , true));

                $error_message = array_key_exists('error', $error_data) ? $error_data['error']." " : "";

                $error_message .= array_key_exists('error_description', $error_data) ? $error_data['error_description']." " : "";

                $error_message .= array_key_exists('message', $error_data) ? $error_data['message'] : "";


            } else {

                $error_message = $ex->getMessage() . PHP_EOL;
            }

            PaymentRepo::subscription_payment_failure_save("", "", $error_message , $paypal_payment_id);

            Session::forget('paypal_payment_id');

            Session::forget('subscription_id');

            return redirect()->route('payment.failure')->with('flash_error' , $error_message);

        }     

        if ($result->getState() == 'approved') { // payment made

            $user_payment_details = UserPayment::where('payment_id',$paypal_payment_id)->first();

            if($user_payment_details) {

                $user_payment_details->status = 1;

                $user_payment_details->amount = $payment->subscription_amount - $payment->coupon_amount;

                $user_payment_details->save();

                if($user = User::find($user_payment_details->user_id)) {

                    $user->user_type = 1;
                    
                    $user->save();

                }

            }

            Session::forget('paypal_payment_id');

            Session::forget('subscription_id');
            
            $response_array = array('success' => true , 'message' => tr('subscription_payment_success') ); 

            $responses = response()->json($response_array);

            $response = $responses->getData();

            return redirect()->route('user.subscription.success')->with('response', $response);
       
        } else {

            return back()->with('flash_error' ,tr('payment_not_approved_contact_admin'));
        }
        
    }

    /**
     * @uses Get the payment for PPV from user
     *
     * @param id = VIDEO ID
     *
     * @param user_id 
     *
     * @return redirect to success/faiture pages, depends on the payment status
     * 
     * @author shobanacs
     *
     * @edited : vidhyar2612
     */

    public function videoSubscriptionPay(Request $request) {

        // Get the PPV total amount based on the selected video

        $video = VideoTape::where('id', $request->id)->first();

        if(count($video) == 0 ) {

            Log::info("Video Details Not Found");

            $error_message =tr('video_details_not_found');

            return back()->with('flash_error' , $error_message);

        }

        $total = $video->ppv_amount;

        $coupon_amount = 0;

        $is_coupon_applied = DEFAULT_FALSE;

        $coupon_reason = "";

        if ($request->coupon_code) {

            $coupon = Coupon::where('coupon_code', $request->coupon_code)->first();

            if ($coupon) {

                $is_coupon_applied = DEFAULT_TRUE;

                if (!$coupon->status) {

                    $coupon_reason = tr('coupon_code_declined');

                } else {

                    $amount_convertion = $coupon->amount;

                    if ($coupon->amount_type == PERCENTAGE) {

                        $amount_convertion = amount_convertion($coupon->amount, $video->amount);

                    }

                    if ($amount_convertion <= $video->ppv_amount) {

                        $total = $video->ppv_amount - $amount_convertion;

                        $coupon_amount = $amount_convertion;

                    }
                }

            } else {

                $coupon_reason = tr('coupon_code_not_exists');

            }

        }

        $item = new Item();

        $item->setName(Setting::get('site_name')) // item name
                    ->setCurrency('USD')
                    ->setQuantity('1')
                    ->setPrice($total);
     
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        // add item to list
        $item_list = new ItemList();
        $item_list->setItems(array($item));
        $total = $total;
        $details = new Details();
        $details->setShipping('0.00')
            ->setTax('0.00')
            ->setSubtotal($total);


        $amount = new Amount();
        $amount->setCurrency('USD')
            ->setTotal($total)
            ->setDetails($details);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription('Payment for the Request');

        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(url('/user/payment/video-status'))
                    ->setCancelUrl(url('/'));

        $payment = new Payment();
        $payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));

        try {

            $payment->create($this->_api_context);

        } catch (\PayPal\Exception\PayPalConnectionException $ex) {

            $error_data = json_decode($ex->getData(), true);

            $error_message = tr('payment_failed_error');

            if(is_array($error_data)) {

                Log::info(print_r($error_data , true));

                $error_message = array_key_exists('error', $error_data) ? $error_data['error']." " : "";
                
                $error_message .= array_key_exists('error_description', $error_data) ? $error_data['error_description']." " : "";

                $error_message .= array_key_exists('message', $error_data) ? $error_data['message'] : "";


            } else {

                $error_message = $ex->getMessage() . PHP_EOL;
            }

            Log::info("Pay API catch METHOD");

            PaymentRepo::ppv_payment_failure_save(Auth::user()->id, $request->id, $error_message);

            return redirect()->route('payment.failure')->with('flash_error' , $error_message);
        }

        foreach($payment->getLinks() as $link) {

            if($link->getRel() == 'approval_url') {

                $redirect_url = $link->getHref();

                break;

            }
        
        }

        // Add payment ID to session

        Session::put('ppv_payment_id', $payment->getId());

        Session::put('video_tape_id', $request->id);

        if(isset($redirect_url)) {

            // $ppv_payment_details = PayPerView::where('user_id' , Auth::user()->id)->where('video_id' , $request->id)->where('amount',0)->first();
            $ppv_payment_details = PayPerView::where('user_id' , Auth::user()->id)->where('video_id' , $request->id)->where('amount',0)->first();

            // if(empty($ppv_payment_details)) {
                $ppv_payment_details = new PayPerView;
            // }

            $ppv_payment_details->expiry_date = date('Y-m-d H:i:s');

            $ppv_payment_details->payment_id  = $payment->getId();

            $ppv_payment_details->user_id = Auth::user()->id;

            $ppv_payment_details->video_id = $request->id;

            $ppv_payment_details->payment_mode = PAYPAL;

            Log::info("User Payment ".print_r($ppv_payment_details, true));

            $ppv_payment_details->coupon_amount = $coupon_amount;

            $ppv_payment_details->coupon_code = $request->coupon_code ? $request->coupon_code : "";

            $ppv_payment_details->ppv_amount = $video->amount;

            $ppv_payment_details->is_coupon_applied = $is_coupon_applied;

            $ppv_payment_details->coupon_reason = $is_coupon_applied ? $coupon_reason : '';

            $ppv_payment_details->save();

            $response_array = array('success' => true); 

            return redirect()->away($redirect_url);

        }

        return redirect()->route('payment.failure')->with('flash_error' , tr('something_wrong'));
                    
    }
    
    /**
     * @uses to store user payment details from the paypal response
     *
     * @param paypal ID
     *
     * @param paypal Token
     *
     * @return redirect to angular pages, depends on the 
     * 
     * @author shobanacs
     *
     * @edited : vidhyar2612
     */

    public function getVideoPaymentStatus(Request $request) {

        // Get the payment ID before session clear
        $ppv_payment_id = Session::get('ppv_payment_id');
        
        // clear the session payment ID
     
        if (empty($request->paymentId) || empty($request->token) || empty($ppv_payment_id)) {
            
            $error_message = tr('payment_id_session_not_found');

            $video_tape_id = Session::get('video_tape_id');

            PaymentRepo::ppv_payment_failure_save(Auth::user()->id, $video_tape_id , $error_message , "");

            Session::forget('video_tape_id');

            Session::forget('ppv_payment_id');

            return redirect()->route('payment.failure')->with('flash_error' , $error_message);

        }

        try { 
     
            $payment = Payment::get($ppv_payment_id, $this->_api_context);
         
            // PaymentExecution object includes information necessary
            // to execute a PayPal account payment.
            // The payer_id is added to the request query parameters
            // when the user is redirected from paypal back to your site
            
            $execution = new PaymentExecution();

            $execution->setPayerId($request->PayerID);
         
            //Execute the payment
            $result = $payment->execute($execution, $this->_api_context);

        } catch(\PayPal\Exception\PayPalConnectionException $ex){

            $error_data = json_decode($ex->getData(), true);

            $error_message = tr('payment_failed_error');

            if(is_array($error_data)) {

                Log::info(print_r($error_data , true));

                $error_message = array_key_exists('error', $error_data) ? $error_data['error']." " : "";
                
                $error_message .= array_key_exists('error_description', $error_data) ? $error_data['error_description']." " : "";

                $error_message .= array_key_exists('message', $error_data) ? $error_data['message'] : "";


            } else {

                $error_message = $ex->getMessage() . PHP_EOL;
            }

            PaymentRepo::ppv_payment_failure_save("", "", $error_message , $ppv_payment_id);

            Session::forget('ppv_payment_id');

            Session::forget('video_tape_id');

            return redirect()->route('payment.failure')->with('flash_error' , $error_message);

        }
     
       // echo '<pre>';print_r($result);echo '</pre>';exit; // DEBUG RESULT, remove it later
     
        if ($result->getState() == 'approved') { // payment made

            $ppv_details = PayPerView::where('payment_id',$ppv_payment_id)->first();

            $video_tape_details = $ppv_details->videoTape;

            // Check the PPV and video details

            if(count($ppv_details) == 0 || count($video_tape_details) == 0) {

                $error_message = tr('ppv_video_details_not_found');

                $video_tape_id = Session::get('video_tape_id');

                PaymentRepo::ppv_payment_failure_save(Auth::user()->id, $video_tape_id , $error_message , "");

                Session::forget('video_tape_id');

                Session::forget('ppv_payment_id');

                return redirect()->route('payment.failure')->with('flash_error' , $error_message);

            }

           // $ppv_details->amount = $ppv_details->videoTape ? $ppv_details->videoTape->ppv_amount : "0.00";

            $ppv_details->amount = $ppv_details->ppv_amount - $ppv_details->coupon_amount;

            Log::info("$ppv_details->amount".$ppv_details->amount);

             if ($video_tape_details->type_of_user == NORMAL_USER) {

                $ppv_details->type_of_user = tr('normal_users');

            } else if($video_tape_details->type_of_user == PAID_USER) {

                $ppv_details->type_of_user = tr('paid_users');

            } else if($video_tape_details->type_of_user == BOTH_USERS) {

                $ppv_details->type_of_user = tr('both_users');
            }


            if ($video_tape_details->type_of_subscription == ONE_TIME_PAYMENT) {

                $ppv_details->type_of_subscription = tr('one_time_payment');

            } else if($video_tape_details->type_of_subscription == RECURRING_PAYMENT) {

                $ppv_details->type_of_subscription = tr('recurring_payment');

            }
            
            $payment->status = PAID_STATUS;

            $payment->is_watched = NOT_YET_WATCHED;

            $ppv_details->save();

            if($ppv_details->amount > 0) {

                // Do Commission spilit  and redeems for moderator

                Log::info("ppv_commission_spilit started");

                PaymentRepo::ppv_commission_split($video_tape_details->id , $ppv_details->id);

                Log::info("ppv_commission_spilit END");  
                    
            }

            Session::forget('ppv_payment_id');

            Session::forget('video_tape_id');
            
            $response_array = array('success' => true , 'message' => tr('ppv_payment_success') ); 

            $responses = response()->json($response_array);

            $response = $responses->getData();

            return redirect()->route('user.video.success' , $video_tape_details->id)->with('flash_success', tr('ppv_payment_success'));

       
        } else {

            return back()->with('flash_error' ,tr('payment_not_approved_contact_admin'));
        }

    }
   
}
