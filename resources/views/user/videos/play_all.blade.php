@extends('layouts.user')
@section('meta_tags')
   <meta property="og:locale" content="en_US" />
   <meta property="og:type" content="article" />
   <meta property="og:title" content="{{$video->title}}" />
   <meta property="og:description" content="<?= $video->title ?>" />
   <meta property="og:url" content="" />
   <meta property="og:site_name" content="{{Setting::get('site_name') ?: tr('site_name')}}" />
   <meta property="og:image" content="{{$video->default_image}}" />
   <meta name="twitter:card" content="summary"/>
   <meta name="twitter:description" content="<?= $video->title ?>"/>
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
      .common-streamtube {
         min-height: 0px !important;
      }
      textarea[name=comments] {
         resize: none;
      }
      #timings {
         padding: 5px;
      }
      .ad_progress {
         position: absolute;
         bottom: 0px;
         width: 100%;
         opacity: 0.8;
         background: #000;
         color: #fff;
         font-size: 12px;
      }
      .progress-bar-div {
         width: 100%;
         height: 5px;
         background: #e0e0e0;
         /*padding: 3px;*/
         border-radius: 3px;
         box-shadow: inset 0 1px 3px rgba(0, 0, 0, .2);
      }
      .progress-bar-fill-div {
         display: block;
         height: 5px;
         background: #cc181e;
         border-radius: 3px;
         /*transition: width 250ms ease-in-out;*/
         /*transition : width 10s ease-in-out;*/
      }
      th {
         border-top: none;
      }

      [id='toggle-heart'] {
        position: absolute;
        left: -100vw;
      }

   </style>
@endsection

@section('content')

<div class="y-content">

   <div class="row y-content-row">
      
      @include('layouts.user.nav')
         
         <div class="page-inner col-sm-9 col-md-10 profile-edit">
            
            <div class="profile-content mar-0">
            
               @include('notification.notify')

               <div class="row no-margin">
                    
                  <div class="col-sm-12 col-md-8 play-video">
                     
                     <div class="single-video-sec">
                        <div id="main-video-player"></div>
                     </div> 
                     <!--end of main-content-->

                  </div>
               
                 
                  <!--end of col-sm-4-->
               </div>
            
            </div>
         
         <div class="sidebar-back"></div>
      
      </div>
   
   </div>
   <!--y-content-row-->
</div>

<?php
   $ads_timing = $video_timings = [];
   
   if(count($ads) > 0 && $ads != null) {
   
       foreach ($ads->between_ad as $key => $obj) {
   
           $video_timings[] = $obj->video_time;
   
           $ads_timing[] = $obj->ad_time;
   
       }
   }
   
   ?>

<!-- MODALS SECTION -->

@include('user.videos._modals')

<!-- MODALS SECTION -->

@endsection

@section('scripts')

<script type="text/javascript" src="{{asset('assets/js/star-rating.js')}}"></script>

<script type="text/javascript" src="{{asset('assets/js/toast.script.js')}}"></script>

<script src="{{asset('jwplayer/jwplayer.js')}}"></script>

