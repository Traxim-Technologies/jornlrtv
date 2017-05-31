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

		$base_query = VideoTape::where('video_tapes.is_approved' , 1)                      				                ->where('video_tapes.status' , 1)
                            ->where('video_tapes.publish_status' , 1)
                            ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
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

	public static function trending($web, $skip = null, $count = 0) {

	    $base_query = VideoTape::where('watch_count' , '>' , 0)
                        ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                        ->where('video_tapes.publish_status' , 1)
                        ->where('video_tapes.status' , 1)
                        ->where('video_tapes.is_approved' , 1)
	                    ->videoResponse()
	                    ->orderby('watch_count' , 'desc');

	    if (Auth::check()) {

            // Check any flagged videos are present

            $flag_videos = flag_videos(Auth::user()->id);

            if($flag_videos) {
                
                $base_query->whereNotIn('video_tapes.id',$flag_videos);
            }
        }

	   if($skip) {

            $videos = $base_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();

        } else if($count > 0){

            $videos = $base_query->skip(0)->take($count)->get();

        } else {

            $videos = $base_query->paginate(16);
            
        }

	    return $videos;
	
	}


    public static function channel_trending($id, $web, $skip = null, $count = 0) {

        $base_query = VideoTape::where('watch_count' , '>' , 0)
                        ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                        ->videoResponse()
                        ->where('channel_id', $id)
                        ->orderby('watch_count' , 'desc');

        if (Auth::check()) {

            // Check any flagged videos are present

            $flag_videos = flag_videos(Auth::user()->id);

            if($flag_videos) {
                
                $base_query->whereNotIn('video_tapes.id',$flag_videos);
            }
        }

       if($skip) {

            $videos = $base_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();

        } else if($count > 0){

            $videos = $base_query->skip(0)->take($count)->get();

        } else {

            $videos = $base_query->paginate(16);
            
        }

        return $videos;
    
    }


    public static function payment_videos($id, $web, $skip = null, $count = 0) {

        $base_query = VideoTape::where('amount' , '>' , 0)
                        ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                        ->videoResponse()
                        ->where('channel_id', $id)
                        ->orderby('amount' , 'desc');

       if($skip) {

            $videos = $base_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();

        } else if($count > 0){

            $videos = $base_query->skip(0)->take($count)->get();

        } else {

            $videos = $base_query->paginate(16);
            
        }

        return $videos;
    
    }

	/**
	 * Suggestion videos based on the Created At 
	 * 
	 */

	public static function suggestion_videos($web = 1, $skip = null, $id = null) {

		$base_query = VideoTape::where('video_tapes.is_approved' , 1)   
                            ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id') 
                            ->where('video_tapes.status' , 1)
                            ->where('video_tapes.publish_status' , 1)
                            ->orderby('video_tapes.created_at' , 'desc')
                            ->videoResponse()
                            ->orderByRaw('RAND()');
        if($id) {

            $base_query->whereNotIn('video_tapes.id', [$id]);
        }

        if (Auth::check()) {

            // Check any flagged videos are present

            $flag_videos = flag_videos(Auth::user()->id);

            if($flag_videos) {
                $base_query->whereNotIn('video_tapes.id',$flag_videos);
            }
        }

        if($skip) {

            $videos = $base_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();
            
        } else {

            $videos = $base_query->paginate(16);
        }

        return $videos;
	
	}

	/**
	 * User Wishlist
	 * 
	 */

	public static function wishlist($user_id, $web = NULL , $skip = 0) {

        $base_query = Wishlist::where('wishlists.user_id' , $user_id)
                        ->leftJoin('video_tapes', 'wishlists.video_tape_id', '=', 'video_tapes.id')
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

        $base_query = UserHistory::where('user_histories.user_id' , $user_id)
                        ->leftJoin('video_tapes', 'video_tapes.id', '=', 'video_tape_id')
                        ->where('video_tapes.is_approved' , 1)
                        ->where('video_tapes.status' , 1)
                        ->where('video_tapes.publish_status' , 1)
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


    public static function channel_videos($channel_id, $web = NULL , $skip = 0) {

        $videos_query = VideoTape::where('video_tapes.is_approved' , 1)
                    ->where('video_tapes.status' , 1)
                    ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                    ->where('video_tapes.channel_id' , $channel_id)
                    ->videoResponse()
                    ->orderby('video_tapes.created_at' , 'desc');
        if (Auth::check()) {
            // Check any flagged videos are present
            $flagVideos = getFlagVideos(Auth::user()->id);

            if($flagVideos) {

                $videos_query->whereNotIn('video_tapes.id', $flagVideos);

            }

        }

        if($web) {
            $videos = $videos_query->paginate(16);
        } else {
            $videos = $videos_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();
        }

        return $videos;
    }


    public static function all_videos($web = NULL , $skip = 0) {

            $videos_query = VideoTape::where('video_tapes.is_approved' , 1)
                        ->where('video_tapes.status' , 1)
                        ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                        ->where('video_tapes.channel_id' , $channel_id)
                        ->videoResponse()
                        ->rand()
                        ->orderby('video_tapes.created_at' , 'asc');
            if (Auth::check()) {
                // Check any flagged videos are present
                $flagVideos = getFlagVideos(Auth::user()->id);

                if($flagVideos) {
                    $videos_query->whereNotIn('video_tapes.id', $flagVideos);
                }
            }

            if($web) {
                $videos = $videos_query->paginate(16);
            } else {
                $videos = $videos_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();
            }

            return $videos;
    }



}