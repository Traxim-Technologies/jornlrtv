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

			<div class="invoice">
				<div class="row" > 
					<div class="col-xs-12 col-sm-12 col-md-10 col-lg-8 col-md-offset-1 col-lg-offset-2 " >
						<div class="text-center invoice1">
						 	<div class="row">
						 		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 invoice-img" style="background-image: url({{asset('images/invoice-bg.jpg')}});">
							 		<div class="invoice-overlay">
							 			<div>
										 	<h3 class="no-margin black-clr">{{$video->title}}</h3>
										 	<p class="invoice-desc"> {{$video->description}}</p>
									 	</div>
									</div>
								</div>
								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 white-bg">
									<div class="spacing1">
									 	<table  class="table text-right top-space table-sripped">
									 		<tbody>
									 			<tr  class="danger">
												    <td>{{tr('type_of_subscription')}}</td>
												    <td>
												    	@if($video->type_of_subscription == 1)

												    		{{tr('one_time_payment')}}

												    	@else

												    		{{tr('recurring_payment')}}

												    	@endif

												    </td>
											    </tr>

											    <tr>
												    <td>{{tr('type_of_user')}}</td>
												    <td>
												    	@if($video->type_of_user == 1)	

												    		{{tr('only_for_normal_users')}}

												    	@elseif($video->type_of_user == 2) 

												    		{{tr('only_for_paid_users')}}

												    	@else

												    		{{tr('all_users')}}

												    	@endif
												    </td>
											    </tr>

											    <tr  class="danger">
												    <td>Amount</td>
												    <td> {{Setting::get('currency')}} {{$video->ppv_amount}}</td>
											    </tr>
											    <!-- <tr>
											        <td>Tax</td>
											        <td> $9.99</td>
											    </tr> -->
											    <tr>
											        <td>{{tr('total')}}</td>
											        <td> {{Setting::get('currency')}} {{$video->ppv_amount}}</td>
											    </tr> 
										    </tbody>
										</table>
										<h3 class="no-margin black-clr top">{{tr('payment_options')}}</h3>
									    <form action="{{route('user.payment-type', $video->id)}}" method="post">
									    	<div>
												<label class="radio1">
												    <input id="radio1" type="radio" name="payment_type" checked value="1">
													<span class="outer"><span class="inner"></span></span>{{tr('paypal')}}
												</label>
											</div>
											<div class="clear-fix"></div>
											<div>
											    <label class="radio1">
												    <input id="radio2" type="radio" name="payment_type" value="2">
												    <span class="outer"><span class="inner"></span></span>{{tr('card_payment')}}
												</label>
											</div>
											<div class="clear-fix"></div>
											<div class="text-right top">
												<button class="btn btn-danger">
													<i class="fa fa-credit-card"></i> &nbsp; {{tr('pay_now')}}
												</button>
											</div>
				 						</form>
			 						</div>
								</div>
							 </div>
						</div>
					</div>
				</div>
			</div>
			<!-- =========INVOICE TEMPLATE ENDS========= -->
			<div class="sidebar-back"></div>  
		</div>

	</div>

</div>
@endsection