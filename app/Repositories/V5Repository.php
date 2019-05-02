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

        Log::info("home_first_section".print_r($request->all() , true));

    	try {

            $user_details = User::find($request->id);

            $data = [];

            /* - - - - - - - - - - - My List section - - - - - - - - - - - */

            $wishlist_videos = VideoHelper::wishlist_videos($request);

            $wishlist_videos_data['title'] = tr('header_wishlist');

            $wishlist_videos_data['description'] = tr('header_wishlist');

            $wishlist_videos_data['url_type'] = URL_TYPE_WISHLIST;

            $wishlist_videos_data['url_page_id'] = 0;

            // $wishlist_videos_data['see_all_url'] = route('userapi.section_wishlists');

            $wishlist_videos_data['data'] = $wishlist_videos ?: [];

            array_push($data, $wishlist_videos_data);

            /* - - - - - - - - - - - My List section - - - - - - - - - - - */


            /* - - - - - - - - - - - New Releases section - - - - - - - - - - - */

            // $new_releases_videos = VideoHelper::new_releases_videos($request);

            // $new_releases_videos_data['title'] = tr('header_new_releases');

            // $new_releases_videos_data['url_type'] = URL_TYPE_NEW_RELEASE;

            // $new_releases_videos_data['url_page_id'] = 0;

            // $new_releases_videos_data['see_all_url'] = route('userapi.section_new_releases');

            // $new_releases_videos_data['data'] = $new_releases_videos ?: [];

            // array_push($data, $new_releases_videos_data);

            /* - - - - - - - - - - - New Releases section - - - - - - - - - - - */

            /* - - - - - - - - - - - Trending Now section - - - - - - - - - - - */

            // $trending_videos = VideoHelper::trending_videos($request);

            // $trending_videos_data['title'] = tr('header_trending');

            // $trending_videos_data['url_type'] = URL_TYPE_TRENDING;

            // $trending_videos_data['url_page_id'] = 0;

            // $trending_videos_data['see_all_url'] = route('userapi.section_trending');

            // $trending_videos_data['data'] = $trending_videos ?: [];

            // array_push($data, $trending_videos_data);

            /* - - - - - - - - - - - Trending Now section - - - - - - - - - - - */

            /* - - - - - - - - - - - Recommented section - - - - - - - - - - - */

            // $suggestion_videos = VideoHelper::suggestion_videos($request);

            // $suggestion_videos_data['title'] = tr('header_recommended');

            // $suggestion_videos_data['url_type'] = URL_TYPE_SUGGESTION;

            // $suggestion_videos_data['url_page_id'] = 0;

            // $suggestion_videos_data['see_all_url'] = route('userapi.section_suggestions');

            // $suggestion_videos_data['data'] = $suggestion_videos ?: [];

            // array_push($data, $suggestion_videos_data);

            /* - - - - - - - - - - - Recommented section - - - - - - - - - - - */

            /* - - - - - - - - - - - Banner section - - - - - - - - - - - */

            // $banner_videos = VideoHelper::banner_videos($request);

            // $banner_videos_data['title'] = tr('header_banner');

            // $banner_videos_data['url_type'] = "";

            // $banner_videos_data['url_page_id'] = 0;

            // $banner_videos_data['see_all_url'] = "";

            // $banner_videos_data['data'] = $banner_videos ?: [];

            /* - - - - - - - - - - - Banner section - - - - - - - - - - - */

            /* - - - - - - - - - - - Originals section - - - - - - - - - - - */

            // $originals_videos = VideoHelper::original_videos($request);

            // $originals_videos_data['title'] = tr('header_originals');

            // $originals_videos_data['url_type'] = URL_TYPE_ORIGINAL;

            // $originals_videos_data['url_page_id'] = 0;

            // $originals_videos_data['see_all_url'] = route('userapi.section_originals');

            // $originals_videos_data['data'] = $originals_videos ?: [];

            /* - - - - - - - - - - - Originals section - - - - - - - - - - - */

            // Get the page title

            $api_page_title = ""; 

            // if($request->category_id) {

            //     $category_details = Category::find($request->category_id);

            //     $api_page_title = $category_details->name ?: "Category"; 

            // }

            // return json_decode(json_encode($data));

            return $data;

			// $response_array = ['success' => true , 'page_title' => $api_page_title,'data' => $data];

			// return response()->json($response_array , 200);

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

}