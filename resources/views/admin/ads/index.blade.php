@extends('layouts.admin')

@section('title', tr('view_ads'))

@section('content-header', tr('view_ads'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-bullhorn"></i> {{tr('view_ads')}}</li>
@endsection

@section('content')

    @include('notification.notify')
	<div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">

          	<div class="box-header label-primary">
                <b style="font-size:18px;">{{tr('view_ads')}}</b>
            </div>

            <div class="box-body">

            	@if(count($model) > 0)

	              	<table id="example1" class="table table-bordered table-striped">

						<thead>
						    <tr>
						      <th>{{tr('id')}}</th>
						      <th>{{tr('channel')}}</th>
						      <th>{{tr('title')}}</th>
						      <th>{{tr('type_of_ads')}}</th>
						      <th>{{tr('action')}}</th>
						    </tr>
						</thead>
						<tbody>


							@foreach($model as $i => $data)
							    <tr>
							      	<td>{{$i+1}}</td>
							      	<td>{{isset($data->get_video_tape->channel_details->name) ? 
							      	$data->get_video_tape->channel_details->name : '-'}}</td>
							      	<td>{{isset($data->get_video_tape->title) ? 
							      	substr($data->get_video_tape->title , 0,25) : '-'}}...</td>
							      	
							      	<td>
							      		@foreach($data->ads_types as $type)
								      		
								      		<span class="label label-success">{{$type}}</span>

								       	@endforeach
							      	</td>
								    <td>
            							<?php /*<ul class="admin-action btn btn-default">
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
            							</ul> */?>
								    </td>
							    </tr>

								<!-- Modal -->
								
							@endforeach
						</tbody>
					</table>
				@else
					<h3 class="no-result">{{tr('no_ads_found')}}</h3>
				@endif
            </div>
          </div>
        </div>
    </div>

@endsection