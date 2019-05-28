<?php 

namespace App\Helpers;

use Auth, DB, Validator, Setting, Exception, Log;

use App\Repositories\V5Repository as V5Repo;

use App\Wishlist;

use App\VideoTape;

use App\UserHistory;

use App\PayPerView;

use App\Flag;


class VideoHelper {

    protected $skip, $take;

    public function __construct(Request $request) {

        $this->take = $request->take ?: (Setting::get('take') ?: 12);

        $this->skip = $request->skip ?: 0;

    }

    /**
     *
     * @method mobile_home()
     *
     * @uses used to get the list of contunue watching videos
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $skip
     *
     * @return list of videos
     */

    public static function mobile_home($request) {

        try {

            $base_query = VideoTape::where('video_tapes.is_approved', ADMIN_VIDEO_APPROVED)   
                            ->leftJoin('channels' , 'video_tapes.channel_id' , '=' , 'channels.id') 
                            ->leftJoin('categories' , 'categories.id' , '=' , 'video_tapes.category_id') 
                            ->where('video_tapes.status' , USER_VIDEO_APPROVED)
                            ->where('video_tapes.publish_status' , VIDEO_PUBLISHED)
                            ->where('channels.is_approved', ADMIN_CHANNEL_APPROVED)
                            ->where('channels.status', USER_CHANNEL_APPROVED)
                            ->where('categories.status', CATEGORY_APPROVE_STATUS)
                            ->orderby('video_tapes.created_at' , 'desc');

            // check page type 

            $base_query = self::get_page_type_query($request, $base_query);

            // Check any flagged videos are present

            $spam_video_ids = flag_videos($request->id);
            
            if($spam_video_ids) {

                $base_query->whereNotIn('video_tapes.id', $spam_video_ids);

            }

            $base_query->where('video_tapes.age_limit','<=', checkAge($request));

            $take = $request->take ?: (Setting::get('take') ?: 12);

            $skip = $request->skip ?: 0;

            $video_tape_ids = $base_query->skip($skip)->take($take)->lists('video_tapes.id')->toArray();

            $video_tapes = V5Repo::video_list_response($video_tape_ids, $request->id, $orderBy = "video_tapes.created_at", $other_select_columns = 'video_tapes.description');

            return $video_tapes;

        }  catch( Exception $e) {

            return [];

        }

    }

    /**
     *
     * @method wishlist_videos()
     *
     * @uses used to get the list of contunue watching videos
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $skip
     *
     * @return list of videos
     */

    public static function wishlist_videos($request) {

        try {

            $base_query = Wishlist::select('wishlists.video_tape_id')
                                ->where('wishlists.user_id' , $request->id)
                                ->leftJoin('video_tapes', 'video_tapes.id', '=' , 'wishlists.video_tape_id')
                                ->orderby('wishlists.updated_at', 'desc');
            // check page type 

            $base_query = self::get_page_type_query($request, $base_query);

            // Check any flagged videos are present

            $spam_video_ids = flag_videos($request->id);
            
            if($spam_video_ids) {

                $base_query->whereNotIn('wishlists.video_tape_id', $spam_video_ids);

            }

            $take = $request->take ?: (Setting::get('take') ?: 12);

            $skip = $request->skip ?: 0;

            $wishlist_video_ids = $base_query->skip($skip)->take($take)->lists('video_tape_id')->toArray();

            $video_tapes = V5Repo::video_list_response($wishlist_video_ids, $request->id, $orderBy = "video_tapes.created_at", $other_select_columns = 'video_tapes.description');

            return $video_tapes;

        }  catch( Exception $e) {

            return [];

        }

    }

    /**
     *
     * @method history_videos()
     *
     * @uses used to get the list of contunue watching videos
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $sub_profile_id
     *
     * @param integer $skip
     *
     * @return list of videos
     */

