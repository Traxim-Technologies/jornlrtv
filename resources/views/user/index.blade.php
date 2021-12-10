@extends('layouts.user')

@section('styles')

<style type="text/css">
    
.list-inline {
  text-align: center;
}
.list-inline > li {
  margin: 10px 5px;
  padding: 0;
}
.list-inline > li:hover {
  cursor: pointer;
}
.list-inline .selected img {
  opacity: 1;
  border-radius: 15px;
}
.list-inline img {
  opacity: 0.5;
  -webkit-transition: all .5s ease;
  transition: all .5s ease;
}
.list-inline img:hover {
  opacity: 1;
}

.item > img {
  max-width: 100%;
  height: auto;
  display: block;
}

.carousel-inner .active {

    background-color: none;
}

.carousel-inner .item {

    padding: 0px;

}
</style>
@endsection

@section('content')

    <div class="y-content bbbb">

   
        <div class="row content-row">

            @include('layouts.user.nav')

            <div class="page-inner col-xs-12 col-sm-9 col-md-10">

                @if(Setting::get('is_banner_video'))


                @if(count($banner_videos) > 0)

                <div class="row" id="slider">
                    <div class="col-md-12 banner-slider">
                        <div id="myCarousel" class="carousel slide">
                            <div class="carousel-inner">
                                @foreach($banner_videos as $key => $banner_video)
                                <div class="{{$key == 0 ? 'active item' : 'item'}}" data-slide-number="{{$key}}">
                                    <a href="{{route('user.single' , $banner_video->video_tape_id)}}">
                                    <img src="{{$banner_video->image}}" style="height:250px;width: 100%;">
                                    
                                    </a>
                                </div>
                                @endforeach
                            </div>

                            <!-- Controls-->
                           <!--  <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
                                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                                <span class="sr-only">{{tr('previous')}}</span>
                            </a>
                            <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
                                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                                <span class="sr-only">{{tr('next')}}</span>
                            </a> -->

                        </div>

                    </div>
                </div>

                @endif

                @endif

                @if(Setting::get('is_banner_ad'))

                @if(count($banner_ads) > 0)

                <div class="row" id="slider">
				<div class="col-sm-2 y-menu scroll">
				<div class="">
    <!--<ul class="y-home menu1">
        <li id="home">
            <a href="{{route('user.dashboard')}}">
                <img src="{{asset('images/sidebar/home-grey.png')}}" class="grey-img">
                <img src="{{asset('images/sidebar/home-red.png')}}" class="red-img">
                <span>{{tr('home')}}</span>
            </a>
        </li>
        <li id="trending">
            <a href="{{route('user.trending')}}">
                <img src="{{asset('images/sidebar/trending-grey.png')}}" class="grey-img">
                <img src="{{asset('images/sidebar/trending-red.png')}}" class="red-img">
                <span>{{tr('trending')}}</span>
            </a>
        </li>

        <li id="custom_live_videos">
            <a href="{{route('user.custom_live_videos.index')}}">
                <img src="{{asset('images/sidebar/video-camera-grey.png')}}" class="grey-img">
                <img src="{{asset('images/sidebar/video-camera-red.png')}}" class="red-img">
                <span>{{tr('user_custom_live_videos')}}</span>
            </a>
        </li>
        <li id="live_videos">
            <a href="{{route('user.live_videos')}}">
                <img src="{{asset('images/sidebar/live-video.png')}}" class="grey-img">
                <img src="{{asset('images/sidebar/live-video-active.png')}}" class="red-img">
                <span>{{tr('live_videos')}}</span>
            </a>
        </li>

        </li>

        <li id="channels">
            <a href="{{route('user.channel.list')}}">
                <img src="{{asset('images/sidebar/search-grey.png')}}" class="grey-img">
                <img src="{{asset('images/sidebar/search-red.png')}}" class="red-img">
                <span>{{tr('browse_channels')}}</span>
            </a>
        </li>

        @if(Auth::check())

            <li id="history">
                <a href="{{route('user.history')}}">
                    <img src="{{asset('images/sidebar/history-grey.png')}}" class="grey-img">
                    <img src="{{asset('images/sidebar/history-red.png')}}" class="red-img">
                    <span>{{tr('history')}}</span>
                </a>
            </li>
            <li id="settings">
                <a href="/settings">
                    <img src="{{asset('images/sidebar/settings-grey.png')}}" class="grey-img">
                    <img src="{{asset('images/sidebar/settings-red.png')}}" class="red-img">
                    <span>{{tr('settings')}}</span>
                </a>
            </li>
            <li id="wishlist">
                <a href="{{route('user.wishlist')}}">
                    <img src="{{asset('images/sidebar/heart-grey.png')}}" class="grey-img">
                    <img src="{{asset('images/sidebar/heart-red.png')}}" class="red-img">
                    <span>{{tr('wishlist')}}</span>
                </a>
            </li>
            @if(Setting::get('create_channel_by_user') == CREATE_CHANNEL_BY_USER_ENABLED || Auth::user()->is_master_user == 1)
                <li id="my_channel">
                    <a href="{{route('user.channel.mychannel')}}">
                        <img src="{{asset('images/sidebar/channel-grey.png')}}" class="grey-img">
                        <img src="{{asset('images/sidebar/channel-red.png')}}" class="red-img">
                        <span>{{tr('my_channels')}}</span>
                    </a>
                </li>

            @endif

            <li id="playlists">
                <a href="{{route('user.playlists.index')}}">
                    <img src="{{asset('images/sidebar/playlist-grey.png')}}" class="grey-img">
                    <img src="{{asset('images/sidebar/playlist-red.png')}}" class="red-img">
                    <span>{{tr('playlists')}}</span>
                </a>
            </li>
    
        @endif
    
    </ul>-->
	<ul class="y-home menu1 slider-side-bar">
               <li id="channels">
            <a href="{{route('user.channel.list')}}">
                <img src="{{asset('images/sidebar/search-grey.png')}}" class="grey-img">
                <img src="{{asset('images/sidebar/search-red.png')}}" class="red-img">
                <span>{{tr('browse_channels')}}</span>
            </a>
        </li>
			</ul>
    @if(count($channels = loadChannels()) > 0)
        
        <ul class="y-home menu1 slider-side-bar" style="margin-top: 20px;">

            <h3>{{tr('channels')}}</h3>

            @foreach($channels as $channel)
                <li id="channels_{{$channel->id}}">
                    <a href="{{route('user.channel',$channel->id)}}"><img src="{{$channel->picture}}">{{$channel->name}}</a>
                </li>
            @endforeach              
        </ul>

    @endif
	<h3 class="menu-foot-head">{{tr('contact')}}</h3>
	<div class="nav-space">

        @if(Setting::get('facebook_link'))

        <a href="{{Setting::get('facebook_link')}}" target="_blank">
            <span class="fa-stack fa-lg">
                <i class="fa fa-circle fa-stack-2x social-fb"></i>
                <i class="fa fa-facebook fa-stack-1x fa-inverse foot-share2"></i>
            </span>
        </a>

        @endif

        @if(Setting::get('twitter_link'))

        <a href="{{Setting::get('twitter_link')}}" target="_blank">
            <span class="fa-stack fa-lg">
                <i class="fa fa-circle fa-stack-2x social-twitter"></i>
                <i class="fa fa-twitter fa-stack-1x fa-inverse foot-share2"></i>
            </span>
        </a>

        @endif

        @if(Setting::get('linkedin_link'))

        <a href="{{Setting::get('linkedin_link')}}" target="_blank">
            <span class="fa-stack fa-lg">
                <i class="fa fa-circle fa-stack-2x social-linkedin"></i>
                <i class="fa fa fa-linkedin fa-stack-1x fa-inverse foot-share2"></i>
            </span>
        </a>

        @endif

        @if(Setting::get('pinterest_link'))

        <a href="{{Setting::get('pinterest_link')}}" target="_blank">
            <span class="fa-stack fa-lg">
                <i class="fa fa-circle fa-stack-2x social-pinterest"></i>
                <i class="fa fa fa-pinterest fa-stack-1x fa-inverse foot-share2"></i>
            </span>
        </a>
        @endif

        @if(Setting::get('google_link'))
        <a href="{{Setting::get('google_link')}}" target="_blank">
            <span class="fa-stack fa-lg">
                <i class="fa fa-circle fa-stack-2x social-google"></i>
                <i class="fa fa fa-google fa-stack-1x fa-inverse foot-share2"></i>
            </span>
        </a>
        @endif

    </div>
	
    <!-- ============PLAY STORE, APP STORE AND SHARE LINKS======= -->

    @if(Setting::get('appstore') || Setting::get('playstore'))

        <!--<ul class="menu-foot" style="margin-top: 10px;">

            <h3>{{tr('download_our_app')}}</h3>

            @if(Setting::get('playstore'))

            <li>
                <a href="{{Setting::get('playstore')}}" target="_blank">
                    <img src="{{asset('images/google-play.png')}}">
                </a>
            </li>

            @endif

            @if(Setting::get('appstore'))

            <li>
                <a href="{{Setting::get('appstore')}}" target="_blank">
                    <img src="{{asset('images/app_store.png')}}" >
                </a>
            </li>

            @endif

        </ul>-->

    @endif

    @if(Setting::get('facebook_link') || Setting::get('twitter_link') || Setting::get('linkedin_link') || Setting::get('pinterest_link') || Setting::get('google_link'))

    <!--<h3 class="menu-foot-head">{{tr('contact')}}</h3>

    <div class="nav-space">

        @if(Setting::get('facebook_link'))

        <a href="{{Setting::get('facebook_link')}}" target="_blank">
            <span class="fa-stack fa-lg">
                <i class="fa fa-circle fa-stack-2x social-fb"></i>
                <i class="fa fa-facebook fa-stack-1x fa-inverse foot-share2"></i>
            </span>
        </a>

        @endif

        @if(Setting::get('twitter_link'))

        <a href="{{Setting::get('twitter_link')}}" target="_blank">
            <span class="fa-stack fa-lg">
                <i class="fa fa-circle fa-stack-2x social-twitter"></i>
                <i class="fa fa-twitter fa-stack-1x fa-inverse foot-share2"></i>
            </span>
        </a>

        @endif

        @if(Setting::get('linkedin_link'))

        <a href="{{Setting::get('linkedin_link')}}" target="_blank">
            <span class="fa-stack fa-lg">
                <i class="fa fa-circle fa-stack-2x social-linkedin"></i>
                <i class="fa fa fa-linkedin fa-stack-1x fa-inverse foot-share2"></i>
            </span>
        </a>

        @endif

        @if(Setting::get('pinterest_link'))

        <a href="{{Setting::get('pinterest_link')}}" target="_blank">
            <span class="fa-stack fa-lg">
                <i class="fa fa-circle fa-stack-2x social-pinterest"></i>
                <i class="fa fa fa-pinterest fa-stack-1x fa-inverse foot-share2"></i>
            </span>
        </a>
        @endif

        @if(Setting::get('google_link'))
        <a href="{{Setting::get('google_link')}}" target="_blank">
            <span class="fa-stack fa-lg">
                <i class="fa fa-circle fa-stack-2x social-google"></i>
                <i class="fa fa fa-google fa-stack-1x fa-inverse foot-share2"></i>
            </span>
        </a>
        @endif

    </div>-->

    @endif
    
    @if(Auth::check())

        @if(!Auth::user()->user_type)

            <div class="menu4 top nav-space">
                <p>{{tr('subscribe_note')}}</p>
                <a href="{{route('user.subscriptions')}}" class="btn btn-sm btn-primary">{{tr('subscribe')}}</a>
            </div> 


        @endif

    @else
        <!--<div class="menu4 top nav-space">
            <p>{{tr('signin_nav_content')}}</p>
            <form method="get" action="{{route('user.login.form')}}">
                <button type="submit">{{tr('login')}}</button>
            </form>
        </div>-->   
    @endif             
