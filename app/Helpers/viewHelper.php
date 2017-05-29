<?php

use App\Helpers\Helper;

use App\Helpers\EnvEditorHelper;

use Carbon\Carbon;

use App\Wishlist;

use App\VideoTape;

use App\UserHistory;

use App\UserRating;

use App\User;

use App\MobileRegister;

use App\PageCounter;

use App\UserPayment;

use App\Settings;

use App\Flag;

use App\Channel;

use App\Redeem;

function tr($key) {

    if (!\Session::has('locale'))
        \Session::put('locale', \Config::get('app.locale'));
    return \Lang::choice('messages.'.$key, 0, Array(), \Session::get('locale'));

}


function envfile($key) {

    $data = EnvEditorHelper::getEnvValues();

    if($data) {
        return $data[$key];
    }

    return "";
}

function get_video_end($video_url) {
    $url = explode('/',$video_url);
    $result = end($url);
    return $result;
}


function register_mobile($device_type) {
    if($reg = MobileRegister::where('type' , $device_type)->first()) {
        $reg->count = $reg->count + 1;
        $reg->save();
    }
    
}

/**
 * Function Name : subtract_count()
 * While Delete user, subtract the count from mobile register table based on the device type
 *
 * @param string $device_ype : Device Type (Andriod,web or IOS)
 * 
 * @return boolean
 */
function subtract_count($device_type) {
    if($reg = MobileRegister::where('type' , $device_type)->first()) {
        $reg->count = $reg->count - 1;
        $reg->save();
    }
}

function get_register_count() {

    $ios_count = MobileRegister::where('type' , 'ios')->first()->count;

    $android_count = MobileRegister::where('type' , 'android')->first()->count;

    $web_count = MobileRegister::where('type' , 'web')->first()->count;

    $total = $ios_count + $android_count + $web_count;

    return array('total' => $total , 'ios' => $ios_count , 'android' => $android_count , 'web' => $web_count);
}

function last_days($days){

  $views = PageCounter::orderBy('created_at','asc')->where('created_at', '>', Carbon::now()->subDays($days))->where('page','home');
  $arr = array();
  $arr['count'] = $views->count();
  $arr['get'] = $views->get();

  return $arr;

}

function counter($page){

    $count_home = PageCounter::wherePage($page)->where('created_at', '>=', new DateTime('today'));

        if($count_home->count() > 0){
            $update_count = $count_home->first();
            $update_count->count = $update_count->count + 1;
            $update_count->save();
        }else{
            $create_count = new PageCounter;
            $create_count->page = $page;
            $create_count->count = 1;
            $create_count->save();
        }
}

function get_recent_users() {
    $users = User::orderBy('created_at' , 'desc')->skip(0)->take(12)->get();

    return $users;
}
function get_recent_videos() {
    $videos = AdminVideo::orderBy('publish_time' , 'desc')->skip(0)->take(12)->get();

    return $videos;
}
function total_revenue() {
    return UserPayment::sum('amount');
}

function check_s3_configure() {

    $key = config('filesystems.disks.s3.key');

    $secret = config('filesystems.disks.s3.secret');

    $bucket = config('filesystems.disks.s3.bucket');

    $region = config('filesystems.disks.s3.region');

    if($key && $secret && $bucket && $region) {
        return 1;
    } else {
        return false;
    }
}


function check_valid_url($file) {

    return 1;

}

function check_nginx_configure() {
    $nginx = shell_exec('nginx -V');
    if($nginx) {
        return true;
    } else {
        if(file_exists("/usr/local/nginx-streaming/conf/nginx.conf")) {
            return true;
        } else {
           return false; 
        }
    }
}

function check_php_configure() {
    return phpversion();
}

function check_mysql_configure() {

    $output = shell_exec('mysql -V');

    $data = 1;

    if($output) {
        preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $version); 
        $data = $version[0];
    }

    return $data; 
}

function check_database_configure() {

    $status = 0;

    $database = config('database.connections.mysql.database');
    $username = config('database.connections.mysql.username');

    if($database && $username) {
        $status = 1;
    }
    return $status;

}

