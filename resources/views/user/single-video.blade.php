@extends('layouts.user')

@section('styles')

<style type="text/css">
.common-youtube {
    min-height: 0px !important;
}
textarea[name=comments] {
    resize: none;
}

.small-box {
    border-radius: 2px;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    display: block;
    margin-bottom: 20px;
    position: relative;
    color: #fff;    
}

.bg-green{
    background-color: #00a65a !important;
}

.bg-aqua {
    background-color: #00c0ef !important;
}

.small-box h3, .small-box p {
    z-index: 5;
}
.small-box h3 {
    font-size: 38px;
    font-weight: bold;
    margin: 0 0 10px;
    padding: 0;
    white-space: nowrap;
}

.small-box > .inner {
    padding: 10px;
}

.small-box p {
    font-size: 15px;
}

.small-box .icon {
    color: rgba(0, 0, 0, 0.15);
    font-size: 90px;
    position: absolute;
    right: 10px;
    top: -10px;
    transition: all 0.3s linear 0s;
    z-index: 0;
}

.small-box > .small-box-footer {
    background: rgba(0, 0, 0, 0.1) none repeat scroll 0 0;
    color: rgba(255, 255, 255, 0.8);
    display: block;
    padding: 3px 0;
    position: relative;
    text-align: center;
    text-decoration: none;
    z-index: 10;
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
                                                <div style="width: 55%;">
                                                    <h3>{{$video->title}}</h3>
                                                </div>


                                                @if(Auth::check())
                                                    @if(Setting::get('is_spam') && Auth::user()->id != $video->channel_details->user_id)
                                                            
                                                        <div class="pull-right">

                                                            @if($flaggedVideo == '')
                                                                <button onclick="showReportForm();" type="button" class="report-button"><i class="fa fa-flag"></i> {{tr('report')}}</button>
                                                            @else 
                                                                <a href="{{route('user.remove.report_video', $flaggedVideo->id)}}" class="btn btn-warning"><i class="fa fa-flag"></i> {{tr('remove_report')}}</a>
                                                            @endif

                                                        </div>
                                                        <div class="clearfix"></div>

                                                    @endif

                                                @endif
                                                
                                            </div>

                                            <div class="video-description">
                                                <h4>{{tr('description')}}</h4>
                                                <p>{{$video->description}}</p>
                                            </div><!--end of video-description-->                                       
                                        </div><!--end of video-title-->                                                             
                                    </div><!--end of details-->

                                    @if(Setting::get('is_spam'))


                                        @if (!$flaggedVideo)
                                            <div class="more-content" style="display: none;" id="report_video_form">
                                                <form name="report_video" method="post" id="report_video" action="{{route('user.add.spam_video')}}">
                                                    <b>Report this Video ?</b>
                                                    <br>
                                                    @foreach($report_video as $report) 
                                                        <input type="radio" name="reason" value="{{$report->value}}" required> {{$report->value}}<br>
                                                    @endforeach
                                                    <input type="hidden" name="video_tape_id" value="{{$video->admin_video_id}}" />
                                                    <p class="help-block"><small>If you report this video, you won't see again the same video in anywhere in your account except "Spam Videos". If you want to continue to report this video as same. Click continue and proceed the same.</small></p>
                                                    <div class="pull-right">
                                                        <button class="btn btn-success btn-sm">Mark as Spam</button>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </form>
                                            </div>
                                        
                                        @endif

                                    @endif

                                    <div class="more-content">
                                        
                                        <div class="share-details row">

                                            <form name="add_to_wishlist" method="post" id="add_to_wishlist" action="{{route('user.add.wishlist')}}">
                                                @if(Auth::check())

                                                    <input type="hidden" value="{{$video->admin_video_id}}" name="admin_video_id">

                                                    @if(count($wishlist_status) == 1)

                                                        <input type="hidden" id="status" value="0" name="status">

                                                        <input type="hidden" id="wishlist_id" value="{{$wishlist_status->id}}" name="wishlist_id">

                                                        @if($flaggedVideo == '')
                                                        <div class="mylist">
                                                            <button style="background-color:rgb(229, 45, 39);" type="submit" id="added_wishlist" data-toggle="tooltip" title="Add to My List">
                                                                <i class="fa fa-heart"></i>
                                                                <span>{{tr('added_wishlist')}}</span>
                                                            </button> 
                                                        </div>
                                                        @endif
                                                    @else

                                                        <input type="hidden" id="status" value="1" name="status">

                                                        <input type="hidden" id="wishlist_id" value="" name="wishlist_id">
                                                        @if($flaggedVideo == '')
                                                            <div class="mylist">
                                                                <button type="submit" id="added_wishlist" data-toggle="tooltip" title="Add to My List">
                                                                    <i class="fa fa-heart"></i>
                                                                    <span>{{tr('add_to_wishlist')}}</span>
                                                                </button> 
                                                            </div>
                                                        @endif
                                                    @endif
                                                
                                                @else
                                                    <!-- Login Popup -->
                                                @endif

                                            </form>

                                            <div class="share">
                                                <a class="share-fb" target="_blank" href="http://www.facebook.com/sharer.php?u={{route('user.single',$video->admin_video_id)}}">
                                                    
                                                    <i class="fa fa-facebook"></i>{{tr('share_on_fb')}}
                                                    
                                                </a>

                                                <a class="share-twitter" target="_blank" href="http://twitter.com/share?text={{$video->title}}...&url={{route('user.single',$video->admin_video_id)}}">
                                                   
                                                    <i class="fa fa-twitter"></i>{{tr('share_on_twitter')}}
                                                    
                                                </a> 
                                            </div><!--end of share-->


                                             <div class="stars ratings">

                                                <div class="views">
                                                    <i class="fa fa-eye fa-2x"></i>&nbsp;{{$video->watch_count}} {{tr('views')}}
                                                </div>
                                                <div class="clearfix"></div>

                                                <a href="#"><i @if($video->ratings >= 1) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                <a href="#"><i @if($video->ratings >= 2) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                <a href="#"><i @if($video->ratings >= 3) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                <a href="#"><i @if($video->ratings >= 4) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                <a href="#"><i @if($video->ratings >= 5) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                            </div><!--end of stars-->

                                        </div><!--end of share-details-->                               
                                    </div>
                                    <!--end of more-content-->

                                </div><!--end of video-content-->

                                <?php /*
                                
                                @if(count($comments) > 0) <div class="v-comments"> @endif

                                    @if(count($comments) > 0) 
                                        <h3>{{tr('comments')}}
                                            <span class="c-380" id="comment_count">{{count($comments)}}</span>
                                        </h3> 
                                    @endif
                                    
                                    <div class="com-content">
                                        @if(Auth::check())

                                            <div class="image-form">
                                                <div class="comment-box1">
                                                    <div class="com-image">
                                                        <img style="width:48px;height:48px" src="{{Auth::user()->picture}}">                                    
                                                    </div><!--end od com-image-->
                                                    
                                                    <div id="comment_form">
                                                        <div>
                                                            <form method="post" id="comment_sent" name="comment_sent" action="{{route('user.add.comment')}}">

                                                                <input type="hidden" value="{{$video->admin_video_id}}" name="admin_video_id">

                                                                <textarea rows="10" id="comment" name="comments" placeholder="{{tr('add_comment_msg')}}"></textarea>

                                                                <input style="float:right;margin-bottom:10px;display:none" type="submit" name="submit" value="send">
                                                            </form>
                                                        </div>                                      
                                                    </div>  <!--end of comment-form-->
                                                </div>                                                              
                                            
                                            </div>

                                        @endif

                                        @if(count($comments) > 0)
                                        
                                            <div class="feed-comment">

                                                <span id="new-comment"></span>

                                                @foreach($comments as $c =>  $comment)

                                                    <div class="display-com">
                                                        <div class="com-image">
                                                            <img style="width:48px;height:48px" src="{{$comment->picture}}">                                    
                                                        </div><!--end od com-image-->

                                                        <div class="display-comhead">
                                                            <span class="sub-comhead">
                                                                <a href="#"><h5 style="float:left">{{$comment->username}}</h5></a>
                                                                <a href="#" class="text-none"><p>{{$comment->created_at->diffForHumans()}}</p></a>
                                                                <p class="com-para">{{$comment->comment}}</p>
                                                            </span>             
                                                            
                                                        </div><!--display-comhead-->                                        
                                                    </div><!--display-com-->

                                                @endforeach

                                            </div>

                                        @else
                                            <div class="feed-comment">

                                                <span id="new-comment"></span>
                                            </div>
                                            <!-- <p>{{tr('no_comments')}}</p> -->
                                        
                                        @endif
                                            
                                    </div>

                                @if(count($comments) > 0) </div> @endif<!--end of v-comments-->

                                */ ?>
                                                                
                            </div>

                            <!--end of main-content-->

                        </div>

                        <!--end of col-sm-8 and play-video-->

                        <div class="col-sm-12 col-md-4 side-video custom-side">
                            <div class="up-next">
                                <h4 class="sugg-head1">{{tr('suggestions')}}</h4>

                                <ul class="video-sugg"> 

                                    @foreach($suggestions->data as $suggestion)
                                    
                                        <li class="sugg-list row">
                                            <div class="main-video">
                                                 <div class="video-image">
                                                    <div class="video-image-outer">
                                                        <a href="{{route('user.single' , $suggestion->admin_video_id)}}"><img src="{{$suggestion->default_image}}"></a>
                                                    </div>                       
                                                </div><!--video-image-->

                                                <div class="sugg-head">
                                                    <div class="suggn-title">                                          
                                                        <h5><a href="{{route('user.single' , $suggestion->admin_video_id)}}">{{$suggestion->title}}</a></h5>
                                                    </div><!--end of sugg-title-->

                                                    <div class="sugg-description">
                                                        <p>{{tr('duration')}}: {{$suggestion->duration}}</p>
                                                    </div><!--end of sugg-description--> 

                                                    <?php /* <span class="stars">
                                                        <a href="#"><i @if($suggestion->ratings > 1) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                        <a href="#"><i @if($suggestion->ratings > 2) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                        <a href="#"><i @if($suggestion->ratings > 3) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                        <a href="#"><i @if($suggestion->ratings > 4) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                        <a href="#"><i @if($suggestion->ratings > 5) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                    </span>                          */ ?>                              
                                                </div><!--end of sugg-head-->
                                    
                                            </div><!--end of main-video-->
                                        </li><!--end of sugg-list-->
                                    @endforeach
                                   
                                    
                                </ul>
                            </div><!--end of up-next-->
                                                
                        </div><!--end of col-sm-4-->

                    </div>
                </div>
            
            </div>

        </div><!--y-content-row-->
    </div>


<?php

    $ads_timing = $video_timings = [] ;

    if(count($ads) > 0 && $ads != null) {

        foreach ($ads->between_ad as $key => $obj) {
            
            $video_timings[] = $obj->video_time;

            $ads_timing[] = $obj->ad_time;
        }
    }

?>

@endsection

@section('scripts')

    <script type="text/javascript">
        $(document).ready(function(){
            $('.video-y-menu').addClass('hidden');
        }); 
        function showReportForm() {
            var divId = document.getElementById('report_video_form').style.display;
            if (divId == 'none') {
                $('#report_video_form').show(500);
            } else {
                $('#report_video_form').hide(500);
            }
        }
    </script>

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


            //hang on event of form with id=myform
            jQuery("form[name='add_to_wishlist']").submit(function(e) {

                //prevent Default functionality
                e.preventDefault();

                //get the action-url of the form
                var actionurl = e.currentTarget.action;

                //do your own request an handle the results
                jQuery.ajax({
                        url: actionurl,
                        type: 'post',
                        dataType: 'json',
                        data: jQuery("#add_to_wishlist").serialize(),
                        success: function(data) {
                           if(data.success == true) {

                                jQuery("#added_wishlist").html("");

                                if(data.status == 1) {
                                    jQuery('#status').val("0");

                                    jQuery('#wishlist_id').val(data.wishlist_id); 
                                    jQuery("#added_wishlist").css({'background':'rgb(229, 45, 39)','color' : '#FFFFFF'});
                                    jQuery("#added_wishlist").append('<i class="fa fa-heart"> {{tr('added_wishlist')}}');
                                } else {
                                    jQuery('#status').val("1");
                                    jQuery('#wishlist_id').val("");
                                    jQuery("#added_wishlist").css({'background':'','color' : ''});
                                    jQuery("#added_wishlist").append('<i class="fa fa-heart"> {{tr('add_to_wishlist')}}');
                                }
                           } else {
                                console.log('Wrong...!');
                           }
                        }
                });

            });

            $('#comment').keydown(function(event) {
                if (event.keyCode == 13) {
                    $(this.form).submit()
                    return false;
                 }
            }).focus(function(){
                if(this.value == "Write your comment here..."){
                     this.value = "";
                }

            }).blur(function(){
                if(this.value==""){
                     this.value = "";
                }
            });

            jQuery("form[name='comment_sent']").submit(function(e) {

                //prevent Default functionality
                e.preventDefault();

                //get the action-url of the form
                var actionurl = e.currentTarget.action;

                var form_data = jQuery("#comment").val();

                if(form_data) {

                    //do your own request an handle the results
                    jQuery.ajax({
                            url: actionurl,
                            type: 'post',
                            dataType: 'json',
                            data: jQuery("#comment_sent").serialize(),
                            success: function(data) {

                               if(data.success == true) {

                                @if(Auth::check())
                                    jQuery('#comment').val("");
                                    jQuery('#no_comment').hide();
                                    var comment_count = 0;
                                    var count = 0;
                                    comment_count = jQuery('#comment_count').text();
                                    var count = parseInt(comment_count) + 1;
                                    jQuery('#comment_count').text(count);
                                    jQuery('#video_comment_count').text(count);

                                    jQuery('#new-comment').prepend('<div class="display-com"><div class="com-image"><img style="width:48px;height:48px" src="{{Auth::user()->picture}}"></div><div class="display-comhead"><span class="sub-comhead"><a href="#"><h5 style="float:left">{{Auth::user()->name}}</h5></a><a href="#"><p>'+data.date+'</p></a><p class="com-para">'+data.comment.comment+'</p></span></div></div>');
                                @endif
                               } else {
                                    console.log('Wrong...!');
                               }
                            }
                    });
                } else {
                    return false;
                }

            });

           //  jQuery("form[name='watch_main_video']").submit(function(e) {

                //prevent Default functionality
               //  e.preventDefault();

               //  jQuery('#watch_main_video_button').fadeOut();

                    var playerInstance = jwplayer("main-video-player");  

                    @if($main_video)



                        if(isOpera || isSafari) {

                            jQuery('#main_video_setup_error').show();
                            jQuery('#trailer_video_setup_error').hide();
                            jQuery('#main-video-player').hide();

                            confirm('The video format is not supported in this browser. Please option some other browser.');

                        } else {


                            if(!jQuery.browser.mobile) {

                            } else {

                                // $('#mainVideo').show();
                                
                                console.log('You are using a mobile device!');

                                <?php $videoStreamUrl = $hls_video; ?>

                                jQuery.ajax({
                                        url: "{{route('test')}}",
                                        type: 'post',
                                        data: {'test' : "test"},
                                        success: function(data) {

                                        }
                                    });
                            }

                            @if($videoStreamUrl) 

                                playerInstance.setup({
                                    // file: "{{$videoStreamUrl}}",
                                    sources: [{
                                        file: "{{$videoStreamUrl}}"
                                      },{
                                        file: "{{$main_video}}"
                                      }],
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

                                if(jQuery.browser.mobile) {

                                    $('#mainVideo').show();
                                    
                                    console.log('You are using a mobile device!');

                                    var path = "{{$hls_video}}";

                                    jQuery.ajax({
                                        url: "{{route('test')}}",
                                        type: 'post',
                                        data: {'test' : "test"},
                                        success: function(data) {

                                        }
                                    });

                                } else {

                                    var videoPath = "{{$videoPath}}";
                                    var videoPixels = "{{$video_pixels}}";

                                    var path = [];

                                    var splitVideo = videoPath.split(',');

                                    var splitVideoPixel = videoPixels.split(',');


                                    for (var i = 0 ; i < splitVideo.length; i++) {

                                        path.push({file : splitVideo[i], label : splitVideoPixel[i]});
                                    }

                                    //alert("HELELo");
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

                            @endif

                            playerInstance.on('setupError', function() {

                                jQuery("#main-video-player").css("display", "none");
                                jQuery('#trailer_video_setup_error').hide();
                               

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

                           
                            
                                jQuery.ajax({
                                    url: "{{route('user.add.history')}}",
                                    type: 'post',
                                    data: {'admin_video_id' : "{{$video->admin_video_id}}"},
                                    success: function(data) {

                                       if(data.success == true) {

                                        console.log('Added to history');

                                        /*var watch_count = 0;
                                        var count = 0;
                                        watch_count = jQuery('#watch_count').text();
                                        var count = parseInt(watch_count) + 1;
                                        jQuery('#watch_count').text(count);

                                        console.log('Added to history');*/

                                       } else {
                                            console.log('Wrong...!');
                                       }
                                    }
                                });
                            

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
