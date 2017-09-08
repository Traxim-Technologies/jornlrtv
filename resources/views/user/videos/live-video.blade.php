@extends( 'layouts.user' )

@section('styles')

<style type="text/css">
video {
	
	width: 100%;

	height: 100%;	
}
</style>
@endsection

@section('content')

<div class="y-content">

	<div class="row content-row">

		@include('layouts.user.nav')

		<div class="page-inner col-sm-9 col-md-10">

			@include('notification.notify')

			<div class="slide-area recom-area abt-sec" ng-app="liveApp" >

				<div class="row live_video">

					<div class="col-lg-8" ng-controller="streamCtrl" ng-cloak ng-init="initRoom({{$data->id}}, '{{$data->virtual_id}}')">

						<div class="live_img" id="videos-container" room="{{$data->id}}">
							<!-- <img src="{{asset('images/mobile-camera.jpg')}}" width="100%" height="400px"> -->

							<img src="{{asset('images/preview_img.jpg')}}" width="100%" id="default_image">
							

							<div class="loader_img" id="loader_btn" style="display: none;"><img src="{{asset('images/loader.svg')}}"/></div>

						</div>

						<hr>

						<div class="pull-left">						

							<button class="btn btn-sm btn-primary text-uppercase">
								 @if(round($data->amount) > 0) 

                                    {{tr('paid')}} - ${{$data->amount}} 

                                @else 

                                {{tr('free')}} 

                                @endif
							</button>

							<button class="btn btn-sm btn-success text-uppercase">
								<i class="fa fa-eye"></i>&nbsp;<span id='viewers_cnt'>{{$data->viewer_cnt}}</span> {{tr('views')}}
							</button>

						</div>

						@if (Auth::check())

							@if($data->user_id == Auth::user()->id)
							<div class="pull-right">

								<a href="{{route('user.live_video.stop_streaming',array('id'=>$data->id))}}" class="btn btn-sm btn-danger">{{tr('stop')}}</a>

							</div>

							@endif

						@endif

						<div class="clearfix"></div>

						<h4>{{$data->title}}</h4>

						<div class="small" style="color:#777">{{tr('streaming_by')}} {{$data->user ? $data->user->name : ''}} {{tr('from')}} @if (Auth::check()) {{convertTimeToUSERzone($data->created_at, Auth::user()->timezone, 'd-m-Y h:i A')}} @else {{convertTimeToUSERzone($data->created_at, '', 'd-m-Y h:i A')}} @endif</div>

						<br>

						<p>{{$data->description}}</p>



					</div>

					<div class="col-lg-4" ng-controller="chatBarCtrl">

						<div class="chat_box">

							<div class="chat-header">

								<i class="fa fa-comments-o fa-2x"></i>&nbsp;&nbsp; {{tr('group_chat')}}

							</div>

							<div class="chat-content">

								<div id="chat-box">

									@if(count($comments) > 0)

									@foreach($comments as $comment)

										<?php $img =  ($comment->getUser) ? $comment->getUser->chat_picture : $comment->getViewUser->chat_picture ;

											$name = ($comment->getUser) ? $comment->getUser->name : $comment->getViewUser->name;

											$uid = ($comment->getUser)? $comment->user_id : $comment->live_video_viewer_id;
										?>

										<div class="item">


							                <div class="col-lg-2 col-md-2 col-xs-2 col-sm-2" style="padding: 0">

							                	<a target="_blank" href="{{route('user.profile', array('id'=>$uid))}}" title="{{$name}}">  
							                		<img src="{{$img}}" alt="user image" class="chat_img">
							                	</a>

							                </div>
							                <div class="message col-lg-10 col-md-10 col-xs-10 col-sm-10">

							                  <a href="{{route('user.profile', array('id'=>$uid))}}" class="name clearfix" style="text-decoration: none">
							                    <small class="text-muted pull-left">{{$name}}</small>
							                    <small class="text-muted pull-right"><i class="fa fa-clock-o"></i> {{$comment->created_at->diffForHumans()}}</small>
							                  </a>

							                 	<div>{{$comment->message}}</div>
							                </div>

							                <div class="clearfix"></div>

							            </div>

						            @endforeach

						            @endif
					            

					            </div>

								<div class="chat_footer">

									<div class="input-group">

										@if(Auth::check())
							                <input class="form-control chat_form_input" placeholder="{{tr('comment_here')}}" required id="chat-input">

							                <div class="input-group-btn">
							                  <button type="button" class="btn btn-danger chat_send_btn" id="chat-send"><i class="fa fa-send"></i></button>
							                </div>

						                @else

						                	 <input class="form-control chat_form_input" placeholder="{{tr('comment_here')}}" required disabled>

							                <div class="input-group-btn">
							                  <button type="button" class="btn btn-danger chat_send_btn" disabled><i class="fa fa-send"></i></button>
							                </div>

						                @endif
						            </div>
								</div>

							</div>


						</div>

					</div>

					<div class="clearfix"></div>


					<div class="col-lg-12">
							
						<div class="row slide-area recom-area">

		                    <div class="box-head recom-head">
		                        <h3 style="padding: 10px 0px;">{{tr('live_videos')}}</h3>
		                    </div>

		                    @if(count($videos) > 0)

		                        <div class="recommend-list row" style="padding: 10px 0px;">

		                            @foreach($videos as $video)
		                                <div class="slide-box recom-box" style="margin: 0.5% 0%;">
		                                    <div class="slide-image recom-image">

		                                        <?php 

		                                        $userId = Auth::check() ? Auth::user()->id : '';

		                                        $url = ($video->amount > 0) ? route('user.payment_url', array('id'=>$video->id, 'user_id'=>$userId)): route('user.live_video.start_broadcasting' , array('id'=>$video->unique_id,'c_id'=>$video->channel_id));


		                                        ?>
		                                        <a href="{{$url}}">

		                                            <div class="bg_img_video" style="background-image:url({{$video->snapshot}})"></div>

		                                        </a>

		                                        <div class="video_duration text-uppercase">
		                                            @if($video->amount > 0) 

		                                                {{tr('paid')}} - ${{$video->amount}} 

		                                            @else {{tr('free')}} @endif
		                                        </div>
		                                    </div><!--end of slide-image-->

		                                    <div class="video-details recom-details">
		                                        <div class="video-head">
		                                            <a href="{{$url}}">

		                                            {{$video->title}}

		                                            </a>
		                                        </div>

		                                        <span class="video_views">
		                                            <i class="fa fa-eye"></i> {{$video->viewer_cnt}} {{tr('views')}} <b>.</b> 
		                                            {{$video->created_at->diffForHumans()}}
		                                        </span> 
		                                    </div><!--end of video-details-->
		                                </div><!--end of slide-box-->
		                            @endforeach
		                            

		                        </div>

		                    @else

		                         <div class="recommend-list row">
		                            <div class="slide-box recom-box"> {{tr('no_live_videos')}}</div>
		                        </div>

		                    @endif

		                    <!--end of recommend-list-->

		                     @if(count($videos) > 0)
		                        
		                        <div class="row">
		                            <div class="col-md-12">
		                                <div align="center" id="paglink"><?php echo $videos->links(); ?></div>
		                            </div>
		                        </div>

		                    @endif
		                </div>

					</div>

				</div>
				 
			</div>

		</div>

	</div>
