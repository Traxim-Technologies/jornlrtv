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


Route::get('/clear-cache', function() {

    $exitCode = Artisan::call('config:cache');

    return back();

})->name('clear-cache');


// Generral configuration routes 

Route::post('project/configurations' , 'ApplicationController@configuration_site');



// UI

Route::get('/video_notification' , 'SampleController@video_notification');

Route::get('/upload_videoUI' , 'SampleController@upload_video');

// Unused Sample Routes

Route::post('angelo/in-app-purchase' , 'SampleController@angelo_in_app_purchase');

Route::get('/addIndex', 'ApplicationController@addIndex')->name('addIndex');

Route::get('/addAll', 'ApplicationController@addAllVideoToEs')->name('addAll');

Route::post('select/sub_category' , 'ApplicationController@select_sub_category')->name('select.sub_category');

Route::post('select/genre' , 'ApplicationController@select_genre')->name('select.genre');


// Chat Message save


Route::get('message/save' , 'ApplicationController@message_save')->name('message.save');


// Cron jobs

Route::get('/automatic/renewal', 'ApplicationController@automatic_renewal')->name('automatic.renewal');

// Application Routes

Route::get('/generate/index' , 'ApplicationController@generate_index');

Route::get('/payment/failure' , 'ApplicationController@payment_failure')->name('payment.failure');

Route::get('/email/verification' , 'ApplicationController@email_verify')->name('email.verify');

// Installation

Route::get('/install/configure', 'InstallationController@install')->name('installTheme');

Route::get('/system/check', 'InstallationController@system_check_process')->name('system-check');

Route::post('/install/theme', 'InstallationController@theme_check_process')->name('install.theme');

Route::post('/install/settings', 'InstallationController@settings_process')->name('install.settings');

Route::get('/user_session_language/{lang}', 'ApplicationController@set_session_language')->name('user_session_language');

Route::get('admin-control', 'ApplicationController@admin_control')->name('control');

Route::post('admin-control', 'ApplicationController@save_admin_control')->name('admin.save.control');

Route::get('/user/searchall' , 'ApplicationController@search_video')->name('search');

Route::any('/user/search' , 'ApplicationController@search_all')->name('search-all');

// Social Login

Route::post('/social', array('as' => 'SocialLogin' , 'uses' => 'SocialAuthController@redirect'));

Route::get('/callback/{provider}', 'SocialAuthController@callback');

// Embed Links

Route::get('/embed', 'ApplicationController@embed_video')->name('embed_video');

// Admin to users login

Route::get('/master/login', 'UserController@master_login')->name('master.login');

// CRON

Route::get('/publish/video', 'ApplicationController@cron_publish_video')->name('publish');

Route::get('/notification/payment', 'ApplicationController@send_notification_user_payment')->name('notification.user.payment');

Route::get('/payment/expiry', 'ApplicationController@user_payment_expiry')->name('user.payment.expiry');

Route::get('cron_delete_video', 'ApplicationController@cron_delete_video');
    

// Static Pages

Route::get('/privacy', 'UserApiController@privacy')->name('user.privacy');

Route::get('/help', 'UserApiController@help')->name('user.help');

Route::get('/terms_condition', 'UserApiController@terms')->name('user.terms');


Route::get('/about', 'ApplicationController@about')->name('user.about');

Route::get('/privacy_policy', 'ApplicationController@privacy')->name('user.privacy_policy');

Route::get('/terms', 'ApplicationController@terms')->name('user.terms-condition');

Route::get('page_view/{id}', 'UserController@page_view')->name('page_view');