    public static function history_videos($request) {

        try {

            $base_query = UserHistory::select('user_histories.video_tape_id')
                                ->where('user_histories.user_id' , $request->id)
                                ->leftJoin('video_tapes', 'video_tapes.id', '=' , 'user_histories.video_tape_id')
                                ->orderby('user_histories.updated_at', 'desc');
            // check page type 

            $base_query = self::get_page_type_query($request, $base_query);

            // Check any flagged videos are present

            $spam_video_ids = self::getFlagVideos($request->id);
            
            if($spam_video_ids) {

                $base_query->whereNotIn('user_histories.video_tape_id', $spam_video_ids);

            }

            $take = Setting::get('admin_take_count', 12);

            $skip = $request->skip ?: 0;

            $user_history_ids = $base_query->skip($skip)->take($take)->lists('video_tape_id')->toArray();

            $video_tapes = V5Repo::video_list_response($user_history_ids, $orderBy = "created_at", $other_select_columns = 'video_tapes.description');

            return $video_tapes;

        }  catch( Exception $e) {

            return [];

        }

    }

    /**
     *
     * @method new_releases_videos()
     *
     * @uses used to get the list of contunue watching videos
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $sub_profile_id
     *
     * @param integer $skip
     *
     * @return list of videos
     */

    public static function new_releases_videos($request) {

        try {

            $base_query = VideoTape::orderby('video_tapes.created_at' , 'desc');

            // check page type 

            $base_query = self::get_page_type_query($request, $base_query);
                       
            // Check any flagged videos are present

            $spam_video_ids = self::getFlagVideos($request->id);
            
            if($spam_video_ids) {

                $base_query->whereNotIn('video_tapes.id', $spam_video_ids);

            }

            // Check any video present in continue watching

            // $continue_watching_video_ids = continueWatchingVideos($request->id);
            
            // if($continue_watching_video_ids) {

            //     $base_query->whereNotIn('video_tapes.id', $continue_watching_video_ids);

            // }

            $take = Setting::get('admin_take_count', 12);

            $skip = $request->skip ?: 0;

            $new_video_ids = $base_query->skip($skip)->take($take)->lists('video_tapes.id')->toArray();

            $video_tapes = V5Repo::video_list_response($new_video_ids);

            return $video_tapes;

        }  catch( Exception $e) {

            return [];

        }

    }

    /**
     *
     * @method continue_watching_videos()
     *
     * @uses used to get the list of contunue watching videos
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $sub_profile_id
     *
     * @param integer $skip
     *
     * @return list of videos
     */

    public static function continue_watching_videos($request) {

        try {

            $base_query = ContinueWatchingVideo::where('continue_watching_videos.sub_profile_id', $request->id)
                                ->leftJoin('video_tapes', 'video_tapes.id', '=', 'continue_watching_videos.admin_video_id')
                                ->orderby('continue_watching_videos.updated_at', 'desc');

            // check page type 

            $base_query = self::get_page_type_query($request, $base_query);
                       
            // Check any flagged videos are present

            $spam_video_ids = self::getFlagVideos($request->id);
            
            if($spam_video_ids) {

                $base_query->whereNotIn('admin_video_id', $spam_video_ids);

            }

            $take = Setting::get('admin_take_count', 12);

            $skip = $request->skip ?: 0;

            $continue_watching_video_ids = $base_query->skip($skip)->take($take)->lists('admin_video_id')->toArray();

            $video_tapes = V5Repo::video_list_response($continue_watching_video_ids);

            return $video_tapes;

        }  catch( Exception $e) {

            return [];

        }

    }


    /**
     *
     * @method trending_videos()
     *
     * @uses used to get the list of contunue watching videos
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $sub_profile_id
     *
     * @param integer $skip
     *
     * @return list of videos
     */

    public static function trending_videos($request) {

        try {

            $base_query = AdminVideo::where('video_tapes.watch_count' , '>' , 0)
                            ->orderby('video_tapes.watch_count' , 'desc');

            // check page type 

            $base_query = self::get_page_type_query($request, $base_query);
                       
            // Check any flagged videos are present

            $spam_video_ids = self::getFlagVideos($request->id);
            
            if($spam_video_ids) {

                $base_query->whereNotIn('video_tapes.id', $spam_video_ids);

            }

            // // Check any video present in continue watching

            // $continue_watching_video_ids = continueWatchingVideos($sub_profile_id);
            
            // if($continue_watching_video_ids) {

            //     $base_query->whereNotIn('video_tapes.id', $continue_watching_video_ids);

            // }

            $take = Setting::get('admin_take_count', 12);

            $skip = $request->skip ?: 0;

            $trending_video_ids = $base_query->skip($skip)->take($take)->lists('video_tapes.id')->toArray();

            $video_tapes = V5Repo::video_list_response($trending_video_ids);

            return $video_tapes;

        }  catch( Exception $e) {

            return [];

        }

    }

