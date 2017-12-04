@extends( 'layouts.user' )

@section( 'styles' )

<link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/custom-style.css')}}"> 

<style>
	
	.list-style li {
		margin: 10px auto;
		text-align: center;
	}
</style>

@endsection

@section('content')

<div class="y-content">

	<div class="row content-row">

		@include('layouts.user.nav')

		<div class="page-inner col-sm-9 col-md-10">

			@include('notification.notify')

			<div class="invoice">

				<div class="row"> 

					<div class="col-xs-12 col-sm-12 col-md-10 col-lg-8 col-md-offset-1 col-lg-offset-2">

						<div class="text-center">

							<h4>{{tr('payment_failed')}}</h4>

							<p>
								The payment may be caused because of the following reasons : 
							</p>

							@if($paypal_error)

								<span style="color: red">{{$paypal_error}}</span>

							@else

							<ul class="list-style">
								<li><span>* Insufficient Funds</span></li>
								<li><span>* Payment Configuration issues</span></li>
								<li><span>* Unexcepted errors ..etc</span></li>
							</ul>

							@endif

							<div class="clearfix"></div>

							<a href="{{url('/')}}" class="btn btn-primary">Go Home</a>

						</div>
						
					</div>

				</div>
			</div>

		</div>

	</div>

</div>

@endsection