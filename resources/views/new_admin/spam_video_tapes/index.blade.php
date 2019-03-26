@extends('layouts.admin')

@section('title', tr('spam_videos'))

@section('content-header', tr('spam_videos'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-flag"></i> {{tr('spam_videos')}}</li>
@endsection

@section('content')

	@include('notification.notify')

	<div class="row">
        <div class="col-xs-12">
          <div class="box box-info">
            <div class="box-body">

            	@if(count($flags) > 0)

	              	<table id="example1" class="table table-bordered table-striped">

						<thead>
						    <tr>
						      <th>{{tr('id')}}</th>
						      <th>{{tr('channel')}}</th>
						      <th>{{tr('title')}}</th>
						      <th>{{tr('user_count')}}</th>
						      <th>{{tr('status')}}</th>
						      <th>{{tr('action')}}</th>
						    </tr>
						</thead>

						<tbody>
							@foreach($flags as $i => $flag_details)
							    
							    <tr>
							      	<td>{{$i+1}}</td>

							      	<td>{{($flag_details->videoTape) ? $flag_details->videoTape->channel_name : ''}}</td>

							      	<td>{{$flag_details->videoTape ? substr($flag_details->videoTape->title , 0,25) : ''}}...</td>

							      	<td><a target="_blank" href="{{route('admin.spam-videos.user-reports' , $flag_details->video_tape_id)}}">{{$flag_details->videoTape ? $flag_details->videoTape->getScopeUserFlags() : 0}}</a></td>
							      	
							      	<td>
							      		@if ($flag_details->videoTape)
							      		@if($flag_details->videoTape->is_approved)
							      			<span class="label label-success">{{tr('approved')}}</span>
							       		@else
							       			<span class="label label-warning">{{tr('pending')}}</span>
							       		@endif
							       		@else

							       			-

							       		@endif

							      	</td>

							      	<td>
            							<ul class="admin-action btn btn-default">
            								
            								<li class="dropup">
            								
								                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
								                  {{tr('action')}} <span class="caret"></span>
								                </a>
								                <ul class="dropdown-menu">
								                  	<li role="presentation">
								                  		@if(Setting::get('admin_delete_control') == YES)

									                  	 	<a role="button" href="javascript:;" class="btn disabled" style="text-align: left">{{tr('delete')}}</a>

									                  	@else
								                  			<a role="menuitem" tabindex="-1" onclick="return confirm('Are you sure?')" href="{{route('admin.video_tapes.delete' , ['video_tape_id' => $flag_details->video_tape_id] )}}">{{tr('delete')}}</a>
								                  		@endif
								                  	</li>

													<li class="divider" role="presentation"></li>

													@if($flag_details->videoTape)

								                  	@if($flag_details->videoTape->is_approved)
								                		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.video_tapes.status',$flag_details->video_tape_id)}}">{{tr('decline')}}</a></li>
								                	@else
								                  		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.video_tapes.status',$flag_details->video_tape_id)}}">{{tr('approve')}}</a></li>
								                  	@endif

								                  	<li class="divider" role="presentation"></li>

								                  	@endif

								                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.spam-videos.user-reports' , $flag_details->video_tape_id)}}">{{tr('user_reports')}}</a></li>
								                </ul>
              								</li>
            							</ul>
							      </td>
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
