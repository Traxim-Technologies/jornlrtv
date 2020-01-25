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

    <div class="y-content">

   
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
                                    <a href="{{route('user.single' , $banner_video->video_tape_id)}}"><img src="{{$banner_video->image}}" style="height:250px;width: 100%;">
                                    
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
                    <div class="col-md-12 banner-slider">
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
                    </div>
                </div>

                @endif

                @endif

                @include('notification.notify')

                <!-- wishlist start -->

                @include('user.home._wishlist')

                <!-- wishlist end -->


                @if(count($recent_videos->items) > 0)

                <hr>
                
                    <div class="slide-area">
                       
                        <div class="box-head">
                            <h3>{{tr('recent_videos')}}</h3>
                        </div>

                        <div class="box">

                            @foreach($recent_videos->items as $recent_video)
                            <div class="slide-box">
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
                                        <div class="hidden-mobile"><i class="fa fa-eye"></i> {{$recent_video->watch_count}} {{tr('views')}} <b>.</b> 
                                        {{$recent_video->publish_time}}</div>
                                    </span>
                                </div><!--end of video-details-->
                            </div><!--end of slide-box-->
                            @endforeach
                   
                              
                        </div><!--end of box--> 
                   
                    </div>
                    <!--end of slide-area-->

                @endif

                @if(count($trendings->items) > 0)

                <hr>

                    <div class="slide-area">
                        <div class="box-head">
                            <h3>{{tr('trending')}}</h3>
                        </div>

                        <div class="box">

                            @foreach($trendings->items as $trending)

                            <div class="slide-box">
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
                                        <div class="hidden-mobile"><i class="fa fa-eye"></i> {{$trending->watch_count}} {{tr('views')}} <b>.</b> 
                                        {{$trending->publish_time}}</div>
                                    </span>
                                </div><!--end of video-details-->
                            </div><!--end of slide-box-->
                            @endforeach
                   
                              
                        </div><!--end of box--> 
                    </div><!--end of slide-area-->

                @endif

                @if(count($suggestions->items) > 0)

                <hr>

                    <div class="slide-area">
                        <div class="box-head">
                            <h3>{{tr('suggestions')}}</h3>
                        </div>

                        <div class="box">

                            @foreach($suggestions->items as $suggestion)
                            <div class="slide-box">
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
                                        <div class="hidden-mobile"><i class="fa fa-eye"></i> {{$suggestion->watch_count}} {{tr('views')}} <b>.</b> 
                                        {{ $suggestion->publish_time}}</div>
                                    </span>
                                </div><!--end of video-details-->
                            </div><!--end of slide-box-->
                            @endforeach
                   
                              
                        </div><!--end of box--> 
                    </div><!--end of slide-area-->

                @endif

                @if($watch_lists)

                @if(count($watch_lists->items) > 0)

                  <hr>

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