<?php


namespace App\Repositories;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Helpers\Helper;

use App\Helpers\VideoHelper;

use Auth, DB, Validator, Setting, Exception, Log;

use App\User;

use App\VideoTape;

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

 	public static function video_list_response($video_tape_ids, $orderby = 'video_tapes.updated_at', $other_select_columns = "") {

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

}