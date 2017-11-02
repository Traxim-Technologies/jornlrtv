@extends( 'layouts.user' )

@section( 'styles' )

<link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/custom-style.css')}}"> 

<style>
	#c4-header-bg-container {
		background-image: url({{$channel->cover}});
	}
	
	@media screen and (-webkit-min-device-pixel-ratio: 1.5),
	screen and (min-resolution: 1.5dppx) {
		#c4-header-bg-container {
			background-image: url({{$channel->cover}});
		}
	}
	
	#c4-header-bg-container .hd-banner-image {
		background-image: url({{$channel->cover}});
	}

	.payment_class {
		-webkit-box-orient: vertical;
		overflow: hidden;
		text-overflow: ellipsis;
		max-height: 38px;
		line-height: 19px;
		padding: 0 !important;
		font-weight: bolder !important;
	}

	.switch {
	    display: inline-block;
	    height: 23px;
	    position: relative;
	    width: 50px;
	    vertical-align: middle;
	}
	.switch input {
	    display: none;
	}
	.slider {
	    background-color: #ccc;
	    bottom: 0;
	    cursor: pointer;
	    left: 0;
	    position: absolute;
	    right: 0;
	    top: 0;
	    transition: all 0.4s ease 0s;
	}
	.slider::before {
	    background-color: white;
	    bottom: 4px;
	    content: "";
	    height: 16px;
	    left: 4px;
	    position: absolute;
	    transition: all 0.4s ease 0s;
	    width: 16px;
	}
	input:checked + .slider {
	    background-color: #51af33;
	}
	input:focus + .slider {
	    box-shadow: 0 0 1px #2196f3;
	}
	input:checked + .slider::before {
	    transform: translateX(26px);
	}
	.slider.round {
	    border-radius: 34px;
	}
	.slider.round::before {
	    border-radius: 50%;
	}


</style>

@endsection 

@section('content')

