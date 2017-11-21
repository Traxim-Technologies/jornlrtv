<?php

namespace App\Http\Controllers;

use App\Repositories\VideoTapeRepository as VideoRepo;

use Illuminate\Http\Request;

use App\Helpers\Helper;

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

use App\Redeem;

use App\RedeemRequest;

use App\Channel;

use App\LikeDislikeVideo;

use App\Card;

use App\Subscription;

use App\UserPayment;

use App\LiveVideo;

use App\LiveVideoPayment;

use App\ChatMessage;

use App\Viewer;

use Exception;

use App\PayPerView;

class UserApiController extends Controller {

    public function __construct(Request $request) {

        $this->middleware('UserApiVal' , array('except' => ['register' , 'login' , 'forgot_password','search_video' , 'privacy','about' , 'terms','contact', 'home', 'trending' , 'getSingleVideo', 'get_channel_videos' ,  'help', 'single_video', 'reasons', 'get_live_url', 'video_detail']));

    }

    
    public function broadcast(Request $request) {
        
        $validator = Validator::make($request->all(),array(
                'title' => 'required',
                'amount' => 'numeric',
                'payment_status'=>'required',
               // 'type' => 'required',
                'description'=>'required',
                'channel_id'=>'required|exists:channels,id',
                'user_id'=>'required|exists:users,id',
            )
        );
        
        if($validator->fails()) {

            $error_messages = implode(',', $validator->messages()->all());

            $response_array = ['success' => false , 'error_messages' => $error_messages , 'error_code' => 001];
        } else {

            $last = LiveVideo::orderBy('port_no', 'desc')->first();

            $model = new LiveVideo;
            $model->title = $request->title;
            $model->payment_status = $request->payment_status;
            $model->type = $request->type ? $request->type : TYPE_PUBLIC;
            $model->channel_id = $request->channel_id;
            $model->amount = 0;

            if($request->payment_status) {

                $model->amount = ($request->amount > 0) ? $request->amount : 1;

            }
            $model->description = ($request->has('description')) ? $request->description : null;
            $model->is_streaming = DEFAULT_TRUE;
            $model->status = DEFAULT_FALSE;
            $model->user_id = $request->user_id;
            $model->virtual_id = md5(time());
            $model->unique_id = $model->title;
            $model->snapshot = asset('images/live_stream.jpg');

            $destination_port = 44104;

            if ($last) {

                if ($last->port_no) {

                    $destination_port = $last->port_no + 2;

                }

            }

            $model->port_no = $destination_port;

            $model->save();

            /*// $usrModel

            $userModel = User::find($request->id);


            $appSettings = json_encode([
                'SOCKET_URL' => Setting::get('SOCKET_URL'),
                'CHAT_ROOM_ID' => isset($model) ? $model->id : null,
                'BASE_URL' => Setting::get('BASE_URL'),
                'TURN_CONFIG' => [],
                'TOKEN' => $request->token,
                'USER_PICTURE'=>$userModel->chat_picture,
                'NAME'=>$userModel->name,
                'CLASS'=>'left',
                'USER' => ['id' => $request->id, 'role' => "model"],
                'VIDEO_PAYMENT'=>null,
            ]);*/

            if ($model) {
                $response_array = [
                    'success' => true , 

                    'data' => $model, 

                    /*'appSettings'=> $appSettings, */

                    'port_no'=>$model->port_no, 

                    'message'=>tr('video_broadcating_success')
                ];

                
            } else {
                $response_array = ['success' => false , 'error_messages' => Helper::get_error_message(003) , 'error_code' => 003];
            }
        }
        return response()->json($response_array,200);

    }

    /**** Live Videos Api *************/

    public function live_videos(Request $request) {

        $validator = Validator::make(
            $request->all(),
            array(
                'skip'=>'required|numeric',
                'browser'=>'required',
                'device_type'=>'required|in:'.DEVICE_ANDROID.','.DEVICE_IOS.','.DEVICE_WEB,
            ));

        if ($validator->fails()) {
            // Error messages added in response for debugging
            $errors = implode(',',$validator->messages()->all());

            $response_array = ['success' => false, 'error_messages' => $errors , 'error' => $errors,'error_code' => 101];

        } else {

                $model = LiveVideo::where('is_streaming', DEFAULT_TRUE)
                        ->where('live_videos.status', DEFAULT_FALSE)
                        ->videoResponse()
                        ->leftJoin('users' , 'users.id' ,'=' , 'live_videos.user_id')
                        ->leftJoin('channels' , 'channels.id' ,'=' , 'live_videos.channel_id')
                        ->orderBy('live_videos.created_at', 'desc')
                        ->skip($request->skip)
                        ->take(Setting::get('admin_take_count' ,12))->get();


                $values = [];



                foreach ($model as $key => $value) {

                    $videopayment = LiveVideoPayment::where('live_video_id', $value->video_id)
                        ->where('live_video_viewer_id', $request->id)
                        ->where('status',DEFAULT_TRUE)->first();

                        // dd($value);

                    $null_safe_value = [
                        "video_image"=> $value->snapshot,
                        "channel_image"=> $value->channel_image ? $value->channel_image : '',
                        "title"=> $value->title,
                        "channel_name"=> $value->channel_name ? $value->channel_name : '',
                        "watch_count"=> $value->viewers,
                        "video"=> $value->video_url ? $value->video_url : VideoRepo::getUrl($value, $request),
                        "video_tape_id"=>$value->video_id,
                        "channel_id"=>$value->channel_id,
                        "description"=> $value->description,
                        "user_id"=>$value->id,
                        "name"=> $value->name,
                        "email"=> $value->email,
                        "user_picture"=> $value->chat_picture,
                        'payment_status' => $value->payment_status ? $value->payment_status : 0,
                        "amount"=> $value->amount,
                        "publish_time"=> $value->date,
                        'currency'=> Setting::get('currency'),
                        "share_link"=>route('user.live_video.start_broadcasting', array('id'=>$value->unique_id,'c_id'=>$value->channel_id)),
                        'video_stopped_status'=>$value->video_stopped_status,
                        'video_payment_status'=> $videopayment ? DEFAULT_TRUE : DEFAULT_FALSE

                    ];

                    $values[] = $null_safe_value;
                }

                $response_array = ['success'=>true, 'data'=>$values];

        }

        return response()->json($response_array, 200);


    }   

