@extends('layouts.admin')

@section('title', tr('view_ads'))

@section('content-header', tr('view_ads'))

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
    <li><a href="{{route('admin.ads_index')}}"><i class="fa fa-bullhorn"></i> {{tr('view_ads')}}</a></li>
    <li class="active">{{tr('view_ads')}}</li>
@endsection 

@section('content')


<div class="col-md-12">
  <div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
      <li class="active"><a href="#preview_ad" data-toggle="tab" aria-expanded="true">{{tr('preview_ad')}}</a></li>

      <li class="pull-right clearfix">
        <a href="{{route('admin.ads_edit' , array('id' => $ads->get_video_tape->id))}}"><i class="fa fa-pencil"></i> {{tr('edit')}}</a>
      </li>
 
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="preview_ad">

            <div class="col-lg-6">
                @include('admin.videos.streaming')
            </div>

            <div class="col-lg-6">


                <h4>{{tr('details')}}</h4>


                <ul class="timeline timeline-inverse">

                @foreach($ads->ad_details as $details)
                  <!-- timeline time label -->
                  <li class="time-label" title="{{tr('video_time')}}">
                        <span class="bg-red">
                          {{$details->video_time}}
                        </span>
                  </li>
                  <!-- /.timeline-label -->
                  <!-- timeline item -->
                  <li>
                    <i class="fa fa-bullhorn bg-blue"></i>

                    <div class="timeline-item">
                      <span class="time"><i class="fa fa-clock-o"></i> {{$details->ad_time}} ({{tr('in_min')}})</span>

                      <h3 class="timeline-header">

                      @if($details->ad_type == 1)

                        <a>{{tr('pre_ad')}}</a> 

                      @elseif($details->ad_type == 2) 

                        <a>{{tr('post_ad')}}</a> 

                      @else

                        <a>{{tr('between_ad')}}</a> 

                      @endif
                        

                      {{tr('details')}}</h3>

                      <div class="timeline-body">
                            
                            <img src="{{$details->file}}" style="width: 100%">

                      </div>
                      <!-- <div class="timeline-footer">
                        <a class="btn btn-primary btn-xs">Read more</a>
                        <a class="btn btn-danger btn-xs">Delete</a>
                      </div> -->
                    </div>
                  </li>
                 @endforeach
                
                </ul>
            </div>

            <div class="clearfix"></div>

        </div>

      <!-- /.tab-pane -->
    </div>
    <!-- /.tab-content -->
  </div>
  <!-- /.nav-tabs-custom -->
</div>
<div class="clearfix"></div>
@endsection

@section('scripts')
    

    <script src="{{asset('jwplayer/jwplayer.js')}}"></script>

    <script>jwplayer.key="{{envfile('JWPLAYER_KEY')}}";</script>

    <script type="text/javascript">
        
        jQuery(document).ready(function(){  

                // Opera 8.0+
                var isOpera = (!!window.opr && !!opr.addons) || !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
                // Firefox 1.0+
                var isFirefox = typeof InstallTrigger !== 'undefined';
                // At least Safari 3+: "[object HTMLElementConstructor]"
                var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
                // Internet Explorer 6-11
                var isIE = /*@cc_on!@*/false || !!document.documentMode;
                // Edge 20+
                var isEdge = !isIE && !!window.StyleMedia;
                // Chrome 1+
                var isChrome = !!window.chrome && !!window.chrome.webstore;
                // Blink engine detection
                var isBlink = (isChrome || isOpera) && !!window.CSS;


           

           
           //  jQuery("form[name='watch_main_video']").submit(function(e) {

                //prevent Default functionality
               //  e.preventDefault();

               //  jQuery('#watch_main_video_button').fadeOut();

                    var playerInstance = jwplayer("main-video-player");  

                    @if($ads->get_video_tape->video)



                        if(isOpera || isSafari) {

                            jQuery('#main_video_setup_error').show();
                            jQuery('#trailer_video_setup_error').hide();
                            jQuery('#main-video-player').hide();

                            confirm('The video format is not supported in this browser. Please option some other browser.');

                        } else {

                                playerInstance.setup({
                                    
                                    file: "{{$ads->get_video_tape->video}}",
                                    image: "{{$ads->get_video_tape->default_image}}",
                                    width: "100%",
                                    aspectratio: "16:9",
                                    height: "270px",
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
                        }
                    
                    @else
                        jQuery('#main_video_error').show();
                        
                    @endif

                    jQuery("#main-video-player").show();

                    console.log(jwplayer().getPosition());

                    var intervalId;


                    var timings = "{{($ads) ? count($ads->between_ad) : 0}}";

                    // var adtimings = 5;

                    var time = 0;

                    // console.log("Timings " + timings.length);

                    function timer(){

                         intervalId = setInterval(function(){

                            var video_time = Math.round(playerInstance.getPosition());


                            console.log("Video Timing "+video_time);

                            @if($ads)

                                @if(count($ads->between_ad) > 0)

                                    @foreach($ads->between_ad as $i => $obj) {

                                        var video_timing = "{{$obj->video_time}}";

                                        console.log("Video Timing "+video_timing);

                                        var a = video_timing.split(':'); // split it at the colons

                                         if (a.length == 3) {
                                             var seconds = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]);
                                         } else {
                                             var seconds = parseInt(a[0]) * 60 + parseInt(a[1]);
                                         }

                                         console.log("Seconds "+seconds);

                                        if (video_time == seconds && time != video_time) {

                                             jwplayer().pause();

                                             time = video_time;

                                             stop();

                                             $('#main-video-player').hide();

                                             $('#main_video_ad').show();

                                             $("#ad_image").attr("src","{{$obj->file}}");


                                             adsPage("{{$obj->ad_time}}");

                                        }
                                    }

                                    @endforeach

                                @endif


                                @if($ads->post_ad)

                                    if (playerInstance.getState() == "complete") {

                                        $('#main-video-player').hide();

                                        $('#main_video_ad').show();

                                        $("#ad_image").attr("src","{{$ads->post_ad->file}}");

                                        stop();

                                        adsPage("{{$ads->post_ad->ad_time}}");

                                    }
                                @endif
                                
                            @endif


                         }, 1000);

                    }

                    function stop(){
                       clearInterval(intervalId);
                    }


                    var adCount = 0;

                    function adsPage(adtimings){

                         // adtimings = adtimings * 60;

                         intervalId = setInterval(function(){

                            adCount += 1;

                            console.log("Ad Count " +adCount);
 
                            if (adCount == adtimings) {

                                adCount = 0;

                                stop();

                                $('#main_video_ad').hide();

                                $('#main-video-player').show();

                                if (playerInstance.getState() != "complete") {

                                    jwplayer().play();

                                    timer();

                                }

                            }

                         }, 1000);

                    }



                    jwplayer().on('displayClick', function(e) {

                        console.log("state pos "+jwplayer().getState());


                        if (jwplayer().getState() == 'idle') {

                            @if($ads)

                                @if($ads->pre_ad)

                                     jwplayer().pause();

                                     $('#main-video-player').hide();

                                     $('#main_video_ad').show();

                                     $("#ad_image").attr("src","{{$ads->pre_ad->file}}");

                                     adsPage("{{$ads->pre_ad->ad_time}}");

                                @endif

                            @endif


                        }

                        @if($ads)
                            @if (((count($ads->between_ad) > 0) || !empty($ads->post_ad)) && empty($ads->pre_ad)) 

                                timer();

                            @endif
                        @endif

                    })



                    

            // });

        });

    </script>

@endsection


