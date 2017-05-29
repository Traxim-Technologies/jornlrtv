
<!-- Main Video Configuration -->

<div class="embed-responsive embed-responsive-16by9" id="main_video_setup_error" style="display: none;">
    <img src="{{asset('error.jpg')}}" class="error-image" alt="{{Setting::get('site_name')}} - Main Video">
</div>

<div class="embed-responsive embed-responsive-16by9" id="main_video_ad" style="display: none">
    <img src="" class="error-image" alt="{{Setting::get('site_name')}} - Main Video Ad" id="ad_image">
    <div class="ad_progress">
   		<div id="timings">{{tr('ad') }} : <span class="seconds"></span></div>
    	<div class="clearfix"></div>
	    <div class="progress-bar-div">
		    <span class="progress-bar-fill-div" style="width: 0%"></span>
		</div>
	</div>
</div>

<div id="main-video-player">
	
</div>


@if(!check_valid_url($video->video))
    <div class="embed-responsive embed-responsive-16by9" style="display:none" id="main_video_error">
        <img src="{{asset('error.jpg')}}" class="error-image" alt="{{Setting::get('site_name')}} - Main Video">
    </div>
@endif


<div class="embed-responsive embed-responsive-16by9" id="flash_error_display" style="display: none;">
   <div style="width: 100%;background: black; color:#fff;height:350px;">
   		 <div style="text-align: center;padding-top:25%">Flash is missing. Download it from <a target="_blank" href="http://get.adobe.com/flashplayer/" class="underline">Adobe</a>.</div>
   </div>
</div>


<!-- Trailer Video Configuration END -->