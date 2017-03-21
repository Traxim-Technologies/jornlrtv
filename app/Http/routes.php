<?php

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

Route::get('/test' , 'ApplicationController@test');

// Installation

Route::get('/install/theme', 'InstallationController@install')->name('installTheme');

Route::get('/system/check', 'InstallationController@system_check_process')->name('system-check');

Route::post('/install/theme', 'InstallationController@theme_check_process')->name('install.theme');

Route::post('/install/settings', 'InstallationController@settings_process')->name('install.settings');

Route::get('/test', 'ApplicationController@test')->name('test');

// Elastic Search Test

Route::get('/addIndex', 'ApplicationController@addIndex')->name('addIndex');

Route::get('/addAll', 'ApplicationController@addAllVideoToEs')->name('addAll');

// CRON

Route::get('/publish/video', 'ApplicationController@cron_publish_video')->name('publish');

Route::get('/notification/payment', 'ApplicationController@send_notification_user_payment')->name('notification.user.payment');

Route::get('/payment/expiry', 'ApplicationController@user_payment_expiry')->name('user.payment.expiry');

// Static Pages

Route::get('/privacy', 'UserApiController@privacy')->name('user.privacy');

Route::get('/terms', 'UserApiController@terms')->name('terms');

Route::get('/about', 'UserController@about')->name('user.about');

Route::get('/contact', 'UserController@contact')->name('user.contact');

// Video upload 

Route::post('select/sub_category' , 'ApplicationController@select_sub_category')->name('select.sub_category');

Route::post('select/genre' , 'ApplicationController@select_genre')->name('select.genre');


