<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Requests;

use App\Moderator;

use App\User;

use App\UserPayment;

use App\Admin;

use App\Category;

use App\SubCategory;

use App\SubCategoryImage;

use App\Genre;

use App\AdminVideo;

use App\AdminVideoImage;

use App\UserHistory;

use App\Wishlist;

use App\UserRating;

use App\Settings;

use App\Page;

use App\Helpers\Helper;

use Validator;

use Hash;

use Mail;

use DB;

use Auth;

use Redirect;

use Setting;

use Log;

use App\Jobs\NormalPushNotification;

define('USER', 0);

define('Moderator',1);

define('NONE', 0);


define('DEFAULT_TRUE', 1);
define('DEFAULT_FALSE', 0);

define('ADMIN', 'admin');
define('MODERATOR', 'moderator');

define('VIDEO_TYPE_UPLOAD', 1);
define('VIDEO_TYPE_YOUTUBE', 2);
define('VIDEO_TYPE_OTHER', 3);


define('VIDEO_UPLOAD_TYPE_s3', 1);
define('VIDEO_UPLOAD_TYPE_DIRECT', 2);


class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');  
    }

    public function login() {
        return view('admin.login')->withPage('admin-login')->with('sub_page','');
    }

    public function dashboard() {

        $admin = Admin::first();

        $admin->token = Helper::generate_token();
        $admin->token_expiry = Helper::generate_token_expiry();

        $admin->save();
        
        $user_count = User::count();
        $provider_count = Moderator::count();
        $video_count = AdminVideo::count();
        $trending = trending();
        $recent_videos = Helper::recently_added();

        $get_registers = get_register_count();
        $recent_users = get_recent_users();
        $total_revenue = total_revenue();

        $view = last_days(10);

        return view('admin.dashboard')->withPage('dashboard')
                    ->with('sub_page','')
                    ->with('user_count' , $user_count)
                    ->with('video_count' , $video_count)
                    ->with('provider_count' , $provider_count)
                    ->with('trending' , $trending)
                    ->with('get_registers' , $get_registers)
                    ->with('view' , $view)
                    ->with('total_revenue' , $total_revenue)
                    ->with('recent_users' , $recent_users)
                    ->with('recent_videos' , $recent_videos);
    }

    public function profile() {

        $admin = Admin::first();
        return view('admin.profile')->with('admin' , $admin)->withPage('profile')->with('sub_page','');
    }

    public function profile_process(Request $request) {

        $validator = Validator::make( $request->all(),array(
                'name' => 'max:255',
                'email' => 'email|max:255',
                'mobile' => 'digits_between:6,13',
                'address' => 'max:300',
                'id' => 'required|exists:admins,id'
            )
        );
        
        if($validator->fails()) {
            $error_messages = implode(',', $validator->messages()->all());
            return back()->with('flash_errors', $error_messages);
        } else {
            
            $admin = Admin::find($request->id);
            
            $admin->name = $request->has('name') ? $request->name : $admin->name;

            $admin->email = $request->has('email') ? $request->email : $admin->email;

            $admin->mobile = $request->has('mobile') ? $request->mobile : $admin->mobile;

            $admin->gender = $request->has('gender') ? $request->gender : $admin->gender;

            $admin->address = $request->has('address') ? $request->address : $admin->address;

            if($request->hasFile('picture')) {
                Helper::delete_picture($admin->picture);
                $admin->picture = Helper::normal_upload_picture($request->picture);
            }
                
            $admin->remember_token = Helper::generate_token();
            $admin->is_activated = 1;
            $admin->save();

            return back()->with('flash_success', Helper::tr('admin_not_profile'));
            
        }
    
    }

    public function change_password(Request $request) {

        $old_password = $request->old_password;
        $new_password = $request->password;
        $confirm_password = $request->confirm_password;
        
        $validator = Validator::make($request->all(), [              
                'password' => 'required|min:6',
                'old_password' => 'required',
                'confirm_password' => 'required|min:6',
                'id' => 'required|exists:admins,id'
            ]);

        if($validator->fails()) {

            $error_messages = implode(',',$validator->messages()->all());

            return back()->with('flash_errors', $error_messages);

        } else {

            $admin = Admin::find($request->id);

            if(Hash::check($old_password,$admin->password))
            {
                $admin->password = Hash::make($new_password);
                $admin->save();

                return back()->with('flash_success', "Password Changed successfully");
                
            } else {
                return back()->with('flash_error', "Pasword is mismatched");
            }
        }

        $response = response()->json($response_array,$response_code);

        return $response;
    }

    public function users() {

        $users = User::orderBy('created_at','desc')->get();

        return view('admin.users')->withPage('users')
                        ->with('users' , $users)
                        ->with('sub_page','view-user');
    }

    public function add_user() {
        return view('admin.add-user')->with('page' , 'users')->with('sub_page','add-user');
    }

    public function edit_user(Request $request) {

        $user = User::find($request->id);
        return view('admin.edit-user')->withUser($user)->with('sub_page','view-user')->with('page' , 'users');
    }

    public function add_user_process(Request $request) {

        if($request->id != '') {

            $validator = Validator::make( $request->all(), array(
                        'name' => 'required|max:255',
                        'email' => 'required|email|max:255',
                        'mobile' => 'required|digits_between:6,13',
                    )
                );
        
        } else {
            $validator = Validator::make( $request->all(), array(
                    'name' => 'required|max:255',
                    'email' => 'required|email|max:255|unique:users,email',
                    'mobile' => 'required|digits_between:6,13',
                )
            );
        
        }
       
        if($validator->fails())
        {
            $error_messages = implode(',', $validator->messages()->all());
            return back()->with('flash_errors', $error_messages);
        } else {

            if($request->id != '') {
                $user = User::find($request->id);
                $message = Helper::tr('admin_not_user');
            } else {
                //Add New User
                $user = new User;
                $new_password = time();
                $new_password .= rand();
                $new_password = sha1($new_password);
                $new_password = substr($new_password, 0, 8);
                $user->password = Hash::make($new_password);
                $message = tr('admin_add_user');
                $user->login_by = 'manual';
                $user->device_type = 'web';
            }

            $user->name = $request->has('name') ? $request->name : '';
            $user->email = $request->has('email') ? $request->email: '';
            $user->mobile = $request->has('mobile') ? $request->mobile : '';
            
            $user->token = Helper::generate_token();
            $user->token_expiry = Helper::generate_token_expiry();
            $user->is_activated = 1;                   

            if($request->id == ''){
                $email_data['name'] = $user->name;
                $email_data['password'] = $new_password;
                $email_data['email'] = $user->email;

                $subject = tr('user_welcome_title');
                $page = "emails.admin_user_welcome";
                $email = $user->email;
                Helper::send_email($page,$subject,$email,$email_data);
            }

            $user->save();

            if($user) {
                register_mobile('web');
                return back()->with('flash_success', $message);
            } else {
                return back()->with('flash_error', Helper::tr('admin_not_error'));
            }

        }
    
    }

    public function delete_user(Request $request) {

        if($user = User::where('id',$request->id)->first()->delete()) {

            return back()->with('flash_success',Helper::tr('admin_not_user_del'));

        } else {
            return back()->with('flash_error',Helper::tr('admin_not_error'));
        }
    }

    public function view_user($id) {

        if($user = User::find($id)) {

            return view('admin.user-details')
                        ->with('user' , $user)
                        ->withPage('users')
                        ->with('sub_page','users');

        } else {
            return back()->with('flash_error',Helper::tr('admin_not_error'));
        }
    }

    public function user_upgrade($id) {

        if($user = User::find($id)) {

            // Check the user is exists in moderators table

            if(!$moderator = Moderator::where('email' , $user->email)->first()) {

                $moderator_user = new Moderator;
                $moderator_user->name = $user->name;
                $moderator_user->email = $user->email;
                if($user->login_by == "manual") {
                    $moderator_user->password = $user->password;  
                    $new_password = "Please use you user login Pasword.";
                } else {
                    $new_password = time();
                    $new_password .= rand();
                    $new_password = sha1($new_password);
                    $new_password = substr($new_password, 0, 8);
                    $moderator_user->password = Hash::make($new_password);
                }

                $moderator_user->picture = $user->picture;
                $moderator_user->mobile = $user->mobile;
                $moderator_user->address = $user->address;
                $moderator_user->save();

                $email_data = array();

                $subject = Helper::tr('user_welcome_title');
                $page = "emails.moderator_welcome";
                $email = $user->email;
                $email_data['name'] = $moderator_user->name;
                $email_data['email'] = $moderator_user->email;
                $email_data['password'] = $new_password;

                Helper::send_email($page,$subject,$email,$email_data);

                $moderator = $moderator_user;

            }

            if($moderator) {
                $user->is_moderator = 1;
                $user->moderator_id = $moderator->id;
                $user->save();

                $moderator->is_activated = 1;
                $moderator->is_user = 1;
                $moderator->save();

                return back()->with('flash_warning',Helper::tr('admin_user_upgrade'));
            } else  {
                return back()->with('flash_error',Helper::tr('admin_not_error'));    
            }

        } else {
            return back()->with('flash_error',Helper::tr('admin_not_error'));
        }

    }

    public function user_upgrade_disable(Request $request) {

        if($moderator = Moderator::find($request->moderator_id)) {

            if($user = User::find($request->id)) {
                $user->is_moderator = 0;
                $user->save();
            }

            $moderator->is_activated = 0;

            $moderator->save();

            return back()->with('flash_success',Helper::tr('admin_user_upgrade_disable'));

        } else {

            return back()->with('flash_error',Helper::tr('admin_not_error'));
        }
    }

    public function view_history($id) {

        if($user = User::find($id)) {

            $user_history = UserHistory::where('user_id' , $id)
                            ->leftJoin('users' , 'user_histories.user_id' , '=' , 'users.id')
                            ->leftJoin('admin_videos' , 'user_histories.admin_video_id' , '=' , 'admin_videos.id')
                            ->select(
                                'users.name as username' , 
                                'users.id as user_id' , 
                                'user_histories.admin_video_id',
                                'user_histories.id as user_history_id',
                                'admin_videos.title',
                                'user_histories.created_at as date'
                                )
                            ->get();

            return view('admin.user-history')
                        ->with('data' , $user_history)
                        ->withPage('users')
                        ->with('sub_page','users');

        } else {
            return back()->with('flash_error',Helper::tr('admin_not_error'));
        }
    }

    public function delete_history($id) {

        if($user_history = UserHistory::find($id)) {

            $user_history = UserHistory::find($id)->delete();

            return back()->with('flash_success',Helper::tr('admin_not_history_del'));

        } else {
            return back()->with('flash_error',Helper::tr('admin_not_error'));
        }
    }

    public function view_wishlist($id) {

        if($user = User::find($id)) {

            $user_wishlist = Wishlist::where('user_id' , $id)
                            ->leftJoin('users' , 'wishlists.user_id' , '=' , 'users.id')
                            ->leftJoin('admin_videos' , 'wishlists.admin_video_id' , '=' , 'admin_videos.id')
                            ->select(
                                'users.name as username' , 
                                'users.id as user_id' , 
                                'wishlists.admin_video_id',
                                'wishlists.id as wishlist_id',
                                'admin_videos.title',
                                'wishlists.created_at as date'
                                )
                            ->get();

            return view('admin.user-wishlist')
                        ->with('data' , $user_wishlist)
                        ->withPage('users')
                        ->with('sub_page','users');

        } else {
            return back()->with('flash_error',Helper::tr('admin_not_error'));
        }
    }

    public function delete_wishlist($id) {

        if($user_wishlist = Wishlist::find($id)) {

            $user_wishlist = Wishlist::find($id)->delete();

            return back()->with('flash_success',Helper::tr('admin_not_wishlist_del'));

        } else {
            return back()->with('flash_error',Helper::tr('admin_not_error'));
        }
    }

    public function moderators() {

        $moderators = Moderator::orderBy('created_at','desc')->get();

        return view('admin.moderators')->with('moderators' , $moderators)->withPage('moderators')->with('sub_page','view-moderator');
    }

    public function add_moderator() {
        return view('admin.add-moderator')->with('page' ,'moderators')->with('sub_page' ,'add-moderator');
    }

    public function edit_moderator($id) {

        $moderator = Moderator::find($id);

        return view('admin.edit-moderator')->with('moderator' , $moderator)->with('page' ,'moderators')->with('sub_page' ,'edit-moderator');
    }

    public function add_moderator_process(Request $request) {

        if($request->id != '') {
            $validator = Validator::make( $request->all(), array(
                        'name' => 'required|max:255',
                        'email' => 'required|email|max:255',
                        'mobile' => 'required|digits_between:6,13',
                    )
                );
        } else {
            $validator = Validator::make( $request->all(), array(
                    'name' => 'required|max:255',
                    'email' => 'required|email|max:255|unique:moderators,email',
                    'mobile' => 'required|digits_between:6,13',
                )
            );
        
        }
       
        if($validator->fails()) {
            $error_messages = implode(',', $validator->messages()->all());
            return back()->with('flash_errors', $error_messages);

        } else {

            if($request->id != '') {
                $user = Moderator::find($request->id);
                $message = Helper::tr('admin_not_moderator');
            } else {
                $message = Helper::tr('admin_add_moderator');
                //Add New User
                $user = new Moderator;
                $new_password = time();
                $new_password .= rand();
                $new_password = sha1($new_password);
                $new_password = substr($new_password, 0, 8);
                $user->password = Hash::make($new_password);
                $user->is_activated = 1;

            }

            $user->name = $request->has('name') ? $request->name : '';
            $user->email = $request->has('email') ? $request->email: '';
            $user->mobile = $request->has('mobile') ? $request->mobile : '';
            
            $user->token = Helper::generate_token();
            $user->token_expiry = Helper::generate_token_expiry();
                               

            if($request->id == ''){
                $email_data['name'] = $user->name;
                $email_data['password'] = $new_password;
                $email_data['email'] = $user->email;

                $subject = Helper::tr('user_welcome_title');
                $page = "emails.moderator_welcome";
                $email = $user->email;
                Helper::send_email($page,$subject,$email,$email_data);
            }

            $user->save();

            if($user) {
                return back()->with('flash_success', $message);
            } else {
                return back()->with('flash_error', Helper::tr('admin_not_error'));
            }

        }
    
    }

    public function delete_moderator(Request $request) {

        if($moderator = Moderator::find($request->id)) {

            $moderator = Moderator::find($request->id)->delete();

        }
        if($moderator) {
            return back()->with('flash_success',Helper::tr('admin_not_moderator_del'));
        } else {
            return back()->with('flash_error',Helper::tr('admin_not_error'));
        }
    }

    public function moderator_approve(Request $request) {

        $moderator = Moderator::find($request->id);

        $moderator->is_activated = 1;

        $moderator->save();

        if($moderator->is_activated ==1) {
            $message = Helper::tr('admin_not_moderator_approve');
        } else {
            $message = Helper::tr('admin_not_moderator_decline');
        }

        return back()->with('flash_success', $message);
    }

    public function moderator_decline(Request $request) {
        
        $moderators = Moderator::orderBy('created_at' , 'asc')->get();

        $moderator = Moderator::find($request->id);

        $moderator->is_activated = 0;

        $moderator->save();

        if($moderator->is_activated == 1){
            $message = Helper::tr('admin_not_moderator_approve');
        } else {
            $message = Helper::tr('admin_not_moderator_decline');
        }
        return back()->with('flash_success', $message)->with('moderators',$moderators);
    }

    public function moderator_view_details($id) {

        if($moderator = Moderator::find($id)) {
            return view('admin.moderator-details')->with('moderator' , $moderator)
                        ->withPage('moderator')
                        ->with('sub_page','view-moderators');
        } else {
            return back()->with('flash_error',Helper::tr('admin_not_error'));
        }
    }

    public function categories() {

        $categories = Category::select('categories.id',
                            'categories.name' , 
                            'categories.picture',
                            'categories.is_series',
                            'categories.status',
                            'categories.is_approved',
                            'categories.created_by'
                        )
                        ->orderBy('categories.created_at', 'desc')
                        ->distinct('categories.id')
                        ->get();

        return view('admin.categories')->with('categories' , $categories)->withPage('categories')->with('sub_page','view-categories');
    }

    public function add_category() {
        return view('admin.add-category')->with('page' ,'categories')->with('sub_page' ,'add-category');
    }

    public function edit_category($id) {

        $category = Category::find($id);

        return view('admin.edit-category')->with('category' , $category)->with('page' ,'categories')->with('sub_page' ,'edit-category');
    }

    public function add_category_process(Request $request) {

        if($request->id != '') {
            $validator = Validator::make( $request->all(), array(
                        'name' => 'required|max:255',
                        'picture' => 'mimes:jpeg,jpg,bmp,png',
                    )
                );
        } else {
            $validator = Validator::make( $request->all(), array(
                    'name' => 'required|max:255',
                    'picture' => 'required|mimes:jpeg,jpg,bmp,png',
                )
            );
        
        }
       
        if($validator->fails()) {
            $error_messages = implode(',', $validator->messages()->all());
            return back()->with('flash_errors', $error_messages);

        } else {

            if($request->id != '') {
                $category = Category::find($request->id);
                $message = tr('admin_not_category');
                if($request->hasFile('picture')) {
                    Helper::delete_picture($category->picture);
                }
            } else {
                $message = tr('admin_add_category');
                //Add New User
                $category = new Category;
                $category->is_approved = DEFAULT_TRUE;
                $category->created_by = ADMIN;
            }

            $category->name = $request->has('name') ? $request->name : '';
            $category->is_series = $request->has('is_series') ? $request->is_series : 0;
            $category->status = 1;
            
            if($request->hasFile('picture') && $request->file('picture')->isValid()) {
                $category->picture = Helper::normal_upload_picture($request->file('picture'));
            }

            $category->save();

            if($category) {
                return back()->with('flash_success', $message);
            } else {
                return back()->with('flash_error', Helper::tr('admin_not_error'));
            }

        }
    
    }

    public function approve_category(Request $request) {

        $category = Category::find($request->id);

        $category->is_approved = $request->status;

        $category->save();

        $message = Helper::tr('admin_not_category_decline');

        if($category->is_approved == DEFAULT_TRUE){

            $message = Helper::tr('admin_not_category_approve');
        }

        return back()->with('flash_success', $message);
    
    }

    public function delete_category(Request $request) {
        
        $category = Category::where('id' , $id)->first()->delete();

        if($category) {
            return back()->with('flash_success',Helper::tr('admin_not_category_del'));
        } else {
            return back()->with('flash_error',Helper::tr('admin_not_error'));
        }
    }


    public function sub_categories($category_id) {

        $category = Category::find($category_id);

        $sub_categories = SubCategory::where('category_id' , $category_id)
                        ->select(
                                'sub_categories.id as id',
                                'sub_categories.name as sub_category_name',
                                'sub_categories.description',
                                'sub_categories.is_approved',
                                'sub_categories.created_by'
                                )
                        ->orderBy('sub_categories.created_at', 'desc')
                        ->get();

        return view('admin.sub-categories')->with('category' , $category)->with('data' , $sub_categories)->withPage('categories')->with('sub_page','view-categories');
    }

    public function add_sub_category($category_id) {

        $category = Category::find($category_id);
    
        return view('admin.add-sub-category')->with('category' , $category)->with('page' ,'categories')->with('sub_page' ,'add-category');
    }

    public function edit_sub_category(Request $request) {

        $category = Category::find($request->category_id);

        $sub_category = SubCategory::find($request->sub_category_id);

        $sub_category_images = SubCategoryImage::where('sub_category_id' , $request->sub_category_id)
                                    ->orderBy('position' , 'ASC')->get();

        $genres = Genre::where('sub_category_id' , $request->sub_category_id)
                        ->orderBy('position' , 'asc')
                        ->get();

        return view('admin.edit-sub-category')
                ->with('category' , $category)
                ->with('sub_category' , $sub_category)
                ->with('sub_category_images' , $sub_category_images)
                ->with('genres' , $genres)
                ->with('page' ,'categories')
                ->with('sub_page' ,'');
    }

    public function add_sub_category_process(Request $request) {

        if($request->id != '') {
            $validator = Validator::make( $request->all(), array(
                        'category_id' => 'required|integer|exists:categories,id',
                        'id' => 'required|integer|exists:sub_categories,id',
                        'name' => 'required|max:255',
                        'picture1' => 'mimes:jpeg,jpg,bmp,png',
                        'picture2' => 'mimes:jpeg,jpg,bmp,png',
                        'picture3' => 'mimes:jpeg,jpg,bmp,png',
                    )
                );
        } else {
            $validator = Validator::make( $request->all(), array(
                    'name' => 'required|max:255',
                    'description' => 'required|max:255',
                    'picture1' => 'required|mimes:jpeg,jpg,bmp,png',
                    'picture2' => 'required|mimes:jpeg,jpg,bmp,png',
                    'picture3' => 'required|mimes:jpeg,jpg,bmp,png',
                )
            );
        
        }
       
        if($validator->fails()) {
            $error_messages = implode(',', $validator->messages()->all());
            return back()->with('flash_errors', $error_messages);

        } else {

            if($request->id != '') {

                $sub_category = SubCategory::find($request->id);

                $message = tr('admin_not_sub_category');

                if($request->hasFile('picture1')) {
                    delete_picture($request->file('picture1'));
                }

                if($request->hasFile('picture2')) {
                    delete_picture($request->file('picture2'));
                }

                if($request->hasFile('picture3')) {
                    delete_picture($request->file('picture3'));
                }
            } else {
                $message = tr('admin_add_sub_category');
                //Add New User
                $sub_category = new SubCategory;

                $sub_category->is_approved = DEFAULT_TRUE;
                $sub_category->created_by = ADMIN;
            }

            $sub_category->category_id = $request->has('category_id') ? $request->category_id : '';
            
            if($request->has('name')) {
                $sub_category->name = $request->name;
            }

            if($request->has('description')) {
                $sub_category->description =  $request->description;   
            }

            $sub_category->save(); // Otherwise it will save empty values

            if($request->has('genre')) {

                foreach ($request->genre as $key => $genres) {
                    $genre = new Genre;
                    $genre->category_id = $request->category_id;
                    $genre->sub_category_id = $sub_category->id;
                    $genre->name = $genres;
                    $genre->status = DEFAULT_TRUE;
                    $genre->is_approved = DEFAULT_TRUE;
                    $genre->created_by = ADMIN;
                    $genre->position = $key+1;
                    $genre->save();
                }
            }
            
            if($request->hasFile('picture1')) {
                sub_category_image($request->file('picture1') , $sub_category->id,1);
            }

            if($request->hasFile('picture2')) {
                sub_category_image($request->file('picture2'), $sub_category->id , 2);
            }

            if($request->hasFile('picture3')) {
                sub_category_image($request->file('picture3'), $sub_category->id , 3);
            }

            if($sub_category) {
                return back()->with('flash_success', $message);
            } else {
                return back()->with('flash_error', Helper::tr('admin_not_error'));
            }

        }
    
    }

    public function approve_sub_category(Request $request) {

        $sub_category = SubCategory::find($request->id);

        $sub_category->is_approved = $request->status;

        $sub_category->save();

        $message = Helper::tr('admin_not_sub_category_decline');

        if($sub_category->is_approved == DEFAULT_TRUE){

            $message = Helper::tr('admin_not_sub_category_approve');
        }

        return back()->with('flash_success', $message);
    
    }

    public function delete_sub_category(Request $request) {

        $sub_category = SubCategory::where('id' , $id)->first()->delete();

        if($sub_category) {
            return back()->with('flash_success',Helper::tr('admin_not_category_del'));
        } else {
            return back()->with('flash_error',Helper::tr('admin_not_error'));
        }
    }

    public function save_genre(Request $request) {

        $validator = Validator::make( $request->all(), array(
                    'category_id' => 'required|integer|exists:categories,id',
                    'id' => 'required|integer|exists:sub_categories,id',
                    'genre' => 'required|max:255',
                )
            );

        if($validator->fails()) {
            $error_messages = implode(',', $validator->messages()->all());
            return back()->with('flash_errors', $error_messages);

        } else {
            // To order the position of the genres
            $position = 1;

            if($check_position = Genre::where('sub_category_id' , $request->id)->orderBy('position' , 'desc')->first()) {
                $position = $check_position->position +1;
            } 

            $genre = new Genre;
            $genre->category_id = $request->category_id;
            $genre->sub_category_id = $request->id;
            $genre->name = $request->genre;
            $genre->position = $position;
            $genre->status = DEFAULT_TRUE;
            $genre->is_approved = DEFAULT_TRUE;
            $genre->created_by = ADMIN;
            $genre->save();

            $message = tr('admin_add_genre');

            if($genre) {
                return back()->with('flash_success', $message);
            } else {
                return back()->with('flash_error', Helper::tr('admin_not_error'));
            }
        }
    
    }

    public function edit_genre(Request $request) {

    }

    public function approve_genre(Request $request) {

        $genre = Genre::find($request->id);

        $genre->is_approved = $request->status;

        $genre->save();

        $message = Helper::tr('admin_not_genre_decline');

        if($genre->is_approved == DEFAULT_TRUE){

            $message = Helper::tr('admin_not_genre_approve');
        }

        return back()->with('flash_success', $message);
    
    }

    public function view_genre(Request $request) {

        $validator = Validator::make($request->all() , [
                'id' => 'required|exists:genres,id'
            ]);

        if($validator->fails()) {
            $error_messages = implode(',', $validator->messages()->all());
            return back()->with('flash_errors', $error_messages);
        } else {
            $genres = Genre::where('genres.id' , $request->id)
                    ->leftJoin('categories' , 'genres.category_id' , '=' , 'categories.id')
                    ->leftJoin('sub_categories' , 'genres.sub_category_id' , '=' , 'sub_categories.id')
                    ->select('genres.id as genre_id' ,'genres.name as genre_name' , 
                             'genres.position' , 'genres.status' , 
                             'genres.is_approved' , 'genres.created_at as genre_date' ,
                             'genres.created_by',

                             'genres.category_id as category_id',
                             'genres.sub_category_id',
                             'categories.name as category_name',
                             'sub_categories.name as sub_category_name')
                    ->orderBy('genres.position' , 'asc')
                    ->get();

        return view('admin.view-genre')->with('genres' , $genres)
                    ->withPage('videos')
                    ->with('sub_page','view-videos');
        }
    }

    public function delete_genre(Request $request) {
        if($genre = Genre::where('id',$request->id)->first()->delete()) {

            return back()->with('flash_success',Helper::tr('admin_not_user_del'));

        } else {
            return back()->with('flash_error',Helper::tr('admin_not_error'));
        }
    }

    public function videos(Request $request) {

        $videos = AdminVideo::leftJoin('categories' , 'admin_videos.category_id' , '=' , 'categories.id')
                    ->leftJoin('sub_categories' , 'admin_videos.sub_category_id' , '=' , 'sub_categories.id')
                    ->leftJoin('genres' , 'admin_videos.genre_id' , '=' , 'genres.id')
                    ->select('admin_videos.id as video_id' ,'admin_videos.title' , 
                             'admin_videos.description' , 'admin_videos.ratings' , 
                             'admin_videos.reviews' , 'admin_videos.created_at as video_date' ,
                             'admin_videos.default_image',
                             'admin_videos.banner_image',

                             'admin_videos.category_id as category_id',
                             'admin_videos.sub_category_id',
                             'admin_videos.genre_id',
                             'admin_videos.is_home_slider',

                             'admin_videos.status','admin_videos.uploaded_by',
                             'admin_videos.edited_by','admin_videos.is_approved',

                             'categories.name as category_name' , 'sub_categories.name as sub_category_name' ,
                             'genres.name as genre_name')
                    ->orderBy('admin_videos.created_at' , 'desc')
                    ->get();

        return view('admin.videos')->with('videos' , $videos)
                    ->withPage('videos')
                    ->with('sub_page','view-videos');
   
    }

    public function add_video(Request $request) {

        $categories = Category::where('categories.is_approved' , 1)
                        ->select('categories.id as id' , 'categories.name' , 'categories.picture' ,
                            'categories.is_series' ,'categories.status' , 'categories.is_approved')
                        ->leftJoin('sub_categories' , 'categories.id' , '=' , 'sub_categories.category_id')
                        ->groupBy('sub_categories.category_id')
                        ->havingRaw("COUNT(sub_categories.id) > 0")
                        ->orderBy('categories.name' , 'asc')
                        ->get();

         return view('admin.video_upload')
                ->with('categories' , $categories)
                ->with('page' ,'videos')
                ->with('sub_page' ,'add-video');

    }

    public function edit_video(Request $request) {

        $categories = Category::orderBy('name' , 'asc')->get();

        $video = AdminVideo::where('admin_videos.id' , $request->id)
                    ->leftJoin('categories' , 'admin_videos.category_id' , '=' , 'categories.id')
                    ->leftJoin('sub_categories' , 'admin_videos.sub_category_id' , '=' , 'sub_categories.id')
                    ->leftJoin('genres' , 'admin_videos.genre_id' , '=' , 'genres.id')
                    ->select('admin_videos.id as video_id' ,'admin_videos.title' , 
                             'admin_videos.description' , 'admin_videos.ratings' , 
                             'admin_videos.reviews' , 'admin_videos.created_at as video_date' ,'admin_videos.is_banner','admin_videos.banner_image',
                             'admin_videos.video','admin_videos.trailer_video',
                             'admin_videos.video_type','admin_videos.video_upload_type',
                             'admin_videos.publish_time','admin_videos.duration',

                             'admin_videos.category_id as category_id',
                             'admin_videos.sub_category_id',
                             'admin_videos.genre_id',

                             'categories.name as category_name' , 'categories.is_series',
                             'sub_categories.name as sub_category_name' ,
                             'genres.name as genre_name')
                    ->orderBy('admin_videos.created_at' , 'desc')
                    ->first();

        $page = 'videos';
        $sub_page = 'add-video';

        if($video->is_banner == 1) {
            $page = 'banner-videos';
            $sub_page = 'banner-videos';
        }

         return view('admin.edit-video')
                ->with('categories' , $categories)
                ->with('video' ,$video)
                ->with('page' ,$page)
                ->with('sub_page' ,$sub_page);
    }

    public function add_video_process(Request $request) {

        if($request->has('video_type') && $request->video_type == VIDEO_TYPE_UPLOAD) {

            $video_validator = Validator::make( $request->all(), array(
                        'video'     => 'required|mimes:mkv,mp4,qt',
                        'trailer_video'  => 'required|mimes:mkv,mp4,qt',
                        )
                    );

            $video_link = $request->file('video');
            $trailer_video = $request->file('trailer_video');

        } else {

            $video_validator = Validator::make( $request->all(), array(
                        'other_video'     => 'required',
                        'other_trailer_video'  => 'required',
                        )
                    );

            $video_link = $request->other_video;
            $trailer_video = $request->other_trailer_video;

        }

        if($video_validator) {

             if($video_validator->fails()) {
                $error_messages = implode(',', $video_validator->messages()->all());
                return back()->with('flash_errors', $error_messages);

            }
        }

        $validator = Validator::make( $request->all(), array(
                    'title'         => 'required|max:255',
                    'description'   => 'required',
                    'category_id'   => 'required|integer|exists:categories,id',
                    'sub_category_id' => 'required|integer|exists:sub_categories,id,category_id,'.$request->category_id,
                    'genre'     => 'exists:genres,id,sub_category_id,'.$request->sub_category_id,
                    'default_image' => 'required|mimes:jpeg,jpg,bmp,png',
                    'banner_image' => 'mimes:jpeg,jpg,bmp,png',
                    'other_image1' => 'required|mimes:jpeg,jpg,bmp,png',
                    'other_image2' => 'required|mimes:jpeg,jpg,bmp,png',
                    'ratings' => 'required',
                    'reviews' => 'required',
                    'duration' => 'required',
                    )
                );

        if($validator->fails()) {
            $error_messages = implode(',', $validator->messages()->all());
            return back()->with('flash_errors', $error_messages);

        } else {

            $video = new AdminVideo;
            $video->title = $request->title;
            $video->description = $request->description;
            $video->category_id = $request->category_id;
            $video->sub_category_id = $request->sub_category_id;
            $video->genre_id = $request->has('genre_id') ? $request->genre_id : 0;
            if($request->has('duration')) {
                $video->duration = $request->duration;
            }

            if($request->video_type == VIDEO_TYPE_UPLOAD) {

                $video->video_upload_type = $request->video_upload_type;

                if($request->video_upload_type == VIDEO_UPLOAD_TYPE_s3) {

                    $video->video = Helper::upload_picture($video_link);
                    $video->trailer_video = Helper::upload_picture($trailer_video); 

                } else {
                    $video->video = Helper::normal_upload_picture($video_link);
                    $video->trailer_video = Helper::normal_upload_picture($trailer_video);  
                }                

            } elseif($request->video_type == VIDEO_TYPE_YOUTUBE) {

                $video->video = get_youtube_embed_link($video_link);
                $video->trailer_video = get_youtube_embed_link($trailer_video);
            } else {

                $video->video = $video;
                $video->trailer_video = $trailer_video;
            }

            $video->video_type = $request->video_type;


            $video->publish_time = date('Y-m-d H:i:s', strtotime($request->publish_time));
            
            $video->default_image = Helper::normal_upload_picture($request->file('default_image'));

            if($request->is_banner) {
                $video->is_banner = 1;
                $video->banner_image = Helper::normal_upload_picture($request->file('banner_image'));
            }

            $video->ratings = $request->ratings;
            $video->reviews = $request->reviews;

            $video->is_approved = DEFAULT_TRUE;  

            if(strtotime($request->publish_time) < strtotime(date('Y-m-d H:i:s'))) {
                $video->status = DEFAULT_TRUE;
            } else {
                $video->status = DEFAULT_FALSE;
            }
            
            $video->uploaded_by = ADMIN;

            $video->save();

            if($video) {

                Helper::upload_video_image($request->file('other_image1'),$video->id,2);

                Helper::upload_video_image($request->file('other_image2'),$video->id,3);
                if($video->is_banner)
                    return redirect(route('admin.banner-videos'));
                else
                    return redirect(route('admin.videos'));
            } else {
                return back()->with('flash_error', tr('admin_not_error'));
            }
        }
    
    }

    public function edit_video_process(Request $request) {

        $video = AdminVideo::find($request->id);

        $video_validator = array();

        if($request->has('video_type') && $request->video_type == VIDEO_TYPE_UPLOAD) {

            $video_validator = Validator::make( $request->all(), array(
                        'video'     => 'required|mimes:mkv,mp4,qt',
                        'trailer_video'  => 'required|mimes:mkv,mp4,qt',
                        )
                    );

            $video_link = $request->hasFile('video') ? $request->file('video') : array();

            $trailer_video = $request->hasFile('trailer_video') ? $request->file('trailer_video') : array();

        } elseif($request->has('video_type') && in_array($request->video_type , array(VIDEO_TYPE_YOUTUBE,VIDEO_TYPE_OTHER))) {

            $video_validator = Validator::make( $request->all(), array(
                        'other_video'     => 'required',
                        'other_trailer_video'  => 'required',
                        )
                    );

            $video_link = $request->has('other_video') ? $request->other_video : array();

            $trailer_video = $request->has('other_trailer_video') ? $request->other_trailer_video : array();
        }

        if($video_validator) {

             if($video_validator->fails()) {
                $error_messages = implode(',', $video_validator->messages()->all());
                return back()->with('flash_errors', $error_messages);

            }
        }

        $validator = Validator::make( $request->all(), array(
                    'id' => 'required|integer|exists:admin_videos,id',
                    'title'         => 'max:255',
                    'description'   => '',
                    'category_id'   => 'required|integer|exists:categories,id',
                    'sub_category_id' => 'required|integer|exists:sub_categories,id,category_id,'.$request->category_id,
                    'genre'     => 'exists:genres,id,sub_category_id,'.$request->sub_category_id,
                    // 'video'     => 'mimes:mkv,mp4,qt',
                    // 'trailer_video'  => 'mimes:mkv,mp4,qt',
                    'default_image' => 'mimes:jpeg,jpg,bmp,png',
                    'other_image1' => 'mimes:jpeg,jpg,bmp,png',
                    'other_image2' => 'mimes:jpeg,jpg,bmp,png',
                    'ratings' => 'required',
                    'reviews' => 'required',
                    )
                );

        if($validator->fails()) {
            $error_messages = implode(',', $validator->messages()->all());
            return back()->with('flash_errors', $error_messages);

        } else {

            $video->title = $request->has('title') ? $request->title : $video->title;

            $video->description = $request->has('description') ? $request->description : $video->description;

            $video->category_id = $request->has('category_id') ? $request->category_id : $video->category_id;

            $video->sub_category_id = $request->has('sub_category_id') ? $request->sub_category_id : $video->sub_category_id;

            $video->genre_id = $request->has('genre_id') ? $request->genre_id : $video->genre_id;

            if($request->has('duration')) {
                $video->duration = $request->duration;
            }

            if(strtotime($request->publish_time) < strtotime(date('Y-m-d H:i:s'))) {
                $video->status = DEFAULT_TRUE;
            } else {
                $video->status = DEFAULT_FALSE;
            }

            if($request->video_type == VIDEO_TYPE_UPLOAD && $video_link && $trailer_video) {

                // Check Previous Video Upload Type, to delete the videos

                if($video->video_upload_type == VIDEO_UPLOAD_TYPE_s3) {
                    Helper::s3_delete_picture($video->video);   
                    Helper::s3_delete_picture($video->trailer_video);  
                } else {
                    Helper::delete_picture($video->video);
                    Helper::delete_picture($video->trailer_video);
                }

                if($request->video_upload_type == VIDEO_UPLOAD_TYPE_s3) {
                    $video->video = Helper::upload_picture($video_link);
                    $video->trailer_video = Helper::upload_picture($trailer_video); 

                } else {
                    $video->video = Helper::normal_upload_picture($video_link);
                    $video->trailer_video = Helper::normal_upload_picture($trailer_video);  
                }                

            } elseif($request->video_type == VIDEO_TYPE_YOUTUBE && $video_link && $trailer_video) {

                $video->video = get_youtube_embed_link($video_link);
                $video->trailer_video = get_youtube_embed_link($trailer_video);
            } else {
                $video->video = $video_link ? $video_link : $video->video;
                $video->trailer_video = $trailer_video ? $trailer_video : $video->trailer_video;
            }

            if($request->hasFile('default_image')) {
                Helper::delete_picture($video->default_image);
                $video->default_image = Helper::normal_upload_picture($request->file('default_image'));
            }

            if($video->is_banner == 1) {
                if($request->hasFile('banner_image')) {
                    Helper::delete_picture($video->banner_image);
                    $video->banner_image = Helper::normal_upload_picture($request->file('banner_image'));
                }
            }

            $video->video_type = $request->video_type ? $request->video_type : $video->video_type;

            $video->video_upload_type = $request->video_upload_type ? $request->video_upload_type : $video->video_upload_type;

            $video->ratings = $request->has('ratings') ? $request->ratings : $video->ratings;

            $video->reviews = $request->has('reviews') ? $request->reviews : $video->reviews;

            $video->edited_by = ADMIN;

            // $video->is_approved = DEFAULT_TRUE;

            $video->save();

            if($video) {

                if($request->hasFile('other_image1')) {
                    Helper::upload_video_image($request->file('other_image1'),$video->id,2);  
                }

                if($request->hasFile('other_image2')) {
                   Helper::upload_video_image($request->file('other_image2'),$video->id,3); 
                }

                return redirect(route('admin.videos'));

            } else {
                return back()->with('flash_error', tr('admin_not_error'));
            }
        }
    
    }

    public function view_video(Request $request) {

        $validator = Validator::make($request->all() , [
                'id' => 'required|exists:admin_videos,id'
            ]);

        if($validator->fails()) {
            $error_messages = implode(',', $validator->messages()->all());
            return back()->with('flash_errors', $error_messages);
        } else {
            $videos = AdminVideo::where('admin_videos.id' , $request->id)
                    ->leftJoin('categories' , 'admin_videos.category_id' , '=' , 'categories.id')
                    ->leftJoin('sub_categories' , 'admin_videos.sub_category_id' , '=' , 'sub_categories.id')
                    ->leftJoin('genres' , 'admin_videos.genre_id' , '=' , 'genres.id')
                    ->select('admin_videos.id as video_id' ,'admin_videos.title' , 
                             'admin_videos.description' , 'admin_videos.ratings' , 
                             'admin_videos.reviews' , 'admin_videos.created_at as video_date' ,
                             'admin_videos.video','admin_videos.trailer_video',
                             'admin_videos.default_image','admin_videos.banner_image','admin_videos.is_banner','admin_videos.video_type',
                             'admin_videos.video_upload_type',

                             'admin_videos.category_id as category_id',
                             'admin_videos.sub_category_id',
                             'admin_videos.genre_id',

                             'categories.name as category_name' , 'sub_categories.name as sub_category_name' ,
                             'genres.name as genre_name')
                    ->orderBy('admin_videos.created_at' , 'desc')
                    ->first();

            $admin_video_images = AdminVideoImage::where('admin_video_id' , $request->id)
                                ->orderBy('is_default' , 'desc')
                                ->get();

        $page = 'videos';
        $sub_page = 'add-video';

        if($videos->is_banner == 1) {
            $page = 'banner-videos';
            $sub_page = 'banner-videos';
        }

        return view('admin.view-video')->with('video' , $videos)
                    ->with('video_images' , $admin_video_images)
                    ->withPage($page)
                    ->with('sub_page',$sub_page);
        }
    }

    public function approve_video($id) {

        $video = AdminVideo::find($id);

        $video->is_approved = DEFAULT_TRUE;

        $video->save();

        if($video->is_approved == DEFAULT_TRUE)
        {
            $message = Helper::tr('admin_not_video_approve');
        }
        else
        {
            $message = Helper::tr('admin_not_video_decline');
        }
        return back()->with('flash_success', $message);
    }

    public function decline_video($id) {
        
        $video = AdminVideo::find($id);

        $video->is_approved = DEFAULT_FALSE;

        $video->save();

        if($video->is_approved == DEFAULT_TRUE){
            $message = Helper::tr('admin_not_video_approve');
        } else {
            $message = Helper::tr('admin_not_video_decline');
        }

        return back()->with('flash_success', $message);
    }

    public function delete_video($id) {

        $video = AdminVideo::where('id' , $id)->first()->delete(); 

        return back()->with('flash_success', 'Video deleted successfully');
    }

    public function slider_video($id) {

        $video = AdminVideo::where('is_home_slider' , 1 )->update(['is_home_slider' => 0]); 

        $video = AdminVideo::where('id' , $id)->update(['is_home_slider' => 1] );

        return back()->with('flash_success', tr('slider_success'));
    
    }

    public function banner_videos(Request $request) {

        $videos = AdminVideo::leftJoin('categories' , 'admin_videos.category_id' , '=' , 'categories.id')
                    ->leftJoin('sub_categories' , 'admin_videos.sub_category_id' , '=' , 'sub_categories.id')
                    ->leftJoin('genres' , 'admin_videos.genre_id' , '=' , 'genres.id')
                    ->where('admin_videos.is_banner' , 1 )
                    ->select('admin_videos.id as video_id' ,'admin_videos.title' , 
                             'admin_videos.description' , 'admin_videos.ratings' , 
                             'admin_videos.reviews' , 'admin_videos.created_at as video_date' ,
                             'admin_videos.default_image',
                             'admin_videos.banner_image',

                             'admin_videos.category_id as category_id',
                             'admin_videos.sub_category_id',
                             'admin_videos.genre_id',
                             'admin_videos.is_home_slider',

                             'admin_videos.status','admin_videos.uploaded_by',
                             'admin_videos.edited_by','admin_videos.is_approved',

                             'categories.name as category_name' , 'sub_categories.name as sub_category_name' ,
                             'genres.name as genre_name')
                    ->orderBy('admin_videos.created_at' , 'desc')
                    ->get();

        return view('admin.banner-videos')->with('videos' , $videos)
                    ->withPage('banner-videos')
                    ->with('sub_page','view-banner-videos');
   
    }

    public function add_banner_video(Request $request) {

        $categories = Category::where('categories.is_approved' , 1)
                        ->select('categories.id as id' , 'categories.name' , 'categories.picture' ,
                            'categories.is_series' ,'categories.status' , 'categories.is_approved')
                        ->leftJoin('sub_categories' , 'categories.id' , '=' , 'sub_categories.category_id')
                        ->groupBy('sub_categories.category_id')
                        ->havingRaw("COUNT(sub_categories.id) > 0")
                        ->orderBy('categories.name' , 'asc')
                        ->get();

        return view('admin.banner-video-upload')
                ->with('categories' , $categories)
                ->with('page' ,'banner-videos')
                ->with('sub_page' ,'add-banner-video');

    }

    public function change_banner_video($id) {

        $video = AdminVideo::find($id);

        $video->is_banner = 0 ;

        $video->save();

        $message = tr('change_banner_video_success');
       
        return back()->with('flash_success', $message);
    }

    public function user_ratings() {
            
            $user_reviews = UserRating::leftJoin('users', 'user_ratings.user_id', '=', 'users.id')
                ->select('user_ratings.id as rating_id', 'user_ratings.rating', 
                         'user_ratings.comment', 
                         'users.first_name as user_first_name', 
                         'users.last_name as user_last_name', 
                         'users.id as user_id', 'user_ratings.created_at')
                ->orderBy('user_ratings.id', 'ASC')
                ->get();
            return view('admin.reviews')->with('name', 'User')->with('reviews', $user_reviews);
    }

    public function delete_user_ratings(Request $request) {

        $user = UserRating::find($request->id)->delete();
        return back()->with('flash_success', tr('admin_not_ur_del'));
    }

    public function user_payments() {
        $payments = UserPayment::orderBy('created_at' , 'desc')->paginate(20);

        return view('admin.user-payments')->with('data' , $payments)->withPage('user-payments')->with('sub_page',''); 
    }

    public function settings() {
        $settings = array();

        return view('admin.settings')->with('settings' , $settings)->withPage('settings')->with('sub_page',''); 
    }

    public function payment_settings() {
        $settings = array();

        return view('admin.payment-settings')->with('settings' , $settings)->withPage('payment-settings')->with('sub_page',''); 
    }

    public function theme_settings() {
        $settings = array();

        $settings[] =  Setting::get('theme');

        if(Setting::get('theme')!= 'default') {
            $settings[] = 'default';
        }

        if(Setting::get('theme')!= 'streamtube') {
            $settings[] = 'streamtube';
        }

        if(Setting::get('theme')!= 'teen') {
            $settings[] = 'teen';
        }

        return view('admin.theme-settings')->with('settings' , $settings)->withPage('theme-settings')->with('sub_page',''); 
    }

    public function settings_process(Request $request) {

        $settings = Settings::all();

        foreach ($settings as $setting) {

            $key = $setting->key;
           
            $temp_setting = Settings::find($setting->id);

                if($temp_setting->key == 'site_icon'){
                    $site_icon = $request->file('site_icon');
                    if($site_icon == null) {
                        $icon = $temp_setting->value;
                    } else {

                        if($temp_setting->value) {
                            Helper::delete_picture($temp_setting->value);
                        }

                        $icon = Helper::normal_upload_picture($site_icon);
                    }

                    $temp_setting->value = $icon;
                    
                } else if($temp_setting->key == 'site_logo'){
                    $picture = $request->file('site_logo');
                    if($picture == null){
                        $logo = $temp_setting->value;
                    } else {
                        if($temp_setting->value) {
                            Helper::delete_picture($temp_setting->value);
                        }
                        $logo = Helper::normal_upload_picture($picture);
                    }

                    $temp_setting->value = $logo;

                } else if($request->$key!=''){
                 
                    if($key == "theme") {
                        if($request->has($key)) {
                            // change_theme($setting->value , $request->$key);
                        }
                    }

                    $temp_setting->value = $request->$key;
                }
            $temp_setting->save();
        }
        
        return back()->with('setting', $settings)->with('flash_success','Settings Updated Successfully');
    
    }

    public function help() {
        return view('admin.help')->withPage('help')->with('sub_page' , "");
    }

    public function viewPages()
    {
        $all_pages = Page::all();

        return view('admin.viewpages')->with('page',"view_pages")->with('sub_page',"view_pages")->with('view_pages',$all_pages);
    }

    public function add_page()
    {
        $pages = Page::all();
        return view('admin.add-page')->with('page' , 'pages')->with('sub_page',"add_page")->with('view_pages',$pages);
    }

    public function editPage($id)
    {
        $page = Page::find($id);
        if($page)
        {
            return view('admin.editPage')->withPage('viewpage')->with('sub_page',"view_pages")->with('pages',$page);
        }
        else
        {
            return back()->with('flash_error',"Something went wrong");
        }
    }

    public function pagesProcess(Request $request)
    {
        $type = $request->type;
        $id = $request->id;
        $heading = $request->heading;
        $description = $request->description;

        $validator = Validator::make(array(
            'heading' => $request->heading,
            'description' => $request->description),
            array('heading' => 'required',
                'description' => 'required'));
        if($validator->fails())
        {
            $error = $validator->messages()->all();
            return back()->with('flash_errors',$error);
        }
        else
        {
            if($request->has('id'))
            {
                $pages = Page::find($id);
                $pages->heading = $heading;
                $pages->description = $description;
                $pages->save();
            }
            else
            {
                $check_page = Page::where('type',$type)->first();
                if(!$check_page)
                {
                    $pages = new Page;
                    $pages->type = $type;
                    $pages->heading = $heading;
                    $pages->description = $description;
                    $pages->save();
                }
                else
                {
                    return back()->with('flash_error',"Page already added");
                }
            }
            if($pages)
            {
                return back()->with('flash_success',"Page added successfully");
            }
            else
            {
                return back()->with('flash_error',"Something went wrong");
            }
        }
    }

    public function deletePage($id)
    {
        $page = Page::where('id',$id)->delete();

        if($page)
        {
            return back()->with('flash_success',"Page deleted successfully");
        }
        else
        {
            return back()->with('flash_error',"Something went wrong");
        }
    }

    public function custom_push() {

        return view('admin.push')->with('title' , "Custom Push")->with('page' , "custom-push");

    }

    public function custom_push_process(Request $request) {

        $validator = Validator::make(
            array(
                'message' => $request->message
                ),
            array(
                'message' => 'required'
                )
        );

        if($validator->fails()) {

            $error = $validator->messages()->all();

            return back()->with('flash_errors',$error);

        } else {

            $message = $request->message;
            $title = Setting::get('site_name');
            $message = $message;
            
            \Log::info($message);

            $id = 'all';

            Helper::send_notification($id,$title,$message);

            // $this->dispatch( new NormalPushNotification($id,$title, $message));

            return back()->with('flash_success' , "Push Notifications sent successfully");
        }
    }
}
