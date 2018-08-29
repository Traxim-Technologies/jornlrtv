@extends( 'layouts.user' )

@section('content')

<div class="y-content">

	<div class="row content-row">

		@include('layouts.user.nav')

		<div class="page-inner col-sm-9 col-md-10">

			@include('notification.notify')

			<div class="new-history">

                <div><h4 class="settings">{{tr('settings')}}</h4></div>

                <div class="row">
                	<div class="col-sm-6 col-md-4 col-lg-4">
                		<div class="settings-card carg-lg">
                			<div class="text-center">
                				<img src="{{asset('images/bg-blue2.jpg')}}" class="settings-card-img">
                				<h4 class="settings-head">user</h4>
                				<p class="settings-subhead">user@streamtube.com</p>
                				<a href="{{route('user.profile')}}" class="settings-link">view profile</a>
                			</div>
                		</div>
                	</div>
                	<div class="col-sm-6 col-md-4 col-lg-4">
                		<a href="{{route('user.subscriptions')}}">
	                		<div class="settings-card">
	                			<div class="display-inline">
	                				<div class="settings-left">
	                					<img src="{{asset('images/subscription.png')}}" class="settings-icon">
	                				</div>
	                				<div class="settings-right">
	                					<h4 class="settings-head">subscription</h4>
	                					<p class="settings-subhead">5 plans</p>
	                				</div>
	                			</div>
	                		</div>
                		</a>
                		<a href="#">
	                		<div class="settings-card">
	                			<div class="display-inline">
	                				<div class="settings-left">
	                					<img src="{{asset('images/subscription-history.png')}}" class="settings-icon">
	                				</div>
	                				<div class="settings-right">
	                					<h4 class="settings-head">my subscription</h4>
	                					<p class="settings-subhead">valid upto <span>18 Mar 2018</span></p>
	                				</div>
	                			</div>
	                		</div>
                		</a>
                	</div>
                	<div class="col-sm-6 col-md-4 col-lg-4">
                		<a href="{{route('user.wishlist')}}">
	                		<div class="settings-card">
	                			<div class="display-inline">
	                				<div class="settings-left">
	                					<img src="{{asset('images/heart.png')}}" class="settings-icon">
	                				</div>
	                				<div class="settings-right">
	                					<h4 class="settings-head">wishlist</h4>
	                					<p class="settings-subhead">15 videos</p>
	                				</div>
	                			</div>
	                		</div>
                		</a>
                		<a href="{{route('user.spam-videos')}}">
	                		<div class="settings-card">
	                			<div class="display-inline">
	                				<div class="settings-left">
	                					<img src="{{asset('images/spam.png')}}" class="settings-icon">
	                				</div>
	                				<div class="settings-right">
	                					<h4 class="settings-head">spam</h4>
	                					<p class="settings-subhead">10 videos</p>
	                				</div>
	                			</div>
	                		</div>
                		</a>
                	</div>
                	<div class="col-sm-6 col-md-4 col-lg-4">
                		<a href="{{route('user.channels.subscribed')}}">
	                		<div class="settings-card">
	                			<div class="display-inline">
	                				<div class="settings-left">
	                					<img src="{{asset('images/computer.png')}}" class="settings-icon">
	                				</div>
	                				<div class="settings-right">
	                					<h4 class="settings-head">subscribed channel</h4>
	                					<p class="settings-subhead">1 channel</p>
	                				</div>
	                			</div>
	                		</div>
                		</a>
                	</div>
                	<div class="col-sm-6 col-md-4 col-lg-4">
                		<a href="{{route('user.history')}}">
	                		<div class="settings-card">
	                			<div class="display-inline">
	                				<div class="settings-left">
	                					<img src="{{asset('images/history1.png')}}" class="settings-icon">
	                				</div>
	                				<div class="settings-right">
	                					<h4 class="settings-head">history</h4>
	                					<p class="settings-subhead">15 videos</p>
	                				</div>
	                			</div>
	                		</div>
                		</a>
                	</div>
                	<div class="col-sm-6 col-md-4 col-lg-4">
                		<a href="{{route('user.ppv.history')}}">
	                		<div class="settings-card">
	                			<div class="display-inline">
	                				<div class="settings-left">
	                					<img src="{{asset('images/dollar.png')}}" class="settings-icon">
	                				</div>
	                				<div class="settings-right">
	                					<h4 class="settings-head">PPV history</h4>
	                					<p class="settings-subhead">15 videos</p>
	                				</div>
	                			</div>
	                		</div>
                		</a>
                	</div>
                	<div class="col-sm-6 col-md-4 col-lg-4">
                		<a href="{{route('user.card.card_details')}}">
	                		<div class="settings-card">
	                			<div class="display-inline">
	                				<div class="settings-left">
	                					<img src="{{asset('images/card.png')}}" class="settings-icon">
	                				</div>
	                				<div class="settings-right">
	                					<h4 class="settings-head">cards</h4>
	                					<p class="settings-subhead">2 cards added</p>
	                				</div>
	                			</div>
	                		</div>
                		</a>
                	</div>
                	<div class="col-sm-6 col-md-4 col-lg-4">
                		<a href="{{route('user.redeems')}}">
	                		<div class="settings-card">
	                			<div class="display-inline">
	                				<div class="settings-left">
	                					<img src="{{asset('images/redeems.png')}}" class="settings-icon">
	                				</div>
	                				<div class="settings-right">
	                					<h4 class="settings-head">redeem</h4>
	                					<p class="settings-subhead">total amount $120</p>
	                				</div>
	                			</div>
	                		</div>
                		</a>
                	</div>
                	<div class="col-sm-6 col-md-4 col-lg-4">
                		<a href="{{route('user.change.password')}}">
	                		<div class="settings-card">
	                			<div class="display-inline">
	                				<div class="settings-left">
	                					<img src="{{asset('images/change.png')}}" class="settings-icon">
	                				</div>
	                				<div class="settings-right">
	                					<h4 class="settings-head settings-mt-1">change password</h4>
	                				</div>
	                			</div>
	                		</div>
                		</a>
                	</div>
                	<div class="col-sm-6 col-md-4 col-lg-4">
                		<a href="{{route('user.delete.account')}}" @if(Auth::user()->login_by != 'manual') onclick="return confirm('Are you sure? . Once you deleted account, you will lose your history and wishlist details.')" @endif>
	                		<div class="settings-card">
	                			<div class="display-inline">
	                				<div class="settings-left">
	                					<img src="{{asset('images/trash.png')}}" class="settings-icon">
	                				</div>
	                				<div class="settings-right">
	                					<h4 class="settings-head settings-mt-1">delete account</h4>
	                				</div>
	                			</div>
	                		</div>
                		</a>
                	</div>
                	<div class="col-sm-6 col-md-4 col-lg-4">
                		<a href="{{route('user.logout')}}">
	                		<div class="settings-card">
	                			<div class="display-inline">
	                				<div class="settings-left">
	                					<img src="{{asset('images/logout.png')}}" class="settings-icon">
	                				</div>
	                				<div class="settings-right">
	                					<h4 class="settings-head settings-mt-1">logout</h4>
	                				</div>
	                			</div>
	                		</div>
                		</a>
                	</div>
                </div>

            </div>

		</div>

	</div>

</div>

@endsection