</div>
				</div>
                    <div class="col-sm-10 col-sm-12 banner-slider" id="hide-muc" style="padding-left: 0; padding-right: 0;padding-top:0;margin-top:0;">
                        <div id="myCarousel" class="carousel slide">
                            <div class="carousel-inner">
                                @foreach($banner_ads as $key => $banner_ad)

                                <div class="{{$key == 0 ? 'active item' : 'item'}}" data-slide-number="{{$key}}" style="background-image: url({{$banner_ad->image}});">
                                    <a href="{{$banner_ad->link}}" target="_blank">

                                        <div class="carousel-caption">

                                            <h3>{{$banner_ad->video_title}}</h3>

                                            <div class="clearfix"></div>

                                            <p class="hidden-xs">@if($banner_ad->content) <?= strlen($banner_ad->content) > 200 ? substr($banner_ad->content , 0 , 200).'...' :  substr($banner_ad->content , 0 , 200)?> @endif</p>
                                        </div>
                                    </a>
                                </div>

                                @endforeach
                            </div>

                            <!-- Controls-->
                            <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
                                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                                <span class="sr-only">{{tr('previous')}}</span>
                            </a>
                            <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
                                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                                <span class="sr-only">{{tr('next')}}</span>
                            </a>
                        </div>
													<!--<div class="googleads" style="width: 100%;text-align: center;">
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
 New Ads 8 April 
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-3326961837800182"
     data-ad-slot="3790510897"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>
