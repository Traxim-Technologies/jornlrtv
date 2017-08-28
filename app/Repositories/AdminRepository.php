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
use App\AssignVideoAd;
use App\Language;

class AdminRepository {

    public static function save_ad($request) {

    	try {

            DB::beginTransaction();

            $model = ($request->has('video_ad_id')) ? VideoAd::find($request->video_ad_id) : new VideoAd();

            $newOne = ($model->id) ? DEFAULT_FALSE : DEFAULT_TRUE;

            $model->video_tape_id = $request->has('video_tape_id') ? $request->video_tape_id : $model->video_tape_id;

            $model->status = DEFAULT_TRUE;

            $ad_types = [];

            if ($model->save()) {

                if(!$request->has('pre_ad_type') && !empty($request->pre_ad_type_id)) {

                    $p_ad = AssignVideoAd::find($request->pre_ad_type_id);

                    if($p_ad) {

                        $p_ad->delete();
                        
                    }

                }

                if($request->has('pre_ad_type')) {

                    if($request->pre_ad_type == PRE_AD) {

                        if(empty($request->pre_ad_time)) {

                            $p_ad = AssignVideoAd::find($request->pre_ad_type_id);

                            if($p_ad) {

                                $p_ad->delete();
                                
                            }

                        } else {

                            $ad_types[] = PRE_AD;

                            $pre_ad_model = ($request->has('pre_ad_type_id')) ? AssignVideoAd::find($request->pre_ad_type_id) : new AssignVideoAd;

                            $pre_ad_model->ad_id = $request->pre_parent_ad_id;

                            $pre_ad_model->video_ad_id = $model->id;

                            $pre_ad_model->ad_type = PRE_AD;

                            $pre_ad_model->video_time = "00:00:00";

                            $pre_ad_model->ad_time = $request->has('pre_ad_time') ? $request->pre_ad_time : $pre_ad_model->pre_ad_time;

                            if ($pre_ad_model->save()) {


                            } else {

                                throw new Exception(tr('something_error'));

                            }
                        }

                    }

                }

                if(!$request->has('post_ad_type') && !empty($request->post_ad_type_id)) {

                    $post_ad = AssignVideoAd::find($request->post_ad_type_id);

                    if($post_ad) {

                        $post_ad->delete();
                        
                    }

                }


                if($request->has('post_ad_type')) {

                    if($request->post_ad_type == POST_AD) {

                        if(empty($request->post_ad_time)) {

                            $post_ad = AssignVideoAd::find($request->post_ad_type_id);

                            if($post_ad) {

                                $post_ad->delete();
                                
                            }

                        } else {

                            $ad_types[] = POST_AD;

                            $post_ad_model = ($request->has('post_ad_type_id')) ? AssignVideoAd::find($request->post_ad_type_id) : new AssignVideoAd;

                            $post_ad_model->ad_type = POST_AD;

                            $post_ad_model->ad_id = $request->post_parent_ad_id;

                            $post_ad_model->video_ad_id = $model->id;

                            $post_ad_model->video_time = $model->getVideoTape ? $model->getVideoTape->duration : '00:00:00';

                            $post_ad_model->ad_time = $request->has('post_ad_time') ? $request->post_ad_time : $post_ad_model->post_ad_time;


                            if ($post_ad_model->save()) {


                            } else {


                                throw new Exception(tr('something_error'));
                                
                            }

                        }

                    }

                }


                if($request->has('between_ad_type')) {


                    if(!$newOne) {

                        if(count($model->getBetweenAdDetails) > 0) {

                            foreach ($model->getBetweenAdDetails as $key => $value) {
                              
                                  if(!in_array($value->id, $request->between_ad_type_id)) {

                                        $value->delete();

                                  }

                            }
                        }

                    }

                    $b_type = DEFAULT_FALSE;

                    foreach ($request->between_ad_type as $key => $value) {
                   
                        if($value == BETWEEN_AD) {

                            $delete = 0;

                            if($request->has('between_ad_time')) {

                                $b_time = $request->between_ad_time[$key] ? $request->between_ad_time[$key] : '';

                                if(empty($b_time)) {

                                    $delete = 1;

                                    $b_ad = AssignVideoAd::find($request->between_ad_type_id[$key]);

                                    if($b_ad) {

                                        $b_ad->delete();

                                    }

                                }

                            }



                            if($delete == 0) {

                                $id = ($request->has('between_ad_type_id')) ? $request->between_ad_type_id[$key] : '';

                                $between_ad_model = ($id) ? AssignVideoAd::find($id) : new AssignVideoAd;

                                $between_ad_model->ad_type = BETWEEN_AD;

                                $between_ad_model->ad_id = $request->has('between_parent_ad_id') ? $request->between_parent_ad_id[$key] : $between_ad_model->ad_id;

                                $between_ad_model->video_ad_id = $model->id;

                                $time = $request->has('between_ad_video_time') ? $request->between_ad_video_time[$key] : $between_ad_model->video_time;

                                $expTime = explode(':', $time);

                                if (count($expTime) == 3) {

                                    $between_ad_model->video_time = $time;

                                }

                                if (count($expTime) == 2) {

                                     $between_ad_model->video_time = "00:".$expTime[0].":".$expTime[1];
                                }

                                $between_ad_model->ad_time = $request->has('between_ad_time') ? $request->between_ad_time[$key] : $between_ad_model->between_ad_time;

                              
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


/*    public static function ad_index() {

        $model = VideoAd::with('getVideoTape')->get();

        return response()->json($model, 200);

    }*/

    public static function ad_index() {

        $model = AdsDetail::orderBy('created_at', 'desc')->get();

        return response()->json($model, 200);

    }


    public static function ad_view($request) {

        $model = VideoAd::with('getVideoTape')->find($request->id);

        return response()->json($model);

    }

    public static function ad_save($request) {

        try {

            DB::beginTransaction();

             $validator = Validator::make( $request->all(),array(
                    'name' => 'required',
                    'ad_time' => 'required|integer',
                    'file' => 'mimes:jpeg,jpg,png',
                    'ad_url'=>'required|url|max:255'
                )
            );
            
            if($validator->fails()) {

                $error_messages = implode(',', $validator->messages()->all());
                
                throw new Exception($error_messages);

            } else {

                $model = ($request->has('id')) ? AdsDetail::find($request->id) : new AdsDetail();

                $model->status = DEFAULT_TRUE;

                $model->name = $request->has('name') ? $request->name : $model->name;

                $model->ad_time = $request->has('ad_time') ? $request->ad_time : $model->ad_time;

                $model->ad_url = $request->has('ad_url') ? $request->ad_url : $model->ad_url;

                if ($request->file) {

                    if($request->has('id')) {

                        Helper::delete_picture($model->file, "/uploads/ad/");

                    }

                    $model->file = Helper::normal_upload_picture($request->file, "/uploads/ad/");
                }

                if ($model->save()) {


                } else {

                    throw new Exception(tr('something_error'));

                }
            }

            DB::commit();

            $response_array = ['success' => true,'message' => ($request->id) ? tr('ad_update_success') : tr('ad_create_success'), 'data'=>$model];

        } catch(Exception $e) {

            DB::rollBack();

            $response_array = ['success' => false,'message' => $e->getMessage()];

        }

        return response()->json($response_array, 200);
    }


    public static function languages_save($request) {

        $validator = Validator::make($request->all(),[
                'folder_name' => 'required|max:4',
                'language'=>'required|max:64',
        ]);
        
        if($validator->fails()) {

            $error_messages = implode(',', $validator->messages()->all());

            return  ['success' => false , 'error' => $error_messages];

        } else {

            $model = ($request->id != '') ? Language::find($request->id) : new Language;

            $lang = ($request->id != '') ? $model->folder_name : '';

            $model->folder_name = $request->folder_name;

            $model->language = $request->language;

            $model->status = DEFAULT_TRUE;

            if($request->hasFile('file')) {

                // Read File Length

                $originallength = readFileLength(base_path().'/resources/lang/en/messages.php');

                $length = readFileLength($_FILES['file']['tmp_name']);

                if ($originallength != $length) {
                    return ['success' => false, 'error'=> Helper::get_error_message(162), 'error_code'=>162];
                }

                if ($model->id != '') {
                    $boolean = ($lang != $request->folder_name) ? DEFAULT_TRUE : DEFAULT_FALSE;

                    Helper::delete_language_files($lang, $boolean);
                }

                Helper::upload_language_file($model->folder_name, $request->file);

            } else {

                if($lang != $request->folder_name)  {
                    $current_path=base_path('resources/lang/'.$lang);
                    $new_path=base_path('resources/lang/'.$request->folder_name);
                    rename($current_path,$new_path);
                }
            }

            $model->save();

            if($model) {
                $response_array = ['success' => true, 'message'=> $request->id != '' ? tr('language_update_success') : tr('language_create_success')];
            } else {
                $response_array = ['success' => false , 'error' => tr('something_error')];
            }
        }
        return $response_array;
    }

    
}