<script type="text/javascript">


   jwplayer.key="{{Setting::get('JWPLAYER_KEY')}}";
   
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
   
   $('.view_rating').rating({disabled: true, showClear: false});
   
   $('.comment_rating').rating({showClear: false});
   
   $(document).on('ready', function() {

      $("#copy-embed1").on( "click", function() {
           $('#popup1').modal('hide'); 
       });

      $('.global_playlist_id').on('click', function(event){

         event.preventDefault();

         var video_tape_id = $(this).attr('id');

         $('#global_playlist_id_'+video_tape_id).modal('show'); 

      });

   });
   
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
   
                       /*var display_style = document.getElementById('check_id').style.display;
   
                       alert(display_style);*/
   
                       if(data.status == 1) {
   
                           jQuery('#status').val("0");
   
                           jQuery('#wishlist_id').val(data.wishlist_id); 
                           jQuery("#added_wishlist").css({'font-family':'arial','background-color':'transparent','color' : '#b31217'});
   
                           if (jQuery(window).width() > 640) {
                           var append = '<i class="fa fa-heart">';
                           // var append = '<i class="fa fa-times-circle">&nbsp;&nbsp;{{tr('wishlist')}}';
                           } else {
                           var append = '<i class="fa fa-heart">';
                           }
                           jQuery("#added_wishlist").append(append);
   
                       } else {
   
                           jQuery('#status').val("1");
                           jQuery('#wishlist_id').val("");
                           jQuery("#added_wishlist").css({'font-family':'arial','background':'','color' : ''});
                           if (jQuery(window).width() > 640) {
                           var append = '<i class="fa fa-heart">';
                           // var append = '<i class="fa fa-plus-circle">&nbsp;&nbsp;{{tr('wishlist')}}';
                           } else {
                           var append = '<i class="fa fa-heart">';
                           }
   
                           jQuery("#added_wishlist").append(append);
   
                       }
   
                  } else {
   
                       // console.log('Wrong...!');
   
                  }
               }
           });
   
       });
   
       
       var playerInstance = jwplayer("main-video-player");  
   
   
       var path = [];
   
       @if($videoStreamUrl) 
   
           path.push({file : "{{$videoStreamUrl}}", label : "Original"});
   
           path.push({file : "{{$video->video}}", label : "Original"});
   
       @else
   
           if(jQuery.browser.mobile) {
   
               $('#mainVideo').show();
   
               // console.log('You are using a mobile device!');
   
               path.push({file : "{{$hls_video}}", label : "Original"});
   
           } else {
   
            @if(count($videoPath) > 0 && $videoPath != '')
   
               @foreach($videoPath as $path)
   
                   path.push({file : "{{$path->file}}", label : "{{$path->label}}"});
   
               @endforeach
   
               @endif
   
           }
   
       @endif
   
       var pre_ad_status = 1;
   
       var post_ad_status = 1;
   
       var between_ad_status = 0;
   
       var OnPlayStatus = 0;
   

      var playlist = [{
         "file":"{{$video->video}}",
         "image":"play1",
         "title": "Surfing Ocean Wave"
         },{
         "file": "{{$video->video}}",
         "image": "play2",
         "title": "Surfers at Sunrise"
         },{
         "file": "{{$video->video}}",
         "image":"play3",
         "title": "Road Cycling Outdoors"
         }];


       playerInstance.setup({
   
           sources: playlist,
           visualplaylist: true,
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
           autostart : true,
           "sharing": {
            "sites": ["facebook","twitter"]
           },
           events : {
   
               onReady : function(event) {
   
                   console.log("onready");
   
               },
   
               onTime:function(event) {
   
                   // Between Ad Play
   
                   var video_time = Math.round(playerInstance.getPosition());
   
                   @if($ads)
   
                       @if(count($ads->between_ad) > 0)
   
                           @foreach($ads->between_ad as $i => $obj) 
   
                               var video_timing = "{{$obj->video_time}}";
   
                               // console.log("Video Timing "+video_timing);
   
                               var a = video_timing.split(':'); // split it at the colons
   
                               if (a.length == 3) {
                                   var seconds = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]);
                               } else {
                                   var seconds = parseInt(a[0]) * 60 + parseInt(a[1]);
                               }


                               // If the user again clicked in between seconds, it wil check whether ad is present or not. if it is enable the ad
                               if (video_time < seconds) {

                                  between_ad_status = 0;

                               }
   
                               // console.log("Seconds "+seconds);
   
                               if (video_time == seconds && between_ad_status != video_time) {
   
                                   between_ad_status = video_time;
   
                                   jwplayer().pause();
   
                                   stop();
   
                                   $("#ad_image").attr("src","{{$obj->assigned_ad->file}}");
   
                                   $("#main-video-player").css({'visibility':'hidden', 'width' : '0%'});
   
                                   $('#main_video_ad').show();
   
                                   @if($obj->assigned_ad->ad_url)
   
                                       $('.click_here_ad').html("<a target='_blank' href='{{$obj->assigned_ad->ad_url}}'>{{tr('click_here_url')}}</a>");
   
                                       $('.click_here_ad').show();
   
                                   @endif
   
   
                                   adsPage("{{$obj->ad_time}}");
   
                               }


                           @endforeach
   
                       @endif
                
   
                   @endif
               },
   
               onBeforePlay : function(event) {
   
   
               },
               onPlay : function(event) {
   
                   // between_ad_status = 0;
   
               },
   
               onComplete : function(event) {
                  
                   console.log("onComplete Fn");

                   between_ad_status = 0;
   
                   @if(Auth::check())
   
                       jQuery.ajax({
                           url: "{{route('user.add.history')}}",
                           type: 'post',
                           data: {'video_tape_id' : "{{$video->video_tape_id}}"},
                           success: function(data) {
                               if(data.success == true) {
                                    window.location.reload(true);
                                   if (data.navigateback) {
   
                                       
   
                                   }
   
                               } else {
                                      
                               }
                           }
                       });
                       
                   @endif
   
                   // For post ad, once video completed the ad will execute
   
                   if (post_ad_status) {
   
                       @if($ads)
   
                       @if($ads->post_ad)
   
                           $("#ad_image").attr("src","{{$ads->post_ad->assigned_ad->file}}");
   
                           $("#main-video-player").css({'visibility':'hidden', 'width' : '0%'});
   
                           $('#main_video_ad').show();
   
                           @if($ads->post_ad->assigned_ad->ad_url)
   
                               $('.click_here_ad').html("<a target='_blank' href='{{$ads->post_ad->assigned_ad->ad_url}}'>{{tr('click_here_url')}}</a>");
   
                               $('.click_here_ad').show();
   
                           @endif
   
                           stop();
   
                           post_ad_status = 0;
   
                           adsPage("{{$ads->post_ad->ad_time}}");
                           
                       @endif
   
                       @endif
   
                   }
   
               }
   
           },
   
           tracks : [{
               file : "{{$video->subtitle ? $video->subtitle : ''}}",
               kind : "captions",
               default : true,
           }],
   
       });
   
       // For Pre Ad , Every first frame the ad will execute
   
       playerInstance.on('firstFrame', function() {
   
           console.log("firstFrame");
   
           post_ad_status = 1;
   
          // OnPlayStatus += 1;
   
           // if (pre_ad_status) {
   
               @if($ads)
   
                   @if($ads->pre_ad)
   
                       $("#ad_image").attr("src","{{$ads->pre_ad->assigned_ad->file}}");
   
                       $("#main-video-player").css({'visibility':'hidden', 'width' : '0%'});
   
                       $('#main_video_ad').show();
   
                       @if($ads->pre_ad->assigned_ad->ad_url)
   
                           $('.click_here_ad').html("<a target='_blank' href='{{$ads->pre_ad->assigned_ad->ad_url}}'>{{tr('click_here_url')}}</a>");
   
                           $(".click_here_ad").show();
   
                       @endif
   
                       jwplayer().pause();
   
                       pre_ad_status = 0;
   
                       adsPage("{{$ads->pre_ad->ad_time}}");
   
                   @endif
   
               @endif
   
           // }
   
       });
       playerInstance.load(playlist);

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
   
   
       jQuery("#main-video-player").show();
   
       // console.log(jwplayer().getPosition());
   
       var intervalId;
   
       var timings = "{{($ads) ? count($ads->between_ad) : 0}}";
   
       var time = 0;
   
       function timer(){
   
           intervalId = setInterval(function(){
   
               //
   
           }, 1000);
   
       }
   
       function stop(){
           clearInterval(intervalId);
       }
   
   
       var adCount = 0;
   
       function adsPage(adtimings){
   
           // alert("timings..!");
   
           $(".seconds").html(adtimings+" sec");
   
           $("#progress").html('<div class="progress-bar-div">'+
           '<span class="progress-bar-fill-div" style="width: 0%"></span>'+
           '</div>');
   
           $(".progress-bar-fill-div").css('transition', 'width '+adtimings+'s ease-in-out');
   
           $('.progress-bar-fill-div').delay(1000).queue(function () {
   
               // console.log("playig");
   
               $(this).css('width', '100%');
   
           });
   
   
           intervalId = setInterval(function(){
   
               adCount += 1;
   
               $(".seconds").html((adtimings - adCount) +" sec");
   
               // console.log("Ad Count " +adCount);
   
               // console.log("Ad Timings "+adtimings);
   
               if (adCount == adtimings) {
   
                   $(this).css('width', '100%')
   
                   adCount = 0;
   
                   stop();
   
                   $(".click_here_ad").hide();
   
                   $("#ad_image").attr("src", "");
   
                   $('#main_video_ad').hide();
   
                   $("#main-video-player").css({'visibility':'visible', 'width' : '100%'});
   
                   if (playerInstance.getState() != "complete") {
   
                       jwplayer().play();
   
                      // timer();
   
                   }
   
               }
   
           }, 1000);
   
       }
   
   });
   
   

   function playlist_save_video_add(video_tape_id) {
      
      var title = $("#playlist_title" ).val();

      var privacy = $("#playlist_privacy" ).val();

      if(title == '') { 

         alert("Title for playlist required");

      } else {

         $.ajax({
               
               url : "{{route('user.playlist.save.video_add')}}",
               data : {title : title , video_tape_id : video_tape_id, privacy : privacy, },
               type: "post",
               success : function(data) {
               
                  if (data.success) {

                     $('#playlist_title').removeAttr('value');  

                     $('#create_playlist_form').hide();

                     alert(data.message);

                     var labal = '<label class="playlist-container">'+data.title+'<input type="checkbox" onclick="playlist_video_update('+video_tape_id+ ', '+data.playlist_id+ ',this)" id="playlist_'+data.playlist_id+'" checked><span class="playlist-checkmark"></span></label>';

                     $('#user_playlists').append(labal);

                  } else {
                     
                     alert(data.error_messages);
                  }
                  
               },
      
               error : function(data) {
               },
         })
      }
   }  
   function addToast() {
       $.Toast("Embedded Link", "Link Copied Successfully.", "success", {
           has_icon:false,
           has_close_btn:true,
           stack: false,
           fullscreen:true,
           timeout:1000,
           sticky:false,
           has_progress:true,
           rtl:false,
       });
   }

</script>
@endsection