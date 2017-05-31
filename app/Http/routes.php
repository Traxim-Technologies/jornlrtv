<?php


use Illuminate\Support\Facades\Redis;


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Report Video type

Route::get('redis/test',function(){
    $redis = Redis::connection();    
    $views=$redis->incr('view');
    dd($views);
});

// REDEEMS

if(!defined('REDEEM_OPTION_ENABLED')) define('REDEEM_OPTION_ENABLED', 1);
if(!defined('REDEEM_OPTION_DISABLED')) define('REDEEM_OPTION_DISABLED', 0);

// Redeeem Request Status

if(!defined('REDEEM_REQUEST_SENT')) define('REDEEM_REQUEST_SENT', 0);
if(!defined('REDEEM_REQUEST_PROCESSING')) define('REDEEM_REQUEST_PROCESSING', 1);
if(!defined('REDEEM_REQUEST_PAID')) define('REDEEM_REQUEST_PAID', 2);
if(!defined('REDEEM_REQUEST_CANCEL')) define('REDEEM_REQUEST_CANCEL', 3);

// Ad Types

if(!defined('PRE_AD')) define('PRE_AD', 1);
if(!defined('POST_AD')) define('POST_AD', 2);
if(!defined('BETWEEN_AD')) define('BETWEEN_AD', 3);

if(!defined('REPORT_VIDEO_KEY')) define('REPORT_VIDEO_KEY', 'REPORT_VIDEO');
if (!defined('IMAGE_RESOLUTIONS_KEY')) define('IMAGE_RESOLUTIONS_KEY', 'IMAGE_RESOLUTIONS');
if (!defined('VIDEO_RESOLUTIONS_KEY')) define('VIDEO_RESOLUTIONS_KEY', 'VIDEO_RESOLUTIONS');

// User Type
if(!defined('NORMAL_USER')) define('NORMAL_USER', 1);
if(!defined('PAID_USER')) define('PAID_USER', 2);
if(!defined('BOTH_USERS')) define('BOTH_USERS', 3);

// Subscription Type
if(!defined('ONE_TIME_PAYMENT')) define('ONE_TIME_PAYMENT', 1);
if(!defined('RECURRING_PAYMENT')) define('RECURRING_PAYMENT', 2);

// REQUEST STATE

if(!defined('REQUEST_STEP_1')) define('REQUEST_STEP_1', 1);
if(!defined('REQUEST_STEP_2')) define('REQUEST_STEP_2', 2);
if(!defined('REQUEST_STEP_3')) define('REQUEST_STEP_3', 3);
if(!defined('REQUEST_STEP_FINAL')) define('REQUEST_STEP_FINAL', 4);


if(!defined('USER')) define('USER', 0);

if(!defined('Moderator')) define('Moderator',1);

if(!defined('NONE')) define('NONE', 0);

if(!defined('MAIN_VIDEO')) define('MAIN_VIDEO', 1);
if(!defined('TRAILER_VIDEO')) define('TRAILER_VIDEO', 2);


if(!defined('DEFAULT_TRUE')) define('DEFAULT_TRUE', 1);
if(!defined('DEFAULT_FALSE')) define('DEFAULT_FALSE', 0);

if(!defined('ADMIN')) define('ADMIN', 'admin');
if(!defined('MODERATOR')) define('MODERATOR', 'moderator');

if(!defined('VIDEO_TYPE_UPLOAD')) define('VIDEO_TYPE_UPLOAD', 1);
if(!defined('VIDEO_TYPE_YOUTUBE')) define('VIDEO_TYPE_YOUTUBE', 2);
if(!defined('VIDEO_TYPE_OTHER')) define('VIDEO_TYPE_OTHER', 3);


if(!defined('VIDEO_UPLOAD_TYPE_s3')) define('VIDEO_UPLOAD_TYPE_s3', 1);
if(!defined('VIDEO_UPLOAD_TYPE_DIRECT')) define('VIDEO_UPLOAD_TYPE_DIRECT', 2);

if(!defined('NO_INSTALL')) define('NO_INSTALL' , 0);

if(!defined('SYSTEM_CHECK')) define('SYSTEM_CHECK' , 1);

