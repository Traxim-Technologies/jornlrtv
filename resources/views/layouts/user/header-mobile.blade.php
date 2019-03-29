<div class="col-xs-12 visible-xs">
    <ul class="mobile-header">
        <li><a href="{{route('user.dashboard')}}" class="mobile-menu">
            <i class="material-icons">home</i> 
            <span class="hidden-xxxs">{{tr('home')}}</span>
        </a></li>
        <li><a href="{{route('user.trending')}}" class="mobile-menu">
            <i class="material-icons">whatshot</i>
            <span class="hidden-xxxs">{{tr('trending')}}</span>
        </a></li>
        <li><a href="{{route('user.channel.list')}}" class="mobile-menu">
            <i class="material-icons">live_tv</i>
            <span class="hidden-xxxs">{{tr('channels')}}</span>
        </a></li>
        <li><a href="{{route('user.channel.list')}}" class="mobile-menu">
            <i class="material-icons">movie</i>
            <span class="hidden-xxxs">{{tr('custom_live_videos')}}</span>
        </a></li>
        <li><a href="{{route('user.live_videos')}}" class="mobile-menu">
            <i class="material-icons">videocam</i>
            <span class="hidden-xxxs">Live videos</span>
        </a></li>

        @if(Auth::check())

            @if(Setting::get('create_channel_by_user') == CREATE_CHANNEL_BY_USER_ENABLED || Auth::user()->is_master_user == 1)

                <li><a href="{{route('user.channel.mychannel')}}" class="mobile-menu">
                    <i class="material-icons">subscriptions</i>
                    <span class="hidden-xxxs">{{tr('my_channels')}}</span>
                </a></li>
            @else

                <li>
                    <a href="{{route('user.history')}}" class="mobile-menu">
                        <i class="material-icons">history</i>
                        <span class="hidden-xxxs">{{tr('history')}}</span>
                    </a>
                </li>
            @endif
        @endif

    </ul>
</div>
