@extends('layouts.admin')

@section('title', tr('view_history'))

@section('content-header')

{{tr('view_history')}} - 

<a href="{{route('admin.users.view' , $user_details->id)}}">{{$user_details->name}}</a>

@endsection

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('admin.users.index')}}"><i class="fa fa-user"></i> {{tr('users')}}</a></li>
    <li class="active"> {{tr('view_history')}}</li>
@endsection

@section('content')

	<div class="row">
        
        <div class="col-xs-12">

        @include('notification.notify')

          	<div class="box">

	            <div class="box-body">

	            	@if(count($user_histories) > 0)

		              	<table id="example1" class="table table-bordered table-striped">

							<thead>
							    <tr>
							      <th>{{tr('id')}}</th>
							      <th>{{tr('username')}}</th>
							      <th>{{tr('video')}}</th>
							      <th>{{tr('date')}}</th>
							      <th>{{tr('action')}}</th>
							    </tr>
							</thead>

							<tbody>

								@foreach($user_histories as $i => $user_history_details)

								    <tr>
								      	<td>{{$i+1}}</td>

								      	<td>{{$user_history_details->username}}</td>
								      	
								      	<td>{{$user_history_details->title}}</td>
								      	
								      	<td>{{$user_history_details->date}}</td>
									    
									    <td>
	            							<ul class="admin-action btn btn-default">
	            								<li class="dropup">

									                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
									                  {{tr('action')}} 
									                  <span class="caret"></span>
									                </a>

									                <ul class="dropdown-menu">
									                  	<li role="presentation"><a role="menuitem" tabindex="-1" onclick="return confirm('Are you sure?');" href="{{route('admin.delete.history' , $user_history_details->user_history_id)}}">{{tr('delete_history')}}</a></li>
									                </ul>

	              								</li>
	            							</ul>
									    </td>
									    
								    </tr>					

								@endforeach
							</tbody>
						</table>
					@else
						<h3 class="no-result">{{tr('no_history_found')}}</h3>
					@endif
	            </div>

          	</div>

        </div>
    
    </div>

@endsection


