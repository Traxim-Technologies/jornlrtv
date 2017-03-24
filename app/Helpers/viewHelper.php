<?php

use App\Helpers\Helper;

use Carbon\Carbon;

use App\SubCategoryImage;

use App\AdminVideoImage;

use App\Category;

use App\SubCategory;

use App\Genre;

use App\Wishlist;

use App\AdminVideo;

use App\UserHistory;

use App\UserRating;

use App\User;

use App\MobileRegister;

use App\PageCounter;

use App\UserPayment;

use App\Settings;

use App\Flag;

use App\PayPerView;

function tr($key) {

    if (!\Session::has('locale'))
        \Session::put('locale', \Config::get('app.locale'));
    return \Lang::choice('messages.'.$key, 0, Array(), \Session::get('locale'));

}

function sub_category_image($picture , $sub_category_id,$position) {

    $image = new SubCategoryImage;

    $check_image = SubCategoryImage::where('sub_category_id' , $sub_category_id)->where('position' , $position)->first();

    if($check_image) {
        $image = $check_image;
    }

    $image->sub_category_id = $sub_category_id;
    $url = Helper::normal_upload_picture($picture);
    $image->picture = $url ? $url : asset('admin-css/img/dummy.jpeg');
    $image->position = $position;
    $image->save();

    return true;
}
function get_sub_category_image($sub_category_id) {

    $images = SubCategoryImage::where('sub_category_id' , $sub_category_id)
                    ->orderBy('position' , 'ASC')
                    ->get();

    return $images;

}

function get_categories() {

    $categories = Category::where('categories.is_approved' , 1)
                        ->select('categories.id as id' , 'categories.name' , 'categories.picture' ,
                            'categories.is_series' ,'categories.status' , 'categories.is_approved')
                        ->leftJoin('admin_videos' , 'categories.id' , '=' , 'admin_videos.category_id')
                        // ->leftJoin('flags' , 'flags.video_id' , '=' , 'admin_videos.id')
                        ->where('admin_videos.status' , 1)
                        ->where('admin_videos.is_approved' , 1)
                        ->groupBy('admin_videos.category_id')
                        ->havingRaw("COUNT(admin_videos.id) > 0")
                        ->orderBy('name' , 'ASC')
                        ->get();
    return $categories;
}

function get_sub_categories($category_id) {

    $sub_categories = SubCategory::where('sub_categories.category_id' , $category_id)
                        ->select('sub_categories.id as id' , 'sub_categories.name' ,
                            'sub_categories.status' , 'sub_categories.is_approved')
                        ->leftJoin('admin_videos' , 'sub_categories.id' , '=' , 'admin_videos.sub_category_id')
                        ->groupBy('admin_videos.sub_category_id')
                        ->havingRaw("COUNT(admin_videos.id) > 0")
                        ->where('sub_categories.is_approved' , 1)
                        ->orderBy('sub_categories.name' , 'ASC')
                        ->get();
    return $sub_categories;
}

function get_category_video_count($category_id) {

    $count = AdminVideo::where('category_id' , $category_id)
                    ->where('is_approved' , 1)
                    ->where('admin_videos.status' , 1)
                    ->count();

    return $count;
}

function get_video_fav_count($video_id) {

    $count = Wishlist::where('admin_video_id' , $video_id)
                ->leftJoin('admin_videos' ,'wishlists.admin_video_id' , '=' , 'admin_videos.id')
                ->where('admin_videos.is_approved' , 1)
                ->where('admin_videos.status' , 1)
                ->count();

    return $count;
}

function get_user_history_count($user_id) {
    $count = UserHistory::where('user_id' , $user_id)
                ->leftJoin('admin_videos' ,'user_histories.admin_video_id' , '=' , 'admin_videos.id')
                ->where('admin_videos.is_approved' , 1)
                ->where('admin_videos.status' , 1)
                ->count();

    return $count;
}