function check_settings_seeder() {
    return Settings::count();
}



/**
 * Function Name : getReportVideoTypes()
 * Load all report video types in settings table
 *
 * @return array of values
 */ 
function getReportVideoTypes() {
    // Load Report Video values
    $model = Settings::where('key', REPORT_VIDEO_KEY)->get();
    // Return array of values
    return $model;

}

/**
 * Function Name : getFlagVideos()
 * To load the videos based on the user
 *
 * @param int $id User Id
 *
 * @return array of values
 */
function flag_videos($id) {

    // Load Flag videos based on logged in user id
    $model = Flag::where('user_id', $id)
        ->leftJoin('video_tapes' , 'flags.video_tape_id' , '=' , 'video_tapes.id')
        ->where('video_tapes.is_approved' , 1)
        ->where('video_tapes.status' , 1)
        ->pluck('video_tape_id')->toArray();

    // Return array of id's
    return $model;
}

/**
 * Function Name : getFlagVideosCnt()
 * To load the videos cnt based on the user 
 *
 * @param int $id User Id
 *
 * @return cnt
 */
function getFlagVideosCnt($id) {
    // Load Flag videos based on logged in user id
    $model = Flag::where('user_id', $id)
        ->leftJoin('video_tapes' , 'flags.video_tape_id' , '=' , 'video_tapes.id')
        ->where('video_tapes.is_approved' , 1)
        ->where('video_tapes.status' , 1)
        ->count();
    // Return array of id's
    return $model;

}


/**
 * Function Name : getImageResolutions()
 * Load all image resoltions types in settings table
 *
 * @return array of values
 */ 
function getImageResolutions() {
    // Load Report Video values
    $model = Settings::where('key', IMAGE_RESOLUTIONS_KEY)->get();
    // Return array of values
    return $model;
}

/**
 * Function Name : getVideoResolutions()
 * Load all video resoltions types in settings table
 *
 * @return array of values
 */ 
function getVideoResolutions() {
    // Load Report Video values
    $model = Settings::where('key', VIDEO_RESOLUTIONS_KEY)->get();
    // Return array of values
    return $model;
}

/**
 * Function Name : convertMegaBytes()
 * Convert bytes into mega bytes
 *
 * @return number
 */
function convertMegaBytes($bytes) {
    return number_format($bytes / 1048576, 2);
}

/**
 * Function Name : get_video_attributes()
 * To get video Attributes
 *
 * @param string $video Video file name
 *
 * @return attributes
 */
function get_video_attributes($video) {

    $command = 'ffmpeg -i ' . $video . ' -vstats 2>&1';

    Log::info("Path ".$video);

    $output = shell_exec($command);

    Log::info("Shell Exec : ".$output);


    $codec = null; $width = null; $height = null;

    $regex_sizes = "/Video: ([^,]*), ([^,]*), ([0-9]{1,4})x([0-9]{1,4})/";

    Log::info("Preg Match :" .preg_match($regex_sizes, $output, $regs));
    if (preg_match($regex_sizes, $output, $regs)) {
        $codec = $regs [1] ? $regs [1] : null;
        $width = $regs [3] ? $regs [3] : null;
        $height = $regs [4] ? $regs [4] : null;
    }

    $hours = $mins = $secs = $ms = null;
    
    $regex_duration = "/Duration: ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2}).([0-9]{1,2})/";
    if (preg_match($regex_duration, $output, $regs)) {
        $hours = $regs [1] ? $regs [1] : null;
        $mins = $regs [2] ? $regs [2] : null;
        $secs = $regs [3] ? $regs [3] : null;
        $ms = $regs [4] ? $regs [4] : null;
    }

    Log::info("Width of the video : ".$width);
    Log::info("Height of the video : ".$height);

    return array('codec' => $codec,
        'width' => $width,
        'height' => $height,
        'hours' => $hours,
        'mins' => $mins,
        'secs' => $secs,
        'ms' => $ms
    );
}


