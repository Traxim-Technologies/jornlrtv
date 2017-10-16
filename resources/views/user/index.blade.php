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

            <div class="page-inner col-sm-9 col-md-10">

                @if(Setting::get('is_banner_video'))


                @if(count($banner_videos) > 0)

                <div class="row" id="slider">
                    <div class="col-md-10 col-md-offset-1">
                        <div id="myCarousel" class="carousel slide">
                            <div class="carousel-inner">
                                @foreach($banner_videos as $key => $banner_video)
                                <div class="{{$key == 0 ? 'active item' : 'item'}}" data-slide-number="{{$key}}">
                                    <a href="{{route('user.single' , $banner_video->video_tape_id)}}"><img src="{{$banner_video->image}}" style="height:250px;width: 100%;">
                                    <?php /*<div class="carousel-caption">
                                        <h3>{{$banner_video->video_title}}</h3>
                                        <p>{{substr($banner_video->content , 0 , 200)}}...</p>
                                    </div> */?>
                                    </a>
                                </div>
                                @endforeach
                            </div>

                            <!-- Controls-->
                            <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
                                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
                                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                    </div>
                </div>

                @endif

                @endif

                @if(Setting::get('is_banner_ad'))

                @if(count($banner_ads) > 0)

                <div class="row" id="slider">
                    <div class="col-md-10 col-md-offset-1">
                        <div id="myCarousel" class="carousel slide">
                            <div class="carousel-inner">
                                @foreach($banner_ads as $key => $banner_ad)
                                <div class="{{$key == 0 ? 'active item' : 'item'}}" data-slide-number="{{$key}}">
                                    <a href="{{$banner_ad->link}}" target="_blank"><img src="{{$banner_ad->image}}" style="height:250px;width: 100%;">
                                    <?php /*<div class="carousel-caption">
                                        <h3>{{$banner_ad->video_title}}</h3>
                                        <p><?= substr($banner_ad->content , 0 , 200)?>...</p>
                                    </div>*/?>
                                    </a>
                                </div>
                                @endforeach
                            </div>

                            <!-- Controls-->
                            <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
                                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
                                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                    </div>
                </div>

                @endif

                @endif





                @include('notification.notify')

                @if(count($wishlists) > 0)

                    <div class="slide-area">
                        <div class="box-head">
                            <h3>{{tr('wishlist')}}</h3>
                        </div>

                        <div class="box">

                            @foreach($wishlists as $wishlist)
                            <div class="slide-box">
                                <div class="slide-image">
                                    <a href="{{route('user.single' , $wishlist->admin_video_id)}}"><img src="{{$wishlist->default_image}}" /></a>

                                    <div class="video_duration">
                                        {{$wishlist->duration}}
                                    </div>
                                </div><!--end of slide-image-->

                                <div class="video-details">
                                    <div class="video-head">
                                        <a href="{{route('user.single' , $wishlist->admin_video_id)}}">{{$wishlist->title}}</a>
                                    </div>

                                    <?php /* 
                                    <div class="sugg-description">
                                        <p>{{tr('duration')}}: {{$wishlist->duration}}</p>
                                    </div>

                                    <!--end of sugg-description--> 

                                     <span class="stars">
                                        <a href="#"><i @if($wishlist->ratings >= 1) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($wishlist->ratings >= 2) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($wishlist->ratings >= 3) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($wishlist->ratings >= 4) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($wishlist->ratings >= 5) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                    </span>  */?>
                                    <span class="video_views">
                                        <i class="fa fa-eye"></i> {{$wishlist->watch_count}} {{tr('views')}} <b>.</b> 
                                        {{$wishlist->created_at->diffForHumans()}}
                                    </span>
                                </div><!--end of video-details-->
                            </div><!--end of slide-box-->
                            @endforeach
                   
                              
                        </div><!--end of box--> 
                    </div><!--end of slide-area-->

                @endif


                @if(count($recent_videos) > 0)

                    <div class="slide-area">
                        <div class="box-head">
                            <h3>{{tr('recent_videos')}}</h3>
                        </div>

                        <div class="box">

                            @foreach($recent_videos as $recent_video)
                            <div class="slide-box">
                                <div class="slide-image">
                                    <a href="{{route('user.single' , $recent_video->admin_video_id)}}"><img src="{{$recent_video->default_image}}" /></a>
                                    <div class="video_duration">
                                        {{$recent_video->duration}}
                                    </div>
                                </div><!--end of slide-image-->

                                <div class="video-details">
                                    <div class="video-head">
                                        <a href="{{route('user.single' , $recent_video->admin_video_id)}}">{{$recent_video->title}}</a>
                                    </div>

                                    <?php /*
                                    <div class="sugg-description">
                                        <p>{{tr('duration')}}: {{$recent_video->duration}}</p>
                                    </div><!--end of sugg-description--> 

                                    <span class="stars">
                                        <a href="#"><i @if($recent_video->ratings >= 1) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($recent_video->ratings >= 2) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($recent_video->ratings >= 3) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($recent_video->ratings >= 4) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($recent_video->ratings >= 5) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                    </span>  */?>
                                    <span class="video_views">
                                        <i class="fa fa-eye"></i> {{$recent_video->watch_count}} {{tr('views')}} <b>.</b> 
                                        {{$recent_video->created_at->diffForHumans()}}
                                    </span>
                                </div><!--end of video-details-->
                            </div><!--end of slide-box-->
                            @endforeach
                   
                              
                        </div><!--end of box--> 
                    </div><!--end of slide-area-->

                @endif

                @if(count($trendings) > 0)

                    <div class="slide-area">
                        <div class="box-head">
                            <h3>{{tr('trending')}}</h3>
                        </div>

                        <div class="box">

                            @foreach($trendings as $trending)
                            <div class="slide-box">
                                <div class="slide-image">
                                    <a href="{{route('user.single' , $trending->admin_video_id)}}"><img src="{{$trending->default_image}}" /></a>
                                    <div class="video_duration">
                                        {{$trending->duration}}
                                    </div>
                                </div><!--end of slide-image-->

                                <div class="video-details">
                                    <div class="video-head">
                                        <a href="{{route('user.single' , $trending->admin_video_id)}}">{{$trending->title}}</a>
                                    </div>
                                    <?php /*<div class="sugg-description">
                                        <p>{{tr('duration')}}: {{$trending->duration}}</p>
                                    </div><!--end of sugg-description--> 

                                    <span class="stars">
                                        <a href="#"><i @if($trending->ratings >= 1) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($trending->ratings >= 2) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($trending->ratings >= 3) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($trending->ratings >= 4) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($trending->ratings >= 5) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                    </span> */?>

                                    <span class="video_views">
                                        <i class="fa fa-eye"></i> {{$trending->watch_count}} {{tr('views')}} <b>.</b> 
                                        {{$trending->created_at->diffForHumans()}}
                                    </span>
                                </div><!--end of video-details-->
                            </div><!--end of slide-box-->
                            @endforeach
                   
                              
                        </div><!--end of box--> 
                    </div><!--end of slide-area-->

                @endif

                @if(count($suggestions) > 0)

                    <div class="slide-area">
                        <div class="box-head">
                            <h3>{{tr('suggestions')}}</h3>
                        </div>

                        <div class="box">

                            @foreach($suggestions as $suggestion)
                            <div class="slide-box">
                                <div class="slide-image">
                                    <a href="{{route('user.single' , $suggestion->admin_video_id)}}"><img src="{{$suggestion->default_image}}" /></a>
                                    <div class="video_duration">
                                        {{$suggestion->duration}}
                                    </div>
                                </div><!--end of slide-image-->

                                <div class="video-details">
                                    <div class="video-head">
                                        <a href="{{route('user.single' , $suggestion->admin_video_id)}}">{{$suggestion->title}}</a>
                                    </div>
                                    <?php /*<div class="sugg-description">
                                        <p>{{tr('duration')}}: {{$suggestion->duration}}</p>
                                    </div><!--end of sugg-description--> 

                                    <span class="stars">
                                        <a href="#"><i @if($suggestion->ratings >= 1) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($suggestion->ratings >= 2) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($suggestion->ratings >= 3) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($suggestion->ratings >= 4) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($suggestion->ratings >= 5) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                    </span> */?>
                                    <span class="video_views">
                                        <i class="fa fa-eye"></i> {{$suggestion->watch_count}} {{tr('views')}} <b>.</b> 
                                        {{$suggestion->created_at->diffForHumans()}}
                                    </span>
                                </div><!--end of video-details-->
                            </div><!--end of slide-box-->
                            @endforeach
                   
                              
                        </div><!--end of box--> 
                    </div><!--end of slide-area-->

                @endif

                @if(count($watch_lists) > 0)

                    <div class="slide-area">
                        <div class="box-head">
                            <h3>{{tr('watch_lists')}}</h3>
                        </div>

                        <div class="box">

                            @foreach($watch_lists as $watch_list)

                            <div class="slide-box">
                                <div class="slide-image">
                                    <a href="{{route('user.single' , $watch_list->admin_video_id)}}"><img src="{{$watch_list->default_image}}" /></a>
                                    <div class="video_duration">
                                        {{$watch_list->duration}}
                                    </div>
                                </div><!--end of slide-image-->

                                <div class="video-details">
                                    <div class="video-head">
                                        <a href="{{route('user.single' , $watch_list->admin_video_id)}}">{{$watch_list->title}}</a>
                                    </div>
                                    <?php /*<div class="sugg-description">
                                        <p>{{tr('duration')}}: {{$watch_list->duration}}</p>
                                    </div><!--end of sugg-description--> 

                                    <span class="stars">
                                        <a href="#"><i @if($watch_list->ratings >= 1) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($watch_list->ratings >= 2) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($watch_list->ratings >= 3) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($watch_list->ratings >= 4) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($watch_list->ratings >= 5) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                    </span>*/?>

                                    <span class="video_views">
                                        <i class="fa fa-eye"></i> {{$watch_list->watch_count}} {{tr('views')}} <b>.</b> 
                                        {{$watch_list->created_at->diffForHumans()}}
                                    </span> 
                                </div><!--end of video-details-->
                            </div><!--end of slide-box-->
                            @endforeach
                   
                              
                        </div><!--end of box--> 
                    </div><!--end of slide-area-->

                @endif
               
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