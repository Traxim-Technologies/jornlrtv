@extends('layouts.admin')

@section('title', tr('videos'))

@section('content-header')

{{ tr('videos') }} 

@endsection

@section('styles')

<style type="text/css">
video {
    width: 100%;
}
</style>
@endsection

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('admin.videos.index')}}"><i class="fa fa-video-camera"></i> {{tr('videos')}}</a></li>
    <li class="active"><i class="fa fa-video-o"></i> {{tr('view_video')}}</li>
@endsection

@section('content')

    @include('notification.notify')

    <div class="row">
        <div class="col-xs-12">

            <div class="panel">
                
                <div class="panel-body">

                    <div class="post">
                        <div class="user-block">

                            <img class="img-circle img-bordered-sm" src="{{$data->user ?  $data->user->chat_picture : asset('placeholder.png') }}" alt="User Image">

                            <span class="username">
                                <a href="{{$data->user ? route('admin.view.user' , $data->user->id ): '#' }}">{{$data->user ? $data->user->name : ""}}</a>
                                <!-- <a href="#" class="pull-right btn-box-tool"><i class="fa fa-times"></i></a> -->
                            </span>

                            <span class="description">{{$data->created_at->diffForHumans()}}</span>

                        </div>


                        <div class="row margin-bottom">

                            <div class="col-sm-6">

                                @if(!$data->is_streaming || $data->status) 

                                     <img class="img-responsive" src="{{$data->snapshot}}" alt="Photo">

                                @else

                                <div ng-app="liveApp">

                                    <div ng-controller="streamCtrl" ng-cloak ng-init="initRoom({{$data->id}}, '{{$data->virtual_id}}')">

                                    <!-- <p class="text-red">Click On Image to See the streaming Video</p> -->

                                        <div class="show-model">
                                          <!--<img src="/images/img1.jpg" class="img-response" ng-hide="isStreaming">-->
                                          <div id="videos-container" room="{{$data->id}}">
                                            
                                            <div id="loader_btn" style="margin-left: 30%">
                                                <p>Video Loading....</p>
                                            </div>

                                          </div>

                                        </div>

                                    </div>
                                </div>

                                @endif
                            </div>

                            <div class="col-sm-6">

                                <div class="row">
                                    <div class="col-xs-12">

                                        <div class="header">

                                            <h3><b>{{tr('title')}}</b></h3>

                                            <h4>{{$data->title}}</h4>

                                        </div>

                                    </div>

                                    <div class="col-sm-6">

                                        <div class="header">

                                            <h4><b>{{tr('payment')}}</b></h4>

                                            @if($data->payment)

                                                <label class="text-red">{{tr('payment')}}</label>

                                            @else
                                                <label class="text-yellow">{{tr('free')}}</label>
                                            @endif

                                        </div>

                                    </div>

                                   

                                    <div class="col-sm-6">

                                        <div class="header">

                                            <h4><b>{{tr('is_streaming')}}</b></h4>

                                            @if($data->is_streaming && !$data->status)

                                                <label class="text-green"><b>{{tr('yes')}}</b></label>

                                            @else
                                                <label class="text-navyblue"><b>{{tr('no')}}</b></label>
                                            @endif

                                        </div>

                                    </div>

                                    @if($data->amount > 0)

                                    <div class="col-lg-6">

                                        <div class="header">

                                            <h4><b>{{tr('amount')}}</b></h4>

                                            $ {{$data->amount}}

                                        </div>
                                    
                                    </div>

                                    @endif

                                    


                                     <div class="col-lg-6">

                                        <div class="header">

                                            <h4><b>{{tr('viewers_cnt')}}</b></h4>


                                            <i class="fa fa-eye text-green"></i> {{$data->viewer_cnt}}

                                        </div>
                                    
                                    </div>



                                     <div class="col-lg-6">

                                        <div class="header">

                                            <h4><b>{{tr('admin_commission')}}</b></h4>


                                            <i class="fa fa-money text-green"></i> $ {{admin_commission($data->id)}}

                                        </div>
                                    
                                    </div>

                                    <div class="col-lg-6">

                                        <div class="header">

                                            <h4><b>{{tr('user_commission')}}</b></h4>


                                            <i class="fa fa-money text-green"></i> $ {{user_commission($data->id)}}

                                        </div>
                                    
                                    </div>

                                    <div class="col-sm-12">

                                        <h3><b>{{tr('description')}}</b></h3>

                                        <p>{{$data->description}}</p>

                                    </div>
                            </div>
                        
                        </div>


                       
                    </div>
                
                </div>

            </div>

        </div>
    </div>
@endsection

@if ($data->is_streaming && !$data->status)

@section('scripts')

<!-- <script src="{{asset('common/js/jQuery.js')}}"></script> -->
<script src="{{asset('lib/angular/angular.min.js')}}"></script>
<script src="{{asset('lib/angular-socket-io/socket.min.js')}}"></script>
<script src="{{asset('lib/socketio/socket.io-1.4.5.js')}}"></script>
<script src="{{asset('lib/rtc-multi-connection/RTCMultiConnection.js')}}"></script>

<script src="{{asset('jwplayer/jwplayer.js')}}"></script>

<script type="text/javascript">


var url = "<?= url('/');?>";

var video_details = <?= $data; ?>;

var appSettings = <?= json_encode([
                'SOCKET_URL' => Setting::get('SOCKET_URL'),
                'CHAT_ROOM_ID' => isset($data) ? $data->id : null,
                'BASE_URL' => Setting::get('BASE_URL'),
                'TURN_CONFIG' => [],
                'TOKEN' => null,
                'USER' => null,
            ]); ?>;

var liveAppCtrl = angular.module('liveApp', [
  'btford.socket-io',
], function ($interpolateProvider) {
  $interpolateProvider.startSymbol('<%');
  $interpolateProvider.endSymbol('%>');
})
.constant('appSettings', appSettings)
.constant('url',url);
liveAppCtrl
    .run(['$rootScope',
        '$window',
        '$timeout',
        function ($rootScope,$window, $timeout) {
            
            $rootScope.appSettings = appSettings;

            $rootScope.videoDetails = video_details;

            console.log($rootScope.videoDetails);
        }
]);
    
</script>
<!-- <script src="{{asset('common/js/factory.js')}}"></script> -->
<script src="{{asset('lib/streamController.js')}}"></script>

@endsection

@endif