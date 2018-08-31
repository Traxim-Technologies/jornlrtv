@extends('layouts.user')


@section('meta_tags')

<meta property="og:locale" content="en_US" />
<meta property="og:type" content="article" />
<meta property="og:title" content="{{$video->title}}" />
<meta property="og:description" content="{{$video->description}}" />
<meta property="og:url" content="" />
<meta property="og:site_name" content="@if(Setting::get('site_name')) {{Setting::get('site_name') }} @else {{tr('site_name')}} @endif" />
<meta property="og:image" content="{{$video->default_image}}" />

<meta name="twitter:card" content="summary"/>
<meta name="twitter:description" content="{{$video->description}}"/>
<meta name="twitter:title" content="{{$video->title}}"/>
<meta name="twitter:image:src" content="{{$video->default_image}}"/>

@endsection

@section('styles')


<link rel="stylesheet" href="{{asset('assets/css/star-rating.css')}}">

<link rel="stylesheet" href="{{asset('assets/css/toast.style.css')}}">

<style type="text/css">

.sub-comhead .rating-md {

    font-size: 11px;

}
.thumb-class {
    cursor:pointer;
    text-decoration:none;
}

</style>

@endsection

@section('content')

    <div class="y-content">

        <div class="row y-content-row">

            @include('layouts.user.nav')

            <div class="page-inner col-sm-9 col-md-10 profile-edit">
            
                <div class="profile-content">

                    @include('notification.notify')

                    <div class="row no-margin">

                        <div class="col-sm-12 col-md-8 play-video">

                            @include('user.videos.streaming')

                            <div class="main-content">
                                <div class="video-content">
                                    <div class="details">
                                        <div class="video-title">

                                            <div class="title row">
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12">

                                                    <h3>{{$video->title}}</h3>
                                                    
                                                </div>
                                                                                               
                                                <div class="clearfix"></div>

                                                <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12">

                                                    <h4 class="">{{$video->description}}</h4>
                                                </div>                                                
                                            </div>
                                          
                                        </div><!--end of video-title-->                                                             
                                    </div><!--end of details-->

                                </div>

                                    
                                    <!--end of more-content-->

                                <!--end of video-content-->

                                          
                            </div>

                            <!--end of main-content-->

                        </div>


                        <!--end of col-sm-8 and play-video-->

                        <div class="col-sm-12 col-md-4 side-video custom-side">
                            <div class="up-next">
                                <h4 class="sugg-head1">{{tr('suggestions')}}</h4>

                                <ul class="video-sugg"> 

                                    @if(count($suggestions) > 0)
           
                                    @foreach($suggestions as $suggestion)
                                    
                                        <li class="sugg-list row">
                                            <div class="main-video">
                                                 <div class="video-image">
                                                    <div class="video-image-outer">
                                                        <a href="{{route('user.custom.live.view' , $suggestion->custom_live_video_id)}}"><img src="{{$suggestion->image}}"></a>
                                                    </div>  
                                                    
                                                    <div class="video_duration">
                                                        LIVE
                                                    </div> 
                                                 </div><!--video-image-->

                                                <div class="sugg-head">
                                                    <div class="suggn-title">                                          
                                                        <h5><a href="{{route('user.custom.live.view' , $suggestion->custom_live_video_id)}}">{{$suggestion->title}}</a></h5>
                                                    </div><!--end of sugg-title-->

                                                    <span class="video_views">
                                                        <div></div>
                                                        <!-- <i class="fa fa-eye"></i>  {{tr('views')}} <b>.</b>  -->
                                                        {{$suggestion->created_at}} 
                                                    </span> 
                                                    <br>
                                                                           
                                                </div><!--end of sugg-head-->
                                    
                                            </div><!--end of main-video-->
                                        </li><!--end of sugg-list-->
                                    @endforeach
                                    
                                    @endif
                                    
                                </ul>
                            </div><!--end of up-next-->
                                                
                        </div><!--end of col-sm-4-->

                    </div>
                
                </div>
                
                <div class="sidebar-back"></div> 

            </div>
           
        </div><!--y-content-row-->
    
    </div>

@endsection

@section('scripts')

    <script type="text/javascript" src="{{asset('assets/js/toast.script.js')}}"></script>

    <script type="text/javascript">

        $(document).ready(function(){
            $('.video-y-menu').addClass('hidden');
        }); 
    
    </script>

    <script src="{{asset('jwplayer/jwplayer.js')}}"></script>

    <script>jwplayer.key="{{Setting::get('JWPLAYER_KEY')}}";</script>

    <script type="text/javascript">


        $(document).ready(function(){

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
                controlBarMode:'floating',
                
            
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
                          

            jQuery("#main-video-player").show();

        });


        function addToast(){
            $.Toast("Embedded Link", "Link Cpoied Successfully.", "success", {
                has_icon:false,
                has_close_btn:true,
                stack: true,
                fullscreen:true,
                timeout:1000,
                sticky:false,
                has_progress:true,
                rtl:false,
            });
        }
    </script>

@endsection