    public function save_live_video(Request $request) {

        $validator = Validator::make($request->all(),array(
                'title' => 'required',
                'amount' => 'required|numeric',
                'payment_status'=>'required|numeric',
                'channel_id'=>'required|exists:channels,id',
               // 'video_url'=>'required',
            )
        );
        
        if($validator->fails()) {

            $errors = implode(',', $validator->messages()->all());

            $response_array = ['success' => false , 'error_messages' => $errors, 'error' => $errors , 'error_code' => 001];
        } else {

            $user = User::find($request->id);

            if ($user) {

                if ($user->user_type) {

                    $model = new LiveVideo;
                    $model->title = $request->title;
                    $model->channel_id = $request->channel_id;
                    $model->payment_status = $request->payment_status;
                    $model->type = TYPE_PUBLIC;
                    $model->amount = ($request->payment_status) ? (($request->has('amount')) ? $request->amount : 0 ): 0;

                    $model->description = ($request->has('description')) ? $request->description : null;
                    $model->is_streaming = DEFAULT_TRUE;
                    $model->status = DEFAULT_FALSE;
                    $model->user_id = $request->id;
                    $model->virtual_id = md5(time());
                    $model->unique_id = $model->title;
                    $model->snapshot = asset("/images/live_stream.jpg");
                    $model->start_time = getUserTime(date('H:i:s'), ($user) ? $user->timezone : '', "H:i:s");

                    // $model->video_url = 'rtsp://104.236.1.170:1935/live/'.$user->id.'_'.$model->id;
                    // $model->video_url = $request->video_url;

                    $model->save();

                    if ($model) {

                        $model->video_url = Setting::get('mobile_rtsp').$user->id.'_'.$model->id;

                        $model->save();

                        $response_array = [
                            'success' => true , 
                            "video_image"=> $model->snapshot,
                            "channel_image"=> $model->channel ? $model->channel->picture : '',
                            "title"=> $model->title,
                            "channel_name"=> $model->channel ? $model->channel->name : '',
                            "watch_count"=> $model->viewer_cnt ? $model->viewer_cnt : 0,
                            "video"=>$model->video_url,
                            "video_tape_id"=>$model->id,
                            "channel_id"=>$model->channel_id,
                            'unique_id'=>$model->unique_id,
                            "description"=> $model->description,
                            "user_id"=>$model->user ? $model->user->id : '',
                            "name"=> $model->user->name,
                            "email"=> $model->user->email,
                            "user_picture"=> $model->user->chat_picture,
                            'payment_status' => $model->payment_status ? $model->payment_status : 0,
                            "amount"=> $model->amount,
                            'currency'=> Setting::get('currency'),
                            "share_link"=>route('user.live_video.start_broadcasting', array('id'=>$model->unique_id,'c_id'=>$model->channel_id)),
                            'is_streaming'=>$model->is_streaming,
                        ];
                    } else {
                        $response_array = ['success' => false , 'error_messages' => Helper::get_error_message(003) , 'error_code' => 003];
                    }

                } else {

                     $response_array = ['success'=>false, 'error_messages'=>Helper::get_error_message(165), 'error_code'=>165];

                }
            } else {

                $response_array = ['success'=>false, 'error_messages'=>Helper::get_error_message(166), 'error_code'=>166];
            }
        }
        return response()->json($response_array,200);

    } 


    public function live_video(Request $request) {
        $validator = Validator::make(
            $request->all(),
            array(
                'browser'=>'required',
                'device_type'=>'required|in:'.DEVICE_ANDROID.','.DEVICE_IOS.','.DEVICE_WEB,
                'video_tape_id'=>'required|exists:live_videos,id',
            ));

        if ($validator->fails()) {

            // Error messages added in response for debugging

            $errors = implode(',',$validator->messages()->all());

            $response_array = ['success' => false,'error_messages' => $errors,'error_code' => 101];

        } else {

            $model = LiveVideo::where('id',$request->video_tape_id)->first();

            if ($model) {

                if ($model->is_streaming) {

                    if(!$model->status) {

                        $user = User::find($model->user_id);

                        if ($user) {

                            // Load Based on id
                            $chat = ChatMessage::where('live_video_id', $model->id)->get();

                            $messages = [];

                            if(count($chat) > 0) {

                                foreach ($chat as $key => $value) {
                                    
                                    $messages[] = Helper::null_safe([
                                        // 'id' => $value->id, 
                                        'user_id' => ($value->getUser)? $value->user_id : $value->live_video_viewer_id, 
                                        'username' => ($value->getUser) ? $value->getUser->name : (($value->getViewUser) ? $value->getViewUser->name : ""),

                                        'picture'=> ($value->getUser) ? $value->getUser->chat_picture : (($value->getViewUser) ? $value->getViewUser->chat_picture : ""),
                                       // 'live_video_id'=>$value->live_video_id, 
                                        'comment'=>$value->message, 
                                        'diff_human_time'=>$value->created_at->diffForHumans()]);

                                }
                                
                            }

                            $videopayment = LiveVideoPayment::where('live_video_id', $model->id)
                                ->where('live_video_viewer_id', $request->id)
                                ->where('status',DEFAULT_TRUE)->first();

                            $suggestions = [];
    

                            $data = [
                                "video_image"=> $model->snapshot,
                                "channel_image"=> $model->channel?$model->channel->picture: '',
                                "title"=> $model->title,
                                "channel_name"=> $model->channel ? $model->channel->name : '',
                                "watch_count"=> $model->viewer_cnt ? $model->viewer_cnt : 0,
                                "video"=> $model->video_url ? VideoRepo::rtmpUrl($model) : VideoRepo::getUrl($model, $request),
                                'unique_id'=>$model->unique_id,
                                "video_tape_id"=>$model->id,
                                "channel_id"=>$model->channel_id,
                                "description"=> $model->description,
                                "user_id"=>$model->user ? $model->user->id : '',
                                "name"=> $model->user ? $model->user->name : '',
                                "email"=> $model->user ? $model->user->email : '',
                                "user_picture"=> $model->user ? $model->user->chat_picture : '',
                                'payment_status' => $model->payment_status ? $model->payment_status : 0,
                                "amount"=> $model->amount,
                                "publish_time"=> $model->date,
                                'currency'=> Setting::get('currency'),
                                "share_link"=>route('user.live_video.start_broadcasting', array('id'=>$model->unique_id,'c_id'=>$model->channel_id)),
                                'video_stopped_status'=>$model->video_stopped_status,
                                'video_payment_status'=> $videopayment ? DEFAULT_TRUE : DEFAULT_FALSE,
                                'comments'=>$messages,  
                                'suggestions'=>$suggestions,
                            ];

                            $response_array = ['success'=>true, 'data'=>$data];

                       }  else {

                            $response_array = ['success'=>false, 'error'=>Helper::get_error_message(166), 'error_code'=>150];

                       }

                    } else {

                        $response_array = ['success'=>false, 'error'=>Helper::get_error_message(163), 'error_code'=>163];

                    }

                } else {

                    $response_array = ['success'=>false, 'error'=>Helper::get_error_message(164), 'error_code'=>164];

                }

            } else {

                $response_array = ['success'=>false, 'error'=>Helper::get_error_message(165), 'error_code'=>165];

            }
        }

        return response()->json($response_array, 200);

    }


    public function save_chat(Request $request) {

        $validator = Validator::make(
            $request->all(),
            array(
                'video_tape_id'=>'required|exists:live_videos,id',
                'viewer_id'=>'required|exists:users,id',
                'message'=>'required',
                'type'=>'required|in:uv,vu',
                'delivered'=>'required',
            ));

        if ($validator->fails()) {
            // Error messages added in response for debugging
            $errors = implode(',',$validator->messages()->all());

            $response_array = ['success' => false,'error_messages' => $errors,'error_code' => 101];

        } else {

            $model = new ChatMessage;

            $model->live_video_id = $request->video_tape_id;

            $model->user_id = $request->id;

            $model->live_video_viewer_id = $request->viewer_id;

            $model->message = $request->message;

            $model->type = $request->type;

            $model->delivered = $request->delivered;

            $model->save();

            Log::info("saving Data");

            Log::info(print_r("Data".$model, true));

            $response_array = ['success'=>true, 'data'=>$model];
        }

        return response()->json($response_array, 200);
    }


