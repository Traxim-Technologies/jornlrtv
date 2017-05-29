@extends('layouts.admin')

@section('title', tr('view_user'))

@section('content-header', tr('view_user'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('admin.users')}}"><i class="fa fa-user"></i> {{tr('users')}}</a></li>
    <li class="active"><i class="fa fa-user"></i> {{tr('view_user')}}</li>
@endsection

@section('styles')

	<style type="text/css">
		.timeline::before {
		    content: '';
		    position: absolute;
		    top: 0;
		    bottom: 0;
		    width: 0;
		    background: #fff;
		    left: 0px;
		    margin: 0;
		    border-radius: 0px;
		}
		.check-redeem {
			color: #FFF !important;
		}

		.nav > li > a:hover, .nav > li > a:active, .nav > li > a:focus {
			color: black !important;
			background-color: green !important;
		}
	</style>

@endsection

@section('content')

	<div class="row">

		<div class="col-md-5 col-md-offset-1">

    		<div class="box box-widget widget-user-2">

            	<div class="widget-user-header bg-blue">
            		<div class="pull-left">
	              		<div class="widget-user-image">
	                		<img class="img-circle" src="@if($user->picture) {{$user->picture}} @else {{asset('admin-css/dist/img/avatar.png')}} @endif" alt="User Avatar">
	              		</div>

	              		<h3 class="widget-user-username">{{$user->name}} </h3>
	      				<h5 class="widget-user-desc">{{tr('user')}}</h5>
      				</div>
      				<div class="pull-right">
      					<a href="{{route('admin.edit.user' , array('id' => $user->id))}}" class="btn btn-sm btn-warning">{{tr('edit')}}</a>
      				</div>
      				<div class="clearfix"></div>
            	</div>

            	<div class="box-footer no-padding">
              		<ul class="nav nav-stacked">
		                <li><a href="#">{{tr('username')}} <span class="pull-right">{{$user->name}}</span></a></li>
		                <li><a href="#">{{tr('email')}} <span class="pull-right">{{$user->email}}</span></a></li>
		                <li><a href="#">{{tr('mobile')}} <span class="pull-right">{{$user->mobile}}</span></a></li>
		                <li><a href="#">{{tr('address')}} <span class="pull-right">{{$user->address}}</span></a></li>
		                <li><a href="#">{{tr('validity_days')}} <span class="pull-right"> 
		                				@if($user->user_type)
                                            <p style="color:#cc181e">The Pack will Expiry within <b>{{get_expiry_days($user->id)['days']}} days</b></p>
                                        @endif</span></a></li>
		                <li>
		                	<a href="#">{{tr('status')}} 
		                		<span class="pull-right">
		                			@if($user->is_verified) 
						      			<span class="label label-success">{{tr('approved')}}</span>
						       		@else 
						       			<span class="label label-warning">{{tr('pending')}}</span>
						       		@endif
		                		</span>
		                	</a>
		                </li>
              		</ul>
            	</div>
          	
          	</div>

		</div>

		<div class="col-md-4 col-md-offset-1">

			<div class="box box-widget widget-user-2">

				<div class="widget-user-header bg-green">

					<h4>{{tr('redeems')}}</h4>

				</div>

				<div class="box-footer no-padding">

              		<ul class="nav nav-stacked">

              			<li>
		                	<a href="#">{{tr('total')}} 
		                		<span class="pull-right">
		                			{{Setting::get('currency')}} {{$user->userRedeem ? $user->userRedeem->total : "0.00"}}
		                		</span>
		                	</a>
		                </li>

		                <li>
		                	<a href="#">{{tr('wallet_balance')}} 
		                		<span class="pull-right">
		                			{{Setting::get('currency')}} {{$user->userRedeem ? $user->userRedeem->remaining : "0.00"}}
		                		</span>
		                	</a>
		                </li>

		                <li>
		                	<a href="#">{{tr('paid_amount')}} 
		                		<span class="pull-right">
		                			{{Setting::get('currency')}} {{$user->userRedeem ? $user->userRedeem->paid : "0.00"}}
		                		</span>
		                	</a>
		                </li>

		                <li>
		                	
		                	<a href="{{route('admin.users.redeems' , $user->id)}}" class="btn btn-success check-redeem" style="background-color: #00a65a !important; color: #fff !important" >	

		                	{{tr('check_redeem_requests')}}

		                	</a>

		                </li>

		            </ul>
		        </div>
			</div>

		</div>

    </div>

@endsection