    /**
     *
     * @method original_videos()
     *
     * @uses used to get the list of contunue watching videos
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $sub_profile_id
     *
     * @param integer $skip
     *
     * @return list of videos
     */

    public static function original_videos($request) {

        try {

            $base_query = AdminVideo::where('video_tapes.is_original_video', YES)
                            ->orderby('video_tapes.updated_at' , 'desc');

            // check page type 

            $base_query = self::get_page_type_query($request, $base_query);
                       
            // Check any flagged videos are present

            $spam_video_ids = self::getFlagVideos($request->id);
            
            if($spam_video_ids) {

                $base_query->whereNotIn('video_tapes.id', $spam_video_ids);

            }

            // // Check any video present in continue watching

            // $continue_watching_video_ids = continueWatchingVideos($sub_profile_id);
            
            // if($continue_watching_video_ids) {

            //     $base_query->whereNotIn('video_tapes.id', $continue_watching_video_ids);

            // }

            $take = Setting::get('admin_take_count', 12);

            $skip = $request->skip ?: 0;

            $original_video_ids = $base_query->skip($skip)->take($take)->lists('video_tapes.id')->toArray();


            $video_tapes = V5Repo::video_list_response($original_video_ids);

            return $video_tapes;

        }  catch( Exception $e) {

            Log::info("original_videos".$e->getMessage());

            return [];

        }

    }

    /**
     *
     * @method suggestion_videos()
     *
     * @uses used to get the list of contunue watching videos
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $sub_profile_id
     *
     * @param integer $skip
     *
     * @return list of videos
     */

    public static function suggestion_videos($request) {

        try {

            $base_query = UserHistory::where('sub_profile_id' , $request->id)->orderByRaw('RAND()');

            // check page type 

            $base_query = self::get_page_type_query($request, $base_query);
                       
            // Check any flagged videos are present

            $spam_video_ids = self::getFlagVideos($request->id);
            
            if($spam_video_ids) {

                $base_query->whereNotIn('user_histories.admin_video_id', $spam_video_ids);

            }

            // Check any video present in continue watching

            // $continue_watching_video_ids = continueWatchingVideos($sub_profile_id);
            
            // if($continue_watching_video_ids) {

            //     $base_query->whereNotIn('user_histories.admin_video_id', $continue_watching_video_ids);

            // }

            $take = Setting::get('admin_take_count', 12);

            $skip = $request->skip ?: 0;

            $suggestion_video_ids = $base_query->skip($skip)->take($take)->lists('admin_video_id')->toArray();

            $video_tapes = V5Repo::video_list_response($suggestion_video_ids);

            return $video_tapes;

        }  catch( Exception $e) {

            return [];

        }

    }

    /**
     *
     * @method banner_videos()
     *
     * @uses used to get the list of contunue watching videos
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $sub_profile_id
     *
     * @param integer $skip
     *
     * @return list of videos
     */

    public static function banner_videos($request) {

        try {

            $base_query = AdminVideo::orderby('video_tapes.created_at' , 'desc');

            // check page type 

            $base_query = self::get_page_type_query($request, $base_query);
                       
            // Check any flagged videos are present

            $spam_video_ids = self::getFlagVideos($request->id);
            
            if($spam_video_ids) {

                $base_query->whereNotIn('video_tapes.id', $spam_video_ids);

            }

            // Check any video present in continue watching

            // $continue_watching_video_ids = continueWatchingVideos($request->id);
            
            // if($continue_watching_video_ids) {

            //     $base_query->whereNotIn('video_tapes.id', $continue_watching_video_ids);

            // }

            $take = Setting::get('admin_take_count', 12);

            $skip = $request->skip ?: 0;

            $banner_video_ids = $base_query->skip($skip)->take($take)->lists('video_tapes.id')->toArray();

            $video_tapes = V5Repo::video_list_response($banner_video_ids, $orderby = 'video_tapes.watch_count');

            foreach ($video_tapes as $key => $admin_video_details) {

                $admin_video_details->banner_image = $admin_video_details->default_image;

                $admin_video_details->wishlist_status = VideoHelper::wishlist_status($admin_video_details->admin_video_id,$request->id);

            }

            return $video_tapes;


        }  catch( Exception $e) {

            return [];

        }

    }

