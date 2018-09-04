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
                        <li class="sub-list search-list row">
                            <div class="main-history">
                                <div class="history-image text-center">
                                    <a href="#" class="">
                                        <img src="{{asset('images/default.png')}}" class="channelsearch-img">
                                    </a> 
                                </div>
                                <div class="history-title mt-15">
                                    <div class="history-head row">
                                        <div class="cross-title">
                                            <h5 class="mb-5"><a href="http://localhost:8000/video/198">5 - minute Crafts</a></h5>
                                            <span class="video_views">
                                                <div>
                                                    <i class="fa fa-eye"></i>&nbsp;100 Subscribers&nbsp;<b>.</b>&nbsp;990 videos
                                                </div>
                                            </span> 
                                        </div> 
                                        <div class="pull-right upload_a">
                                            <a class="st_video_upload_btn subscribe_btn" href="#" style="color: #fff !important;text-decoration: none">
                                                <i class="fa fa-users"></i>&nbsp;Subscribe&nbsp;25</a>
                                        </div>
                                    </div> <!--end of history-head--> 

                                    <div class="description">
                                        <div>Subscribe to 5-Minute Crafts KIDS: https://goo.gl/PEuLVt Our Social Media: Facebook: https://www.facebook.com/5min.crafts/ Instagram: https://www.instagram.com/5.min.crafts/ Have you ever seen a talking slime? Here he is – Slick Slime Sam: https://goo.gl/zarVZo The Bright Side of Youtube: https://goo.gl/rQTJZz SMART Youtube: https://goo.gl/JTfP6L For more videos and articles visit: http://www.brightside.me/</div>
                                    </div><!--end of description-->                                                     
                                </div>
                            </div>
                        </li>
                    </ul>

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
                                                        <div>
                                                            <i class="fa fa-eye"></i> {{$video->watch_count}} {{tr('views')}}<b>.</b> 
                                                            {{$video->created_at}}
                                                        </div>
                                                    </span> 
                                                </div> 
                                                                      
                                            </div> <!--end of history-head--> 

                                            <div class="description">
                                                <div><?= $video->description?></div>
                                            </div><!--end of description--> 

                                            <span class="stars">
                                               <a><i @if($video->ratings >= 1) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                               <a><i @if($video->ratings >= 2) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                               <a><i @if($video->ratings >= 3) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                               <a><i @if($video->ratings >= 4) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                               <a><i @if($video->ratings >= 5) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
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

                    @if(count($videos->items) > 0)
                        <div class="row">
                            <div class="col-md-12">
                                <div align="center" id="paglink"><?php echo $videos->pagination; ?></div>
                            </div>
                        </div>
                    @endif
                    
                </div>
                <div class="sidebar-back"></div> 
            </div>
        </div>
    </div>

@endsection