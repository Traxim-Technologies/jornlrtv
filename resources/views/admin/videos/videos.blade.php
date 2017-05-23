@extends('layouts.admin')

@section('title', tr('videos'))

@section('content-header', tr('videos'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-video-camera"></i> {{tr('videos')}}</li>
@endsection

@section('content')

    @include('notification.notify')
	<div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">

          	<div class="box-header label-primary">
                <b style="font-size:18px;">{{tr('videos')}}</b>
                <a href="{{route('admin.add.video')}}" class="btn btn-default pull-right">{{tr('add_video')}}</a>
            </div>

            <div class="box-body">

            	@if(count($videos) > 0)

	              	<table id="example1" class="table table-bordered table-striped">

						<thead>
						    <tr>
						      <th>{{tr('id')}}</th>
						      <th>{{tr('channel')}}</th>
						      <th>{{tr('title')}}</th>
						      @if(Setting::get('theme') == 'default')
						      	<th>{{tr('slider_video')}}</th>
						      @endif
						      <th>{{tr('ad_status')}}</th>
						      <th>{{tr('status')}}</th>
						      <th>{{tr('action')}}</th>
						    </tr>
						</thead>
						<tbody>


							@foreach($videos as $i => $video)
							    <tr>
							      	<td>{{$i+1}}</td>
							      	<td>{{$video['channel_details'] ? $video['channel_details']['name'] : ''}}</td>
							      	<td>{{substr($video['title'] , 0,25)}}...</td>
							      	@if(Setting::get('theme') == 'default')
							      	<td>
							      		@if($video['is_home_slider'] == 0 && $video['is_approved'] && $video['status'])
							      			<a href="{{route('admin.slider.video' , $video['admin_video_id'])}}"><span class="label label-danger">{{tr('set_slider')}}</span></a>
							      		@elseif($video['is_home_slider'])
							      			<span class="label label-success">{{tr('slider')}}</span>
							      		@else
							      			-
							      		@endif
							      	</td>

							      	@endif
							      	<td class="text-center">
							      		@if($video['user_details']['ads_status'])
							      			<span class="label label-success">{{tr('yes')}}</span>
							      		@else
							      			<span class="label label-danger">{{tr('no')}}</span>
							      		@endif
							      	</td>
							      	<td>
							      		@if ($video['publish_status'] == 0)
							      			<span class="label label-danger">{{tr('compress')}}</span>
							      		@else
								      		@if($video['is_approved'])
								      			<span class="label label-success">{{tr('approved')}}</span>
								       		@else
								       			<span class="label label-warning">{{tr('pending')}}</span>
								       		@endif
								       	@endif
							      	</td>
								    <td>
            							<ul class="admin-action btn btn-default">
            								<li class="dropup">
								                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
								                  {{tr('action')}} <span class="caret"></span>
								                </a>
								                <ul class="dropdown-menu">
								                	@if ($video['publish_status'] == 1)
								                  	<li role="presentation">
                                                        @if(Setting::get('admin_delete_control'))
                                                            <a role="button" href="javascript:;" class="btn disabled" style="text-align: left">{{tr('edit')}}</a>
                                                        @else
                                                            <a role="menuitem" tabindex="-1" href="{{route('admin.edit.video' , array('id' => $video['admin_video_id']))}}">{{tr('edit')}}</a>
                                                        @endif
                                                    </li>
                                                    @endif
								                  	<li role="presentation"><a role="menuitem" tabindex="-1" target="_blank" href="{{route('admin.view.video' , array('id' => $video['admin_video_id']))}}">{{tr('view')}}</a></li>

								               

								                  	<li class="divider" role="presentation"></li>

								                  	@if($video['is_approved'])
								                		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.video.decline',$video['admin_video_id'])}}">{{tr('decline')}}</a></li>
								                	@else
								                		@if ($video['publish_status'] == 0)
								                			<li role="presentation"><a role="menuitem" tabindex="-1">{{tr('compress')}}</a></li>
								                		@else 
								                  			<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.video.approve',$video['admin_video_id'])}}">{{tr('approve')}}</a></li>
								                  		@endif
								                  	@endif

								                  	@if($video['status'] == 0)
								                  		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.video.publish-video',$video['admin_video_id'])}}">{{tr('publish')}}</a></li>
								                  	@endif

								                  	@if($video['user_details']['ads_status']) 

								                  		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.ads_create', $video['admin_video_id'])}}">{{tr('video_ad')}}</a></li>

								                  	@endif

								                  	@if ($video['publish_status'] == 1)
									                  	<li class="divider" role="presentation"></li>

									                  	<li role="presentation">
									                  		@if(Setting::get('admin_delete_control'))

										                  	 	<a role="button" href="javascript:;" class="btn disabled" style="text-align: left">{{tr('delete')}}</a>

										                  	@else
									                  			<a role="menuitem" tabindex="-1" onclick="return confirm('Are you sure?')" href="{{route('admin.delete.video' , array('id' => $video['admin_video_id']))}}">{{tr('delete')}}</a>
									                  		@endif
									                  	</li>
								                  	@endif
								                </ul>
              								</li>
            							</ul>
								    </td>
							    </tr>

								<!-- Modal -->
								
							@endforeach
						</tbody>
					</table>
				@else
					<h3 class="no-result">{{tr('no_video_found')}}</h3>
				@endif
            </div>
          </div>
        </div>
    </div>

@endsection