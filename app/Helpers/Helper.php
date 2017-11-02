<?php 

   namespace App\Helpers;

    use Hash;

    use App\Admin;

    use App\User;

    use Auth;

    use AWS;

    use App\Requests;

    use Mail;

    use File;

    use Log;

    use Storage;

    use Setting;

    use DB;

    use App\Jobs\OriginalVideoCompression;

    use App\VideoTape;

    use App\Wishlist;

    use App\UserHistory;

    use App\UserRating;

    use App\LiveVideo;

    use Intervention\Image\ImageManagerStatic as Image;

    use App\LikeDislikeVideo;
    
    use App\PayPerView;

    class Helper
    {

        /**
         * Used to generate index.php
         *
         * 
         */

        public static function generate_index_file($folder) {

            $filename = public_path()."/".$folder."/index.php"; 

            if(!file_exists($filename)) {

                $index_file = fopen($filename,'w');

                $sitename = Setting::get("site_name");

                fwrite($index_file, '<?php echo "You Are trying to access wrong path!!!!--|E"; ?>');       

                fclose($index_file);
            }
        
        }

        public static function clean($string)
        {
            $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

            return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
        }

        public static function web_url()
        {
            return url('/');
        }

        public static function generate_email_code($value = "")
        {
            return uniqid($value);
        }

        public static function generate_email_expiry()
        {
            return time() + 24*3600*30;  // 30 days
        }

        // Check whether email verification code and expiry

        public static function check_email_verification($verification_code , $data , &$error) 
        {

            // Check the data exists

            if($data) {

                // Check whether verification code is empty or not

                if($verification_code) {

                    if ($verification_code !=  $data->verification_code ) {

                        $error = 'Verification Code Mismatched';

                        return FALSE;

                    }

                }
                    
                // Check whether verification code expiry 

                if ($data->verification_code_expiry > time()) {

                    // Token is valid

                    $error = NULL;

                    return true;

                } else {

                    $data->verification_code = Helper::generate_email_code();

                    $data->verification_code_expiry = Helper::generate_email_expiry();

                    $data->save();

                    // If code expired means send mail to that user

                    $subject = tr('verification_code_title');
                    $email_data = $data;
                    $page = "emails.welcome";
                    $email = $data['email'];
                    $result = Helper::send_email($page,$subject,$email,$email_data);

                    $error = 'Verification Code Expired';

                    return FALSE;
                }
            }
        }

        // Note: $error is passed by reference
        public static function is_token_valid($entity, $id, $token, &$error)
        {
            if (
                ( $entity== 'USER' && ($row = User::where('id', '=', $id)->where('token', '=', $token)->first()) ) ||
                ( $entity== 'PROVIDER' && ($row = Provider::where('id', '=', $id)->where('token', '=', $token)->first()) )
            ) {
                if ($row->token_expiry > time()) {
                    // Token is valid
                    $error = NULL;
                    return $row;
                } else {
                    $error = array('success' => false, 'error' => Helper::get_error_message(103), 'error_code' => 103);
                    return FALSE;
                }
            }
            $error = array('success' => false, 'error' => Helper::get_error_message(104), 'error_code' => 104);
            return FALSE;
        }

        // Convert all NULL values to empty strings
        public static function null_safe($arr)
        {
            $newArr = array();
            foreach ($arr as $key => $value) {
                $newArr[$key] = ($value == NULL) ? "" : $value;
            }
            return $newArr;
        }

        public static function generate_token()
        {
            return Helper::clean(Hash::make(rand() . time() . rand()));
        }

        public static function generate_token_expiry()
        {
            return time() + 24*3600*30;  // 30 days
        }

        public static function send_email($page,$subject,$email,$email_data)
        {
            \Log::info(envfile('MAIL_USERNAME'));

            \Log::info(envfile('MAIL_PASSWORD'));

            if( config('mail.username') &&  config('mail.password')) {

                try {

                    $site_url=url('/');

                    $mail_status = Mail::send($page, array('email_data' => $email_data,'site_url' => $site_url), function ($message) use ($email, $subject) {

                        $message->to($email)->subject($subject);
                        
                    });

                    //If error from Mail::send

                    // if($mail_status->failures() > 0) {

                    //     //Fail for which email address...

                    //     foreach(Mail::failures as $address) {
                           
                    //         print $address . ', ';

                    //     }

                    //     exit;
                    // }  
                } catch(Exception $e) {
                    \Log::info($e);
                    return Helper::get_error_message(123);
                }
                return Helper::get_message(105);

            } else {
                return Helper::get_error_message(123);
            }
        }

        public static function get_error_message($code)
        {
            switch($code) {
                case 101:
                    $string = "Invalid input.";
                    break;
                case 102:
                    $string = "Email address is already in use.";
                    break;
                case 103:
                    $string = "Token expired.";
                    break;
                case 104:
                    $string = "Invalid token.";
                    break;
                case 105:
                    $string = "Invalid email or password.";
                    break;
                case 106:
                    $string = "All fields are required.";
                    break;
                case 107:
                    $string = "The current password is incorrect.";
                    break;
                case 108:
                    $string = "The passwords do not match.";
                    break;
                case 109:
                    $string = "There was a problem with the server. Please try again.";
                    break;
                case 111:
                    $string = "Email is not activated.";
                    break;
                case 115:
                    $string = "Invalid refresh token.";
                    break;
                case 123:
                    $string = "Something went wrong in mail configuration";
                    break;
                case 124:
                    $string = "This Email is not registered";
                    break;
                case 125:
                    $string = "Not a valid social registration User";
                    break;
                case 130:
                    $string = "No results found";
                    break;
                case 131:
                    $string = 'Password doesn\'t match';
                    break;
                case 132:
                    $string = 'Provider ID not found';
                    break;
                case 133:
                    $string = 'User ID not found';
                    break;
                case 141:
                    $string = "Something went wrong while paying amount.";
                    break;
                case 144:
                    $string = "Account is disabled by admin";
                    break;
                case 145:
                    $string = "The video is already added in history.";
                    break;
                case 146:
                    $string = "Something went Wrong.Please try again later!.";
                    break;
                case 147:
                    $string = tr('redeem_disabled_by_admin');
                    break;
                case 148:
                    $string = tr('minimum_redeem_not_have');
                    break;
                case 149:
                    $string = tr('redeem_wallet_empty');
                    break;
                case 150:
                    $string = tr('redeem_request_status_mismatch');
                    break;
                case 151:
                    $string = tr('redeem_not_found');
                    break;
                 case 901:
                    $string = "Default card is not available. Please add a card";
                    break;
                case 902:
                    $string = "Something went wrong with Payment Configuration";
                    break;
                case 903:
                    $string = "Payment is not completed. Please try to pay Again";
                    break;

                case 162:
                    $string = tr('failed_to_upload');

                    break;

                case 163 :
                    $string = tr('streaming_stopped');

                    break;
                case 164:
                    
                    $string = tr('not_yet_started');

                    break;
                case 165 :

                    $string = tr('no_video_found');

                    break;

                case 166 :

                    $string = tr('no_user_found');

                    break;
                case 165 :

                $string = tr('user_not_subscribed');

                break;

                


                case 1000:
                    $string = tr('video_is_in_flag_list');
                    break;
                case 1001:
                    $string = tr('video_not_found');
                    break;

                default:
                    $string = "Unknown error occurred.";
            }
            return $string;
        }

        public static function get_message($code)
        {
            switch($code) {
                case 101:
                    $string = "Success";
                    break;
                case 102:
                    $string = "Password Changed successfully.";
                    break;
                case 103:
                    $string = "Successfully logged in.";
                    break;
                case 104:
                    $string = "Successfully logged out.";
                    break;
                case 105:
                    $string = "Successfully signed up.";
                    break;
                case 106:
                    $string = "Mail sent successfully";
                    break;
                case 107:
                    $string = "Payment successfully done";
                    break;
                case 108:
                    $string = "Favourite provider deleted successfully";
                    break;
                case 109:
                    $string = "Payment mode changed successfully";
                    break;
                case 110:
                    $string = "Payment mode changed successfully";
                    break;
                case 111:
                    $string = "Service Accepted";
                    break;
                case 112:
                    $string = "provider started";
                    break;
                case 113:
                    $string = "Arrived to service location";
                    break;
                case 114:
                    $string = "Service started";
                    break;
                case 115:
                    $string = "Service completed";
                    break;
                case 116:
                    $string = "User rating done";
                    break;
                case 117:
                    $string = "Request cancelled successfully.";
                    break;
                case 118:
                    $string = "Wishlist added.";
                    break;
                case 119:
                    $string = "Payment confirmed successfully.";
                    break;
                case 120:
                    $string = "History added.";
                    break;
                case 121:
                    $string = "History deleted Successfully.";
                    break;
                default:
                    $string = "";
            
            }
            
            return $string;
        }

        public static function get_push_message($code) {

            switch ($code) {
                case 601:
                    $string = "No Provider Available";
                    break;
                case 602:
                    $string = "No provider available to take the Service.";
                    break;
                case 603:
                    $string = "Request completed successfully";
                    break;
                case 604:
                    $string = "New Request";
                    break;
                default:
                    $string = "";
            }

            return $string;

        }

        public static function generate_password()
        {
            $new_password = time();
            $new_password .= rand();
            $new_password = sha1($new_password);
            $new_password = substr($new_password,0,8);
            return $new_password;
        }

        public static function upload_picture($picture)
        {
            Helper::delete_picture($picture, "/uploads/");

            $s3_url = "";

            $file_name = Helper::file_name();

            $ext = $picture->getClientOriginalExtension();
            $local_url = $file_name . "." . $ext;

            if(config('filesystems')['disks']['s3']['key'] && config('filesystems')['disks']['s3']['secret']) {

                Storage::disk('s3')->put($local_url, file_get_contents($picture) ,'public');

                $s3_url = Storage::url($local_url);
            } else {
                $ext = $picture->getClientOriginalExtension();
                $picture->move(public_path() . "/uploads", $file_name . "." . $ext);
                $local_url = $file_name . "." . $ext;

                $s3_url = Helper::web_url().'/uploads/'.$local_url;
            }

            return $s3_url;
        }

        public static function normal_upload_picture($picture, $path, $user = null)
        {
            $s3_url = "";

            $file_name = Helper::file_name();

            $ext = $picture->getClientOriginalExtension();

            $local_url = $file_name . "." . $ext;

            // $path = '/uploads/images/';

            $inputFile = base_path('public'.$path.$local_url);

            // Convert bytes into MB
            $bytes = convertMegaBytes($picture->getClientSize());

            if ($bytes > Setting::get('image_compress_size')) {

                // Compress the video and save in original folder
                $FFmpeg = new \FFmpeg;

                $FFmpeg
                    ->input($picture->getPathname())
                    ->output($inputFile)
                    ->ready();
                // dd($FFmpeg->command);
            } else {

                $picture->move(public_path() . $path, $local_url);

            }


            if ($user) {

                // open an image file
                $img = Image::make(public_path().$path.$local_url);

                // resize image instance
                $img->resize(60, 60);

                // save image in desired format
                $img->save(public_path()."/uploads/user_chat_img/".$local_url);


                $user->chat_picture = Helper::web_url()."/uploads/user_chat_img/".$local_url;
            }

            $s3_url = Helper::web_url().$path.$local_url;

            return $s3_url;
        }


        public static function subtitle_upload($subtitle)
        {
            $s3_url = "";

            $file_name = Helper::file_name();

            $ext = $subtitle->getClientOriginalExtension();

            $local_url = $file_name . "." . $ext;

            $path = '/uploads/subtitles/';

            $subtitle->move(public_path() . $path, $local_url);

            $s3_url = Helper::web_url().$path.$local_url;

            return $s3_url;
        }


        public static function video_upload($picture)
        {
            
            $s3_url = "";

            $file_name = Helper::file_name();

            $ext = $picture->getClientOriginalExtension();

            $local_url = $file_name . ".mp4";

            $path = '/uploads/videos/';

            // dd($picture);

            // Convert bytes into MB
            $bytes = convertMegaBytes($picture->getClientSize());

            $inputFile = public_path().$path.$local_url;

            if ($bytes > Setting::get('video_compress_size')) {

                // dispatch(new OriginalVideoCompression($picture->getPathname(), $inputFile));

                Log::info("Compress Video : ".'Success');

                // Compress the video and save in original folder
                $FFmpeg = new \FFmpeg;

                $FFmpeg
                    ->input($picture->getPathname())
                    ->vcodec('h264')
                    ->constantRateFactor('28')
                    ->output($inputFile, 'mp4')
                    ->ready();

            } else {
                Log::info("Original Video");

                $picture->move(public_path() . $path, $local_url);
            }

            $s3_url = Helper::web_url().$path.$local_url;        

            Log::info("Compress Video completed");

            return ['db_url'=>$s3_url, 'baseUrl'=> $inputFile, 'local_url'=>$local_url, 'file_name'=>$file_name];
        }

        public static function delete_picture($picture, $path) {

            if (file_exists(public_path() . $path . basename($picture))) {
            // "/uploads/"
                File::delete( public_path() . $path . basename($picture));

            }
            return true;
        }

        public static function s3_delete_picture($picture) {
            Log::info($picture);

            Storage::Delete(basename($picture));
            return true;
        }

        public static function file_name() {

            $file_name = time();
            $file_name .= rand();
            $file_name = sha1($file_name);

            return $file_name;
        }

        public static function send_notification($id,$title,$message) {

            Log::info("Send Push Started");

            // Check the user type whether "USER" or "PROVIDER"
            if($id == "all") {
                $users = User::where('push_status' , 1)->get();
            } else {
                $users = User::find($id);
            }

            $push_data = array();

            $push_message = array('success' => true,'message' => $message,'data' => array());

            $push_notification = 1; // Check the push notifictaion is enabled

            if ($push_notification == 1) {

                Log::info('Admin enabled the push ');

                if($users){

                    Log::info('Check users variable');

                    foreach ($users as $key => $user) {

                        Log::info('Individual User');

                        if ($user->device_type == 'ios') {

                            Log::info("iOS push Started");

                            require_once app_path().'/ios/apns.php';

                            $msg = array("alert" => $message,
                                "status" => "success",
                                "title" => $title,
                                "message" => $push_message,
                                "badge" => 1,
                                "sound" => "default",
                                "status" => "",
                                "rid" => "",
                                );

                            if (!isset($user->device_token) || empty($user->device_token)) {
                                $deviceTokens = array();
                            } else {
                                $deviceTokens = $user->device_token;
                            }

                            $apns = new \Apns();
                            $apns->send_notification($deviceTokens, $msg);

                            Log::info("iOS push end");

                        } else {

                            Log::info("Andriod push Started");

                            require_once app_path().'/gcm/GCM_1.php';
                            require_once app_path().'/gcm/const.php';

                            if (!isset($user->device_token) || empty($user->device_token)) {
                                $registatoin_ids = "0";
                            } else {
                                $registatoin_ids = trim($user->device_token);
                            }
                            if (!isset($push_message) || empty($push_message)) {
                                $msg = "Message not set";
                            } else {
                                $msg = $push_message;
                            }
                            if (!isset($title) || empty($title)) {
                                $title1 = "Message not set";
                            } else {
                                $title1 = trim($title);
                            }

                            $message = array(TEAM => $title1, MESSAGE => $msg);

                            $gcm = new \GCM();
                            $registatoin_ids = array($registatoin_ids);
                            $gcm->send_notification($registatoin_ids, $message);

                            Log::info("Andriod push end");

                        }

                    }

                }

            } else {
                Log::info('Push notifictaion is not enabled. Please contact admin');
            }

            Log::info("*************************************");

        }



        /**
         *  Function Name : search_video()
         */
        public static function search_video($request,$key,$web = NULL,$skip = 0) {

            $videos_query = VideoTape::where('video_tapes.is_approved' ,'=', 1)
                        ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                        ->where('title','like', '%'.$key.'%')
                        ->where('video_tapes.status' , 1)
                        ->videoResponse()
                        ->where('video_tapes.age_limit','<=', checkAge($request))
                        ->orderBy('video_tapes.created_at' , 'desc');
            if($web) {
                $videos = $videos_query->paginate(16);
            } else {
                $videos = $videos_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();
            }

            return $videos;
        }

        public static function wishlists($user_id ) {

            $data = $ids = [];

            if($user_id) {

                $data = Wishlist::where('wishlists.user_id' , $user_id)->orderby('wishlists.created_at', 'desc')->pluck('video_tape_id');

                if(count($data) > 0) {

                    foreach ($data as $key => $value) {

                        $ids[] = $value;

                    }
                }


            }

            return $ids;
        }

        public static function history($user_id ) {

            $data = $ids = [];

            if($user_id) {

                $data = UserHistory::where('user_histories.user_id' , $user_id)->pluck('video_tape_id');

                if(count($data) > 0) {

                    foreach ($data as $key => $value) {

                        $ids[] = $value;

                    }
                }


            }

            return $ids;
        }


        public static function live_video_search($request,$key,$web = NULL,$skip = 0) {

            $videos_query = LiveVideo::where('live_videos.is_streaming' ,DEFAULT_TRUE)
                        ->where('title','like', '%'.$key.'%')
                        ->where('live_videos.status' ,DEFAULT_FALSE)
                        ->orderBy('live_videos.created_at' , 'desc');
            if($web) {
                $videos = $videos_query->paginate(16);
            } else {
                $videos = $videos_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();
            }

            return $videos;
        }

        public static function check_wishlist_status($user_id,$video_id) {

            $status = Wishlist::where('wishlists.user_id' , $user_id)
                                        ->where('video_tape_id' , $video_id)
                                        ->first();

            return $status ? $status : 0;
        }


        public static function history_status($video_id,$user_id) {
            if(UserHistory::where('video_tape_id' , $video_id)->where('user_histories.user_id' , $user_id)->count()) {
                return 1;
            } else {
                return 0;
            }
        }


        public static function upload_avatar($folder,$picture) {

            $file_name = Helper::file_name();

            $ext = $picture->getClientOriginalExtension();

            $local_url = $file_name . "." . $ext;

            $ext = $picture->getClientOriginalExtension();

            $picture->move(public_path()."/".$folder, $file_name . "." . $ext);

            $url = Helper::web_url().'/'.$folder."/".$local_url;

            return $url;
        
        }

        public static function delete_avatar($folder,$picture) {
            File::delete( public_path() . "/".$folder."/". basename($picture));
            return true;
        }


        public static function banner_videos() {

            $videos = VideoTape::where('video_tapes.is_approved' , 1)
                            ->where('video_tapes.status' , 1)
                            ->where('video_tapes.is_banner' , 1)
                            ->select(
                                'video_tapes.id as video_tape_id' ,
                                'video_tapes.title','video_tapes.ratings',
                                'video_tapes.banner_image as default_image'
                                )
                            ->orderBy('created_at' , 'desc')
                            ->get();

            return $videos;
        }

         public static function video_ratings($video_id) {

            $ratings = UserRating::where('video_tape_id' , $video_id)
                            ->leftJoin('users' , 'user_ratings.user_id' , '=' , 'users.id')
                            ->select('users.id as user_id' , 'users.name as username',
                                    'users.picture as picture' ,
                                    'user_ratings.rating' , 'user_ratings.comment',
                                    'user_ratings.created_at')
                            ->orderby('created_at', 'desc')
                            ->get();
            if(!$ratings) {
                $ratings = array();
            }

            return $ratings;
        }


        public static function get_user_comments($user_id,$web = NULL) {

            $videos_query = UserRating::where('user_id' , $user_id)
                            ->leftJoin('video_tapes' ,'user_ratings.video_tape_id' , '=' , 'video_tapes.id')
                            ->where('video_tapes.is_approved' , 1)
                            ->where('video_tapes.status' , 1)
                            ->select('video_tapes.id as video_tape_id' ,
                                'video_tapes.title','video_tapes.description' ,
                                'default_image','video_tapes.watch_count',
                                'video_tapes.duration',
                                DB::raw('DATE_FORMAT(video_tapes.publish_time , "%e %b %y") as publish_time'))
                            ->orderby('user_ratings.created_at' , 'desc')
                            ->groupBy('video_tapes.id');

            if($web) {
                $videos = $videos_query->paginate(16);
            } else {
                $videos = $videos_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();
            }

            return $videos;

        }


        public static function upload_language_file($folder,$picture,$filename) {

            $ext = $picture->getClientOriginalExtension();
            
            $picture->move(base_path() . "/resources/lang/".$folder ."/", $filename);

        }

        public static function delete_language_files($folder, $boolean, $filename) {
            if ($boolean) {
                $path = base_path() . "/resources/lang/" .$folder;
                \File::cleanDirectory($path);
                \Storage::deleteDirectory( $path );
                rmdir( $path );
            } else {
                \File::delete( base_path() . "/resources/lang/" . $folder ."/".$filename);
            }
            return true;
        }

        public static function like_status($user_id,$video_id) {

            if(LikeDislikeVideo::where('video_tape_id' , $video_id)->where('user_id' , $user_id)->where('like_status' , DEFAULT_TRUE)->count()) {

                return 1;

            } else {

                return 0;
            }
        }

        /**
         * Function Name : watchFullVideo()
         * To check whether the user has to pay the amount or not
         * 
         * @param integer $user_id User id
         * @param integer $user_type User Type
         * @param integer $video_id Video Id
         * 
         * @return true or not
         */

        public static function watchFullVideo($user_id, $user_type, $video) {
            
            return false;
        }
    }



