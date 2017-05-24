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


   
    

                
    @if(count($channels = getChannels()) > 0)
        
        <ul class="y-home" style="margin-top: 10px;">
            <h3>{{tr('channels')}}</h3>
            @foreach($channels as $channel)
                <li>
                    <a href="{{route('user.channel',$channel->id)}}"><img src="{{$channel->picture}}">{{$channel->name}}</a>
                </li>
            @endforeach              
        </ul>

    @endif

    @if(Auth::check())

        <ul class="y-home" style="margin-top: 10px;">
            <h3>{{tr('my_channels')}}</h3>
            <?php $channels = getChannels(Auth::user()->id);?>
            @foreach($channels as $channel)
                <li>
                    <a href="{{route('user.channel',$channel->id)}}"><img src="{{$channel->picture}}">{{$channel->name}}</a>
                </li>
            @endforeach  

            @if(Auth::user()->user_type)    

            <li>
                <a href="{{route('user.create_channel')}}"><i class="fa fa-tv fa-2x" style="vertical-align: middle;"></i> {{tr('create_channel')}}</a>
            </li>    

            @endif     
        </ul>


    @else
        <div class="menu4">
            <p>Sign in now to see your channels and recommendations!</p>
            <form method="get" action="{{route('user.login.form')}}">
                <button type="submit">{{tr('login')}}</button>
            </form>
        </div>   
    @endif             
</div>