/**
 * Function Name :readFile()
 * To read a input file and get attributes
 * 
 * @param string $inputFile File name
 *
 * @return $attributes
 */
function readFileName($inputFile) {

    $finfo = finfo_open(FILEINFO_MIME_TYPE);

    $mime_type = finfo_file($finfo, $inputFile); // check mime type

    finfo_close($finfo);

    $video_attributes = [];
    
    if (preg_match('/video\/*/', $mime_type)) {

        Log::info("Inside ffmpeg");

        $video_attributes = get_video_attributes($inputFile, 'ffmpeg');
    } 

    return $video_attributes;
}

function getResolutionsPath($video, $resolutions, $streaming_url) {

    $video_resolutions = ($streaming_url) ? [$streaming_url.Setting::get('original_key').get_video_end($video)] : [$video];

    $pixels = ['Original'];
    $exp = explode('original/', $video);

    if (count($exp) == 2) {
        if ($resolutions) {
            $split = explode(',', $resolutions);
            foreach ($split as $key => $resoltuion) {
                $streamUrl = ($streaming_url) ? $streaming_url.Setting::get($resoltuion.'_key').$exp[1] : $exp[0].$resoltuion.'/'.$exp[1];
                array_push($video_resolutions, $streamUrl);
                $splitre = explode('x', $resoltuion);
                array_push($pixels, $splitre[1].'p');
            }
        }
    }
    $video_resolutions = implode(',', $video_resolutions);

    $pixels = implode(',', $pixels);
    return ['video_resolutions' => $video_resolutions, 'pixels'=> $pixels];
}


function deleteVideoAndImages($video) {

    if ($video->video_type == VIDEO_TYPE_UPLOAD ) {
        if($video->video_upload_type == VIDEO_UPLOAD_TYPE_s3) {
            Helper::s3_delete_picture($video->video);   
            Helper::s3_delete_picture($video->trailer_video);  
        } else {
            $videopath = '/uploads/videos/original/';
            Helper::delete_picture($video->video, $videopath); 
            $splitVideos = ($video->video_resolutions) 
                        ? explode(',', $video->video_resolutions)
                        : [];
            foreach ($splitVideos as $key => $value) {
               Helper::delete_picture($video->video, $videopath.$value.'/');
            }

            Helper::delete_picture($video->trailer_video, $videopath);
            // @TODO
            $splitTrailer = ($video->trailer_video_resolutions) 
                        ? explode(',', $video->trailer_video_resolutions)
                        : [];
            foreach ($splitTrailer as $key => $value) {
               Helper::delete_picture($video->trailer_video, $videopath.$value.'/');
            }
        }
    }

    if($video->default_image) {
        Helper::delete_picture($video->default_image, "/uploads/images/");
    }

    if($video->is_banner == 1) {
        if($video->banner_image) {
            Helper::delete_picture($video->banner_image, "/uploads/images/");
        }
    }
}

/**
 * Check the default subscription is enabled by admin
 *
 */

function user_type_check($user) {

    $user = User::find($user);

    if($user) {

        // User need subscripe the plan

        if(Setting::get('is_subscription')) {

            $user->user_type = 0;

        } else {
            // Enable the user as paid user
            $user->user_type = 1;
        }

        $user->save();

    }

}


function get_expiry_days($id) {
    
    $data = UserPayment::where('user_id' , $id)->orderBy('updated_at', 'desc')->first();

    // User Amount

    $amt = UserPayment::select('user_id', DB::raw('sum(amount) as amt'))->where('user_id', $id)->groupBy('user_id')->first();

    $days = 0;

    if($data) {
        $start_date = new \DateTime(date('Y-m-d h:i:s'));
        $end_date = new \DateTime($data->expiry_date);

        $time_interval = date_diff($start_date,$end_date);
        $days = $time_interval->days;
    }

    return ['days'=>$days, 'amount'=>($amt) ? $amt->amt : 0];
}


//this function convert string to UTC time zone

