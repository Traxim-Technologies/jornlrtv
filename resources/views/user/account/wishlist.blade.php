@extends('layouts.user')

@section('content')

<div class="y-content">
    <div class="row content-row">

        @include('layouts.user.nav')

        <div class="history-content page-inner col-sm-9 col-md-10">
            
            @include('notification.notify')

            <div class="new-history">
                <div class="content-head">
                    <div><h4>{{tr('wishlist')}}</h4></div>              
                </div><!--end of content-head-->

                @if(count($videos->items) > 0)

                    <ul class="history-list">

                        @foreach($videos->items as $i => $video)

                        <li class="sub-list row">
                            <div class="main-history">
                                 <div class="history-image">
                                    <a href="{{route('user.single' , $video->video_tape_id)}}"><img src="{{$video->video_image}}"></a>
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
                                        <div class="cross-title1">
                                            <h5><a href="{{route('user.single' , $video->video_tape_id)}}">{{$video->title}}</a></h5>
                                            <span class="video_views">
                                                <div><a href="{{route('user.channel',$video->channel_id)}}">{{$video->channel_name}}</a></div>
                                                <i class="fa fa-eye"></i> {{$video->watch_count}} {{tr('views')}} <b>.</b> 
                                                {{$video->created_at}}
                                            </span> 
                                        </div> 
                                        <div class="cross-mark1">
                                            <a onclick="return confirm('Are you sure?');" href="{{route('user.delete.wishlist' , array('video_tape_id' => $video->video_tape_id))}}"><i class="fa fa-times" aria-hidden="true"></i></a>
                                        </div><!--end of cross-mark-->                       
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
                       
                    </ul>

                @else
                    <p>{{tr('no_wishlist_found')}}</p>
                @endif

                @if(count($videos->items) > 0)

                    @if($videos->items)
                    <div class="row">
                        <div class="col-md-12">
                            <div align="center" id="paglink"><?php echo $videos->pagination; ?></div>
                        </div>
                    </div>
                    @endif
                @endif
                
            </div>
            <div class="sidebar-back"></div> 
        </div>

    </div>
</div>

@endsection