Route::group(['prefix' => 'admin'], function(){

    Route::get('login', 'Auth\AdminAuthController@showLoginForm')->name('admin.login');

    Route::post('login', 'Auth\AdminAuthController@login')->name('admin.login.post');

    Route::get('logout', 'Auth\AdminAuthController@logout')->name('admin.logout');

    // Registration Routes...

    Route::get('register', 'Auth\AdminAuthController@showRegistrationForm');

    Route::post('register', 'Auth\AdminAuthController@register');

    // Password Reset Routes...
    Route::get('password/reset/{token?}', 'Auth\AdminPasswordController@showResetForm');

    Route::post('password/email', 'Auth\AdminPasswordController@sendResetLinkEmail');

    Route::post('password/reset', 'Auth\AdminPasswordController@reset');

    Route::get('/', 'AdminController@dashboard')->name('admin.dashboard');

    Route::get('/profile', 'AdminController@profile')->name('admin.profile');

	Route::post('/profile/save', 'AdminController@profile_process')->name('admin.save.profile');

	Route::post('/change/password', 'AdminController@change_password')->name('admin.change.password');

    // users

    Route::get('/users', 'AdminController@users')->name('admin.users');

    Route::get('/add/user', 'AdminController@add_user')->name('admin.add.user');

    Route::get('/edit/user', 'AdminController@edit_user')->name('admin.edit.user');

    Route::post('/add/user', 'AdminController@add_user_process')->name('admin.save.user');

    Route::get('/delete/user', 'AdminController@delete_user')->name('admin.delete.user');

    Route::get('/view/user/{id}', 'AdminController@view_user')->name('admin.view.user');

    Route::get('/user/upgrade/{id}', 'AdminController@user_upgrade')->name('admin.user.upgrade');

    Route::any('/upgrade/disable', 'AdminController@user_upgrade_disable')->name('user.upgrade.disable');

    // User History - admin

    Route::get('/user/history/{id}', 'AdminController@view_history')->name('admin.user.history');

    Route::get('/delete/history/{id}', 'AdminController@delete_history')->name('admin.delete.history');
    
    // User Wishlist - admin

    Route::get('/user/wishlist/{id}', 'AdminController@view_wishlist')->name('admin.user.wishlist');

    Route::get('/delete/wishlist/{id}', 'AdminController@delete_wishlist')->name('admin.delete.wishlist');

    // Moderators

    Route::get('/moderators', 'AdminController@moderators')->name('admin.moderators');

    Route::get('/add/moderator', 'AdminController@add_moderator')->name('admin.add.moderator');

    Route::get('/edit/moderator/{id}', 'AdminController@edit_moderator')->name('admin.edit.moderator');

    Route::post('/add/moderator', 'AdminController@add_moderator_process')->name('admin.save.moderator');

    Route::get('/delete/moderator/{id}', 'AdminController@delete_moderator')->name('admin.delete.moderator');
    
    Route::get('/moderator/approve/{id}', 'AdminController@moderator_approve')->name('admin.moderator.approve');

    Route::get('/moderator/decline/{id}', 'AdminController@moderator_decline')->name('admin.moderator.decline');

    Route::get('/view/moderator/{id}', 'AdminController@moderator_view_details')->name('admin.moderator.view');

    // Categories

    Route::get('/categories', 'AdminController@categories')->name('admin.categories');

    Route::get('/add/category', 'AdminController@add_category')->name('admin.add.category');

    Route::get('/edit/category/{id}', 'AdminController@edit_category')->name('admin.edit.category');

    Route::post('/add/category', 'AdminController@add_category_process')->name('admin.save.category');

    Route::get('/delete/category', 'AdminController@delete_category')->name('admin.delete.category');

    Route::get('/view/category/{id}', 'AdminController@view_category')->name('admin.view.category');

    Route::get('/category/approve', 'AdminController@approve_category')->name('admin.category.approve');

    // Admin Sub Categories

    Route::get('/subCategories/{category}', 'AdminController@sub_categories')->name('admin.sub_categories');

    Route::get('/add/subCategory/{category}', 'AdminController@add_sub_category')->name('admin.add.sub_category');

    Route::get('/edit/subCategory/{category_id}/{sub_category_id}', 'AdminController@edit_sub_category')->name('admin.edit.sub_category');

    Route::post('/add/subCategory', 'AdminController@add_sub_category_process')->name('admin.save.sub_category');

    Route::get('/delete/subCategory/{id}', 'AdminController@delete_sub_category')->name('admin.delete.sub_category');

    Route::get('/view/subCategory/{id}', 'AdminController@view_sub_category')->name('admin.view.sub_category');

    Route::get('/subCategory/approve', 'AdminController@approve_sub_category')->name('admin.sub_category.approve');

    // Genre

    Route::post('/save/genre' , 'AdminController@save_genre')->name('admin.save.genre');

    Route::get('/genre/approve', 'AdminController@approve_genre')->name('admin.genre.approve');

    Route::get('/delete/genre/{id}', 'AdminController@delete_genre')->name('admin.delete.genre');

    Route::get('/view/genre/{id}', 'AdminController@view_genre')->name('admin.view.genre');

    // Videos

    Route::get('/videos', 'AdminController@videos')->name('admin.videos');

    Route::get('/add/video', 'AdminController@add_video')->name('admin.add.video');

    Route::get('/edit/video/{id}', 'AdminController@edit_video')->name('admin.edit.video');

    Route::post('/edit/video/process', 'AdminController@edit_video_process')->name('admin.save.edit.video');

    Route::get('/view/video', 'AdminController@view_video')->name('admin.view.video');

    Route::post('/add/video', 'AdminController@add_video_process')->name('admin.save.video');

    Route::get('/delete/video/{id}', 'AdminController@delete_video')->name('admin.delete.video');

    Route::get('/video/approve/{id}', 'AdminController@approve_video')->name('admin.video.approve');

    Route::get('/video/publish-video/{id}', 'AdminController@publish_video')->name('admin.video.publish-video');

    Route::get('/video/decline/{id}', 'AdminController@decline_video')->name('admin.video.decline');

    // Slider Videos

    Route::get('/slider/video/{id}', 'AdminController@slider_video')->name('admin.slider.video');

    // Banner Videos

    Route::get('/banner/videos', 'AdminController@banner_videos')->name('admin.banner.videos');

    Route::get('/add/banner/video', 'AdminController@add_banner_video')->name('admin.add.banner.video');

    Route::get('/change/banner/video/{id}', 'AdminController@change_banner_video')->name('admin.change.video');
    
    // User Payment details
    Route::get('user/payments' , 'AdminController@user_payments')->name('admin.user.payments');

    // Settings

    Route::get('settings' , 'AdminController@settings')->name('admin.settings');

    Route::get('payment/settings' , 'AdminController@payment_settings')->name('admin.payment.settings');

    Route::get('theme/settings' , 'AdminController@theme_settings')->name('admin.theme.settings');
    
    Route::post('settings' , 'AdminController@settings_process')->name('admin.save.settings');

    Route::get('settings/email' , 'AdminController@email_settings')->name('admin.email.settings');

    Route::post('settings/email' , 'AdminController@email_settings_process')->name('admin.email.settings.save');

    Route::get('help' , 'AdminController@help')->name('admin.help');

    // Pages

    Route::get('/viewPage', array('as' => 'viewPages', 'uses' => 'AdminController@viewPages'));

    Route::get('/editPage/{id}', array('as' => 'editPage', 'uses' => 'AdminController@editPage'));

    Route::post('/editPage', array('as' => 'editPageProcess', 'uses' => 'AdminController@pagesProcess'));

    Route::get('/pages', array('as' => 'addPage', 'uses' => 'AdminController@add_page'));

    Route::post('/pages', array('as' => 'adminPagesProcess', 'uses' => 'AdminController@pagesProcess'));

    Route::get('/deletePage/{id}', array('as' => 'deletePage', 'uses' => 'AdminController@deletePage'));

    // Custom Push

    Route::get('/custom/push', 'AdminController@custom_push')->name('admin.push');

    Route::post('/custom/push', 'AdminController@custom_push_process')->name('admin.send.push');

});