if(!defined('THEME_CHECK')) define('THEME_CHECK' , 2);

if(!defined('INSTALL_COMPLETE')) define('INSTALL_COMPLETE' , 3);


if(!defined('ADMIN')) define('ADMIN', 'admin');
if(!defined('MODERATOR')) define('MODERATOR', 'moderator');

// Payment Constants
if(!defined('COD')) define('COD',   'cod');
if(!defined('PAYPAL')) define('PAYPAL', 'paypal');
if(!defined('CARD')) define('CARD',  'card');


if(!defined('RATINGS')) define('RATINGS', '0,1,2,3,4,5');


if(!defined('PUBLISH_NOW')) define('PUBLISH_NOW', 1);
if(!defined('PUBLISH_LATER')) define('PUBLISH_LATER', 2);

if(!defined('DEVICE_ANDROID')) define('DEVICE_ANDROID', 'android');
if(!defined('DEVICE_IOS')) define('DEVICE_IOS', 'ios');

if(!defined('WISHLIST_EMPTY')) define('WISHLIST_EMPTY' , 0);
if(!defined('WISHLIST_ADDED')) define('WISHLIST_ADDED' , 1);
if(!defined('WISHLIST_REMOVED')) define('WISHLIST_REMOVED' , 2);

if(!defined('RECENTLY_ADDED')) define('RECENTLY_ADDED' , 'recent');
if(!defined('TRENDING')) define('TRENDING' , 'trending');
if(!defined('SUGGESTIONS')) define('SUGGESTIONS' , 'suggestion');
if(!defined('WISHLIST')) define('WISHLIST' , 'wishlist');
if(!defined('WATCHLIST')) define('WATCHLIST' , 'watchlist');
if(!defined('BANNER')) define('BANNER' , 'banner');

if(!defined('WEB')) define('WEB' , 1);

// Route::get('/ui' , 'ApplicationController@ui')->name('ui');

Route::get('/subscriptions' , 'ApplicationController@subscriptions')->name('subscriptions.index');

Route::get('/subscriptions/view' , 'ApplicationController@subscription_view')->name('subscriptions.view');

Route::get('/videos/create' , 'ApplicationController@video_create')->name('videos.create');

// Route::get('/channels/create' , 'ApplicationController@channel_create')->name('channels.create');

// Route::get('/channels/view' , 'ApplicationController@channel_view')->name('channels.view');

// Route::get('/channels/index' , 'ApplicationController@channel_index')->name('channels.index');


Route::get('/test' , 'ApplicationController@test')->name('test');

Route::post('/test' , 'ApplicationController@test')->name('test');

Route::get('/email/verification' , 'ApplicationController@email_verify')->name('email.verify');

// Installation

Route::get('/install/theme', 'InstallationController@install')->name('installTheme');

Route::get('/system/check', 'InstallationController@system_check_process')->name('system-check');

Route::post('/install/theme', 'InstallationController@theme_check_process')->name('install.theme');

Route::post('/install/settings', 'InstallationController@settings_process')->name('install.settings');

// Elastic Search Test

Route::get('/addIndex', 'ApplicationController@addIndex')->name('addIndex');

Route::get('/addAll', 'ApplicationController@addAllVideoToEs')->name('addAll');

// CRON

Route::get('/publish/video', 'ApplicationController@cron_publish_video')->name('publish');

Route::get('/notification/payment', 'ApplicationController@send_notification_user_payment')->name('notification.user.payment');

Route::get('/payment/expiry', 'ApplicationController@user_payment_expiry')->name('user.payment.expiry');

// Static Pages

Route::get('/privacy', 'UserApiController@privacy')->name('user.privacy');

Route::get('/terms', 'UserApiController@terms')->name('user.terms');

Route::get('/contact', 'UserController@contact')->name('user.contact');

Route::get('/privacy_policy', 'ApplicationController@privacy')->name('user.privacy_policy');

Route::get('/terms', 'ApplicationController@terms')->name('user.terms-condition');

Route::get('/about', 'ApplicationController@about')->name('user.about');

// Video upload 

Route::post('select/sub_category' , 'ApplicationController@select_sub_category')->name('select.sub_category');