Route::group(['prefix' => 'admin' , 'as' => 'admin.'], function(){

    Route::get('login', 'Auth\AdminAuthController@showLoginForm')->name('login');

    Route::post('login', 'Auth\AdminAuthController@login')->name('login.post');

    Route::get('logout', 'Auth\AdminAuthController@logout')->name('logout');

    Route::get('secure', 'AdminController@secure')->name('secure');

    // Registration Routes...

    Route::get('register', 'Auth\AdminAuthController@showRegistrationForm');

    Route::post('register', 'Auth\AdminAuthController@register');

    // Password Reset Routes...
    Route::get('password/reset/{token?}', 'Auth\AdminPasswordController@showResetForm');

    Route::post('password/email', 'Auth\AdminPasswordController@sendResetLinkEmail');

    Route::post('password/reset', 'Auth\AdminPasswordController@reset');

    // Admin Dashboard 
    
    Route::get('/', 'AdminController@dashboard')->name('dashboard');


    Route::get('/payment/failure' , 'ApplicationController@payment_failure')->name('payment.failure');

    // User CRUD Operations

    Route::get('/users/list', 'AdminController@users_list')->name('users');

    Route::get('/users/create', 'AdminController@users_create')->name('users.create');

    Route::get('/users/edit', 'AdminController@users_edit')->name('users.edit');
    
    Route::post('/users/save', 'AdminController@users_save')->name('users.save');

    Route::get('/users/view/{id}', 'AdminController@users_view')->name('users.view');

    Route::get('/users/delete', 'AdminController@users_delete')->name('users.delete');

    Route::get('/users/status','AdminController@users_status_change')->name('users.status');

    Route::get('/users/verify/{id?}', 'AdminController@users_verify_status')->name('users.verify');

    Route::get('/users/channels/{id}', 'AdminController@users_channels')->name('users.channels');

    Route::get('/users/history/{id}', 'AdminController@users_history')->name('users.history');
    Route::get('users/type/{id}','AdminController@users_type_list')->name('users.type');

    Route::get('/users/history/delete/{id}', 'AdminController@users_history_delete')->name('users.history.delete');

    Route::get('/users/wishlist/{id}', 'AdminController@users_wishlist')->name('users.wishlist');

    Route::get('/users/wishlist/delete/{id}', 'AdminController@users_wishlist_delete')->name('users.wishlist.delete');

    //User Subscriptions

    Route::get('/users/subscriptions/{id}', 'AdminController@users_subscriptions')->name('users.subscriptions.plans');

    Route::get('/users/subscriptions/save/{s_id}/u_id/{u_id}', 'AdminController@users_subscription_save')->name('users.subscription.save');


    // Channel CRUD Operations

    Route::get('/channels', 'AdminController@channels')->name('channels');

    Route::get('/channels/create', 'AdminController@channels_create')->name('channels.create');

    Route::get('/channels/edit/{id}', 'AdminController@channels_edit')->name('channels.edit');

    Route::get('/channels/videos/{id?}', 'AdminController@channels_videos')->name('channels.videos');

    Route::post('/channels/save', 'AdminController@channels_save')->name('channels.save');

    Route::get('/channels/delete', 'AdminController@channels_delete')->name('channels.delete');

    Route::get('/channels/view/{id}', 'AdminController@channels_view')->name('channels.view');

    Route::get('/channels/status/change', 'AdminController@channels_status_change')->name('channel.approve');

    Route::get('/channels/subscribers', 'AdminController@channels_subscribers')->name('channels.subscribers');


    // Videos CRUD Operations

    Route::get('/videos/create', 'AdminController@videos_create')->name('videos.create');

    Route::get('/videos/edit/{id}', 'AdminController@videos_edit')->name('videos.edit');

    Route::post('/videos/save', 'AdminController@videos_save')->name('videos.save');

    Route::get('/videos/images/{id}', 'AdminController@videos_images')->name('videos.images');

    Route::post('/videos/upload/image', 'AdminController@videos_upload_image')->name('videos.upload_image');

    Route::post('videos/save/default_img', 'AdminController@videos_save_default_img')->name('videos.save.default_img');

    Route::get('/videos/list/{id?}', 'AdminController@videos_list')->name('videos.list');

    Route::get('/videos/view', 'AdminController@videos_view')->name('videos.view');

    Route::post('/videos/set-ppv/{id}', 'AdminController@videos_set_ppv')->name('videos.set-ppv');

    Route::get('/videos/delete/{id}', 'AdminController@videos_delete')->name('videos.delete');

    Route::get('/videos/status/{id}', 'AdminController@videos_status')->name('videos.status');

    Route::get('/videos/publish/{id}', 'AdminController@videos_publish')->name('videos.publish');

    Route::get('/videos/remove-ppv/{id}', 'AdminController@videos_remove_ppv')->name('videos.remove-ppv');

    Route::get('/videos/wishlist/{id}', 'AdminController@videos_wishlist')->name('videos.wishlist');

    // Video compression status

    Route::get('/videos/compression/complete','AdminController@videos_compression_complete')->name('compress.status');


    // Videos

    Route::get('/live_videos', 'AdminController@live_videos')->name('live-videos.index');

    Route::get('/live-videos/list', 'AdminController@live_videos_history')->name('live-videos.history');

    Route::get('/live-videos/view/{id}', 'AdminController@live_videos_view')->name('live-videos.view');


    // Banner Videos

    Route::get('/banner/videos/set/{id}', 'AdminController@banner_videos_set')->name('banner.videos.set');

    Route::get('/banner/videos', 'AdminController@banner_videos')->name('banner.videos');

    Route::get('/banner/videos/create', 'AdminController@banner_videos_create')->name('banner.videos.create');

    Route::get('/banner/videos/remove/{id}', 'AdminController@banner_videos_remove')->name('banner.videos.remove');


    // Spam Videos

    Route::get('/spam-videos', 'AdminController@spam_videos')->name('spam-videos');

    Route::get('/spam-videos/user-reports/{id}', 'AdminController@spam_videos_user_reports')->name('spam-videos.user-reports');

    Route::get('/spam/per-user-reports/{id}', 'AdminController@spam_videos_each_user_reports')->name('spam-videos.per-user-reports');

    Route::get('/unspam-video/{id}', 'AdminController@spam_videos_unspam')->name('spam-videos.unspam-video');


    // Reviews

    Route::get('/reviews', 'AdminController@user_reviews')->name('reviews');

    Route::get('/reviews/delete', 'AdminController@user_reviews_delete')->name('reviews.delete');


    // Ads

    Route::get('ads-details/create','AdminController@ads_details_create')->name('ads-details.create');

    Route::get('ads-details/edit','AdminController@ads_details_edit')->name('ads-details.edit');

    Route::post('ads-details/save','AdminController@ads_details_save')->name('ads-details.save');

    Route::get('ads-details/index','AdminController@ads_details_index')->name('ads-details.index');

    Route::get('ads-details/view','AdminController@ads_details_view')->name('ads-details.view');

    Route::get('ads-details/status','AdminController@ads_details_status')->name('ads-details.status');

    Route::get('ads-details/delete','AdminController@ads_details_delete')->name('ads-details.delete');

    Route::get('ads-details/ad-status/{id?}', 'AdminController@ads_details_ad_status_change')->name('ads-details.ad-status-change');

    // Assign Ads

    Route::post('video-ads/assign/ads', 'AdminController@video_ads_assign_ad')->name('video-ads.assign.ads');

    Route::get('videos/assign-ad', 'AdminController@video_assign_ad')->name('videos.assign_ad');


    // Exports tables

    Route::get('/users/export/', 'AdminExportController@users_export')->name('users.export');

    Route::get('/channels/export/', 'AdminExportController@channels_export')->name('channels.export');

    Route::get('/videos/export/', 'AdminExportController@videos_export')->name('videos.export');


    Route::get('/live-videos/export/', 'AdminExportController@livevideos_export')->name('live-videos.export');

    Route::get('/subscription/payment/export/', 'AdminExportController@subscription_export')->name('subscription.export');

    Route::get('/payperview/payment/export/', 'AdminExportController@payperview_export')->name('payperview.export');

     Route::get('/live-video/payment/export/', 'AdminExportController@livevideo_payment_export')->name('livevideo-payment.export');

    


    // Video Ads

    Route::get('/video-ads/list', 'AdminController@video_ads_list')->name('video_ads.list');

    Route::get('video-ads/edit/{id}','AdminController@video_ads_edit')->name('video_ads.edit');

    Route::get('video-ads/create','AdminController@video_ads_create')->name('video_ads.create');

    Route::get('video-ads/view','AdminController@video_ads_view')->name('video-ads.view');


    Route::get('video-ads/delete','AdminController@video_ads_delete')->name('video-ads.delete');

    Route::post('video-ads/save','AdminController@video_ads_save')->name('video-ads.save');

    Route::post('video-ads/inter-ads', 'AdminController@video_ads_inter_ads')->name('video-ads.inter-ads');




    // Banner Ads

    Route::get('banner-ads/create','AdminController@banner_ads_create')->name('banner-ads.create');

    Route::get('banner-ads/edit','AdminController@banner_ads_edit')->name('banner-ads.edit');

    Route::post('banner-ads/save','AdminController@banner_ads_save')->name('banner-ads.save');

    Route::get('banner-ads/list','AdminController@banner_ads')->name('banner-ads.list');

    Route::get('banner-ads/status/{id}','AdminController@banner_ads_status')->name('banner-ads.status');

    Route::get('banner-ads/delete','AdminController@banner_ads_delete')->name('banner-ads.delete');

    Route::get('banner-ads/view','AdminController@banner_ads_view')->name('banner-ads.view');

    Route::post('banner-ads/position','AdminController@banner_ads_position')->name('banner-ads.position');


    // Subscriptions

    Route::get('/subscriptions', 'AdminController@subscriptions')->name('subscriptions.index');

    Route::get('/subscriptions/create', 'AdminController@subscription_create')->name('subscriptions.create');

    Route::get('/subscriptions/edit/{id}', 'AdminController@subscription_edit')->name('subscriptions.edit');

    Route::post('/subscriptions/create', 'AdminController@subscription_save')->name('subscriptions.save');

    Route::get('/subscriptions/delete/{id}', 'AdminController@subscription_delete')->name('subscriptions.delete');

    Route::get('/subscriptions/view/{id}', 'AdminController@subscription_view')->name('subscriptions.view');

    Route::get('/subscriptions/status/{id}', 'AdminController@subscription_status')->name('subscriptions.status');


    // Coupons

    // Get the add coupon forms
    Route::get('/coupons/add','AdminController@coupon_create')->name('add.coupons');

    // Get the edit coupon forms
    Route::get('/coupons/edit/{id}','AdminController@coupon_edit')->name('edit.coupons');

    // Save the coupon details
    Route::post('/coupons/save','AdminController@coupon_save')->name('save.coupon');

    // Get the list of coupon details
    Route::get('/coupons/list','AdminController@coupon_index')->name('coupon.list');

    //Get the particular coupon details
    Route::get('/coupons/view/{id}','AdminController@coupon_view')->name('coupon.view');

    // Delete the coupon details
    Route::get('/coupons/delete/{id}','AdminController@coupon_delete')->name('delete.coupon');

    //Coupon approve and decline status
    Route::get('/coupon/status','AdminController@coupon_status_change')->name('coupon.status');

    //Redeems

    Route::get('/redeems/{id?}', 'AdminController@user_redeem_requests')->name('users.redeems');

    Route::post('/redeems/pay', 'AdminController@user_redeem_pay')->name('users.redeem.pay');

    // Payment details

    Route::get('revenues/dashboard' , 'AdminController@revenues')->name('revenues.dashboard');
    
    Route::get('revenues/ppv-payments' , 'AdminController@ppv_payments')->name('revenues.ppv_payments');

    Route::get('/revenues/subscription/payments/{id?}' , 'AdminController@subscription_payments')->name('revenues.subscription-payments');

    Route::get('live-video/payments', 'AdminController@live_video_payments')->name('live.videos.payments');


    // Settings

    
    Route::get('settings' , 'AdminController@settings')->name('settings');

    Route::post('save_common_settings' , 'AdminController@save_common_settings')->name('save.common-settings');
    
    Route::post('settings' , 'AdminController@settings_process')->name('save.settings');

    Route::post('settings/email' , 'AdminController@email_settings_process')->name('email.settings.save');

    // Languages

    Route::get('/languages/index', 'LanguageController@languages_index')->name('languages.index'); 

    Route::get('/languages/download', 'LanguageController@languages_download')->name('languages.download'); 

    Route::get('/languages/create', 'LanguageController@languages_create')->name('languages.create');
    
    Route::get('/languages/edit/{id}', 'LanguageController@languages_edit')->name('languages.edit');

    Route::get('/languages/status/{id}', 'LanguageController@languages_status')->name('languages.status');   

    Route::post('/languages/save', 'LanguageController@languages_save')->name('languages.save');

    Route::get('/languages/delete/{id}', 'LanguageController@languages_delete')->name('languages.delete');

    Route::get('/languages/set_default_language/{name}', 'LanguageController@set_default_language')->name('languages.set_default_language');


    // Custom Push

    Route::get('/custom/push', 'AdminController@custom_push')->name('push');

    Route::post('/custom/push', 'AdminController@custom_push_process')->name('send.push');


    // Pages

    Route::get('/pages', 'AdminController@pages')->name('pages.index');

    Route::get('/pages/edit/{id}', 'AdminController@pages_edit')->name('pages.edit');

    Route::get('/pages/view/{id}', 'AdminController@pages_view')->name('pages.view');

    Route::get('/pages/create', 'AdminController@pages_create')->name('pages.create');

    Route::post('/pages/create', 'AdminController@pages_save')->name('pages.save');

    Route::get('/pages/delete/{id}', 'AdminController@pages_delete')->name('pages.delete');


    // Admin profile pages

    Route::get('/profile', 'AdminController@profile')->name('profile');

    Route::post('/profile/save', 'AdminController@profile_process')->name('save.profile');

    Route::post('/change/password', 'AdminController@change_password')->name('change.password');

    // Admin Help, account pages

    Route::get('help' , 'AdminController@help')->name('help');

     // Tags

    Route::get('/tags', 'AdminController@tags')->name('tags');

    Route::post('/save/tag', 'AdminController@save_tag')->name('save.tag');

    Route::get('/delete/tag', 'AdminController@delete_tag')->name('tags.delete');

    Route::get('/status/tag', 'AdminController@tag_status')->name('tags.status');


    // Categories CRUD operations

    Route::post('/categories/save', 'AdminController@categories_save')->name('categories.save');

    Route::get('/categories/delete', 'AdminController@categories_delete')->name('categories.delete');

    Route::get('/categories/status', 'AdminController@categories_status')->name('categories.status');

    Route::get('/categories/create', 'AdminController@categories_create')->name('categories.create');

    Route::get('/categories/edit', 'AdminController@categories_edit')->name('categories.edit');

    Route::get('/categories/list', 'AdminController@categories_list')->name('categories.list');

    Route::get('categories/videos', 'AdminController@categories_videos')->name('categories.videos');

    Route::get('categories/view', 'AdminController@categories_view')->name('categories.view');

    Route::get('categories/channels', 'AdminController@categories_channels')->name('categories.channels');

    Route::get('/tags/videos/{id?}', 'AdminController@tags_videos')->name('tags.videos');

    // Custom Live Videos

    Route::get('custom/live/videos', 'AdminController@custom_live_videos')->name('custom.live');

    Route::get('custom/live/create', 'AdminController@custom_live_videos_create')->name('custom.live.create');

    Route::get('custom/live/edit', 'AdminController@custom_live_videos_edit')->name('custom.live.edit');

    Route::post('custom/live/save', 'AdminController@custom_live_videos_save')->name('custom.live.save');

    Route::get('custom/live/delete', 'AdminController@custom_live_videos_delete')->name('custom.live.delete');

    Route::get('custom/live/view/{id}', 'AdminController@custom_live_videos_view')->name('custom.live.view');

    Route::get('custom/live/change-status', 'AdminController@custom_live_videos_change_status')->name('custom.live.change_status');

        // Cancel Subscription

    Route::post('/user/subscription/pause', 'AdminController@user_subscription_pause')->name('cancel.subscription');

    Route::get('/user/subscription/enable', 'AdminController@user_subscription_enable')->name('enable.subscription');

    // Subscribers

    Route::get('automatic/subscribers', 'AdminController@automatic_subscribers')->name('automatic.subscribers');

    Route::get('cancelled/subscribers', 'AdminController@cancelled_subscribers')->name('cancelled.subscribers');

});


