@extends('layouts.admin')

@section('title', tr('channel_payments'))

@section('content-header')

{{ tr('channel_payments')}} - {{ formatted_amount(total_video_revenue('admin')) }}

@endsection

@section('breadcrumb')

    <li><a href="{{ route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{ tr('home')}}</a></li>

    <li class="active"><i class="fa fa-credit-card	"></i> {{ tr('channel_payments')}}</li>

@endsection

@section('styles')

<style>
	.modal-body li {
		padding: 12px 5px;
	}
</style>
@endsection

@section('content')

	<div class="row">

        <div class="col-xs-12">

        	@include('notification.notify')

          	<div class="box box-info">  

         		<div class="box-header label-primary">

                <!-- EXPORT OPTION START -->

					@if(count($payments) > 0 )
	                
		                <ul class="admin-action btn btn-default pull-right" style="margin-right: 50px">
		                 	
							<li class="dropdown">
				                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
				                  {{ tr('export')}} <span class="caret"></span>
				                </a>
				                <ul class="dropdown-menu">
				                  	<li role="presentation">
				                  		<a role="menuitem" tabindex="-1" href="{{ route('admin.payperview.export' , ['format' => 'xls'])}}">
				                  			<span class="text-red"><b>{{ tr('excel_sheet')}}</b></span>
				                  		</a>
				                  	</li>

				                  	<li role="presentation">
				                  		<a role="menuitem" tabindex="-1" href="{{ route('admin.payperview.export' , ['format' => 'csv'])}}">
				                  			<span class="text-blue"><b>{{ tr('csv')}}</b></span>
				                  		</a>
				                  	</li>
				                </ul>
							</li>
						</ul>

					@endif

	            <!-- EXPORT OPTION END -->
            	</div>
            	<div class="box-body">

            	@if(count($payments) > 0)

	              	<table id="example1" class="table table-bordered table-striped">

						<thead>
						    <tr>
								<th>{{ tr('id')}}</th>
								<th>{{ tr('username')}}</th>
								<th>{{ tr('channel')}}</th>
								<th>{{ tr('payment_id')}}</th>
								<th>{{ tr('payment_mode')}}</th>
								<th>{{ tr('amount')}}</th>
								<th>{{ tr('is_current')}}</th>
								<th>{{ tr('status')}}</th>
						    </tr>
						</thead>

						<tbody>

							@foreach($payments as $i => $payment_details)

							    <tr>	

							      	<td>{{ $i+1}}</td>

							      	<td>
							      		@if($payment_details->UserDetails->name)

							      		<a href="{{ route('admin.users.view' , ['user_id' => $payment_details->user_id] )}}"> 
							      			{{ $payment_details->UserDetails->name ? $payment_details->UserDetails->name : " "}} 
							      		</a>

							      		@endif
									</td>

									<td><a href="{{ route('admin.channels.view' , ['channel_id' => $payment_details->channel_id] )}}">
										{{$payment_details->channelDetails->name ?? '-'}}
									</td>

							      	<td>{{ $payment_details->payment_id}}</td>

							      	<td>{{ $payment_details->payment_mode}}</td>

							      	<td>{{ formatted_amount($payment_details->amount) }}</td>

						      		<td>
							      		@if($payment_details->is_current == YES)

							      			<label class="label label-danger">{{ tr('yes')}}</label>

							      		@else
							      			<label class="label label-success">{{ tr('no')}}</label>

							      		@endif 
							      	</td>

							      	<td>
							      		@if($payment_details->amount <= 0)

							      			<label class="label label-danger">{{ tr('not_paid')}}</label>

							      		@else
							      			<label class="label label-success">{{ tr('paid')}}</label>

							      		@endif 
							      	</td>

							      
							    </tr>	

		
							@endforeach

						</tbody>

					</table>

				@else
					<h3 class="no-result">{{ tr('no_result_found')}}</h3>
				@endif
	            </div>

	          	</div>

      		</div>

    </div>

@endsection


