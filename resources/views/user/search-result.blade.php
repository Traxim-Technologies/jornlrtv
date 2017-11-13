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

                        @if(count($videos))

                            @foreach($videos as $v => $video)

                                <li class="sub-list search-list row">
                                    <div class="main-history">
                                         <div class="history-image">
                                            <a href="{{route('user.single' , $video->video_tape_id)}}">
                                                <img src="{{$video->default_image}}">
                                            </a>        
                                            <div class="video_duration">
                                                {{$video->duration}}
                                            </div>                 
                                        </div><!--history-image-->

                                        <div class="history-title">
                                            <div class="history-head row">
                                                <div class="cross-title">
                                                    <h5>
                                                        <a href="{{route('user.single' , $video->video_tape_id)}}">{{$video->title}}</a></h5>
                                                    <span class="video_views">
                                                        <i class="fa fa-eye"></i> {{number_format_short($video->watch_count)}} {{tr('views')}}<b>.</b> 
                                                        {{$video->created_at->diffForHumans()}}
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

                    @if(count($videos) > 0)
                        <div class="row">
                            <div class="col-md-12">
                                <div align="center" id="paglink"><?php echo $videos->links(); ?></div>
                            </div>
                        </div>
                    @endif
                    
                </div>
                <div class="sidebar-back"></div> 
            </div>
        </div>
    </div>

@endsection