@extends('layouts.admin')

@section('title', tr('video_payments'))

@section('content-header',tr('video_payments') . ' ( $ ' . total_video_revenue() . ' ) ' ) 

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-credit-card	"></i> {{tr('video_payments')}}</li>
@endsection

@section('content')

@include('notification.notify')

	<div class="row">
        <div class="col-xs-12">
          <div class="box">
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
						      <th>{{tr('paid_date')}}</th>
						    </tr>
						</thead>

						<tbody>

							@foreach($data as $i => $payment)

							    <tr>
							      	<td>{{$i+1}}</td>
							      	<td><a target="_blank" href="{{route('admin.videos.view' , $payment->id)}}">{{$payment->getVideo ? $payment->getVideo->title : ''}}</a></td>
							      	<td><a target="_blank" href="{{route('admin.view.user' , $payment->user_id)}}"> {{$payment->user ? $payment->user->name : ''}} </a></td>
							      	<td>{{$payment->payment_id}}</td>
							      	<td>$ {{$payment->amount}}</td>
							      	<td>{{$payment->created_at->diffForHumans()}}</td>
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


