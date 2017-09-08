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



			<div class="modal fade cus-mod" id="paypal" role="dialog">
                <div class="modal-dialog">
                
                  <!-- Modal content-->
                  <div class="modal-content">

                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title text-center text-uppercase">{{tr('payment_options')}}</h4>
                        </div>


                        <div class="modal-body">
                            <!-- <p>Please Pay to see the full video</p>  -->
                                <div class="col-lg-6">
                                  <!-- small box -->
                                  <div class="small-box bg-green">
                                    <div class="inner">
                                      <h3>{{ Setting::get('currency')}} {{$subscription->amount}}</h3>
                                      <div class="clearfix"></div>
                                      <p style="float: none;" class="text-left">{{tr('paypal_payment')}}</p>
                                    </div>
                                    <div class="icon">
                                      <i class="fa fa-money"></i>
                                    </div>
                                     <div class="clearfix"></div>
                                    <a href="{{route('user.paypal' , $subscription->id)}}" class="small-box-footer">{{tr('for_subscription')}} <i class="fa fa-arrow-circle-right"></i></a>
                                  </div>
                                </div>
                           
                                <div class="col-lg-6">
                                  <!-- small box -->
                                  <div class="small-box bg-aqua">
                                    <div class="inner">
                                      <h3>{{ Setting::get('currency')}} {{$subscription->amount}}</h3>
                                      <div class="clearfix"></div>
                                      <p style="float: none;" class="text-left">{{tr('stripe_payment')}}</p>
                                    </div>
                                    <div class="icon">
                                      <i class="fa fa-money"></i>
                                    </div>
                                     <div class="clearfix"></div>
                                    <a onclick="return confirm('Are you sure want to pay through card?')" href="{{route('user.card.stripe_payment' , ['subscription_id' => $subscription->id])}}" class="small-box-footer">{{tr('for_subscription')}} <i class="fa fa-arrow-circle-right"></i></a>
                                  </div>
                                </div>
                            
                            
                            <div class="clearfix"></div>
                            
                        </div>

                        
                  </div>
                  
                </div>
            
            </div>

														@if($subscription->amount > 0)

															<?php /* @if(Setting::get('payment_type') == 'paypal')

															<a href="{{route('user.paypal' , $subscription->id)}}" class="btn btn-success pull-right">{{tr('choose_plan')}}</a>

															@else

																<a href="{{route('user.card.stripe_payment' , ['subscription_id' => $subscription->id])}}" class="btn btn-success pull-right">{{tr('choose_plan')}}</a>

															@endif */?>

															 <button  type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#paypal">{{tr('choose_plan')}}</button>

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

								@endif
								
							</div>
						</div>
					</div>
				</div>

		</div>

	</div>

</div>

@endsection