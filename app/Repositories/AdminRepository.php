<?php


namespace App\Repositories;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Validator;
use Hash;
use Log;
use DB;
use Exception;
use App\VideoAd;
use App\AdsDetail;
use App\VideoTape;

class AdminRepository {

    public static function save_ad($request) {

    	try {

            // dd($request->all());

            DB::beginTransaction();

            $model = ($request->has('video_ad_id')) ? VideoAd::find($request->video_ad_id) : new VideoAd();

            $model->video_tape_id = $request->has('video_tape_id') ? $request->video_tape_id : $model->video_tape_id;

            $model->status = DEFAULT_TRUE;

            $ad_types = [];

            if ($model->save()) {

                if($request->has('pre_ad_type')) {

                    if($request->pre_ad_type == PRE_AD) {

                        if(empty($request->pre_ad_time)) {

                            $p_ad = AdsDetail::find($request->pre_ad_type_id);

                            if($p_ad) {

                                Helper::delete_picture($p_ad->file, "/uploads/ad/");

                                $p_ad->delete();
                                
                            }

                        } else {

                            $ad_types[] = PRE_AD;

                            $pre_ad_model = ($request->has('pre_ad_type_id')) ? AdsDetail::find($request->pre_ad_type_id) : new AdsDetail;

                            $pre_ad_model->ads_id = $model->id;

                            $pre_ad_model->ad_type = PRE_AD;

                            $pre_ad_model->video_time = "00:00:00";

                            $pre_ad_model->ad_time = $request->has('pre_ad_time') ? $request->pre_ad_time : $pre_ad_model->pre_ad_time;

                            if ($request->pre_ad_file) {

                                if($request->has('pre_ad_type_id')) {

                                    Helper::delete_picture($pre_ad_model->file, "/uploads/ad/");

                                }

                                $pre_ad_model->file = Helper::normal_upload_picture($request->pre_ad_file, "/uploads/ad/");
                            }

                            if ($pre_ad_model->save()) {


                            } else {

                                throw new Exception(tr('something_error'));

                            }
                        }

                    }

                }


                if($request->has('post_ad_type')) {

                    if($request->post_ad_type == POST_AD) {

                        if(empty($request->post_ad_time)) {

                            $post_ad = AdsDetail::find($request->post_ad_type_id);

                            if($post_ad) {

                                Helper::delete_picture($post_ad->file, "/uploads/ad/");

                                $post_ad->delete();
                                
                            }

                        } else {

                            $ad_types[] = POST_AD;

                            $post_ad_model = ($request->has('post_ad_type_id')) ? AdsDetail::find($request->post_ad_type_id) : new AdsDetail;

                            $post_ad_model->ads_id = $model->id;

                            $post_ad_model->ad_type = POST_AD;

                            $post_ad_model->video_time = $model->getVideoTape ? $model->getVideoTape->duration : '00:00:00';

                            $post_ad_model->ad_time = $request->has('post_ad_time') ? $request->post_ad_time : $post_ad_model->post_ad_time;

                            if ($request->post_ad_file) {

                                if($request->has('post_ad_type_id')) {

                                    Helper::delete_picture($post_ad_model->file, "/uploads/ad/");

                                }

                                $post_ad_model->file = Helper::normal_upload_picture($request->post_ad_file, "/uploads/ad/");
                            }

                            if ($post_ad_model->save()) {


                            } else {


                                throw new Exception(tr('something_error'));
                                
                            }

                        }

                    }

                }


                if($request->has('between_ad_type')) {

                    $b_type = DEFAULT_FALSE;

                    foreach ($request->between_ad_type as $key => $value) {
                   
                        if($value == BETWEEN_AD) {

                            $delete = 0;

                            if($request->has('between_ad_time')) {

                                $b_time = $request->between_ad_time[$key] ? $request->between_ad_time[$key] : '';

                                if(empty($b_time)) {

                                    $b_ad = AdsDetail::find($request->between_ad_type_id[$key]);

                                    if($b_ad) {

                                        Helper::delete_picture($b_ad->file, "/uploads/ad/");

                                        $b_ad->delete();

                                        $delete = 1;

                                    }

                                }

                            }

                            if($delete == 0) {

                                $between_ad_model = ($request->has('between_ad_type_id')) ? AdsDetail::find($request->between_ad_type_id[$key]) : new AdsDetail;

                                $between_ad_model->ads_id = $model->id;

                                $between_ad_model->ad_type = BETWEEN_AD;

                                $time = $request->has('between_ad_video_time') ? $request->between_ad_video_time[$key] : $between_ad_model->video_time;

                                $expTime = explode(':', $time);

                               //  dd($time);

                                if (count($expTime) == 3) {

                                    $between_ad_model->video_time = $time;

                                }

                                if (count($expTime) == 2) {

                                     $between_ad_model->video_time = "00:".$expTime[0].":".$expTime[1];
                                }


                                $between_ad_model->ad_time = $request->has('between_ad_time') ? $request->between_ad_time[$key] : $between_ad_model->between_ad_time;

                                if ($request->between_ad_file) {


                                    if($request->between_ad_file[$key] != null) {

                                        if($request->has('between_ad_type_id')) {

                                            Helper::delete_picture($between_ad_model[$key]->file, "/uploads/ad/");

                                        }

                                        $between_ad_model->file = Helper::normal_upload_picture($request->between_ad_file[$key], "/uploads/ad/");

                                    }
                                }

                                if ($between_ad_model->save()) {

                                    $b_type = DEFAULT_TRUE;

                                } else {


                                    throw new Exception(tr('something_error'));
                                    
                                }
                            }

                        }
                    }

                    if($b_type) {

                        $ad_types[] = BETWEEN_AD;

                    }

                }

                $model->types_of_ad = ($ad_types) ? implode(',', $ad_types) : $model->types_of_ad;

                if ($model->save()) {

                    $video_tape = VideoTape::find($request->video_tape_id);

                    $video_tape->ad_status = DEFAULT_TRUE;

                    if ($video_tape->save()) {


                    } else {

                        throw new Exception(tr('something_error'));

                    }

                } else {

                    throw new Exception(tr('something_error'));

                }

            } else {

                throw new Exception(tr('something_error'));

            }

            DB::commit();

            $response_array = ['success' => true,'message' => ($request->video_ad_id) ? tr('ad_update_success') : tr('ad_create_success'), 'data'=>$model];

        } catch(Exception $e) {

            DB::rollBack();

            $response_array = ['success' => false,'message' => $e->getMessage()];

        }

        return response()->json($response_array, 200);
    }


    public static function ad_index() {

        $model = VideoAd::with('getVideoTape')->get();

        return response()->json($model, 200);

    }


    public static function ad_view($request) {

        $model = VideoAd::with('getVideoTape')->find($request->id);

        return response()->json($model);

    }
}