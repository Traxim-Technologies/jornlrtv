@extends('layouts.admin')

@section('title', tr('ppv_payments'))

@section('content-header',tr('ppv_payments') . ' ( $ ' . total_ppv_admin_video_revenue() . ' ) ' ) 

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-credit-card	"></i> {{tr('ppv_payments')}}</li>
@endsection

@section('content')

@include('notification.notify')

	<div class="row">
        <div class="col-xs-12">

          <div class="box box-info">            
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
				                  		<a role="menuitem" tabindex="-1" href="{{route('admin.payperview.export' , ['format' => 'xls'])}}">
				                  			<span class="text-red"><b>{{tr('excel_sheet')}}</b></span>
				                  		</a>
				                  	</li>

				                  	<li role="presentation">
				                  		<a role="menuitem" tabindex="-1" href="{{route('admin.payperview.export' , ['format' => 'csv'])}}">
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
								<th>{{tr('payment_mode')}}</th>
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

							      		@if($payment->title)

							      			<a href="{{route('admin.videos.view' , array('id' => $payment->video_id))}}">
							      				{{$payment->title}}
							      			</a>

							      		@else

							      		 	-

							      		@endif

							      	</td>

							      	<td>
							      		@if($payment->userDetails)

							      		<a href="{{route('admin.users.view' , $payment->user_id)}}"> 
							      			{{$payment->userDetails ? $payment->userDetails->name : " - "}} 
							      		</a>

							      		@endif

									</td>

							      	<td>{{$payment->payment_id}}</td>


							      	<td>{{$payment->payment_mode}}</td>

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
														{{tr('video')}} : @if($payment->title)

										      			<a href="{{route('admin.videos.view' , array('id' => $payment->video_id))}}">
										      				{{$payment->title}}
										      			</a>

											      		@else

											      		 	-

											      		@endif
										      		</li>

										      		<li>
										      			{{tr('username')}} :

										      			<a href="{{route('admin.users.view' , $payment->user_id)}}"> 
							      							{{$payment->user_name ? $payment->user_name : "-"}} 
							      						</a>

										      		</li>

										      		<li>{{tr('total')}} : {{Setting::get('currency')}} {{$payment->amount}}</li>

										      		<li>{{tr('admin_ppv_commission')}} : {{Setting::get('currency')}} {{$payment->admin_ppv_amount}}</li>

										      		<li>{{tr('user_ppv_commission')}} : {{Setting::get('currency')}} {{$payment->user_ppv_amount}}</li>

										      		<li>{{tr('reason')}} : {{$payment->reason}}</li>

										      		<li>{{tr('paid_date')}} : {{date('d M Y',strtotime($payment->created_at))}}</li>

										      		<li>{{tr('type_of_subscription')}} : {{$payment->type_of_subscription}}</li>

										      		<li>{{tr('type_of_user')}} : {{$payment->type_of_user}}</li>

										      		<li>{{tr('coupon_code')}} : {{$payment->coupon_code}}</li>
											      <li>{{tr('coupon_amount')}} : {{Setting::get('currency')}} {{$payment->coupon_amount? $payment->coupon_amount : "0.00"}}</li>
											      <li>{{tr('plan_amount')}} : {{Setting::get('currency')}} {{$payment->ppv_amount ? $payment->ppv_amount : "0.00"}}</li>
											      <li>{{tr('final_amount')}} : {{Setting::get('currency')}} {{$payment->amount ? $payment->amount : "0.00" }}</li>
											      <li>{{tr('is_coupon_applied')}} : @if($payment->is_coupon_applied)
										<span class="label label-success">{{tr('yes')}}</span>
										@else
										<span class="label label-danger">{{tr('no')}}</span>
										@endif</li>
											      <li>{{tr('coupon_reason')}} : {{$payment->coupon_reason ? $payment->coupon_reason : '-'}}</li>

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


