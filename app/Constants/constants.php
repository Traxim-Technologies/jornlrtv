<?php

// Report Video type

if (!defined('USER_APPROVED')) define('USER_APPROVED',1);

if (!defined('USER_DECLINED')) define('USER_DECLINED',0);


if (!defined('USER_EMAIL_VERIFIED')) define('USER_EMAIL_VERIFIED',1);

if (!defined('USER_EMAIL_NOT_VERIFIED')) define('USER_EMAIL_NOT_VERIFIED',0);


if(!defined('PUSH_TO_ALL')) define('PUSH_TO_ALL', 0);

if(!defined('PUSH_TO_CHANNEL_SUBSCRIBERS')) define('PUSH_TO_CHANNEL_SUBSCRIBERS', 1);

if(!defined('PUSH_REDIRECT_HOME')) define('PUSH_REDIRECT_HOME', 1);
if(!defined('PUSH_REDIRECT_CHANNEL')) define('PUSH_REDIRECT_CHANNEL', 2);
if(!defined('PUSH_REDIRECT_SINGLE_VIDEO')) define('PUSH_REDIRECT_SINGLE_VIDEO', 3);


if(!defined('DEVICE_ANDROID')) define('DEVICE_ANDROID', 'android');

if(!defined('DEVICE_IOS')) define('DEVICE_IOS', 'ios');

if(!defined('DEVICE_WEB')) define('DEVICE_WEB', 'web');

// if (!defined('RTMP_URL')) define('RTMP_URL', 'rtmp://'.Setting::get('cross_platform_url').'/live/');

// Channel settings 

if(!defined('CREATE_CHANNEL_BY_USER_ENABLED')) define('CREATE_CHANNEL_BY_USER_ENABLED' , 1);

if(!defined('CREATE_CHANNEL_BY_USER_DISENABLED')) define('CREATE_CHANNEL_BY_USER_DISENABLED' , 0);

// REDEEMS

if(!defined('REDEEM_OPTION_ENABLED')) define('REDEEM_OPTION_ENABLED', 1);

if(!defined('REDEEM_OPTION_DISABLED')) define('REDEEM_OPTION_DISABLED', 0);

// Redeeem Request Status

if(!defined('REDEEM_REQUEST_SENT')) define('REDEEM_REQUEST_SENT', 0);
if(!defined('REDEEM_REQUEST_PROCESSING')) define('REDEEM_REQUEST_PROCESSING', 1);
if(!defined('REDEEM_REQUEST_PAID')) define('REDEEM_REQUEST_PAID', 2);
if(!defined('REDEEM_REQUEST_CANCEL')) define('REDEEM_REQUEST_CANCEL', 3);

if(!defined('TYPE_PUBLIC')) define('TYPE_PUBLIC', 'public');
if(!defined('TYPE_PRIVATE')) define('TYPE_PRIVATE', 'private');

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
if(!defined('VIDEO_TYPE_LIVE')) define('VIDEO_TYPE_LIVE', 2);


if(!defined('VIDEO_UPLOAD_TYPE_s3')) define('VIDEO_UPLOAD_TYPE_s3', 1);
if(!defined('VIDEO_UPLOAD_TYPE_DIRECT')) define('VIDEO_UPLOAD_TYPE_DIRECT', 2);

if(!defined('NO_INSTALL')) define('NO_INSTALL' , 0);

if(!defined('SYSTEM_CHECK')) define('SYSTEM_CHECK' , 1);

if(!defined('INSTALL_COMPLETE')) define('INSTALL_COMPLETE' , 2);


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
if(!defined('ALL_VIDEOS')) define('ALL_VIDEOS', 'All Videos');
if(!defined('JWT_SECRET')) define('JWT_SECRET', '12345');

if(!defined('PERCENTAGE')) define('PERCENTAGE',0);

if(!defined('ABSOULTE')) define('ABSOULTE',1);

if(!defined('WEB')) define('WEB' , 1);



// User status
if(!defined('NEW_USER')) define('NEW_USER', 0);
if(!defined('EXISTING_USER')) define('EXISTING_USER', 1);

// Subscription user tpe
if(!defined('SUBSCRIBED_USER')) define('SUBSCRIBED_USER', 1);
if(!defined('NON_SUBSCRIBED_USER')) define('NON_SUBSCRIBED_USER', 0);


// Ads status

if(!defined('ADS_ENABLED')) define('ADS_ENABLED', 1);
if(!defined('ADS_DISABLED')) define('ADS_DISABLED', 0);


// Admin status

// Video status

if(!defined('ADMIN_VIDEO_APPROVED_STATUS')) define('ADMIN_VIDEO_APPROVED_STATUS', 1);
if(!defined('ADMIN_VIDEO_DECLINED_STATUS')) define('ADMIN_VIDEO_DECLINED_STATUS', 0);

// Channel status

if(!defined('ADMIN_CHANNEL_APPROVED_STATUS')) define('ADMIN_CHANNEL_APPROVED_STATUS', 1);
if(!defined('ADMIN_CHANNEL_DECLINED_STATUS')) define('ADMIN_CHANNEL_DECLINED_STATUS', 0);


// User status

if(!defined('USER_VIDEO_APPROVED_STATUS')) define('USER_VIDEO_APPROVED_STATUS', 1);
if(!defined('USER_VIDEO_DECLINED_STATUS')) define('USER_VIDEO_DECLINED_STATUS', 0);

// Channel status

if(!defined('USER_CHANNEL_APPROVED_STATUS')) define('USER_CHANNEL_APPROVED_STATUS', 1);
if(!defined('USER_CHANNEL_DECLINED_STATUS')) define('USER_CHANNEL_DECLINED_STATUS', 0);


if(!defined('MY_CHANNEL')) define('MY_CHANNEL', 1);
if(!defined('OTHERS_CHANNEL')) define('OTHERS_CHANNEL', 0);


// Category Status

if(!defined('CATEGORY_APPROVE_STATUS')) define('CATEGORY_APPROVE_STATUS', 1);
if(!defined('CATEGORY_DECLINE_STATUS')) define('CATEGORY_DECLINE_STATUS', 0);

// Tag Status

if(!defined('TAG_APPROVE_STATUS')) define('TAG_APPROVE_STATUS', 1);
if(!defined('TAG_DECLINE_STATUS')) define('TAG_DECLINE_STATUS', 0);


// AUTORENEWAL STATUS

if(!defined('AUTORENEWAL_ENABLED')) define('AUTORENEWAL_ENABLED',0);

if(!defined('AUTORENEWAL_CANCELLED')) define('AUTORENEWAL_CANCELLED',1);


// Active plan

if(!defined('ACTIVE_PLAN')) define('ACTIVE_PLAN', 1);

if(!defined('NOT_ACTIVE_PLAN')) define('NOT_ACTIVE_PLAN',0);


// Paid status

if(!defined('PAID_STATUS')) define('PAID_STATUS', 1);