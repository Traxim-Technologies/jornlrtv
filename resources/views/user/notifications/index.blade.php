@extends('layouts.user')

@section('styles')

@endsection

@section('content')

<div class="y-content">
        
    <div class="row content-row">

        @include('layouts.user.nav')

		<div class="page-inner col-sm-9 col-md-10">

			@include('notification.notify')

			<div class="sub-history">
				<h3 class="no-margin text-left">{{tr('notifications')}}</h3>
			</div>

			<div class="row">

				@foreach($notifications as $notification_details)

					<div class="notification-list-content">

			            <a href="{{$notification_details->notification_redirect_url}}" target="_blank">
			                <div class="row">
			                    <div class="col-lg-2 col-sm-3 col-2 text-center">
			                        <img src="{{$notification_details->picture}}" class="w-50 rounded-circle">
			                    </div>

			                    <div class="col-lg-10 col-sm-8 col-10">
			                        <!-- <strong class="text-info">David John</strong> -->
			                        <div>
			                            {{$notification_details->message}}
			                        </div>
			                        <small class="text-warning">{{$notification_details->created_at}}</small>
			                    </div>
			                </div>

			            </a>

			        </div>
		        
		        @endforeach

	        </div>

		</div>

   	</div>

</div>

@endsection