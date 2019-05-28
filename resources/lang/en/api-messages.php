<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Messages Language Keys
	|--------------------------------------------------------------------------
	|
	|	This is the master keyword file. Any new keyword added to the website
	|	should go here first.
	|
	|
	*/


	/*********** COMMON ERRORS *******/

	'token_expiry' => 'Token Expired',
	'invalid_token' => 'Invalid Token ',
	'invalid_input' => 'Invalid Input',
	'without_id_token_user_accessing_request' => 'The requested action needs login.',

	'are_you_sure' => 'Are you sure?',
	'unknown_error_occured' => 'Unknown error occured',
	'something_went_wrong' => 'Sorry, Something went wrong, while functioning the current request!!',
	'no_result_found' => 'No Result Found',
	
	'invalid_email_address' => 'The email address is invalid!!',

	'stripe_not_configured' => 'The Stripe Payment is not configured Properly!!!',

	'login_success' => 'Successfully loggedin!!',
	'logout_success' => 'Successfully loggedout!!',

	'mail_send_failure' => 'The mail send process is failed!!!',
	'mail_not_configured' => 'The mail configuration failed!!!',
	'mail_sent_success' => 'Mail sent successfully',

	'forgot_password_email_verification_error' => 'The email verification not yet done Please check you inbox.',
	'forgot_password_decline_error' => 'The requested email is disabled by admin.',
	
	'password_not_correct' => 'Sorry, the password is not matched.',
	'password_mismatch' => 'The password doesn\'t match with existing record. Please try again!!',
	'password_change_success' => 'Password changed successfully!!',

	'account_delete_success' => 'Account deleted successfully!!!',

	'user_details_not_found' => 'The selected user not exists.',
	'provider_details_not_found' => 'The selected provider not exists.',

	/*********** COMMON ERRORS *******/

	// = = = = = = = = = USERS = = = = = = = = = = 

	'user_forgot_password_deny_for_social_login' => 'The forgot password only available for manual login.',

	'user_change_password_deny_for_social_login' => 'The change password only available for manual login.',

	'user_details_not_save' => 'User details not saved',

	'user_profile_update_success' => 'The Profile updated',

	'username_password_not_match' => 'Sorry, the username or password you entered do not match. Please try again',

	'user_login_decline' => 'Sorry, Your account has been disabled.',
	'user_not_verified' => 'Please verify your email address!!',
	'user_no_payment_mode' => 'Update the payment mode in account and try again!!!',

	'card_added_success' => 'Card Added successfully!!',
	'card_deleted_success' => 'Card Deleted successfully!!',
	'card_default_success' => 'Selected Card has been changed into Default Card', 
	'user_payment_mode_update_success' => 'Payment Mode updated successfully..!!!',

	'no_default_card' => 'Please add card and try again!!',

	'notification_enable' => 'Notification has been successfully enabled',
	'notification_disable' => 'Notification has been successfully disabled',

	'wishlist_delete_error' => 'The wishlist remove error',
	
	'wishlist_add_success' => 'The video added to wishlist',
	'wishlist_delete_success' => 'The wishlist video removed',
	'wishlist_clear_success' => 'wishlist List songs has been cleared successfully',


	'subscription_not_found' => 'The subscription is not available now.',
	'subscription_payment_success' => 'Payment success..!!',
	'subscription_payment_error' => 'The subscription payment failed..!!',

	'coupon_code_not_found' => 'Coupon code not found',
	'coupon_code_declined' => 'The coupon is invalid',
	'coupon_code_limit_exceeds' => 'Coupon Limit Reached..!, You can`t use the coupon code.',

	'create_a_new_coupon_row' => 'Create a new User coupon Details',
	'total_no_of_users_maximum_limit_reached' => 'Coupon Limit Reached..!, You can`t use the coupon code.',
	'coupon_code_per_user_limit_exceeds' => 'Your maxiumum limit is over..!',
	'add_no_of_times_used_coupon' => 'Already coupon row added, increase no of times used the coupon',

	'video_tape_not_found' => 'The video details is not found.',

	// History management

	'history_video_added' => 'The video added to history',
	'history_video_tape_removed' => 'The video removed from history',
	'history_cleared' => 'The history cleared',
	'history_failed' => 'The action failed',

	// Redeems Management

	'REDEEM_REQUEST_SENT' => 'Sent to Admin.',
	'REDEEM_REQUEST_PROCESSING' => 'On Progress',
	'REDEEM_REQUEST_PAID' => 'Paid',
	'REDEEM_REQUEST_CANCEL' => 'Cancelled',

	'redeem_disabled_by_admin' => 'Redeems is disabled by admin',
	'redeem_not_found' => 'Redeem record not found.',
	'redeem_wallet_empty' => 'Redeem wallet is empty',
	'redeem_minimum_limit_failed' => 'Earn the minimum limit and try again.!',
	'redeem_request_status_mismatch' => 'Redeem request status mismatched',

	'redeem_request_cancelled_success' => 'Th request cancelled and credited the redeems to your wallet.',
	'redeem_request_send_success' => 'Your Redeem Request Sent to Admin.',

);
