@extends('layouts.admin')

@section('title', 'Requests')

@section('content-header', 'Requests')

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>Home</a></li>
    <li class="active"><i class="fa fa-paper-plane"></i> Requests</li>
@endsection

@section('content')

	<div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-body">

            	@if(count($requests) > 0)

	              	<table id="example1" class="table table-bordered table-striped">

						<thead>
						    <tr>
						      <th>ID</th>
						      <th>Seeker Name</th>
						      <th>Poster Name</th>
						      <th>Job Title</th>
						      <th>Address</th>
						      <th>Job Start Date</th>
						      <th>Timeline</th>
						      <th>Start Time</th>
						      <th>End Time</th>
						      <th>Karama Point</th>
						      <th>Status</th>
						      <!-- <th>Action</th> -->
						    </tr>
						</thead>

						<tbody>
							@foreach($requests as $i => $request)
					
							    <tr>
							      <td>{{$i+1}}</td>
							      <td>{{substr($request->user_name,0,10)}}</td>
							      <td>{{substr($request->provider_name,0,10)}}</td>
							      <td>{{substr($request->job_title,0,10)}}</td>
							      <td>{{$request->address}}</td>
							      <td>@if($request->job_date && $request->job_date != '0000-00-00') {{date('H:i - d M, Y',strtotime($request->job_date))}} @endif</td>
							      <td>{{$request->timeline}}</td>
							      <td>{{$request->start_time}}</td>
							      <td>{{$request->end_time}}</td>
							      <td>{{$request->karama_point}}</td>
							      <td>
							      		@if($request->status == 1) 
							      			<span class="label label-success">JOB SENT</span>
							       		@elseif($request->status == 2)
							       			<span class="label label-warning">JOB ACCEPTED</span>
							       		@elseif($request->status == 3)
							       			<span class="label label-warning">JOB REJECTED</span>
							       		@elseif($request->status == 4)
							       			<span class="label label-warning">JOB STARTED</span>
							       		@elseif($request->status == 5)
							       			<span class="label label-warning">JOB COMPLETED USER</span>
							       		@elseif($request->status == 6)
							       			<span class="label label-warning">JOB COMPLETED</span>
							       		@elseif($request->status == 7)
							       			<span class="label label-warning">JOB CANCELLED</span>
							       		@elseif($request->status == 8)
							       			<span class="label label-warning">JOB CANCELLED</span>
							       		@endif
							      </td>
							      <!-- <td>
            							<ul class="admin-action btn btn-default">
            								<li class="dropdown">
								                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
								                  Action <span class="caret"></span>
								                </a>
								                <ul class="dropdown-menu">
								                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="#">View Request</a></li>
								                </ul>
              								</li>
            							</ul>
							      </td> -->
							    </tr>
							@endforeach
						</tbody>
					</table>
				@else
					<h3 class="no-result">No results found</h3>
				@endif
            </div>
          </div>
        </div>
    </div>

@endsection