    public function video_subscription(Request $request) {

        $validator = Validator::make(
            $request->all(),
            array(
                'video_tape_id'=>'required|exists:live_videos,id',
                'payment_id'=>'required',

            ));

        if ($validator->fails()) {
            // Error messages added in response for debugging
            $errors = implode(',',$validator->messages()->all());

            $response_array = ['success' => false,'error_messages' => $errors,'error_code' => 101];

        } else {

            $subscription = LiveVideo::find($request->video_tape_id);

            $user_payment = new LiveVideoPayment;

            $check_live_video_payment = LiveVideoPayment::where('live_video_viewer_id' , $request->id)->where('live_video_id' , $request->video_tape_id)->first();

            if($check_live_video_payment) {
                $user_payment = $check_live_video_payment;
            }

            // $user_payment->expiry_date = date('Y-m-d H:i:s');
            $user_payment->payment_id  = $request->payment_id;
            $user_payment->live_video_viewer_id = $request->id;
            $user_payment->live_video_id = $request->video_tape_id;
            
            $user_payment->user_id = $subscription->user_id;

            $user_payment->status = DEFAULT_TRUE;

            $user_payment->amount = $subscription->amount;

            $user_payment->save();

            if($user_payment) {

                $total = $subscription->amount;

                // Commission Spilit 

                $admin_commission = Setting::get('admin_commission')/100;

                $admin_amount = $total * $admin_commission;

                $user_amount = $total - $admin_amount;

                $user_payment->admin_amount = $admin_amount;

                $user_payment->user_amount = $user_amount;

                $user_payment->save();

                // Commission Spilit Completed

                if($user = User::find($user_payment->user_id)) {

                    $user->total_admin_amount = $user->total_admin_amount + $admin_amount;

                    $user->total_user_amount = $user->total_user_amount + $user_amount;

                    $user->remaining_amount = $user->remaining_amount + $user_amount;

                    $user->total = $user->total + $total;

                    $user->save();

                    add_to_redeem($user->id, $user_amount);
                
                }

            }

            $viewerModel = User::find($request->id);
         

            $response_array = ['success'=>true, 'message'=>tr('payment_success'), 
                        'data'=>['id'=>$request->id,
                                 'token'=>$viewerModel ? $viewerModel->token : '']];

        }

        return response()->json($response_array, 200);

    }

    public function get_viewers(Request $request) {

        $validator = Validator::make(
            $request->all(),
            array(
                'video_tape_id'=>'required|exists:live_videos,id',
            ));

        if ($validator->fails()) {
            // Error messages added in response for debugging
            $errors = implode(',',$validator->messages()->all());

            $response_array = ['success' => false,'error_messages' => $errors,'error_code' => 101];

        } else {


        // Load Viewers model

            $model = Viewer::where('video_id', $request->video_tape_id)->where('user_id', $request->id)->first();

            if(!$model) {

                $model = new Viewer;

                $model->video_id = $request->video_tape_id;

                $model->user_id = $request->id;

            }

            $model->count = ($model->count) ? $model->count + 1 : 1;

            $model->save();

            if ($model) {


                if ($model->getVideo) {

                    $model->getVideo->viewer_cnt += 1;

                    $model->getVideo->save();
                    
                }

            }

            $response_array  = ['success'=>true, 
                'viewer_cnt'=> $model->getVideo ? $model->getVideo->viewer_cnt : 0];

        }

        return response()->json($response_array);
    }

    public function peerProfile(Request $request) {

        $validator = Validator::make(
            $request->all(),
            array(
                'peer_id'=>'required|exists:users,id',
            ));

        if ($validator->fails()) {
            // Error messages added in response for debugging
            $errors = implode(',',$validator->messages()->all());

            $response_array = ['success' => false,'error_messages' => $errors,'error_code' => 101];

        } else {

            $user = User::find($request->peer_id);


            $response_array = Helper::null_safe(array(
                'success' => true,
                'id' => $user->id,
                'name' => $user->name,
                'mobile' => $user->mobile,
                'gender' => $user->gender,
                'email' => $user->email,
                'picture' => $user->picture,
                'chat_picture' => $user->chat_picture,
                'description'=>$user->description,
                'token' => $user->token,
                'token_expiry' => $user->token_expiry,
                'login_by' => $user->login_by,
                'social_unique_id' => $user->social_unique_id,
            ));

            $response_array = response()->json(Helper::null_safe($response_array), 200);

        }

    
        return $response_array;

    }

    public function close_streaming(Request $request) {

        $validator = Validator::make(
            $request->all(), array(
                'video_tape_id'=>'required|exists:live_videos,id',
        ));

        if ($validator->fails()) {
            // Error messages added in response for debugging
            $errors = implode(',',$validator->messages()->all());

            $response_array = ['success' => false,'error_messages' => $errors,'error_code' => 101];

        } else {

            // Load Model
            $model = LiveVideo::find($request->video_tape_id);

            $model->status = DEFAULT_TRUE;

            $model->end_time = getUserTime(date('H:i:s'), ($model->user) ? $model->user->timezone : '', "H:i:s");

            $model->no_of_minutes = getMinutesBetweenTime($model->start_time, $model->end_time);

            if ($model->save()) {

                $response_array = ['success'=>true, 'message'=>tr('streaming_stopped')];
            }
        }

        return response()->json($response_array,200);
    }


    public function checkVideoStreaming(Request $request) {

        $validator = Validator::make(
            $request->all(), array(
                'video_tape_id'=>'required|exists:live_videos,id',
        ));

        if ($validator->fails()) {
            // Error messages added in response for debugging
            $errors = implode(',',$validator->messages()->all());

            $response_array = ['success' => false,'error_messages' => $errors,'error_code' => 101];

        } else {

            $video = LiveVideo::find($request->video_tape_id);


            $user = User::find($request->id);

            $status = false;

            if ($user) {

                if ($user->token == $request->token) {

                    $status = false;

                    $token = $user->token;

                } else {

                    $status = true;

                    $token = $user->token;

                }
            }

            if ($video) {

                if($video->is_streaming) {

                    if (!$video->status) {

                        $response_array = ['success'=> true, 
                            'message'=>tr('video_streaming'), 
                            'viewer_cnt'=>$video->viewer_cnt ? $video->viewer_cnt : 0,
                            'data'=> ['status'=>$status, 'token'=>$token]];


                    } else {

                        $response_array = ['success'=> false, 'message'=>tr('streaming_stopped')];


                    }

                } else {

                    $response_array = ['success'=> false, 'message'=>tr('no_streaming_video_present')];

                }

            } else {

                $response_array = ['success'=> false, 'message'=>tr('no_live_video_present')];

            }
           

            return response()->json($response_array,200);

        }
    }



    public function stripe_payment_video(Request $request) {

        $userModel = User::find($request->id);

        if ($userModel->card_id) {

            $user_card = Card::find($userModel->card_id);

            if ($user_card && $user_card->is_default) {

                $video = LiveVideo::find($request->video_tape_id);

                if($video && !$video->status && $video->is_streaming) {

                    $total = $video->amount;

                    // Get the key from settings table
                    $stripe_secret_key = Setting::get('stripe_secret_key');

                    $customer_id = $user_card->customer_id;
                    
                    if($stripe_secret_key) {

                        \Stripe\Stripe::setApiKey($stripe_secret_key);
                    } else {

                        $response_array = array('success' => false, 'error_messages' => Helper::get_error_message(902) , 'error_code' => 902);

                        return response()->json($response_array , 200);

                        // return back()->with('flash_error', Helper::get_error_message(902));
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

                            $user_payment = new LiveVideoPayment;
                            $user_payment->payment_id  = $payment_id;
                            $user_payment->live_video_viewer_id = $request->id;
                            $user_payment->user_id = $video->user_id;
                            $user_payment->live_video_id = $video->id;
                            $user_payment->status = 1;
                            $user_payment->amount = $amount;

                            // Commission Spilit 

                            $admin_commission = Setting::get('admin_commission')/100;

                            $admin_amount = $amount * $admin_commission;

                            $user_amount = $amount - $admin_amount;

                            $user_payment->admin_amount = $admin_amount;

                            $user_payment->user_amount = $user_amount;

                            $user_payment->save();

                            // Commission Spilit Completed

                            if($user = User::find($user_payment->user_id)) {

                                $user->total_admin_amount = $user->total_admin_amount + $admin_amount;

                                $user->total_user_amount = $user->total_user_amount + $user_amount;

                                $user->remaining_amount = $user->remaining_amount + $user_amount;

                                $user->total = $user->total + $total;

                                $user->save();

                                add_to_redeem($user->id, $user_amount);
                            
                            }

                            $data = ['id'=> $request->id, 'token'=> $user->token , 'payment_id' => $payment_id];

                            $response_array = array('success' => true, 'message'=>tr('payment_success'),'data'=> $data);

                        } else {

                            $response_array = array('success' => false, 'error_messages' => Helper::get_error_message(902) , 'error_code' => 902);

                        }
                    
                    } catch (\Stripe\StripeInvalidRequestError $e) {

                        Log::info(print_r($e,true));

                        $response_array = array('success' => false , 'error_messages' => Helper::get_error_message(903) ,'error_code' => 903);


                       return response()->json($response_array , 200);
                    
                    }

                
                } else {

                    $response_array = array('success' => false , 'error_messages' => tr('no_live_video_found'));
                    
                }


            } else {

                // return back()->with('flash_error', tr('no_default_card_available'));

                $response_array = array('success' => false , 'error_messages' => tr('no_default_card_available'));

            }

        } else {

            // return back()->with('flash_error', tr('no_default_card_available'));

            $response_array = array('success' => false , 'error_messages' => tr('no_default_card_available'));

        }


