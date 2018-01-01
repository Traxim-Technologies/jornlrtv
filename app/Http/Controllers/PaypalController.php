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

 
class PaypalController extends Controller {
   
    private $_api_context;
 
    public function __construct() {

        $this->middleware('PaypalCheck');
       
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

            $error_message = "Subscription Details Not Found";

            return redirect()->route("payment.failure")->with('flash_error' , $error_message);

        }

        $total = $subscription ? $subscription->amount : "1.00" ;

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

            $error_message = isset($error_data['error']) ? $error_data['error']: "".".".isset($error_data['error_description']) ? $error_data['error_description'] : "";

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

            $user_payment = UserPayment::where('user_id' , Auth::user()->id)->first();

            if($user_payment) {

                $expiry_date = $user_payment->expiry_date;

                $user_payment->expiry_date = date('Y-m-d H:i:s', strtotime($expiry_date. "+".$subscription->plan." months"));

            } else {

                $user_payment = new UserPayment;

                $user_payment->expiry_date = date('Y-m-d H:i:s',strtotime("+".$subscription->plan." months"));
            }

            $user_payment->payment_id  = $payment->getId();
            $user_payment->subscription_id  = $subscription->id;
            $user_payment->user_id = Auth::user()->id;
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
        
        // clear the session payment ID
     
        if (empty($request->paymentId) || empty($request->token) || empty($paypal_payment_id)) {
        	
            $error_message = "Payment ID - Session Not Found";

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

            $error_message = "Payment Failed";

            if(is_array($error_data)) {
                $error_message = isset($error_data['error']) ? $error_data['error']: "".".".isset($error_data['error_description']) ? $error_data['error_description'] : "";

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

                $user_payment_details->amount = $user_payment_details->getSubscription ? $user_payment_details->getSubscription->amount : 0;

                $user_payment_details->save();

                if($user = User::find($user_payment_details->user_id)) {

                    $user->user_type = 1;
                    
                    $user->save();

                }

            }

            Session::forget('paypal_payment_id');

            Session::forget('subscription_id');
            
            $response_array = array('success' => true , 'message' => "Payment Successful" ); 

            $responses = response()->json($response_array);

            $response = $responses->getData();

            return redirect()->route('user.subscription.success')->with('response', $response);
       
        } else {

            return back()->with('flash_error' , 'Payment is not approved. Please contact admin');
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

        if(count($video) == 0 ){
            return back()->with('flash_error' , "");
        }

        $total = $video->ppv_amount;

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

            $error_message = $ex->getMessage() . PHP_EOL;

            Log::info("Pay API catch METHOD");

            PaymentRepo::ppv_payment_failure_save($request->user_id, $request->id, $error_message);

            return back();
        }

        foreach($payment->getLinks() as $link) {

            if($link->getRel() == 'approval_url') {

                $redirect_url = $link->getHref();

                break;

            }
        
        }

        // Add payment ID to session

        Session::put('paypal_payment_id', $payment->getId());

        if(isset($redirect_url)) {

            $ppv_payment_details = PayPerView::where('user_id' , Auth::user()->id)->where('video_id' , $request->id)->where('amount',0)->first();

            if(empty($ppv_payment_details)) {
                $ppv_payment_details = new PayPerView;
            }

            $ppv_payment_details->expiry_date = date('Y-m-d H:i:s');
            $ppv_payment_details->payment_id  = $payment->getId();
            $ppv_payment_details->user_id = Auth::user()->id;
            $ppv_payment_details->video_id = $request->id;
            $ppv_payment_details->save();

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
     * @return redirect to angular pages, depends on the 
     * 
     * @author shobanacs
     *
     * @edited : vidhyar2612
     */

    public function getVideoPaymentStatus(Request $request) {

        // Get the payment ID before session clear
        $payment_id = Session::get('paypal_payment_id');
        
        // clear the session payment ID
     
        if (empty($request->PayerID) || empty($request->token)) {
            
          return back()->with('flash_error','Payment Failed!!');

        } 

        try { 
     
            $payment = Payment::get($payment_id, $this->_api_context);
         
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

            $error_message = $ex->getMessage() . PHP_EOL;

            PaymentRepo::ppv_payment_failure_save("", "", $error_message , $payment_id);

            Session::forget('paypal_payment_id');

            return back();

        }
     
       // echo '<pre>';print_r($result);echo '</pre>';exit; // DEBUG RESULT, remove it later
     
        if ($result->getState() == 'approved') { // payment made

            $payment = PayPerView::where('payment_id',$payment_id)->first();

            $payment->amount = $payment->videoTape->ppv_amount;

            if ($payment->videoTape->type_of_user == 1) {

                $payment->type_of_user = "Normal User";

            } else if($payment->videoTape->type_of_user == 2) {

                $payment->type_of_user = "Paid User";

            } else if($payment->videoTape->type_of_user == 3) {

                $payment->type_of_user = "Both Users";
            }


            if ($payment->videoTape->type_of_subscription == 1) {

                $payment->type_of_subscription = "One Time Payment";

            } else if($payment->videoTape->type_of_subscription == 2) {

                $payment->type_of_subscription = "Recurring Payment";

            }
            
            $payment->save();

            if($payment->amount > 0) {

               // Do Commission spilit  and redeems for moderator

                Log::info("ppv_commission_spilit started");

                PaymentRepo::ppv_commission_split($video->id , $payment->id , $video->uploaded_by);

                Log::info("ppv_commission_spilit END");  
                    
            }

            Session::forget('paypal_payment_id');
            
            $response_array = array('success' => true , 'message' => "Payment Successful" ); 

            $responses = response()->json($response_array);

            $response = $responses->getData();

            return redirect()->route('user.video.success' , $payment->video_id)->with('flash_success', tr('payment_successful'));

       
        } else {

            return back()->with('flash_error' , 'Payment is not approved. Please contact admin');
        }
            
           
    }
   
}
