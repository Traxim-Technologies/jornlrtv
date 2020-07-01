
<!-- Main Video Configuration -->

<div class="embed-responsive embed-responsive-16by9" id="main_video_setup_error" style="display: none;">
    <img src="{{asset('error.jpg')}}" class="error-image" alt="{{Setting::get('site_name')}} - Main Video">
</div>

<div class="embed-responsive embed-responsive-16by9" id="main_video_ad" style="display: none">
    <img src="" class="error-image" alt="{{Setting::get('site_name')}} - Main Video Ad" id="ad_image">

    <div class="click_here_ad" style="display: none">

    </div>

    <div class="ad_progress">
   		<div id="timings">{{tr('ad') }} : <span class="seconds"></span></div>
    	<div class="clearfix"></div>
	    <div id="progress">
        
      </div>
	</div>
</div>

@if($video->channel_subscription_amount < 0)

  <div id="main-video-player"></div>

@else

<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">{{tr('channel_subscription')}}</button>

<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">

       <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{tr('channel_subscription')}}</h4>
      </div>
        <div class="modal-body">
            
            <form action="{{  Setting::get('admin_delete_control') == YES ? '#' : route('user.channel_subscription_payment') }}" method="post" enctype="multipart/form-data">

                <div class="box-body">

                    <div class="col-md-6">

                        <div class="form-group">
                            <h4>
                                <span class="text-success"><b>{{ tr('subscription_amount') }}</b></span>-{{formatted_amount($video->channel_subscription_amount)}}
                            </h4>
                           
                            <hr>

                            <h4>
                                <span class="text-success"><b>{{tr('channel_name')}}</b></span> - {{$video->channel_name}}
                            </h4>
                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="form-group">

                            <label for="title" class="">{{ tr('amount') }}*</label>

                            <input type="number" name="amount" value="{{ old('amount') ?: $video->amount }}"  min="1"  required class="form-control" placeholder="{{ tr('amount') }}">

                        </div>

                        <input type="hidden" name="user_id" value="{{Auth::user()->id}}">

                        <input type="hidden" name="channel_id" value="{{$video->channel_id}}">

                        <div class="form-group">

                            <label for="title" class="">{{ tr('coupon_code') }}</label>

                            <input type="text" name="coupon_code" value="{{ old('coupon_code') }}" class="form-control"    placeholder="{{ tr('coupon_code') }}">

                        </div>


                    </div>

                        <div class="modal-footer">
                            <a href="" class="btn btn-danger">{{ tr('reset') }}</a>
                            <button type="submit" class="btn btn-success" @if(Setting::get('admin_delete_control') == YES) disabled @endif>{{ tr('submit') }}</button>
                        </div>

                </div>

            </form>
        </div>
        
    </div>

  </div>

</div>


@endif


 <div class="embed-responsive embed-responsive-16by9" id="flash_error_display" style="display: none;">
         <div style="width: 100%;background: black; color:#fff;height: 100%;">
               <div style="text-align: center;align-items: center;">{{tr('flash_missing')}}<a target="_blank" href="http://get.adobe.com/flashplayer/" class="underline">{{tr('adobe')}}</a>.</div>
         </div>
  </div>


@if(!check_valid_url($video->video))
    <div class="embed-responsive embed-responsive-16by9" style="display:none" id="main_video_error">
        <img src="{{asset('error.jpg')}}" class="error-image" alt="{{Setting::get('site_name')}} - Main Video">
    </div>
@endif


<div class="embed-responsive embed-responsive-16by9" id="flash_error_display" style="display: none;">
   <div style="width: 100%;background: black; color:#fff;height:350px;">

   		 <div style="text-align: center;padding-top:25%">{{tr('flash_missing')}}<a target="_blank" href="http://get.adobe.com/flashplayer/" class="underline">{{tr('adobe')}}</a>.</div>
   </div>
</div>


<!-- Trailer Video Configuration END -->