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
										 	<h3 class="no-margin black-clr">Plan tilte</h3>
										 	<p class="invoice-desc"> When viewing on anything larger than 768px wide, there is no difference:</p>
									 	</div>
									</div>
								</div>
								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 white-bg">
									<div class="spacing1">
									 	<table  class="table text-right top-space table-sripped">
									 		<tbody>
											    <tr class="danger">
												    <td>Amount</td>
												    <td> $199.00</td>
											    </tr>
											    <tr>
											        <td>Tax</td>
											        <td> $9.99</td>
											    </tr>
											    <tr class="danger">
											        <td>Total</td>
											        <td> 208.99</td>
											    </tr> 
										    </tbody>
										</table>
										<h3 class="no-margin black-clr top">Payment Options</h3>
									    <form >
									    	<div>
												<label class="radio1">
												    <input id="radio1" type="radio" name="radios" checked>
													<span class="outer"><span class="inner"></span></span>Paypal
												</label>
											</div>
											<div class="clear-fix"></div>
											<div>
											    <label class="radio1">
												    <input id="radio2" type="radio" name="radios">
												    <span class="outer"><span class="inner"></span></span>Card Payment
												</label>
											</div>
											<div class="clear-fix"></div>
											<div class="text-right top">
												<button class="btn btn-danger">
													<i class="fa fa-credit-card"></i> &nbsp; Pay Now
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