Route::post('select/genre' , 'ApplicationController@select_genre')->name('select.genre');

Route::get('admin/control', 'ApplicationController@admin_control')->name('control');

Route::post('admin/control', 'ApplicationController@save_admin_control')->name('admin.save.control');


Route::group(['prefix' => 'admin' , 'as' => 'admin.'], function(){

    Route::get('login', 'Auth\AdminAuthController@showLoginForm')->name('login');

    Route::post('login', 'Auth\AdminAuthController@login')->name('login.post');

    Route::get('logout', 'Auth\AdminAuthController@logout')->name('logout');

    // Registration Routes...

    Route::get('register', 'Auth\AdminAuthController@showRegistrationForm');

    Route::post('register', 'Auth\AdminAuthController@register');

    // Password Reset Routes...
    Route::get('password/reset/{token?}', 'Auth\AdminPasswordController@showResetForm');

    Route::post('password/email', 'Auth\AdminPasswordController@sendResetLinkEmail');

    Route::post('password/reset', 'Auth\AdminPasswordController@reset');

    Route::get('/', 'AdminController@dashboard')->name('dashboard');

    Route::get('/profile', 'AdminController@profile')->name('profile');

	Route::post('/profile/save', 'AdminController@profile_process')->name('save.profile');

	Route::post('/change/password', 'AdminController@change_password')->name('change.password');

    // users

    Route::get('/user/channels/{id}', 'AdminController@user_channels')->name('users.channels');

    Route::get('/users', 'AdminController@users')->name('users');

    Route::get('/add/user', 'AdminController@add_user')->name('add.user');

    Route::get('/edit/user', 'AdminController@edit_user')->name('edit.user');

    Route::post('/add/user', 'AdminController@add_user_process')->name('save.user');

    Route::get('/delete/user', 'AdminController@delete_user')->name('delete.user');

    Route::get('/view/user/{id}', 'AdminController@view_user')->name('view.user');

    Route::get('/user/upgrade/{id}', 'AdminController@user_upgrade')->name('user.upgrade');

    Route::any('/upgrade/disable', 'AdminController@user_upgrade_disable')->name('user.upgrade.disable');

    Route::get('/redeems/{id?}', 'AdminController@user_redeem_requests')->name('users.redeems');

    Route::post('/redeems/pay', 'AdminController@user_redeem_pay')->name('users.redeem.pay');

    // User History - admin

    Route::get('/user/history/{id}', 'AdminController@view_history')->name('user.history');

    Route::get('/delete/history/{id}', 'AdminController@delete_history')->name('delete.history');
    
    // User Wishlist - admin

    Route::get('/user/wishlist/{id}', 'AdminController@view_wishlist')->name('user.wishlist');

    Route::get('/delete/wishlist/{id}', 'AdminController@delete_wishlist')->name('delete.wishlist');

    // Spam Videos
    Route::get('/spam-videos', 'AdminController@spam_videos')->name('spam-videos');

    Route::get('/view-users/{id}', 'AdminController@view_users')->name('view-users');
    
    // Categories

    Route::get('/channels', 'AdminController@channels')->name('channels');

    Route::get('/add/channel', 'AdminController@add_channel')->name('add.channel');

    Route::get('/edit/channel/{id}', 'AdminController@edit_channel')->name('edit.channel');

    Route::post('/add/channel', 'AdminController@add_channel_process')->name('save.channel');

    Route::get('/delete/channel', 'AdminController@delete_channel')->name('delete.channel');

    Route::get('/view/channel/{id}', 'AdminController@view_channel')->name('view.channel');

    Route::get('/channel/approve', 'AdminController@approve_channel')->name('channel.approve');

    Route::get('/channel/videos/{id?}', 'AdminController@channel_videos')->name('channel.videos');

    

    // Videos

    Route::get('/videos', 'AdminController@videos')->name('videos');

    Route::get('/ad_videos', 'AdminController@ad_videos')->name('ad_videos');

    Route::get('/add/video', 'AdminController@add_video')->name('add.video');

    Route::get('/edit/video/{id}', 'AdminController@edit_video')->name('edit.video');

    Route::post('/edit/video/process', 'AdminController@edit_video_process')->name('save.edit.video');

    Route::get('/view/video', 'AdminController@view_video')->name('view.video');

    Route::post('save_video', 'AdminController@video_save')->name('video_save');

    Route::post('save_default_img', 'AdminController@save_default_img')->name('save_default_img');

    Route::post('upload_video_image', 'AdminController@upload_video_image')->name('upload_video_image');

    Route::post('/save_video_payment/{id}', 'AdminController@save_video_payment')->name('save.video-payment');

    Route::get('/delete/video/{id}', 'AdminController@delete_video')->name('delete.video');

    Route::get('/video/approve/{id}', 'AdminController@approve_video')->name('video.approve');

    Route::get('/video/publish-video/{id}', 'AdminController@publish_video')->name('video.publish-video');

    Route::get('/video/decline/{id}', 'AdminController@decline_video')->name('video.decline');

    Route::get('get_images/{id}', 'AdminController@get_images')->name('get_images');

    // Slider Videos

    Route::get('/slider/video/{id}', 'AdminController@slider_video')->name('slider.video');

    // Banner Videos

    Route::get('/banner/videos', 'AdminController@banner_videos')->name('banner.videos');

    Route::get('/add/banner/video', 'AdminController@add_banner_video')->name('add.banner.video');

    Route::get('/change/banner/video/{id}', 'AdminController@change_banner_video')->name('change.video');
    
    // User Payment details
    Route::get('user/payments' , 'AdminController@user_payments')->name('user.payments');

    Route::get('user/video-payments' , 'AdminController@video_payments')->name('user.video-payments');

    Route::get('/remove_payper_view/{id}', 'AdminController@remove_payper_view')->name('remove_pay_per_view');

    // Settings

    Route::get('settings' , 'AdminController@settings')->name('settings');

    Route::post('save_common_settings' , 'AdminController@save_common_settings')->name('save.common-settings');

    Route::get('payment/settings' , 'AdminController@payment_settings')->name('payment.settings');
    
    Route::post('settings' , 'AdminController@settings_process')->name('save.settings');

    Route::get('settings/email' , 'AdminController@email_settings')->name('email.settings');

    Route::post('settings/email' , 'AdminController@email_settings_process')->name('email.settings.save');

    Route::get('help' , 'AdminController@help')->name('help');

    // Pages

    Route::get('/pages', 'AdminController@pages')->name('pages.index');

    Route::get('/pages/edit/{id}', 'AdminController@page_edit')->name('pages.edit');

    Route::get('/pages/create', 'AdminController@page_create')->name('pages.create');

    Route::post('/pages/create', 'AdminController@page_save')->name('pages.save');

    Route::get('/pages/delete/{id}', 'AdminController@page_delete')->name('pages.delete');


    // Custom Push

    Route::get('/custom/push', 'AdminController@custom_push')->name('push');

    Route::post('/custom/push', 'AdminController@custom_push_process')->name('send.push');


    // Ads

    Route::get('ad_create','AdminController@ad_create')->name('ad_create');

    Route::get('ad_edit','AdminController@ad_edit')->name('ad_edit');

    Route::post('save_ad','AdminController@save_ad')->name('save_ad');

    Route::get('ad_index','AdminController@ad_index')->name('ad_index');

    Route::get('ad_status','AdminController@ad_status')->name('ad_status');

    Route::get('ad_delete','AdminController@ad_delete')->name('ad_delete');

    Route::get('ad_view','AdminController@ad_view')->name('ad_view');

    Route::get('assign_ad', 'AdminController@assign_ad')->name('assign_ad');

    Route::post('assign_ad', 'AdminController@save_assign_ad')->name('assign_ads');




    Route::get('ads_create/{video_tape_id}','AdminController@ads_create')->name('ads_create');

    Route::post('save_ads','AdminController@save_ads')->name('save_ads');

    Route::get('ads_edit/{id}','AdminController@ads_edit')->name('ads_edit');

    Route::get('ads_delete','AdminController@ads_delete')->name('ads_delete');

    Route::get('ads_index','AdminController@ads_index')->name('ads_index');

    Route::get('ads_view','AdminController@ads_view')->name('ads_view');

    Route::post('add_between_ads', 'AdminController@add_between_ads')->name('add.between_ads');


    // Subscriptions

    Route::get('users/subscription/payments/{id?}' , 'AdminController@user_subscription_payments')->name('user.subscription.payments');

    Route::get('/user_subscriptions/{id}', 'AdminController@user_subscriptions')->name('subscriptions.plans');

    Route::get('/subscription/save/{s_id}/u_id/{u_id}', 'AdminController@user_subscription_save')->name('subscription.save');


    Route::get('/subscriptions', 'AdminController@subscriptions')->name('subscriptions.index');

    Route::get('/subscriptions/create', 'AdminController@subscription_create')->name('subscriptions.create');

    Route::get('/subscriptions/edit/{id}', 'AdminController@subscription_edit')->name('subscriptions.edit');

    Route::post('/subscriptions/create', 'AdminController@subscription_save')->name('subscriptions.save');

    Route::get('/subscriptions/delete/{id}', 'AdminController@subscription_delete')->name('subscriptions.delete');

    Route::get('/subscriptions/view/{id}', 'AdminController@subscription_view')->name('subscriptions.view');

    Route::get('/subscriptions/status/{id}', 'AdminController@subscription_status')->name('subscriptions.status');

});

