@extends('layouts.user')

@section('content')

    <div class="y-content">
        
        <div class="row content-row">

            @include('layouts.user.nav')

            <div class="page-inner col-sm-9 col-md-10">

                <div class="slide-area recom-area">

                    @include('notification.notify')

                    <div class="box-head recom-head">
                        <h3>{{tr('live_videos')}}</h3>
                    </div>

                    @if(count($videos) > 0)

                        <div class="recommend-list row">

                            @foreach($videos as $video)
                                <div class="slide-box recom-box">
                                    <div class="slide-image recom-image">

                                        <?php 

                                        $userId = Auth::check() ? Auth::user()->id : '';

                                        $url = ($video->amount > 0) ? route('user.payment_url', array('id'=>$video->id, 'user_id'=>$userId)): route('user.live_video.start_broadcasting' , array('id'=>$video->unique_id,'c_id'=>$video->channel_id));


                                        ?>
                                        <a href="{{$url}}">

                                            <img src="{{$video->snapshot}}" />

                                        </a>

                                        <div class="video_duration text-uppercase">
                                            @if($video->amount > 0) 

                                                {{tr('paid')}} - ${{$video->amount}} 

                                            @else {{tr('free')}} @endif
                                        </div>
                                    </div><!--end of slide-image-->

                                    <div class="video-details recom-details">
                                        <div class="video-head">
                                            <a href="{{$url}}">

                                            {{$video->title}}

                                            </a>
                                        </div>

                                        <span class="video_views">
                                            <i class="fa fa-eye"></i> {{$video->viewer_cnt}} {{tr('views')}} <b>.</b> 
                                            {{$video->created_at->diffForHumans()}}
                                        </span> 
                                    </div><!--end of video-details-->
                                </div><!--end of slide-box-->
                            @endforeach
                            

                        </div>

                    @else

                         <div class="recommend-list row">
                            <div class="slide-box recom-box"> {{tr('no_live_videos')}}</div>
                        </div>

                    @endif

                    <!--end of recommend-list-->

                     @if(count($videos) > 0)
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div align="center" id="paglink"><?php echo $videos->links(); ?></div>
                            </div>
                        </div>

                    @endif
                </div>

                <!--end of slide-area-->

                
            </div>

        </div>
    </div>

@endsection