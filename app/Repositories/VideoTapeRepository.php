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

use DB;

use Setting;

use App\ChannelSubscription;

use App\Flag;

use App\UserRating;

use App\User;

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

	public static function recently_added($request, $web = 1) {

		$base_query = VideoTape::where('video_tapes.is_approved' , 1)                      				                ->where('video_tapes.status' , 1)
                            ->where('video_tapes.publish_status' , 1)
                            ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                            ->orderby('video_tapes.created_at' , 'desc')
                            ->where('video_tapes.age_limit','<=', checkAge($request))
                            ->videoResponse();

        if (Auth::check()) {

            // Check any flagged videos are present

            $flag_videos = flag_videos($request->id);

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

	public static function trending($request, $web, $skip = null, $count = 0) {

	    $base_query = VideoTape::where('watch_count' , '>' , 0)
                        ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                        ->where('video_tapes.publish_status' , 1)
                        ->where('video_tapes.status' , 1)
                        ->where('video_tapes.is_approved' , 1)
	                    ->videoResponse()
                        ->where('video_tapes.age_limit','<=', checkAge($request))
	                    ->orderby('watch_count' , 'desc');

	    if (Auth::check()) {

            // Check any flagged videos are present

            $flag_videos = flag_videos($request->id);

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

	public static function suggestion_videos($request, $web = 1, $skip = null, $id = null) {

		$base_query = VideoTape::where('video_tapes.is_approved' , 1)   
                            ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id') 
                            ->where('video_tapes.status' , 1)
                            ->where('video_tapes.publish_status' , 1)
                            ->orderby('video_tapes.created_at' , 'desc')
                            ->videoResponse()
                            ->where('video_tapes.age_limit','<=', checkAge($request))
                            ->orderByRaw('RAND()');
        if($id) {

            $base_query->whereNotIn('video_tapes.id', [$id]);
        }

        if (Auth::check()) {

            // Check any flagged videos are present

            $flag_videos = flag_videos($request->id);

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

	public static function wishlist($request, $web = NULL , $skip = 0) {

        $base_query = Wishlist::where('wishlists.user_id' , $request->id)
                            ->leftJoin('video_tapes' ,'wishlists.video_tape_id' , '=' , 'video_tapes.id')
                            ->leftJoin('channels' ,'video_tapes.channel_id' , '=' , 'channels.id')
                            ->where('video_tapes.is_approved' , 1)
                            ->where('video_tapes.status' , 1)
                            ->where('wishlists.status' , 1)
                            ->select(
                                    'wishlists.id as wishlist_id','video_tapes.id as video_tape_id' ,
                                    'video_tapes.title','video_tapes.description' ,
                                    'default_image','video_tapes.watch_count','video_tapes.ratings',
                                    'video_tapes.duration','video_tapes.channel_id',
                                    DB::raw('DATE_FORMAT(video_tapes.publish_time , "%e %b %y") as publish_time') , 'channels.name as channel_name', 'wishlists.created_at')
                            ->where('video_tapes.age_limit','<=', checkAge($request))
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

    public static function watch_list($request, $web = NULL , $skip = 0) {

        $base_query = UserHistory::where('user_histories.user_id' , $request->id)
                            ->leftJoin('video_tapes' ,'user_histories.video_tape_id' , '=' , 'video_tapes.id')
                            ->leftJoin('channels' ,'video_tapes.channel_id' , '=' , 'channels.id')
                            ->where('video_tapes.is_approved' , 1)
                            ->where('video_tapes.status' , 1)
                            ->select('user_histories.id as history_id','video_tapes.id as video_tape_id' ,
                                'video_tapes.title','video_tapes.description' , 'video_tapes.duration',
                                'default_image','video_tapes.watch_count','video_tapes.ratings',
                                DB::raw('DATE_FORMAT(video_tapes.publish_time , "%e %b %y") as publish_time'), 'video_tapes.channel_id','channels.name as channel_name', 'user_histories.created_at')
                            ->where('video_tapes.age_limit','<=', checkAge($request))
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

     public static function channelVideos($request, $channel_id, $web = NULL , $skip = 0) {

        $videos_query = VideoTape::where('video_tapes.is_approved' , 1)
                    ->where('video_tapes.status' , 1)
                    ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                    ->where('video_tapes.channel_id' , $channel_id)
                    ->shortVideoResponse()
                    ->orderby('video_tapes.created_at' , 'desc');
        if ($request->id) {
            // Check any flagged videos are present
            $flagVideos = getFlagVideos($request->id);

            if($flagVideos) {

                $videos_query->whereNotIn('video_tapes.id', $flagVideos);

            }

        }

        $videos_query->where('video_tapes.age_limit','<=', checkAge($request));

        if($web) {
            $videos = $videos_query->paginate(16);
        } else {
            $videos = $videos_query->skip($skip)->take(Setting::get('admin_take_count' ,12))->get();
        }


        $data = [];

        if(count($videos) > 0) {

            foreach ($videos as $key => $value) {

                if($request->id) {

                    if($user_details = User::find($request->id)) {

                        $value['pay_per_view_status'] = Helper::watchFullVideo($user_details->id, $user_details->user_type, $value);
                        
                        $value['user_type'] = $user_details->user_type;

                    }
                }

                $value['watch_count'] = number_format_short($value->watch_count);

                $value['wishlist_status'] = $request->id ? (Helper::check_wishlist_status($request->id,$value->video_tape_id) ? DEFAULT_TRUE : DEFAULT_FALSE): 0;

                $value['share_url'] = route('user.single' , $value->video_tape_id);

                array_push($data, $value->toArray());
            }
        }

        return $data;
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


    public static function admin_recently_added() {

        $base_query = VideoTape::where('video_tapes.is_approved' , 1)                                       
                            ->where('video_tapes.status' , 1)
                            ->where('video_tapes.publish_status' , 1)
                            ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id')
                            ->orderby('video_tapes.created_at' , 'desc')
                            ->videoResponse()->paginate(16);


        return $base_query;

    }

    /**
     *
     * return the common response for single video
     *
     */

    public static function single_response($video_tape_id , $user_id = "" , $login_by) {

        // Initialize the empty array

        $data = [];

        $video_tape_details = VideoTape::where('video_tapes.id' , $video_tape_id)
                                    ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id') 
                                    ->where('video_tapes.status' , 1)
                                    ->where('video_tapes.publish_status' , 1)
                                    ->where('video_tapes.is_approved' , 1)
                                    ->videoResponse()
                                    ->first();
        if($video_tape_details) {

            $video_tape_details->publish_time = $video_tape_details->publish_time ? $video_tape_details->publish_time : '';

           // $video_tape_details->

            $data = $video_tape_details->toArray();

            $data['wishlist_status'] = $data['history_status'] = $data['is_subscribed'] = $data['is_liked'] = $data['pay_per_view_status'] = $data['user_type'] = $data['flaggedVideo'] = 0;

            $data['comment_rating_status'] = 1;

            if($user_id) {


                $data['flaggedVideo'] = Flag::where('video_tape_id',$video_tape_id)->where('user_id', $user_id)->first();

                $data['wishlist_status'] = Helper::check_wishlist_status($user_id,$video_tape_id) ? 1 : 0;

                $data['history_status'] = count(Helper::history_status($user_id,$video_tape_id)) > 0? 1 : 0;

                $data['is_subscribed'] = check_channel_status($user_id, $video_tape_details->channel_id);

                $data['is_liked'] = Helper::like_status($user_id,$video_tape_id);

                $mycomment = UserRating::where('user_id', $user_id)->where('video_tape_id', $video_tape_id)->first();

                if ($mycomment) {

                    $data['comment_rating_status'] = DEFAULT_FALSE;
                }

                if($user_details = User::find($user_id)) {

                    $data['pay_per_view_status'] = Helper::watchFullVideo($user_details->id, $user_details->user_type, $video_tape_details);
                    
                    $data['user_type'] = $user_details->user_type;

                }

            }

            $data['subscriberscnt'] = subscriberscnt($video_tape_details->channel_id);

            $data['share_url'] = route('user.single' , $video_tape_id);

            $data['embed_link'] = route('embed_video', array('u_id'=>$video_tape_details->unique_id));

            $video_url = $video_tape_details->video;

            if($login_by == DEVICE_ANDROID) {

                $video_url = Setting::get('streaming_url') ? Setting::get('streaming_url').get_video_end($data['video']) : $video_url;

            }

            if($login_by == DEVICE_IOS) {

                $video_url = Setting::get('HLS_STREAMING_URL') ? Setting::get('HLS_STREAMING_URL').get_video_end($data['video']) : $video_url;

            }

            $data['video'] = $video_url;


        }


        return $data;

    }

    public static function suggestions($request) {

         $data = [];

        $base_query = VideoTape::where('video_tapes.is_approved' , 1)   
                            ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id') 
                            ->where('video_tapes.status' , 1)
                            ->where('video_tapes.publish_status' , 1)
                            ->orderby('video_tapes.watch_count' , 'desc')
                            ->shortVideoResponse();

        if ($request->id) {

            // Check any flagged videos are present

            $flag_videos = flag_videos($request->id);

            if($flag_videos) {

                $base_query->whereNotIn('video_tapes.id',$flag_videos);

            }
        
        }

        if ($request->video_tape_id) {

            $base_query->whereNotIn('video_tapes.id', [$request->video_tape_id]);

        }

        $base_query->where('video_tapes.age_limit','<=', checkAge($request));

        $videos = $base_query->skip($request->skip)->take(4)->get();

        if(count($videos) > 0) {

            foreach ($videos as $key => $value) {

                $value['watch_count'] = number_format_short($value->watch_count);
                
                $value['wishlist_status'] = $request->id ? (Helper::check_wishlist_status($request->id,$value->video_tape_id) ? DEFAULT_TRUE : DEFAULT_FALSE): 0;

                $value['share_url'] = route('user.single' , $value->video_tape_id);

                array_push($data, $value->toArray());
            }
        }

        return $data;

        
    }

}