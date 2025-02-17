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
use App\Jobs\BellNotificationJob;
use App\VideoTapeImage;
use App\UserPayment;
use Auth;
use Exception;
use Setting;

use App\Jobs\SubscriptionMail;

use App\Jobs\sendPushNotification;

use App\Category;

use App\VideoTapeTag;

use App\Subscription;

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


    /**
     * Function Name : channel_save
     *
     * @uses To save/update channel based on details
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param Integer (request) $channel_id
     * 
     * @return success/failure message
     *
     */
	public static function channel_save($request) {

        try {  

            $request->request->add([
                'user_id' => $request->device_type == DEVICE_WEB ? $request->user_id : $request->id
            ]);


            DB::beginTransaction();

            $validator = Validator::make( $request->all(), [
                    'name' => 'required|min:6|max:255',
                    'description' => 'required|max:1000',
                    'picture' => $request->channel_id ? 'mimes:jpeg,jpg,bmp,png' : 'required|mimes:jpeg,jpg,bmp,png',
                    'cover' => $request->channel_id ? 'mimes:jpeg,jpg,bmp,png' : 'required|mimes:jpeg,jpg,bmp,png',
                    'channel_id' => $request->channel_id ? 'required|exists:channels,id' : '',
                    'is_paid_channel' => 'required',
                    'subscription_amount' => $request->is_paid_channel == YES ? 'required' :'',
                ]
            );

            if ($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);                    

            }

                if ($request->channel_id != '') {

                    $channel_details = Channel::find($request->channel_id);

                    $message = tr('admin_channel_update_success');

                } else {

                    $message = tr('admin_channel_create_success');
                    //Add New User
                    $channel_details = new Channel;

                    $channel_details->is_approved = DEFAULT_TRUE;

                    $channel_details->status = DEFAULT_TRUE;
                }

                $channel_details->name = $request->has('name') ? $request->name : '';

                $channel_details->description = $request->has('description') ? $request->description : '';
                $channel_details->is_paid_channel = $request->is_paid_channel == YES ? YES : NO;

                $channel_details->subscription_amount = $request->is_paid_channel== YES ? $request->subscription_amount : 0;
                
                $channel_details->user_id = $request->has('id') ? $request->id : $request->user_id;

                if($channel_details->youtube_channel_id == "" && $request->youtube_channel_id) {

                    $channel_details->youtube_channel_created_at = date('Y-m-d H:i:s');
                }

                $channel_details->youtube_channel_id = $request->youtube_channel_id ?: "";

                if($request->youtube_channel_id) {

                    $channel_details->youtube_channel_updated_at = date('Y-m-d H:i:s');

                }

                $channel_details->unique_id = $channel_details->name;
                
                if($request->hasFile('picture') && $request->file('picture')->isValid()) {
                    
                    if($channel_details->id) {
                        Helper::delete_picture($channel_details->picture, "/uploads/channels/picture/");
                    }

                    $channel_details->picture = Helper::normal_upload_picture($request->file('picture'), "/uploads/channels/picture/");
                }

                if($request->hasFile('cover') && $request->file('cover')->isValid()) {
                    
                    if($channel_details->id)  {
                        Helper::delete_picture($channel_details->cover, "/uploads/channels/cover/");
                    }

                    $channel_details->cover = Helper::normal_upload_picture($request->file('cover'), "/uploads/channels/cover/");
                }


                if ($channel_details->save()) {

                    // For response purpose

                    $channel_details->channel_id = $channel_details->id;

                    DB::commit();

                } else {

                    throw new Exception(tr('admin_channel_save_error'), 101);
                }

                if ($channel_details) {

                    $response_array = ['success' => true, 'message' => $message, 'data' => $channel_details];
                } else {

                    $response_array = ['success' => false, 'error_messages' => tr('something_error')]; 
                }

        } catch (Exception $e) {
            
            DB::rollBack();

            $message = $e->getMessage();

            $response_array = ['success' => false, 'error_messages' => $message, 'error_code' => $e->getCode()];
        }    

        return response()->json($response_array, 200);    
	}


    public static function video_save(Request $request) {

        try {

            DB::beginTransaction();

            $validator = Validator::make( $request->all(), array(
                        'title'         => 'required|max:255',
                        'category_id'=>'required|exists:categories,id,status,'.CATEGORY_APPROVE_STATUS,
                        'tag_id'=>"",
                        'description'   => 'required',
                        'channel_id'   => 'required|integer|exists:channels,id',
                       // 'video'     => 'required|mimes:mkv,mp4,qt',
                        //'subtitle'=>'mimes:text/str',
                        'video_publish_type'=>'required',
                        'video_type'=>'required|in:'.VIDEO_TYPE_UPLOAD.','.VIDEO_TYPE_YOUTUBE.','.VIDEO_TYPE_OTHERS
                        // 'age_limit'=>'required|numeric'
                        ));

            if( $validator->fails() ) {

                Log::info("Fails Validator 1");

                $error_messages = implode(',', $validator->messages()->all());

                throw new Exception($error_messages);

            } else {

                Log::info("Success validation and navigated to create new object");

                $model = $request->has('id') ? VideoTape::find($request->id) : new VideoTape;

                $required = 0;

                if ($model->video_type != $request->video_type) {

                    $required = 1;
                }

                if (in_array($request->video_type , [ VIDEO_TYPE_YOUTUBE , VIDEO_TYPE_OTHERS] )) {

                    $OtherVideovalidator = Validator::make( $request->all(), array(
                        'other_video'=> $required ? 'required|url' : 'url',
                        'other_image' => $required ? 'required| mimes:jpeg,jpg,bmp,png' : 'mimes:jpeg,jpg,bmp,png',
                    ));
                   
                    if($OtherVideovalidator->fails()) {

                        $error_messages = implode(',', $OtherVideovalidator->messages()->all());

                        Log::info("Video SAVE - Validation Fails".$error_messages);

                        throw new Exception($error_messages);

                    }

                } else {

                    if (Setting::get('ffmpeg_installed') == FFMPEG_NOT_INSTALLED) {

                        throw new Exception(tr('ffmpeg_need_to_configure'));
                        
                    }

                    $uploadVideovalidator = Validator::make( $request->all(), array(
                        'video'=>$request->id ? 'mimes:mp4' : 'required|mimes:mp4',
                    ));
                    
                    if($uploadVideovalidator->fails()) {

                        $error_messages = implode(',', $uploadVideovalidator->messages()->all());

                        Log::info("Video SAVE - Validation Fails".$error_messages);

                        throw new Exception($error_messages);

                    }

                }

                if ($model->id) {

                    if ($request->video_type == VIDEO_TYPE_UPLOAD) {

                        $uploadVideovalidator = Validator::make( $request->all(), array(
                            'video'=>'required|mimes:mp4',
                        ));

                        if($uploadVideovalidator->fails()) {

                            $error_messages = implode(',', $uploadVideovalidator->messages()->all());

                            Log::info("Video SAVE - Validation Fails".$error_messages);

                            throw new Exception($error_messages);

                        }
                        

                    }

                }

                $model->uploaded_by = $request->uploaded_by ?: "user";

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

                $model->age_limit = $request->has('age_limit') ? $request->age_limit : 0;

                $model->publish_time = $request->has('publish_time') 
                            ? date('Y-m-d H:i:s', strtotime($request->publish_time)) : date('Y-m-d H:i:s');
                

                $model->status = DEFAULT_FALSE;

                // $model->publish_status = DEFAULT_TRUE;

                $model->is_approved = DEFAULT_TRUE;

                if($model->publish_time) {

                    if(strtotime($model->publish_time) <= strtotime(date('Y-m-d H:i:s'))) {

                        $model->publish_status = DEFAULT_TRUE;

                    } else {

                        $model->publish_status = DEFAULT_FALSE;
                    }
                    
                }

                if($request->hasFile('subtitle')) {

                    if ($model->id) {

                        if ($model->subtitle) {

                            Helper::delete_picture($model->subtitle, "/uploads/subtitles/");  

                        }  
                    }

                    $model->subtitle =  Helper::subtitle_upload($request->file('subtitle'));

                }

                /**
                 | Step 1: check the video type = VIDEO_TYPE_UPLOAD , VIDEO_TYPE_YOUTUBE , VIDEO_TYPE_OTHERS
                 |
                 | Step 2: video_type == VIDEO_TYPE_YOUTUBE || VIDEO_TYPE_OTHERS
                 |
                 |          - save the video details and image
                 |
                 | Step 3: video_type = VIDEO_TYPE_UPLOAD
                 |
                 |          - Based on the comporession status and video size, convert the video by different
                 |          - solutions.
                 |
                 */

                $model->video_type = $request->video_type;


                $main_video_duration = "";

                $frames = 0;

                Log::info("video_type".$request->video_type);

                $model->is_banner = $request->has('is_banner') ? $request->is_banner : DEFAULT_FALSE;

                // Save Category

                $new_category = 1;

                $old_category = "";

                if ($model->category_id) {

                    $old_category = Category::find($model->category_id);

                }

                if ($request->category_id == $model->category_id) {

                    $new_category = 0;

                }

                $model->category_id = $request->category_id;

                $category = Category::find($request->category_id);

                $model->category_name = $category->name;

                $model->unique_id = $model->title;

                if(in_array($request->video_type , [ VIDEO_TYPE_YOUTUBE , VIDEO_TYPE_OTHERS] )) {

                    Log::info("INSIDE".$request->video_type);

                    $model->compress_status = DEFAULT_TRUE;
                    
                    $model->status = DEFAULT_TRUE;

                    // Check the previous video upload type and delete the video, if file upload 

                    if($model->id) {

                        Helper::delete_picture($model->video, "/uploads/videos/");

                        self::deleteVideoTapeImage($model->id);
                    }

                    $model->video = $model->id ? ($request->other_video ? $request->other_video : $model->video) : $request->other_video;


                    if($request->hasFile('other_image')) {

                        if ($model->id) {

                            Helper::delete_picture($model->default_image, "/uploads/images/");

                        }

                        $model->default_image = Helper::normal_upload_picture($request->file('other_image') , '/uploads/images/');
                    }

                    $model->duration = $request->duration ? $request->duration : $model->duration;

                } else {
                        
                    // Delete previous videos

                    if($request->hasFile('video')) {
            
                        if($model->id) {

                            Helper::delete_picture($model->video, "/uploads/videos/");

                        }

                        $main_video_duration = Helper::video_upload($request->video);

                        $model->video = $main_video_duration['db_url'];

                        $getDuration = readFileName($main_video_duration['baseUrl']);

                        $seconds = 10;

                        Log::info('seconds : '.$seconds);

                        if ($getDuration) {

                            $model->duration = $getDuration['hours'].':'.$getDuration['mins'].':'.$getDuration['secs'];

                            $seconds = $getDuration['hours'] * 3600 + $getDuration['mins'] * 60 + $getDuration['secs'];

                        }

                        if ($seconds <= 0) {

                            $seconds = 10;

                        }

                        $img = time();

                        $FFmpeg = new \FFmpeg;

                        $frames = ($model->is_banner == DEFAULT_TRUE) ? 4 : 3;

                        Log::info('frames : ' .$frames);

                        Log::info('seconds with frames : '.($seconds/$frames));

                        $FFmpeg
                            ->input($main_video_duration['baseUrl'])
                            ->constantVideoFrames($frames)
                            ->customVideoFrames(1 / ($seconds/$frames))
                            ->output(public_path()."/uploads/images/{$model->channel_id}_{$img}_%03d.png")
                            ->ready();

                        if ($request->video->getClientSize()) {

                            $bytes = convertMegaBytes($request->video->getClientSize());

                            if ($bytes > Setting::get('video_compress_size')) {

                            } else {

                                $model->compress_status = DEFAULT_TRUE;

                                $model->status = DEFAULT_TRUE;
                            }

                        }

                    }

                }

                //$model->video_type = VIDEO_TYPE_UPLOAD;

                $model->save();

                Log::info("saved Video Object : ".'Success');

                if($model->save()) {

                    if ($request->tag_id) {

                        $tag = VideoTapeTag::select('tag_id')
                                ->where('video_tape_id', $model->id)
                                ->get()
                                ->pluck('tag_id')
                                ->where('status', TAG_APPROVE_STATUS)
                                ->toArray();

                        foreach ($tag as $key => $video_tag_id) {
                           
                            if(in_array($video_tag_id, $request->tag_id)) {
             

                            } else {

                                $video_tag = VideoTapeTag::where('tag_id', $video_tag_id)
                                        ->where('video_tape_id', $model->id)->first();

                                if ($video_tag) {

                                    $video_tag->delete();
                                    
                                }

                            }

                        }

                        foreach ($request->tag_id as $key => $tag_id) {
                            
                            $tag = VideoTapeTag::where('tag_id', $tag_id)
                                        ->where('video_tape_id', $model->id)->first();

                            if (!$tag) {

                                $tag = new VideoTapeTag;

                            }

                            $tag->video_tape_id = $model->id;

                            $tag->tag_id = $tag_id;

                            $tag->status = TAG_APPROVE_STATUS;

                            $tag->save();

                        }

                    }

                    if ($new_category) {

                        $category->no_of_uploads += 1;

                        $category->save(); 

                        if($old_category) {

                            $old_category->no_of_uploads =  $old_category->no_of_uploads > 0 ? $old_category->no_of_uploads - 1  : 0;

                            $old_category->save();

                        }
                        
                    }

                    $video_path = [];


                    // Start queues for videos 

                    if($model->video_type == VIDEO_TYPE_UPLOAD) {

                        // Check the redis enable status - If no redis, remove compression video queue
                        
                        if (envfile('QUEUE_DRIVER') != 'redis' || Setting::get('ffmpeg_installed') == FFMPEG_NOT_INSTALLED) {

                            \Log::info("Queue Driver : ".envfile('QUEUE_DRIVER'));

                            $model->status = DEFAULT_TRUE;

                            $model->compress_status = DEFAULT_TRUE;

                            $model->save();

                        } else {

                             // To start compression process, based on the main video duration

                            Log::info(print_r($main_video_duration, true));

                            if($main_video_duration) {

                                $inputFile = $main_video_duration['baseUrl'];
                                $local_url = $main_video_duration['local_url'];
                                $file_name = $main_video_duration['file_name'];

                                if (file_exists($inputFile)) {

                                    $model->status = DEFAULT_FALSE;

                                    $model->compress_status = DEFAULT_FALSE;

                                    $model->save();

                                    Log::info("Main queue Videos : ".'Success');

                                    dispatch(new CompressVideo($inputFile, $local_url, $model->id, $file_name));

                                    Log::info("Main queue completed : ".'Success');
                                }
                            } else {

                                $model->status = DEFAULT_TRUE;

                                $model->compress_status = DEFAULT_TRUE;

                                $model->save();

                            }
                        }

                        if ($frames > 0) {

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

                        }

                    }

                    $model->save();

                    if ($model->status && $model->compress_status) {

                        // Channel Subscription email

                        dispatch(new SubscriptionMail($model->channel_id, $model->id));

                        $notification_data['from_user_id'] = $model->user_id; 

                        $notification_data['to_user_id'] = 0;

                        $notification_data['notification_type'] = BELL_NOTIFICATION_NEW_VIDEO;

                        $notification_data['channel_id'] = $model->channel_id;

                        $notification_data['video_tape_id'] = $model->id;

                        dispatch(new BellNotificationJob(json_decode(json_encode($notification_data))));

                        $title = $content = $model->title;


                        if(check_push_notification_configuration() && Setting::get('push_notification') == YES ) {

                            $push_data = ['type' => PUSH_REDIRECT_SINGLE_VIDEO, 'video_id' => $model->id];

                            dispatch(new sendPushNotification(PUSH_TO_ALL , $title , $content, PUSH_REDIRECT_SINGLE_VIDEO , $model->id, $model->channel_id, $push_data, PUSH_TO_CHANNEL_SUBSCRIBERS));
 
                        }
                        
                    }
                   
                } else {
                    
                    throw new Exception(tr('admin_not_error'));
                    
                    // $response_array = ['success'=>false , 'message'=>tr('admin_not_error')];
                   
                }
            }

            DB::commit();

            $response_array =  ['success'=>true , 'data'=> $model, 'video_path' => $video_path];

        } catch (Exception $e) {

            DB::rollBack();

            $response_array = ['success'=>false, 'error_messages'=>[$e->getMessage(),$e->getLine(),$e->getFile()]];

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

        try {

            DB::beginTransaction();

            $video = VideoTape::find($request->default_image_id);

            if ($video) {

                $required = 0;

                if ($video->video_type != $request->video_type) {

                    $required = 1;

                }

                if (in_array($request->video_type , [ VIDEO_TYPE_YOUTUBE , VIDEO_TYPE_OTHERS] )) {
                    $OtherVideovalidator = Validator::make( $request->all(), array(
                        'other_video'=>'required|url',
                        'other_image' => $required ? 'required|mimes:jpeg,jpg,bmp,png' : 'mimes:jpeg,jpg,bmp,png',
                    ));
                    if($OtherVideovalidator->fails()) {


                        $error_messages = implode(',', $OtherVideovalidator->messages()->all());

                        Log::info("Video SAVE - Validation Fails".$error_messages);

                        throw new Exception($error_messages);

                    }

                    self::deleteVideoTapeImage($video->id);

                    Helper::delete_picture($video->video, "/uploads/videos/");

                } else {

                    $uploadVideovalidator = Validator::make( $request->all(), array(
                        'video'=>$required ? 'required' : '',
                    ));
                    if($uploadVideovalidator->fails()) {

                        $error_messages = implode(',', $uploadVideovalidator->messages()->all());

                        Log::info("Video SAVE - Validation Fails".$error_messages);

                        throw new Exception($error_messages);

                    }

                }

                $video->video_type =  $request->video_type;

                $video->title = $request->has('title') ? $request->title : $video->title;

                $video->description = $request->has('description') ? $request->description : $video->description;

                $video->age_limit = $request->has('age_limit') ? $request->age_limit : 0;

                $video->channel_id = $request->has('channel_id') ? $request->channel_id : $video->channel_id;

                $video->reviews = $request->has('reviews') ? $request->reviews : $video->reviews;

                $video->ratings = $request->has('ratings') ? $request->ratings : $video->ratings;

                $video->video_publish_type = $request->has('video_publish_type') ? $request->video_publish_type : $video->video_publish_type;

                $video->publish_time = $request->has('publish_time') 
                                ? date('Y-m-d H:i:s', strtotime($request->publish_time)) : $video->publish_time;

                if($request->hasFile('subtitle')) {

                    if ($video->id) {

                        if ($video->subtitle) {

                            Helper::delete_picture($video->subtitle, "/uploads/subtitles/");  

                        }  
                    }

                    $video->subtitle =  Helper::subtitle_upload($request->file('subtitle'));

                }

                 // Save Category

                $new_category = 1;

                $old_category = "";

                if ($video->category_id) {

                    $old_category = Category::find($video->category_id);

                }

                if ($request->category_id == $video->category_id) {

                    $new_category = 0;

                }

                $video->category_id = $request->category_id;

                $category = Category::find($request->category_id);

                $video->category_name = $category->name;

                $video->status = DEFAULT_TRUE;

               /* $video->tags = $request->tags ? (is_array($request->tags) ? implode(',', $request->tags) : $request->tags) : '';*/

                $video->save();

                if ($request->tag_id) {

                    $request->tag_id = is_array($request->tag_id) ? $request->tag_id : explode(',', $request->tag_id);

                    $tag = VideoTapeTag::select('tag_id')
                            ->where('video_tape_id', $video->id)
                            ->get()
                            ->pluck('tag_id')
                            ->toArray();

                    foreach ($tag as $key => $video_tag_id) {
                       
                        if(in_array($video_tag_id, $request->tag_id)) {
         

                        } else {

                            $video_tag = VideoTapeTag::where('tag_id', $video_tag_id)
                                    ->where('video_tape_id', $video->id)->first();

                            if ($video_tag) {

                                $video_tag->delete();
                                
                            }

                        }

                    }

                    foreach ($request->tag_id as $key => $tag_id) {
                        
                        $tag = VideoTapeTag::where('tag_id', $tag_id)
                                    ->where('video_tape_id', $video->id)->first();

                        if (!$tag) {

                            $tag = new VideoTapeTag;

                        }

                        $tag->video_tape_id = $video->id;

                        $tag->tag_id = $tag_id;

                        $tag->status = TAG_APPROVE_STATUS;

                        $tag->save();

                    }

                }

                if ($new_category) {

                    $category->no_of_uploads += 1;

                    $category->save(); 

                    if ($old_category) {

                        $old_category->no_of_uploads =  $old_category->no_of_uploads > 0 ? $old_category->no_of_uploads - 1  : 0;

                        $old_category->save();

                    }
                    
                }

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

                DB::commit();

                $response_array = ['success'=>true, 'default_image_id'=>$request->default_image_id];

            } else {


                throw new Exception(tr('video_not_found'));
                

            }

        } catch (Exception $e) {

            DB::rollBack();

            $response_array = ['success'=>false, 'error_messages'=> $e->getMessage()];

        }

        return response()->json($response_array);
    }


    /**
     * Function Name : users_subscription_save
     *
     * To save subscription details based on user id
     *
     * @created Vithya R
     *
     * @updated
     *
     * @param integer $s_id - Subscription id, $u_id - User id
     * 
     * @return - response of array of subscription details
     *
     */
    public static function save_subscription($s_id, $u_id) {

        $subscription = Subscription::find($s_id);

        if ($subscription) {

            $load = UserPayment::where('user_id', $u_id)->orderBy('created_at', 'desc')->first();

            $payment = new UserPayment();

            $payment->subscription_id = $s_id;

            $payment->user_id = $u_id;

            $payment->amount =   $subscription->amount ;

            $payment->subscription_amount =  $subscription->amount;

            $payment->payment_id = ($payment->amount > 0) ? uniqid(str_replace(' ', '-', 'PAY')) : 'Free Plan'; 

            if ($load) {
                $payment->expiry_date = date('Y-m-d H:i:s', strtotime("+{$subscription->plan} months", strtotime($load->expiry_date)));
            } else {
                $payment->expiry_date = date('Y-m-d H:i:s',strtotime("+{$subscription->plan} months"));
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

        } else {

            return response()->json(['success'=> false, 'message'=>tr('subscription_not_found')]);
        }
    }

    /**
     * Function Name : videos_compression_complete()
     *
     * @uses To complete the compressing videos
     *
     * @param integer video id - Video id
     *
     * @created Vithya R
     *
     * @updated
     *
     * @return response of success/failure message
     */
    public static function videos_compression_complete(Request $request) {

        try {

            $video = VideoTape::find($request->id);

            if ($video) {

                // Check the video has compress state or not

                if ($video->compress_status <= DEFAULT_FALSE) {

                    $video->status = DEFAULT_TRUE;

                    $video->compress_status = DEFAULT_TRUE;

                    if($video->save()){

                    
                    } else {

                        throw new Exception(tr('video_not_saved'));
                    
                    }

                } else {

                    throw new Exception(tr('already_video_compressed'));
                    
                }

            } else {

                throw new Exception(tr('video_not_found'));
                
            }

            $response_array = ['success'=>true, 'message'=> tr('video_compress_success')];

            return response()->json($response_array);

        } catch(Exception $e) {

            $response_array = ['success'=>false, 'error_messages'=>$e->getMessage()];

            return response()->json($response_array);
        }
    }

    public static function getUrl($video, $request) {


        $sdp = $video->user_id.'-'.$video->id.'.sdp';

        $device_type = $request->device_type;

        $browser = $request->browser;

        if ($device_type == DEVICE_ANDROID) {

            $url = "rtmp://".Setting::get('cross_platform_url')."/live/".$sdp;

        } else if($device_type == DEVICE_IOS) {

            $url = "http://".Setting::get('cross_platform_url')."/live/".$sdp."/playlist.m3u8";

        } else {

            $browser = $browser ? $browser : get_browser();

            if (strpos($browser, 'safari') !== false) {
                
                $url = "http://".Setting::get('cross_platform_url')."/live/".$sdp."/playlist.m3u8";  

            } else {

                $url = "rtmp://".Setting::get('cross_platform_url')."/live/".$sdp;
            }

        }

        return $url;
    }


    public static function rtmpUrl($model) {

        $RTMP_URL = 'rtmp://'.Setting::get('cross_platform_url').'/live/';

        $url = $RTMP_URL.$model->user_id.'_'.$model->id;

        return $url;
    }


    public static function iosUrl($model) {

        $sdp = $model->video_url;

        $url =  "http://".Setting::get('cross_platform_url')."/live/".$sdp."/playlist.m3u8";

        return $url;
    }

   public static function webIosUrl($model) {

        $sdp = $model->user_id.'-'.$model->id.'.sdp';;

        $url =  "http://".Setting::get('cross_platform_url')."/live/".$sdp."/playlist.m3u8";

        return $url;
    }
}