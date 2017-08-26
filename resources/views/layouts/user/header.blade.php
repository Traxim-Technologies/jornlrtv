<div class="youtube-nav">
    <div class="row">
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-8">

            <a href="#"><i class="fa fa-align-justify toggle-icon" aria-hidden="true"></i></a>

            <a href="{{route('user.dashboard')}}">
                @if(Setting::get('site_logo'))
                    <img src="{{Setting::get('site_logo')}}" style="width: 80%;height: 35px;vertical-align: bottom">
                @else
                    <img src="{{asset('logo.png')}}" style="width: 80%;height: 35px;vertical-align: bottom">
                @endif
            </a>
        

        </div>

        <div class="col-lg-3 col-md-3 col-sm-4 col-xs-4 visible-xs hidden-sm hidden-md hidden-lg">
            @if(Auth::check())
                <div class="y-button profile-button">
                   <div class="dropdown">
                          <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            @if(Auth::user()->picture != "")
                                <img class="profile-image" src="{{Auth::user()->picture}}">
                            @else
                                <img class="profile-image" src="{{asset('placeholder.png')}}">
                            @endif
                            
                          </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                            <li><a href="{{route('user.profile')}}">{{tr('profile')}}</a></li>
                            <li><a href="{{route('user.wishlist')}}">{{tr('wishlist')}}</a></li>
                            <li><a href="{{route('user.history')}}">{{tr('history')}}</a></li>
                            @if(Setting::get('is_spam')) 
                            <li><a href="{{route('user.spam-videos')}}">{{tr('spam_videos')}}</a></li>
                            @endif

                            @if(Setting::get('is_payper_view')) 
                             <li><a href="{{route('user.pay-per-videos')}}">{{tr('pay_per_videos')}}</a></li>

                             @endif
                            @if (Auth::user()->login_by == 'manual') 
                                <li role="separator" class="divider"></li>
                                <li><a href="{{route('user.change.password')}}">{{tr('change_password')}}</a></li>
                            @endif

                            <li role="separator" class="divider"></li>
                            <li><a href="{{route('user.delete.account')}}" @if(Auth::user()->login_by != 'manual') onclick="return confirm('Are you sure? . Once you deleted account, you will lose your history and wishlist details.')" @endif>{{tr('delete_account')}}</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="{{route('user.logout')}}">{{tr('logout')}}</a></li>
                          </ul>
                        </div>
                </div><!--y-button end-->
            @endif

            <ul class="nav navbar-nav pull-left">

                @if(Setting::get('admin_language_control'))

                
                    
                    @if(count($languages = getActiveLanguages()) > 1) 
                       
                        <li  class="dropdown">
                    
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" style="padding: 5px 15px; margin-top: 3px; margin-right: 5px;color: #cc181e"><i class="fa fa-globe"></i> <b class="caret"></b></a>

                            <ul class="dropdown-menu" style="min-width: 70px;overflow: hidden;position: absolute;background: #fff;">

                                @foreach($languages as $h => $language)

                                    <li class="{{(\Session::get('locale') == $language->folder_name) ? 'active' : ''}}" ><a href="{{route('user_session_language', $language->folder_name)}}" style="{{(\Session::get('locale') == $language->folder_name) ? 'background-color: #cc181e' : ''}}">{{$language->folder_name}}</a></li>
                                @endforeach
                                
                            </ul>
                         
                        </li>
                
                    @endif

                @endif

            </ul>

        </div>


        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">

        <div id="custom-search-input" class="">
            <form method="post" action="{{route('search-all')}}" id="userSearch">
            <div class="input-group">
                
                    <input type="text" id="auto_complete_search" name="key" class="search-query form-control" required placeholder="Search" />
                    <span class="input-group-btn">
                        <button class="btn btn-danger" type="submit">
                        <span class=" glyphicon glyphicon-search"></span>
                        </button>
                    </span>
                
            </div>
            </form>
        </div><!--custom-search-input end-->

        </div>

        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 hidden-xs visible-sm visible-md visible-lg">
        @if(Auth::check())
            <div class="y-button profile-button">
               <div class="dropdown">
                      <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        @if(Auth::user()->picture != "")
                            <img class="profile-image" src="{{Auth::user()->picture}}">
                        @else
                            <img class="profile-image" src="{{asset('placeholder.png')}}">
                        @endif
                        
                      </button>
                      <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <li><a href="{{route('user.profile')}}">{{tr('profile')}}</a></li>
                        <li><a href="{{route('user.wishlist')}}">{{tr('wishlist')}}</a></li>
                        <li><a href="{{route('user.history')}}">{{tr('history')}}</a></li>
                        @if(Setting::get('is_spam')) 
                        <li><a href="{{route('user.spam-videos')}}">{{tr('spam_videos')}}</a></li>
                        @endif

                        @if(Setting::get('is_payper_view')) 
                         <li><a href="{{route('user.pay-per-videos')}}">{{tr('pay_per_videos')}}</a></li>

                         @endif
                        @if (Auth::user()->login_by == 'manual') 
                            <li role="separator" class="divider"></li>
                            <li><a href="{{route('user.change.password')}}">{{tr('change_password')}}</a></li>
                        @endif

                        <li role="separator" class="divider"></li>
                        <li><a href="{{route('user.delete.account')}}" @if(Auth::user()->login_by != 'manual') onclick="return confirm('Are you sure? . Once you deleted account, you will lose your history and wishlist details.')" @endif>{{tr('delete_account')}}</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="{{route('user.logout')}}">{{tr('logout')}}</a></li>
                      </ul>
                    </div>
            </div><!--y-button end-->
        @else
            <div class="y-button">
                <a href="{{route('user.login.form')}}" class="y-signin">{{tr('login')}}</a>
            </div><!--y-button end-->

        @endif


        <ul class="nav navbar-nav pull-right">

                @if(Setting::get('admin_language_control'))

                
                    
                    @if(count($languages = getActiveLanguages()) > 1) 
                       
                        <li  class="dropdown">
                    
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" style="padding: 5px 15px; margin-top: 3px; margin-right: 5px;color: #cc181e"><i class="fa fa-globe"></i> <b class="caret"></b></a>

                            <ul class="dropdown-menu">

                                @foreach($languages as $h => $language)

                                    <li class="{{(\Session::get('locale') == $language->folder_name) ? 'active' : ''}}" ><a href="{{route('user_session_language', $language->folder_name)}}" style="{{(\Session::get('locale') == $language->folder_name) ? 'background-color: #cc181e' : ''}}">{{$language->folder_name}}</a></li>
                                @endforeach
                                
                            </ul>
                         
                        </li>
                
                    @endif

                @endif

            </ul>




        </div>

    </div><!--end of row-->
</div><!--end of youtube-nav-->