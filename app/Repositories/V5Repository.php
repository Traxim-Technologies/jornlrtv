<?php


namespace App\Repositories;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Helpers\Helper;

use App\Helpers\VideoHelper;

use Auth, DB, Validator, Setting, Exception, Log;

use App\User;

use App\VideoTape, App\PayPerView;

class V5Repository {


    public static function home_first_section(Request $request) {

      // Log::info("home_first_section".print_r($request->all() , true));

    	try {

            $user_details = User::find($request->id);

            $data = [];

            /* - - - - - - - - - - - Trending section - - - - - - - - - - - */

            $trending_videos = VideoHelper::trending_videos($request);

            $trending_videos_data['title'] = tr('header_trending');

            $trending_videos_data['description'] = tr('header_trending');

            $trending_videos_data['url_type'] = URL_TYPE_TRENDING;

            $trending_videos_data['url_page_id'] = 0;

            $trending_videos_data['see_all_url'] = "";

            $trending_videos_data['data'] = $trending_videos ?: [];

            array_push($data, $trending_videos_data);

            /* - - - - - - - - - - - Trending section - - - - - - - - - - - */

            /* - - - - - - - - - - - My List section - - - - - - - - - - - */

            $wishlist_videos = VideoHelper::wishlist_videos($request);

            $wishlist_videos_data['title'] = tr('header_wishlist');

            $wishlist_videos_data['description'] = tr('header_wishlist');

            $wishlist_videos_data['url_type'] = URL_TYPE_WISHLIST;

            $wishlist_videos_data['url_page_id'] = 0;

            $wishlist_videos_data['see_all_url'] = "";

            $wishlist_videos_data['data'] = $wishlist_videos ?: [];

            array_push($data, $wishlist_videos_data);

            /* - - - - - - - - - - - My List section - - - - - - - - - - - */

            /* - - - - - - - - - - - New Release section - - - - - - - - - - - */

            $recent_videos = VideoHelper::new_releases_videos($request);

            $recent_videos_data['title'] = tr('header_new_releases');

            $recent_videos_data['description'] = tr('header_new_releases');

            $recent_videos_data['url_type'] = URL_TYPE_NEW_RELEASE;

            $recent_videos_data['url_page_id'] = 0;

            $recent_videos_data['see_all_url'] = "";

            $recent_videos_data['data'] = $recent_videos ?: [];

            array_push($data, $recent_videos_data);

            /* - - - - - - - - - - - New Release section - - - - - - - - - - - */

            /* - - - - - - - - - - - Suggestions section - - - - - - - - - - - */

            $recent_videos = VideoHelper::suggestion_videos($request);

            $recent_videos_data['title'] = tr('header_new_releases');

            $recent_videos_data['description'] = tr('header_new_releases');

            $recent_videos_data['url_type'] = URL_TYPE_NEW_RELEASE;

            $recent_videos_data['url_page_id'] = 0;

            $recent_videos_data['see_all_url'] = "";

            $recent_videos_data['data'] = $recent_videos ?: [];

            array_push($data, $recent_videos_data);

            /* - - - - - - - - - - - Suggestions section - - - - - - - - - - - */

            return $data;


	} catch(Exception $e) {

		$error_messages = $e->getMessage();

		$error_code = $e->getCode();

		$response_array = ['success' => false , 'error_messages' => $error_messages , 'error_code' => $error_code];

		return response()->json($response_array , 200);

	}
    
    }

    /**
	 *
	 * Function Name: 
	 *
	 * @uses used to get the common list details for video
	 *
	 * @created Vidhya R
	 *
	 * @updated Vidhya R
	 *
	 * @param 
	 *
	 * @return
	 */

 	public static function video_list_response($video_tape_ids, $user_id, $orderby = 'video_tapes.updated_at', $other_select_columns = "") {

        $user_details = User::find($user_id);

 		$base_query = VideoTape::whereIn('video_tapes.id' , $video_tape_ids)
 							->orderBy($orderby , 'desc');

 		if($other_select_columns != "") {

 			$base_query = $base_query->ShortVideoResponse($other_select_columns);

 		} else {

 			$base_query = $base_query->ShortVideoResponse();
 		}
 		
 		$video_tapes = $base_query->get();

        foreach ($video_tapes as $key => $video_tape_details) {

            $video_tape_details->currency = Setting::get('currency', '$');

            $video_tape_details->share_url = route('user.single' , $video_tape_details->video_tape_id);

            $video_tape_details->watch_count = number_format_short($video_tape_details->watch_count);

            $video_tape_details->should_display_ppv = $video_tape_details->is_my_channel = NO;

            if($user_details) {

                if($channel_details = Channel::find($video_tape_details->channel_id)) {

                    $video_tape_details->is_my_channel = $channel_details->user_id == $user_details->id ? YES: NO;

                }

                $ppv_details = self::pay_per_views_status_check($user_details->id, $user_details->user_type, $video_tape_details)->getData();

                $watch_video_free = DEFAULT_TRUE;

                $video_tape_details->should_display_ppv = $ppv_details->success == $watch_video_free ? NO : YES;
            }
        
        }

 		return $video_tapes;

 	}

