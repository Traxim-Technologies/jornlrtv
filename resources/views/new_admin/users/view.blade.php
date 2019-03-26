@extends('layouts.admin')

@section('title', tr('view_user'))

@section('content-header') 

	{{ tr('view_user') }} 

@endsection

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i>{{ tr('home') }}</a></li>
    <li><a href="{{ route('admin.users.index') }}"><i class="fa fa-user"></i> {{ tr('users') }}</a></li>
    <li class="active"><i class="fa fa-user"></i> {{ tr('view_user') }}</li>
@endsection

@section('styles')

<style type="text/css">
	.timeline::before {
	    content: '';
	    position: absolute;
	    top: 0;
	    bottom: 0;
	    width: 0;
	    background: #fff;
	    left: 0px;
	    margin: 0;
	    border-radius: 0px;
	}
	.check-redeem {
		color: #FFF !important;
	}

	.nav > li > a:hover, .nav > li > a:active, .nav > li > a:focus {
		color: black !important;
		background-color: green !important;
	}

	.text-ellipsis {
	  white-space: nowrap;
	  overflow: hidden;
	  text-overflow: ellipsis;
	}

	hr {

		margin-top: 10px;
		margin-bottom: 10px;
	}

	.h4-header {
		background-color:#f7f7f7; font-size: 18px; text-align: center; padding: 7px 10px; margin-top: 0;
	}
</style>

@endsection

