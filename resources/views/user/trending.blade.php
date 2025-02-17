@extends('layouts.user')

@section('content')

    <div class="y-content">
        
        <div class="row content-row">

            @include('layouts.user.nav')

            <div class="col-sm-12">

                <div class="slide-area slide-area1">
                    <div class="box-head recom-head">
                        <h3>{{tr('trending')}}</h3>
                    </div>


                    @if(count($videos->items) > 0)

                        <div class="recommend-list row">

                            @foreach($videos->items as $video)
                                <div class="slide-box recom-box">
                                <div class="slide-box-shadow">
                                    <div class="slide-image">
                                        <a href="{{$video->url}}">
                                            <!-- <img src="{{$video->video_image}}" /> -->
                                            <!-- <div style="background-image: url({{$video->video_image}});" class="slide-img1"></div> -->
                                            <img src="{{asset('streamtube/images/placeholder.gif')}}" data-src="{{$video->video_image}}" class="slide-img1 placeholder" />
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
                                    </div><!--end of slide-image-->

                                    <div class="video-details recom-details">
                                        <div class="video-head">
                                            <a href="{{$video->url}}">{{$video->title}}</a>
                                        </div>
                                       

                                        <span class="video_views">
                                            <div><a href="{{route('user.channel',$video->channel_id)}}"></a></div>
											<div class="hidden-mobile">
											<ul>
											<li>{{$video->channel_name}}
                                            <i class="fa fa-eye"></i> {{$video->watch_count}} {{tr('views')}} </li>
                                            <li>{{ common_date($video->created_at) }}</li>
											</ul>
											</div>
                                        </span> 
                                    </div><!--end of video-details-->
									</div>
                                </div><!--end of slide-box-->
                            @endforeach
                            

                        </div>

                    @else

                         <div class="recommend-list row">
                            <div class="slide-box recom-box"> {{tr('no_trending_videos')}}</div>
                        </div>

                    @endif

                    <!--end of recommend-list-->

                     @if(count($videos->items) > 0)

                        <div class="row">
                            <div class="col-md-12">
                                <div align="center" id="paglink"><?php echo $videos->pagination; ?></div>
                            </div>
                        </div>

                    @endif
                </div>

                <!--end of slide-area-->

                <div class="sidebar-back"></div> 
            </div>

        </div>
    </div>

@endsection