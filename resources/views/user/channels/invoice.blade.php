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
				<h3 class="no-margin text-center mb-20 mt-0">{{tr('channel_invoice')}}</h3>	
				<div class="row" > 
					<div class="col-xs-12 col-sm-12 col-md-10 col-lg-8 col-md-offset-1 col-lg-offset-2 " >
						<div class="text-center invoice1 white-bg">
						 	<div class="row">
						 		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 invoice-img" style="background-image: url({{asset('images/invoice-bg.jpg')}});">
							 		<div class="invoice-overlay">
							 			<div class="invoice-desc">
										 	<!-- <h3 class="no-margin black-clr">{{$channel_details->name}}</h3> -->
										 	<!-- <p class="invoice-desc"><?= $channel_details->description ?></p> -->
									 	</div>
									</div>
								</div>
								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 white-bg">
									<div class="spacing1">
									 	<table  class="table text-right top-space table-sripped">
									 		<tbody>

									 			<tr class="active">
											        <td>{{tr('channel_name')}}</td>
											        <td> <span id="remaining_amount">{{$channel_details->name}}</span></td>
											    </tr> 

											    <tr class="danger">
												    <td>{{tr('amount')}}</td>
												    <td> {{Setting::get('currency')}} {{$channel_details->subscription_amount}}</td>
											    </tr>

											    <tr id="coupon_value_tr" style="display: none">
											        <td>{{tr('coupon_value')}}</td>
											        <td id="coupon_value"></td>
											    </tr>

											    <tr id="coupon_amount_tr" style="display: none">
											        <td>{{tr('coupon_amount')}}</td>
											        <td> {{Setting::get('currency')}}<span id="coupon_amount_val"></span></td>
											    </tr>
											    
										    </tbody>
										</table>

										@if($channel_details->subscription_amount > 0)

										<form method="post" action="{{route('user.channel.payment')}}">

										
											<!-- coupon code -->

										
											<h4 class="no-margin black-clr top mb-15">{{tr('payment_options')}}</h4>

									    	<input type="hidden" name="channel_id" value="{{$channel_details->id}}">

									    	<input type="hidden" name="u_id" value="{{Auth::user()->id ?? 0}}">
									    	<div>
												<label class="radio1">
												    <input id="radio1" type="radio" name="payment_type" checked value="1">
													<span class="outer"><span class="inner"></span></span>{{tr('paypal')}}
												</label>
											</div>
											<div class="clear-fix"></div>

											@if(Setting::get('payment_type') == 'stripe')
											<div>
											    <label class="radio1">
												    <input id="radio2" type="radio" name="payment_type" value="2">
												    <span class="outer"><span class="inner"></span></span>{{tr('card_payment')}}
												</label>
											</div>
											@endif

											<div class="clear-fix"></div>
											<div class="text-right top">
												<button id="my_button" class="btn btn-danger">
													<i class="fa fa-credit-card"></i> &nbsp; {{tr('pay_now')}}
												</button>
											</div>

										</form>
				 						
				 						@else

											<div class="clear-fix"></div>
											<div class="text-right top">
												
												<i class="fa fa-credit-card"></i> &nbsp; {{tr('free_subscribe_paynow')}}
												</a>
											</div>
										@endif


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


@section('scripts')
<script>
    $('#my_button').on('click', function(){

    	setTimeout(function(){

    		$('#my_button').attr("disabled", true);

    	}, 1000);
        // alert('paypal action');
        
    });
</script>
@endsection
