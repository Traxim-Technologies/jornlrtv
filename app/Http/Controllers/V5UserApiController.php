<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Helpers\Helper;

use Exception, DB, Validator, Setting, Log;

use App\User, App\Card;

class V5UserApiController extends Controller
{
	public function __construct(Request $request) {

        $this->middleware('UserApiVal');

    }
    
    /**
     * @method cards_add()
     *
     * @uses Update the selected payment mode 
     *
     * @created Vidhya R
     *
     * @updated vithya R
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

                throw new Exception(Helper::get_error_message(50108), 50108);
            }
        
            $validator = Validator::make(
                    $request->all(),
                    [
                        'card_token' => 'required',
                    ]
                );

            if ($validator->fails()) {

                $error = implode(',',$validator->messages()->all());
             
                throw new Exception($error , 101);

            } 

            Log::info("INSIDE CARDS ADD");

            $user_details = User::find($request->id);

            if(!$user_details) {

                throw new Exception(Helper::get_error_message(9001), 9001);
                
            }

            // Get the key from settings table
            
            $customer = \Stripe\Customer::create([
                    "card" => $request->card_token,
                    "email" => $user_details->email,
                    "description" => "Customer for ".Setting::get('site_name', 'ST'),
                ]);

            if(!$customer) {
                
                throw new Exception(Helper::get_error_message(117) , 117);

            }

            $customer_id = $customer->id;

            $card_details = new Card;

            $card_details->user_id = $request->id;

            $card_details->customer_id = $customer_id;

            $card_details->card_token = $customer->sources->data ? $customer->sources->data[0]->id : "";

            $card_details->card_name = $customer->sources->data ? $customer->sources->data[0]->brand : "";

            $card_details->last_four = $customer->sources->data? $customer->sources->data[0]->last4 : "";

            $card_details->month = $customer->sources->data ? $customer->sources->data[0]->exp_month : "";

            $card_details->year = $customer->sources->data ? $customer->sources->data[0]->exp_year : "";

            // Check is any default is available

            $check_card_details = Card::where('user_id',$request->id)->count();

            $card_details->is_default = $check_card_details ? 0 : 1;

            if($card_details->save()) {

                if($user_details) {

                    $user_details->card_id = $check_card_details ? $user_details->card_id : $card_details->id;

                    $user_details->save();
                }

                $data = Card::where('id' , $card_details->id)->select('id as card_id' , 'customer_id' , 'last_four' ,'card_name', 'card_token' , 'is_default' )->first();

                $response_array = ['success' => true , 'message' => Helper::get_message(50007), 'data' => $data];

                DB::commit();

            	return response()->json($response_array , 200);

            } else {

                throw new Exception(Helper::get_error_message(117), 117);
                
            }
       
        } catch(Stripe_CardError | \Stripe\StripeInvalidRequestError | Stripe_AuthenticationError | Stripe_ApiConnectionError | Stripe_Error $e) {

            Log::info("error1");

            $error1 = $e->getMessage();

            $response_array = ['success' => false , 'error' => $error1 ,'error_code' => 903];

            return response()->json($response_array , 200);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
   
    }
}