    /**
     *
     * @method category_videos()
     *
     * @uses used to get the list of contunue watching videos
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $sub_profile_id
     *
     * @param integer $skip
     *
     * @return list of videos
     */

    public static function category_videos($request) {

        try {

            $category_ids = is_array($request->category_id) ? $request->category_id : [$request->category_id];

            $base_query = AdminVideo::whereIn('video_tapes.category_id', $category_ids)
                                ->orderby('video_tapes.created_at' , 'desc');
                       
            // check page type 

            $base_query = self::get_page_type_query($request, $base_query);

            // Check any flagged videos are present

            $spam_video_ids = self::getFlagVideos($request->id);
            
            if($spam_video_ids) {

                $base_query->whereNotIn('video_tapes.id', $spam_video_ids);

            }

            // Check any video present in continue watching

            // $continue_watching_video_ids = continueWatchingVideos($request->id);
            
            // if($continue_watching_video_ids) {

            //     $base_query->whereNotIn('video_tapes.id', $continue_watching_video_ids);

            // }

            $take = Setting::get('admin_take_count', 12);

            $skip = $request->skip ?: 0;

            $category_video_ids = $base_query->skip($skip)->take($take)->lists('video_tapes.id')->toArray();

            $video_tapes = V5Repo::video_list_response($category_video_ids);

            return $video_tapes;

        }  catch( Exception $e) {

            return [];

        }

    }

    /**
     *
     * @method sub_category_videos()
     *
     * @uses used to get the list of contunue watching videos
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $sub_profile_id
     *
     * @param integer $skip
     *
     * @return list of videos
     */

    public static function sub_category_videos($request) {

        try {

            $sub_category_ids = is_array($request->sub_category_id) ? $request->sub_category_id : [$request->sub_category_id];

            $base_query = AdminVideo::whereIn('video_tapes.sub_category_id', $sub_category_ids)
                            ->orderby('video_tapes.created_at' , 'desc');

            // check page type 

            $base_query = self::get_page_type_query($request, $base_query);
                       
            // Check any flagged videos are present

            $spam_video_ids = self::getFlagVideos($request->id);
            
            if($spam_video_ids) {

                $base_query->whereNotIn('video_tapes.id', $spam_video_ids);

            }

            // Check any video present in continue watching

            $continue_watching_video_ids = continueWatchingVideos($request->id);
            
            if($continue_watching_video_ids) {

                $base_query->whereNotIn('video_tapes.id', $continue_watching_video_ids);

            }

            $take = Setting::get('admin_take_count', 12);

            $skip = $request->skip ?: 0;

            $sub_category_video_ids = $base_query->skip($skip)->take($take)->lists('video_tapes.id')->toArray();

            $video_tapes = V5Repo::video_list_response($sub_category_video_ids);

            return $video_tapes;

        }  catch( Exception $e) {

            return [];

        }

    }

    /**
     *
     * @method genre_videos()
     *
     * @uses used to get the list of contunue watching videos
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $sub_profile_id
     *
     * @param integer $skip
     *
     * @return list of videos
     */

    public static function genre_videos($request) {

        try {

            $genre_ids = is_array($request->genre_id) ? $request->genre_id : [$request->genre_id];

            $base_query = AdminVideo::whereIn('video_tapes.genre_id', $genre_ids)
                            ->orderby('video_tapes.created_at' , 'desc');

            // check page type 

            $base_query = self::get_page_type_query($request, $base_query);
                       
            // Check any flagged videos are present

            $spam_video_ids = self::getFlagVideos($request->id);
            
            if($spam_video_ids) {

                $base_query->whereNotIn('video_tapes.id', $spam_video_ids);

            }

            // Check any video present in continue watching

            // $continue_watching_video_ids = continueWatchingVideos($request->id);
            
            // if($continue_watching_video_ids) {

            //     $base_query->whereNotIn('video_tapes.id', $continue_watching_video_ids);

            // }

            $take = Setting::get('admin_take_count', 12);

            $skip = $request->skip ?: 0;

            $genre_video_ids = $base_query->skip($skip)->take($take)->lists('video_tapes.id')->toArray();

            $video_tapes = V5Repo::video_list_response($genre_video_ids);

            return $video_tapes;

        }  catch( Exception $e) {

            return [];

        }

    }