function convertTimeToUTCzone($str, $userTimezone, $format = 'Y-m-d H:i:s') {

    try {
        $new_str = new DateTime($str, new DateTimeZone($userTimezone));

        $new_str->setTimeZone(new DateTimeZone('UTC'));
    }
    catch(\Exception $e) {

    }

    return $new_str->format( $format);
}

//this function converts string from UTC time zone to current user timezone

function convertTimeToUSERzone($str, $userTimezone, $format = 'Y-m-d H:i:s') {

    if(empty($str)){
        return '';
    }
    try{
        $new_str = new DateTime($str, new DateTimeZone('UTC') );
        $new_str->setTimeZone(new DateTimeZone( $userTimezone ));
    }
    catch(\Exception $e) {
        // Do Nothing
    }
    
    return $new_str->format( $format);
}


/**
 * Function Name : getFlagVideos()
 * To load the videos based on the user
 *
 * @param int $id User Id
 *
 * @return array of values
 */
function getFlagVideos($id) {
    // Load Flag videos based on logged in user id
    $model = Flag::where('user_id', $id)
        ->leftJoin('video_tapes' , 'flags.video_tape_id' , '=' , 'video_tapes.id')
        ->where('video_tapes.is_approved' , 1)
        ->where('video_tapes.status' , 1)
        ->pluck('video_tape_id')->toArray();
    // Return array of id's
    return $model;
}

function total_subscription_revenue($id = "") {

    if($id) {
        return UserPayment::where('subscription_id' , $id)->sum('amount');
    }
    return UserPayment::sum('amount');
}

function loadChannels() {

    $model = Channel::where('channels.is_approved', DEFAULT_TRUE)
                ->select('channels.*', 'video_tapes.id as admin_video_id', 'video_tapes.is_approved',
                    'video_tapes.status', 'video_tapes.channel_id')
                ->leftJoin('video_tapes', 'video_tapes.channel_id', '=', 'channels.id')
                ->where('channels.status', DEFAULT_TRUE)
                ->where('video_tapes.is_approved', DEFAULT_TRUE)
                ->where('video_tapes.status', DEFAULT_TRUE)
                ->groupBy('video_tapes.channel_id')
                ->get();
    
    return $model;
}


function getChannels($id = null) {

    $model = Channel::where('is_approved', DEFAULT_TRUE)->where('status', DEFAULT_TRUE);


    if ($id) {
        $model->where('user_id', $id);
    }

    $response = $model->get();

    return $response;
}

function getAmountBasedChannel($id) {

    $model = VideoTape::where('channel_id', $id)->sum('amount');

    return $model;

}

// Based on the request type, it will return string value for that request type

function redeem_request_status($status) {
    
    if($status == REDEEM_REQUEST_SENT) {
        $string = tr('REDEEM_REQUEST_SENT');
    } elseif($status == REDEEM_REQUEST_PROCESSING) {
        $string = tr('REDEEM_REQUEST_PROCESSING');
    } elseif($status == REDEEM_REQUEST_PAID) {
        $string = tr('REDEEM_REQUEST_PAID');
    } elseif($status == REDEEM_REQUEST_CANCEL) {
        $string = tr('REDEEM_REQUEST_CANCEL');
    } else {
        $string = tr('REDEEM_REQUEST_SENT');
    }

    return $string;
}

/**
 * Function : add_to_redeem()
 * 
 * @param $id = role ID
 *
 * @param $amount = earnings
 *
 * @description : If the role earned any amount, use this function to update the redeems
 *
 */

function add_to_redeem($id , $amount) {

    \Log::info('Add to Redeem Start');

    if($id && $amount) {

        $data = Redeem::where('user_id' , $id)->first();

        if(!$data) {
            $data = new Redeem;
            $data->user_id = $id;
        }

        $data->total = $data->total + $amount;
        $data->remaining = $data->remaining+$amount;
        $data->save();
   
    }

    \Log::info('Add to Redeem End');
}

function get_banner_count() {
    return VideoTape::where('is_banner' , 1)->count();
}