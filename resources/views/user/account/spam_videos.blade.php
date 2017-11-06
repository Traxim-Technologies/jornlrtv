@extends('layouts.user')

@section('content')

<div class="y-content">
    <div class="row content-row">

        @include('layouts.user.nav')

        <div class="history-content page-inner col-sm-9 col-md-10">
            
            @include('notification.notify')

            <div class="new-history">
                <div class="content-head">
                    <div><h4>{{tr('spam_videos')}}</h4></div>              
                </div><!--end of content-head-->

                @if(count($model->data) > 0)

                    <ul class="history-list">
                    
                        @foreach($model->data as $i => $spamvideo)

                        <li class="sub-list row">
                            <div class="main-history">
                                 <div class="history-image">
                                    <a href="{{route('user.single' , $spamvideo->video_tape_id)}}"><img src="{{$spamvideo->video_tape->default_image}}"></a>
                                    <div class="video_duration">
                                        {{$spamvideo->video_tape->duration}}
                                    </div>                        
                                </div><!--history-image-->

                                <div class="history-title">
                                    <div class="history-head row">
                                        <div class="cross-title1">
                                            <h5><a href="{{route('user.single' , $spamvideo->video_tape_id)}}">{{$spamvideo->video_tape->title}}</a></h5>
                                            <!-- <p class="duration">{{tr('duration')}}: {{$spamvideo->video_tape->duration}}</p> -->
                                            <span class="video_views">
                                                <i class="fa fa-eye"></i> {{number_format_short($spamvideo->video_tape->watch_count)}} {{tr('views')}} 
                                                <?php /*<b>.</b> 
                                                {{$history->video_tape->created_at->diffForHumans()}}*/?>
                                            </span>
                                        </div> 
                                        <div class="cross-mark1">
                                            <a onclick="return confirm('Are you sure?');" href="{{route('user.remove.report_video',$spamvideo->id)}}"><i class="fa fa-times" aria-hidden="true"></i></a>
                                        </div><!--end of cross-mark-->                       
                                    </div> <!--end of history-head--> 

                                    <div class="description">
                                        <p>{{$spamvideo->video_tape->description}}</p>
                                    </div><!--end of description--> 

                                    <span class="stars">
                                        <a href="#"><i @if($spamvideo->video_tape->ratings >= 1) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                        <a href="#"><i @if($spamvideo->video_tape->ratings >= 2) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                        <a href="#"><i @if($spamvideo->video_tape->ratings >= 3) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                        <a href="#"><i @if($spamvideo->video_tape->ratings >= 4) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                        <a href="#"><i @if($spamvideo->video_tape->ratings >= 5) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                    </span>                                               
                                </div><!--end of history-title--> 
                                
                            </div><!--end of main-history-->
                        </li>    

                        @endforeach
                       
                    </ul>

                @else
                    <p>{{tr('no_spam_found')}}</p>
                @endif

                @if(count($model->data) > 0)

                    @if($model->pagination)
                    <div class="row">
                        <div class="col-md-12">
                            <div align="center" id="paglink"><?php echo $model->pagination; ?></div>
                        </div>
                    </div>
                    @endif
                @endif
                
            </div>
        
        </div>

    </div>
</div>

@endsection