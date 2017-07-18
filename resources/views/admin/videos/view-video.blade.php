@extends('layouts.admin')

@section('title', tr('view_video'))

@section('content-header', tr('view_video'))

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
    <li><a href="{{route('admin.videos')}}"><i class="fa fa-video-camera"></i> {{tr('videos')}}</a></li>
    <li class="active">{{tr('video')}}</li>
@endsection 

@section('content')

    <?php $url = $trailer_url = ""; ?>

    <div class="row">

        @include('notification.notify')
        <div class="col-lg-12">
            <div class="box box-primary">
            <div class="box-header with-border btn-primary">
                <div class='pull-left'>
                    <h3 class="box-title" style="color: white"> <b>{{$video->title}}</b></h3>
                    <br>
                    <span style="margin-left:0px;color: white" class="description">Created Time - {{$video->video_date}}</span>
                </div>
                <div class='pull-right'>
                    @if ($video->compress_status == 0) <span class="label label-danger">{{tr('compress')}}</span>
                    @else
                    <a href="{{route('admin.edit.video' , array('id' => $video->admin_video_id))}}" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i> {{tr('edit')}}</a>
                    @endif
                </div>
                <div class="clearfix"></div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">

              <div class="row">
                  <div class="col-lg-12 row">

                    <div class="col-lg-4">

                        <div class="box-body box-profile">

                            <h4>{{tr('details')}}</h4>
                            
                            <ul class="list-group list-group-unbordered">
                                <li class="list-group-item">
                                    <b><i class="fa fa-suitcase margin-r-5"></i>{{tr('channel')}}</b> <a class="pull-right">{{$video->channel_name}}</a>
                                </li>
                              
                                

                                <li class="list-group-item">
                                    <b><i class="fa fa-clock-o margin-r-5"></i>{{tr('duration')}}</b> <a href="#" class="pull-right">{{$video->duration}}</a>
                                </li>
                                <li class="list-group-item">
                                    <b><i class="fa fa-bullhorn margin-r-5"></i>{{tr('ad_status')}}</b> <a class="pull-right">
                                        
                                        @if($video->ad_status)
                                            <span class="label label-success">{{tr('yes')}}</span>
                                        @else
                                            <span class="label label-danger">{{tr('no')}}</span>
                                        @endif
                                    </a>
                                </li>

                                <li class="list-group-item">
                                    <b><i class="fa fa-star margin-r-5"></i>{{tr('ratings')}}</b> <a class="pull-right">
                                        <span class="starRating-view">
                                            <input id="rating5" type="radio" name="ratings" value="5" @if($video->ratings == 5) checked @endif>
                                            <label for="rating5">5</label>

                                            <input id="rating4" type="radio" name="ratings" value="4" @if($video->ratings == 4) checked @endif>
                                            <label for="rating4">4</label>

                                            <input id="rating3" type="radio" name="ratings" value="3" @if($video->ratings == 3) checked @endif>
                                            <label for="rating3">3</label>

                                            <input id="rating2" type="radio" name="ratings" value="2" @if($video->ratings == 2) checked @endif>
                                            <label for="rating2">2</label>

                                            <input id="rating1" type="radio" name="ratings" value="1" @if($video->ratings == 1) checked @endif>
                                            <label for="rating1">1</label>
                                        </span>
                                  </a>
                                </li>

                                <li class="list-group-item">
                                    <b><i class="fa fa-eye margin-r-5"></i>{{tr('views')}}</b> <a class="pull-right">{{$video->watch_count}}</a>
                                </li>

                                <li class="list-group-item">
                                    <b><i class="fa fa-clock-o margin-r-5"></i>{{tr('amount')}}</b> <a class="pull-right"> {{Setting::get('currency')}} {{$video->amount}}</a>
                                </li>
                            
                            </ul>

                        </div>

                    </div>

                    <div class="col-lg-8">

                        <div class="box-body box-profile">
                            <h4></h4>
                        </div>

                         <div class="row" style="overflow-x: hidden;overflow-y: scroll; height:20em">
                         
                            <div class="col-lg-12">

                              <strong><i class="fa fa-file-text-o margin-r-5"></i> {{tr('description')}}</strong>

                              <p style="margin-top: 10px;">{{$video->description}}.</p>
                            </div>
                            <div class="col-lg-12">
                                  <strong><i class="fa fa-file-text-o margin-r-5"></i> {{tr('reviews')}}</strong>

                                  <p style="margin-top: 10px;">{{$video->reviews}}.</p>
                            </div>
                           
                         </div>

                    </div>
                    
                  </div>
                </div>

                <hr>


                <div class="row">
                  <div class="col-lg-12">
                        <div class="col-lg-6">

                            <strong><i class="fa fa-video-camera margin-r-5"></i> {{tr('full_video')}}</strong>

                            <div class="margin-t-10" style="margin-top:10px;">
                                @if($video->video_upload_type == 1)
                                    <?php $url = $video->video; ?>
                                    <div id="main-video-player"></div>
                                @else
                                    @if(check_valid_url($video->video))

                                        <?php $url = (Setting::get('streaming_url')) ? Setting::get('streaming_url').get_video_end($video->video) : $video->video; ?>
                                        <div id="main-video-player"></div>
                                    @else
                                        <div class="image">
                                            <img src="{{asset('error.jpg')}}" alt="{{Setting::get('site_name')}}">
                                        </div>
                                    @endif

                                @endif
                            </div>
                        </div>

                       <div class="col-lg-6">

                           <strong><i class="fa fa-file-picture-o margin-r-5"></i> {{tr('images')}}</strong>

                            <div class="row margin-bottom" style="margin-top: 10px;">
                                <!-- /.col -->
                                <div class="col-lg-12">
                                  <div class="row">
                                    <div class="col-lg-6">
                                        <img alt="Photo" src="{{($video->default_image) ? $video->default_image : ''}}" class="img-responsive" style="width:100%;height:130px;">
                                    </div>
                                    @foreach($video_images as $i => $image)
                                    <div class="col-lg-6">
                                      <img alt="Photo" src="{{$image->image}}" class="img-responsive" style="width:100%;height:130px">
                                      <br>
                                    </div>
                                    @endforeach
                                    @if ($video->is_banner) 
                                        <div class="col-lg-6">
                                            <img alt="Photo" src="{{$video->banner_image}}" class="img-responsive" style="width:100%;height:130px" title="Banner Image">
                                        </div>
                                    @endif
                                    <!-- /.col -->
                                  </div>
                                </div>
                                  <!-- /.row -->
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

                @if($url)

                    var playerInstance = jwplayer("main-video-player");


                    @if($videoStreamUrl) 

                        playerInstance.setup({
                            file: "{{$videoStreamUrl}}",
                            image: "{{$video->default_image}}",
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
                    @else 
                        var videoPath = "{{$videoPath}}";
                        var videoPixels = "{{$video_pixels}}";

                        var path = [];

                        var splitVideo = videoPath.split(',');

                        var splitVideoPixel = videoPixels.split(',');


                        for (var i = 0 ; i < splitVideo.length; i++) {
                            path.push({file : splitVideo[i], label : splitVideoPixel[i]});
                        }
                        playerInstance.setup({
                            sources: path,
                            image: "{{$video->default_image}}",
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

                        playerInstance.on('setupError', function() {

                                jQuery("#main-video-player").css("display", "none");
                                
                               

                                var hasFlash = false;
                                try {
                                    var fo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
                                    if (fo) {
                                        hasFlash = true;
                                    }
                                } catch (e) {
                                    if (navigator.mimeTypes
                                            && navigator.mimeTypes['application/x-shockwave-flash'] != undefined
                                            && navigator.mimeTypes['application/x-shockwave-flash'].enabledPlugin) {
                                        hasFlash = true;
                                    }
                                }

                                if (hasFlash == false) {
                                    jQuery('#flash_error_display').show();
                                    return false;
                                }

                                jQuery('#main_video_setup_error').css("display", "block");

                                confirm('The video format is not supported in this browser. Please option some other browser.');
                            
                            });


                    
                    @endif

                @endif

               
        });

    </script>

@endsection

