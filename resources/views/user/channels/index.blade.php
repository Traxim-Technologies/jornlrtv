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
								<div class="hd-banner-image "></div>
							</div>
							<div id="header-links">
								<ul class="about-secondary-links">
									<li class="channel-links-item">
										<a href="http://www.facebook.com/adele" rel="me nofollow" target="_blank" title="Facebook" class="about-channel-link yt-uix-redirect-link about-channel-link-with-icon">
									        <img src="//s2.googleusercontent.com/s2/favicons?domain_url=http%3A%2F%2Fwww.facebook.com%2Fadele&amp;feature=youtube_channel" class="about-channel-link-favicon" alt="" width="16" height="16">
									    </a>
									</li>

									<li class="channel-links-item">
										<a href="http://twitter.com/adele" rel="me nofollow" target="_blank" title="Twitter" class="about-channel-link yt-uix-redirect-link about-channel-link-with-icon">
									        <img src="//s2.googleusercontent.com/s2/favicons?domain_url=http%3A%2F%2Ftwitter.com%2Fadele&amp;feature=youtube_channel" class="about-channel-link-favicon" alt="" width="16" height="16">
									    </a>
									</li>

									

								</ul>

							</div>


							<a class="channel-header-profile-image spf-link" href="">
						      <img class="channel-header-profile-image" src="{{$channel->picture}}" title="{{$channel->name}}" alt="{{$channel->name}}">
						    </a>
						</div>

					</div>

					<div>
						<div class="pull-left">
							<h1 class="st_channel_heading">{{$channel->name}}</h1>
						</div>
						<div class="pull-right upload_a">
							@if(Auth::check())
								@if($channel->user_id == Auth::user()->id)
									<a class="st_video_upload_btn" href="{{route('user.video_upload', ['id'=>$channel->id])}}"><i class="fa fa-plus-circle"></i> {{tr('upload_video')}}</a>
									<a class="st_video_upload_btn" href="{{route('user.channel_edit', $channel->id)}}"><i class="fa fa-pencil"></i> {{tr('edit_channel')}}</a>
									<a class="st_video_upload_btn" href="{{route('user.delete.channel', ['id'=>$channel->id])}}"><i class="fa fa-trash"></i> {{tr('delete_channel')}}</a>
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
							<li role="presentation">
								<a href="#videos" class="yt-uix-button  spf-link  yt-uix-sessionlink yt-uix-button-epic-nav-item yt-uix-button-size-default" aria-controls="videos" role="tab" data-toggle="tab"><span class="yt-uix-button-content">{{tr('videos')}}</span> </a>
							</li>
							<li role="presentation">
								<a href="#about" class="yt-uix-button  spf-link  yt-uix-sessionlink yt-uix-button-epic-nav-item yt-uix-button-size-default" aria-controls="about" role="tab" data-toggle="tab"><span class="yt-uix-button-content">{{tr('about_video')}}</span> </a>
							</li>
							@if(Auth::check())
								@if($channel->user_id == Auth::user()->id)
									<li role="presentation">
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
														<a href="{{route('user.single', $trending_videos[0]->admin_video_id)}}"><img src="{{$trending_videos[0]->default_image}}"></a>
													</div>
													<div class="video-details recom-details">
														<div class="video-head">
															<a href="{{route('user.single', $trending_videos[0]->admin_video_id)}}"> {{$trending_videos[0]->title}}</a>
														</div>
														<div class="sugg-description">
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
															<a href="{{route('user.single', $trending_video->admin_video_id)}}"><img src="{{$trending_video->default_image}}"></a>
														</div>
														<!--end of slide-image-->

														<div class="video-details recom-details">
															<div class="video-head">
																<a href="{{route('user.single', $trending_video->admin_video_id)}}">{{$trending_video->title}}</a>
															</div>
															<div class="sugg-description">
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
					<div class="slide-area recom-area">

						<div class="recommend-list row">

							@if(count($videos) == 0 || empty($videos->items()))

						  		<p style="color: #000">{{tr('no_video_found')}}</p>

						  	@endif


							@if(count($videos) > 0 )

								@foreach($videos as $video)
								<div class="slide-box recom-box">
									<div class="slide-image recom-image">
										<a href="{{route('user.single', $video->admin_video_id)}}"><img src="{{$video->default_image}}"></a>
									</div>
									<!--end of slide-image-->

									<div class="video-details recom-details">
										<div class="video-head">
											<a href="{{route('user.single', $video->admin_video_id)}}">{{$video->title}}</a>
										</div>
										<div class="sugg-description">
											<p style="color: #000">{{tr('duration')}} : {{$video->duration}}<span class="content-item-time-created lohp-video-metadata-item"><i class="fa fa-clock-o" aria-hidden="true"></i> {{($video->created_at) ? $video->created_at->diffForHumans() : 0}}</span>
											</p>
										</div>

		                                <span class="stars">
                                           <a href="#"><i @if($video->ratings >= 1) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                           <a href="#"><i @if($video->ratings >= 2) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                           <a href="#"><i @if($video->ratings >= 3) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                           <a href="#"><i @if($video->ratings >= 4) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                           <a href="#"><i @if($video->ratings >= 5) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                        </span>  

									</div>
									<!--end of video-details-->
								</div>
								@endforeach
							@endif

						</div>
						<!--end of recommend-list-->
						<div class="row">
							<div class="col-md-12">
								<div id="paglink" align="center">
									@if(count($videos) > 0 )
									
										<?php echo $videos->links();?>

									@endif
								</div>
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
					                    <div><h4>{{tr('payment_videos')}}</h4></div>              
					                </div><!--end of content-head-->

					                @if(count($payment_videos) > 0)

					                    <ul class="history-list">

					                        @foreach($payment_videos as $i => $video)


					                        <li class="sub-list row">
					                            <div class="main-history">
					                                 <div class="history-image">
					                                    <a href="{{route('user.single' , $video->admin_video_id)}}"><img src="{{$video->default_image}}"></a>                        
					                                </div><!--history-image-->

					                                <div class="history-title">
					                                    <div class="history-head row">
					                                        <div class="cross-title">
					                                            <h5 class="payment_class"><a href="{{route('user.single' , $video->admin_video_id)}}">{{$video->title}} ($ {{$video->amount}})</a></h5>
					                                            <p style="color: #000" class="duration">{{tr('duration')}}: {{$video->duration}} (<span class="content-item-time-created lohp-video-metadata-item"><i class="fa fa-clock-o" aria-hidden="true"></i> {{($video->created_at) ? $video->created_at->diffForHumans() : 0}}</span> ) </p>
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
					                       
					                    </ul>

					                @else
					                    <p style="color: #000">{{tr('no_videos_found')}}</p>
					                @endif

					                @if(count($payment_videos) > 0)

					                    @if($payment_videos)
					                    <div class="row">
					                        <div class="col-md-12">
					                            <div align="center" id="paglink"><?php echo $payment_videos->links(); ?></div>
					                        </div>
					                    </div>
					                    @endif
					                @endif
					                
					            </div>

						</div>
					</div>

				</li>
			</ul>

		</div>

	</div>

</div>


@endsection

@section( 'scripts' )

<!-- Add Js files and inline js here -->

@endsection