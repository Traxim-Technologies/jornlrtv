<?php


namespace App\Repositories;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Validator;
use App\User;
use Hash;
use Log;
use DB;
use App\Channel;
use App\VideoTape;
use App\Jobs\CompressVideo;
use App\VideoTapeImage;
use App\UserPayment;
use Auth;
use Exception;
use Setting;


class CommonRepository {


	/**
	 * Usage : Register api - validation for the basic register fields 
	 *
	 */

	public static function basic_validation($data = [], &$errors = []) {

		$validator = Validator::make( $data,array(
                'device_type' => 'required|in:'.DEVICE_ANDROID.','.DEVICE_IOS.','.DEVICE_WEB,
                'device_token' => 'required',
                'login_by' => 'required|in:manual,facebook,google',
            )
        );
        
	    if($validator->fails()) {
	        $errors = implode(',', $validator->messages()->all());
	        return false;
	    }

	    return true;

	}

		/**
	 * Usage : Register api - validation for the social register or login 
	 *
	 */

	public static function social_validation($data = [] , &$errors = []) {

		$validator = Validator::make( $data,array(
                'social_unique_id' => 'required',
                'name' => 'required|max:255',
                'email' => 'required|email|max:255',
                'mobile' => 'digits_between:6,13',
                'picture' => '',
                'gender' => 'in:male,female,others',
            )
        );
        
	    if($validator->fails()) {
	        $errors = implode(',', $validator->messages()->all());
	        return false;
	    }

	    return true;

	}

	/**
	 * Usage : Register api - validation for the manual register fields 
	 *
	 */

	public static function manual_validation($data = [] , &$errors = []) {

		$validator = Validator::make( $data,array(
                'name' => 'required|max:255',
                'email' => 'required|email',
                'password' => 'required|min:6',
                'mobile' => 'digits_between:6,13',
                'picture' => 'mimes:jpeg,jpg,bmp,png',
            )
        );
        
	    if($validator->fails()) {
	        $errors = implode(',', $validator->messages()->all());
	        return false;
	    }

	    return true;

	}

	/**
	 * Usage : Login api - validation for the manual login fields 
	 *
	 */

	public static function email_validation($data = [] , &$errors = [] , $table = "users") {

		$validator = Validator::make( $data,[
                'email' => 'required|email|exists:'.$table.',email',
            ],
            [
            	'exists' => tr('email_id_not_found')
            ]
        );
        
	    if($validator->fails()) {
	        $errors = implode(',', $validator->messages()->all());
	        return false;
	    }

	    return true;

	}

	/**
	 * Usage : Login api - validation for the manual login fields 
	 *
	 */

	public static function login_validation($data = [] , &$errors = [] , $table = "users") {

		$validator = Validator::make( $data,[
                'email' => 'required|email|exists:'.$table.',email',
                'password' => 'required',
            ]
        );
        
	    if($validator->fails()) {
	        $errors = implode(',', $validator->messages()->all());
	        return false;
	    }

	    return true;

	}

	public static function change_password_validation($data = [] , &$errors = [] , $table = "users") {

		$validator = Validator::make( $data,[
                'password' => 'required|confirmed',
                'old_password' => 'required',
            ]
        );
        
	    if($validator->fails()) {
	        $errors = implode(',', $validator->messages()->all());
	        return false;
	    }

	    return true;

	}



