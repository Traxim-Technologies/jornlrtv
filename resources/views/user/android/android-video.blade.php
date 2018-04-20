<!DOCTYPE html>
<html>

<head>
    <title>@if(Setting::get('site_name')) {{Setting::get('site_name') }} @else {{tr('site_name')}} @endif</title>

    <script src="{{asset('streamtube/js/jquery.min.js')}}"></script>


	<script src="{{asset('lib/angular/angular.min.js')}}"></script>
	<script src="{{asset('lib/angular-socket-io/socket.min.js')}}"></script>
	<script src="{{asset('lib/socketio/socket.io-1.4.5.js')}}"></script>
	<script src="{{asset('lib/rtc-multi-connection/RTCMultiConnection.js')}}"></script>

	<script type="text/javascript">

var appSettings = <?= $appSettings ?>;


var video_details = <?= $data; ?>;

var url = "<?= url('/');?>";

var live_user_id = "<?= Auth::check() ? Auth::user()->id : '' ?>";

var user_token = "<?= Auth::check() ? Auth::user()->token : '' ?>";

var is_vod = "<?= Setting::get('is_vod')?>";

var liveAppCtrl = angular.module('liveApp', [
  'btford.socket-io',

], function ($interpolateProvider) {
  $interpolateProvider.startSymbol('<%');
  $interpolateProvider.endSymbol('%>');
})
.constant('appSettings', appSettings)
.constant('url',url)
.constant('live_user_id',live_user_id)
.constant('user_token',user_token);

liveAppCtrl
    .run(['$rootScope',
        '$window',
        '$timeout',
        function ($rootScope,$window, $timeout) {
            
            $rootScope.appSettings = appSettings;

            $rootScope.videoDetails = video_details;
        }
]);
    
console.log(appSettings);

</script>

<script src="{{asset('assets/js/androidController.js')}}"></script>

</head>

<body>

<div class="slide-area recom-area abt-sec" ng-app="liveApp" >

	<div class="row live_video">

		<div class="col-lg-8" ng-controller="androidCtrl" ng-cloak ng-init="initRoom({{$data->id}}, '{{$data->virtual_id}}')">

			<div class="live_img" id="videos-container" room="{{$data->id}}" style="position: fixed;height: 100%;
			background-color: #000;width: 100%;top:0;left:0;">
				<!-- <img src="{{asset('images/mobile-camera.jpg')}}" width="100%" height="400px"> -->

				<!-- <img src="{{asset('images/preview_img.jpg')}}" width="100%" id="default_image"> -->
				<div style="background-image: url({{asset('images/mobile-camera.jpg')}});" style="background-size: cover;background-position: center;background-repeat: no-repeat;height: 100%;"></div>
				

				<div class="loader_img" id="loader_btn" style="display: none;"><img src="{{asset('images/loader.svg')}}"/></div>

			</div>



		</div>

		
	</div>
	 
</div>




</body>

</html>