Route::get('/', 'UserController@index')->name('user.dashboard');

Route::get('/single', 'UserController@single_video');

Route::get('/user/searchall' , 'ApplicationController@search_video')->name('search');

Route::any('/user/search' , 'ApplicationController@search_all')->name('search-all');

// Categories and single video 

Route::get('categories', 'UserController@all_categories')->name('user.categories');

Route::get('channel/{id}', 'UserController@channel_videos')->name('user.channel');


Route::get('video/{id}', 'UserController@single_video')->name('user.single');


// Social Login

Route::post('/social', array('as' => 'SocialLogin' , 'uses' => 'SocialAuthController@redirect'));

Route::get('/callback/{provider}', 'SocialAuthController@callback');


Route::group(['as' => 'user.'], function(){

    Route::get('login', 'Auth\AuthController@showLoginForm')->name('login.form');

    Route::post('login', 'Auth\AuthController@login')->name('login.post');

    Route::get('logout', 'Auth\AuthController@logout')->name('logout');

    // Registration Routes...
    Route::get('register', 'Auth\AuthController@showRegistrationForm')->name('register.form');

    Route::post('register', 'Auth\AuthController@register')->name('register.post');

    // Password Reset Routes...
    Route::get('password/reset/{token?}', 'Auth\PasswordController@showResetForm');

    Route::post('password/email', 'Auth\PasswordController@sendResetLinkEmail');

    Route::post('password/reset', 'Auth\PasswordController@reset');

    

    Route::get('profile', 'UserController@profile')->name('profile');

    Route::get('update/profile', 'UserController@update_profile')->name('update.profile');

    Route::post('update/profile', 'UserController@profile_save')->name('profile.save');

    Route::get('/profile/password', 'UserController@profile_change_password')->name('change.password');

    Route::post('/profile/password', 'UserController@profile_save_password')->name('profile.password');

    // Delete Account

    Route::get('/delete/account', 'UserController@delete_account')->name('delete.account');

    Route::post('/delete/account', 'UserController@delete_account_process')->name('delete.account.process');


    Route::get('history', 'UserController@history')->name('history');

    Route::get('deleteHistory', 'UserController@delete_history')->name('delete.history');

    Route::post('addHistory', 'UserController@add_history')->name('add.history');

    // Report Spam Video

    Route::post('markSpamVideo', 'UserController@save_report_video')->name('add.spam_video');

    Route::get('unMarkSpamVideo/{id}', 'UserController@remove_report_video')->name('remove.report_video');

    Route::get('spamVideos', 'UserController@spam_videos')->name('spam-videos');


    // Wishlist

    Route::post('addWishlist', 'UserController@add_wishlist')->name('add.wishlist');

    Route::get('deleteWishlist', 'UserController@delete_wishlist')->name('delete.wishlist');

    Route::get('wishlist', 'UserController@wishlist')->name('wishlist');

    // Comments

    Route::post('addComment', 'UserController@add_comment')->name('add.comment');

    Route::get('comments', 'UserController@comments')->name('comments');
    
    // Paypal Payment
    Route::get('/paypal/{id}','PaypalController@pay')->name('paypal');

    Route::get('/user/payment/status','PaypalController@getPaymentStatus')->name('paypalstatus');

    Route::get('/trending', 'UserController@trending')->name('trending');

    Route::get('/user_subscriptions', 'UserController@subscriptions')->name('subscriptions');

    Route::get('/subscription/save/{s_id}/u_id/{u_id}', 'UserController@user_subscription_save')->name('subscription.save');

    // Channels

    Route::get('create_channel', 'UserController@channel_create')->name('create_channel');

    Route::post('save_channel', 'UserController@save_channel')->name('save_channel');

    Route::get('edit_channel/{id}', 'UserController@channel_edit')->name('channel_edit');

    Route::get('delete_channel', 'UserController@channel_delete')->name('delete.channel');

    // Video Upload

    Route::get('upload_video', 'UserController@video_upload')->name('video_upload');

    Route::post('video_save', 'UserController@video_save')->name('video_save');

    Route::post('save_default_img', 'UserController@save_default_img')->name('save_default_img');

    Route::post('upload_video_image', 'UserController@upload_video_image')->name('upload_video_image');

    Route::post('ad_request', 'UserController@ad_request')->name('ad_request');

    Route::get('/delete/video/{id}', 'UserController@video_delete')->name('delete.video');

    Route::get('/edit_video/{id}', 'UserController@video_edit')->name('edit.video');

    Route::get('get_images/{id}', 'UserController@get_images')->name('get_images');

    // Redeems

    Route::get('redeems/', 'UserController@redeems')->name('redeems');

    Route::get('send/redeem', 'UserController@send_redeem_request')->name('redeems.send.request');

    Route::get('redeem/request/cancel/{id?}', 'UserController@redeem_request_cancel')->name('redeems.request.cancel');

});

