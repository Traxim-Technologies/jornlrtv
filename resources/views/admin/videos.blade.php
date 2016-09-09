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
          <div class="box">
            <div class="box-body">

            	@if(count($videos) > 0)

	              	<table id="example1" class="table table-bordered table-striped">

						<thead>
						    <tr>
						      <th>{{tr('id')}}</th>
						      <th>{{tr('category')}}</th>
						      <th>{{tr('sub_category')}}</th>
						      <th>{{tr('genre')}}</th>
						      <th>{{tr('title')}}</th>
						      <th>{{tr('description')}}</th>
						      @if(Setting::get('theme') == 'default')
						      	<th>{{tr('slider_video')}}</th>
						      @endif
						      <th>{{tr('status')}}</th>
						      <th>{{tr('action')}}</th>
						    </tr>
						</thead>

						<tbody>
							@foreach($videos as $i => $video)
					
							    <tr>
							      	<td>{{$i+1}}</td>
							      	<td>{{$video->category_name}}</td>
							      	<td>{{$video->sub_category_name}}</td>
							      	<td>@if($video->genre_name) {{$video->genre_name}} @else - @endif</td>
							      	<td>{{substr($video->title , 0,25)}}...</td>
							      	<td>{{substr($video->description , 0, 25)}}...</td>
							      	@if(Setting::get('theme') == 'default')
							      	<td>
							      		@if($video->is_home_slider == 0 && $video->is_approved && $video->status)
							      			<a href="{{route('admin.slider.video' , $video->video_id)}}"><span class="label label-danger">{{tr('set_slider')}}</span></a>
							      		@elseif($video->is_home_slider)
							      			<span class="label label-success">{{tr('slider')}}</span>
							      		@else
							      			-
							      		@endif
							      	</td>

							      	@endif
							      	<td>
							      		@if($video->is_approved) 
							      			<span class="label label-success">{{tr('approved')}}</span>
							       		@else 
							       			<span class="label label-warning">{{tr('pending')}}</span>
							       		@endif
							      	</td>
								    <td>
            							<ul class="admin-action btn btn-default">
            								<li class="dropdown">
								                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
								                  {{tr('action')}} <span class="caret"></span>
								                </a>
								                <ul class="dropdown-menu">
								                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.edit.video' , array('id' => $video->video_id))}}">{{tr('edit_video')}}</a></li>
								                  	<li role="presentation"><a role="menuitem" tabindex="-1" target="_blank" href="{{route('admin.view.video' , array('id' => $video->video_id))}}">{{tr('view_video')}}</a></li>
								                  	@if($video->is_approved)
								                		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.video.decline',$video->video_id)}}">{{tr('decline')}}</a></li>
								                	@else
								                  		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.video.approve',$video->video_id)}}">{{tr('approve')}}</a></li>
								                  	@endif

								                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.delete.video' , array('id' => $video->video_id))}}">{{tr('delete_video')}}</a></li>
								                </ul>
              								</li>
            							</ul>
								    </td>
							    </tr>
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


