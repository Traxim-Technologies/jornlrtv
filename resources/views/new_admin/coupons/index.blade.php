@extends('layouts.admin')

@section('title',tr('coupons'))

@section('content-header',tr('coupons'))

@section('breadcrumb')

	<li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i>{{ tr('home') }}</a></li>
	<li class="active">{{ tr('coupons') }}</li>

@endsection

@section('content')
	@include('notification.notify')
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-info">
				<div class="box-header label-primary">
					<b style="font-size: 18px;">{{ tr('coupons') }}</b>
					<a href="{{ route('admin.coupons.create') }}" class="btn btn-default pull-right">{{ tr('add_coupon') }}</a>
				</div>
				<div class="box-body">
					@if(count($coupons)>0)
					<table id = "example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>{{ tr('id') }}</th>
								<th>{{ tr('title') }}</th>
								<th>{{ tr('coupon_code') }}</th>
								<th>{{ tr('amount_type') }}</th>
								<th>{{ tr('amount') }}</th>
								<th>{{ tr('expiry_date') }}</th>
								<th>{{ tr('status') }}</th>
								<th>{{ tr('action') }}</th>
							</tr>
						</thead>
						<tbody>
							@foreach($coupons as $i => $coupon_details)
							<tr>
								<td>{{ $i+1 }}</td>

								<td><a href=""></a> href="{{ route('admin.coupons.view', ['coupon_id' => $coupon_details->id] ) }}">{{ $coupon_details->title }}</a></td>

								<td>{{ $coupon_details->coupon_code }}</td>

								<td>
									@if($coupon_details->amount_type == PERCENTAGE)
									<span class="label label-primary">{{ tr('percentage') }}</span>
									@else
									<span class="label label-primary">{{ tr('absoulte') }}</span>
									@endif
								</td>

								<td>
									@if($coupon_details->amount_type == PERCENTAGE)
									{{ $coupon_details->amount }} %
									@else
									{{ Setting::get('currency') }} {{ $coupon_details->amount }} 
									@endif
								</td>

								<td>							
									{{ date('d M y', strtotime($coupon_details->expiry_date)) }}
								</td>

								<td>
									@if($coupon_details->status == APPROVED)								<span class="label label-success">{{ tr('active') }}</span>
									@else
										<span class="label label-warning">{{ tr('inactive') }}</span>
									@endif
								</td>

								<td>
									<ul class="admin-action btn btn-default">

										<li class="dropdown">

											<a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                                  {{ tr('action') }} <span class="caret"></span>
                                            </a>
										
											<ul class="dropdown-menu">

												<li role="presentation">
													<a class = "menuitem"  tabindex= "-1" href="{{ route('admin.coupons.edit', ['coupon_id' => $coupon_details->id] ) }}">{{ tr('edit') }}</a>
												</li>

												<li role="presentation">
													<a class="menuitem" tabindex="-1" href="{{ route('admin.coupons.view', ['coupon_id' => $coupon_details->id] ) }}">{{ tr('view') }}</a>
												</li>

												<li role="presentation">
													<a class="menuitem" tabindex="-1" href="{{ route('admin.coupons.delete', ['coupon_id' => $coupon_details->id]) }}" onclick="return confirm('Are You Sure?')">{{ tr('delete') }}</a>
												</li>

												<li role="presentation">
													@if($coupon_details->status == DECLINED)
													<a class="menuitem" tabindex="-1" href="{{ route('admin.coupons.status',['coupon_id' => $coupon_details->id] ) }}" onclick="return confirm('Are You Sure?')">{{ tr('active') }} </a>
													@else
													<a class="menuitem" tabindex="-1" href="{{ route('admin.coupons.status',['coupon_id' => $coupon_details->id] ) }}" onclick="return confirm('Are You Sure')">{{ tr('inactive') }}</a>
													@endif
												</li>
											</ul>

										</li>

									</ul>

								</td>

							</tr>

							@endforeach
						</tbody>

					</table>
					@else
						<h3 class="no-result">{{ tr('coupon_result_not_found_error') }}</h3>
					@endif

				</div>

			</div>

		</div>

	</div>


@endsection

