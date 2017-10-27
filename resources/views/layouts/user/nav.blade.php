<div class="y-menu col-sm-3 col-md-2">
    <ul class="y-home menu1">
        <li id="home">
            <a href="{{route('user.dashboard')}}">
                <img src="{{asset('streamtube/images/y1.jpg')}}">{{tr('home')}}
            </a>
        </li>
        <li id="trending">
            <a href="{{route('user.trending')}}">
                <img src="{{asset('streamtube/images/y10.png')}}">{{tr('trending')}}
            </a>
        </li>
    </ul>
                
    @if(count($channels = loadChannels()) > 0)
        
        <ul class="y-home" style="margin-top: 10px;">


            <a href="{{route('user.channel.list')}}" title="Click Here To View Channels"><h3>{{tr('channels')}}</h3></a>

            @foreach($channels as $channel)
                <li>
                    <a href="{{route('user.channel',$channel->id)}}"><img src="{{$channel->picture}}">{{$channel->name}}</a>
                </li>
            @endforeach              
        </ul>

    @endif

    @if(Auth::check())

        <!-- Check the create channel options are enabled by admin -->

        @if(Setting::get('create_channel_by_user') == CREATE_CHANNEL_BY_USER_ENABLED || Auth::user()->is_master_user == 1)

            <?php $channels = getChannels(Auth::user()->id);?>

            @if(count($channels) > 0 || Auth::user()->user_type)

                <ul class="y-home" style="margin-top: 10px;">
                   

                    <h3>{{tr('my_channels')}}</h3>


                    @foreach($channels as $channel)
                        <li>
                            <a href="{{route('user.channel',$channel->id)}}"><img src="{{$channel->picture}}">{{$channel->name}}</a>
                        </li>
                    @endforeach  


                    @if(Auth::user()->user_type)  

                        @if(count($channels) == 0 || Setting::get('multi_channel_status'))  

                        <li>
                            <a href="{{route('user.create_channel')}}"><i class="fa fa-tv fa-2x" style="vertical-align: middle;"></i> {{tr('create_channel')}}</a>
                        </li>    

                        @endif

                    @endif     
                
                </ul>

            @endif
            
        @endif


        @if(!Auth::user()->user_type)

            <div class="menu4">
                <p>{{tr('subscribe_note')}}</p>
                <a href="{{route('user.subscriptions')}}" class="btn btn-sm btn-primary">{{tr('subscribe')}}</a>
            </div> 


        @endif


    @else
        <div class="menu4">
            <p>Sign in now to see your channels and recommendations!</p>
            <form method="get" action="{{route('user.login.form')}}">
                <button type="submit">{{tr('login')}}</button>
            </form>
        </div>   
    @endif             
</div>