<div class="y-content">

	<div class="row content-row">

		@include('layouts.user.nav')

		<div class="page-inner col-sm-9 col-md-10">

			@include('notification.notify')

			<div class="branded-page-v2-top-row">
			
				<div class="branded-page-v2-header channel-header yt-card">
					<div id="gh-banner">
						
						<div id="c4-header-bg-container" class="c4-visible-on-hover-container  has-custom-banner">
							<div class="hd-banner">
								<div class="hd-banner-image"></div>
							</div>
	
							<a class="channel-header-profile-image spf-link" href="">
						      <img class="channel-header-profile-image" src="{{$channel->picture}}" title="{{$channel->name}}" alt="{{$channel->name}}">
						    </a>
						</div>

					</div>

					<div>
						<div class="pull-left">
							<h1 class="st_channel_heading">{{$channel->name}}</h1>
							<p class="subscriber-count">{{$subscriberscnt}} Subscribers</p>
						</div>
						<div class="pull-right upload_a">
							@if(Auth::check())
								@if($channel->user_id == Auth::user()->id)
									<a class="st_video_upload_btn" href="{{route('user.video_upload', ['id'=>$channel->id])}}"><i class="fa fa-plus-circle"></i> {{tr('upload_video')}}</a>
									<a class="st_video_upload_btn" href="{{route('user.channel_edit', $channel->id)}}"><i class="fa fa-pencil"></i> {{tr('edit_channel')}}</a>
									<a class="st_video_upload_btn" onclick="return confirm('Are you sure?');" href="{{route('user.delete.channel', ['id'=>$channel->id])}}"><i class="fa fa-trash"></i> {{tr('delete_channel')}}</a>
								@endif

								@if($channel->user_id != Auth::user()->id)

									@if (!$subscribe_status)

									<a class="st_video_upload_btn subscribe_btn" href="{{route('user.subscribe.channel', array('user_id'=>Auth::user()->id, 'channel_id'=>$channel->id))}}" style="color: #fff !important">{{tr('subscribe')}} &nbsp; {{$subscriberscnt}} </a>

									@else 

										<a class="st_video_upload_btn" href="{{route('user.unsubscribe.channel', array('subscribe_id'=>$subscribe_status))}}" onclick="return confirm('Are you sure want to Unsubscribe the channel?')">{{tr('un_subscribe')}} &nbsp; {{$subscriberscnt}}</a>

									@endif
								@else

									@if($subscriberscnt > 0)

									<a class="st_video_upload_btn subscribe_btn" href="{{route('user.channel.subscribers', array('channel_id'=>$channel->id))}}" style="color: #fff !important"><i class="fa fa-users"></i>&nbsp;{{tr('subscribers')}} &nbsp; {{$subscriberscnt}}</a>

									@endif

								@endif
							@endif
						</div>
						<div class="clearfix"></div>
					</div>

					<div id="channel-subheader" class="clearfix branded-page-gutter-padding appbar-content-trigger">
						<ul id="channel-navigation-menu" class="clearfix nav nav-tabs" role="tablist">
							<li role="presentation" class="active">
								<a href="#home1" class="yt-uix-button  spf-link  yt-uix-sessionlink yt-uix-button-epic-nav-item yt-uix-button-size-default" aria-controls="home" role="tab" data-toggle="tab"><span class="yt-uix-button-content">{{tr('home')}}</span></a>
							</li>
							<li role="presentation" id="videos_sec">
								<a href="#videos" class="yt-uix-button  spf-link  yt-uix-sessionlink yt-uix-button-epic-nav-item yt-uix-button-size-default" aria-controls="videos" role="tab" data-toggle="tab"><span class="yt-uix-button-content">{{tr('videos')}}</span> </a>
							</li>
							<li role="presentation">
								<a href="#about" class="yt-uix-button  spf-link  yt-uix-sessionlink yt-uix-button-epic-nav-item yt-uix-button-size-default" aria-controls="about" role="tab" data-toggle="tab"><span class="yt-uix-button-content">{{tr('about_video')}}</span> </a>
							</li>
							@if(Auth::check())
								@if($channel->user_id == Auth::user()->id)
									<li role="presentation" id="payment_managment_sec">
										<a href="#payment_managment" class="yt-uix-button  spf-link  yt-uix-sessionlink yt-uix-button-epic-nav-item yt-uix-button-size-default" aria-controls="payment_managment" role="tab" data-toggle="tab"><span class="yt-uix-button-content">{{tr('payment_managment')}} ($ {{getAmountBasedChannel($channel->id)}})</span> </a>
									</li>
								@endif
							@endif
						</ul>
					</div>
				</div>
			</div>

			<ul class="tab-content">

				<li role="tabpanel" class="tab-pane active" id="home1">
					<div class="feed-item-dismissable">
						<div class="feed-item-main feed-item-no-author">
							<div class="feed-item-main-content">
								<div class="shelf-wrapper clearfix">
									<div class="big-section-main">

										<h2 class="branded-page-module-title">
									        <span class="branded-page-module-title-text">
										      What to watch next
										    </span>

									  	</h2>

									  	@if(count($trending_videos) == 0)

									  		<p>{{tr('no_video_found')}}</p>

									  	@endif
										<div class="lohp-shelf-content row">
											<div class="lohp-large-shelf-container col-md-6">

												@if(count($trending_videos) > 0)

												<div class="slide-box recom-box big-box-slide">
													<div class="slide-image recom-image hbb">
														<a href="{{route('user.single', $trending_videos[0]->video_tape_id)}}"><img src="{{$trending_videos[0]->default_image}}"></a>
														<div class="video_duration">
					                                        {{$trending_videos[0]->duration}}
					                                    </div>
													</div>
													<div class="video-details recom-details">
														<div class="video-head">
															<a href="{{route('user.single', $trending_videos[0]->video_tape_id)}}"> {{$trending_videos[0]->title}}</a>
														</div>
														<?php /*<div class="sugg-description">
															<p>{{tr('duration')}}: {{$trending_videos[0]->duration}}<span class="content-item-time-created lohp-video-metadata-item"><i class="fa fa-clock-o" aria-hidden="true"></i> {{$trending_videos[0]->created_at ? $trending_videos[0]->created_at->diffForHumans() : 0}} </span>
															</p>
														</div>
														<!--end of sugg-description-->

														<span class="stars">
				                                           <a href="#"><i @if($trending_videos[0]->ratings >= 1) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
				                                           <a href="#"><i @if($trending_videos[0]->ratings >= 2) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
				                                           <a href="#"><i @if($trending_videos[0]->ratings >= 3) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
				                                           <a href="#"><i @if($trending_videos[0]->ratings >= 4) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
				                                           <a href="#"><i @if($trending_videos[0]->ratings >= 5) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
				                                        </span> */?>
				                                         <span class="video_views">
					                                        <i class="fa fa-eye"></i> {{$trending_videos[0]->watch_count}} {{tr('views')}} <b>.</b> 
					                                        {{$trending_videos[0]->created_at->diffForHumans()}}
					                                    </span>
													</div>
												</div>
												@endif
											</div>
											<div class="lohp-medium-shelves-container col-md-6">

												@if(count($trending_videos) > 1)

												@foreach($trending_videos as $index => $trending_video)

												@if ($index > 0)
												<div class="col-md-6 col-sm-6 col-xs-6">

													<div class="slide-box recom-box big-box-slide">
														<div class="slide-image recom-image hbbb">
															<a href="{{route('user.single', $trending_video->video_tape_id)}}"><img src="{{$trending_video->default_image}}"></a>
															<div class="video_duration">
						                                        {{$trending_video->duration}}
						                                    </div>
														</div>
														<!--end of slide-image-->

														<div class="video-details recom-details">
															<div class="video-head">
																<a href="{{route('user.single', $trending_video->video_tape_id)}}">{{$trending_video->title}}</a>
															</div>
															<?php /*<div class="sugg-description">
																<p>{{tr('duration')}}: {{$trending_video->duration}}<span class="content-item-time-created lohp-video-metadata-item"><i class="fa fa-clock-o" aria-hidden="true"></i> {{($trending_video->created_at) ? $trending_video->created_at->diffForHumans() : 0}}</span>
																</p>
															</div>
															<!--end of sugg-description-->

															 <span class="stars">
					                                           <a href="#"><i @if($trending_video->ratings >= 1) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
					                                           <a href="#"><i @if($trending_video->ratings >= 2) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
					                                           <a href="#"><i @if($trending_video->ratings >= 3) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
					                                           <a href="#"><i @if($trending_video->ratings >= 4) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
					                                           <a href="#"><i @if($trending_video->ratings >= 5) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
					                                        </span> */?>
					                                        <span class="video_views">
						                                        <i class="fa fa-eye"></i> {{$trending_video->watch_count}} {{tr('views')}} <b>.</b> 
						                                        {{$trending_video->created_at->diffForHumans()}}
						                                    </span>
														</div>
														<!--end of video-details-->
													</div>
												</div>

												@endif

												@endforeach

												@endif

											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</li>

				<li role="tabpanel" class="tab-pane" id="videos">

					<div class="slide-area recom-area abt-sec">
						<div class="abt-sec-head">
							
							 <div class="new-history">
					                <div class="content-head">
					                    <div><h4 style="color: #000;">{{tr('videos')}}&nbsp;&nbsp;
					                    @if(Auth::check())

					                    @if(Auth::user()->id == $channel->user_id)
					                    <small style="font-size: 12px">({{tr('note')}}:{{tr('ad_note')}} )</small>

					                    @endif

					                    @endif
					                    </h4></div>              
					                </div><!--end of content-head-->

					                @if(count($videos) > 0)

					                    <ul class="history-list">

					                        @foreach($videos as $i => $video)


					                        <li class="sub-list row">
					                            <div class="main-history">
					                                 <div class="history-image">
					                                    <a href="{{route('user.single' , $video->video_tape_id)}}"><img src="{{$video->default_image}}"></a>
					                                    <div class="video_duration">
					                                        {{$video->duration}}
					                                    </div>                        
					                                </div><!--history-image-->

					                                <div class="history-title">
					                                    <div class="history-head row">
					                                        <div class="cross-title">
					                                            <h5 class="payment_class"><a href="{{route('user.single' , $video->video_tape_id)}}">{{$video->title}}</a></h5>
					                                            <?php /*<p style="color: #000" class="duration">{{tr('duration')}}: {{$video->duration}} (<span class="content-item-time-created lohp-video-metadata-item"><i class="fa fa-clock-o" aria-hidden="true"></i> {{($video->created_at) ? $video->created_at->diffForHumans() : 0}}</span> ) </p> */?>
					                                            <span class="video_views">
							                                        <i class="fa fa-eye"></i> {{$video->watch_count}} {{tr('views')}} <b>.</b> 
							                                        {{$video->created_at->diffForHumans()}}
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


					                        <span id="videos_list"></span>

					                        <div id="video_loader"></div>
					                       
					                    </ul>

					                @else

					                   <p style="color: #000">{{tr('no_video_found')}}</p>

					                @endif

					                <?php /* @if(count($videos) > 0)

					                    @if($videos)
					                    <div class="row">
					                        <div class="col-md-12">
					                            <div align="center" id="paglink"><?php echo $videos->links(); ?></div>
					                        </div>
					                    </div>
					                    @endif 
					                @endif*/ ?>
					                
					            </div>

						</div>
					</div>


				</li>
				<li role="tabpanel" class="tab-pane" id="about">

					<div class="slide-area recom-area abt-sec">
						<div class="abt-sec-head">
							<h5>{{$channel->description}}</h5>
						</div>
					</div>


				</li>


				<li role="tabpanel" class="tab-pane" id="payment_managment">

					<div class="slide-area recom-area abt-sec">
						<div class="abt-sec-head">
							
							 <div class="new-history">
					                <div class="content-head">
					                    <div><h4 style="color: #000;">{{tr('payment_videos')}}</h4></div>              
					                </div><!--end of content-head-->

					                @if(count($payment_videos) > 0)

					                    <ul class="history-list">

					                        @foreach($payment_videos as $i => $video)


					                        <li class="sub-list row">
					                            <div class="main-history">
					                                 <div class="history-image">
					                                    <a href="{{route('user.single' , $video->video_tape_id)}}"><img src="{{$video->default_image}}"></a> 
					                                    <div class="video_duration">
					                                        {{$video->duration}}
					                                    </div>                          
					                                </div><!--history-image-->

					                                <div class="history-title">
					                                    <div class="history-head row">
					                                        <div class="cross-title">
					                                            <h5 class="payment_class"><a href="{{route('user.single' , $video->video_tape_id)}}">{{$video->title}} ($ {{$video->amount}})</a></h5>
					                                            <?php /*<p style="color: #000" class="duration">{{tr('duration')}}: {{$video->duration}} (<span class="content-item-time-created lohp-video-metadata-item"><i class="fa fa-clock-o" aria-hidden="true"></i> {{($video->created_at) ? $video->created_at->diffForHumans() : 0}}</span> ) </p> */?>

					                                            <span class="video_views">
							                                        <i class="fa fa-eye"></i> {{$video->watch_count}} {{tr('views')}} <b>.</b> 
							                                        {{$video->created_at->diffForHumans()}}
							                                    </span> 

					                                        </div> 
					                                        <div class="cross-mark">
					                                            <a onclick="return confirm('Are you sure?');" href="{{route('user.delete.video' , array('id' => $video->video_tape_id))}}"><i class="fa fa-times" aria-hidden="true"></i></a>
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

					                        <span id="payment_videos_list"></span>

					                        <div id="payment_video_loader"></div>
					                       
					                    </ul>

					                @else
					                    <p style="color: #000">{{tr('no_videos_found')}}</p>
					                @endif

					                <?php /* @if(count($payment_videos) > 0)

					                    @if($payment_videos)
					                    <div class="row">
					                        <div class="col-md-12">
					                            <div align="center" id="paglink"><?php echo $payment_videos->links(); ?></div>
					                        </div>
					                    </div>
					                    @endif
					                @endif */?>
					                
					            </div>

						</div>
					</div>

				</li>
			</ul>

		</div>

	</div>

