@extends('layouts.admin')

@section('title', tr('channels'))

@section('content-header')

@if(isset($user)) <span class="text-green"> {{$user->name}} </span>- @endif {{tr('channels')}}

@endsection

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-suitcase"></i> {{tr('channels')}}</li>
@endsection

@section('content')

	@include('notification.notify')

	<div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">
          	<div class="box-header label-primary">
                <b style="font-size:18px;">{{tr('channels')}}</b>
                <a href="{{route('admin.add.channel')}}" class="btn btn-default pull-right">{{tr('add_channel')}}</a>

                <!-- EXPORT OPTION START -->

					@if(count($channels) > 0 )
	                
		                <ul class="admin-action btn btn-default pull-right" style="margin-right: 20px">
		                 	
							<li class="dropdown">
				                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
				                  {{tr('export')}} <span class="caret"></span>
				                </a>
				                <ul class="dropdown-menu">
				                  	<li role="presentation">
				                  		<a role="menuitem" tabindex="-1" href="{{route('admin.channels.export' , ['format' => 'xls'])}}">
				                  			<span class="text-red"><b>{{tr('excel_sheet')}}</b></span>
				                  		</a>
				                  	</li>

				                  	<li role="presentation">
				                  		<a role="menuitem" tabindex="-1" href="{{route('admin.channels.export' , ['format' => 'csv'])}}">
				                  			<span class="text-blue"><b>{{tr('csv')}}</b></span>
				                  		</a>
				                  	</li>
				                </ul>
							</li>
						</ul>

					@endif

	            <!-- EXPORT OPTION END -->

            </div>
            <div class="box-body">

            	@if(count($channels) > 0)

	              	<table id="example1" class="table table-bordered table-striped">

						<thead>
						    <tr>
						      <th>{{tr('id')}}</th>
						      <th>{{tr('channel')}}</th>
						      <th>{{tr('user_name')}}</th>
						      <th>{{tr('picture')}}</th>
						      <th>{{tr('cover')}}</th>
						      <th>{{tr('subscribers')}}</th>
						      <th>{{tr('amount')}}</th>
						      <th>{{tr('status')}}</th>
						      <th>{{tr('action')}}</th>
						    </tr>
						</thead>

						<tbody>
							@foreach($channels as $i => $channel)

							    <tr>
							      	<td>{{$i+1}}</td>
							      	<td><a href="{{route('admin.channel.videos', $channel->id)}}">{{$channel->name}}</a></td>
							      	<td>{{$channel->getUser ? $channel->getUser->name : ''}}</td>
							      	<td>
	                                	<img style="height: 30px;" src="{{$channel->picture}}">
	                            	</td>

	                            	<td>
	                                	<img style="height: 30px;" src="{{$channel->cover}}">
	                            	</td>

	                            	<td><a href="{{route('admin.subscribers', array('id'=> $channel->id))}}">{{$channel->getChannelSubscribers()->count()}}</a></td>

	                            	<td>{{Setting::get('currency')}} {{getAmountBasedChannel($channel->id)}}</td>

								    <td>
								      		@if($channel->is_approved)
								      			<span class="label label-success">{{tr('approved')}}</span>
								       		@else
								       			<span class="label label-warning">{{tr('pending')}}</span>
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
                                                            <a role="menuitem" tabindex="-1" href="{{route('admin.edit.channel' , array('id' => $channel->id))}}">{{tr('edit')}}</a>
                                                        @endif
                                                    </li>

													<li class="divider" role="presentation"></li>


													<li role="presentation">
                                                       
                                                        <a role="menuitem" tabindex="-1" href="{{route('admin.channel.videos', $channel->id)}}">{{tr('videos')}}</a>

                                                    </li>

                                                    <li role="presentation"><a href="{{route('admin.subscribers', array('id'=> $channel->id))}}">{{tr('subscribers')}}</a></li>

													<li class="divider" role="presentation"></li>

								                  	<li role="presentation">

									                  	@if(Setting::get('admin_delete_control'))

										                  	<a role="button" href="javascript:;" class="btn disabled" style="text-align: left">{{tr('delete')}}</a>

										                @else

								                  			<a role="menuitem" tabindex="-1" onclick="return confirm('Are you sure?')" href="{{route('admin.delete.channel' , array('channel_id' => $channel->id))}}">{{tr('delete')}}</a>
								                  		@endif
								                  	</li>

													<li class="divider" role="presentation"></li>

								                  	@if($channel->is_approved)
								                  		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.channel.approve' , array('id' => $channel->id , 'status' =>0))}}">{{tr('decline')}}</a></li>
								                  	@else
								                  		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.channel.approve' , array('id' => $channel->id , 'status' => 1))}}">{{tr('approve')}}</a></li>
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
					<h3 class="no-result">{{tr('no_result_found')}}</h3>
				@endif
            </div>
          </div>
        </div>
    </div>

@endsection
