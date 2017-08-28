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
		        <div class="col-xs-12">
		          <div class="box">
		            <div class="box-body">

		            	@if(count($payments) > 0)

			              	<table id="example1" class="table table-bordered table-striped">

								<thead>
								    <tr>
								      <th>{{tr('id')}}</th>
								      <th>{{tr('username')}}</th>
								      <th>{{tr('payment_id')}}</th>
								      <th>{{tr('amount')}}</th>
								      <th>{{tr('expiry_date')}}</th>
								    </tr>
								</thead>

								<tbody>

									@foreach($payments as $i => $payment)

									    <tr>
									      	<td>{{$i+1}}</td>
									      	<td><a href="{{route('admin.view.user' , $payment->user_id)}}"> {{($payment->user) ? $payment->user->name : ''}} </a></td>
									      	<td>{{$payment->payment_id}}</td>
									      	<td>$ {{$payment->amount}}</td>
									      	<td>{{date('d M Y',strtotime($payment->expiry_date))}}</td>
									    </tr>					
									@endforeach
								</tbody>
							</table>

							<div>
								
							</div>
						@else
							<h3 class="no-result">{{tr('no_result_found')}}</h3>
						@endif

		            </div>
		          </div>
		        </div>
		    </div>


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
												<a target="_blank" href="{{route('admin.subscriptions.view' , $subscription->unique_id)}}">{{$subscription->title}}</a>
											</h3>

											<div class="subscription-desc">
												<?php echo $subscription->description; ?>
											</div>

											<br>

											<p>
												<span class="btn btn-danger pull-left">{{ Setting::get('currency')}} {{$subscription->amount}} / {{$subscription->plan}} M</span>

												<a href="{{route('admin.subscription.save' , ['s_id' => $subscription->id, 'u_id'=>$id])}}" class="btn btn-success pull-right">{{tr('choose')}}</a>

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