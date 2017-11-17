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
	    width: 45px;
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
	    left: 0px;
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

		<div class="page-inner col-sm-9 col-md-10 p-t-5">


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
							<h1 class="st_channel_heading text-uppercase">{{$channel->name}}</h1>
							<?php /*<p class="subscriber-count">{{$subscriberscnt}} Subscribers</p> */?>
						</div>
						<div class="pull-right upload_a">
							@if(Auth::check())

								@if($channel->user_id == Auth::user()->id)

									<a class="st_video_upload_btn" href="{{route('user.video_upload', ['id'=>$channel->id])}}">
										<i class="fa fa-plus-circle"></i> 
										{{tr('upload_video')}}
									</a>

									@if(Setting::get('broadcast_by_user') == 1 || Auth::user()->is_master_user == 1)

										<button class="st_video_upload_btn text-uppercase" data-toggle="modal" data-target="#start_broadcast">
											<i class="fa fa-video-camera"></i> 
											{{tr('start_broadcast')}}
										</button>

									@endif

									<a class="st_video_upload_btn" href="{{route('user.channel_edit', $channel->id)}}">
										<i class="fa fa-pencil"></i> 
										{{tr('edit_channel')}}
									</a>

									<a class="st_video_upload_btn" onclick="return confirm('Are you sure?');" href="{{route('user.delete.channel', ['id'=>$channel->id])}}">
										<i class="fa fa-trash"></i> {{tr('delete_channel')}}
									</a>


									@if(Setting::get('broadcast_by_user') == 1 || Auth::check() ? Auth::user()->is_master_user == 1 : 0)

										<div id="start_broadcast" class="modal fade" role="dialog">
										    <div class="modal-dialog">

										    <!-- Modal content-->
										    <div class="modal-content">
										        <div class="modal-header start_brocadcast_form">
										            <button type="button" class="close" data-dismiss="modal">&times;</button>
										        	<h4 class="modal-title text-uppercase text-center">{{tr('start_broadcast')}}</h4>
										        </div>

										        <div class="modal-body body-modal">

										      	<form method="post" action="{{route('user.live_video.broadcast')}}">

											      	<input type="hidden" name="channel_id" value="{{$channel->id}}">

											      	<input type="hidden" name="user_id" value="{{$channel->user_id}}">

													<!-- Text input-->

				                                    <div class="form-group form-group1">
				                                    	<input type="text" class="form-control signup-form1" placeholder="Enter Title" id="title" name="title" required="">
				                                   	</div>

				                                    <!-- <div class="row">
					                                    <label class="col-md-3 control-label title-form" for="sms">{{tr('title')}}</label>
					                                    <div class="col-md-9">
					                                      <input id="title" name="title" type="text" placeholder="Video Title" class="form-control" required="">
					                                    </div>

					                                    <div class="clearfix"></div>
				                                    </div> -->
				                                  

				                                 <!--  <br> -->


				                                  <!-- Multiple Radios (inline) -->
	 			                                  <!--<div class="row">
				                                    <label class="col-md-3 control-label" for="reqType">{{tr('payment')}}</label>
				                                    <div class="col-md-9">
				                                      <label class="radio-inline" for="reqType-1">
				                                        <input type="radio" name="payment_status" id="reqType-1" value="0" checked  onchange="return $('#price').hide();">
				                                        Free </label>
				                                      <label class="radio-inline" for="reqType-0">
				                                        <input type="radio" name="payment_status" id="reqType-0" value="1" onchange="return $('#price').show()">
				                                        Paid </label>
				                                    </div>
				                                  </div> -->

				                                 	<!-- payment -->
				                                 	@if(!Setting::get('is_subscription') || Setting::get('is_default_paid_user'))

				                                 		<input type="hidden"  name="payment_status" value="0">

				                                 	@else

														<div class="form-group radio-btn">

															<label class="control-label col-sm-3 zero-padding" for="optradio">{{tr('payment')}}</label>

															<div class="col-sm-8">

															    <label class="radio-inline width-100" for="reqType-1">
																	<input type="radio" id="reqType-1" checked="checked" class="option-input radio" name="payment_status" onchange="return $('#price').hide();" value="0">Free
																</label>
																<label class="radio-inline">
																	<input type="radio" id="reqType-0" class="option-input radio" name="payment_status" onchange="return $('#price').show()" value="1">Paid
															    </label>
													      	</div>
														
														</div>

					                                  	<div class="clearfix"></div>

				                                  	@endif

				                                  <!-- Multiple Radios (inline) -->
				                                  <!-- <div class="row" style="display: none">
				                                    <label class="col-md-3 control-label" for="dataFormat">{{tr('type')}}</label>
				                                    <div class="col-md-9">
				                                      <label class="radio-inline" for="dataFormat-0">
				                                        <input type="radio" name="type" value="public" checked onchange="return $('#price').hide();">
				                                        Public </label>
				                                      <label class="radio-inline" for="dataFormat-1">
				                                        <input type="radio" name="type" id="dataFormat-1" value="private" onchange="return $('#price').show()">
				                                        Private </label>
				                                    </div>
				                                  </div> -->
				                                  

				                                  <!-- Multiple Checkboxes (inline) -->
				                                 <!--  <div class="row" style="display: none" id="price">
				                                    <label class="col-md-3 control-label title-form" for="sms">{{tr('amount')}}</label>
				                                    <div class="col-md-9">
				                                      <input id="Amount" name="amount" type="number" placeholder="Amount" class="form-control" pattern="[0-9]{0,}">
				                                    </div>
				                                  </div> -->

				                                 	<!-- ======amount===== -->
					                                <div class="form-group form-group1" style="display: none" id="price">
					                                    <input id="Amount" name="amount" type="number" placeholder="Amount" pattern="[0-9]{0,}" class="form-control signup-form1">
					                                </div>
				                                  

					                                <!-- ===========description======= -->
				                                  <!-- <div class="row">
				                                    <label class="col-md-3 control-label title-form" for="sms">{{tr('description')}}</label>
				                                    <div class="col-md-9">
				                                      <textarea id="description" name="description" placeholder="{{tr('description')}}" class="form-control" ng-model="description" required></textarea>
				                                    </div>
				                                  </div>
				                                  <br> -->

				                                    <div class="form-group form-group1">
													    <textarea id="description" name="description" class="form-control signup-form1" rows="5" id="comment" placeholder="{{tr('description')}}"></textarea>
													</div>


				                                  <!-- Button (Double) -->
	 			                                  <!-- <div class="row">
				                                    <label class="col-md-3 control-label" for="submitButton"></label>
				                                    <div class="col-md-9">
				                                      <div class="button-form-f">
				                                        <button type="submit" id="submitButton" name="submitButton" class="btn btn-danger" style="background: #ff0000">{{tr('broadcast')}}</button>
				                                        <button type="reset" value="reset" id="reset" name="reset" class="btn btn-default" data-dismiss="modal">{{tr('cancel')}}</button>
				                                      </div>
				                                    </div>
				                                  </div> -->

				                                @if(Setting::get('broadcast_by_user') == 1 || Auth::user()->is_master_user == 1) 
				                                  	<button class="signup-btn submit" type="submit" id="submitButton" name="submitButton">{{tr('broadcast')}}</button>
				                                @else

				                                  	<button class="signup-btn submit" type="button" onclick="return alert('Broadcast option is disabled by admin.');">{{tr('broadcast')}}</button>

				                                @endif


										      <!-- <div class="modal-footer">
										        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
										      </div> -->

										      </form>

										      </div>

										      <div class="clearfix"></div>
										    </div>

										  </div>
										
										</div>

									@endif

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


							@if(Setting::get('broadcast_by_user') == 1 || (Auth::check() ? Auth::user()->is_master_user == 1 : 0))


								<li role="presentation">

									<a href="#live_videos_section" class="yt-uix-button  spf-link  yt-uix-sessionlink yt-uix-button-epic-nav-item yt-uix-button-size-default" aria-controls="live_videos_section" role="tab" data-toggle="tab">
										<span class="yt-uix-button-content">{{tr('live_videos')}}</span> 
									</a>

								</li>

							@endif

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
														<a href="{{$trending_videos[0]->url}}"><img src="{{$trending_videos[0]->video_image}}"></a>
														@if($trending_videos[0]->ppv_amount > 0)
					                                        @if(!$trending_videos[0]->ppv_status)
					                                            <div class="video_amount">

					                                            {{tr('pay')}} - {{Setting::get('currency')}}{{$trending_videos[0]->ppv_amount}}

					                                            </div>
					                                        @endif
					                                    @endif
														<div class="video_duration">
					                                        {{$trending_videos[0]->duration}}
					                                    </div>
													</div>
													<div class="video-details recom-details">
														<div class="video-head">
															<a href="{{$trending_videos[0]->url}}"> {{$trending_videos[0]->title}}</a>
														</div>
														
				                                         <span class="video_views">
					                                        <i class="fa fa-eye"></i> {{$trending_videos[0]->watch_count}} {{tr('views')}} <b>.</b> 
					                                        {{$trending_videos[0]->created_at}}
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
															<a href="{{$trending_video->url}}"><img src="{{$trending_video->video_image}}"></a>
															@if($trending_video->ppv_amount > 0)
						                                        @if(!$trending_video->ppv_status)
						                                            <div class="video_amount">

						                                            {{tr('pay')}} - {{Setting::get('currency')}}{{$trending_video->ppv_amount}}

						                                            </div>
						                                        @endif
						                                    @endif
															<div class="video_duration">
						                                        {{$trending_video->duration}}
						                                    </div>
														</div>
														<!--end of slide-image-->

														<div class="video-details recom-details">
															<div class="video-head">
																<a href="{{$trending_video->url}}">{{$trending_video->title}}</a>
															</div>
															
					                                        <span class="video_views">
						                                        <i class="fa fa-eye"></i> {{$trending_video->watch_count}} {{tr('views')}} <b>.</b> 
						                                        {{$trending_video->created_at}}
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
					                                        <div class="cross-title2">
					                                            <h5 class="payment_class"><a href="{{$video->url}}">{{$video->title}}</a></h5>
					                                           
					                                            <span class="video_views">
							                                        <i class="fa fa-eye"></i> {{$video->watch_count}} {{tr('views')}} <b>.</b> 
							                                        {{$video->created_at}}
							                                    </span>
					                                        </div> 
						@if(Auth::check())
						@if($channel->user_id == Auth::user()->id)
						<div class="cross-mark2">

                       		@if($video->amount > 0) 

                        	<div class="modal fade modal-top" id="earning_{{$video->video_tape_id}}" role="dialog">
							    <div class="modal-dialog bg-img modal-sm" style="background-image: url({{asset('images/popup-back.jpg')}});">
							        <!-- Modal content-->
							        <div class="modal-content earning-content">
							        	<div class="modal-header text-center">
								          	<button type="button" class="close" data-dismiss="modal">&times;</button>
								          	<h3 class="modal-title no-margin">{{tr('total_earnings')}}</h3>
								        </div>
								        <div class="modal-body text-center">
								        	<div class="amount-circle">
								        		<h3 class="no-margin">${{$video->amount}}</h3>
								       		</div>
								          	<p>{{tr('total_views')}} - {{$video->watch_count}}</p>
								          	<a href="{{route('user.redeems')}}">
								          		<button class="btn btn-danger top">{{tr('view_redeem')}}</button>
								          	</a>
								        </div>
							        </div>
							    </div>
							</div>

							@endif

					                                            

					                                            

                            <label style="float:none; margin-top: 6px;" class="switch hidden-xs" title="{{$video->ad_status ? tr('disable_ad') : tr('enable_ad')}}">
                                <input id="change_adstatus_id" type="checkbox" @if($video->ad_status) checked @endif onchange="change_adstatus(this.value, {{$video->video_tape_id}})">
                                <div class="slider round"></div>
                            </label>

	                        <div class="btn-group show-on-hover">
					          <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					            Action <span class="caret"></span>
					          </button>

					          <ul class="dropdown-menu dropdown-menu-right" role="menu">

					          	@if(Setting::get('is_payper_view') == 1)

					            <li><a data-toggle="modal" data-target="#pay-perview_{{$video->video_tape_id}}">{{tr('pay_per_view')}}</a></li>

					            @endif

					            @if($video->amount > 0) 
					            <li><a data-toggle="modal" data-target="#earning_{{$video->video_tape_id}}">{{tr('total_earning')}}</a></li>
					            <li class="divider"></li>
					            @endif
					            
					            <li><a title="edit" href="{{route('user.edit.video', $video->video_tape_id)}}">{{tr('edit_video')}}</a></li>
					            <li><a title="delete" onclick="return confirm('Are you sure?');" href="{{route('user.delete.video' , array('id' => $video->video_tape_id))}}"> {{tr('delete_video')}}</a></li>
					            <li class="visible-xs">
	                    			<a href="#">Disable Ad</a>
	                    		</li>
					          </ul>
					        </div>                   
				                           <!-- ========modal pay per view======= -->
                        	<div id="pay-perview_{{$video->video_tape_id}}" class="modal fade" role="dialog">
								<div class="modal-dialog">
									<div class="modal-content">
										<form  action="{{route('user.save.video-payment', $video->video_tape_id)}}" method="POST">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal">&times;</button>
												<h4 class="modal-title text-left">{{tr('pay_per_view')}}</h4>
											</div>
											<div class="modal-body">
											   
											    	<h4 class="black-clr text-left">{{tr('type_of_user')}}</h4>
											    	<div>
														<label class="radio1">
														    <input id="radio1" type="radio" name="type_of_user"  value="{{NORMAL_USER}}" {{($video->type_of_user == NORMAL_USER) ? 'checked' : ''}} required>
															<span class="outer"><span class="inner"></span></span>{{tr('normal_user')}}
														</label>
													</div>
													<div>
													    <label class="radio1">
														    <input id="radio2" type="radio" name="type_of_user" value="{{PAID_USER}}" {{($video->type_of_user == PAID_USER) ? 'checked' : ''}} required>
														    <span class="outer"><span class="inner"></span></span>{{tr('paid_user')}}
														</label>
													</div>
													<div>
													    <label class="radio1">
														    <input id="radio2" type="radio" name="type_of_user" {{($video->type_of_user == BOTH_USERS) ? 'checked' : ''}} required>
														    <span class="outer"><span class="inner"></span></span>{{tr('both_user')}}
														</label>
													</div>

													<div class="clearfix"></div>

													<h4 class="black-clr text-left">{{tr('type_of_subscription')}}</h4>
													<div>
													    <label class="radio1">
														    <input id="radio2" type="radio" name="type_of_subscription" value="{{ONE_TIME_PAYMENT}}" {{($video->type_of_subscription == ONE_TIME_PAYMENT) ? 'checked' : ''}} required>
														    <span class="outer"><span class="inner"></span></span>{{tr('one_time_payment')}}
														</label>
													</div>
													<div>
													    <label class="radio1">
														    <input id="radio2" type="radio" name="type_of_subscription" value="{{RECURRING_PAYMENT}}" {{($video->type_of_subscription == RECURRING_PAYMENT) ? 'checked' : ''}} required>
														    <span class="outer"><span class="inner"></span></span>{{tr('recurring_payment')}}
														</label>
													</div>

													<div class="clearfix"></div>

													<h4 class="black-clr text-left">{{tr('amount')}}</h4>
													<div>
								                       <input type="text" required value="{{$video->ppv_amount}}" name="ppv_amount" class="form-control" id="amount" placeholder="{{tr('amount')}}" pattern="[0-9]{1,}">
								                  <!-- /input-group -->
								                
										            </div>

														
						 						
												<div class="clearfix"></div>
											</div>

											 <div class="modal-footer">
										      	<div class="pull-left">
										      		@if($video->ppv_amount > 0)
										       			<a class="btn btn-danger" href="{{route('admin.remove_pay_per_view', $video->video_tape_id)}}">{{tr('remove_pay_per_view')}}</a>
										       		@endif
										       	</div>
										        <div class="pull-right">
											        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
											        <button type="submit" class="btn btn-primary">Submit</button>
											    </div>
											    <div class="clearfix"></div>
										      </div>
									      </form>
									</div>
								</div>
							</div>	
					<!-- ========modal ends======= -->
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

				@if(Setting::get('broadcast_by_user') == 1 || (Auth::check() ? Auth::user()->is_master_user == 1 : 0))


				<li role="tabpanel" class="tab-pane" id="live_videos_section">

					<div class="slide-area recom-area abt-sec">
						<div class="abt-sec-head">
							
							 <div class="new-history">
					                <div class="content-head">
					                    <div><h4 style="color: #000;">{{tr('live_videos')}}&nbsp;&nbsp;
					                   
					                    </h4></div>              
					                </div><!--end of content-head-->

					                @if(count($live_videos) > 0)

					                    <ul class="history-list">

					                        @foreach($live_videos as $i => $live_video)

					                         <?php 


                                        $userId = Auth::check() ? Auth::user()->id : '';

                                        $url = ($live_video->amount > 0) ? route('user.payment_url', array('id'=>$live_video->id, 'user_id'=>$userId)): route('user.live_video.start_broadcasting' , array('id'=>$live_video->unique_id,'c_id'=>$live_video->channel_id));


                                        ?>

                                        <div class="modal fade cus-mod" id="paypal_{{$live_video->id}}" role="dialog">
							                <div class="modal-dialog">
							                
							                  <!-- Modal content-->
							                  <div class="modal-content">

							                        <div class="modal-header">
							                            <button type="button" class="close" data-dismiss="modal">&times;</button>
							                            <h4 class="modal-title text-center text-uppercase" style="color: #000 !important">{{tr('payment_options')}}</h4>
							                        </div>


							                        <div class="modal-body">
							                            <!-- <p>Please Pay to see the full video</p>  -->
							                                <div class="col-lg-6">
							                                  <!-- small box -->
							                                  <div class="small-box bg-green">
							                                    <div class="inner">
							                                      <h3>{{ Setting::get('currency')}} {{$live_video->amount}}</h3>
							                                      <div class="clearfix"></div>
							                                      <p style="float: none;" class="text-left">{{tr('paypal_payment')}}</p>
							                                    </div>
							                                    <div class="icon">
							                                      <i class="fa fa-money"></i>
							                                    </div>
							                                     <div class="clearfix"></div>
							                                    <a href="{{route('user.live_video_paypal', array('id'=>$live_video->id, 'user_id'=>$userId))}}" class="small-box-footer">{{tr('to_view_video')}} <i class="fa fa-arrow-circle-right"></i></a>
							                                  </div>
							                                </div>
							                           
							                                <div class="col-lg-6">
							                                  <!-- small box -->
							                                  <div class="small-box bg-aqua">
							                                    <div class="inner">
							                                      <h3>{{ Setting::get('currency')}} {{$live_video->amount}}</h3>
							                                      <div class="clearfix"></div>
							                                      <p style="float: none;" class="text-left">{{tr('stripe_payment')}}</p>
							                                    </div>
							                                    <div class="icon">
							                                      <i class="fa fa-money"></i>
							                                    </div>
							                                     <div class="clearfix"></div>
							                                    <a onclick="return confirm('Are you sure want pay through card?')" href="{{route('user.stripe_payment_video', array('id'=>$live_video->id, 'user_id'=>$userId))}}" class="small-box-footer">{{tr('to_view_video')}} <i class="fa fa-arrow-circle-right"></i></a>
							                                  </div>
							                                </div>
							                            
							                            
							                            <div class="clearfix"></div>
							                            
							                        </div>

							                        
							                  </div>
							                  
							                </div>
							            
							            </div>  


					                        <li class="sub-list row">
					                            <div class="main-history">
					                                 <div class="history-image">
					                                 	@if($live_video->amount > 0) 

					                                 		@if (isPaidAmount($live_video->id))
					                                 		
																<a href="{{$url}}">					                                 	
					                                 		@else
				                                        		<a data-toggle="modal" data-target="#paypal_{{$live_video->id}}" style="cursor: pointer;">
				                                        	@endif
				                                        @else
				                                    
				                                        <a href="{{$url}}">

				                                        @endif

				                                            <img src="{{$live_video->snapshot}}" /> 

				                                        </a>

					                                    <div class="video_duration text-uppercase">
					                                         @if($live_video->amount > 0) 

				                                                {{tr('paid')}} - ${{$live_video->amount}} 

				                                            @else {{tr('free')}} @endif
					                                    </div>                        
					                                </div><!--history-image-->

					                                <div class="history-title">
					                                    <div class="history-head row">
					                                        <div class="cross-title">
					                                            <h5 class="payment_class">

					                                            	@if($live_video->amount > 0) 
							                                        	<a data-toggle="modal" data-target="#paypal_{{$live_video->id}}" style="cursor: pointer;">
							                                        @else
							                                    
							                                        <a href="{{$url}}">

							                                        @endif

							                                            {{$live_video->title}}

							                                        </a>


					                                            </h5>
					                                           
					                                            <span class="video_views">
							                                        <i class="fa fa-eye"></i> {{$live_video->viewers_cnt}} {{tr('views')}} <b>.</b> 
							                                        {{$live_video->created_at->diffForHumans()}}
							                                    </span>
					                                        </div> 
					                                        <!--end of cross-mark-->                       
					                                    </div> <!--end of history-head--> 

					                                    <div class="description">
					                                        <p>{{$live_video->description}}</p>
					                                    </div><!--end of description--> 
					                                </div><!--end of history-title--> 
					                                
					                            </div><!--end of main-history-->
					                        </li>    

					                        @endforeach
					                       
					                    </ul>

					                @else

					                   <p style="color: #000">{{tr('no_video_found')}}</p>

					                @endif

					                @if(count($live_videos) > 0)

					                    @if($live_videos)
					                    <div class="row">
					                        <div class="col-md-12">
					                            <div align="center" id="paglink"><?php echo $live_videos->links(); ?></div>
					                        </div>
					                    </div>
					                    @endif
					                @endif
					                
					            </div>

						</div>
					</div>


				</li>

				@endif


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
					                                            <h5 class="payment_class"><a href="{{$video->url}}">{{$video->title}} ($ {{$video->amount}})</a></h5>
					                                            

					                                            <span class="video_views">
							                                        <i class="fa fa-eye"></i> {{$video->watch_count}} {{tr('views')}} <b>.</b> 
							                                        {{$video->created_at}}
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

			<div class="sidebar-back"></div> 
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