</div>

@endsection


@section('scripts')

<!-- <script src="{{asset('common/js/jQuery.js')}}"></script> -->
<script src="{{asset('lib/angular/angular.min.js')}}"></script>
<script src="{{asset('lib/angular-socket-io/socket.min.js')}}"></script>
<script src="{{asset('lib/socketio/socket.io-1.4.5.js')}}"></script>
<script src="{{asset('lib/rtc-multi-connection/RTCMultiConnection.js')}}"></script>

<script type="text/javascript">





var appSettings = <?= $appSettings ?>;

var port_no = <?= $data->port_no; ?>;

var video_details = <?= $data; ?>;

var socket_url =  "<?= Setting::get('kurento_socket_url'); ?>";

var stop_streaming_url ="<?= route('user.live_video.stop_streaming', array('id'=>$data->id)) ?>";


/*$(window).on('beforeunload', function() {

		$.ajax({

			type : "get",

			url : stop_streaming_url,

			success : function (data) {

				console.log("success");
			}, 

			error : function (data) {
				console.log("success");


			}
		});

	window.location.href = stop_streaming_url;

      return 'Are you sure you want to leave?';
});
*/

/*
$(window).on('unload', function(){

	console.log(stop_streaming_url);

	$.ajax({

		type : "get",

		url : stop_streaming_url,

		success : function (data) {

			console.log("success");
		}, 

		error : function (data) {
			console.log("success");


		}
	});

});

 window.onbeforeunload = function(evt) {
            var message = 'Are you sure you want to leave?';
            if (typeof evt == 'undefined') {
                evt = window.event;
            }       
            if (evt) {
                evt.returnValue = message;
            }
            return message;
        } 
*/


var url = "<?= url('/');?>";

var liveAppCtrl = angular.module('liveApp', [
  'btford.socket-io',

], function ($interpolateProvider) {
  $interpolateProvider.startSymbol('<%');
  $interpolateProvider.endSymbol('%>');
})
.constant('appSettings', appSettings)
.constant('port_no', port_no)
.constant('socket_url', socket_url)
.constant('stop_streaming_url',stop_streaming_url)
.constant('url',url);

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

<script src="{{asset('assets/js/streamController.js')}}"></script>

@endsection
