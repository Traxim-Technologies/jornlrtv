@extends('layouts.admin')

@section('title', tr('subscriptions'))

@section('content-header')

{{  tr('subscriptions')  }} - {{ Setting::get('currency') }} {{ total_subscription_revenue() }}

@endsection

@section('breadcrumb')

    <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i>{{ tr('home') }}</a></li>
    <li class="active"><i class="fa fa-key"></i> {{ tr('subscriptions') }}</li>

@endsection

@section('content')

	<div class="row">

        <div class="col-xs-12">
		
			@include('notification.notify')

          	<div class="box box-primary">

          		<div class="box-header label-primary">
                	<b>{{ tr('subscriptions') }}</b>

                	<a href="{{ route('admin.subscriptions.create') }}" style="float:right" class="btn btn-default">{{ tr('add_subscription') }}</a>
	            </div>
	            
	            <div class="box-body">
	            	
	              	<table id="example1" class="table table-bordered table-striped">

						<thead>
						    <tr>
						      	<th>{{ tr('id') }}</th>
						      	<th>{{ tr('title') }}</th>
						      	<th>{{ tr('plan') }}</th>
						      	<th>{{ tr('amount') }}</th>
						      	<th>{{ tr('status') }}</th>
						      	<th>{{ tr('subscribers') }}</th>
						      	<th>{{ tr('action') }}</th>
						    </tr>
						</thead>

						<tbody>
						
							@foreach($subscriptions as $i => $subscription_details)

							    <tr>
							      	<td>{{ $i+1 }}</td>

							      	<td><a href="{{ route('admin.subscriptions.view' , ['subscription_id' => $subscription_details->id] ) }}"> {{ $subscription_details->title }} </a></td>

							      	<td>{{ $subscription_details->plan }}</td>

							      	<td>{{ Setting::get('currency' , "$") }} {{ $subscription_details->amount }}</td>
							      	
							      	<td class="text-center">

						      			@if($subscription_details->status)
							      			<span class="label label-success">{{ tr('approved') }}</span>
							      		@else
							      			<span class="label label-warning">{{ tr('pending') }}</span>
							      		@endif
							      	</td>

							      	<td>
							      		<a href="{{ route('admin.revenues.subscription-payments' , ['subscription_id' => $subscription_details->id] ) }}" class="btn btn-success btn-xs">
							      			{{ tr('subscribers') }}
							      		</a>
							      	</td>
							      
									<td>
										<ul class="admin-action btn btn-default">

											<li class="dropdown">

									            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
									              {{ tr('action') }} <span class="caret"></span>
									            </a>

									            <ul class="dropdown-menu">

									              	<li role="presentation">
									              		<a role="menuitem" tabindex="-1" href="{{ route('admin.subscriptions.view' , ['subscription_id' => $subscription_details->id] ) }}"><i class="fa fa-eye"></i>&nbsp;{{ tr('view') }}
									              		</a>
									              	</li>									              	

													@if(Setting::get('admin_delete_control') == DEFAULT_TRUE )
														<li  role="presentation"> <a role="menuitem" tabindex="-1" href="javascript:;" class="btn disabled" style="text-align: left"><i class="fa fa-trash" onclick="return confirm(&quot;{{ tr('admin_subscription_delete_confirmation', $subscription_details->title ) }}&quot;);"></i>&nbsp;{{ tr('delete') }}</a></li>

														<li  role="presentation"> <a role="menuitem" tabindex="-1" tabindex="-1" href="javascript:;"><i class="fa fa-edit"></i>&nbsp;{{ tr('edit') }}
								              			</a></li>

													@else
														<li  role="presentation"> <a role="menuitem" tabindex="-1" tabindex="-1" onclick="return confirm('Are you sure?');" href="{{ route('admin.subscriptions.delete', array('id' => $subscription_details->id)) }}"><i class="fa fa-trash" onclick="return confirm(&quot;{{ tr('admin_subscription_delete_confirmation', $subscription_details->title ) }}&quot;);" ></i>&nbsp;{{ tr('delete') }}</a></li>

														<li  role="presentation"> <a role="menuitem" tabindex="-1" tabindex="-1" href="{{ route('admin.subscriptions.edit' , ['subscription_id' => $subscription_details->id] ) }}"><i class="fa fa-edit"></i>&nbsp;{{ tr('edit') }}
								              			</a></li>
													@endif	

									    
									              	<li role="presentation" class="divider"></li>

									              	@if($subscription_details->status == DEFAULT_TRUE)

									              		<li role="presentation">
									              			<a role="menuitem" tabindex="-1" href="{{ route('admin.subscriptions.status' , ['subscription_id' => $subscription_details->id] ) }}" onclick="return confirm(&quot;{{ tr('admin_subscription_decline_confirmation', $subscription_details->title) }}&quot;)">
									              				<span class="text-red"><b><i class="fa fa-close"></i>&nbsp;{{ tr('decline') }}</b></span>
									              			</a>
									              		</li>

									              	@else

														<li role="presentation">
									              			<a role="menuitem" tabindex="-1" href="{{ route('admin.subscriptions.status' , ['subscription_id' => $subscription_details->id] ) }}" onclick="return confirm(&quot;{{ tr('admin_subscription_approve_confirmation', $subscription_details->title) }}&quot;)">
									              				<span class="text-green"><b><i class="fa fa-check"></i>&nbsp;{{ tr('approve') }}</b></span>
									              			</a>
									              		</li>								              	

									              	@endif								       								              									       

									            </ul>
											
											</li>
										</ul>

									</td>
							    
							    </tr>

							@endforeach

						</tbody>
					
					</table>
	            </div>

          	</div>

        </div>

    </div>

@endsection
