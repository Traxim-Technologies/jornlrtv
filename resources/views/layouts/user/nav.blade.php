<div class="y-menu col-sm-3 col-md-2 scroll">
    <ul class="y-home menu1">
        <li id="home">
            <a href="{{route('user.dashboard')}}">
                <img src="{{asset('images/home-grey.png')}}" class="grey-img">
                <img src="{{asset('images/home-red.png')}}" class="red-img">
                <span>{{tr('home')}}</span>
            </a>
        </li>
        <li id="trending">
            <a href="{{route('user.trending')}}">
                <img src="{{asset('images/trending-grey.png')}}" class="grey-img">
                <img src="{{asset('images/trending-red.png')}}" class="red-img">
                <span>{{tr('trending')}}</span>
            </a>
        </li>

         <li id="custom_live_videos">
            <a href="{{route('user.custom.live.index')}}">
                <img src="{{asset('images/video-camera1.png')}}" class="grey-img">
                <img src="{{asset('images/video-camera-red.png')}}" class="red-img">
                <span>{{tr('custom_live_videos')}}</span>
            </a>
        </li>

        <li id="channels">
            <a href="{{route('user.channel.list')}}">
                <img src="{{asset('images/search-grey.png')}}" class="grey-img">
                <img src="{{asset('images/search-red.png')}}" class="red-img">
                <span>{{tr('browse_channels')}}</span>
            </a>
        </li>

        @if(Auth::check())

            <li id="history">
                <a href="{{route('user.history')}}">
                    <img src="{{asset('images/history-grey.png')}}" class="grey-img">
                    <img src="{{asset('images/history-red.png')}}" class="red-img">
                    <span>{{tr('history')}}</span>
                </a>
            </li>
            <li id="settings">
                <a href="/settings">
                    <img src="{{asset('images/settings-grey.png')}}" class="grey-img">
                    <img src="{{asset('images/settings-red.png')}}" class="red-img">
                    <span>{{tr('settings')}}</span>
                </a>
            </li>
            <li id="wishlist">
                <a href="{{route('user.wishlist')}}">
                    <img src="{{asset('images/heart-grey.png')}}" class="grey-img">
                    <img src="{{asset('images/heart-red.png')}}" class="red-img">
                    <span>{{tr('wishlist')}}</span>
                </a>
            </li>
            @if(Setting::get('create_channel_by_user') == CREATE_CHANNEL_BY_USER_ENABLED || Auth::user()->is_master_user == 1)
                <li id="my_channel">
                    <a href="{{route('user.channel.mychannel')}}">
                        <img src="{{asset('images/channel-grey.png')}}" class="grey-img">
                        <img src="{{asset('images/channel-red.png')}}" class="red-img">
                        <span>{{tr('my_channels')}}</span>
                    </a>
                </li>

            @endif
        @endif
    </ul>
                
    @if(count($channels = loadChannels()) > 0)
        
        <ul class="y-home menu1" style="margin-top: 10px;">

            <h3>{{tr('channels')}}</h3>

            @foreach($channels as $channel)
                <li id="channels_{{$channel->id}}">
                    <a href="{{route('user.channel',$channel->id)}}"><img src="{{$channel->picture}}">{{$channel->name}}</a>
                </li>
            @endforeach              
        </ul>

    @endif

    <!-- ============PLAY STORE, APP STORE AND SHARE LINKS======= -->

    @if(Setting::get('appstore') || Setting::get('playstore'))

        <ul class="menu-foot" style="margin-top: 10px;">

            <h3>{{tr('download_our_app')}}</h3>

            @if(Setting::get('playstore'))

            <li>
                <a href="{{Setting::get('playstore')}}" target="_blank">
                    <img src="{{asset('images/google-play.png')}}">
                </a>
            </li>

            @endif

            @if(Setting::get('appstore'))

            <li>
                <a href="{{Setting::get('appstore')}}" target="_blank">
                    <img src="{{asset('images/app_store.png')}}" >
                </a>
            </li>

            @endif

        </ul>

    @endif

    <h3 class="menu-foot-head">{{tr('contact')}}</h3>

    <div class="nav-space">

        <a href="{{Setting::get('facebook_link')}}" target="_blank">
            <span class="fa-stack fa-lg">
                <i class="fa fa-circle fa-stack-2x social-fb"></i>
                <i class="fa fa-facebook fa-stack-1x fa-inverse foot-share2"></i>
            </span>
        </a>

        <a href="{{Setting::get('twitter_link')}}" target="_blank">
            <span class="fa-stack fa-lg">
                <i class="fa fa-circle fa-stack-2x social-twitter"></i>
                <i class="fa fa-twitter fa-stack-1x fa-inverse foot-share2"></i>
            </span>
        </a>

        <a href="{{Setting::get('linkedin_link')}}" target="_blank">
            <span class="fa-stack fa-lg">
                <i class="fa fa-circle fa-stack-2x social-linkedin"></i>
                <i class="fa fa fa-linkedin fa-stack-1x fa-inverse foot-share2"></i>
            </span>
        </a>

        <a href="{{Setting::get('pinterest_link')}}" target="_blank">
            <span class="fa-stack fa-lg">
                <i class="fa fa-circle fa-stack-2x social-pinterest"></i>
                <i class="fa fa fa-pinterest fa-stack-1x fa-inverse foot-share2"></i>
            </span>
        </a>

        <a href="{{Setting::get('google_link')}}" target="_blank">
            <span class="fa-stack fa-lg">
                <i class="fa fa-circle fa-stack-2x social-google"></i>
                <i class="fa fa fa-google fa-stack-1x fa-inverse foot-share2"></i>
            </span>
        </a>

    </div>
    
    @if(Auth::check())

        <!-- Check the create channel options are enabled by admin -->

        <?php /*@if(Setting::get('create_channel_by_user') == CREATE_CHANNEL_BY_USER_ENABLED || Auth::user()->is_master_user == 1)

            <?php $channels = getChannels(Auth::user()->id);?>

            @if(count($channels) > 0 || Auth::user()->user_type)

                <ul class="y-home" style="margin-top: 10px;">
                   

                    <h3>{{tr('my_channels')}}</h3>


                    @foreach($channels as $channel)
                        <li>
                            <a href="{{route('user.channel',$channel->id)}}"><img src="{{$channel->picture}}">{{$channel->name}}</a>
                        </li>
                    @endforeach  


                    @if(Auth::user()->user_type || Auth::user()->is_master_user == 1)  

                        @if(count($channels) == 0 || Setting::get('multi_channel_status') || Auth::user()->is_master_user == 1)  

                        <li>
                            <a href="{{route('user.create_channel')}}"><i class="fa fa-tv fa-2x" style="vertical-align: middle;"></i> {{tr('create_channel')}}</a>
                        </li>    

                        @endif

                    @endif     
                
                </ul>

            @endif
            
        @endif */?>


        @if(!Auth::user()->user_type)

            <div class="menu4 top nav-space">
                <p>{{tr('subscribe_note')}}</p>
                <a href="{{route('user.subscriptions')}}" class="btn btn-sm btn-primary">{{tr('subscribe')}}</a>
            </div> 


        @endif

    @else
        <div class="menu4 top nav-space">
            <p>{{tr('signin_nav_content')}}</p>
            <form method="get" action="{{route('user.login.form')}}">
                <button type="submit">{{tr('login')}}</button>
            </form>
        </div>   
    @endif             
</div>