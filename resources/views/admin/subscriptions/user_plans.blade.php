@extends('layouts.admin')

@section('title', tr('subscriptions'))

@section('content-header', tr('subscriptions'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-key"></i> {{tr('subscriptions')}}</li>
@endsection

@section('after-styles')

<style>

.subscription-image {
	overflow: hidden !important;
	position: relative !important;
	height: 15em !important;
	background-position: center !important;
	background-repeat: no-repeat !important;
	background-size: cover !important;
	margin: 0 !important;
	width: 100%;
}

.subscription-desc {
	min-height: 10em !important;
	max-height: 10em !important;
	overflow: scroll !important;
	margin-bottom: 10px !important;
}

</style>

@endsection

@section('content')

			

			<div class="row">

				<div class="col-md-12">

					<!-- <h3>{{tr('subscription')}}</h3> -->

					@include('notification.notify')

					<div class="row">

						@if(count($subscriptions) > 0)

							@foreach($subscriptions as $s => $subscription)

								<div class="col-md-4 col-lg-4 col-sm-6 col-xs-12">

									<div class="thumbnail">

										<!-- <img alt="{{$subscription->title}}" src="{{$subscription->picture ?  $subscription->picture : asset('common/img/landing-9.png')}}" class="subscription-image" /> -->
										<div class="caption">

											<h3>
												{{$subscription->title}}
											</h3>

											<div class="subscription-desc">
												<?php echo $subscription->description; ?>
											</div>

											<br>

											<p>
												<span class="btn btn-danger pull-left">{{ Setting::get('currency')}} {{$subscription->amount}} / {{$subscription->plan}} M</span>

												<a href="{{route('admin.subscription.save' , ['s_id' => $subscription->id, 'u_is'=>$id])}}" class="btn btn-success pull-right">{{tr('choose')}}</a>

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


@endsection