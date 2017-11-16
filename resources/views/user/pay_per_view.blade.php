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

			
			<!-- <div class="row"> -->
				<div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3 text-center">
					<div class="row">
						<h3 class="no-margin payment-section">Select Payment</h3>
						<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
							<div class="payment-card">
								<h4>Subscription Plan</h4>
								<img src="{{asset('images/subscriptions-new.png')}}">
								<p>Click here to see subscription plan</p>
								<div>
									<button class="btn btn-danger">Click Here</button>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
							<div class="payment-card">
								<h4>Pay Per Video</h4>
								<img src="{{asset('images/PayPer_Icon.png')}}">
								<p>One time Payment</p>
								<p>Amount - $100.00 </p>
								<div>
									<button class="btn btn-danger">Click Here</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			<!-- </div> -->
			  
		</div>

	</div>

</div>
@endsection