</div>


@endsection

@section('scripts')

<script>
    
    function change_adstatus(val, id) {

        var url = "{{route('user.ad_request')}}";


        $.ajax({
            url : url,
            method : "POST",
            data : {id : id , status : val},
            success : function(result) {
                console.log(result);

                if (result == true) {
                    // window.location.reload();
                }
            }

        });

    }


    var stopScroll = false;

	var searchLength = "{{count($videos)}}";

	var stopPaymentScroll = false;

	var searchPaymentLength = "{{count($payment_videos)}}";

    
    $(window).scroll(function() {

	    if($(window).scrollTop() == $(document).height() - $(window).height()) {

	    	var value = $('ul#channel-navigation-menu').find('li.active').attr('id');

	    	//alert(value);

	    	if (value == 'videos_sec') {

		    	if (!stopScroll) {

					// console.log("New Length " +searchLength);

					if (searchLength > 0) {

						videos(searchLength);

					}

				}
			}

			if (value == 'payment_managment_sec') {

				if (!stopPaymentScroll) {

					// console.log("New Length " +searchLength);

					if (searchPaymentLength > 0) {

						payment_videos(searchPaymentLength);

					}

				}
			}

		}

	});


	function videos(cnt) {

    	channel_id = "{{$channel->id}}";

    	$.ajax({

    		type : "post",

    		url : "{{route('user.video.get_videos')}}",

    		beforeSend : function () {

				$("#video_loader").html('<h1 class="text-center"><i class="fa fa-spinner fa-spin"></i></h1>');
			},

			data : {skip : cnt, channel_id : channel_id},

			async : false,

			success : function (data) {

				$("#videos_list").append(data.view);

				if (data.length == 0) {

					stopScroll = true;

				} else {

					stopScroll = false;

					// console.log(searchLength);

					// console.log(data.length);

					searchLength = parseInt(searchLength) + data.length;

					// console.log("searchLength" +searchLength);

				}

			}, 

			complete : function() {

				$("#video_loader").html('');

			},

			error : function (data) {


			},

    	});

    }







	function payment_videos(cnt) {

    	channel_id = "{{$channel->id}}";

    	$.ajax({

    		type : "post",

    		url : "{{route('user.video.payment_mgmt_videos')}}",

    		beforeSend : function () {

				$("#payment_video_loader").html('<h1 class="text-center"><i class="fa fa-spinner fa-spin"></i></h1>');
			},

			data : {skip : cnt, channel_id : channel_id},

			async : false,

			success : function (data) {

				$("#payment_videos_list").append(data.view);

				if (data.length == 0) {

					stopPaymentScroll = true;

				} else {

					stopPaymentScroll = false;

					// console.log(searchLength);

					// console.log(data.length);

					searchPaymentLength = parseInt(searchPaymentLength) + data.length;

					// console.log("searchLength" +searchLength);

				}

			}, 

			complete : function() {

				$("#payment_video_loader").html('');

			},

			error : function (data) {


			},

    	});

    }
</script>
@endsection