        return response()->json($response_array,200);
        

    }

    public function get_live_url(Request $request) {

        $id = $request->video_id;

        $device_type = $request->device_type;

        $browser = $request->browser;

        \Log::info("Live Video Id ".$id);

        $video = LiveVideo::where('id', $id)->first(); 

        if ($video) {

            if($video->is_streaming) {

                if (!$video->status) {


                    if ($video->video_url) {

                        $sdp = $video->user_id.'_'.$video->id;

                        $browser = $browser ? strtolower($browser) : get_browser();

                        if (strpos($browser, 'safari') !== false) {
                            
                            $url = "http://".Setting::get('cross_platform_url')."/live/".$sdp."/playlist.m3u8";  

                        } else {

                            $url = "rtmp://".Setting::get('cross_platform_url')."/live/".$sdp;
                        }

                    } else {

                        $sdp = $video->user_id.'-'.$video->id.'.sdp';

                        if ($device_type == DEVICE_ANDROID) {

                            $url = "rtsp://".Setting::get('cross_platform_url')."/live/".$sdp;

                        } else if($device_type == DEVICE_IOS) {

                            $url = "http://".Setting::get('cross_platform_url')."/live/".$sdp."/playlist.m3u8";

                        } else {

                            $browser = $browser ? strtolower($browser) : get_browser();

                            if (strpos($browser, 'safari') !== false) {
                                
                                $url = "http://".Setting::get('cross_platform_url')."/live/".$sdp."/playlist.m3u8";  

                            } else {

                                $url = "rtmp://".Setting::get('cross_platform_url')."/live/".$sdp;
                            }

                        }
                    }

                    $response_array = ['success'=> true, 'url'=>$url];

                } else {

                    $response_array = ['success'=> false, 'message'=>tr('stream_stopped')];

                }

            } else {

                $response_array = ['success'=> false, 'message'=>tr('no_streaming_video_present')];

            }

        } else {

            $response_array = ['success'=> false, 'message'=>tr('no_live_video_present')];

        }

        return response()->json($response_array);
 
    }


    public function save_vod(Request $request) {


        $data = explode(',', $request->video_blob);

        if ($data[1] != '') {

            $fileName = $request->id.'_'.$request->video_id.'.webm';

            file_put_contents(join(DIRECTORY_SEPARATOR, [public_path(), 'uploads', 'vod',$fileName]), base64_decode($data[1]));

            $live = LiveVideo::find($request->video_id);

            if ($live) {

                $model = new VideoTape;

                $model->channel_id = $live->channel_id;

                $model->unique_id = $live->title;

                $model->title = $live->title;

                $model->description = $live->description;

                $model->default_image = $live->snapshot;

                $model->video = asset('uploads/vod/'.$fileName);

                $model->status = DEFAULT_TRUE;

                $model->compress_status = DEFAULT_TRUE;

                $model->video_type = VIDEO_TYPE_LIVE;

                $model->save();

                $response_array = ['success'=>true, 'model'=>$model];

                return response()->json($response_array);

            } else{

                $response_array = ['success'=>false, 'error_message'=>tr('no_live_video_found')];

                return response()->json($response_array);

            }
        
            
        }

        $response_array = ['success'=>false, 'error_message'=>tr('no_live_video_found')];

        return response()->json($response_array);


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
           
            $response_array = array('success' => false, 'error' => 'Invalid Input', 'error_code' => 401, 'error_messages' => $error_messages );
       
        } else {

            $user = User::find($request->id);

            if(Hash::check($request->old_password,$user->password)) {

                $user->password = \Hash::make($request->password);
                
                $user->save();

                $response_array = Helper::null_safe(array('success' => true , 'message' => Helper::get_message(102)));

            } else {

                $response_array = array('success' => false , 'error' => Helper::get_error_message(131),'error_messages' => Helper::get_error_message(131) ,'error_code' => 131);
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

            if($history = UserHistory::where('user_histories.user_id' , $request->id)->where('video_tape_id' ,$request->video_tape_id)->first()) {

                $response_array = array('success' => true , 'error_messages' => Helper::get_error_message(145) , 'error_code' => 145);

            } else {

                // Save Wishlist

                if($request->id) {

                    $rev_user = new UserHistory();
                    $rev_user->user_id = $request->id;
                    $rev_user->video_tape_id = $request->video_tape_id;
                    $rev_user->status = DEFAULT_TRUE;
                    $rev_user->save();

                }

                $response_array = array('success' => true);
           
            }

            $payperview = PayPerView::where('user_id', $request->id)
                            ->where('video_id',$request->video_tape_id)
                            ->where('status',0)->first();

            if ($payperview) {

                $payperview->status = DEFAULT_TRUE;

                $payperview->save();

            }


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
                'video_tape_id' => 'required|integer|exists:video_tapes,id'
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
     * Function Name : add_wishlist()
     *
     * @usage_place : MOBILE & WEB
     *
     * To add a wishlist based on user id
     *
     * @param Integer $request - Video Id
     *
     * @return response of Boolean with message
     */
    public function add_wishlist(Request $request) {

        $validator = Validator::make(
            $request->all(),
            array(
                'video_tape_id' => 'required|integer|exists:video_tapes,id',
            ),
            array(
                'exists' => 'The :attribute doesn\'t exists please provide correct video id',
                'unique' => 'The :attribute already added in wishlist.'
            )
        );

        if ($validator->fails()) {

            $error = implode(',', $validator->messages()->all());

            $response_array = array('success' => false, 'error_messages' => $error, 'error_code' => 101);


            $wishlist = Wishlist::where('user_id' , $request->id)->where('video_tape_id' , $request->video_tape_id)->first();

            if(count($wishlist) > 0) {


            } else {

                //Save Wishlist
                $wishlist = new Wishlist();
                $wishlist->user_id = $request->id;
                $wishlist->video_tape_id = $request->video_tape_id;
                $wishlist->status = DEFAULT_TRUE;
                $wishlist->save();
            }
           
            $message = "Added to wishlist";

            $response_array = array('success' => true ,'wishlist_id' => $wishlist->id , 'wishlist_status' => $wishlist->status,'message' => $message);
        }

        return response()->json($response_array, 200);
    
    }

    /**
     * Function Name : delete_wishlist()
     *
     * @usage_place : MOBILE & WEB
     *
     * To delete wishlist based on user id
     * 
     * @param intger $request - Video tape id
     *
     * @return response of success/failure message
     */
    public function delete_wishlist(Request $request) {

        $validator = Validator::make(
            $request->all(),
            array(
                'video_tape_id' => 'required|integer|exists:video_tapes,id',
            ),
            array(
                'exists' => 'The :attribute doesn\'t exists please add to wishlists',
            )
        );

        if ($validator->fails()) {

            $error = implode(',', $validator->messages()->all());

            $response_array = array('success' => false, 'error_messages' => $error, 'error_code' => 101);

        } else {

            /** Clear All wishlist of the loggedin user */

            if($request->status == 1) {

                $wishlist = Wishlist::where('user_id',$request->id)->delete();

            } else {  /** Clear particularv wishlist of the loggedin user */


                $wishlist = Wishlist::where('user_id',$request->id)->where('video_tape_id' , $request->video_tape_id)->delete();
   
                
                
            }

            $response_array = array('success' => true);
        }

        return response()->json($response_array, 200);
    
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
                'rating' => 'integer|in:'.RATINGS,
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
            $rating->rating = $request->has('rating') ? $request->rating : 0;
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
                        'mobile' => 'required|digits_between:6,13',
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
                    $user->dob = date("Y-m-d" , strtotime($request->dob));;
                }

                 if ($user->dob) {

                    $from = new \DateTime($user->dob);
                    $to   = new \DateTime('today');

                    $user->age_limit = $from->diff($to)->y;

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

                $user->device_token = $request->has('device_token') ? $request->device_token : "";
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

                // Check the default subscription and save the user type 

                user_type_check($user->id);

                // Send welcome email to the new user:
                if($new_user) {
                    $subject = tr('user_welcome_title');
                    $email_data = $user;
                    $page = "emails.welcome";
                    $email = $user->email;
                    // Helper::send_email($page,$subject,$email,$email_data);
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

                    // if($user->is_activated) {

                        if(Hash::check($request->password, $user->password)){

                            /* manual login success */
                            $operation = true;

                        } else {
                            $response_array = [ 'success' => false, 'error_messages' => Helper::get_error_message(105), 'error_code' => 105 ];
                        }
                    /*} else {
                        $response_array = ['success' => false , 'error' => Helper::get_error_message(144),'error_code' => 144];
                    }*/

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
                );

            }

        }
        return response()->json($response_array,200);

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

                $new_password = Helper::generate_password();
                $user->password =$new_password;

                $email_data = array();
                $subject = tr('user_forgot_email_title');
                $email = $user->email;
                $email_data['user']  = $user;
                $email_data['password'] = \Hash::make($new_password);
                $page = "emails.forgot-password";
                $email_send = Helper::send_email($page,$subject,$user->email,$email_data);

                $response_array['success'] = true;
                $response_array['message'] = Helper::get_message(106);
                $user->save();

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
                                ->where('video_tapes.status' , 1)
                                ->where('video_tapes.publish_status' , 1)
                                ->where('video_tapes.is_approved' , 1)
                               // ->orderby('video_tapes.publish_time' , 'desc')
                                ->shortVideoResponse();

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

                    if($request->id) {

                        if($user_details = User::find($request->id)) {

                            $value['pay_per_view_status'] = Helper::watchFullVideo($user_details->id, $user_details->user_type, $value);
                            
                            $value['user_type'] = $user_details->user_type;

                        }
                    }

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
                                ->shortVideoResponse();

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

                    if($request->id) {

                        if($user_details = User::find($request->id)) {

                            $value['pay_per_view_status'] = Helper::watchFullVideo($user_details->id, $user_details->user_type, $value);
                            
                            $value['user_type'] = $user_details->user_type;

                        }
                    }

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
                            ->where('video_tapes.status' , 1)
                            ->where('video_tapes.publish_status' , 1)
                            ->orderby('video_tapes.created_at' , 'desc')
                            ->shortVideoResponse();

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

                if($request->id) {

                    if($user_details = User::find($request->id)) {

                        $value['pay_per_view_status'] = Helper::watchFullVideo($user_details->id, $user_details->user_type, $value);
                        
                        $value['user_type'] = $user_details->user_type;

                    }
                }

                $value['watch_count'] = number_format_short($value->watch_count);

                $value['wishlist_status'] = $request->id ? (Helper::check_wishlist_status($request->id,$value->video_tape_id) ? DEFAULT_TRUE : DEFAULT_FALSE): 0;

                $value['share_url'] = route('user.single' , $value->video_tape_id);

                array_push($data, $value->toArray());
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
                            ->where('video_tapes.status' , 1)
                            ->where('video_tapes.publish_status' , 1)
                            ->orderby('video_tapes.watch_count' , 'desc')
                            ->shortVideoResponse();

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

                if($request->id) {

                    if($user_details = User::find($request->id)) {

                        $value['pay_per_view_status'] = Helper::watchFullVideo($user_details->id, $user_details->user_type, $value);
                        
                        $value['user_type'] = $user_details->user_type;

                    }
                }

                $value['watch_count'] = number_format_short($value->watch_count);
                
                $value['wishlist_status'] = $request->id ? (Helper::check_wishlist_status($request->id,$value->video_tape_id) ? DEFAULT_TRUE : DEFAULT_FALSE): 0;

                $value['share_url'] = route('user.single' , $value->video_tape_id);

                array_push($data, $value->toArray());
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

            $response_array = array('success' => true, 'channel_id'=>$channels->id, 
                        'channel_name'=>$channels->name, 'channel_image'=>$channels->picture,
                        'channel_cover'=>$channels->cover, 
                        'channel_description'=>$channels->description,
                        'is_subscribed'=>$channel_status,
                        'subscribers_count'=>$subscriberscnt,
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

                    // Comments Section

                    $comments = [];

                    if($comments = Helper::video_ratings($request->video_tape_id,0)) {

                        $comments = $comments->toArray();

                    }

                    $data['comments'] = $comments;

                    $data['suggestions'] = VideoRepo::suggestions($request);
                    
                    $response_array = ['success' => true , 'data' => $data ];

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
                                ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id') 
                                ->where('video_tapes.status' , 1)
                                ->where('video_tapes.publish_status' , 1)
                                ->where('title', 'like', "%".$request->key."%")
                                ->orderby('video_tapes.watch_count' , 'desc')
                                ->select('video_tapes.id as video_tape_id' , 'video_tapes.title');

            if ($request->id) {

                // Check any flagged videos are present

                $flag_videos = flag_videos($request->id);

                if($flag_videos) {

                    $base_query->whereNotIn('video_tapes.id',$flag_videos);

                }
            
            }

            $base_query->where('video_tapes.age_limit','<=', checkAge($request));

            $data = $base_query->skip($request->skip)->take(Setting::get('admin_take_count' ,12))->get()->toArray();


            $response_array = array('success' => true, 'data' => $data);
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

            $page_data['type'] = "Terms";

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

                        /** @todo Send mail notification to admin */ 

                        // if($admin_details = get_admin_mail()) {

                        //     $subject = tr('provider_redeeem_send_title');

                        //     $page = "emails.redeems.redeem-request-send";

                        //     $email = $admin_details->email;
                            
                        //     Helper::send_email($page,$subject,$email,$admin_details);
                        // }

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

            $data = Redeem::where('provider_id' , $request->id)->select('total' , 'paid' , 'remaining' , 'status')->get()->toArray();

            $response_array = ['success' => true , 'data' => $data];

        } else {
            $response_array = ['success' => false , 'error_messages' => Helper::error_message(147) , 'error_code' => 147];
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


    public function user_channel_list(Request $request) {

        $age = 0;

        $channel_id = [];

        $query = Channel::where('channels.is_approved', DEFAULT_TRUE)
                ->select('channels.*', 'video_tapes.id as video_tape_id', 'video_tapes.is_approved',
                    'video_tapes.status', 'video_tapes.channel_id')
                ->leftJoin('video_tapes', 'video_tapes.channel_id', '=', 'channels.id')
                // ->where('channels.status', DEFAULT_TRUE)
                ->groupBy('channels.id')
                ->where('channels.user_id',$request->id);

        /*if($request->id) {

            $user = User::find($request->id);

            $age = $user->age_limit;

            $age = $age ? ($age >= Setting::get('age_limit') ? 1 : 0) : 0;

            if ($request->id) {

                $channel_id = ChannelSubscription::where('user_id', $request->id)->pluck('channel_id')->toArray();

                $query->whereIn('channels.id', $channel_id);
            }


            $query->where('video_tapes.age_limit','<=', $age);

        }*/

        $channels = $query->paginate(16);

        $items = $channels->items();

        $lists = [];

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

        $pagination = (string) $channels->links();

        $response_array = ['success'=>true, 'channels'=>$lists, 'pagination'=>$pagination];

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
                $response_array = array('success' => false , 'error_messages' => 'Something went wrong');
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

               if ($user->one_time_subscription == DEFAULT_TRUE) {

                   $query->where('amount','>', 0);

               }

            } 

        }

        $model = $query->orderBy('amount' , 'asc')->get();

        $response_array = ['success'=>true, 'data'=>$model];

        return response()->json($response_array, 200);

    }

    public function pay_now(Request $request) {

        $validator = Validator::make(
            $request->all(),
            array(
                'subscription_id'=>'required|exists:subscriptions,id',
                'payment_id'=>'required',
            ));

        if ($validator->fails()) {
            // Error messages added in response for debugging
            $errors = implode(',',$validator->messages()->all());

            $response_array = ['success' => false,'error_messages' => $errors,'error_code' => 101];

        } else {

            $model = UserPayment::where('user_id' , $request->id)
                        ->orderBy('id', 'desc')->first();

            $subscription = Subscription::find($request->subscription_id);

            $user_payment = new UserPayment();

            if ($model) {
                $user_payment->expiry_date = date('Y-m-d H:i:s', strtotime("+{$subscription->plan} months", strtotime($model->expiry_date)));
            } else {
                $user_payment->expiry_date = date('Y-m-d H:i:s',strtotime("+{$subscription->plan} months"));
            }

            $user_payment->payment_id  = $request->payment_id;
            $user_payment->user_id = $request->id;
            $user_payment->amount = $subscription->amount;
            $user_payment->subscription_id = $request->subscription_id;
            $user_payment->save();

            if($user_payment) {

                $user_payment->user->user_type = DEFAULT_TRUE;

                $user_payment->user->save();
            }

            $response_array = ['success'=>true, 'message'=>tr('payment_success'), 
                    'data'=>[
                        'id'=>$request->id,
                        'token'=>$user_payment->user ? $user_payment->user->token : '',
                        ]];

        }

        return response()->json($response_array, 200);

    }

    public function subscribedPlans(Request $request){

        $validator = Validator::make(
            $request->all(),
            array(
                'skip'=>'required|numeric',
            ));

        if ($validator->fails()) {

            // Error messages added in response for debugging
            
            $errors = implode(',',$validator->messages()->all());

            $response_array = ['success' => false,'error_messages' => $errors,'error_code' => 101];

        } else {

            $model = UserPayment::where('user_id' , $request->id)
                        ->leftJoin('subscriptions', 'subscriptions.id', '=', 'subscription_id')
                        ->select('user_id as id',
                                'subscription_id',
                                'user_payments.id as user_subscription_id',
                                'subscriptions.title as title',
                                'subscriptions.description as description',
                                'subscriptions.plan',
                                'user_payments.amount as amount',
                                // 'user_payments.expiry_date as expiry_date',
                                \DB::raw('DATE_FORMAT(user_payments.expiry_date , "%e %b %Y") as expiry_date'),
                                'user_payments.created_at as created_at',
                                DB::raw("'$' as currency"))
                        ->orderBy('user_payments.updated_at', 'desc')
                        ->skip($request->skip)
                        ->take(Setting::get('admin_take_count' ,12))
                        ->get();
            $response_array = ['success'=>true, 'data'=>$model];

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
                'number' => 'required|numeric',
                'card_token'=>'required',
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

       $model = Channel::select('id as channel_id', 'name as channel_name')->where('is_approved', DEFAULT_TRUE)->where('status', DEFAULT_TRUE)->where('user_id', $request->id)->get();

        if($model) {

            $response_array = array('success' => true , 'data' => $model);

        } else {
            $response_array = array('success' => false,'error_messages' => Helper::get_error_message(135),'error_code' => 135);
        }

        $response = response()->json($response_array, 200);
        
        return $response;
    }


    public function stripe_payment(Request $request) {

        $validator = Validator::make($request->all(), 
            array(
                'subscription_id' => 'required|exists:subscriptions,id',
            )
            );

        if($validator->fails()) {

            $errors = implode(',', $validator->messages()->all());
            
            $response_array = ['success' => false, 'error_messages' => $errors, 'error_code' => 101];
        } else {

            $subscription = Subscription::find($request->subscription_id);


            if ($subscription) {

                $total = $subscription->amount;

                $user = User::find($request->id);

                if ($total > 0) {

                    $check_card_exists = User::where('users.id' , $request->id)
                                    ->leftJoin('cards' , 'users.id','=','cards.user_id')
                                    ->where('cards.id' , $user->card_id)
                                    ->where('cards.is_default' , DEFAULT_TRUE);

                    if($check_card_exists->count() != 0) {

                        $user_card = $check_card_exists->first();

                        $stripe_secret_key = Setting::get('stripe_secret_key');

                        // print_r("User Card Details ".print_r($user_card, true));

                        $customer_id = $user_card->customer_id;

                        if($stripe_secret_key) {
                            \Stripe\Stripe::setApiKey($stripe_secret_key);
                        } else {

                            $response_array = array('success' => false, 'error_messages' =>Helper::get_error_message(902), 'error_code' => 902);

                            return response()->json($response_array , 200);
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

                                $user_payment = UserPayment::where('user_id' , $request->id)->first();

                                if($user_payment) {

                                    $expiry_date = $user_payment->expiry_date;
                                    $user_payment->expiry_date = date('Y-m-d H:i:s', strtotime($expiry_date. "+".$subscription->plan." months"));

                                } else {
                                    $user_payment = new UserPayment;
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
                                
                                $data = ['id' => $user->id , 'token' => $user->token,'paymentid'=>$payment_id];

                                Log::info("Stripe Payment".print_r($data, true));

                                $response_array = ['success' => true, 'message'=>tr('payment_success') , 'data' => $data];

                                return response()->json($response_array, 200);

                            } else {

                                $response_array = array('success' => false, 'error_messages' => Helper::get_error_message(903) , 'error_code' => 903);

                                return response()->json($response_array, 200);

                            }

                        
                        } catch (\Stripe\StripeInvalidRequestError $e) {

                            Log::info(print_r($e,true));

                            $response_array = array('success' => false , 'error_messages' => Helper::get_error_message(903) ,'error_code' => 903);

                            return response()->json($response_array , 200);

                        
                        }

                    } else {
                        $response_array = array('success' => false, 'error_messages' => Helper::get_error_message(901) , 'error_code' => 901);
                        return response()->json($response_array , 200);
                    }

                } else {

                   

                    $user_payment = UserPayment::where('user_id' , $request->id)->first();

                    if($user_payment) {

                        $expiry_date = $user_payment->expiry_date;
                        $user_payment->expiry_date = date('Y-m-d H:i:s', strtotime($expiry_date. "+".$subscription->plan." months"));

                    } else {
                        $user_payment = new UserPayment;
                        $user_payment->expiry_date = date('Y-m-d H:i:s',strtotime("+".$subscription->plan." months"));
                    }


                    $user_payment->payment_id  = "free_plan";
                    $user_payment->user_id = $request->id;
                    $user_payment->subscription_id = $request->subscription_id;
                    $user_payment->status = 1;
                    $user_payment->amount = $subscription->amount;
                    $user_payment->save();


                    $user->user_type = 1;

                    $user->save();
                    
                    $data = ['id' => $user->id , 'token' => $user->token, 'paymentid'=>$user_payment->payment_id];

                    Log::info("Stripe Payment".print_r($data, true));

                    $response_array = ['success' => true, 'message'=>tr('payment_success') , 'data' => $data];

                    return response()->json($response_array, 200);

                    
                }

            } else {

                $response_array = array('success' => false ,'error_messages' => Helper::get_error_message(901));

            }         

            
        }

        return response()->json($response_array , 200);
    
    }

    public function subscribe_channel(Request $request) {

        $validator = Validator::make( $request->all(), array(
                'channel_id'     => 'required|exists:channels,id',
                ));


        if ($validator->fails()) {

            $error_messages = implode(',', $validator->messages()->all());

            $response_array = ['success'=>false, 'error_messages'=>$error_messages];

        } else {

            $model = ChannelSubscription::where('user_id', $request->id)->where('channel_id',$request->channel_id)->first();

            if (!$model) {

                $model = new ChannelSubscription;

                $model->user_id = $request->id;

                $model->channel_id = $request->channel_id;

                $model->status = DEFAULT_TRUE;

                $model->save();

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

                if($user_details = User::find($request->id)) {

                    $data['pay_per_view_status'] = Helper::watchFullVideo($user_details->id, $user_details->user_type, $video_tape_details);
                    
                    $data['user_type'] = $user_details->user_type;

                }

            }

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

        // Load Flag videos based on logged in user id
        $model = Flag::where('flags.user_id', $request->id)
            ->leftJoin('video_tapes' , 'flags.video_tape_id' , '=' , 'video_tapes.id')
            ->where('video_tapes.is_approved' , 1)
            ->where('video_tapes.status' , 1)
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

            return response()->json(['success'=>false , 'message'=>$error_messages]);
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
             ->leftJoin('video_tapes' ,'pay_per_views.video_id' , '=' , 'video_tapes.id')
            ->where('video_tapes.is_approved' , 1)
            ->where('video_tapes.status' , 1)
            ->where('video_tapes.age_limit','<=', checkAge($request))
            ->orderby('pay_per_views.created_at' , 'desc')
            ->paginate(16);

        $video = array('data' => $model->items(), 'pagination' => (string) $model->links());


      
        $items = [];

        foreach ($video['data'] as $key => $value) {
            
            $items[] = displayVideoDetails($value->videoTapeResponse, $request->id);

        }

        return response()->json(['items'=>$items, 'pagination'=>isset($model['pagination']) ? $model['pagination'] : 0]);
    }

    public function search_list($request,$key,$web = NULL,$skip = 0) {

        $base_query = VideoTape::where('video_tapes.is_approved' ,'=', 1)
                    ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                    ->where('title','like', '%'.$key.'%')
                    ->where('video_tapes.status' , 1)
                    ->videoResponse()
                    ->where('video_tapes.age_limit','<=', checkAge($request))
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

            $validator = Validator::make($request->all(), 
                array(
                    'video_tape_id' => 'required|exists:video_tapes,id',
                ),  array(
                    'exists' => 'The :attribute doesn\'t exists',
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

                                $total = $video->amount;

                                if ($total > 0) {

                                    
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
                                            $user_payment->video_id = $request->admin_video_id;
                                            $user_payment->status = DEFAULT_FALSE;
                                            $user_payment->amount = $amount;

                                            $user_payment->save();

                                            if($user_payment->amount > 0) {

                                                    $total = $payment->amount;

                                                    // Commission Spilit 

                                                    $admin_commission = Setting::get('admin_ppv_commission')/100;

                                                    $admin_amount = $total * $admin_commission;

                                                    $moderator_amount = $total - $admin_amount;

                                                    $video->admin_ppv_amount = $admin_amount;

                                                    $video->user_ppv_amount = $moderator_amount;

                                                    $video->save();

                                                    // Commission Spilit Completed

                                                    if($moderator = User::find($video->user_id)) {

                                                        $moderator->total_admin_amount = $moderator->total_admin_amount + $admin_amount;

                                                        $moderator->total_user_amount = $moderator->total_user_amount + $moderator_amount;

                                                        $moderator->remaining_amount = $moderator->remaining_amount + $moderator_amount;

                                                        $moderator->total_amount = $moderator->total_amount + $total;

                                                        $moderator->save();

                                                       // $video_amount = $moderator_amount;

                                                    }

                                                    add_to_redeem($video->user_id , $moderator_amount);
                                                        
                                            }

                                            // Commission Spilit 
                                           /* if($video->watch_count >= Setting::get('video_viewer_count') && is_numeric($video->uploaded_by)) {

                                                $video_amount = Setting::get('amount_per_video');

                                                $video->redeem_amount += $video_amount;

                                                if($video->amount > 0) { 

                                                    $total = $video_amount;

                                                    // Commission Spilit 

                                                    $admin_commission = Setting::get('admin_commission')/100;

                                                    $admin_amount = $total * $admin_commission;

                                                    $moderator_amount = $total - $admin_amount;

                                                    $video->admin_amount = $admin_amount;

                                                    $video->user_amount = $moderator_amount;

                                                    $video->save();

                                                    // Commission Spilit Completed

                                                    if($moderator = Moderator::find($video->uploaded_by)) {

                                                        $moderator->total_admin_amount = $moderator->total_admin_amount + $admin_amount;

                                                        $moderator->total_user_amount = $moderator->total_user_amount + $moderator_amount;

                                                        $moderator->remaining_amount = $moderator->remaining_amount + $moderator_amount;

                                                        $moderator->total = $moderator->total + $total;

                                                        $moderator->save();

                                                        $video_amount = $moderator_amount;

                                                    }
                                                    
                                                }

                                                add_to_redeem($video->uploaded_by , $video_amount);

                                                \Log::info("ADD History - add_to_redeem");

                                            } */

                                            $video->save();

                                            $data = ['id'=> $request->id, 'token'=> $userModel->token , 'payment_id' => $payment_id];

                                            $response_array = array('success' => true, 'message'=>tr('payment_success'),'data'=> $data);

                                        } else {

                                            $response_array = array('success' => false, 'error_messages' => Helper::get_error_message(902) , 'error_code' => 902);

                                            throw new Exception(tr('no_video_found'));

                                        }
                                    
                                    } catch (\Stripe\StripeInvalidRequestError $e) {

                                        Log::info(print_r($e,true));

                                        $response_array = array('success' => false , 'error_messages' => $e->getMessage() ,'error_code' => 903);

                                       return response()->json($response_array , 200);
                                    
                                    }

                                }

                            
                            } else {

                                $response_array = array('success' => false , 'error_messages' => tr('no_video_found'));

                                throw new Exception(tr('no_video_found'));
                                
                            }

                        } else {

                            $response_array = array('success' => false , 'error_messages' => tr('no_default_card_available'));

                            throw new Exception(tr('no_default_card_available'));

                        }

                    } else {

                        $response_array = array('success' => false , 'error_messages' => tr('no_default_card_available'));

                        throw new Exception(tr('no_default_card_available'));

                    }

                } else {

                    throw new Exception(tr('no_user_detail_found'));
                    

                }

            }

            DB::commit();

            return response()->json($response_array,200);

        } catch (Exception $e) {

            DB::rollback();

            $e = $e->getMessage();

            $response_array = ['success'=>false, 'error_messages'=>$e];

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
                            ->where('video_tapes.is_approved' , 1)
                            ->where('video_tapes.status' , 1)
                            ->where('wishlists.status' , 1)
                            ->select(
                                    'wishlists.id as wishlist_id',
                                    'video_tapes.id as video_tape_id' ,
                                    'video_tapes.title',
                                    'video_tapes.description' ,
                                    'video_tapes.ppv_amount',
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
                            ->leftJoin('channels' ,'video_tapes.channel_id' , '=' , 'channels.id')
                            ->where('video_tapes.is_approved' , 1)
                            ->where('video_tapes.status' , 1)
                            ->select('user_histories.id as history_id',
                                    'video_tapes.id as video_tape_id' ,
                                    'video_tapes.title',
                                    'video_tapes.description' , 
                                    'video_tapes.duration',
                                    'default_image',
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
                            ->orderby('user_histories.created_at' , 'desc');
        
        if ($request->id) {

            // Check any flagged videos are present

            $flag_videos = flag_videos($request->id);

            if($flag_videos) {

                $base_query->whereNotIn('video_tapes.id',$flag_videos);

            }
        
        }

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


    public function pay_per_videos(Request $request) {

        // Load all the paper view videos based on logged in user id
        $model = PayPerView::where('pay_per_views.user_id', $request->id)
             ->leftJoin('video_tapes' ,'pay_per_views.video_id' , '=' , 'video_tapes.id')
            ->where('video_tapes.is_approved' , 1)
            ->where('video_tapes.status' , 1)
            ->where('video_tapes.age_limit','<=', checkAge($request))
            ->orderby('pay_per_views.created_at' , 'desc')
            ->paginate(16);

        $video = array('data' => $model->items(), 'pagination' => (string) $model->links());

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
                            ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                            ->orderby('video_tapes.created_at' , 'desc')
                            ->where('video_tapes.age_limit','<=', checkAge($request))
                            ->videoResponse();

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
                        ->videoResponse()
                        ->where('video_tapes.age_limit','<=', checkAge($request))
                        ->orderby('watch_count' , 'desc');

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
                            ->where('video_tapes.status' , 1)
                            ->where('video_tapes.publish_status' , 1)
                            ->orderby('video_tapes.created_at' , 'desc')
                            ->videoResponse()
                            ->where('video_tapes.age_limit','<=', checkAge($request))
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

            if ($request->id) {

                $channel_id = ChannelSubscription::where('user_id', $request->id)->pluck('channel_id')->toArray();

                $query->whereIn('channels.id', $channel_id);
            }


            $query->where('video_tapes.age_limit','<=', $age);

        }

        $channels = $query->paginate(16);

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

            $pagination = (string) $channels->links();

        }

        $response_array = ['success'=>true, 'channels'=>$lists, 'pagination'=>$pagination];

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
    public function channel_videos($channel_id, $skip) {

        $videos_query = VideoTape::where('video_tapes.is_approved' , 1)
                    ->where('video_tapes.status' , 1)
                    ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                    ->where('video_tapes.channel_id' , $channel_id)
                    ->videoResponse()
                    ->orderby('video_tapes.created_at' , 'desc');

        $u_id = '';

        if (Auth::check()) {

            $u_id = Auth::user()->id;

            // Check any flagged videos are present
            $flagVideos = getFlagVideos($u_id);

            if($flagVideos) {

                $videos_query->whereNotIn('video_tapes.id', $flagVideos);

            }

        }

        if ($skip >= 0) {

            $videos = $videos_query->skip($skip)->take(Setting::get('admin_take_count', 12));

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
    public function channel_trending($id, $count) {

        $base_query = VideoTape::where('watch_count' , '>' , 0)
                        ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                        ->videoResponse()
                        ->where('channel_id', $id)
                        ->orderby('watch_count' , 'desc');

        $u_id = "";

        if (Auth::check()) {

            // Check any flagged videos are present

            $u_id = Auth::user()->id;

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

        $items = [];

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

        $base_query = VideoTape::where('amount' , '>' , 0)
                        ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                        ->videoResponse()
                        ->where('channel_id', $id)
                        ->orderby('amount' , 'desc');

        if($skip >= 0) {

            $videos = $base_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();

        } else {

            $videos = $base_query->paginate(16);
            
        }

        $u_id = Auth::check() ? Auth::user()->id : '';    

        $items = [];

        foreach ($videos as $key => $value) {

            $items[] = displayVideoDetails($value, $u_id);

        }

        return response()->json($items);

    
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
                    ->videoResponse()
                    ->first();

        if ($video) {

            if (Setting::get('is_payper_view')) {

                if ($request->id != $video->channel_created_by) {

                    $user = User::find($request->id);

                    if ($video->ppv_amount > 0) {

                        $ppv_status = $user ? watchFullVideo($user->id, $user->user_type, $video) : false;

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

            if ($video->publish_status == 1) {

                $hls_video = (Setting::get('HLS_STREAMING_URL')) ? Setting::get('HLS_STREAMING_URL').get_video_end($video->video) : $video->video;

                if (\Setting::get('streaming_url')) {

                    if ($video->is_approved == 1) {


                        if ($video->video_resolutions) {


                            $videoStreamUrl = Helper::web_url().'/uploads/smil/'.get_video_end_smil($video->video).'.smil';
                        }

                    }

                    \Log::info("video Stream url".$videoStreamUrl);

                    \Log::info("Empty Stream url".empty($videoStreamUrl));

                    \Log::info("File Exists Stream url".!file_exists($videoStreamUrl));


                    if(empty($videoStreamUrl) || !file_exists($videoStreamUrl)) {

                        $videoPath = $video->video_path ? $video->video.','.$video->video_path : $video->video;

                        // dd($videoPath);
                        $video_pixels = $video->video_resolutions ? 'original,'.$video->video_resolutions : 'original';


                    }


                } else {


                    $videoPath = $video->video_path ? $video->video.','.$video->video_path : $video->video;

                    // dd($videoPath);
                    $video_pixels = $video->video_resolutions ? 'original,'.$video->video_resolutions : 'original';
                    
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
            
            $response_array = ['video'=>$video, 'comments'=>$comments, 
                'channels' => $channels, 'suggestions'=>$suggestions,
                'wishlist_status'=> $wishlist_status, 'history_status' => $history_status, 'main_video'=>$main_video,
                'report_video'=>$report_video, 'flaggedVideo'=>$flaggedVideo , 'videoPath'=>$videoPath,
                'video_pixels'=>$video_pixels, 'videoStreamUrl'=>$videoStreamUrl, 'hls_video'=>$hls_video,
                'like_count'=>$like_count,'dislike_count'=>$dislike_count,
                'ads'=>$ads, 'subscribe_status'=>$subscribe_status,
                'subscriberscnt'=>$subscriberscnt,'comment_rating_status'=>$comment_rating_status,
                'embed_link' => $embed_link];

            return response()->json(['success'=>true, 'response_array'=>$response_array], 200);

        } else {

            return response()->json(['success'=>false, 'error_messages'=>tr('video_not_found')]);
        }

    }
}