Route::group(['as' => 'user.'], function(){

    Route::get('/', 'UserController@index')->name('dashboard');

    Route::get('/trending', 'UserController@trending')->name('trending');

    Route::get('channels', 'UserController@channels')->name('channel.list');

    Route::get('history', 'UserController@history')->name('history');

    Route::get('wishlist', 'UserController@wishlist')->name('wishlist');

    Route::get('channel/{id}', 'UserController@channel_videos')->name('channel');

    Route::get('video/{id}', 'UserController@single_video')->name('single');

    // Wishlist

    Route::post('addWishlist', 'UserController@add_wishlist')->name('add.wishlist');

    Route::get('deleteWishlist', 'UserController@delete_wishlist')->name('delete.wishlist');


    // Comments

    Route::post('addComment', 'UserController@add_comment')->name('add.comment');


    Route::get('deleteHistory', 'UserController@delete_history')->name('delete.history');

    Route::post('addHistory', 'UserController@add_history')->name('add.history');


    Route::get('delete-video/{id}/{user_id}', 'UserController@delete_video')->name('delete_video');

    Route::get('ppv-video/{id}/{coupon_code?}','PaypalController@videoSubscriptionPay')->name('ppv-video-payment');

    Route::get('user/payment/video-status','PaypalController@getVideoPaymentStatus')->name('paypalstatus');


    Route::get('paypal_video','PaypalController@payPerVideo')->name('live_video_paypal');

    Route::get('user/payment/livevideo-status','PaypalController@getLiveVideoPaymentStatus')->name('live-paypalstatus');


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

    // Subscribe

    Route::get('/subscribe_channel', 'UserController@subscribe_channel')->name('subscribe.channel');

    Route::get('/unsubscribe_channel', 'UserController@unsubscribe_channel')->name('unsubscribe.channel');

    Route::get('/subscribers', 'UserController@channel_subscribers')->name('channel.subscribers');

    Route::post('take_snapshot/{rid}', 'UserController@setCaptureImage')->name('setCaptureImage');
    

    Route::get('profile', 'UserController@profile')->name('profile');

    Route::get('update/profile', 'UserController@update_profile')->name('update.profile');

    Route::post('update/profile', 'UserController@profile_save')->name('profile.save');

    Route::get('/profile/password', 'UserController@profile_change_password')->name('change.password');

    Route::post('/profile/password', 'UserController@profile_save_password')->name('profile.password');

    // Delete Account

    Route::get('/delete/account', 'UserController@delete_account')->name('delete.account');

    Route::post('/delete/account', 'UserController@delete_account_process')->name('delete.account.process');


    // Channels

    Route::get('channel_create', 'UserController@channel_create')->name('create_channel');

    Route::post('save_channel', 'UserController@save_channel')->name('save_channel');

    Route::get('channel_edit/{id}', 'UserController@channel_edit')->name('channel_edit');

    Route::get('delete_channel', 'UserController@channel_delete')->name('delete.channel');



    // Report Spam Video

    Route::post('markSpamVideo', 'UserController@save_report_video')->name('add.spam_video');

    Route::get('unMarkSpamVideo/{id}', 'UserController@remove_report_video')->name('remove.report_video');

    Route::get('spamVideos', 'UserController@spam_videos')->name('spam-videos');

    Route::get('payment-video', 'UserController@payment_url')->name('payment_url');

    Route::post('live-videos/payment', 'UserController@live_videos_payment_url')->name('live-videos.payment-url');

    Route::get('stripe-payment-video', 'UserController@stripe_payment_video')->name('stripe_payment_video');
    

    Route::post('/save_video_payment/{id}', 'UserController@save_video_payment')->name('save.video-payment');

    Route::get('/remove_payper_view/{id}', 'UserController@remove_payper_view')->name('remove_pay_per_view');


    // Paypal Payment
    Route::get('/paypal/{id}/{coupon_code?}','PaypalController@pay')->name('paypal');

    Route::get('/user/payment/status','PaypalController@getPaymentStatus')->name('paypalstatus');

    Route::get('/live_videos', 'UserController@live_videos')->name('live_videos');

    Route::get('/subscriptions', 'UserController@subscriptions')->name('subscriptions');

    Route::get('/subscription/save/{s_id}/u_id/{u_id}', 'UserController@user_subscription_save')->name('subscription.save');
    

    // Video Upload

    Route::get('upload_video', 'UserController@video_upload')->name('video_upload');

    Route::post('video_save', 'UserController@video_save')->name('video_save');

    Route::post('save_default_img', 'UserController@save_default_img')->name('save_default_img');

    Route::post('upload_video_image', 'UserController@upload_video_image')->name('upload_video_image');

    Route::post('ad_request', 'UserController@ad_request')->name('ad_request');

    Route::get('/delete/video/{id}', 'UserController@video_delete')->name('delete.video');

    Route::get('/edit_video/{id}', 'UserController@video_edit')->name('edit.video');

    Route::get('get_images/{id}', 'UserController@get_images')->name('get_images');

    Route::post('/like_video', 'UserController@likeVideo')->name('video.like');

    Route::post('/dis_like_video', 'UserController@disLikeVideo')->name('video.disLike');

    // Redeems

    Route::get('redeems/', 'UserController@redeems')->name('redeems');

    Route::get('send/redeem', 'UserController@send_redeem_request')->name('redeems.send.request');

    Route::get('redeem/request/cancel/{id?}', 'UserController@redeem_request_cancel')->name('redeems.request.cancel');


    Route::get('/card', 'UserController@card_details')->name('card.card_details');

    Route::post('/add-card', 'UserController@payment_card_add')->name('card.add_card');


    Route::post('/pay_tour','UserController@pay_tour')->name('card.card');

    Route::patch('/payment', 'UserController@payment_card_default')->name('card.default');

    Route::delete('/payment', 'UserController@payment_card_delete')->name('card.delete');

    Route::get('/stripe_payment', 'UserController@stripe_payment')->name('card.stripe_payment');

    Route::get('/ppv-stripe-payment', 'UserController@ppv_stripe_payment')->name('card.ppv-stripe-payment');

    Route::get('/subscribed-channels', 'UserController@subscribed_channels')->name('channels.subscribed');


    // Live videos

    Route::post('broadcast', 'UserController@broadcast')->name('live_video.broadcast');

    Route::get('broadcasting', 'UserController@broadcasting')->name('live_video.start_broadcasting');

    Route::get('stop-streaming', 'UserController@stop_streaming')->name('live_video.stop_streaming');

    Route::post('get_viewer_cnt','UserController@get_viewer_cnt')->name('live_video.get_viewer_cnt');

    Route::post('add/watch_count', 'UserController@watch_count')->name('add.watch_count');


    Route::post('/partialVideos', 'UserController@partialVideos')->name('video.get_videos');

    Route::post('/payment_mgmt_videos', 'UserController@payment_mgmt_videos')->name('video.payment_mgmt_videos');

    Route::get('invoice', 'UserController@invoice')->name('subscription.invoice');

    Route::get('ppv-invoice/{id}', 'UserController@ppv_invoice')->name('subscription.ppv_invoice');

    Route::get('subscription-type/{id}', 'UserController@pay_per_view')->name('subscription.pay_per_view');

    Route::get('pay-per-videos', 'UserController@payper_videos')->name('pay-per-videos');

    Route::post('payment-type/{id}', 'UserController@payment_type')->name('payment-type');

    Route::post('subscription/payment', 'UserController@subscription_payment')->name('subscription.payment');

    Route::get('subscription-success', 'UserController@payment_success')->name('subscription.success');

    Route::get('video-success/{id}', 'UserController@video_success')->name('video.success');

    Route::get('mychannels/list', 'UserController@my_channels')->name('channel.mychannel');

    Route::post('/forgot/password', 'UserController@forgot_password')->name('forgot.password');

    Route::get('subscription/history', 'UserController@subscription_history')->name('subscription.history');

    Route::get('ppv/history', 'UserController@ppv_history')->name('ppv.history');

    Route::get('/tags/list', 'UserController@tags_videos')->name('tags.videos');

    Route::post('/subscriptions/enable', 'UserController@subscriptions_autorenewal_enable')->name('subscriptions.enable-subscription');

    Route::post('/subscriptions/pause', 'UserController@subscriptions_autorenewal_pause')->name('subscriptions.pause-subscription');

    // Category view

    Route::get('category/{id}', 'UserController@categories_view')->name('categories.view');

    Route::post('/categories/videos', 'UserController@categories_videos')->name('categories.videos');

    Route::post('/categories/channels', 'UserController@categories_channels')->name('categories.channels');


    // Live Streaming video

    Route::get('/single/live/video/{id?}' , 'UserController@single_custom_live_video')->name('custom.live.view');

    Route::get('/custom/live/videos' , 'UserController@custom_live_videos')->name('custom.live.index');

    Route::get('live/history', 'UserController@live_history')->name('live.history');

    Route::post('/live/videos/mgmt', 'UserController@live_mgmt_videos')->name('live.video.mgmt');

    Route::get('android/video', 'UserController@android_web_page')->name('android.video');


    // Settings page

    Route::get('/settings' , 'UserController@settings');

    Route::get('/live/video/invoice', 'UserController@live_videos_invoice')->name('live-video.invoice');


});

