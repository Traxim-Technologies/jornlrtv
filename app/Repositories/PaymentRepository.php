<?php

/**************************************************
* Repository Name: PaymentRepository
*
* Purpose: This repository used to do all functions related payments.
*
*@author: vidhyar2612
*
* Date Created: 30 Dec 2017
**************************************************/

namespace App\Repositories;

use Illuminate\Http\Request;

use App\Helpers\Helper;

use Validator;

use Hash;

use Log;

use Setting;

use Session;

use App\User;

use App\UserPayment;

use App\VideoTape;

use App\PayPerView;

use App\Subscription;

class PaymentRepository {

    /**
     * @uses to store the payment failure
     *
     * @param $user_id
     *
     * @param $subscription_id
     *
     * @param $reason
     *
     * @param $payment_id = After payment - if any configuration failture or timeout
     *
     * @return boolean response
     */

    public static function subscription_payment_failure_save($user_id = 0 , $subscription_id = 0 , $reason = "" , $payment_id = "") {

        Log::info("subscription_payment_failure_save STRAT");

        /*********** DON't REMOVE LOGS **************/

        // Log::info("1- Subscription ID".$subscription_id);

        // Log::info("2- USER ID".$user_id);
        
        // Log::info("3- MESSAGE ID".$reason);

        // Check the user_id and subscription id not null

        /************ AFTER user paid, if any configuration failture *******/

        if($payment_id) {

            $user_payment_details = UserPayment::where('payment_id',$payment_id)->first();

            $user_payment_details->reason = "After_Payment"." - ".$reason;

            $user_payment_details->save();

            return true;

        }

        /************ Before user payment, if any configuration failture or TimeOut *******/

        if(!$user_id || !$subscription_id) {

            Log::info('Payment failure save - USER ID and Subscription ID not found');

            return false;

        }

        // Get the user payment details

        $user_payment = new UserPayment();

        $user_payment->expiry_date = date('Y-m-d H:i:s');

        $user_payment->payment_id  = "Payment-Failed";
        
        $user_payment->user_id = $user_id;
        
        $user_payment->subscription_id = $subscription_id;
        
        $user_payment->status = 0;

        $user_payment->reason = $reason ? $reason : "";

        $user_payment->save();

        return true;
        

    }

    /**
     * @uses to store the PPV payment failure
     *
     * @param $user_id
     *
     * @param $admin_video_id
     *
     * @param $payment_id
     *
     * @param $reason
     *
     * @param $payment_id = After payment - if any configuration failture or timeout
     *
     * @return boolean response
     */

	public static function ppv_payment_failure_save($user_id = 0 , $video_tape_id = 0 , $reason = "" , $payment_id = "") {

        /*********** DON't REMOVE LOGS **************/

        // Log::info("1- Subscription ID".$subscription_id);

        // Log::info("2- USER ID".$user_id);
        
        // Log::info("3- MESSAGE ID".$reason);

	    // Check the user_id and subscription id not null

        /************ AFTER user paid, if any configuration failture  or timeout *******/

        if($payment_id) {

            $ppv_payment_details = PayPerView::where('payment_id',$payment_id)->first();

            $ppv_payment_details->reason = "After_Payment"." - ".$reason;

            $ppv_payment_details->save();

            return true;

        }

        /************ Before user payment, if any configuration failture or TimeOut *******/

        if(!$user_id || !$video_tape_id) {

            Log::info('Payment failure save - USER ID and Subscription ID not found');

            return false;

        }

        $ppv_user_payment_details = PayPerView::where('user_id' , $user_id)->where('video_id' , $video_tape_id)->where('amount',0)->first();

        if(empty($ppv_user_payment_details)) {

            $ppv_user_payment_details = new PayPerView;

        }

        $ppv_user_payment_details->expiry_date = date('Y-m-d H:i:s');

        $ppv_user_payment_details->payment_id  = "Payment-Failed";

        $ppv_user_payment_details->user_id = $user_id;

        $ppv_user_payment_details->video_id = $video_tape_id;

        $ppv_user_payment_details->reason = "BEFORE-".$reason;

        // @todo 

        

        $ppv_user_payment_details->save();

        return true;
	    

	}

    /**
     * @uses to store the payment with commission split 
     *
     * @param $admin_video_id
     *
     * @param $payperview_id
     *
     * @param $moderator_id
     * 
     * @return boolean response
     */

    public static function ppv_commission_split($admin_video_id = "" , $payperview_id = "" , $moderator_id = "") {

        if(!$admin_video_id || !$payperview_id || !$moderator_id) {

            return false;
        }

        /***************************************************
         *
         * commission details need to update in following sections 
         *
         * admin_videos table - how much earnings for particular video
         *
         * pay_per_views - On Payment how much commission has calculated 
         *
         * Moderator - If video uploaded_by moderator means need add commission amount to their redeems
         *
         ***************************************************/

        // Get the details

        $video_tape_details = VideoTape::find($admin_video_id);

        if(count($video_tape_details) == 0 ) {

            Log::info('ppv_commission_split - VideoTape Not Found');

            return false;
        }

        $ppv_details = PayPerView::find($payperview_id);

        if(count($ppv_details) == 0 ) {

            Log::info('ppv_commission_split - PayPerView Not Found');

            return false;

        }

        $total = $video_tape_details->amount;

        // Commission split 

        $admin_commission = Setting::get('admin_commission')/100;

        $admin_ppv_amount = $total * $admin_commission;

        $user_ppv_amount = $total - $admin_ppv_amount;

        // Update video earnings

        $video_tape_details->admin_ppv_amount = $video_tape_details->admin_ppv_amount + $admin_ppv_amount;

        $video_tape_details->user_ppv_amount = $video_tape_details->user_ppv_amount+$user_ppv_amount;

        $video_tape_details->save();

        // Update PPV Details

        if($ppv_details) {

            $ppv_details->currency = Setting::get('currency');

            $ppv_details->admin_ppv_amount = $admin_ppv_amount;

            $ppv_details->user_ppv_amount = $user_ppv_amount;

            $ppv_details->save();
        
        }

        // Check the video uploaded by moderator or admin (uploaded_by = admin , uploaded_by = moderator ID)

        if(is_numeric($video_tape_details->uploaded_by)) {

            add_to_redeem($video_tape_details->uploaded_by , $user_ppv_amount , $admin_ppv_amount);

        } else {

            Log::info("No Redeems - ");
        }

        return true;

    }
}