function get_user_wishlist_count($user_id) {

    $count = Wishlist::where('user_id' , $user_id)
                ->leftJoin('admin_videos' ,'wishlists.admin_video_id' , '=' , 'admin_videos.id')
                ->where('admin_videos.is_approved' , 1)
                ->where('admin_videos.status' , 1)
                ->count();

    return $count;
}

function get_user_comment_count($user_id) {

    $count = UserRating::where('user_id' , $user_id)
                ->leftJoin('admin_videos' ,'user_ratings.admin_video_id' , '=' , 'admin_videos.id')
                ->where('admin_videos.is_approved' , 1)
                ->where('admin_videos.status' , 1)
                ->count();

    return $count;

}

function get_video_comment_count($video_id) {

    $count = UserRating::where('admin_video_id' , $video_id)
                ->leftJoin('admin_videos' ,'user_ratings.admin_video_id' , '=' , 'admin_videos.id')
                ->where('admin_videos.is_approved' , 1)
                ->where('admin_videos.status' , 1)
                ->count();

    return $count;

}

function total_video_count() {
    
    $count = AdminVideo::where('is_approved' , 1)->where('admin_videos.status' , 1)->count();

    return $count;

}

function get_sub_category_video_count($id) {
    
    $count = AdminVideo::where('sub_category_id' , $id)->where('admin_videos.status' , 1)->where('is_approved' , 1)->count();

    return $count;
}
function get_genre_video_count($id) {
    
    $count = AdminVideo::where('genre_id' , $id)->where('admin_videos.status' , 1)->where('is_approved' , 1)->count();

    return $count;
}

function get_sub_category_details($id) {

    $sub_category = SubCategory::where('id' , $id)->first();

    return $sub_category;
}

function get_genre_details($id) {

    $genre = Genre::where('id' , $id)->first();

    return $genre;
}

function get_genres($id) {

    $genres = Genre::where('sub_category_id' , $id)->where('is_approved'  , 1)->get();

    return $genres;
}

function get_youtube_embed_link($video_url) {

    if(strpos($video_url , 'embed')) {
       return $video_url;
    }

    $video_url_id = substr($video_url, strpos($video_url, "=") + 1);

    $youtube_embed = "https://www.youtube.com/embed/" . $video_url_id;

    return $youtube_embed;

}

function get_video_end($video_url) {
    $url = explode('/',$video_url);
    $result = end($url);

    return $result;
}

function get_video_image($video_id)
{
    $video_image = AdminVideoImage::where('admin_video_id',$video_id)->orderBy('position','ASC')->get();
    return $video_image;
}

function wishlist($user_id) {

    $videos = Wishlist::where('user_id' , $user_id)
                    ->leftJoin('admin_videos' ,'wishlists.admin_video_id' , '=' , 'admin_videos.id')
                    ->leftJoin('categories' ,'admin_videos.category_id' , '=' , 'categories.id')
                    ->where('admin_videos.is_approved' , 1)
                    ->select(
                            'wishlists.id as wishlist_id',
                            'admin_videos.id as admin_video_id' ,
                            'admin_videos.title','admin_videos.description' ,
                            'default_image','admin_videos.watch_count',
                            'admin_videos.default_image',
                            'admin_videos.ratings',
                            'admin_videos.duration',
                            DB::raw('DATE_FORMAT(admin_videos.publish_time , "%e %b %y") as publish_time') , 'categories.name as category_name')
                    ->orderby('wishlists.created_at' , 'desc')
                    ->skip(0)->take(10)
                    ->get();

    if(!$videos) {
        $videos = array();
    }

    return $videos;
}

function trending() {

    $videos = AdminVideo::where('watch_count' , '>' , 0)
                    ->select(
                        'admin_videos.id as admin_video_id' , 
                        'admin_videos.title',
                        'admin_videos.description',
                        'default_image','admin_videos.watch_count' , 
                        'admin_videos.publish_time',
                        'admin_videos.default_image',
                        'admin_videos.ratings'
                        )
                    ->orderby('watch_count' , 'desc')
                    ->skip(0)->take(10)
                    ->get();

    return $videos;
}

