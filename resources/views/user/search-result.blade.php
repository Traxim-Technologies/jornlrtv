@extends('layouts.user')

@section('content')

    <div class="y-content">
        <div class="row content-row">

            @include('layouts.user.nav')
    
            <div class=" page-inner col-sm-9 col-md-10">
                <div class="new-history">
                    <div class="content-head search-head">
                        <div><h4>{{tr('search_result')}} "{{$key}}"</h4></div>               
                    </div><!--end of content-head-->

                    <ul class="history-list">

                        @if(count($videos->items) > 0)

                            @foreach($videos->items as $v => $video)

                                <li class="sub-list search-list row">
                                    <div class="main-history">
                                         <div class="history-image">
                                            <a href="{{$video->url}}">
                                                <img src="{{$video->video_image}}">
                                            </a>        
                                            @if($video->ppv_amount > 0)
                                                @if(!$video->ppv_status)
                                                    <div class="video_amount">

                                                    {{tr('pay')}} - {{Setting::get('currency')}}{{$video->ppv_amount}}

                                                    </div>
                                                @endif
                                            @endif
                                            <div class="video_duration">
                                                {{$video->duration}}
                                            </div>                 
                                        </div><!--history-image-->

                                        <div class="history-title">
                                            <div class="history-head row">
                                                <div class="cross-title">
                                                    <h5>
                                                        <a href="{{$video->url}}">{{$video->title}}</a></h5>
                                                    <span class="video_views">
                                                         <div><a href="{{route('user.channel',$video->channel_id)}}">{{$video->channel_name}}</a></div>
                                                        <i class="fa fa-eye"></i> {{$video->watch_count}} {{tr('views')}}<b>.</b> 
                                                        {{$video->created_at}}
                                                    </span> 
                                                </div> 
                                                                      
                                            </div> <!--end of history-head--> 

                                            <div class="description">
                                                <p>{{$video->description}}</p>
                                            </div><!--end of description--> 

                                            <span class="stars">
                                               <a href="#"><i @if($video->ratings >= 1) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                               <a href="#"><i @if($video->ratings >= 2) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                               <a href="#"><i @if($video->ratings >= 3) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                               <a href="#"><i @if($video->ratings >= 4) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                               <a href="#"><i @if($video->ratings >= 5) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                            </span>                                                       
                                        </div><!--end of history-title--> 
                                        
                                    </div><!--end of main-history-->
                                </li>

                            @endforeach

                        @else

                            <!-- <p class="no-result">{{tr('no_search_result')}}</p> -->
                            <img src="{{asset('images/no-result.jpg')}}" class="img-responsive auto-margin">

                        @endif
                       
                    </ul>

                    @if(count($videos->items) > 16)
                        <div class="row">
                            <div class="col-md-12">
                                <div align="right" id="paglink">
                                     <a href="{{route('user.trending')}}" class="btn btn-sm btn-danger text-uppercase">{{tr('see_all')}}</a>

                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    @endif

                    <hr>


                    <ul class="history-list">

                        @if(count($live_videos) > 0)

                            <div><h5 class="text-uppercase" style="font-weight: bold;">{{tr('live_video_title')}} "{{$key}}"</h5></div>

                            @foreach($live_videos as $v => $live_video)

                                <?php 

                                    $userId = Auth::check() ? Auth::user()->id : '';

                                    $url = ($live_video->amount > 0) ? route('user.payment_url', array('id'=>$live_video->id, 'user_id'=>$userId)): route('user.live_video.start_broadcasting' , array('id'=>$live_video->unique_id,'c_id'=>$live_video->channel_id));

                                    ?>

                                <li class="sub-list search-list row">
                                    <div class="main-history">
                                         <div class="history-image">
                                            <a href="{{$url}}">
                                                <img src="{{$live_video->snapshot}}">
                                            </a>        
                                            <div class="video_duration text-uppercase">
                                                @if($live_video->amount > 0) 

                                                {{tr('paid')}} - ${{$live_video->amount}} 

                                                @else {{tr('free')}} @endif
                                            </div>                 
                                        </div><!--history-image-->

                                        <div class="history-title">
                                            <div class="history-head row">
                                                <div class="cross-title">
                                                    <h5>
                                                        <a href="{{$url}}">{{$live_video->title}}</a></h5>
                                                    <span class="video_views">
                                                        <i class="fa fa-eye"></i> {{$live_video->viewer_cnt}} {{tr('views')}}<b>.</b> 
                                                        {{$live_video->created_at->diffForHumans()}}
                                                    </span> 
                                                </div> 
                                                                      
                                            </div> <!--end of history-head--> 

                                            <div class="description">
                                                <p>{{$live_video->description}}</p>
                                            </div><!--end of description--> 

                                                                                                  
                                        </div><!--end of history-title--> 
                                        
                                    </div><!--end of main-history-->
                                </li>

                            @endforeach

                        @else

                            <p class="no-result">{{tr('no_search_result')}}</p>

                        @endif
                       
                    </ul>

                    @if(count($live_videos) > 16)
                        <div class="row">
                            <div class="col-md-12">
                                <div align="right" id="paglink">

                                    <?php //echo $live_videos->links(); ?>

                                    <a href="{{route('user.live_videos')}}" class="btn btn-sm btn-danger text-uppercase">{{tr('see_all')}}</a>
                                        
                                </div>
                            </div>
                        </div>
                    @endif
                    
                </div>
                <div class="sidebar-back"></div> 
            </div>
        </div>
    </div>

@endsection