@extends('layouts.user')

@section('content')

    <div class="y-content">
        <div class="row content-row">

            @include('layouts.user.nav')

            <div class="page-inner col-sm-9 col-md-10">


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
                                </div><!--end of slide-image-->

                                <div class="video-details">
                                    <div class="video-head">
                                        <a href="{{route('user.single' , $recent_video->admin_video_id)}}">{{$recent_video->title}}</a>
                                    </div>
                                    <div class="sugg-description">
                                        <p>{{tr('duration')}}: {{$recent_video->duration}}</p>
                                    </div><!--end of sugg-description--> 

                                    <span class="stars">
                                        <a href="#"><i @if($recent_video->rating > 1) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($recent_video->rating > 2) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($recent_video->rating > 3) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($recent_video->rating > 4) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($recent_video->rating > 5) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
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
                                </div><!--end of slide-image-->

                                <div class="video-details">
                                    <div class="video-head">
                                        <a href="{{route('user.single' , $trending->admin_video_id)}}">{{$trending->title}}</a>
                                    </div>
                                    <div class="sugg-description">
                                        <p>{{tr('duration')}}: {{$trending->duration}}</p>
                                    </div><!--end of sugg-description--> 

                                    <span class="stars">
                                        <a href="#"><i @if($trending->rating > 1) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($trending->rating > 2) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($trending->rating > 3) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($trending->rating > 4) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($trending->rating > 5) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
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
                                </div><!--end of slide-image-->

                                <div class="video-details">
                                    <div class="video-head">
                                        <a href="{{route('user.single' , $suggestion->admin_video_id)}}">{{$suggestion->title}}</a>
                                    </div>
                                    <div class="sugg-description">
                                        <p>{{tr('duration')}}: {{$suggestion->duration}}</p>
                                    </div><!--end of sugg-description--> 

                                    <span class="stars">
                                        <a href="#"><i @if($suggestion->rating > 1) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($suggestion->rating > 2) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($suggestion->rating > 3) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($suggestion->rating > 4) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($suggestion->rating > 5) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
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
                                </div><!--end of slide-image-->

                                <div class="video-details">
                                    <div class="video-head">
                                        <a href="{{route('user.single' , $watch_list->admin_video_id)}}">{{$watch_list->title}}</a>
                                    </div>
                                    <div class="sugg-description">
                                        <p>{{tr('duration')}}: {{$watch_list->duration}}</p>
                                    </div><!--end of sugg-description--> 

                                    <span class="stars">
                                        <a href="#"><i @if($watch_list->rating > 1) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($watch_list->rating > 2) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($watch_list->rating > 3) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($watch_list->rating > 4) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i @if($watch_list->rating > 5) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
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