Route::get('/', 'UserController@index')->name('user.dashboard');

Route::get('/single', 'UserController@single_video');

Route::get('/user/searchall' , 'ApplicationController@search_video')->name('search');

Route::any('/user/search' , 'ApplicationController@search_all')->name('search-all');

// Route::any('/user/search' , 'ApplicationController@search_all')->name('search-all');

// Categories and single video 

Route::get('categories', 'UserController@all_categories')->name('user.categories');

Route::get('category/{id}', 'UserController@category_videos')->name('user.category');

Route::get('subcategory/{id}', 'UserController@sub_category_videos')->name('user.sub-category');

Route::get('genre/{id}', 'UserController@genre_videos')->name('user.genre');

Route::get('video/{id}', 'UserController@single_video')->name('user.single');


// Social Login

Route::post('/social', array('as' => 'SocialLogin' , 'uses' => 'SocialAuthController@redirect'));

Route::get('/callback/{provider}', 'SocialAuthController@callback');


Route::group([], function(){

    Route::get('login', 'Auth\AuthController@showLoginForm')->name('user.login.form');

    Route::post('login', 'Auth\AuthController@login')->name('user.login.post');

    Route::get('logout', 'Auth\AuthController@logout')->name('user.logout');

    // Registration Routes...
    Route::get('register', 'Auth\AuthController@showRegistrationForm')->name('user.register.form');

    Route::post('register', 'Auth\AuthController@register')->name('user.register.post');

    // Password Reset Routes...
    Route::get('password/reset/{token?}', 'Auth\PasswordController@showResetForm');

    Route::post('password/email', 'Auth\PasswordController@sendResetLinkEmail');

    Route::post('password/reset', 'Auth\PasswordController@reset');

    Route::get('profile', 'UserController@profile')->name('user.profile');

    Route::get('update/profile', 'UserController@update_profile')->name('user.update.profile');

    Route::post('update/profile', 'UserController@profile_save')->name('user.profile.save');

    Route::get('/profile/password', 'UserController@profile_change_password')->name('user.change.password');

    Route::post('/profile/password', 'UserController@profile_save_password')->name('user.profile.password');

    // Delete Account

    Route::get('/delete/account', 'UserController@delete_account')->name('user.delete.account');

    Route::post('/delete/account', 'UserController@delete_account_process')->name('user.delete.account.process');


    Route::get('history', 'UserController@history')->name('user.history');

    Route::get('deleteHistory', 'UserController@delete_history')->name('user.delete.history');

    Route::post('addHistory', 'UserController@add_history')->name('user.add.history');

    // Wishlist

    Route::post('addWishlist', 'UserController@add_wishlist')->name('user.add.wishlist');

    Route::get('deleteWishlist', 'UserController@delete_wishlist')->name('user.delete.wishlist');

    Route::get('wishlist', 'UserController@wishlist')->name('user.wishlist');

    // Comments

    Route::post('addComment', 'UserController@add_comment')->name('user.add.comment');

    Route::get('comments', 'UserController@comments')->name('user.comments');
    
    // Paypal Payment
    Route::get('/paypal/{id}','PaypalController@pay')->name('paypal');

    Route::get('/user/payment/status','PaypalController@getPaymentStatus')->name('paypalstatus');

    Route::get('/trending', 'UserController@trending')->name('user.trending');

});


