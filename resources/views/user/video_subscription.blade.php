@extends( 'layouts.user' )


@section( 'styles' )

<link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/custom-style.css')}}"> 

@endsection

@section('content')

<div class="y-content">

	<div class="row content-row">

		@include('layouts.user.nav')

		<div class="page-inner col-sm-9 col-md-10">

			@include('notification.notify')

			<div class="col-xs-12 col-sm-10 col-md-8 col-lg-6 col-sm-offset-1 col-md-offset-2 col-lg-offset-3">
				<div class="payment-success text-center">
					<img src="{{asset('images/payment-success.png')}}">
					<h1 class="red-clr">Thank You!</h1>
					<h3>Your payment is received Successfully !!!</h3>
					<p>Now you can watch video</p>
					<a href="#" class="top">
						<button class="btn btn-danger"><i class="fa fa-video-camera"></i> &nbsp;Watch Video</button>
					</a>
				</div>
			</div>

		</div>

	</div>
</div>

@endsection