@extends('layouts.admin')

@section('title', tr('live_video_payments'))

@section('content-header',tr('live_video_payments'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-credit-card"></i> {{tr('live_video_payments')}}</li>
@endsection

@section('content')

@include('notification.notify')

	<div class="row">
        <div class="col-xs-12">

          <div class="box">
          	<div class="box-header label-primary">

          	<!-- EXPORT OPTION START -->

					@if(count($data) > 0 )
	                
		                <ul class="admin-action btn btn-default pull-right" style="margin-right: 50px">
		                 	
							<li class="dropdown">
				                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
				                  {{tr('export')}} <span class="caret"></span>
				                </a>
				                <ul class="dropdown-menu">
				                  	<li role="presentation">
				                  		<a role="menuitem" tabindex="-1" href="{{route('admin.livevideo-payment.export' , ['format' => 'xls'])}}">
				                  			<span class="text-red"><b>{{tr('excel_sheet')}}</b></span>
				                  		</a>
				                  	</li>

				                  	<li role="presentation">
				                  		<a role="menuitem" tabindex="-1" href="{{route('admin.livevideo-payment.export' , ['format' => 'csv'])}}">
				                  			<span class="text-blue"><b>{{tr('csv')}}</b></span>
				                  		</a>
				                  	</li>
				                </ul>
							</li>
						</ul>

					@endif

	            <!-- EXPORT OPTION END -->
	           	</div>
            <div class="box-body">
            	@if(count($data) > 0)

	              	<table id="example1" class="table table-bordered table-striped">

						<thead>
						    <tr>
						      <th>{{tr('id')}}</th>
						      <th>{{tr('video')}}</th>
						      <th>{{tr('username')}}</th>
						      <th>{{tr('payment_id')}}</th>
						      <th>{{tr('amount')}}</th>
							  <th>{{tr('admin_live_commission')}}</th>
						      <th>{{tr('user_live_commission')}}</th>
						      <th>{{tr('paid_date')}}</th>
						      <th>{{tr('status')}}</th>
						    </tr>
						</thead>

						<tbody>

							@foreach($data as $i => $payment)

							    <tr>
							      	<td>{{$i+1}}</td>
							      	<td><a target="_blank" href="{{route('admin.live-videos.view' , $payment->id)}}">{{$payment->getVideoPayment ? $payment->getVideoPayment->title : ''}}</a></td>

							      	<td><a target="_blank" href="{{route('admin.users.view' , $payment->user_id)}}"> {{$payment->user ? $payment->user->name : '-'}} </a></td>
							      	<td>{{$payment->payment_id}}</td>
							      	<td>$ {{$payment->amount}}</td>
							      	<td>$ {{$payment->admin_amount}}</td>
							      	<td>$ {{$payment->user_amount}}</td>
							      	<td>{{$payment->created_at->diffForHumans()}}</td>
							      	<td>
							      	@if($payment->status)
							      		<label class="text-green">{{tr('paid')}}</label>
							      	@else
							      		<label class="text-red">{{tr('not_paid')}}</label>
							      	@endif
							      	</td>
							    </tr>					

							@endforeach
						</tbody>
					</table>
				@else
					<h3 class="no-result">{{tr('no_result_found')}}</h3>
				@endif
            </div>
          </div>
        </div>
    </div>

@endsection


