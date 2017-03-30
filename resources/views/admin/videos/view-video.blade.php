@extends('layouts.admin')

@section('title', tr('view_video'))

@section('content-header', tr('view_video'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('admin.videos')}}"><i class="fa fa-video-camera"></i> {{tr('videos')}}</a></li>
    <li class="active">{{tr('video')}}</li>
@endsection 

@section('content')

    @include('notification.notify')

    <?php $url = $trailer_url = ""; ?>

    <div class="row">

        <div class="col-lg-7">

            <div class="box box-widget">

                <div class="box-header with-border">
                    <div class="user-block">
                        <span style="margin-left:0px" class="username"><a href="#">{{$video->title}}</a></span>
                        <span style="margin-left:0px" class="description">Created Time - {{$video->video_date}}</span>
                    </div>
                    
                    
                    <div class="box-tools">
                        <button data-widget="collapse" class="btn btn-box-tool" type="button">
                            <i class="fa fa-minus"></i>
                        </button>
                    </div>

                </div>

                <div class="box-body">
                    <h4 style="font-weight:800;color:#3c8dbc">{{tr('category')}}</h4>

                    <p style="margin-top:10px;border-bottom: 1px solid #f4f4f4;padding-bottom: 10px;">{{$video->category_name}}</p>

                    <h4 style="font-weight:800;color:#3c8dbc">{{tr('sub_category')}}</h4>

                    <p style="margin-top:10px;border-bottom: 1px solid #f4f4f4;padding-bottom: 10px;">{{$video->sub_category_name}}</p>
                   
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

                    <h4 style="font-weight:800;color:#3c8dbc">{{tr('duration')}}</h4>

                    <p style="margin-top:10px;border-bottom: 1px solid #f4f4f4;padding-bottom: 10px;">{{$video->duration}}</p>

                    <h4 style="font-weight:800;color:#3c8dbc">{{tr('description')}}</h4>

                    <p style="margin-top:10px;border-bottom: 1px solid #f4f4f4;padding-bottom: 10px;">{{$video->description}}</p>

                    <h4 style="font-weight:800;color:#3c8dbc">{{tr('ratings')}}</h4>

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
                    
                    <h4 style="font-weight:800;color:#3c8dbc">{{tr('reviews')}}</h4>

                    <p style="">{{$video->reviews}}</p>

                    <h4 style="font-weight:800;color:#3c8dbc">{{tr('video_type')}}</h4>

                    <p style="margin-top:10px;border-bottom: 1px solid #f4f4f4;padding-bottom: 10px;">
                        @if($video->video_type == 1)
                            {{tr('video_upload_link')}}
                        @endif
                        @if($video->video_type == 2)
                            {{tr('youtube')}}
                        @endif
                        @if($video->video_type == 3)
                            {{tr('other_link')}}
                        @endif
                    </p>

                    <h4 style="font-weight:800;color:#3c8dbc">{{tr('video_upload_type')}}</h4>

                    <p style="margin-top:10px;border-bottom: 1px solid #f4f4f4;padding-bottom: 10px;">
                        @if($video->video_upload_type == 1)
                            {{tr('s3')}}
                        @endif
                        @if($video->video_upload_type == 2)
                            {{tr('direct')}}
                        @endif          
                    </p>
                
                </div>

            </div>

            @if($video->banner_image)

                <div class="box box-widget">

                    <div class="box-header with-border">
                        <div class="user-block">
                            <span style="margin-left:0px" class="username"><a href="#">{{tr('banner_image')}}</a></span>
                        </div>

                        <div class="box-tools">

                            <button data-widget="collapse" class="btn btn-box-tool" type="button">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>

                    </div>

                    <div class="box-body">
                        <img alt="Photo" src="{{$video->banner_image}}" style="width:100%;height:150px;">
                    </div>
                </div>

            @endif

        </div>

        <div class="col-lg-5">

            <div class="box box-widget">

                <div class="box-header with-border">
                    <div class="user-block">
                        <span style="margin-left:0px" class="username"><a href="#">{{tr('trailer_video')}}</a></span>
                    </div>

                    <div class="box-tools">

                        <!-- <button title="Mark as read" data-toggle="tooltip" class="btn btn-box-tool" type="button">
                            <i class="fa fa-circle-o"></i>
                        </button> -->

                        <button data-widget="collapse" class="btn btn-box-tool" type="button">
                            <i class="fa fa-minus"></i>
                        </button>

                        <!-- <button data-widget="remove" class="btn btn-box-tool" type="button">
                            <i class="fa fa-times"></i>
                        </button> -->
                    </div>

                </div>

                <div class="box-body">

                        @if($video->video_upload_type == 1)
                            <?php $trailer_url = $video->trailer_video; ?>
                            <div id="trailer-video-player"></div>
                        @else

                            @if(check_valid_url($video->trailer_video))

                                <?php $trailer_url = (Setting::get('streaming_url')) ? Setting::get('streaming_url').get_video_end($video->trailer_video) : $video->trailer_video; ?>

                                <div id="trailer-video-player"></div>

                            @else
                                <div class="image">
                                    <img src="{{asset('error.jpg')}}" alt="{{Setting::get('site_name')}}">
                                </div>
                            @endif

                        @endif

                </div>

            </div>

            @if($video->default_image)

                <div class="box box-widget">

                    <div class="box-header with-border">
                        <div class="user-block">
                            <span style="margin-left:0px" class="username"><a href="#">{{tr('default_image')}}</a></span>
                        </div>

                        <div class="box-tools">

                            <button data-widget="collapse" class="btn btn-box-tool" type="button">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>

                    </div>

                    <div class="box-body">
                        <img alt="Photo" src="{{$video->default_image}}" style="width:100%;height:150px;">
                    </div>
                </div>

            @endif


            @if(count($video_images) > 0)
                
                @foreach($video_images as $i => $image)
                    
                    <div class="box box-widget">

                        <div class="box-header with-border">
                            <div class="user-block">
                                <span style="margin-left:0px" class="username"><a href="#">Image {{$image->position}}</a></span>
                            </div>

                            <div class="box-tools">

                                <!-- <button title="Mark as read" data-toggle="tooltip" class="btn btn-box-tool" type="button">
                                    <i class="fa fa-circle-o"></i>
                                </button> -->

                                <button data-widget="collapse" class="btn btn-box-tool" type="button">
                                    <i class="fa fa-minus"></i>
                                </button>

                                <!-- <button data-widget="remove" class="btn btn-box-tool" type="button">
                                    <i class="fa fa-times"></i>
                                </button> -->
                            </div>

                        </div>

                        <div class="box-body">
                            <img alt="Photo" src="{{$image->image}}" style="width:100%;height:150px;">
                        </div>
                    </div>

                @endforeach

            @endif

        </div>


    </div>

@endsection

@section('scripts')
    
     <script src="{{asset('jwplayer/jwplayer.js')}}"></script>

    <script>jwplayer.key="{{env('JWPLAYER_KEY')}}";</script>

    <script type="text/javascript">
        
        jQuery(document).ready(function(){


                console.log('Inside Video');
                    
                console.log('Inside Video Player');

                @if($url)

                    var playerInstance = jwplayer("main-video-player");

                    playerInstance.setup({
                       
                        file: "{{$url}}",
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

                @endif

                @if($trailer_url)

                    var playerInstance = jwplayer("trailer-video-player");

                    playerInstance.setup({
                        file: "{{$trailer_url}}",
                        image: "{{$video->default_image}}",
                        width: "100%",
                        aspectratio: "16:9",
                        primary: "flash",
                    });

                @endif
        });

    </script>

@endsection

