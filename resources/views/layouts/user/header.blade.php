@if (!Auth::check()) 

<style type="text/css">
    
.mobile-header li {

    width: 19% !important;

}
</style>


@endif

<div class="header-search" id="search-section">
    <form method="post" action="{{route('search-all')}}" id="userSearch_min">
        <div class="form-group no-margin pull-left width-95">
            <input type="text" id="auto_complete_search_min" name="key" class="auto_complete_search search-query form-control" required placeholder="Search">
        </div>
    </form>

    <a href="#" id="close-btn"><i class="fa fa-close"></i></a>   

    <div class="clear-both"></div>
</div>

<div class="streamtube-top">
<div class="streamtube-nav" id="header-section">

    <div class="row">

        <div class="col-lg-2 col-md-3 col-sm-3 col-xs-6">

            <a href="#" class="hidden-xs"><img src="{{asset('images/fa-fa-bar.png')}}" id="sidehide" class="toggle-icon"></a>

            <a href="#" class="hidden-lg hidden-md hidden-sm"><img src="{{asset('images/menu_white.png')}}" class="toggle-icon"></a>

            <a href="{{route('user.dashboard')}}"> 
                @if(Setting::get('site_logo'))
                    <img src="{{Setting::get('site_logo')}}" class="logo-img">
                @else
                    <img src="{{asset('logo.png')}}" class="logo-img">
                @endif
            </a>

        </div>

        <div class="col-lg-8 col-md-7 col-sm-6 col-xs-12 hidden-xs">

            <div id="custom-search-input" class="">
                <form method="post" action="{{route('search-all')}}" id="userSearch">
                <div class="input-group search-input">
                    
                        <input type="text" id="auto_complete_search" name="key" class="search-query form-control" required placeholder="{{tr('search')}}" />
                        <div class="input-group-btn">
                            <button class="btn btn-danger" type="submit">
                            <i class=" glyphicon glyphicon-search"></i>
                            </button>
                        </div>
                    
                </div>
                </form>
            </div><!--custom-search-input end-->

        </div>

        <!-- ========RESPONSIVE  HEADER VISIBLE IN MOBAILE VIEW====== -->
        
        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12 hidden-xs visible-sm visible-md visible-lg">

            @if(Auth::check())

                <div class="y-button profile-button">

                   <div class="dropdown pull-right">

                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">

                            <img class="profile-image" src="{{Auth::user()->picture ?: asset('placeholder.png')}}">
                        </button>
                        
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">

                            <a href="{{route('user.profile')}}">
                                <div class="display-inline">
                                    <div class="menu-profile-left">
                                        <img src="{{Auth::user()->picture ?: asset('placeholder.png')}}">
                                    </div>
                                    <div class="menu-profile-right">
                                        <h4>{{Auth::user()->name}}</h4>
                                        <p>{{Auth::user()->email}}</p>
                                    </div>
                                </div>
                            </a>

                            <li role="separator" class="divider"></li>

                            <div class="row">

                                <div class="col-xs-6">
                                    <a href="/settings" class="menu-link">
                                        <i class="fa fa-cog"></i>
                                        {{tr('settings')}}
                                    </a>
                                </div>

                                <div class="col-xs-6">
                                    <a href="{{route('user.logout')}}" onclick="return confirm(&quot;{{tr('logout_confirmation')}}&quot;)" class="menu-link">
                                        <i class="fa fa-sign-out"></i>
                                        {{tr('logout')}}
                                    </a>
                                </div>
                            </div>
                           

                        </ul>
                
                    </div>

                </div>

                <ul class="nav navbar-nav pull-right">

                    <li  class="dropdown">
                        <a class="nav-link text-light notification-link" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" onclick="return notificationsStatusUpdate();">
                            <i class="fa fa-bell"></i>
                        </a>

                        <ul class="dropdown-menu-notification dropdown-menu">

                            <li class="notification-head text-light bg-dark">
                                <div class="row">
                                    <div class="col-lg-12 col-sm-12 col-12">
                                        <span>
                                            {{tr('notifications')}} 
                                            (<span id="global-notifications-count">0</span>)
                                        </span>
                                        <!-- <a href="" class="float-right text-light">Mark all as read</a> -->
                                    </div>
                                </div>
                            </li>

                            <span id="global-notifications-box"></span>
                            
                            <li class="notification-footer bg-dark text-center" id="viewAll">
                                <a href="{{route('user.bell_notifications.index')}}" class="text-light">
                                    {{tr('view_all')}}
                                </a>
                            </li>
                        </ul>

                    </li>
                 
                </ul>
                @if(Setting::get('is_direct_upload_button') == YES)

                <a href="{{userChannelId()}}" class="btn pull-right user-upload-btn" title="{{tr('upload_video')}}">
                     {{tr('upload')}} 
                    <i class="fa fa-upload fa-1x"></i>
                </a>

                @endif

            @else
                <div class="y-button header-login-signup-buttons">
                    <a href="{{route('user.login.form')}}" class="y-signin login-header-button">{{tr('login')}} |</a>
					
                    <a href="{{route('user.register.form')}}" class="y-signin sign-up-header-button">Sign up</a>
                </div>

                @if(Setting::get('is_direct_upload_button') == YES)

                    <a href="{{route('user.login.form')}}" class="btn pull-right user-upload-btn" title="{{tr('upload_video')}}"> 
                        {{tr('upload')}} 
                        <i class="fa fa-upload fa-1x"></i>
                    </a>

                @endif

            @endif

            <ul class="nav navbar-nav pull-right" style="margin: 3.5px 0px">

                @if(Setting::get('admin_language_control'))
                    
                    @if(count($languages = getActiveLanguages()) > 1) 
                       
                        <li  class="dropdown">
                    
                            <a href="#" class="dropdown-toggle language-icon" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-globe"></i> <b class="caret"></b></a>

                            <ul class="dropdown-menu languages">

                                @foreach($languages as $h => $language)

                                    <li class="{{(\Session::get('locale') == $language->folder_name) ? 'active' : ''}}" ><a href="{{route('user_session_language', $language->folder_name)}}" style="{{(\Session::get('locale') == $language->folder_name) ? 'background-color: #cc181e' : ''}}">{{$language->folder_name}}</a></li>
                                @endforeach
                                
                            </ul>
                         
                        </li>
                
                    @endif

                @endif

            </ul>

        </div>

        <!-- ======== RESPONSIVE HEADER VISIBLE IN MOBAILE VIEW====== -->

        @include('layouts.user.header-mobile')

        <!-- ======== RESPONSIVE HEADER VISIBLE IN MOBAILE VIEW====== -->

    </div><!--end of row-->


