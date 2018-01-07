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

			<div class="sub-history">
				<h3 class="no-margin">Subscription History</h3>	

				<?php $subscription_details = get_expiry_days(Auth::user()->id);?>

                <p style="color:#cc181e">{{tr('no_of_days_expiry')}} <b>{{$subscription_details['days']}} days (Paid ${{$subscription_details['amount']}})</b></p>

                <div class="pull-right">
                	
                	<a href="{{route('user.subscriptions')}}"><button class="btn btn-sm btn-primary">{{tr('view_plans')}}</button></a>

                </div>

                <div class="clearfix"></div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1">
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
											<p class="top5">{{$temp->plan}} months</p>
										</div>

										@if($temp->status)
										<div class="col-xs-6 col-sm-6">
											<small class="no-margin">Expies On</small>
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

	</div>
</div>

@endsection