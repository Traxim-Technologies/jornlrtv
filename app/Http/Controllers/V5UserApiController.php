<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Helpers\Helper;

use Exception, DB, Validator, Setting, Log;

use App\User, App\Card, App\Wishlist;

use App\VideoTape, App\VideoTapeTag;

use App\Channel;


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

    /**
     * @method channels_list_for_owners()
     *
     * @uses Get the user channels
     *
     * @created Vidhya R
     *
     * @updated vithya R
     *
     * @param Form data
     * 
     * @return JSON Response
     */

    public function channels_list_for_owners(Request $request) {

        try {

            $channels = Channel::

           $model = Channel::select('id as channel_id', 'name as channel_name')->where('is_approved', DEFAULT_TRUE)->where('status', DEFAULT_TRUE)
                ->where('user_id', $request->id)->get();

            if($model) {

                $response_array = array('success' => true , 'data' => $model);

            } else {
                $response_array = array('success' => false,'error_messages' => Helper::get_error_message(135),'error_code' => 135);
            }

            $response = response()->json($response_array, 200);
            
            return $response;

            return $this->sendResponse($message = "", $code = 0, $channels);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    }


    /**
     * Function Name : channels_view()
     *
     * @uses used to get the channel details
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param integer channel_id
     * 
     * @return json response
     */
    public function channels_view(Request $request) {

        try {

            $validator = Validator::make($request->all(),
                [
                    'channel_id' => 'required|integer|exists:channels,id',
                ],
                [
                    'exists' => 'The :attribute doesn\'t exists',
                ]
            );

            if ($validator->fails()) {

                $error_messages = implode(',', $validator->messages()->all());

                throw new Exception($error_messages, 101);
                
            }

            $data = array();

            $channel_details = Channel::where('id', $request->channel_id)
                                    ->where('user_id', $request->id)
                                    ->where('status', APPROVED)
                                    ->select('id as channel_id', 'name as channel_name','cover as channel_cover', 'description as channel_description', 'picture as channel_image')
                                    ->first();

            if(!$channel_details) {

                throw new Exception(Helper::get_error_message(50102), 50102);
            }

            $video_tapes = VideoRepo::channelVideos($request, $channel_details->id, '', $request->skip);

            $channel_details->subscribers_count = subscriberscnt($channel_details->id);

            $channel_details->videos = $video_tapes; 

            $response_array = ['success' => true, 'data' => $channel_details];
    

            return response()->json($response_array, 200);

        } catch (Exception $e) {

            $error_messages = $e->getMessage(); $error_code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $error_code];

            return response()->json($response_array, 200);
        }

    }

    /**
     * Function Name : video_tapes_view()
     *
     * @uses used to get the channel details
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param integer video_tape_id
     * 
     * @return json response
     */
    public function video_tapes_view(Request $request) {

        try {

            $validator = Validator::make($request->all(),
                [
                    'video_tape_id' => 'required|integer|exists:video_tapes,id',
                ],
                [
                    'exists' => 'The :attribute doesn\'t exists',
                ]
            );

            if ($validator->fails()) {

                $error_messages = implode(',', $validator->messages()->all());

                throw new Exception($error_messages, 906);
                
            }

            $data = array();

            $video_tape_details = VideoTape::where('id', $request->video_tape_id)
                                    ->where('user_id', $request->id)
                                    ->where('status', APPROVED)
                                    ->select('id as video_tape_id', 'title', 'description', 'default_image', 'age_limit', 'duration', 'video_publish_type', 'publish_status', 'publish_time', 'is_approved as is_admin_approved', 'status as video_status', 'watch_count', 'is_pay_per_view', 'type_of_subscription', 'ppv_amount', 'category_name','video_type', 'channel_id', 'user_ppv_amount as ppv_revenue', 'amount as ads_revenue', 'category_id')
                                    ->first();

            if(!$video_tape_details) {

                throw new Exception(Helper::get_error_message(906), 906);
            }

            $video_tape_details->video_publish_type_text = $video_tape_details->video_publish_type == PUBLISH_NOW ? tr('PUBLISH_NOW') : tr('PUBLISH_LATER');

            $video_types = [VIDEO_TYPE_UPLOAD => tr('VIDEO_TYPE_UPLOAD'), VIDEO_TYPE_LIVE => tr('VIDEO_TYPE_LIVE'), VIDEO_TYPE_YOUTUBE => tr('VIDEO_TYPE_YOUTUBE'), VIDEO_TYPE_OTHERS => tr('VIDEO_TYPE_OTHERS')];

            $video_tape_details->video_type_text = $video_types[$video_tape_details->video_type];

            $video_tape_details->total_revenue = $video_tape_details->ads_revenue + $video_tape_details->ppv_revenue;

            $channel_details = Channel::find($video_tape_details->channel_id);

            $video_tape_details->channel_name = $channel_details ? $channel_details->name: "";

            $video_tape_details->tags = VideoTapeTag::select('tag_id', 'tags.name as tag_name')
                                            ->leftJoin('tags', 'tags.id', '=', 'video_tape_tags.tag_id')
                                            ->where('video_tape_tags.status', TAG_APPROVE_STATUS)
                                            ->where('video_tape_id', $request->video_tape_id)
                                            ->get()->toArray();

            $video_tape_details->wishlist_count = get_wishlist_count($request->video_tape_id);

            $response_array = ['success' => true, 'data' => $video_tape_details];
    
            return response()->json($response_array, 200);

        } catch (Exception $e) {

            $error_messages = $e->getMessage(); $error_code = $e->getCode();

            $response_array = ['success' => false, 'error_messages' => $error_messages, 'error_code' => $error_code];

            return response()->json($response_array, 200);
        }

    }
}