      public static function single_video_response($video_tape_id, $user_id) {

            // $data = array();

            // $video_tape_details = VideoTape::where('id', $request->video_tape_id)
            //                         ->where('user_id', $request->id)
            //                         ->where('status', APPROVED)
            //                         ->select('id as video_tape_id', 'title', 'description', 'default_image', 'age_limit', 'duration', 'video_publish_type', 'publish_status', 'publish_time', 'is_approved as is_admin_approved', 'status as video_status', 'watch_count', 'is_pay_per_view', 'type_of_subscription', 'ppv_amount', 'category_name','video_type', 'channel_id', 'user_ppv_amount as ppv_revenue', 'amount as ads_revenue', 'category_id')
            //                         ->first();

            // if(!$video_tape_details) {

            //     throw new Exception(Helper::get_error_message(906), 906);
            // }
      
      }

    /**
     * Function Name : pay_per_views_status_check
     *
     * To check the status of the pay per view in each video
     *
     * @created Vithya
     * 
     * @updated
     *
     * @param object $request - Video related details, user related details
     *
     * @return response of success/failure response of datas
     */
    public static function pay_per_views_status_check($user_id, $user_type, $video_data) {

        // Check video details present or not

        if ($video_data) {

            // Check the video having ppv or not

            if ($video_data->is_pay_per_view) {

                $is_ppv_applied_for_user = DEFAULT_FALSE; // To check further steps , the user is applicable or not

                // Check Type of User, 1 - Normal User, 2 - Paid User, 3 - Both users

                switch ($video_data->type_of_user) {

                    case NORMAL_USER:
                        
                        if (!$user_type) {

                            $is_ppv_applied_for_user = DEFAULT_TRUE;
                        }

                        break;

                    case PAID_USER:
                        
                        if ($user_type) {

                            $is_ppv_applied_for_user = DEFAULT_TRUE;
                        }
                        
                        break;
                    
                    default:

                        // By default it will taks as Both Users

                        $is_ppv_applied_for_user = DEFAULT_TRUE;

                        break;
                
                }

                if ($is_ppv_applied_for_user) {

                    // Check the user already paid or not

                    $ppv_model = PayPerView::where('status', DEFAULT_TRUE)
                        ->where('user_id', $user_id)
                        ->where('video_id', $video_data->video_tape_id)
                        ->orderBy('id','desc')
                        ->first();

                    $watch_video_free = DEFAULT_FALSE;

                    if ($ppv_model) {

                        // Check the type of payment , based on that user will watch the video 

                        switch ($video_data->type_of_subscription) {

                            case ONE_TIME_PAYMENT:
                                
                                $watch_video_free = DEFAULT_TRUE;
                                
                                break;

                            case RECURRING_PAYMENT:

                                // If the video is recurring payment, then check the user already watched the paid video or not 
                                
                                if (!$ppv_model->is_watched) {

                                    $watch_video_free = DEFAULT_TRUE;
                                }
                                
                                break;
                            
                            default:

                                // By default it will taks as true

                                $watch_video_free = DEFAULT_TRUE;

                                break;
                        }

                        if ($watch_video_free) {

                            $response_array = ['success'=>true, 'message'=>Helper::get_message(124), 'code'=>124];

                        } else {

                            $response_array = ['success'=>false, 'message'=>Helper::get_message(125), 'code'=>125];

                        }

                    } else {

                        // 125 - User pay and watch the video

                        $response_array = ['success'=>false, 'message'=>Helper::get_message(125), 'code'=>125];
                    }

                } else {

                    $response_array = ['success'=>true, 'message'=>Helper::get_message(124), 'code'=>124];

                }

            } else {

                // 124 - User can watch the video
                
                $response_array = ['success' => true, 'message'=>Helper::get_message(123), 'code'=>124];
            }

        } else {

            $response_array = ['success'=>false, 'error_messages'=>Helper::get_error_message(906), 
                'error_code'=>906];
        }

        return response()->json($response_array);
    
    }

}