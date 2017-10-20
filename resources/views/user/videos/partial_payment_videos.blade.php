 @foreach($payment_videos as $i => $video)


    <li class="sub-list row">
        <div class="main-history">
             <div class="history-image">
                <a href="{{route('user.single' , $video->admin_video_id)}}"><img src="{{$video->default_image}}"></a> 
                <div class="video_duration">
                    {{$video->duration}}
                </div>                          
            </div><!--history-image-->

            <div class="history-title">
                <div class="history-head row">
                    <div class="cross-title">
                        <h5 class="payment_class"><a href="{{route('user.single' , $video->admin_video_id)}}">{{$video->title}} ($ {{$video->amount}})</a></h5>
                        <?php /*<p style="color: #000" class="duration">{{tr('duration')}}: {{$video->duration}} (<span class="content-item-time-created lohp-video-metadata-item"><i class="fa fa-clock-o" aria-hidden="true"></i> {{($video->created_at) ? $video->created_at->diffForHumans() : 0}}</span> ) </p> */?>

                        <span class="video_views">
                            <i class="fa fa-eye"></i> {{$video->watch_count}} {{tr('views')}} <b>.</b> 
                            {{$video->created_at->diffForHumans()}}
                        </span> 

                    </div> 
                    <div class="cross-mark">
                        <a onclick="return confirm('Are you sure?');" href="{{route('user.delete.video' , array('id' => $video->admin_video_id))}}"><i class="fa fa-times" aria-hidden="true"></i></a>
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