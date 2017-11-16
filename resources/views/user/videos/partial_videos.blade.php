@foreach($videos as $i => $video)

    <li class="sub-list row">
        <div class="main-history">
             <div class="history-image">
                <a href="{{$video->url}}"><img src="{{$video->video_image}}"></a>
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
                        <h5 class="payment_class"><a href="{{$video->url}}">{{$video->title}}</a></h5>
                        <?php /*<p style="color: #000" class="duration">{{tr('duration')}}: {{$video->duration}} (<span class="content-item-time-created lohp-video-metadata-item"><i class="fa fa-clock-o" aria-hidden="true"></i> {{($video->created_at) ? $video->created_at->diffForHumans() : 0}}</span> ) </p> */?>
                        <span class="video_views">
                            <i class="fa fa-eye"></i> {{$video->watch_count}} {{tr('views')}} <b>.</b> 
                            {{$video->created_at}}
                        </span>
                    </div> 
                    @if(Auth::check())
					@if($channel->user_id == Auth::user()->id)
                    <div class="cross-mark">
                        <a title="delete" onclick="return confirm('Are you sure?');" href="{{route('user.delete.video' , array('id' => $video->video_tape_id))}}" class="btn btn-danger btn-sm"><i class="fa fa-times" style="color:#fff" aria-hidden="true"></i></a>
                        <a title="edit" style="display:inline-block;" href="{{route('user.edit.video', $video->video_tape_id)}}"  class="btn btn-warning btn-sm"><i class="fa fa-edit" aria-hidden="true" style="color:#fff"></i></a>

                        <label style="float:none" class="switch" title="{{$video->ad_status ? tr('disable_ad') : tr('enable_ad')}}">
                            <input id="change_adstatus_id" type="checkbox" @if($video->ad_status) checked @endif onchange="change_adstatus(this.value, {{$video->video_tape_id}})">
                            <div class="slider round"></div>
                        </label>
                    </div>
                    @endif
                    @endif

                    <!--end of cross-mark-->                       
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