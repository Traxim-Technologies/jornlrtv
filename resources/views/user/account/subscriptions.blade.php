@extends('layouts.user')


@section('content')

<div class="y-content">

    <div class="row y-content-row">

        @include('layouts.user.nav')

		<div class="page-inner col-sm-9 col-md-10 profile-edit">
				
				<div class="profile-content">	

					<div class="row no-margin">

                    	<div class="col-sm-12 profile-view">

							<h3>{{tr('subscriptions')}}</h3>

							@include('notification.notify')

							<div class="row">

								@if(count($subscriptions) > 0)

									@foreach($subscriptions as $s => $subscription)
									
										<div class="col-md-4 col-lg-4 col-sm-6 col-xs-12">

											<div class="thumbnail">

												<img alt="{{$subscription->title}}" src="{{$subscription->picture ?  $subscription->picture : asset('images/landing-9.png')}}" class="subscription-image" />
												<div class="caption">

													<h3>
														{{$subscription->title}}
													</h3>

													<div class="subscription-desc">
														<?php echo $subscription->description; ?>
													</div>

													<p>
														<span class="btn btn-danger pull-left">{{ Setting::get('currency')}} {{$subscription->amount}} / {{$subscription->plan}} M</span>

														<!-- <a href="#" class="btn btn-success pull-right">{{tr('choose')}}</a> -->



			

														@if($subscription->amount > 0)

															<?php /* @if(Setting::get('payment_type') == 'paypal')

															<a href="{{route('user.paypal' , $subscription->id)}}" class="btn btn-success pull-right">{{tr('choose_plan')}}</a>

															@else

																<a href="{{route('user.card.stripe_payment' , ['subscription_id' => $subscription->id])}}" class="btn btn-success pull-right">{{tr('choose_plan')}}</a>

															@endif */?>

															<?php /*<button  type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#paypal">{{tr('choose_plan')}}</button> */?>

															<a href="{{route('user.subscription.invoice' , ['s_id' => $subscription->id, 'u_id'=>Auth::user()->id])}}" class="btn btn-success pull-right">{{tr('choose_plan')}}</a>

														@else

																<a href="{{route('user.subscription.save' , ['s_id' => $subscription->id, 'u_id'=>Auth::user()->id])}}" class="btn btn-success pull-right">{{tr('choose_plan')}}</a>
															

														@endif
													</p>
													<br>
													<br>
												</div>
											
											</div>
										
										</div>

									@endforeach

								@else

									 <img src="{{asset('images/no-result.jpg')}}" class="img-responsive auto-margin">

								@endif
								
							</div>
						</div>
					</div>
				</div>
			<div class="sidebar-back"></div> 
		</div>

	</div>

</div>

@endsection