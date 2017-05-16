@extends( 'layouts.user' )

@section( 'styles' )

<!-- Add css file and inline css here -->
<link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/custom-style.css')}}"> @endsection @section('content')

<div class="y-content">
	<div class="row content-row">

		@include('layouts.user.nav')

		<div class="page-inner col-sm-9 col-md-10">

			@include('notification.notify')

			<div class="slide-area recom-area abt-sec des-crt">
				<div class="abt-sec-head description-create">
					<div class="lohp-shelf-content row">
						<div class="lohp-large-shelf-container col-md-8">
							<div class="slide-box recom-box big-box-slide">
								<div class="slide-image recom-image hbb">
									<a href="http://demo.streamhash.com/video/55"><img src="http://demo.streamhash.com/uploads/41ae6130255ead312bbe946be259b7a6b02b54e5.jpg"></a>
								</div>
								<!--end of slide-image-->

								<!--
								<div class="video-details recom-details">
									<div class="video-head">
										<a href="http://demo.streamhash.com/video/55"> Blippbuilder Tutorial AR Creator for Education</a>
									</div>
									<div class="sugg-description">
										<p>Duration: 00:14:00<span class="content-item-time-created lohp-video-metadata-item" title="11 months ago"><i class="fa fa-clock-o" aria-hidden="true"></i> 11 months ago</span>
										</p>
									</div>
									end of sugg-description

									<span class="stars">
	                                    <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
	                                   <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
	                                   <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
	                                   <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
	                                   <a href="#"><i class="fa fa-star" aria-hidden="true"></i></a>
	                                </span>
								





								</div>
-->

								<!--end of video-details-->
							</div>
							<div class="create-video-input row">
								<div class="row">
									<div class="col-lg-6">
										<div class="input-group">
											<input type="text" class="form-control" placeholder="Select Channel" aria-describedby="basic-addon1">
											<div class="input-group-btn">
												<button type="button" class="btn btn-default dropdown-toggle crt-vdo-btn-gp" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button>
												<ul class="dropdown-menu dropdown-menu-right">
													<li><a href="#">Action</a>
													</li>
													<li><a href="#">Another action</a>
													</li>
													<li><a href="#">Something else here</a>
													</li>
													
												</ul>
											</div>
											<!-- /btn-group -->
										</div>
										<!-- /input-group -->
									</div>
									<!-- /.col-lg-6 -->
									<div class="col-lg-6">
										<div class="input-group">
											<input type="text" class="form-control" placeholder="Title" aria-describedby="basic-addon1">
											
											<!-- /btn-group -->
										</div>
										<!-- /input-group -->
									</div>
									<!-- /.col-lg-6 -->
									
									<div class="col-md-12">
									<div class="input-group crt-vdo-des-grp">
									<textarea class="form-control description" id="description" name="Description"></textarea>
									</div>
								</div>
								</div>
								<!-- /.row -->

								
							</div>
						</div>

						<div class="lohp-medium-shelves-container col-md-4">
							<div class="col-md-12">

								<div class="slide-box recom-box big-box-slide sq-bx">
									<div class="slide-image recom-image hbb">
										<a href="http://demo.streamhash.com/video/55"><img src="http://demo.streamhash.com/uploads/41ae6130255ead312bbe946be259b7a6b02b54e5.jpg"></a>
									</div>
									<!--end of slide-image-->

									<!--
								<div class="video-details recom-details">
									<div class="video-head">
										<a href="http://demo.streamhash.com/video/55"> Blippbuilder Tutorial AR Creator for Education</a>
									</div>
									<div class="sugg-description">
										<p>Duration: 00:14:00<span class="content-item-time-created lohp-video-metadata-item" title="11 months ago"><i class="fa fa-clock-o" aria-hidden="true"></i> 11 months ago</span>
										</p>
									</div>
									end of sugg-description

									<span class="stars">
	                                    <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
	                                   <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
	                                   <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
	                                   <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
	                                   <a href="#"><i class="fa fa-star" aria-hidden="true"></i></a>
	                                </span>
								





								</div>
-->

									<!--end of video-details-->
								</div>
							</div>

							<div class="col-md-12">

								<div class="slide-box recom-box big-box-slide sq-bx">
									<div class="slide-image recom-image hbb">
										<a href="http://demo.streamhash.com/video/55"><img src="http://demo.streamhash.com/uploads/41ae6130255ead312bbe946be259b7a6b02b54e5.jpg"></a>
									</div>
									<!--end of slide-image-->

									<!--
								<div class="video-details recom-details">
									<div class="video-head">
										<a href="http://demo.streamhash.com/video/55"> Blippbuilder Tutorial AR Creator for Education</a>
									</div>
									<div class="sugg-description">
										<p>Duration: 00:14:00<span class="content-item-time-created lohp-video-metadata-item" title="11 months ago"><i class="fa fa-clock-o" aria-hidden="true"></i> 11 months ago</span>
										</p>
									</div>
									end of sugg-description

									<span class="stars">
	                                    <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
	                                   <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
	                                   <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
	                                   <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
	                                   <a href="#"><i class="fa fa-star" aria-hidden="true"></i></a>
	                                </span>
								





								</div>
-->
									<!--end of video-details-->
								</div>
							</div>

							<div class="col-md-12">

								<div class="slide-box recom-box big-box-slide sq-bx">
									<div class="slide-image recom-image hbb">
										<a href="http://demo.streamhash.com/video/55"><img src="http://demo.streamhash.com/uploads/41ae6130255ead312bbe946be259b7a6b02b54e5.jpg"></a>
									</div>
									<!--end of slide-image-->

									<!--
								<div class="video-details recom-details">
									<div class="video-head">
										<a href="http://demo.streamhash.com/video/55"> Blippbuilder Tutorial AR Creator for Education</a>
									</div>
									<div class="sugg-description">
										<p>Duration: 00:14:00<span class="content-item-time-created lohp-video-metadata-item" title="11 months ago"><i class="fa fa-clock-o" aria-hidden="true"></i> 11 months ago</span>
										</p>
									</div>
									end of sugg-description

									<span class="stars">
	                                    <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
	                                   <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
	                                   <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
	                                   <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
	                                   <a href="#"><i class="fa fa-star" aria-hidden="true"></i></a>
	                                </span>
								





								</div>
-->
									<!--end of video-details-->
								</div>
							</div>
							
							<div class="btn-createvideo col-md-12">
					<button id="done-create" name="Done" class="btn btn create-btn-btn">lorem ipsum</button>
					</div>
						</div>
						
						<div class="col-md-12">
							<div class="btn-create-w-vd">
					<button id="cancel-create" name="cancel" class="btn create-btn-btn">cancel</button>
					<button id="done-create" name="Done" class="btn btn-success create-btn-btn">Done</button>

					</div>
						</div>
					</div>

				</div>
			</div>


		</div>

	</div>

</div>

@endsection

@section( 'scripts' )

<!-- Add Js files and inline js here -->

@endsection