function category_video_count($category_id)
{
    $category_video_count = AdminVideo::where('category_id',$category_id)->where('is_approved' , 1)->count();
    return $category_video_count;
}

function sub_category_videos($sub_category_id) 
{

    $videos_query = AdminVideo::where('admin_videos.is_approved' , 1)
                ->leftJoin('categories' , 'admin_videos.category_id' , '=' , 'categories.id')
                ->leftJoin('sub_categories' , 'admin_videos.sub_category_id' , '=' , 'sub_categories.id')
                ->where('admin_videos.sub_category_id' , $sub_category_id)
                ->select(
                    'admin_videos.id as admin_video_id' , 
                    'admin_videos.default_image' , 
                    'admin_videos.ratings' , 
                    'admin_videos.watch_count' , 
                    'admin_videos.title' ,
                    'admin_videos.description',
                    'admin_videos.sub_category_id' , 
                    'admin_videos.category_id',
                    'categories.name as category_name',
                    'sub_categories.name as sub_category_name',
                    'admin_videos.duration',
                    DB::raw('DATE_FORMAT(admin_videos.publish_time , "%e %b %y") as publish_time')
                    )
                ->orderby('admin_videos.sub_category_id' , 'asc');
    if (Auth::check()) {
        // Check any flagged videos are present
        $flagVideos = getFlagVideos(Auth::user()->id);

        if($flagVideos) {
            $videos_query->whereNotIn('admin_videos.id', $flagVideos);
        }
    }

    $videos = $videos_query->get();

    if(!$videos) {
        $videos = array();
    }

    return $videos;
} 

