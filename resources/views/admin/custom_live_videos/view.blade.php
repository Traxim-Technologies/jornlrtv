@extends('layouts.admin')

@section('title', tr('view_custom_live_video'))

@section('content-header', tr('view_custom_live_video'))

@section('styles')

<style>
hr {
    margin-bottom: 10px;
    margin-top: 10px;
}
</style>

@endsection

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('admin.custom.live')}}"><i class="fa fa-video-camera"></i> {{tr('custom_live_videos')}}</a></li>
    <li class="active">{{tr('view_custom_live_video')}}</li>
@endsection 

@section('content')

    <div class="row">

        @include('notification.notify')
        <div class="col-lg-12">
            <div class="box box-primary">
            <div class="box-header with-border">
                <div class='pull-left'>
                    <h3 class="box-title"> <b>{{$video->title}}</b></h3>
                    <br>
                    <span style="margin-left:0px" class="description">Created Time - {{$video->created_at->diffForHumans()}}</span>
                </div>
                <div class='pull-right'>
                   
                    <a href="{{route('admin.custom.live.edit' , array('id' => $video->id))}}" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i> {{tr('edit')}}</a>
                
                </div>
                <div class="clearfix"></div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">

              <div class="row">
                  <div class="col-lg-12 row">

                    <div class="col-lg-6">
                        <div class="box-body box-profile">
                        <h4>{{tr('details')}}</h4>
                            <ul class="list-group list-group-unbordered">
                                <li class="list-group-item">
                                    <div class="col-lg-4">   
                                        <b><i class="fa fa-suitcase margin-r-5"></i>{{tr('title')}}</b> 
                                    </div>
                                    <div class="col-lg-8">   
                                        <a>{{$video->title}}</a>
                                    </div>
                                    <div class="clearfix"></div>
                                </li>


                                <li class="list-group-item">
                                    <div class="col-lg-12">   
                                        <b><i class="fa fa-video-camera margin-r-5"></i>{{tr('hls_video_url')}}</b> 
                                    </div>
                                    <div class="col-lg-12"> 
                                    <br>  
                                        <a>{{$video->hls_video_url}}</a>
                                    </div>
                                    <div class="clearfix"></div>
                                </li>

                                <li class="list-group-item">

                                    <div class="col-lg-12"> 

                                        <b><i class="fa fa-video-camera margin-r-5"></i>
                                        {{tr('rtmp_video_url')}}</b> 

                                    </div>
                                    <div class="col-lg-12"> 
                                    <br>  
                                        <a>{{$video->rtmp_video_url}}</a>
                                    </div>
                                    <div class="clearfix"></div>
                                </li>


                                <li class="list-group-item">

                                    <div class="col-lg-12">   
                                        <b><i class="fa fa-book margin-r-5"></i>{{tr('description')}}</b> 
                                    </div>
                                    <div class="col-lg-12"> 
                                        <br>   
                                        <p style="word-wrap: break-word;">{{$video->description}}</p>
                                    </div>
                                    <div class="clearfix"></div>

                                </li>
                                
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <strong><i class="fa fa-file-picture-o margin-r-5"></i> {{tr('images')}}</strong>

                        <div class="row margin-bottom" style="margin-top: 10px;">
                            <div class="col-lg-12">
                              <img alt="Photo" src="{{isset($video->image) ? $video->image : ''}}" class="img-responsive" style="width:100%;height:250px;">
                            </div>
                              <!-- /.row -->
                        </div>

                    </div>
                    
                  </div>
                </div>

              <hr>

            
                <div class="row">
                  <div class="col-lg-12">
                       <div class="col-lg-6">

                            <strong><i class="fa fa-video-camera margin-r-5"></i> {{tr('video')}}</strong>

                            <br>
                            <br>

                            <div class="">
                    
                                    @if(check_valid_url($video->rtmp_video_url))

                                        <?php $url = $video->rtmp_video_url; ?>

                                        <div id="main-video-player"></div>

                                    @else
                                        <div class="image">
                                            <img src="{{asset('error.jpg')}}" alt="{{Setting::get('site_name')}}">
                                        </div>
                                    @endif

                            </div>
                        </div>
                        
                    </div>
                </div>
            <!-- /.box-body -->
            </div>
        </div>
    </div>
    </div>
@endsection

@section('scripts')
    
     <script src="{{asset('jwplayer/jwplayer.js')}}"></script>

    <script>jwplayer.key="{{Setting::get('JWPLAYER_KEY')}}";</script>

    <script type="text/javascript">
        
        jQuery(document).ready(function(){


                console.log('Inside Video');
                    
                console.log('Inside Video Player');

                console.log("{{$video->rtmp_video_url}}");
                console.log("{{$video->hls_video_url}}");

                var playerInstance = jwplayer("main-video-player");

                playerInstance.setup({
                    sources: [
                {
                    file: "{{$video->rtmp_video_url}}"
                }, 
                {
                    file : "{{$video->hls_video_url}}"
                }
                ],
                    image: "{{$video->image}}",
                    width: "100%",
                    aspectratio: "16:9",
                    primary: "flash",
                    controls : true,
                    "controlbar.idlehide" : false,
                    controlBarMode:'floating',
                    "controls": {
                      "enableFullscreen": false,
                      "enablePlay": false,
                      "enablePause": false,
                      "enableMute": true,
                      "enableVolume": true
                    },
                    // autostart : true,
                    "sharing": {
                        "sites": ["reddit","facebook","twitter"]
                      }
                });
                    
                
        });

    </script>

@endsection