Route::group(['prefix' => 'userApi'], function(){

    Route::post('/register','UserApiController@register');
    
    Route::post('/login','UserApiController@login');

    Route::get('/userDetails','UserApiController@user_details');

    Route::get('/userDetails','UserApiController@user_details');

    Route::post('/updateProfile', 'UserApiController@update_profile');

    Route::post('/forgotpassword', 'UserApiController@forgot_password');

    Route::post('/changePassword', 'UserApiController@change_password');

    Route::get('/tokenRenew', 'UserApiController@token_renew');

    Route::post('/deleteAccount', 'UserApiController@delete_account');

    Route::post('/settings', 'UserApiController@settings');


    // Categories And SubCategories

    Route::post('/categories' , 'UserApiController@get_categories');

    Route::post('/subCategories' , 'UserApiController@get_sub_categories');


    // Videos and home

    Route::post('/home' , 'UserApiController@home');
    
    Route::post('/common' , 'UserApiController@common');

    Route::post('/categoryVideos' , 'UserApiController@get_category_videos');

    Route::post('/subCategoryVideos' , 'UserApiController@get_sub_category_videos');

    Route::post('/singleVideo' , 'UserApiController@single_video');

    Route::post('/searchVideo' , 'UserApiController@search_video')->name('search-video');

    Route::post('/test_search_video' , 'UserApiController@test_search_video');


    // Rating and Reviews

    Route::post('/userRating', 'UserApiController@user_rating');

    // Wish List

    Route::post('/addWishlist', 'UserApiController@add_wishlist');

    Route::post('/getWishlist', 'UserApiController@get_wishlist');

    Route::post('/deleteWishlist', 'UserApiController@delete_wishlist');

    // History

    Route::post('/addHistory', 'UserApiController@add_history');

    Route::post('getHistory', 'UserApiController@get_history');

    Route::post('/deleteHistory', 'UserApiController@delete_history');

    Route::get('/clearHistory', 'UserApiController@clear_history');

    // Index

    Route::post('/index', 'UserApiController@index');

    Route::post('/redeems', 'UserApiController@redeems');

    Route::post('/send_redeem_request', 'UserApiController@send_redeem_request');

});
