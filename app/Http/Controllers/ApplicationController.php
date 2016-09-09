<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Requests;

use App\Helpers\Helper;

use App\Category;

use App\SubCategory;

use App\SubCategoryImage;

use App\Genre;

use App\AdminVideo;

use Validator;

use Hash;

use Mail;

use Auth;

use Redirect;

use Setting;

use Log;

use Elasticsearch\ClientBuilder;


class ApplicationController extends Controller {


    public function select_genre(Request $request) {
        
        $id = $request->option;

        $genres = Genre::where('sub_category_id', '=', $id)
                        ->where('is_approved' , 1)
                          ->orderBy('name', 'asc')
                          ->get();

        return response()->json($genres);
    }

    public function select_sub_category(Request $request) {
        
        $id = $request->option;

        $subcategories = Subcategory::where('category_id', '=', $id)
                        ->where('is_approved' , 1)
                          ->orderBy('name', 'asc')
                          ->get();

        return response()->json($subcategories);
    }

    public function cron_publish_video(Request $request) {
        
        Log::info('cron_publish_video');

        $videos = AdminVideo::where('publish_time' ,'<=' ,date('Y-m-d H:i:s'))
                        ->where('status' , 0)
                        ->update(['status' , 1]);
    }

    public function addIndex() {

        $params = array();

        $client = ClientBuilder::create()->build();

        $params['body']  = array(
          'id' => 0
        );

        $params['index'] = 'live-streaming';
        $params['type']  = 'live-streaming';
        $params['id'] = 'live-streaming';

        $result = $client->index($params);
    }

    public function add_value_index() {

        $client = ClientBuilder::create()->build();

        $params = array();

        $params['body']  = array(
          'video_id' => $video->id,
          'title' => $video->title,
          'description' => $video->description
        );

        $params['index'] = 'start_streaming';
        $params['type']  = 'streaming_type';
        $params['id'] = 'streaming_id';

        $result = $client->index($params);

        dd($result);
    }

    public function addAllVideoToEs() {

        $videos = AdminVideo::where('is_approved' , 1)->get();

        if(count($videos)) {

            foreach ($videos as $video) {

                $params = array();

                $client = ClientBuilder::create()->build();

                $params['body']  = array(
                  'id' => $video->id,
                  'title' => $video->title,
                  'description' => $video->description,
                );

                $params['index'] = 'live-streaming';
                $params['type']  = 'live-streaming';
                $params['id'] = $video->id;

                $result = $client->index($params);

                Log::info("Result Elasticsearch ".print_r($result ,true));

            }

        }
    }

    public function test() {
        return view('errors.404');
    }

    public function search_video(Request $request) {

        $validator = Validator::make(
            $request->all(),
            array(
                'term' => 'required',
            ),
            array(
                'exists' => 'The :attribute doesn\'t exists',
            )
        );
    
        if ($validator->fails()) {

            $error_messages = implode(',', $validator->messages()->all());
            $response_array = array('success' => false, 'error' => Helper::get_error_message(101), 'error_code' => 101, 'error_messages'=>$error_messages);

            return false;
        
        } else {

            $q = $request->term;

            $items = array();
            
            $results = Helper::search_video($q);

            foreach ($results as $i => $key) {

                $check = $i+1;

                if($check <=10) {
 
                    array_push($items,$key->title);

                } if($check == 10 ) {
                    array_push($items,"View All" );
                }
            }

            return response()->json($items);
        }     
    }

    public function search_all(Request $request) {

        $validator = Validator::make(
            $request->all(),
            array(
                'key' => 'required',
            ),
            array(
                'exists' => 'The :attribute doesn\'t exists',
            )
        );
    
        if ($validator->fails()) {

            $error_messages = implode(',', $validator->messages()->all());
            $response_array = array('success' => false, 'error' => Helper::get_error_message(101), 'error_code' => 101, 'error_messages'=>$error_messages);
        
        } else {

            $q = $request->key;

            $videos = Helper::search_video($q,1);

            return view('user.search-result')->with('key' , $q)->with('videos' , $videos)->with('page' , "")->with('subPage' , "");
        }     
    }

}