@extends('layouts.admin')

@section('title', tr('ppv_payments'))

@section('content-header')

{{tr('ppv_payments')}} - {{Setting::get('currency')}} {{total_video_revenue('admin')}}


@endsection

@section('breadcrumb')

    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>

    <li class="active"><i class="fa fa-credit-card	"></i> {{tr('ppv_payments')}}</li>

@endsection


@section('styles')

<style>
	.modal-body li {
		padding: 12px 5px;
	}
</style>
@endsection

@section('content')

@include('notification.notify')

	<div class="row">
        <div class="col-xs-12">
          <div class="box box-info">
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
								<!-- <th>{{tr('admin_amount')}}</th> -->
								<!-- <th>{{tr('user_amount')}}</th> -->
								<th>{{tr('reason')}}</th>
								<th>{{tr('status')}}</th>
								<th>{{tr('action')}}</th>
						    </tr>
						</thead>

						<tbody>

							@foreach($data as $i => $payment)

							    <tr>

							      	<td>{{$i+1}}</td>

							      	<td>

							      		@if($payment->videoTapeDetails)

							      			<a href="{{route('admin.view.video' , array('id' => $payment->videoTapeDetails->id))}}">
							      				{{$payment->videoTapeDetails->title}}
							      			</a>

							      		@else

							      		 	-

							      		@endif

							      	</td>

							      	<td>

							      		@if($payment->userDetails)

							      		<a href="{{route('admin.users.view' , $payment->user_id)}}"> 
							      			{{$payment->userDetails ? $payment->userDetails->name : " "}} 
							      		</a>

							      		@endif

									</td>

							      	<td>{{$payment->payment_id}}</td>

							      	<td>{{Setting::get('currency')}} {{$payment->amount}}</td>

							      	<!-- <td>{{Setting::get('currency')}} {{$payment->admin_ppv_amount}}</td> -->

							      	<!-- <td>{{Setting::get('currency')}} {{$payment->user_ppv_amount}}</td> -->

							      	<td>{{$payment->reason}}</td>

							      	<td>
							      		@if($payment->amount <= 0)

							      			<label class="label label-danger">{{tr('not_paid')}}</label>

							      		@else
							      			<label class="label label-success">{{tr('paid')}}</label>

							      		@endif 
							      	</td>

							      	<td><a href="" data-toggle="modal" data-target="#PPV_DETAILS_{{$payment->id}}" class="btn btn-sm btn-success">{{tr('view')}}</a></td>
							    </tr>	


								<div class="modal fade" id="PPV_DETAILS_{{$payment->id}}" role="dialog">
									<div class="modal-dialog modal-lg">
										<div class="modal-content">

											<div class="modal-header">

												<button type="button" class="close" data-dismiss="modal">&times;</button>

												<h4 class="modal-title">{{$payment->payment_id}}</h4>

											</div>

											<div class="modal-body">
												<ul>
													<li>
														{{tr('video')}} : @if($payment->videoTapeDetails)

										      			<a href="{{route('admin.view.video' , array('id' => $payment->videoTapeDetails->id))}}">
										      				{{$payment->videoTapeDetails->title}}
										      			</a>

											      		@else

											      		 	-

											      		@endif
										      		</li>

										      		<li>
										      			{{tr('username')}} :

										      			<a href="{{route('admin.users.view' , $payment->user_id)}}"> 
							      							{{$payment->userDetails ? $payment->userDetails->name : " "}} 
							      						</a>

										      		</li>

										      		<li>{{tr('total')}} : {{Setting::get('currency')}} {{$payment->amount}}</li>

										      		<li>{{tr('admin_ppv_commission')}} : {{Setting::get('currency')}} {{$payment->admin_ppv_amount}}</li>

										      		<li>{{tr('user_ppv_commission')}} : {{Setting::get('currency')}} {{$payment->user_ppv_amount}}</li>

										      		<li>{{tr('reason')}} : {{$payment->reason}}</li>

										      		<li>{{tr('paid_date')}} : s{{date('d M Y',strtotime($payment->created_at))}}</li>
												</ul>
											</div>

											<div class="modal-footer">
												<button type="button" class="btn btn-default" data-dismiss="modal">{{tr('close')}}</button>
											</div>
										
										</div>
									</div>
								</div>

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