	public static function channel_save($request) {


        $validator = [];

        if($request->id != '') {

            if ($request->has('picture')) {

                $validator = Validator::make( $request->all(), array(
                        'picture'     => 'required|mimes:jpeg,jpg,bmp,png',
                        ));

            }

            if ($request->has('cover')) {

                $validator = Validator::make( $request->all(), array(
                        'cover'  => 'required|mimes:jpeg,jpg,bmp,png',
                        )
                    );
            }

        }

        if ($validator) {

            if ($validator->fails()) {

                $error_messages = implode(',', $validator->messages()->all());

                $response_array = ['success'=> false, 'error'=>$error_messages];
            }

        } else {

    		if($request->id != '') {
    			
                $validator = Validator::make( $request->all(), array(
                            'name' => 'required|max:255',
                            'description'=>'required',
                        ));
            } else {
                $validator = Validator::make( $request->all(), array(
                        'name' => 'required|max:255',
                        'description'=>'required',
                        'picture' => 'required|mimes:jpeg,jpg,bmp,png',
                        'cover' => 'required|mimes:jpeg,jpg,bmp,png',
                    )
                );
            
            }
           
            if($validator->fails()) {

                $error_messages = implode(',', $validator->messages()->all());

                $response_array = ['success'=> false, 'error'=>$error_messages];

                // return back()->with('flash_errors', $error_messages);

            } else {

                if($request->id != '') {

                    $channel = Channel::find($request->id);

                    $message = tr('channel_update_success');

                    if($request->hasFile('picture')) {
                        Helper::delete_picture($channel->picture, "/uploads/channel/picture/");
                    }

                    if($request->hasFile('cover')) {
                        Helper::delete_picture($channel->cover, "/uploads/channel/cover/");
                    }

                } else {
                    $message = tr('channel_create_success');
                    //Add New User
                    $channel = new Channel;

                    $channel->is_approved = DEFAULT_TRUE;

                }

                $channel->name = $request->has('name') ? $request->name : '';

                $channel->description = $request->has('description') ? $request->description : '';

                $channel->user_id = $request->has('user_id') ? $request->user_id : '';

                $channel->status = DEFAULT_TRUE;

                $channel->unique_id =  $channel->name;
                
                if($request->hasFile('picture') && $request->file('picture')->isValid()) {
                    if($channel->id)  {
                        Helper::delete_picture($channel->picture, "/uploads/channels/picture/");
                    }
                    $channel->picture = Helper::normal_upload_picture($request->file('picture'), "/uploads/channels/picture/");
                }

                if($request->hasFile('cover') && $request->file('cover')->isValid()) {
                    if($channel->id)  {
                        Helper::delete_picture($channel->cover, "/uploads/channels/cover/");
                    }
                    $channel->cover = Helper::normal_upload_picture($request->file('cover'), "/uploads/channels/cover/");
                }

                $channel->save();

                if($channel) {
                    // return back()->with('flash_success', $message);
                    $response_array = ['success'=>true, 'message'=>$message, 'data'=>$channel];
                } else {
                    // return back()->with('flash_error', tr('admin_not_error'));
                    $response_array = ['success'=>false, 'error'=>tr('something_error')];
                }

            }
        }

        return response()->json($response_array, 200);
    
	}



