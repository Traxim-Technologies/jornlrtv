@extends('layouts.user')

@section('content')

    <div class="y-content">
        <div class="row content-row">

            @include('layouts.user.nav')
    
            <div class="history-content page-inner col-sm-9 col-md-10">
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
                                            <a href="{{route('user.single' , $video->admin_video_id)}}">
                                                <img src="{{$video->default_image}}">
                                            </a>                        
                                        </div><!--history-image-->

                                        <div class="history-title">
                                            <div class="history-head row">
                                                <div class="cross-title">
                                                    <h5>
                                                        <a href="{{route('user.single' , $video->admin_video_id)}}">{{$video->title}}</a></h5>
                                                    <p class="duration">{{tr('duration')}}: {{$video->duration}}</p>
                                                </div> 
                                                                      
                                            </div> <!--end of history-head--> 

                                            <div class="description">
                                                <p>{{$video->description}}</p>
                                            </div><!--end of description--> 

                                            <span class="stars">
                                               <a href="#"><i @if($video->rating > 1) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                               <a href="#"><i @if($video->rating > 2) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                               <a href="#"><i @if($video->rating > 3) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                               <a href="#"><i @if($video->rating > 4) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                               <a href="#"><i @if($video->rating > 5) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                            </span>                                                       
                                        </div><!--end of history-title--> 
                                        
                                    </div><!--end of main-history-->
                                </li>

                            @endforeach

                        @else

                        @endif
                       
                    </ul>
                    
                </div>
            </div>
        </div>
    </div>

@endsection