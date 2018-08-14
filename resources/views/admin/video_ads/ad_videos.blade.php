@extends('layouts.admin')

@section('title', tr('assigned_ads'))

@section('content-header', tr('assigned_ads'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-bullhorn"></i> {{tr('assigned_ads')}}</li>
@endsection

@section('content')

    @include('notification.notify')
	<div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">

          	<div class="box-header label-primary">
                <b style="font-size:18px;">{{tr('assigned_ads')}}</b>
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
						      <th>{{tr('status')}}</th>
						      <th>{{tr('action')}}</th>
						    </tr>
						</thead>
						<tbody>
							@foreach($model as $i => $data)
							    <tr>
							      	<td>{{$i+1}}</td>
							      	<td>{{$data->name}}</td>
							      	<td>{{substr($data->title , 0,25)}}...</td>
							      	
							      	<td>

							      		<?php $types = getTypeOfAds($data->types_of_ad);?>

							      		@foreach($types as $type)
								      		
								      		<span class="label label-success">{{$type}}</span>

								       	@endforeach
							      	</td>
							      	<td>
							      		
							      		@if($data->status)
							      			<span class="label label-success">{{tr('approved')}}</span>
							       		@else
							       			<span class="label label-danger">{{tr('user_disabled')}}</span>
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
                                                        @if(Setting::get('admin_delete_control'))
                                                            <a role="button" href="javascript:;" class="btn disabled" style="text-align: left">{{tr('edit')}}</a>
                                                        @else
                                                            <a role="menuitem" tabindex="-1" href="{{route('admin.video_ads.edit' , array('id' => $data->id))}}">{{tr('edit')}}</a>
                                                        @endif
                                                    </li>
                                                    
								                  	<li role="presentation"><a role="menuitem" tabindex="-1" target="_blank" href="{{route('admin.video-ads.view' , array('id' => $data->id))}}">{{tr('view')}}</a></li
								               
														
								                  	<li class="divider" role="presentation"></li>

								                  	<li role="presentation">
								                  		@if(Setting::get('admin_delete_control'))

									                  	 	<a role="button" href="javascript:;" class="btn disabled" style="text-align: left">{{tr('delete')}}</a>

									                  	@else
								                  			<a role="menuitem" tabindex="-1" onclick="return confirm('Are you sure?')" href="{{route('admin.video-ads.delete' , array('id' => $data->id))}}">{{tr('delete')}}</a>
								                  		@endif
								                  	</li>
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
					<h3 class="no-result">{{tr('no_assigned_ads_found')}}</h3>
				@endif
            </div>
          </div>
        </div>
    </div>

@endsection