    /**
     *
     * @method cast_crews_videos()
     *
     * @uses used to get the list of contunue watching videos
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $sub_profile_id
     *
     * @param integer $skip
     *
     * @return list of videos
     */

    public static function cast_crews_videos($request) {

        try {

            $cast_crew_ids = is_array($request->cast_crew_id) ? $request->cast_crew_id : [$request->cast_crew_id];

            $base_query = VideoCastCrew::whereIn('video_cast_crews.cast_crew_id', $cast_crew_ids)
                                    ->leftJoin('video_tapes', 'video_tapes.id', '=' , 'video_cast_crews.admin_video_id')
                                    ->orderby('video_cast_crews.created_at' , 'desc');

            // check page type 

            $base_query = self::get_page_type_query($request, $base_query);
                       
            // Check any flagged videos are present

            $spam_video_ids = self::getFlagVideos($request->id);
            
            if($spam_video_ids) {

                $base_query->whereNotIn('video_tapes.id', $spam_video_ids);

            }

            // Check any video present in continue watching

            $continue_watching_video_ids = continueWatchingVideos($request->id);
            
            if($continue_watching_video_ids) {

                $base_query->whereNotIn('video_tapes.id', $continue_watching_video_ids);

            }

            $take = Setting::get('admin_take_count', 12);

            $skip = $request->skip ?: 0;

            $cast_crew_ids = $base_query->skip($skip)->take($take)->lists('video_tapes.id')->toArray();

            $video_tapes = V5Repo::video_list_response($cast_crew_ids);

            return $video_tapes;

        }  catch( Exception $e) {

            return [];

        }

    }


    /**
     *
     * @method wishlist_status()
     *
     * @uses used to get the wishlist status of the video
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $sub_profile_id
     *
     * @return boolean 
     */
    public static function wishlist_status($admin_video_id,$sub_profile_id) {

        $wishlist_details = Wishlist::where('admin_video_id' , $admin_video_id)
                        ->where('sub_profile_id' , $sub_profile_id)
                        ->where('status' , YES)
                        ->count();

        $wishlist_status = $wishlist_details ? YES : NO;

        return $wishlist_status;

        
    }

    /**
     *
     * @method history_status()
     *
     * @uses used to get the wishlist status of the video
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $sub_profile_id
     *
     * @return boolean 
     */
    public static function history_status($admin_video_id,$sub_profile_id) {

        $history_details = UserHistory::where('admin_video_id' , $admin_video_id)->where('sub_profile_id' , $sub_profile_id)->count();

        $history_status = $history_details ? YES : NO;

        return $history_status;

    }

    /**
     *
     * @method like_status()
     *
     * @uses used to get the like status of the video
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $sub_profile_id
     *
     * @return boolean 
     */
    public static function like_status($admin_video_id,$sub_profile_id) {

        $like_video_details = LikeDislikeVideo::where('admin_video_id' , $admin_video_id)->where('sub_profile_id' , $sub_profile_id)->first();

        $like_status = NO;

        if($like_video_details) {

            if($like_video_details->like_status == DEFAULT_TRUE) {

                $like_status = YES;

            } else if($like_video_details->dislike_status == DEFAULT_TRUE){

                $like_status = -1;

            }
        
        }

        return $like_status;

    }

    /**
     *
     * @method likes_count()
     *
     * @uses used to get the like status of the video
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $sub_profile_id
     *
     * @return boolean 
     */
    public static function likes_count($admin_video_id) {

        $likes_count = LikeDislikeVideo::where('admin_video_id' , $admin_video_id)->where('like_status' , DEFAULT_TRUE)->count();

        return $likes_count ?: 0;

    }

