<?php


namespace App\Repositories;

use App\Helpers\Helper;

use Illuminate\Http\Request;

use Validator;

use Log;

use Auth;

use App\VideoTape;

use App\Wishlist;

use App\UserHistory;

class VideoTapeRepository {


	/**
	 * Usage : Register api - validation for the basic register fields 
	 *
	 */

	public static function basic_validation($data = [], &$errors = []) {

		$validator = Validator::make( $data,array(
                'device_type' => 'required|in:'.DEVICE_ANDROID.','.DEVICE_IOS.','.DEVICE_WEB,
                'device_token' => 'required',
                'login_by' => 'required|in:manual,facebook,google',
            )
        );
        
	    if($validator->fails()) {
	        $errors = implode(',', $validator->messages()->all());
	        return false;
	    }

	    return true;

	}

	/**
	 * Recently Added videos based on the Created At 
	 * 
	 */

	public static function recently_added($web = 1) {

		$base_query = VideoTape::where('video_tapes.is_approved' , 1)                      				               ->where('video_tapes.status' , 1)
                          ->orderby('video_tapes.created_at' , 'desc')
                          ->videoResponse();

        if (Auth::check()) {

            // Check any flagged videos are present

            $flag_videos = flag_videos(Auth::user()->id);

            if($flag_videos) {
                $base_query->whereNotIn('video_tapes.id',$flag_videos);
            }
        }

        if($web) {

            $videos = $base_query->paginate(16);

        } else {
            $videos = $base_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();
        }

        return $videos;
	
	}

	/**
	 * Trending videos based on the watch count
	 * 
	 */

	public static function trending($web = 1) {

	    $base_query = VideoTape::where('watch_count' , '>' , 0)
	                    ->videoResponse()
	                    ->orderby('watch_count' , 'desc');

	    if (Auth::check()) {

            // Check any flagged videos are present

            $flag_videos = flag_videos(Auth::user()->id);

            if($flag_videos) {
                $base_query->whereNotIn('video_tapes.id',$flag_videos);
            }
        }

	   if($web) {

            $videos = $base_query->paginate(16);

        } else {

            $videos = $base_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();
        }

	    return $videos;
	
	}

	/**
	 * Suggestion videos based on the Created At 
	 * 
	 */

	public static function suggestion_videos($web = 1) {

		$base_query = VideoTape::where('video_tapes.is_approved' , 1)                      				->where('video_tapes.status' , 1)
                          ->orderby('video_tapes.created_at' , 'desc')
                          ->videoResponse()
                          ->orderByRaw('RAND()');

        if (Auth::check()) {

            // Check any flagged videos are present

            $flag_videos = flag_videos(Auth::user()->id);

            if($flag_videos) {
                $base_query->whereNotIn('video_tapes.id',$flag_videos);
            }
        }

        if($web) {

            $videos = $base_query->paginate(16);

        } else {
            $videos = $base_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();
        }

        return $videos;
	
	}

	/**
	 * User Wishlist
	 * 
	 */

	public static function wishlist($user_id, $web = NULL , $skip = 0) {

        $base_query = Wishlist::where('user_id' , $user_id)
                        ->leftJoin('video_tapes', function ($join)
                            {
                                $join->on('wishlists.video_tape_id', '=', 'video_tapes.id');
                                $join = (new VideoTape())->videoResponse();
                            })
                        ->where('video_tapes.is_approved' , 1)
                        ->where('video_tapes.status' , 1)
                        ->where('wishlists.status' , 1)
                        ->orderby('wishlists.created_at' , 'desc');

        if (Auth::check()) {

            // Check any flagged videos are present

	       	$flag_videos = flag_videos(Auth::user()->id);

            if($flag_videos) {
                $base_query->whereNotIn('video_tapes.id',$flag_videos);
            }
        
        }

        if($web) {
            $videos = $base_query->paginate(16);

        } else {
            $videos = $base_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();
        }

        return $videos;

    }

    /**
	 * User Watch List
	 * 
	 */

    public static function watch_list($user_id, $web = NULL , $skip = 0) {

        $base_query = UserHistory::where('user_id' , $user_id)
                        ->leftJoin('video_tapes', function($join) {
                            $join->on('video_tapes.id', '=', 'video_tape_id');
                            $join = (new VideoTape())->videoResponse();
                        })
                        ->where('video_tapes.is_approved' , 1)
                        ->where('video_tapes.status' , 1)
                        ->orderby('user_histories.created_at' , 'desc');
        
        if (Auth::check()) {

            // Check any flagged videos are present

	       	$flag_videos = flag_videos(Auth::user()->id);

            if($flag_videos) {
                $base_query->whereNotIn('video_tapes.id',$flag_videos);
            }
        
        }

        if($web) {
            $videos = $base_query->paginate(16);

        } else {
            $videos = $base_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();
        }

        return $videos;

    }


}