Route::group(['prefix' => 'moderator'], function(){

    Route::get('login', 'Auth\ModeratorAuthController@showLoginForm')->name('moderator.login');

    Route::post('login', 'Auth\ModeratorAuthController@login')->name('moderator.login.post');

    Route::get('logout', 'Auth\ModeratorAuthController@logout')->name('moderator.logout');

    // Registration Routes...
    Route::get('register', 'Auth\ModeratorAuthController@showRegistrationForm');

    Route::post('register', 'Auth\ModeratorAuthController@register');

    // Password Reset Routes...
    Route::get('password/reset/{token?}', 'Auth\ModeratorPasswordController@showResetForm');

    Route::post('password/email', 'Auth\ModeratorPasswordController@sendResetLinkEmail');

    Route::post('password/reset', 'Auth\ModeratorPasswordController@reset');

    Route::get('/', 'ModeratorController@dashboard')->name('moderator.dashboard');


    Route::get('/profile', 'ModeratorController@profile')->name('moderator.profile');

	Route::post('/profile/save', 'ModeratorController@profile_process')->name('moderator.save.profile');

	Route::post('/change/password', 'ModeratorController@change_password')->name('moderator.change.password');


    // Categories

    Route::get('/categories', 'ModeratorController@categories')->name('moderator.categories');

    Route::get('/add/category', 'ModeratorController@add_category')->name('moderator.add.category');

    Route::get('/edit/category/{id}', 'ModeratorController@edit_category')->name('moderator.edit.category');

    Route::post('/add/category', 'ModeratorController@add_category_process')->name('moderator.save.category');

    Route::get('/delete/category', 'ModeratorController@delete_category')->name('moderator.delete.category');

    Route::get('/view/category/{id}', 'ModeratorController@view_category')->name('moderator.view.category');

    // Admin Sub Categories

    Route::get('/subCategories/{category}', 'ModeratorController@sub_categories')->name('moderator.sub_categories');

    Route::get('/add/subCategory/{category}', 'ModeratorController@add_sub_category')->name('moderator.add.sub_category');

    Route::get('/edit/subCategory/{category_id}/{sub_category_id}', 'ModeratorController@edit_sub_category')->name('moderator.edit.sub_category');

    Route::post('/add/subCategory', 'ModeratorController@add_sub_category_process')->name('moderator.save.sub_category');

    Route::get('/delete/subCategory/{id}', 'ModeratorController@delete_sub_category')->name('moderator.delete.sub_category');

    // Genre

    Route::post('/save/genre' , 'ModeratorController@save_genre')->name('moderator.save.genre');

    Route::get('/delete/genre/{id}', 'ModeratorController@delete_genre')->name('moderator.delete.genre');

    // Videos

    Route::get('/videos', 'ModeratorController@videos')->name('moderator.videos');

    Route::get('/add/video', 'ModeratorController@add_video')->name('moderator.add.video');

    Route::get('/edit/video/{id}', 'ModeratorController@edit_video')->name('moderator.edit.video');

    Route::post('/edit/video', 'ModeratorController@edit_video_process')->name('moderator.save.edit.video');

    Route::get('/view/video', 'ModeratorController@view_video')->name('moderator.view.video');

    Route::post('/add/video', 'ModeratorController@add_video_process')->name('moderator.save.video');

    Route::get('/delete/video', 'ModeratorController@delete_video')->name('moderator.delete.video');

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

    // 

});
