@extends('layouts.admin')

@section('title', tr('user_subscription_payments'))

@section('content-header')

{{ tr('user_subscription_payments') }} - {{Setting::get('currency')}} {{total_subscription_revenue($subscription ? $subscription->id : "")}}

@endsection

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-money"></i> {{tr('user_subscription_payments')}}</li>
@endsection

@section('content')

@include('notification.notify')

	<div class="row">

        <div class="col-xs-12">

          	<div class="box box-primary">

          		<div class="box-header label-primary">
	                <b>{{tr('user_subscription_payments')}}</b>
	                <a href="{{route('admin.users')}}" style="float:right" class="btn btn-default">{{tr('view_users')}}</a>
	            </div>

	            <div class="box-body">

	              	<table id="example1" class="table table-bordered table-striped">

						<thead>
						    <tr>
								<th>{{tr('id')}}</th>
								<th>{{tr('subscription')}}</th>
								<th>{{tr('payment_id')}}</th>
								<th>{{tr('username')}}</th>
								<!-- <th>{{tr('payment_mode')}}</th> -->
								<!-- <th>{{tr('no_of_jobs')}}</th> -->
								<th>{{tr('plan')}}</th>
								<th>{{tr('amount')}}</th>
								<th>{{tr('status')}}</th>
						    </tr>
						</thead>

						<tbody>

							@if(count($data) > 0)

								@foreach($data as $i => $payment)

								    <tr>
								      	<td>{{$i+1}}</td>

								      	<td>	
								      		@if($payment->getSubscription)

								      			<a href="{{route('admin.subscriptions.view' , $payment->getSubscription->unique_id)}}">

								      				{{$payment->getSubscription ? $payment->getSubscription->title : ""}}

								      			</a>

							      			@else 
							      				{{$payment->getSubscription ? $payment->getSubscription->title : ""}}
							      			@endif

								      	</td>
								      	
								      	<td>{{$payment->payment_id}}</td>

								      	<td>
								      		<a href="{{route('admin.view.user' , $payment->user_id)}}"> @if($payment->user) {{$payment->user->name}} @endif</a>
								      	</td>

								      	<!-- <td class="text-uppercase">{{$payment->payment_mode}}</td> -->

								      	<!-- <td>{{$payment->no_of_jobs}}</td> -->

								      	<td>{{$payment->getSubscription ? $payment->getSubscription->plan : ""}}</td>

								      	<td class="text-red"><b>{{Setting::get('currency')}} {{$payment->amount}}</b></td>

								      	<td>
								      		@if($payment->status) 
								      			<span style="color: green;"><b>{{tr('paid')}}</b></span>
								      		@else
								      			<span style="color: red"><b>{{tr('not_paid')}}</b></span>

								      		@endif
								      	</td>
								    </tr>					

								@endforeach

							@endif

						</tbody>
					
					</table>
					
	            </div>

          	</div>
        </div>
    </div>

@endsection