</div>-->

                    </div>
                </div>

                @endif

                @endif

                @include('notification.notify')

                <!-- wishlist start -->

                @include('user.home._wishlist')

                <!-- wishlist end -->

                @if(count($live_videos) > 0)

                
                
                    <div class="slide-area">
                       
                        <div class="box-head">
                            <h3>{{tr('live_videos')}}</h3>
                        </div>

                        <div class="box">

                            @foreach($live_videos as $live_video)

                            <div class="slide-box ">
                            
                                <div class="slide-image">
                                    <a href="{{$live_video->url}}">
                                        <img src="{{$live_video->video_image ?: asset('live.jpg')}}"class="slide-img1 placeholder" />
                                    </a>
                                    @if($live_video->payment_status > 0 && $live_video->amount > 0)

                                        <div class="video_amount">

                                        {{$live_video->video_payment_status ? tr('paid') : tr('pay')}} - {{formatted_amount($live_video->amount)}}

                                        </div>
                                    @endif

                                    <div class="video_mobile_views">
                                        {{$live_video->watch_count}} {{tr('views')}}
                                    </div>
                                    <div class="video_duration">
                                        {{tr('live')}}
                                    </div>
                                </div><!--end of slide-image-->

                                <div class="video-details">
                                    <div class="video-head">
                                        <a href="{{$live_video->url}}">{{$live_video->title}}</a>
                                    </div>

                                    <span class="video_views">
                                        <div><a href="{{route('user.channel',$live_video->channel_id)}}">{{$live_video->channel_name}}</a></div>
                                        <div class="hidden-mobile">
										<ul>
										<li><i class="fa fa-eye"></i> {{$live_video->watch_count}}</li>
											<li>{{tr('views')}} </li>
										</ul>
										</div>
                                    </span>
                                </div><!--end of video-details-->
								
                            </div><!--end of slide-box-->
                            @endforeach
                   
                              
                        </div><!--end of box--> 
                   <div class="googleads" style="width: 100%;text-align: center;">
					</div>
                    </div>
                    <!--end of slide-area-->

                @endif

                @if(count($recent_videos->items) > 0)
               
              
                
                    <div class="slide-area">
                       
                        <div class="box-head">
                            <h3>{{tr('recent_videos')}}</h3>
							<a href="#">See all</a>
                        </div>
						
                        <div class="box">

                            @foreach($recent_videos->items as $recent_video)
                            <div class="slide-box text-slider">
							<div class="slide-box-shadow">
                                <div class="slide-image">
                                    <a href="{{$recent_video->url}}">
                                        <!-- <img src="{{$recent_video->video_image}}" /> -->
                                        <!-- <div style="background-image: url({{$recent_video->video_image}});" class="slide-img1"></div> -->
                                        <img src="{{asset('streamtube/images/placeholder.gif')}}" data-src="{{$recent_video->video_image}}"class="slide-img1 placeholder" />
                                    </a>
                                    @if($recent_video->ppv_amount > 0)
                                        @if(!$recent_video->ppv_status)
                                            <div class="video_amount">

                                            {{tr('pay')}} - {{Setting::get('currency')}}{{$recent_video->ppv_amount}}

                                            </div>
                                        @endif
                                    @endif
                                    <div class="video_mobile_views">
                                        {{$recent_video->watch_count}} {{tr('views')}}
                                    </div>
                                    <div class="video_duration">
                                        {{$recent_video->duration}}
                                    </div>
                                </div><!--end of slide-image-->

                                <div class="video-details">
                                    <div class="video-head">
                                        <a href="{{$recent_video->url}}">{{$recent_video->title}}</a>
                                    </div>

                                    <span class="video_views">
                                        <div><a href="{{route('user.channel',$recent_video->channel_id)}}">{{$recent_video->channel_name}}</a></div>
                                        <div class="hidden-mobile">
										<ul>
										<li>
										<i class="fa fa-eye"></i> {{$recent_video->watch_count}} {{tr('views')}} </li> 
                                        <li>{{$recent_video->publish_time}}</li>
										</ul>
										</div>
                                    </span>
                                </div><!--end of video-details-->
                            </div><!--end of slide-box-->
                            </div><!--end of slide-box-->
                            @endforeach
                   
                              
                        </div><!--end of box--> 
                 
                    </div>
                    <!--end of slide-area-->
						

                @endif