    /**
     *
     * @method download_button_status()
     *
     * @uses used to get the like status of the video
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $sub_profile_id
     *
     * @return boolean 
     */
    public static function download_button_status($admin_video_id , $user_id, $admin_video_download_status, $user_type, $is_ppv) {

        $offline_video_details = OfflineAdminVideo::where('admin_video_id' , $admin_video_id)
                                        ->where('user_id', $user_id)
                                        ->first();

        $download_button_status = DOWNLOAD_BTN_DONT_SHOW;

        if($offline_video_details) {

            if(in_array($offline_video_details->download_status, [DOWNLOAD_INITIATE_STAUTS, DOWNLOAD_PROGRESSING_STAUTS])) {

                $download_button_status = DOWNLOAD_BTN_ONPROGRESS;

            } elseif ($offline_video_details->download_status == DOWNLOAD_COMPLETE_STAUTS) {
                
                $download_button_status = DOWNLOAD_BTN_COMPLETED;

            } elseif (in_array( $offline_video_details->download_status, [DOWNLOAD_PAUSE_STAUTS, DOWNLOAD_CANCEL_STAUTS, DOWNLOAD_DELETE_STAUTS]) && $admin_video_download_status == DOWNLOAD_ON && $user_type == SUBSCRIBED_USER && $is_ppv == NO) {
                
                $download_button_status = DOWNLOAD_BTN_SHOW;

            }
        
        } else {

            if($admin_video_download_status == DOWNLOAD_ON) {

                if($user_type == NON_SUBSCRIBED_USER) {

                    $download_button_status == DOWNLOAD_BTN_USER_NEEDS_TO_SUBSCRIBE;

                } elseif ($is_ppv == YES) {

                    $download_button_status = DOWNLOAD_BTN_USER_NEEDS_PAY_FOR_VIDEO;

                } else {

                    $download_button_status = DOWNLOAD_BTN_SHOW;
                }
            }

        }

        return $download_button_status;

    }

    /**
     *
     * @method download_button_status()
     *
     * @uses used to get the like status of the video
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $sub_profile_id
     *
     * @return boolean 
     */
    public static function download_status_text($offline_video_status) {

        $text = "";

        switch ($offline_video_status) {

            case DOWNLOAD_INITIATE_STAUTS:
                    
                $text = tr('download_initiated');

                break;

            case DOWNLOAD_PROGRESSING_STAUTS:
                    
                $text = tr('download_progressing');

                break;

            case DOWNLOAD_PAUSE_STAUTS:
                    
                $text = tr('download_paused');

                break;

            case DOWNLOAD_COMPLETE_STAUTS:
                    
                $text = tr('download_completed');

                break;

            case DOWNLOAD_CANCEL_STAUTS:
                    
                $text = tr('download_cancelled');

                break;
            
            default:
                $text = "";

                break;
        }

        return $text;

    }  

    /**
     *
     * @method download_button_status()
     *
     * @uses used to get the like status of the video
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $sub_profile_id
     *
     * @return boolean 
     */
    public static function get_video_resolutions($request) {

        $resolutions_data = $download_urls = [];

        $video_resolutions = explode(',', $request->video_resolutions);

        $video_resize_path = $request->video_resize_path ? explode(',', $request->video_resize_path) : [];

        if($video_resize_path && $video_resolutions) {

            foreach ($video_resolutions as $key => $value) {
            
                $download_url = new \stdClass();

                $download_url->title = $value;

                $download_url->type = "MP4";

                $video_link = $normal_converted_vod_video = $download_url->link = isset($video_resize_path[$key]) ? $video_resize_path[$key] : $admin_video_details->video;

                $request->video = $video_link;

                $normal_converted_vod_video = self::get_streaming_link_video($video_link, $request); 

                $resolutions_data[$value] = $normal_converted_vod_video;
                
                array_push($download_urls, $download_url);

            }

        }

        $resolutions_data['original'] = self::get_streaming_link_video($request->video, $request);

        return [$resolutions_data, $download_urls];

    }  


    /**
     *
     * @method get_rtmp_link_video()
     *
     * @uses used to convert the normal video to RTMP Video
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param string $video_link
     *
     * @return string $normal_converted_video 
     */
    public static function get_streaming_link_video($video_link, $request) {

        // Check video video_type

        if($request->video_type == VIDEO_TYPE_YOUTUBE) {

            if ($request->device_type != DEVICE_WEB) {

                $normal_converted_video = get_api_youtube_link($video_link);

            } else {

                $normal_converted_video = get_youtube_embed_link($video_link);

            }

        } elseif ($request->video_type == VIDEO_TYPE_UPLOAD && $request->video_upload_type == VIDEO_UPLOAD_TYPE_DIRECT) {

            if(check_valid_url($request->video)) {

                $stream_url_rtmp_or_hls = $request->device_type == DEVICE_IOS ? Setting::get('HLS_STREAMING_URL') : Setting::get('streaming_url');

                if($stream_url_rtmp_or_hls) {

                    $normal_converted_video = $stream_url_rtmp_or_hls.get_video_end($request->video);
                }

            }
            
        } else {

            $normal_converted_video = $request->video;
        }

        return $normal_converted_video;

    }