    public static function video_save($request) {

        try {

            DB::beginTransaction();

            $validator = Validator::make( $request->all(), array(
                        'title'         => 'required|max:255',
                        'description'   => 'required',
                        'channel_id'   => 'required|integer|exists:channels,id',
                        'video'     => 'required|mimes:mkv,mp4,qt',
                        'video_publish_type'=>'required'
                        ));

            if($validator->fails()) {

                Log::info("Fails Validator 1");

                $error_messages = implode(',', $validator->messages()->all());

                // $response_array = ['success'=>false, 'message'=>$error_messages];

                throw new Exception($error_messages);

            } else {

                Log::info("Success validation and navigated to create new object");

                $model = $request->has('id') ? VideoTape::find($request->id) : new VideoTape;

                $model->title = $request->has('title') ? $request->title : $model->title;

                $model->description = $request->has('description') ? $request->description : $model->description;

                $channel_id = $request->has('channel_id') ? $request->channel_id : $model->channel_id;

                $model->channel_id = $channel_id;

                if($channel_id) {

                    $channel = Channel::find($channel_id);
                    
                    if($channel) {
                        $model->user_id = $channel->user_id;
                    }
                }

                $model->reviews = $request->has('reviews') ? $request->reviews : $model->reviews;

                $model->ratings = $request->has('ratings') ? $request->ratings : 0;

                $model->video_publish_type = $request->has('video_publish_type') ? $request->video_publish_type : $model->video_publish_type;

                if($model->id) {

                    Helper::delete_picture($model->video, "/uploads/videos/");

                }

                if ($request->video->getClientSize()) {

                    $bytes = convertMegaBytes($request->video->getClientSize());

                    if ($bytes > Setting::get('video_compress_size')) {

                    } else {

                        $model->compress_status = DEFAULT_TRUE;
                    }

                }
                        
                $main_video_duration = Helper::video_upload($request->video);

                $model->video = $main_video_duration['db_url'];

                $model->is_banner = $request->has('is_banner') ? $request->is_banner : DEFAULT_FALSE;

                $getDuration = readFileName($main_video_duration['baseUrl']);

                if ($getDuration) {

                    // dd($getDuration);

                    $model->duration = $getDuration['hours'].':'.$getDuration['mins'].':'.$getDuration['secs'];

                    $seconds = $getDuration['hours'] * 3600 + $getDuration['mins'] * 60 + $getDuration['secs'];

                }

                $model->unique_id = $model->title;

                $img = time();

                $FFmpeg = new \FFmpeg;

                $frames = ($model->is_banner == DEFAULT_TRUE) ? 4 : 3;

               // dd(1/ ($seconds/$frames));

                $FFmpeg
                    ->input($main_video_duration['baseUrl'])
                    ->constantVideoFrames($frames)
                    ->customVideoFrames(1 / ($seconds/$frames))
                    ->output(public_path()."/uploads/images/{$model->channel_id}_{$img}_%03d.png")
                    ->ready();


                $model->publish_time = $request->has('publish_time') 
                            ? date('Y-m-d H:i:s', strtotime($request->publish_time)) : '';
                

                $model->status = DEFAULT_FALSE;

                $model->publish_status = DEFAULT_TRUE;

                $model->is_approved = DEFAULT_TRUE;

                if($model->publish_time) {
                    if(strtotime($model->publish_time) < strtotime(date('Y-m-d H:i:s'))) {
                        $model->publish_status = DEFAULT_TRUE;
                    } else {
                        $model->publish_status = DEFAULT_FALSE;
                    }
                }

                $model->save();

                Log::info("saved Video Object : ".'Success');

                if($model->save()) {

                    if($main_video_duration) {

                        $inputFile = $main_video_duration['baseUrl'];
                        $local_url = $main_video_duration['local_url'];
                        $file_name = $main_video_duration['file_name'];

                        if (file_exists($inputFile)) {
                            Log::info("Main queue Videos : ".'Success');
                            dispatch(new CompressVideo($inputFile, $local_url, $model->id, $file_name));
                            Log::info("Main queue completed : ".'Success');
                        }
                    }


                    if (env('QUEUE_DRIVER') != 'redis') {

                        \Log::info("Queue Driver : ".env('QUEUE_DRIVER'));

                        $model->status = DEFAULT_TRUE;

                        $model->compress_status = DEFAULT_TRUE;

                        $model->save();
                    }

                   $video_path = [];
                    
                   Log::info('Video Id Ajax : '.$model->id);

                  

                    $get_image_model = ($request->id) ? self::deleteVideoTapeImage($model->id) : []; 


                    $img_status = DEFAULT_FALSE;

                    for ($i = 0 ; $i < $frames; $i++) {
         
                        // Create a thunail images

                        $pos = $i+1;

                        $video_path[] = Helper::web_url().'/uploads/images/'.$model->channel_id.'_'.$img.'_00'.$pos.'.png';


                        if($model->is_banner && $i == 0) {

                            if($model->banner_image) {

                                Helper::delete_picture($model->banner_image, "/uploads/images/");

                            }

                            $model->banner_image = Helper::web_url().'/uploads/images/'.$model->channel_id.'_'.$img.'_00'.$pos.'.png';

                            // print_r($model->banner_image);

                            $img_status = DEFAULT_TRUE;
                        
                        }

                        if ($i == $img_status) {

                            if($model->default_image) {

                                Helper::delete_picture($model->default_image, "/uploads/images/");

                            }

                            $model->default_image = Helper::web_url().'/uploads/images/'.$model->channel_id.'_'.$img.'_00'.$pos.'.png';

                        }

                        if($i > $img_status) {

                            $video_img = new VideoTapeImage();

                            $video_img->video_tape_id = $model->id;

                            $video_img->image = Helper::web_url().'/uploads/images/'.$model->channel_id.'_'.$img.'_00'.$pos.'.png';

                            $video_img->is_default = 0;

                            $video_img->position = $pos;

                            $video_img->save();
                        }

                    }

                    $model->save();

                    $response_array =  ['success'=>true , 'data'=> $model, 'video_path'=>$video_path];
                   
                } else {
                    
                    throw new Exception(tr('admin_not_error'));
                    
                    // $response_array = ['success'=>false , 'message'=>tr('admin_not_error')];
                   
                }
            }

            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();

            $response_array = ['success'=>false, 'message'=>$e->getMessage()];

        }

        return response()->json($response_array);

    }


    public static function deleteVideoTapeImage($id) {

        $model = VideoTapeImage::where('video_tape_id', $id)->delete();

        return $model;

    }


    public static function get_video_tape_images($video_id) {

        $model = VideoTape::find($video_id);

        $videoTapeImage = $model->getVideoTapeImages;

        if($model->is_banner) {

            $video_path = [$model->banner_image, $model->default_image];

        } else {

            $video_path = [$model->default_image];

        }


        foreach ($videoTapeImage as $key => $value) {
            
            array_push($video_path, $value->image);

        }

        $response = ['data'=>$model, 'video_path' => $video_path];

        return response()->json($response, 200);

    }