<div class="googleads" style="width: 100%;text-align: center; margin-bottom:60px;">

<!-- <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script> -->
<!-- <!-- New Ads 8 April --> 
<!-- <ins class="adsbygoogle" -->
     <!-- style="display:block" -->
     <!-- data-ad-client="ca-pub-3326961837800182" -->
     <!-- data-ad-slot="3790510897" -->
     <!-- data-ad-format="auto" -->
     <!-- data-full-width-responsive="true"></ins> -->
<!-- <script> -->
     <!-- (adsbygoogle = window.adsbygoogle || []).push({}); -->
<!-- </script> -->
</div>
                @if(count($trendings->items) > 0)

               

                    <div class="slide-area">
                        <div class="box-head">
                            <h3>{{tr('trending')}}</h3>
							<a href="#">See all</a>
                        </div>

                        <div class="box">

                            @foreach($trendings->items as $trending)

                            <div class="slide-box">
                            <div class="slide-box-shadow">
                                <div class="slide-image">
                                    <a href="{{$trending->url}}">
                                        <!-- <img src="{{$trending->video_image}}" /> -->
                                        <!-- <div style="background-image: url({{$trending->video_image}});" class="slide-img1"></div> -->
                                        <img src="{{asset('streamtube/images/placeholder.gif')}}" data-src="{{$trending->video_image}}" class="slide-img1 placeholder" />
                                    </a>
                                    @if($trending->ppv_amount > 0)
                                        @if(!$trending->ppv_status)
                                            <div class="video_amount">

                                            {{tr('pay')}} - {{Setting::get('currency')}}{{$trending->ppv_amount}}

                                            </div>
                                        @endif
                                    @endif
                                    <div class="video_mobile_views">
                                        {{$trending->watch_count}} {{tr('views')}}
                                    </div>
                                    <div class="video_duration">
                                        {{$trending->duration}}
                                    </div>
                                </div><!--end of slide-image-->

                                <div class="video-details">
                                    <div class="video-head">
                                        <a href="{{$trending->url}}">{{$trending->title}}</a>
                                    </div>
                                    
                                    <span class="video_views">
                                        <div><a href="{{route('user.channel',$trending->channel_id)}}">{{$trending->channel_name}}</a></div>
                                        <div class="hidden-mobile">
										<ul>
										<li>
										<i class="fa fa-eye"></i> {{$trending->watch_count}} {{tr('views')}} </li>
                                        <li>{{$trending->publish_time}}</li>
										</ul>
										</div>
                                    </span>
                                </div><!--end of video-details-->
                            </div><!--end of slide-box-->
                            </div><!--end of slide-box-->
                            @endforeach
                   
                              
                        </div><!--end of box--> 
					
                    </div><!--end of slide-area-->

                @endif

                @if(count($suggestions->items) > 0)

               

                    <div class="slide-area suggestions-gap">
                        <div class="box-head">
                            <h3>{{tr('suggestions')}}</h3>
							<a href="#">See all</a>
                        </div>

                        <div class="box">

                            @foreach($suggestions->items as $suggestion)
                            <div class="slide-box">
                            <div class="slide-box-shadow">
                                <div class="slide-image">
                                    <a href="{{$suggestion->url}}">
                                        <!-- <img src="{{$suggestion->video_image}}" /> -->
                                       <!--  <div style="background-image: url({{$suggestion->video_image}});" class="slide-img1"></div> -->
                                       <img src="{{asset('streamtube/images/placeholder.gif')}}" data-src="{{$suggestion->video_image}}" class="slide-img1 placeholder" />
                                    </a>

                                    @if($suggestion->ppv_amount > 0)
                                        @if(!$suggestion->ppv_status)
                                            <div class="video_amount">

                                            {{tr('pay')}} - {{Setting::get('currency')}}{{$suggestion->ppv_amount}}

                                            </div>
                                        @endif
                                    @endif
                                    <div class="video_mobile_views">
                                        {{$suggestion->watch_count}} {{tr('views')}}
                                    </div>
                                    <div class="video_duration">
                                        {{$suggestion->duration}}
                                    </div>
                                </div><!--end of slide-image-->

                                <div class="video-details">
                                    <div class="video-head">
                                        <a href="{{$suggestion->url}}">{{$suggestion->title}}</a>
                                    </div>
                                   
                                    <span class="video_views">
                                        <div><a href="{{route('user.channel',$suggestion->channel_id)}}">{{$suggestion->channel_name}}</a></div>
                                        <div class="hidden-mobile">
										<ul>
										<li><i class="fa fa-eye"></i> {{$suggestion->watch_count}} {{tr('views')}} </li> 
                                        <li>{{ $suggestion->publish_time}}</li>
										</ul>
										</div>
                                    </span>
                                </div><!--end of video-details-->
                            </div><!--end of slide-box-->
                            </div><!--end of slide-box-->
                            @endforeach
                   
                              
                        </div><!--end of box--> 
                    </div><!--end of slide-area-->

                @endif

                @if($watch_lists)

                @if(count($watch_lists->items) > 0)

                 

                    <div class="slide-area">
                        <div class="box-head">
                            <h3>{{tr('watch_lists')}}</h3>
                        </div>

                        <div class="box">

                            @foreach($watch_lists->items as $watch_list)

                            <div class="slide-box">
                                <div class="slide-image">
                                    <a href="{{$watch_list->url}}">
                                        <!-- <img src="{{$watch_list->video_image}}" /> -->
                                        <img src="{{asset('streamtube/images/placeholder.gif')}}" data-src="{{$watch_list->video_image}}" class="slide-img1 placeholder" />
                                    </a>
                                    @if($watch_list->ppv_amount > 0)
                                        @if(!$watch_list->ppv_status)
                                            <div class="video_amount">

                                            {{tr('pay')}} - {{Setting::get('currency')}}{{$watch_list->ppv_amount}}

                                            </div>
                                        @endif
                                    @endif
                                    <div class="video_mobile_views">
                                        {{$watch_list->watch_count}} {{tr('views')}}
                                    </div>
                                    <div class="video_duration">
                                        {{$watch_list->duration}}
                                    </div>
                                </div><!--end of slide-image-->

                                <div class="video-details">
                                    <div class="video-head">
                                        <a href="{{$watch_list->url}}">{{$watch_list->title}}</a>
                                    </div>
                                    <span class="video_views">
                                        <div><a href="{{route('user.channel',$watch_list->channel_id)}}">{{$watch_list->channel_name}}</a></div>
                                        <div class="hidden-mobile"><i class="fa fa-eye"></i> {{ $watch_list->watch_count}} {{tr('views')}} <b>.</b> 
                                        {{$watch_list->publish_time}}</div>
                                    </span> 
                                </div><!--end of video-details-->
                            </div><!--end of slide-box-->
                            @endforeach
                   
                              
                        </div><!--end of box--> 
                    </div><!--end of slide-area-->

                @endif

                @endif
             
                <div class="sidebar-back"></div>  
            </div>

        </div>
    </div>

@endsection

@section('scripts')

<script>
$(document).ready(function(){
  $("#sidehide").click(function(){
    $("#hide-muc").toggleClass("col-sm-10");
  });
});
</script>

<script type="text/javascript">
$('#myCarousel').carousel({
    interval: 3500
});

// This event fires immediately when the slide instance method is invoked.
$('#myCarousel').on('slide.bs.carousel', function (e) {
    var id = $('.item.active').data('slide-number');
        
    // Added a statement to make sure the carousel loops correct
        if(e.direction == 'right'){
        id = parseInt(id) - 1;  
      if(id == -1) id = 7;
    } else{
        id = parseInt(id) + 1;
        if(id == $('[id^=carousel-thumb-]').length) id = 0;
    }
  
    $('[id^=carousel-thumb-]').removeClass('selected');
    $('[id=carousel-thumb-' + id + ']').addClass('selected');
});

// Thumb control
$('[id^=carousel-thumb-]').click( function(){
  var id_selector = $(this).attr("id");
  var id = id_selector.substr(id_selector.length -1);
  id = parseInt(id);
  $('#myCarousel').carousel(id);
  $('[id^=carousel-thumb-]').removeClass('selected');
  $(this).addClass('selected');
});
</script>
@endsection