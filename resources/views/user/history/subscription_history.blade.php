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

			<div class="spacing1 text-center">
				<h3 class="no-margin">{{tr('subscription_history')}}</h3>	

				<?php $subscription_details = get_expiry_days(Auth::user()->id);?>

                <p style="color:#cc181e;margin-top: 10px;">{{tr('no_of_days_expiry')}} <b>{{$subscription_details['days']}} days (Paid ${{$subscription_details['amount']}})</b></p>

                <div class="pull-right">
                	
                	<a href="{{route('user.subscriptions')}}"><button class="btn btn-sm btn-primary mb-20">{{tr('view_plans')}}</button></a>

                </div>

                <div class="clearfix"></div>
				
				<?php /* 
				<div class="">
					<div class="row">
						<div class="col-xs-12 col-sm-12 col-md-12">
							<div class="row">

								@if(count($response->data) > 0)

									@foreach($response->data as $temp)

									<div class="col-xs-12 col-sm-6 col-md-4 top">
										<div class="sub-history-card">
											<div class="sub-head">
												<h4 class="no-margin-top text-ellipsis">{{$temp->title}}</h4>
												<p class="no-margin">{{$temp->currency}}{{$temp->amount}}</p>
											</div>
											<div class="sub-desc">
												<p class="no-margin"><?= $temp->description;?></p>
											</div>
											<div class="row sub-deatils">
												<div class="col-xs-6 col-sm-6">

													@if($temp->status)

													<small class="label label-success">{{tr('success')}}</small>

													@else
													<small class="label label-danger">{{tr('failed')}}</small>
													@endif
													<p class="top5 mb-0">{{$temp->plan}} {{tr('months')}}</p>
												</div>

												@if($temp->status)
												<div class="col-xs-6 col-sm-6">
													<small class="no-margin">{{tr('expires_on')}}</small>
													<p class="no-margin">{{$temp->expiry_date}}</p>
												</div>
												@endif
											</div>
										</div>
									</div>

									@endforeach

									<div class="row">
			                            <div class="col-md-12">
			                                <div align="center" id="paglink"><?php echo $response->pagination; ?></div>
			                            </div>
			                        </div>

								@else

									<img src="{{asset('images/no-result.jpg')}}" class="img-responsive auto-margin">

								@endif
								
							</div>
						</div>
					</div>
				</div>
				*/ ?>

				<!-- new UI -->
				<div class="row">
				@if(count($response->data) > 0)

					@foreach($response->data as $temp)

					<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
						<div class="new-subcription-history">
							<div class="space row">
								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
									<h4>{{$temp->title}}</h4>
								</div>
								<div class="col-lg-6">
									<a href="#" class="link" data-toggle="modal" data-target="#enable">Enable Autorenewal</a>
								</div>
							</div>
							<p class="subscriptions-line"></p>
							<div class="space">
								<h1 class="price">
									<span class="icon">{{$temp->currency}}</span>
									<span class="amount">{{$temp->amount}}</span>
									<span class="period">/ {{$temp->plan}}&nbsp;{{tr('months')}}</span>
								</h1>
								<h5>Payment status:&nbsp;
									@if($temp->status)
										<span class="gold-clr">Success</span>
									@else
										<span class="red-clr">{{tr('failed')}}</span>
									@endif
								</h5>
							</div>
							<p class="subscriptions-line"></p>
							<div class="space white-bg subscription-height">
								<h5 class=""><span class="head">expiry date:</span>&nbsp;{{$temp->expiry_date}}</h5>
								<h5 class=""><span class="head">cancel reason:</span>&nbsp;Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
								tempor incididunt ut labore et </h5>
								<div class="subscription-desc-list">
									<?= $temp->description;?>
								</div>
							</div>
						</div>
					</div>

					@endforeach

					<div class="row">
                        <div class="col-md-12">
                            <div align="center" id="paglink"><?php echo $response->pagination; ?></div>
                        </div>
                    </div>

                    @else

					<img src="{{asset('images/no-result.jpg')}}" class="img-responsive auto-margin">

				@endif			
				</div>
				<!-- new UI -->

				<!--enable modal -->
				<div class="modal fade" id="enable" role="dialog">
					<div class="modal-dialog">

					  	<!-- Modal content-->
					  	<div class="modal-content autorenewal">
					    	<div class="modal-header">
					      		<button type="button" class="close" data-dismiss="modal">&times;</button>
					      		<h4 class="modal-title">enable autorenewal</h4>
					    	</div>
					    	<div class="modal-body">
					      		<p class="note grey-clr text-left">Your subscription autorenewal is paused. Please activate autorenewal and enjoy your videos</p>
					      		<div class="text-right">
					      			<button type="button" class="btn btn-primary mr-10">enable</button>
					      			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					      		</div>
					    	</div>
					  </div>
					  
					</div>
				</div>
				<div class="modal fade" id="disable" role="dialog">
					<div class="modal-dialog">

					  	<!-- Modal content-->
					  	<div class="modal-content autorenewal">
					    	<div class="modal-header">
					      		<button type="button" class="close" data-dismiss="modal">&times;</button>
					      		<h4 class="modal-title">disable autorenewal</h4>
					    	</div>
					    	<div class="modal-body">
					      		<p class="note grey-clr text-left">Pause your subscription autorenewal to take a break on the payment</p>
					      		<form>
					      			<div class="form-group" id="disable_form">
									  	<textarea class="form-control" rows="4" id="comment" placeholder="Enter cancel reason"></textarea>
									  	<p class="underline2"></p>
									</div>
					      		</form>
					      		<div class="text-right">
					      			<button type="button" class="btn btn-primary mr-10">disable</button>
					      			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					      		</div>
					    	</div>
					  </div>
					  
					</div>
				</div>
				<!-- modal -->
			</div>
		</div>

	</div>
</div>

@endsection