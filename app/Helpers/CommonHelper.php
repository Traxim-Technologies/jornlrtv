<?php 

namespace App\Helpers;

use App\Requests;

use Hash, Auth, AWS, Mail ,File ,Log ,Storage ,Setting ,DB;

use App\Admin;

use App\User; 

class CommonHelper {

	public static function send_email($page,$subject,$email,$email_data) {

	    // check the email notification

	    if(Setting::get('is_email_notification') == YES) {

	        // Don't check with envfile function. Because without configuration cache the email will not send

	        if( config('mail.username') &&  config('mail.password')) {

	            try {

	                $site_url=url('/');

	                $isValid = 1;

	                if(envfile('MAIL_DRIVER') == 'mailgun' && Setting::get('MAILGUN_PUBLIC_KEY')) {

	                    Log::info("isValid - STRAT");

	                    # Instantiate the client.

	                    $email_address = new Mailgun(Setting::get('MAILGUN_PUBLIC_KEY'));

	                    $validateAddress = $email;

	                    # Issue the call to the client.
	                    $result = $email_address->get("address/validate", ['address' => $validateAddress]);

	                    # is_valid is 0 or 1

	                    $isValid = $result->http_response_body->is_valid;

	                    Log::info("isValid FINAL STATUS - ".$isValid);

	                }

	                if($isValid) {

	                    if (Mail::queue($page, ['email_data' => $email_data,'site_url' => $site_url], 
	                            function ($message) use ($email, $subject) {

	                                $message->to($email)->subject($subject);
	                            }
	                    )) {

	                        $message = Helper::success_message(102);

	                        $response_array = ['success' => true , 'message' => $message];

	                        return json_decode(json_encode($response_array));

	                    } else {

	                        throw new Exception(Helper::error_message(116) , 116);
	                        
	                    }

	                } else {

	                    $error = Helper::error_message();

	                    throw new Exception($error, 115);                  

	                }

	            } catch(\Exception $e) {

	                $error = $e->getMessage();

	                $error_code = $e->getCode();

	                $response_array = ['success' => false , 'error' => $error , 'error_code' => $error_code];
	                
	                return json_decode(json_encode($response_array));

	            }
	        
	        } else {

	            $error = Helper::error_message(106);

	            $response_array = ['success' => false , 'error' => $error , 'error_code' => 106];
	                
	            return json_decode(json_encode($response_array));

	        }
	    
	    } else {
	        Log::info("email notification disabled by admin");
	    }

	}

	public static function error_message($code , $test ="") {

        switch($code) {
            
            case 101:
                $string = apitr('invalid_input');
                break;
            case 102:
                $string = apitr('username_password_not_match');
                break;
            case 103:
                $string = apitr('user_details_not_save');
                break;
            case 104: 
                $string = apitr('invalid_email_address');
                break;
            case 105: 
                $string = apitr('mail_send_failure');
                break;
            case 106: 
                $string = apitr('mail_not_configured');
                break;
            case 107:
                $string = apitr('stripe_not_configured');
                break;
            case 108:
                $string = apitr('password_not_correct');
                break;
            case 109:
                $string = apitr('user_no_payment_mode');
                break;
            case 110:
                $string = apitr('user_payment_not_saved');
                break;
            case 111:
                $string = apitr('no_default_card');
                break;
            case 112:
                $string = apitr('no_default_card'); // not used
                break;
            case 113:
                $string = apitr('stripe_payment_not_configured');
                break;
            case 114:
                $string = apitr('stripe_payment_failed');
                break;
            case 115:
                $string = apitr('stripe_payment_card_add_failed');
                break;
            case 116:
                $string = apitr('user_forgot_password_deny_for_social_login');
                break;
            case 117:
                $string = apitr('forgot_password_email_verification_error');
                break;
            case 118:
                $string = apitr('forgot_password_decline_error');
                break;
            case 119:
                $string = apitr('user_change_password_deny_for_social_login');
                break;
            case 200:
                $string = apitr('host_details_not_found'); // Not used
                break;
            case 201:
                $string = apitr('provider_details_not_found'); // Not used
                break;
            case 202:
                $string = apitr('invalid_request_input'); // Not used
                break;
            case 203:
                $string = apitr('subscription_not_found');
                break;
            case 204:
                $string = apitr('subscription_payment_error');
                break;
            case 205:
                $string = apitr('coupon_code_not_found');
                break;
            case 206:
                $string = apitr('coupon_code_not_found');
                break;
            case 207:
                $string = apitr('coupon_code_limit_exceeds');
                break;
            case 208:
                $string = apitr('coupon_code_per_user_limit_exceeds');
                break;

            // USE BELOW CONSTANTS FOR AUTHENTICATION CHECK
            case 1000:
                $string = apitr('user_login_decline');
                break;
            case 1001:
                $string = apitr('user_not_verified');
                break;
            case 1002:
                $string = apitr('user_details_not_found');
                break;
            case 1003:
                $string = apitr('token_expiry');
                break;
            case 1004:
                $string = apitr('invalid_token');
                break;
            case 1005:
                $string = apitr('without_id_token_user_accessing_request');
                break;

            default:
                $string = apitr('unknown_error_occured');
        }

        return $string;
    
    }

    public static function success_message($code) {

        switch($code) {
            case 101:
                $string = apitr('login_success');
                break;
            case 102:
                $string = apitr('mail_sent_success');
                break;
            case 103:
                $string = apitr('account_delete_success');
                break;
            case 104:
                $string = apitr('password_change_success');
                break;
            case 105:
                $string = apitr('card_added_success');
                break;
            case 106:
                $string = apitr('logout_success');
                break;
            case 107:
                $string = apitr('card_deleted_success');
                break;
            case 108:
                $string = apitr('card_default_success');
                break;  
            case 109:
                $string = apitr('user_payment_mode_update_success');
                break;
            case 200:
                $string = apitr('wishlist_add_success');
                break;
            case 201:
                $string = apitr('wishlist_delete_success');
                break;
            case 202:
                $string = apitr('wishlist_clear_success');
                break;
            case 203:
                $string = apitr('not_used');
                break;
            case 204:
                $string = apitr('bell_notification_updated');
                break;
            case 205: 
                $string = apitr('subscription_payment_success');
                break;
            case 206: 
                $string = apitr('notification_enable');
                break;
            case 207: 
                $string = apitr('notification_disable');
                break;


            default:
                $string = "";
        
        }
        
        return $string;
    
    }


}