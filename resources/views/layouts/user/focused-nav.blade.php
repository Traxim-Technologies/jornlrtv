<div class="streamtube-nav signup-nav">
    <div class="row">
        <div class="test you-image col-lg-2 col-md-2 col-sm-2 col-xs-8">
            <a href="{{route('user.dashboard')}}" class="">
                @if(Setting::get('site_logo'))
                    <img src="{{Setting::get('site_logo')}}">
                @else
                    <img src="{{asset('logo.png')}}">
                @endif
            </a>
        </div><!--test end-->

        <div class="y-button">

            <ul class="nav navbar-nav pull-right" style="margin: 3.5px 0px">

                @if(Setting::get('admin_language_control'))

                @if(count($languages = getActiveLanguages()) > 1) 

                    <li  class="dropdown">
                
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" style="padding: 5px 15px; margin-right: 5px;color: #cc181e"><i class="fa fa-globe"></i> <b class="caret"></b></a>

                        <ul class="dropdown-menu" style="min-width: 70px;overflow: hidden;position: absolute;background: #fff;">

                            @foreach($languages as $h => $language)

                                <li class="{{(\Session::get('locale') == $language->folder_name) ? 'active' : ''}}" ><a href="{{route('user_session_language', $language->folder_name)}}" style="{{(\Session::get('locale') == $language->folder_name) ? 'background-color: #cc181e' : ''}}">{{$language->folder_name}}</a></li>
                            @endforeach
                        </ul>
                     
                    </li>

                @endif
                
                @endif

            </ul>

            @if(Auth::check())
                <a href="{{route('user.profile')}}" class="y-signin">{{tr('back_profile')}}</a>
            @else
               <!--  <div class="hidden-xs hidden-sm visible-md visible-lg">
                <a href="{{route('user.register.form')}}" class="y-signin">{{tr('signup')}}</a>
                <a href="{{route('user.login.form')}}" class="y-signin">{{tr('login')}}</a>
                </div> -->
            @endif
        
        </div><!--y-button end-->

    </div><!--end of row-->
</div><!--end of streamtube-nav-->