Route::group(['prefix' => 'userApi'], function(){

        // Live Urls
    
    Route::post('get_live_url', 'UserApiController@get_live_url');

    Route::post('save_vod', 'UserApiController@save_vod');

    Route::post('live_videos', 'UserApiController@live_videos');

    Route::post('save_live_video', 'UserApiController@save_live_video');

    Route::post('/live_video', 'UserApiController@live_video');

    Route::post('save_chat', 'UserApiController@save_chat');

    Route::post('video_subscription', 'UserApiController@video_subscription');

    Route::post('get_viewers', 'UserApiController@get_viewers');

    Route::post('peerProfile', 'UserApiController@peerProfile');

    Route::get('checkVideoStreaming', 'UserApiController@checkVideoStreaming');

    Route::post('close_streaming', 'UserApiController@close_streaming');

    Route::post('stripe/live/ppv', 'UserApiController@stripe_live_ppv');

    Route::post('ppv_paypal', 'UserApiController@ppv_paypal');

    

    Route::post('/watch_count', 'UserController@watch_count');

    Route::post('/register','UserApiController@register');
    
    Route::post('/login','UserApiController@login');

    Route::get('/userDetails','UserApiController@user_details');

    Route::get('/userDetails','UserApiController@user_details');

    Route::post('/updateProfile', 'UserApiController@update_profile');

    Route::post('/forgotpassword', 'UserApiController@forgot_password');

    Route::post('/changePassword', 'UserApiController@change_password');

    Route::post('/deleteAccount', 'UserApiController@delete_account');

    Route::post('/settings', 'UserApiController@settings');

    // Videos and home

    Route::post('/home' , 'UserApiController@home');

    Route::post('/trending' , 'UserApiController@trending');
    
   // Route::post('/common' , 'UserApiController@common');

    Route::post('/single_video' , 'UserApiController@single_video');
    
   // Route::post('/singleVideo' , 'UserApiController@getSingleVideo');

    Route::post('/searchVideo' , 'UserApiController@search_video')->name('search-video');

    Route::post('/channel_videos', 'UserApiController@get_channel_videos');


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

    //Route::post('/redeems/list', 'UserApiController@redeems');

    // Route::post('/send_redeem_request', 'UserApiController@send_redeem_request');


    Route::post('/like_video', 'UserApiController@likeVideo');

    Route::post('/dis_like_video', 'UserApiController@disLikeVideo');

    // Cards 

    Route::post('card_details', 'UserApiController@card_details');

    Route::post('cards_add', 'UserApiController@cards_add');
    
    Route::post('payment_card_add', 'UserApiController@payment_card_add');

    Route::post('default_card', 'UserApiController@default_card');

    Route::post('delete_card', 'UserApiController@delete_card');

    Route::post('/stripe_payment', 'UserApiController@stripe_payment');
    

    // SubScriptions 

    Route::post('subscription_plans', 'UserApiController@subscription_plans');

    Route::post('subscribedPlans', 'UserApiController@subscribedPlans');

    Route::post('pay_now', 'UserApiController@pay_now');

    Route::post('/my_channels', 'UserApiController@my_channels');

    Route::post('/mychannel/list', 'UserApiController@user_channel_list');

    Route::post('subscribe_channel', 'UserApiController@subscribe_channel');

    Route::post('unsubscribe_channel', 'UserApiController@unsubscribe_channel');

    Route::post('subscribed_channels', 'UserApiController@subscribed_channels');

    Route::post('/add_spam', 'UserApiController@add_spam');

    Route::get('/spam-reasons', 'UserApiController@reasons');

    Route::post('remove_spam', 'UserApiController@remove_spam');

    Route::post('spam_videos', 'UserApiController@spam_videos_list');


    Route::post('channel/create', 'UserApiController@create_channel');

    Route::post('ppv_list', 'UserApiController@ppv_list');

    Route::post('/redeems/list', 'UserApiController@redeems');

    Route::post('redeem/request/list', 'UserApiController@redeem_request_list');

    Route::post('redeems/request', 'UserApiController@send_redeem_request');

    Route::post('redeem/request/cancel', 'UserApiController@redeem_request_cancel');

    Route::post('paypal_ppv', 'UserApiController@paypal_ppv');

    Route::post('stripe_ppv', 'UserApiController@stripe_ppv');

    Route::post('channel/edit', 'UserApiController@channel_edit');

    Route::post('channel/delete', 'UserApiController@channel_delete');

    Route::post('/video/erase-streaming', 'UserApiController@erase_streaming');

    //categories

    Route::post('categories/list', 'UserApiController@categories_list');

    Route::post('categories/view', 'UserApiController@categories_view');

    Route::post('categories/videos', 'UserApiController@categories_videos');

    Route::post('categories/channels/list', 'UserApiController@categories_channels_list');

    //Tags

    Route::post('tags/list', 'UserApiController@tags_list');

    Route::post('tags/view', 'UserApiController@tags_view');

    Route::post('tags/videos', 'UserApiController@tags_videos');


    // Automatic subscription with cancel

    Route::post('/cancel/subscription', 'UserApiController@autorenewal_cancel');

    Route::post('/autorenewal/enable', 'UserApiController@autorenewal_enable');

    // Coupons

    Route::post('/apply/coupon/subscription', 'UserApiController@apply_coupon_subscription');

    Route::post('apply/coupon/videos', 'UserApiController@apply_coupon_video_tapes');

    Route::post('apply/coupon/live-videos', 'UserApiController@apply_coupon_live_videos');    
});
