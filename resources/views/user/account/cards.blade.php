@extends('layouts.user')

@section('styles')


<link rel="stylesheet" type="text/css" href="{{asset('assets/css/card.css')}}" />

@endsection

@section('content')

<div class="y-content">

    <div class="row y-content-row">

        @include('layouts.user.nav')

		<div class="page-inner col-sm-9 col-md-10 profile-edit">
				
				<div class="profile-content profile-details">	

					<div class="row no-margin">

						<div class="col-lg-12">

                    		<h4>{{tr('cards')}}</h4>

		            		<div class="card-wrapper row">

		            			<div class="jp-card-container">

			            			<div class="jp-card jp-card-visa jp-card-identified col-lg-4 col-md-offset-1">

				            			<div class="jp-card-front">

					            			<div class="jp-card-logo jp-card-visa">

					            				Visa

					            			</div>

				            				<div class="jp-card-lower">

				            					<div class="jp-card-shiny"></div>

					            				<div class="jp-card-cvc jp-card-display">•••</div>

					            				<div class="jp-card-number jp-card-display jp-card-invalid">XXXX XXXX XXXX XXXX</div>

					            				<div class="jp-card-name jp-card-display">{{Auth::user()->name}}</div>

					            				<div class="jp-card-expiry jp-card-display" data-before="month/year" data-after="validthru"><span id="jp-month">••</span>/<span id="jp-year">••</span></div>

											</div>

										</div>


									</div>

									<div class="jp-card jp-card-visa jp-card-identified jp-card-flipped col-lg-4 col-md-offset-1">

										<div class="jp-card-back">

											<div class="jp-card-bar"></div>

											<div class="jp-card-cvc jp-card-display">•••</div>

											<div class="jp-card-shiny"></div>

										</div>

									</div>

								</div>

							</div>

							<br>


					        <form action="{{ route('user.card.add_card') }}" method="POST" id="payment-form" class="form-horizontal card">

					        <div class="row" id="card-payment">
					            <div>

					                <input id="id" name="id" type="hidden" required>

					                <div class="input-group-signup col-lg-3">
					                    <input id="name" name="number" type="text" placeholder="{{tr('card_number')}}" class="form-control" required data-stripe="number" 
					                    onkeyup="card_number_onkey(this.value)" maxlength="16">
					                </div>
					                <div class="input-group-signup col-lg-3">
					                    <input id="email" name="cvc" type="text" placeholder="{{tr('cvv')}}" class="form-control input-md" data-stripe="cvc" onkeyup="$('.jp-card-cvc').html(this.value)">
					                </div>

					                <div class="input-group-signup col-lg-2">
					                    <input id="nationality" name="month" type="text" placeholder="{{tr('mm')}}" class="form-control" autocomplete="cc-exp" data-stripe="exp-month" onkeyup="$('#jp-month').html(this.value)" maxlength="2" pattern="[0-9]{2,}">
					                </div>
					                <div class="input-group-signup col-lg-2">
					                    <input id="language" name="year" data-stripe="exp-year"
					                    autocomplete="cc-exp" type="text" placeholder="{{tr('yy')}}" class="form-control" onkeyup="$('#jp-year').html(this.value)" maxlength="2" pattern="[0-9]{2,}">
					                </div>

					                <div class="input-group-signup col-lg-2">

					                  <button class="btn btn-sm btn-success" type="submit">{{tr('submit')}}</button>

					                </div>

					                <div class="clearfix"></div>

					                <div class="payment-errors text-danger col-lg-12"></div>

					                <br>

					            </div>
					        </div>

					        </form>

					        <hr>

					        @if(count($cards) > 0)

					            @foreach($cards as $card)

					            <div class="row">
						              <div class="col-lg-1">
						                <img src="/images/visa-card.png" alt="">
						              </div>

						              <div class="col-lg-3">PERSONAL*********{{$card->last_four}}</div>

						              @if(!$card->is_default)
						              <div class="col-lg-2">

						                <form action="{{ route('user.card.default') }}" method="POST" class="pull-left">
						                      <input type="hidden" name="_method" value="PATCH">
						                      <input type="hidden" name="card_id" value="{{ $card->id }}">

						                      <button class="btn btn-primary btn-sm" type="submit" class="bk-nw-ct text-white"><i class="fa fa-check"></i> {{tr('set_as_default')}}</button>

						                </form>
					                   </div>

					                   <div class="col-lg-2"> 
						                <form action="{{ route('user.card.delete') }}" method="POST" class="pull-left">

						                    <input type="hidden" name="_method" value="DELETE">
						                    
						                    <input type="hidden" name="card_id" value="{{ $card->id }}">

						                    <button class="btn btn-danger btn-sm" type="submit" class="bk-nw-ct text-white"><i class="fa fa-times"></i> {{tr('delete_card')}}</button>
						                </form>

						                <div class="clearfix"></div>
						              </div>
						              @endif
						              <div class="clearfix"></div>
					            </div>

					            <br>

					            @endforeach

					            

					          @else

					            {{tr('no_card_details_found')}}


					        @endif

					        <br>
					        <br>
					     </div>
					</div>
				</div>
				
			<div class="sidebar-back"></div> 
		</div>

	</div>

</div>

@endsection



@section('scripts')


<!-- <script type="text/javascript" src="{{ asset('assets/js/card1.js') }}"></script>
    
<script>
    new Card({
    form: document.querySelector('form'),
    container: '.card-wrapper'
});
</script> -->

<script type="text/javascript" src="{{ asset('assets/js/card.js') }}"></script>

<script>
    $('#card-payment form').card({ container: $('.card-wrapper')});

    function card_number_onkey(value) {


    	$('.jp-card-number').html(value.replace(/\W/gi, '').replace(/(.{4})/g, '$1 '));

    	
    }
</script>

<script type="text/javascript" src="https://js.stripe.com/v2/"></script>

<script type="text/javascript">
    // This identifies your website in the createToken call below
    Stripe.setPublishableKey('{{ Setting::get("stripe_publishable_key", "pk_test_AHFoxSxndSb5RjlwHpfceeYa")}}');

    
    var stripeResponseHandler = function (status, response) {
        var $form = $('#payment-form');

        console.log(response);

        if (response.error) {
            // Show the errors on the form
            $form.find('.payment-errors').text(response.error.message);
            $form.find('button').prop('disabled', false);
            alert(response.error.message);

        } else {
            // token contains id, last4, and card type
            var token = response.id;
            // Insert the token into the form so it gets submitted to the server
            $form.append($('<input type="hidden" id="stripeToken" name="stripeToken" />').val(token));
             // alert(token);
            // and re-submit

            jQuery($form.get(0)).submit();

        }
    
    };

    $('#payment-form').submit(function (e) {
        
        if ($('#stripeToken').length == 0)
        {
            var $form = $(this);
            // Disable the submit button to prevent repeated clicks
            $form.find('button').prop('disabled', true);
            console.log($form);
            Stripe.card.createToken($form, stripeResponseHandler);

            // Prevent the form from submitting with the default action
            return false;
        }
    
    });


</script>
@endsection