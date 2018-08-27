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

					@foreach($response->data as  $key => $temp)

					<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
						<div class="new-subcription-history">
							<div class="space row">
								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
									<h4>{{$temp->title}}</h4>
								</div>
								@if($key == 0 && $temp->status == PAID_STATUS)

									@if ($temp->is_cancelled == AUTORENEWAL_ENABLED)
										<div class="col-lg-6">
											<a href="#" class="link" data-toggle="modal" data-target="#disable">{{tr('pause_autorenewal')}}</a>
										</div>
									@else 
										<div class="col-lg-6">
											<a href="#" class="link" data-toggle="modal" data-target="#enable">{{tr('enable_autorenewal')}}</a>
										</div>
									@endif
								
								@endif
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
										<span class="gold-clr">{{tr('success')}}</span>
									@else
										<span class="red-clr">{{tr('failed')}}</span>
									@endif
								</h5>
							</div>
							<p class="subscriptions-line"></p>
							<div class="space white-bg subscription-height">
								<h5 class=""><span class="head">{{tr('expiry_date')}}:</span>&nbsp;{{$temp->expiry_date}}</h5>
								@if($temp->cancel_reason)

								<h5 class=""><span class="head">{{tr('cancel_reason')}}:</span>&nbsp;{{$temp->cancel_reason}} </h5>

								@endif
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
					      		<h4 class="modal-title">{{tr('enable_autorenewal')}}</h4>
					    	</div>
					    	<div class="modal-body">

					    		<form method="post" action="{{route('user.subscriptions.enable-subscription')}}">
						      		<p class="note grey-clr text-left">{{tr('enable_autorenewal_notes')}}</p>
						      		<div class="text-right">
						      			<button type="submit" class="btn btn-primary mr-10">{{tr('enable')}}</button>
						      			<button type="button" class="btn btn-default" data-dismiss="modal">{{tr('close')}}</button>
						      		</div>
						      	</form>
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
					      		<h4 class="modal-title">{{tr('pause_autorenewal')}}</h4>
					    	</div>
					    	<div class="modal-body">

					    		<form method="post" action="{{route(
					    		'user.subscriptions.pause-subscription')}}">
						      		<p class="note grey-clr text-left">{{tr('pause_autorenewal_notes')}}</p>
						      			<div class="form-group" id="disable_form">
										  	<textarea class="form-control" rows="4" id="comment" placeholder="{{tr('cancel_reason')}}" name="cancel_reason"></textarea>
										  	<p class="underline2"></p>
										</div>
						      		
						      		<div class="text-right">
						      			<button type="submit" class="btn btn-primary mr-10">{{tr('pause')}}</button>
						      			<button type="button" class="btn btn-default" data-dismiss="modal">{{tr('close')}}</button>
						      		</div>
					      		</form>
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