    /**
     *
     * @method get_page_type_query()
     *
     * @uses based on the page type, change the query
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param Request $request
     *
     * @param $base_query
     *
     * @return $base_query 
     */
    public static function get_page_type_query($request, $base_query) {

        if($request->page_type == API_PAGE_TYPE_SERIES) {

            $base_query  = $base_query->where('video_tapes.genre_id', "!=", 0);

        } elseif($request->page_type == API_PAGE_TYPE_FLIMS) {

            $base_query  = $base_query->where('video_tapes.genre_id', "=", 0);

        } elseif($request->page_type == API_PAGE_TYPE_KIDS) {

            $base_query  = $base_query->where('video_tapes.is_kids_video', "=", KIDS_SECTION_YES);

        } elseif($request->page_type == API_PAGE_TYPE_CATEGORY) {

            $base_query  = $base_query->where('video_tapes.category_id', $request->category_id);

        } elseif($request->page_type == API_PAGE_TYPE_SUB_CATEGORY) {

            $base_query  = $base_query->where('video_tapes.sub_category_id', $request->sub_category_id);

        } elseif($request->page_type == API_PAGE_TYPE_GENRE) {

            $base_query  = $base_query->where('video_tapes.genre_id', $request->genre_id);

        }

        return $base_query;

    }

    /**
     *
     * @method get_ppv_page_type()
     *
     * @uses based on the page type, change the query
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param Request $request
     *
     * @param $base_query
     *
     * @return $base_query 
     */
    public static function get_ppv_page_type($admin_video_details, $user_type, $is_pay_per_view = NO) {

        if($is_pay_per_view == NO) {

            $data['ppv_page_type'] = PPV_PAGE_TYPE_NONE;

            $data['ppv_page_type_content'] = [];

            return json_decode(json_encode($data));

        }

        $ppv_page_type = PPV_PAGE_TYPE_INVOICE; $data = $ppv_page_type_content = [];

        if($admin_video_details->type_of_user == NORMAL_USER || $admin_video_details->type_of_user == BOTH_USERS) {

            if($user_type == NON_SUBSCRIBED_USER) {

                $ppv_page_type = PPV_PAGE_TYPE_CHOOSE_SUB_OR_PPV;

                $subscription_data['title'] = tr('api_choose_subscription');

                $subscription_data['description'] = tr('api_click_here_to_subscribe');

                $subscription_data['type'] = SUBSCRIPTION;

                $ppv_data['title'] = tr('api_ppv_title', 'Recurring');

                $ppv_data['description'] = tr('api_click_here_to_ppv', type_of_subscription($admin_video_details->type_of_subscription));

                $ppv_data['type'] = PPV;

                $ppv_page_type_content = json_decode(json_encode([$subscription_data, $ppv_data]));

            }

        }

        $data['ppv_page_type'] = $ppv_page_type;

        $data['ppv_page_type_content'] = $ppv_page_type_content;

        return json_decode(json_encode($data));


    }

    /**
     *
     * @method get_ppv_page_type()
     *
     * @uses based on the page type, change the query
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param Request $request
     *
     * @param $base_query
     *
     * @return $base_query 
     */
    public static function videoPlayDuration($admin_video_id, $sub_profile_id) {

        $continue_watching_video_details = ContinueWatchingVideo::where('admin_video_id', $admin_video_id)
                                        ->where('sub_profile_id', $sub_profile_id)
                                        ->first();

        return $continue_watching_video_details;

    }

    /**
     *
     * @method getFlagVideos()
     *
     * @uses based on the page type, change the query
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param Request $request
     * 
     * @return $base_query 
     */
    public static function getFlagVideos($user_id) {

        // Load Flag videos based on logged in user id
        
        $video_tape_ids = Flag::where('flags.user_id', $user_id)
                            ->leftJoin('video_tapes' , 'flags.video_tape_id' , '=' , 'video_tapes.id')
                            ->where('video_tapes.is_approved' , ADMIN_VIDEO_APPROVED_STATUS)
                            ->where('video_tapes.status' , USER_VIDEO_APPROVED_STATUS)
                            ->pluck('video_tape_id')
                            ->toArray();

        // Return array of id's
        return $video_tape_ids;
    }


}
