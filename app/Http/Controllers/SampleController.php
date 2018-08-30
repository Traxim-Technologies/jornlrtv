<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class SampleController extends Controller
{
    public function angelo_in_app_purchase(Request $request) {

    	\Log::info(print_r($reqeust->all() , true));

    	return response()->json(['success' => true]);
    }

    public function video_notification(Request $request) {
    	return view('emails.video_notification');
    }

    public function upload_video(Request $request) {
    	return view('user.videos.upload_video');
    }

}