function change_theme($old_theme,$new_theme) {

    Log::info('The Request came inside of the \'change_theme\' function');

    \Artisan::call('view:clear');

    \Artisan::call('cache:clear');

    /** View Folder change **/

    if(file_exists(base_path('resources/views/user'))) {

        Log::info('Change old theme as original theme ');

        // Change current theme as original theme 

        $current_path=base_path('resources/views/user');
        $new_path=base_path('resources/views/'.$old_theme);

        rename($current_path,$new_path);

        Log::info('Old theme changed');

    }

    if(file_exists(base_path('resources/views/'.$new_theme))) {

        Log::info('make the user requested theme as the current theme');

        // make the user requested theme as the current theme

        $current_path=base_path('resources/views/'.$new_theme);
        $new_path=base_path('resources/views/user');

        rename($current_path,$new_path); 

        Log::info('Current theme changed');

    }

    /** View Folder change **/

    /** Layout User Folder change **/

    if(file_exists(base_path('resources/views/layouts/user'))) {

        Log::info('Change old theme as original theme ');

        // Change current theme as original theme 

        $current_path=base_path('resources/views/layouts/user');
        $new_path=base_path('resources/views/layouts/'.$old_theme);

        rename($current_path,$new_path);

        Log::info('Old theme changed');

    }

    if(file_exists(base_path('resources/views/layouts/'.$new_theme))) {

        Log::info('make the user requested theme as the current theme');

        // make the user requested theme as the current theme

        $current_path=base_path('resources/views/layouts/'.$new_theme);
        $new_path=base_path('resources/views/layouts/user');

        rename($current_path,$new_path); 

        Log::info('Current theme changed');

    }

    /** Layout User Folder change **/

    /** User file change **/

    if(file_exists(base_path('resources/views/layouts/user.blade.php'))) {

        Log::info('Change old theme as original theme ');

        // Change current theme as original theme 

        $current_path=base_path('resources/views/layouts/user.blade.php');
        $new_path=base_path('resources/views/layouts/'.$old_theme.'.blade.php');

        rename($current_path,$new_path);

        Log::info('Old theme changed');

    }

    if(file_exists(base_path('resources/views/layouts/'.$new_theme.'.blade.php'))) {

        Log::info('make the user requested theme as the current theme');

        // make the user requested theme as the current theme

        $current_path=base_path('resources/views/layouts/'.$new_theme.'.blade.php');
        $new_path=base_path('resources/views/layouts/user.blade.php');

        rename($current_path,$new_path); 

        Log::info('Current theme changed');

    }

    /** User file change **/

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

function get_slider_video() {
    return AdminVideo::where('is_home_slider' , 1)
            ->select('admin_videos.id as admin_video_id' , 'admin_videos.default_image',
                'admin_videos.title','admin_videos.trailer_video', 'admin_videos.video_type','admin_videos.video_upload_type')
            ->first();
}

function check_valid_url($file) {

    $video = get_video_end($file);

    // if(file_exists(public_path('uploads/'.$video))) {
        return 1;
    // } else {
    //     return 0;
    // }

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

function delete_install() {
    $controller = base_path('app/Http/Controllers/InstallationController.php');

    $public = base_path('public/install');
    
    $views = base_path('resources/views/install');

    if(is_dir($public)) {
        rmdir($public);
    }

    if(is_dir($views)) {
        rmdir($views);
    }

    if(file_exists($controller)) {
        unlink($controller);
    } 

    return true;
}

function get_banner_count() {
    return AdminVideo::where('is_banner' , 1)->count();
}

function get_expiry_days($id) {
    
    $data = UserPayment::where('user_id' , $id)->first();

    $days = 0;

    if($data) {
        $start_date = new \DateTime(date('Y-m-d h:i:s'));
        $end_date = new \DateTime($data->expiry_date);

        $time_interval = date_diff($start_date,$end_date);
        $days = $time_interval->days;
    }

    return $days;
}

function all_videos($web = NULL , $skip = 0) 
{

    $videos_query = AdminVideo::where('admin_videos.is_approved' , 1)
                ->where('admin_videos.status' , 1)
                ->leftJoin('categories' , 'admin_videos.category_id' , '=' , 'categories.id')
                ->leftJoin('sub_categories' , 'admin_videos.sub_category_id' , '=' , 'sub_categories.id')
                ->select(
                    'admin_videos.id as admin_video_id' , 
                    'admin_videos.default_image' , 
                    'admin_videos.ratings' , 
                    'admin_videos.watch_count' , 
                    'admin_videos.title' ,
                    'admin_videos.description',
                    'admin_videos.sub_category_id' , 
                    'admin_videos.category_id',
                    'categories.name as category_name',
                    'sub_categories.name as sub_category_name',
                    'admin_videos.duration',
                    DB::raw('DATE_FORMAT(admin_videos.publish_time , "%e %b %y") as publish_time')
                    )
                ->orderby('admin_videos.created_at' , 'desc');
    if (Auth::check()) {
        // Check any flagged videos are present
        $flagVideos = getFlagVideos(Auth::user()->id);

        if($flagVideos) {
            $videos_query->whereNotIn('admin_videos.id',$flagVideos);
        }
    }

    if($web) {
        $videos = $videos_query->paginate(20);
    } else {
        $videos = $videos_query->skip($skip)->take(20)->get();
    }

    return $videos;
} 

function get_trending_count() {

    $data = AdminVideo::where('watch_count' , '>' , 0)
                    ->where('admin_videos.is_approved' , 1)
                    ->where('admin_videos.status' , 1)
                    ->skip(0)->take(20)
                    ->count();

    return $data;

}

function get_wishlist_count($id) {
    
    $data = Wishlist::where('user_id' , $id)
                ->leftJoin('admin_videos' ,'wishlists.admin_video_id' , '=' , 'admin_videos.id')
                ->where('admin_videos.is_approved' , 1)
                ->where('admin_videos.status' , 1)
                ->where('wishlists.status' , 1)
                ->count();

    return $data;

}

function get_suggestion_count($id) {

    $data = Wishlist::where('user_id' , $id)
                ->leftJoin('admin_videos' ,'wishlists.admin_video_id' , '=' , 'admin_videos.id')
                ->where('admin_videos.is_approved' , 1)
                ->where('admin_videos.status' , 1)
                ->where('wishlists.status' , 1)
                ->count();

    return $data;

}

function get_recent_count($id) {

    $data = Wishlist::where('user_id' , $id)
                ->leftJoin('admin_videos' ,'wishlists.admin_video_id' , '=' , 'admin_videos.id')
                ->where('admin_videos.is_approved' , 1)
                ->where('admin_videos.status' , 1)
                ->where('wishlists.status' , 1)
                ->count();

    return $data;

}

function get_history_count($id) {

    $data = UserHistory::where('user_id' , $id)
                ->leftJoin('admin_videos' ,'user_histories.admin_video_id' , '=' , 'admin_videos.id')
                ->where('admin_videos.is_approved' , 1)
                ->where('admin_videos.status' , 1)
                ->count();

    return $data;

}


//this function convert string to UTC time zone

function convertTimeToUTCzone($str, $userTimezone, $format = 'Y-m-d H:i:s') {

    $new_str = new DateTime($str, new DateTimeZone($userTimezone));

    $new_str->setTimeZone(new DateTimeZone('UTC'));

    return $new_str->format( $format);
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
function getFlagVideos($id) {
    // Load Flag videos based on logged in user id
    $model = Flag::where('user_id', $id)
        ->leftJoin('admin_videos' , 'flags.video_id' , '=' , 'admin_videos.id')
        ->leftJoin('categories' , 'admin_videos.category_id' , '=' , 'categories.id')
        ->leftJoin('sub_categories' , 'admin_videos.sub_category_id' , '=' , 'sub_categories.id')
        ->where('admin_videos.is_approved' , 1)
        ->where('admin_videos.status' , 1)
        ->pluck('video_id')->toArray();
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
        ->leftJoin('admin_videos' , 'flags.video_id' , '=' , 'admin_videos.id')
        ->leftJoin('categories' , 'admin_videos.category_id' , '=' , 'categories.id')
        ->leftJoin('sub_categories' , 'admin_videos.sub_category_id' , '=' , 'sub_categories.id')
        ->where('admin_videos.is_approved' , 1)
        ->where('admin_videos.status' , 1)
        ->count();
    // Return array of id's
    return $model;
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
function watchFullVideo($user_id, $user_type, $video) {
    if ($user_type == 1) {
        if ($video->amount == 0) {
            return true;
        }else if($video->amount > 0 && ($video->type_of_user == PAID_USER || $video->type_of_user == BOTH_USERS)) {
            if ($video->type_of_subscription == ONE_TIME_PAYMENT) {
                // Load Payment view
                $paymentView = PayPerView::where('user_id', $user_id)->where('video_id', $video->admin_video_id)->where('status', 1)->first();
                if ($paymentView) {
                    return true;
                }
            }
        } else if($video->amount > 0 && $video->type_of_user == NORMAL_USER){
            return true;
        }
    } else {
        if($video->amount > 0 && ($video->type_of_user == NORMAL_USER || $video->type_of_user == BOTH_USERS)) {
            if ($video->type_of_subscription == ONE_TIME_PAYMENT) {
                // Load Payment view
                $paymentView = PayPerView::where('user_id', $user_id)->where('video_id', $video->admin_video_id)->where('status', 1)->first();
                if ($paymentView) {
                    return true;
                }
            }
        } 
    }
    return false;
}

/**
 * Function Name : total_video_revenue
 * To sum all the payment based on video subscription
 *
 * @return amount
 */
function total_video_revenue() {
    return PayPerView::where('status', 1)->sum('amount');
}

/**
 * Function Name : user_total_amount
 * To sum all the payment based on video subscription
 *
 * @return amount
 */
function user_total_amount() {
    return PayPerView::where('user_id', Auth::user()->id)->where('status', 1)->sum('amount');
}