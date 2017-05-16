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
				<form action="">
					<div class="lohp-shelf-content row">
						<div class="lohp-large-shelf-container col-md-9 col-sm-12 col-xs-12">
							<div class="slide-box recom-box big-box-slide vdo-pg-bx">
								<div class="slide-image recom-image hbb">
									<a href="#">
									<img class="img-responsive vdo-img" style="background-image: url({{asset('images/ian-somer.jpg')}})">
									</a
								</div>
								</div>
						</div>
						
						<div class="create-video-input row">
								<div class="row">
									<div class="col-lg-6">
										<div class="input-group">
											<input type="text" class="form-control" placeholder="Select Channel" aria-describedby="basic-addon1">
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
													<div class="col-md-12">
							<div class="btn-create-w-vd">
					<button id="cancel-create" name="cancel" class="btn create-btn-btn">cancel</button>
					<button id="done-create" name="Done" class="btn btn-success create-btn-btn">Done</button>

					</div>
						</div>

							</div>
						<ul class="lohp-medium-shelves-container col-md-3 col-xs-12 col-sm-12">
							<li class="col-md-12 col-sm-6 col-xs-6 side-box-vd active">

								<div class="slide-box recom-box big-box-slide sq-bx">
									<div class="slide-image recom-image hbb">
										<a href=""><img class="img-responsive vdo-img" style="background-image: url({{asset('images/ian-somer.jpg')}})"></a>
									</div>
									
								</div>
							</li>

							<li class="col-md-12 col-sm-6 col-xs-6 side-box-vd ">

								<div class="slide-box recom-box big-box-slide sq-bx">
									<div class="slide-image recom-image hbb">
										<a href="">
										<img class="img-responsive vdo-img" style="background-image: url({{asset('images/ian-somer.jpg')}})">
										</a>
									</div>
									
								</div>
							</li>

							<li class="col-md-12 col-sm-6 col-xs-6 side-box-vd ">

								<div class="slide-box recom-box big-box-slide sq-bx">
									<div class="slide-image recom-image hbb">
										<a href="">
										<img class="img-responsive vdo-img" style="background-image: url({{asset('images/ian-somer.jpg')}})">
										</a>
									</div>
									
								</div>
							</li>
							<div class="clearfix"></div>
							<li class="btn-createvideo col-md-12">
								<input id="" class="btn btn create-btn-btn" type="file">
					</li>
						</ul>
						
					</div>
				</form>
				</div>
			</div>


		</div>

	</div>

</div>

@endsection

@section( 'scripts' )

<!-- Add Js files and inline js here -->

@endsection