    public static function set_default_image($request) {

       // dd($request->all());

        $video_tape = ($request->video_tape_id) ? VideoTape::find($request->video_tape_id) : '';

        if($video_tape) {

            $img_status = DEFAULT_FALSE;

            if($video_tape->is_banner) {

                $img_status = DEFAULT_TRUE;

            }

            if ($request->idx > $img_status) {

                $model = VideoTapeImage::find($request->id);

                $data = VideoTape::find($model->video_tape_id);

                $default_image = $data->default_image;

                $data->default_image = $request->img;

                if ($data->save()) {

                    $model->image = $default_image;

                    if ($model->save()) {

                        return response()->json($model);

                    }

                }

            } else {

                $model = VideoTape::find($request->id);

                $default_image = $model->image;

                $data = VideoTapeImage::where('image', $request->img)->first();

                if ($data) {

                    $data->image = $default_image;

                    if($data->save()) {

                        $model->default_image = $request->img;

                        if ($model->save()) {

                            return response()->json($model);

                        }

                    }

                }
            }

        }

        return response()->json(false);

    }

    public static function upload_video_image($request) {


        $video = VideoTape::find($request->default_image_id);

        $video->title = $request->has('title') ? $request->title : $video->title;

        $video->description = $request->has('description') ? $request->description : $video->description;

        $video->channel_id = $request->has('channel_id') ? $request->channel_id : $video->channel_id;

        $video->reviews = $request->has('reviews') ? $request->reviews : $video->reviews;

        $video->ratings = $request->has('ratings') ? $request->ratings : $video->ratings;

        $video->video_publish_type = $request->has('video_publish_type') ? $request->video_publish_type : $video->video_publish_type;

        $video->publish_time = $request->has('publish_time') 
                        ? date('Y-m-d H:i:s', strtotime($request->publish_time)) : $video->publish_time;

        $video->save();

        if ($request->banner_image)  {

            $model = VideoTape::find($request->default_image_id);

            if($model->banner_image)  {
                Helper::delete_picture($model->banner_image, "/uploads/images/");
            }
            $model->banner_image = Helper::normal_upload_picture($request->file('banner_image'), "/uploads/images/");

            $model->save();

        }
        if ($request->default_image)  {

            $model = VideoTape::find($request->default_image_id);

            if($model->default_image)  {
                Helper::delete_picture($model->default_image, "/uploads/images/");
            }
            $model->default_image = Helper::normal_upload_picture($request->file('default_image'), "/uploads/images/");

            $model->save();

        }

        if ($request->other_image_1)  {

            $model = VideoTapeImage::find($request->other_image_id_1);

            if($model->image)  {
                Helper::delete_picture($model->image, "/uploads/images/");
            }
            $model->image = Helper::normal_upload_picture($request->file('other_image_1'), "/uploads/images/");

            $model->save();
            
        }


        if ($request->other_image_2)  {

            $model = VideoTapeImage::find($request->other_image_id_2);

            if($model->image)  {
                Helper::delete_picture($model->image, "/uploads/images/");
            }
            $model->image = Helper::normal_upload_picture($request->file('other_image_2'), "/uploads/images/");

            $model->save();
            
        }

        return response()->json($request->default_image_id);

    }



    public static function save_subscription($s_id, $u_id) {

        $load = UserPayment::where('user_id', $u_id)->orderBy('created_at', 'desc')->first();

        $payment = new UserPayment();

        $payment->subscription_id = $s_id;

        $payment->user_id = $u_id;

        $payment->amount = ($payment->getSubscription) ? $payment->getSubscription->amount  : 0;

        $payment->payment_id = ($payment->amount > 0) ? uniqid(str_replace(' ', '-', 'PAY')) : 'Free Plan'; 

        if ($load) {
            $payment->expiry_date = date('Y-m-d H:i:s', strtotime("+{$payment->getSubscription->plan} months", strtotime($load->expiry_date)));
        } else {
            $payment->expiry_date = date('Y-m-d H:i:s',strtotime("+{$payment->getSubscription->plan} months"));
        }

        $payment->status = DEFAULT_TRUE;

        if ($payment->save())  {

            $payment->user->user_type = DEFAULT_TRUE;

            if($payment->amount == 0) {

                $payment->user->zero_subscription_status = DEFAULT_TRUE;
            }

            if ($payment->user->save()) {

                return response()->json(['success'=> true, 'message'=>tr('subscription_applied_success')]);

            }

        }

        return response()->json(['success'=> false, 'message'=>tr('something_error')]);
    }

}