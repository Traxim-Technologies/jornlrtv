@extends('layouts.admin')

@section('title', tr('playlists'))

@section('content-header') 

{{tr('playlists')}} 

@endsection

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-user"></i> {{tr('playlists')}}</li>
@endsection

@section('content')

	@include('notification.notify')

	<div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">
          	<div class="box-header label-primary">
                <b style="font-size:18px;">{{tr('playlists')}}</b>

                <a href="{{route('admin.playlists.create')}}" class="btn btn-default pull-right">{{tr('add_playlist')}}</a>

                <!-- EXPORT OPTION START -->

					@if(count($playlists) > 0 )
	                
		                <ul class="admin-action btn btn-default pull-right" style="margin-right: 20px">
		                 	
							<li class="dropdown">
				                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
				                  {{tr('export')}} <span class="caret"></span>
				                </a>
				                <ul class="dropdown-menu">
				                  	<li role="presentation">
				                  		<a role="menuitem" tabindex="-1" href="{{route('admin.playlists.export' , ['format' => 'xls'])}}">
				                  			<span class="text-red"><b>{{tr('excel_sheet')}}</b></span>
				                  		</a>
				                  	</li>

				                  	<li role="presentation">
				                  		<a role="menuitem" tabindex="-1" href="{{route('admin.playlists.export' , ['format' => 'csv'])}}">
				                  			<span class="text-blue"><b>{{tr('csv')}}</b></span>
				                  		</a>
				                  	</li>
				                </ul>
							</li>
						</ul>

					@endif

	            <!-- EXPORT OPTION END -->
            </div>
            <div class="box-body table-responsive">

            	@if(count($playlists) > 0)

	              	<table id="example1" class="table table-bordered table-striped">

						<thead>
						    <tr>
						      <th>{{tr('id')}}</th>
						      <th>{{tr('title')}}</th>
						      <th>{{tr('email')}}</th>
						      <th>{{tr('no_of_channels')}}</th>
						      <th>{{tr('no_of_videos')}}</th>
						      <th>{{tr('validity_days')}}</th>
						      <th>{{tr('redeems')}}</th>
						      @if(Setting::get('email_verify_control'))
						      <th>{{tr('email_verification')}}</th>
						      @endif
						      <th>{{tr('status')}}</th>
						      <th>{{tr('action')}}</th>
						    </tr>
						</thead>

						<tbody>
							@foreach($playlists as $i => $playlist_details)

							    <tr>
							      	<td>{{$i+1}}</td>
							      	<td>
							      		<a href="{{route('admin.playlists.view' , $playlist_details->id)}}"> 

							      			{{$playlist_details->name}}

							      			@if($playlist_details->user_type)

							      			<span class="text-green pull-right"><i class="fa fa-check-circle"></i></span>

							      			@else

							      			<span class="text-red pull-right"><i class="fa fa-times"></i></span>

							      			@endif

							      		</a>
							      	</td>
							      	<td>{{$playlist_details->email}}</td>
							      	
							      	<td class="text-center"><a target="_blank" href="{{route('admin.playlists.channels' , $playlist_details->id)}}">{{$playlist_details->get_channel_count}}</a></td>

							      	<td class="text-center"><a target="_blank" href="{{route('admin.video_tapes.list' , $playlist_details->id)}}">{{$playlist_details->get_channel_videos_count}}</a></td>

									<td>
										@if($playlist_details->user_type)
											{{get_expiry_days($playlist_details->id)['days']}} days
										@endif
									</td>

							      	<td>
							      		<b>{{Setting::get('currency')}} {{$playlist_details->userRedeem ? $playlist_details->userRedeem->remaining : 0}}</b>
							     	</td>

							      @if(Setting::get('email_verify_control'))

							      <td>

							      	@if(!$playlist_details->is_verified)

							      		<a href="{{route('admin.playlists.verify' , $playlist_details->id)}}" class="btn btn-xs btn-success">{{tr('verify')}}</a>

							      	@else

							      		<span>{{tr('verified')}}</span>

							      	@endif
							      	
							      </td>

							      @endif

							      <td>
							      		
							      		@if($playlist_details->status)
							      			<span class="label label-success">{{tr('approved')}}</span>
							       		@else
							       			<span class="label label-warning">{{tr('pending')}}</span>
							       		@endif

							      </td>
							 
							      <td>
            							<ul class="admin-action btn btn-default">

            								<li class="@if($i < 2) dropdown @else dropup @endif">
								               
								                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
								                  {{tr('action')}} <span class="caret"></span>
								                </a>

								                <ul class="dropdown-menu dropdown-menu-right">
								                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.playlists.edit' , array('id' => $playlist_details->id))}}">{{tr('edit')}}</a></li>

								                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.playlists.view' , $playlist_details->id)}}">{{tr('view')}}</a></li>
								                  	
								                  	<li role="presentation" class="divider"></li>

								                  	
								                  	<li role="presentation">
								                  	 	@if(Setting::get('admin_delete_control'))
								                  	 		<a role="button" href="javascript:;" class="btn disabled" style="text-align: left">{{tr('delete')}}</a>
								                  	 	@elseif(get_expiry_days($playlist_details->id) > 0)

								                  	 		<a role="menuitem" tabindex="-1" onclick="return confirm('Are you sure want to delete the premium user?');" href="{{route('admin.playlists.delete', array('id' => $playlist_details->id))}}">{{tr('delete')}}
								                  			</a>
								                  		@else 
								                  			<a role="menuitem" tabindex="-1" onclick="return confirm('Are you sure?');" href="{{route('admin.delete.user', array('id' => $playlist_details->id))}}">{{tr('delete')}}
								                  			</a>
								                  	 	@endif

								                  	</li>

								                  	<?php 

								                  		$approve_notes = tr('approve_notes');

								                  		$decline_notes = tr('decline_notes');

								                  	?>
								                  	@if($playlist_details->status==0)
								                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.playlists.status',array('id'=>$playlist_details->id,'status'=>1))}}" onclick='return confirm("{{$approve_notes}}")'>{{tr('approve')}}</a></li>
								                  	@else
								                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.playlists.status',array('id'=>$playlist_details->id,'status'=>0))}}"  onclick='return confirm("{{$decline_notes}}")'>{{tr('decline')}}</a></li>
								                  	@endif
								                  	
								                </ul>
              								</li>
            							</ul>
							      </td>
							    </tr>
							@endforeach
						</tbody>
					</table>
				@else
					<h3 class="no-result">{{tr('no_playlist_found')}}</h3>
				@endif
            </div>
          </div>
        </div>
    </div>

@endsection
