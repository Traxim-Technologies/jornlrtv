@extends('layouts.admin')

@section('title', tr('playlist'))

@section('content-header') 

{{ tr('playlist') }} 

@endsection

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i>{{ tr('home') }}</a></li>
    <li class="active"><i class="fa fa-user"></i> {{ tr('playlist') }}</li>
@endsection

@section('content')

	<div class="row">

        <div class="col-xs-12">
			
			@include('notification.notify')

          	<div class="box box-primary">

	          	<div class="box-header label-primary">
	                <b style="font-size:18px;">{{ tr('playlist') }}</b>
	            </div>

	            <div class="box-body table-responsive">

	            	@if(count($playlists) > 0)

		              	<table id="example1" class="table table-bordered table-striped">

							<thead>

							    <tr>

							      	<th>{{ tr('id') }}</th>

							      	<th>{{ tr('channel') }}</th>

							      	<th>{{ tr('playlist') }}</th>

							      	<th>{{ tr('total_videos') }}</th>
							      
							      	<th>{{ tr('action') }}</th>

							    </tr>

							</thead>

							<tbody>

								@foreach($playlists as $i => $playlist_details)

								    <tr>
								      	<td>{{ $i+1 }}</td>

								      	<td>
								      		@if($playlist_details->channel_name)
								      			<a href="{{ route('admin.channels.view' ,  ['channel_id' => $playlist_details->channel_id] ) }}">{{ $playlist_details->channel_name}}</a>
								      		@else
								      			-
								      		@endif
								      	</td>

								      	<td>{{ $playlist_details->title }}</td>

								 		<td>
								 			<a href="{{ route('admin.users.playlist.view' ,  ['playlist_id' => $playlist_details->playlist_id] ) }}"> {{ $playlist_details->total_videos }}</a>
								 			</td>								      	
								      	<td>

										@if(Setting::get('admin_delete_control') == YES )

          									<a href="javascript:;" onclick="return confirm(&quot;{{ tr('admin_user_playlist_delete_confirmation',$playlist_details->title) }}&quot;)" class="btn btn-danger" title="{{tr('decline')}}" ><b><i class="fa fa-trash"></i></b> 
          									</a>	

          								@else

											<a href="{{ route('admin.users.playlist.delete', ['playlist_id' => $playlist_details->playlist_id] ) }}" onclick="return confirm(&quot;{{ tr('admin_user_playlist_delete_confirmation',$playlist_details->title) }}&quot;)" class="btn btn-danger" title="{{tr('decline')}}" ><b><i class="fa fa-trash"></i></b> 
          									</a>

          								@endif
								      	</td>

								    </tr>

								@endforeach

							</tbody>

						</table>

					@else
						<h3 class="no-result">{{ tr('no_playlist_found') }}</h3>
					@endif

	            </div>

          	</div>

        </div>

    </div>

@endsection