@section('content')

	<div class="row">
		
		<div class="col-sm-12">

			@include('notification.notify')

		</div>

	    <div class="col-md-3">

	      <!-- Profile Image -->
	      <div class="box box-widget widget-user">

	      	 	<div class="widget-user-header bg-black">
	            	<h3 class="widget-user-username text-capitalize text-ellipsis">{{ $user_details->name }}</h3>
	              	<h5 class="widget-user-desc">{{tr('user')}} 

	              		@if($user_details->user_type)
		      				<span class="text-white"><i class="fa fa-check-circle"></i></span>
		      			@else
		      				<span class="text-white" ><i class="fa fa-times-circle"></i></span>
		      			@endif

	              	</h5>
	            </div>

	            <div class="widget-user-image">
	              	<img class="img-circle" src="{{ $user_details->picture }}" alt="{{ $user_details->name }}" style="height: 90px">
	            </div>

		        <div class="box-body box-profile">

		          <h3 class="profile-username text-center"></h3>

		          <p class="text-muted text-center"></p>

		          <br>

		          <ul class="list-group list-group-unbordered">

		            <li class="list-group-item">
		              	<b>{{ tr('channels') }}</b> <a target="_blank" class="pull-right" href="{{ route('admin.users.channels' ,['user_id' => $user_details->id]) }}">{{ $user_details->get_channel_count }}</a>
		            </li>

		            <li class="list-group-item">
		              	<b>{{ tr('videos') }}</b> <a class="pull-right" target="_blank" href="{{ route('admin.video_tapes.index' ,['user_id' => $user_details->id]) }}">{{ $user_details->get_channel_videos_count }}</a>
		            </li>

		            <li class="list-group-item">
		              	<b>{{ tr('wishlists') }}</b> <a href="{{ route('admin.users.wishlist',['user_id' => $user_details->id]) }}" class="pull-right" target="_blank">{{ $user_details->user_wishlist_count }}</a>
		            </li>

		            <li class="list-group-item">
		              	<b>{{ tr('histories') }}</b> <a href="{{ route('admin.users.history',['user_id' => $user_details->id]) }}" class="pull-right" target="_blank">{{ $user_details->user_history_count }}</a>
		            </li>
		            
		             <li class="list-group-item">
		              	<b>{{ tr('reviews') }}</b> <a href="{{ route('admin.reviews',['user_id' => $user_details->id]) }}" class="pull-right" target="_blank">{{ $user_details->user_rating_count }}</a>
		            </li>
		            
		            <li class="list-group-item">
		              	<b>{{ tr('spam_reports') }}</b> <a href="{{ route('admin.users.history',['user_id' => $user_details->id]) }}" class="pull-right" target="_blank">{{ $user_details->user_flag_count }}</a>
		            </li>

		            <li class="list-group-item">
		              	<b>{{ tr('status') }}</b> <a class="pull-right">
		              	@if($user_details->status == USER_APPROVED) 
			      			<span class="label label-success">{{ tr('approved') }}</span>
			       		@else 
			       			<span class="label label-warning">{{ tr('pending') }}</span>
			       		@endif
		              </a>
		            </li>

		            <li class="list-group-item">
		            
		              <b>{{ tr('is_verified') }}</b> <a class="pull-right">
		              	@if(!$user_details->is_verified == USER_EMAIL_VERIFIED) 

			      			<a href="{{ route('admin.users.verify' ,['user_id' => $user_details->id]) }}" class="btn btn-xs btn-warning pull-right">{{ tr('verify') }}</a>

			       		@else 
			       			<span class="label label-success">{{ tr('verified') }}</span>
			       		@endif
		              </a>
		            </li>

		          </ul>
		          <center>

		          	@if(Setting::get('admin_delete_control') == YES )

		          		<a href="{{ route('admin.users.edit', ['user_id' => $user_details->id] ) }}" class="btn btn-warning" title="{{tr('edit')}}"><b><i class="fa fa-edit"></i></b></a>

               			@if(get_expiry_days($user_details->id) > 0)
                  	 		<a onclick="return confirm(&quot;{{tr('admin_user_delete_with_expiry_days_confirmation', get_expiry_days($user_details->id) ) }} &quot;);" href="javascript:;" class="btn btn-danger" title="{{tr('delete')}}"><b><i class="fa fa-trash"></i></b></a>	
                  		@else 
                  			<a onclick="return confirm(&quot;{{ tr('admin_premium_user_delete_confirmation', $user_details->name ) }}&quot;);" href="javascript:;" class="btn btn-danger" title="{{tr('delete')}}"><b><i class="fa fa-trash"></i></b>
                  			</a>
                  	 	@endif

               		@else
               			<a href="{{ route('admin.users.edit' , ['user_id' => $user_details->id] ) }}" class="btn btn-warning" title="{{tr('edit')}}"><b><i class="fa fa-edit"></i></b></a>	
                  		
                  	 	@if(get_expiry_days($user_details->id) > 0)
                  	 	
                  	 		<a onclick="return confirm(&quot;{{ tr('admin_user_delete_with_expiry_days_confirmation' ) }}&quot;);" href="{{ route('admin.users.delete', ['user_id' => $user_details->id] ) }}" class="btn btn-danger" title="{{tr('delete')}}"> <b><i class="fa fa-trash"></i></b> </a>	
                  		@else                   			
                  	 		<a onclick="return confirm(&quot;{{ tr('admin_premium_user_delete_confirmation', $user_details->name ) }}&quot;);" href="{{ route('admin.users.delete', ['user_id' => $user_details->id] ) }}" class="btn btn-danger" title="{{tr('delete')}}"><b><i class="fa fa-trash"></i></b>
                  			</a>
                  	 	@endif

                  	@endif

		          	@if($user_details->status == USER_APPROVED)		
	              		<a href="{{ route('admin.users.status', ['user_id' => $user_details->id] ) }}" onclick="return confirm(&quot;{{ tr('admin_user_decline_confirmation',$user_details->name) }}&quot;)" class="btn btn-warning" title="{{tr('decline')}}" ><i class="fa fa-close"></i></a>	              	
	              	@else	              	
	              		<a href="{{ route('admin.users.status',['user_id' => $user_details->id] ) }}" onclick="return confirm(&quot;{{ tr('admin_user_approve_confirmation', $user_details->name) }}&quot;)" class="btn btn-success" title="{{tr('approve')}}"><i class="fa fa-check"></i></a>
	              	@endif

	              	<a  href="{{ route('admin.users.playlist.index' , ['user_id' => $user_details->id] ) }}" class="btn btn-info" title="{{tr('playlist')}}"><i class="fa fa-video-camera"></i></a>

	              	</center>
		        </div>
		        <!-- /.box-body -->
	      </div>
	      <!-- /.box -->

	      <!-- About Me Box -->
	      <div class="box box-primary">

	        <div class="box-header with-border">
	          	<h3 class="box-title">{{ tr('about_me') }}</h3>
	        </div>
	        <!-- /.box-header -->
	        <div class="box-body">

	          	<strong><i class="fa fa-map-marker margin-r-5"></i> {{ tr('location') }}</strong>
	         	<p class="text-muted">{{ $user_details->timezone }}</p>
	          	<hr>	         

	            <p><strong><i class="fa fa-bell margin-r-5"></i> {{ tr('validity_days') }}</strong></p>
                <p style="color:#cc181e">The Pack will Expiry within <b>{{ get_expiry_days($user_details->id)['days'] }} days</b></p>
           

            	<p><a target="_blank" href="{{ route('admin.users.subscriptions.plans' ,['user_id' => $user_details->id]) }}" class="btn btn-xs btn-success"><i class="fa fa-hand-pointer-o"></i>&nbsp;{{ tr('subscribe') }}</a></p>

	        </div>
	        <!-- /.box-body -->
	      </div>
	      <!-- /.box -->
	    </div>
	    <!-- /.col -->
	    <div class="col-md-9">

	      	<div class="nav-tabs-custom">
		        <ul class="nav nav-tabs">
		          	<li class="active"><a href="#activity" data-toggle="tab" aria-expanded="true">{{ tr('profile') }}</a></li>
		          	
		          	<li class=""><a href="#channels_id" data-toggle="tab" aria-expanded="false">{{ tr('channels') }}</a></li>
		          
		          	<li class=""><a href="#wishlist_list" data-toggle="tab" aria-expanded="false">{{ tr('favourites') }}</a></li>
		          
		          	<li class=""><a href="#history_list" data-toggle="tab" aria-expanded="false">{{ tr('history') }}</a></li>
		          
		          	<li class=""><a href="#reviews_list" data-toggle="tab" aria-expanded="false">{{ tr('reviews') }}</a></li>
		          
		          	<li class=""><a href="#spam_reports_list" data-toggle="tab" aria-expanded="false">{{ tr('spam_reports') }}</a></li>
		        </ul>

		        <div class="tab-content">

		          	<div class="tab-pane active" id="activity">

		          		<h4 class="h4-header"><b>{{ tr('personal_info') }}</b></h4>

		            	<table class="table table-striped">
			            	<tr>
			            		<th>{{ tr('username') }}</th>
			            		<td>{{ $user_details->name }}</td>
			            	</tr>
			            	<tr>
			            		<th>{{ tr('email') }}</th>
			            		<td>{{ $user_details->email }}</td>
			            	</tr>
			            	<tr>
			            		<th>{{ tr('dob') }}</th>
			            		<td>{{ date('d-m-Y', strtotime($user_details->dob)) }}</td>
			            	</tr>
			            	
			            	<tr>
			            		<th>{{ tr('mobile') }}</th>
			            		<td>{{ $user_details->mobile }}</td>
			            	</tr>

			            	<tr>
			            		<th>{{ tr('device_type') }}</th>
			            		<td>{{ $user_details->device_type }}</td>
			            	</tr>

			            	<tr>
			            		<th>{{ tr('register_type') }}</th>
			            		<td>{{ $user_details->register_type }}</td>
			            	</tr>

			            	<tr>
			            		<th>{{ tr('login_type') }}</th>
			            		<td>{{ $user_details->login_type }}</td>
			            	</tr>

			            	<tr>
			            		<th>{{ tr('description')}}</th>
			            		<td><?php echo $user_details->description ?></td>
			            	</tr>
			            	
		            	</table>

		            	<h4 class="h4-header"><b>{{ tr('redeems') }}</b></h4>
	                	
	                	<table class="table table-striped">
			            	<tr>
			            		<th>{{ tr('total_earning') }}</th>
			            		<td>{{ Setting::get('currency') }} {{ $user_details->userRedeem ? number_format_short($user_details->userRedeem->total) : "0.00" }}</td>
			            	</tr>
			            	<tr>
			            		<th>{{ tr('wallet_balance') }}</th>
			            		<td>{{ Setting::get('currency') }} {{ $user_details->userRedeem ? number_format_short($user_details->userRedeem->remaining) : "0.00" }}</td>
			            	</tr>
			            	<tr>
			            		<th>{{ tr('paid_amount') }}</th>
			            		<td>{{ Setting::get('currency') }} {{ $user_details->userRedeem ? number_format_short($user_details->userRedeem->paid) : "0.00" }}</td>
			            	</tr>
			            	<tr>
				            	<td>
				            		<a target="_blank" href="{{ route('admin.users.redeems' ,['user_id' => $user_details->id]) }}" class="btn btn-success check-redeem" style="background-color: #00a65a !important; color: #fff !important" >	
					                	{{ tr('check_redeem_requests') }}
					                </a>
				            	</td>
			            	</tr>
		            	</table>

		          	</div>
		            <!-- /.tab-pane -->

		          	<div class="tab-pane" id="channels_id">

			          	<blockquote>
			                <p>{{ tr('channels_short_notes') }}</p>
			                <cite>
			                <a href="{{ route('admin.users.channels',['user_id' => $user_details->id]) }}" target="_blank">{{ tr('to_view_more') }}</a></cite></small>
			            </blockquote>

		          		<div class="row">

		          		@if(count($channels) > 0)

			          		@foreach($channels as $channel_details)

				            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">
					          <!-- Widget: user widget style 1 -->
					          	<div class="box box-widget widget-user">
						            <!-- Add the bg color to the header using any of the bg-* classes -->

						            <div class="widget-user-header bg-black" style="background: url('{{ $channel_details->cover }}') center center;">
						            	<h3 class="widget-user-username">{{ $channel_details->channel_name }}</h3>
						              	<h5 class="widget-user-desc">{{ tr('channel') }}</h5>
						            </div>

						            <div class="widget-user-image">
						              	<img class="img-circle" src="{{ $channel_details->picture }}" alt="{{ $channel_details->channel_name }}" style="height: 90px">
						            </div>

						            <div class="box-footer">
						              	<div class="row">
							                <div class="col-sm-4 border-right">
							                  <div class="description-block">
							                    <h5 class="description-header"><a href="{{ route('admin.channels.videos', ['channel_id' => $channel_details->channel_id] ) }}">{{ $channel_details->videos }}</a></h5>
							                    <span class="description-text">{{ tr('videos') }}</span>
							                  </div>
							                  <!-- /.description-block -->
							                </div>
							                <!-- /.col -->
							                <div class="col-sm-4 border-right">
							                    <div class="description-block">
								                    <h5 class="description-header"><a href="{{ route('admin.channels.subscribers', ['channel_id'=> $channel_details->channel_id] ) }}"> {{ $channel_details->subscribers }}</a></h5>
								                    <span class="description-text">{{ tr('subscribers') }}</span>
							                    </div>
							                  <!-- /.description-block -->
							                </div>
							                <!-- /.col -->
							                <div class="col-sm-4">
								                <div class="description-block">
								                    <h5 class="description-header">{{ $channel_details->currency }}{{ number_format_short($channel_details->earnings) }}</h5>
								                    <span class="description-text">{{ tr('earnings') }}</span>
								                </div>
							                  <!-- /.description-block -->
							                </div>
						                <!-- /.col -->
						              	</div>
						              <!-- /.row -->
						            </div>

					          	</div>
					          <!-- /.widget-user -->
					        </div>

					        @endforeach

				        @else

				        	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
				        		<p class="text-center">{{ tr('no_channels_found') }}</p>
				        	</div>

				        @endif
				     </div>
		          	</div> 
		          	<!-- /.tab-pane -->

		         	<div class="tab-pane" id="wishlist_list">

		          		<blockquote>
			                <p>{{ tr('favourites_notes') }}</p>
			                <cite><a href="{{ route('admin.users.wishlist',['user_id' => $user_details->id]) }}" target="_blank">{{ tr('to_view_more') }}</a>
			                </cite></small>
			            </blockquote>

		           		<table id="datatable-withoutpagination" class="table table-bordered table-striped">
							
							<thead>
							    <tr>
							        <th>{{ tr('id') }}</th>
							        <th>{{ tr('video') }}</th>
							        <th>{{ tr('date') }}</th>
							        <th>{{ tr('action') }}</th>
							    </tr>
							</thead>

							<tbody>

								@foreach($wishlists as $i => $wishlist_details)

								    <tr>
								      	<td>{{ $i+1 }}</td>

								      	<td><a href="{{route('admin.video_tapes.view' , ['id' => $wishlist_details->video_tape_id] )}}"> {{ $wishlist_details->title }}</a></td>

								      	<td>{{ $wishlist_details->created_at->diffForHumans() }}</td>

									    <td>
									        <a class="btn btn-sm btn-danger" onclick="return confirm(&quot;{{ tr('admin_user_wishlist_delete_confirm',$wishlist_details->title) }}&quot;);" href="{{ route('admin.users.wishlist.delete' ,['wishlist_id' => $wishlist_details->id] ) }}"><i class="fa fa-trash"></i></a>		
									    </td>
								    </tr>					

								@endforeach

							</tbody>

						</table>

		          	</div>

		          	<div class="tab-pane" id="history_list">

		          		<blockquote>
			                <p>{{ tr('history_notes') }}</p>
			                <cite>
			                <a target="_blank" href="{{ route('admin.users.history',['user_id' => $user_details->id]) }}">{{ tr('to_view_more') }}</a>
			                </cite></small>
			            </blockquote>

		           		<table id="datatable-withoutpagination1" class="table table-bordered table-striped">
							<thead>
							    <tr>
							      <th>{{ tr('id') }}</th>
							      <th>{{ tr('video') }}</th>
							      <th>{{ tr('date') }}</th>
							      <th>{{ tr('action') }}</th>
							    </tr>
							</thead>

							<tbody>

								@foreach($histories as $i => $history_details)

								    <tr>
								      	<td>{{ $i+1 }}</td>

								      	<td>
								      		<a href="{{route('admin.video_tapes.view' , ['id' => $history_details->video_tape_id] )}}"> {{ $history_details->title }} </a> 
								      	</td>

								      	<td>{{ $history_details->created_at->diffForHumans() }}</td>

									    <td>
									        <a class="btn btn-sm btn-danger" onclick="return confirm(&quot;{{ tr('admin_user_history_delete_confirm', $history_details->title) }}&quot;);" href="{{ route('admin.users.history.delete' ,['history_id' => $history_details->id] ) }}"><i class="fa fa-trash"></i></a>   
									    </td>

								    </tr>					

								@endforeach

							</tbody>

						</table>

		          	</div>

		          	<div class="tab-pane" id="spam_reports_list">

		          		<blockquote>
			                <p>{{ tr('spam_reports_notes') }}</p>
			                <cite><a href="{{ route('admin.spam-videos.per-user-reports',['user_id' => $user_details->id]) }}" target="_blank">{{ tr('to_view_more') }}</a>
			                </cite></small>
			            </blockquote>

		           		<table id="datatable-withoutpagination" class="table table-bordered table-striped">

							<thead>
							    <tr>
							      <th>{{ tr('id') }}</th>
							      <th>{{ tr('video') }}</th>
							      <th>{{ tr('reason') }}</th>
							      <th>{{ tr('date') }}</th>
							      <th>{{ tr('action') }}</th>
							    </tr>
							</thead>

							<tbody>

								@foreach($spam_reports as $i => $spam_report_details)

								    <tr>
								      	<td>{{ $i+1 }}</td>
								      	
								      	<td>
							      			<a href="{{route('admin.videos.view' , ['id' => $spam_report_details->video_tape_id] )}}"> {{substr($spam_report_details->title , 0,25)}}...</a>
							      		</td>

								      	<td>{{ $spam_report_details->reason }}</td>
								      	
								      	<td>{{ $spam_report_details->created_at->diffForHumans() }}</td>
									   
									    <td>	            							
									        <a class="btn btn-sm btn-danger" onclick="return confirm(&quot; {{ tr('admin_user_review_delete_confirmation') }}&quot;);"  href="{{ route('admin.spam-videos.unspam-video' , $spam_report_details->id) }}"><i class="fa fa-trash"></i></a>		
									    </td>

								    </tr>					

								@endforeach

							</tbody>

						</table>

		          	</div>

		            <div class="tab-pane" id="reviews_list">

		          		<blockquote>
			                <p>{{ tr('reviews_notes_list') }}</p>
			                <cite><a href="{{ route('admin.reviews', array('user_id'=>$user_details->id)) }}" target="_blank">{{ tr('to_view_more') }}</a>
			                </cite></small>
			            </blockquote>

		           		<table id="datatable-withoutpagination" class="table table-bordered table-striped">
							
							<thead>
							    <tr>
							      <th>{{ tr('id') }}</th>
							      <th>{{ tr('video') }}</th>
							      <th>{{ tr('comments') }}</th>
							      <th>{{ tr('date') }}</th>
							      <th>{{ tr('action') }}</th>
							    </tr>
							</thead>

							<tbody>

								@foreach($user_ratings as $i => $user_rating_details)

								    <tr>
								      	<td>{{ $i+1 }}</td>

								      	<td>
								      		<a href="{{route('admin.videos.view' , ['id' => $user_rating_details->video_tape_id] )}}">{{ $user_rating_details->title }}
								      		</a>
								      	</td>

								      	<td>{{ $user_rating_details->comment }}</td>

								      	<td>{{ $user_rating_details->created_at->diffForHumans() }}</td>

									    <td>	            							
									        <a class="btn btn-sm btn-danger" onclick="return confirm(&quot; {{ tr('admin_user_review_delete_confirmation') }}&quot;);" href="{{ route('admin.reviews.delete' , ['user_rating_id'=>$user_rating_details->id] ) }}"><i class="fa fa-trash"></i></a>
									    </td>

								    </tr>					

								@endforeach

							</tbody>

						</table>

		            </div>

		          <!-- /.tab-pane -->
		        </div>
		        <!-- /.tab-content -->
	      	</div>
	      	<!-- /.nav-tabs-custom -->

	    </div>
	        <!-- /.col -->
    </div>

@endsection


@section('scripts')

<script type="text/javascript">
$("#datatable-withoutpagination").DataTable({
     "paging": false,
     "lengthChange": false,
     "searching": false,
     "language": {
           "info": ""
    }
});
$("#datatable-withoutpagination1").DataTable({
     "paging": false,
     "lengthChange": false,
     "searching": false,
     "language": {
           "info": ""
    }
});
</script>
@endsection