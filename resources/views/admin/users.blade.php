@extends('layouts.admin')

@section('title', tr('users'))

@section('content-header', tr('users'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-user"></i> {{tr('users')}}</li>
@endsection

@section('content')

	@include('notification.notify')

	<div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-body">

            	@if(count($users) > 0)

	              	<table id="example1" class="table table-bordered table-striped">

						<thead>
						    <tr>
						      <th>{{tr('id')}}</th>
						      <th>{{tr('username')}}</th>
						      <th>{{tr('email')}}</th>
						      <th>{{tr('dob')}}</th>
						      <th>{{tr('mobile')}}</th>
						      <th>{{tr('address')}}</th>
						      <th>{{tr('upgrade')}}</th>
						      <!-- <th>{{tr('status')}}</th> -->
						      <th>{{tr('action')}}</th>
						    </tr>
						</thead>

						<tbody>
							@foreach($users as $i => $user)
					
							    <tr>
							      	<td>{{$i+1}}</td>
							      	<td>{{$user->name}}</td>
							      	<td>{{$user->email}}</td>
							      	<td>@if($user->dob && $user->dob != 0000-00-00) {{date('d M, Y',strtotime($user->dob))}} @else - @endif</td>
							      	<td>{{$user->mobile}}</td>
							      	<td>{{$user->address}}</td>
							      	<td>
							      		@if($user->is_moderator)
							      			<a onclick="return confirm('Are you sure?');" href="{{route('user.upgrade.disable' , array('id' => $user->id, 'moderator_id' => $user->moderator_id))}}" class="label label-warning">{{tr('disable')}}</a>
							      		@else
							      			<a onclick="return confirm('Are you sure?');" href="{{route('admin.user.upgrade' , array('id' => $user->id ))}}" class="label label-danger">{{tr('upgrade')}}</a>
							      		@endif

							      </td>
							      <!-- <td>
							      		if($user->is_activated) 
							      			<span class="label label-success">{{tr('approved')}}</span>
							       		else 
							       			<span class="label label-warning">{{tr('pending')}}</span>
							       		endif
							       </td> -->
							      <td>
            							<ul class="admin-action btn btn-default">
            								<li class="dropdown">
								                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
								                  {{tr('action')}} <span class="caret"></span>
								                </a>
								                <ul class="dropdown-menu">
								                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.edit.user' , array('id' => $user->id))}}">{{tr('edit_user')}}</a></li>
								                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.view.user' , $user->id)}}">{{tr('view_user')}}</a></li>
								                  	<li role="presentation" class="divider"></li>

								                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.user.history', $user->id)}}">{{tr('view_history')}}</a></li>

								                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.user.wishlist', $user->id)}}">{{tr('view_wishlist')}}</a></li>

								                  	<li role="presentation" class="divider"></li>
								                  	<li role="presentation"><a role="menuitem" tabindex="-1" onclick="return confirm('Are you sure?');" href="{{route('admin.delete.user', array('id' => $user->id))}}">{{tr('delete_user')}}</a></li>

								                </ul>
              								</li>
            							</ul>
							      </td>
							    </tr>
							@endforeach
						</tbody>
					</table>
				@else
					<h3 class="no-result">{{tr('no_user_found')}}</h3>
				@endif
            </div>
          </div>
        </div>
    </div>

@endsection