<div class="header-nav-menu">
<div class="row">
<div class="col-sm-2 desktop-menu">
<ul class="y-home menu1 header-nav-menu">
<li id="explore">
            <a href="{{route('user.dashboard')}}">
                <img src="{{asset('images/sidebar/explore-gray.png')}}" class="grey-img">
                <img src="{{asset('images/sidebar/explore-red.png')}}" class="red-img">
                <span>Explore</span>
            </a>
        </li>
		</ul>
</div>
<div class="col-sm-10">
<ul class="y-home menu1 header-nav-menu">
<li id="explore" class="mobile-menu" style="display:none;">
            <a href="{{route('user.dashboard')}}">
                <img src="{{asset('images/sidebar/explore-gray.png')}}" class="grey-img">
                <img src="{{asset('images/sidebar/explore-red.png')}}" class="red-img">
                <span>Explore</span>
            </a>
        </li>
        <li id="home">
            <a href="{{route('user.dashboard')}}">
                <img src="{{asset('images/sidebar/home-grey.png')}}" class="grey-img">
                <img src="{{asset('images/sidebar/home-red.png')}}" class="red-img">
                <span>{{tr('home')}}</span>
            </a>
        </li>
        <li id="trending">
            <a href="{{route('user.trending')}}">
                <img src="{{asset('images/sidebar/trending-grey.png')}}" class="grey-img">
                <img src="{{asset('images/sidebar/trending-red.png')}}" class="red-img">
                <span>{{tr('trending')}}</span>
            </a>
        </li>

        <li id="custom_live_videos">
            <a href="{{route('user.custom_live_videos.index')}}">
                <img src="{{asset('images/sidebar/live-gray.png')}}" class="grey-img">
                <img src="{{asset('images/sidebar/live-red-tv.png')}}" class="red-img">
                <span>{{tr('user_custom_live_videos')}}</span>
            </a>
        </li>
        <li id="live_videos">
            <a href="{{route('user.live_videos')}}">
                <img src="{{asset('images/sidebar/live-video.png')}}" class="grey-img">
                <img src="{{asset('images/sidebar/live-video-active.png')}}" class="red-img">
                <span>{{tr('live_videos')}}</span>
            </a>
        </li>

        </li>

        <!-- <li id="channels"> -->
            <!-- <a href="{{route('user.channel.list')}}"> -->
                <!-- <img src="{{asset('images/sidebar/search-grey.png')}}" class="grey-img"> -->
                <!-- <img src="{{asset('images/sidebar/search-red.png')}}" class="red-img"> -->
                <!-- <span>{{tr('browse_channels')}}</span> -->
            <!-- </a> -->
        <!-- </li> -->

        @if(Auth::check())

            <li id="history">
                <a href="{{route('user.history')}}">
                    <img src="{{asset('images/sidebar/history-grey.png')}}" class="grey-img">
                    <img src="{{asset('images/sidebar/history-red.png')}}" class="red-img">
                    <span>{{tr('history')}}</span>
                </a>
            </li>
            <li id="settings">
                <a href="/settings">
                    <img src="{{asset('images/sidebar/settings-grey.png')}}" class="grey-img">
                    <img src="{{asset('images/sidebar/settings-red.png')}}" class="red-img">
                    <span>{{tr('settings')}}</span>
                </a>
            </li>
            <li id="wishlist">
                <a href="{{route('user.wishlist')}}">
                    <img src="{{asset('images/sidebar/heart-grey.png')}}" class="grey-img">
                    <img src="{{asset('images/sidebar/heart-red.png')}}" class="red-img">
                    <span>{{tr('wishlist')}}</span>
                </a>
            </li>
            @if(Setting::get('create_channel_by_user') == CREATE_CHANNEL_BY_USER_ENABLED || Auth::user()->is_master_user == 1)
                <li id="my_channel">
                    <a href="{{route('user.channel.mychannel')}}">
                        <img src="{{asset('images/sidebar/channel-grey.png')}}" class="grey-img">
                        <img src="{{asset('images/sidebar/channel-red.png')}}" class="red-img">
                        <span>{{tr('my_channels')}}</span>
                    </a>
                </li>

            @endif

            <li id="playlists">
                <a href="{{route('user.playlists.index')}}">
                    <img src="{{asset('images/sidebar/playlist-grey.png')}}" class="grey-img">
                    <img src="{{asset('images/sidebar/playlist-red.png')}}" class="red-img">
                    <span>{{tr('playlists')}}</span>
                </a>
            </li>
    
        @endif
    
    </ul>
	</div>
	</div>
	</div><!--end header-nav-menu-->
	</div>
</div><!--end of streamtube-nav-->
