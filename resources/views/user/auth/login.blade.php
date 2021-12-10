@extends('layouts.user.focused')

@section('content')

<div class="login-space-top">
<div class="login-space">

    <div class="container">
    <div class="row" style="border-radius: 10px;-webkit-box-shadow: -5px 8px 5px rgb(0 0 0 / 15%);">
    <div class="col-sm-6">
    <div class="common-form login-common text-center">



        @include('notification.notify')

        <!-- <div class="signup-head"> -->
            <!-- <h3>{{tr('login')}}</h3> -->
        <!-- </div><!--end  of signup-head--> 

       
        <div class="sign-up login-page">
            {!! csrf_field() !!}

            @if($errors->has('email') || $errors->has('password'))
                <div data-abide-error="" class="alert callout">
                    <p>
                        <i class="fa fa-exclamation-triangle"></i> 
                        <strong> 
                            @if($errors->has('email')) 
                                {{ $errors->first('email') }}
                            @else 
                                {{$errors->first('password')}}
                            @endif
                        </strong>
                    </p>
                </div>
            @endif

            <form class="signup-form login-form" role="form" method="POST" action="{{ url('/login') }}">
                <div class="form-group">

                    <label for="email">{{'Email Address'}}</label>

                    <input type="email" name="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="{{tr('email')}}" required value="{{old('email')}}"> 
                    
                </div>

                <div class="form-group">

                    <label for="password">{{tr('password')}}</label>

                    <input type="password" name="password" class="form-control" id="password" placeholder="{{tr('password')}}" required value="{{old('password')}}">

                    <span class="form-error">
                        @if ($errors->has('password'))
                            <strong>{{ $errors->first('password') }}</strong>
                        @endif
                    </span>

                </div>

                <input type="hidden" name="timezone" value="" id="userTimezone">

                <div class="change-pwd">
                    <button type="submit" class="btn btn-primary signup-submit">{{tr('submit')}}</button>
                </div>  
				<p class="col-xs-12 divider1">Or</p>
				         
            </form>
			 @if((config('services.facebook.client_id') && config('services.facebook.client_secret'))
            || (config('services.twitter.client_id') && config('services.twitter.client_secret'))
            || (config('services.google.client_id') && config('services.google.client_secret')))

            <div class="social-form">
                <div class="social-btn">
  @if(config('services.google.client_id') && config('services.google.client_secret'))
                        
                        <div class="social-google">
                            <!--google-form-->
                            <form class="social-form form-horizontal" role="form" method="POST" action="http://staging.jornlr.com/social">
                                <input type="hidden" value="google" name="provider" id="provider">
                                
                                <input type="hidden" name="timezone" value="" id="g-userTimezone">

                                <a href="#">
                                    <button type="submit">
                                        <img src="images/google-img-login.png"/>
                                    </button>
                                </a>
                            </form>
                        </div>

                    @endif
					
                    @if(config('services.facebook.client_id') && config('services.facebook.client_secret'))
                        <div class="social-fb">
                            <!--fb-form-->
                            <form class="social-form form-horizontal" role="form" method="POST" action="http://staging.jornlr.com/social">
                                <input type="hidden" value="facebook" name="provider" id="provider">
                                <input type="hidden" name="timezone" value="" id="f-userTimezone">

                                <a href="#">
                                    <button type="submit">
                                       <img src="images/facebook-login.png"/>
                                    </button>
                                </a>
                            </form>
                        </div>

                    @endif

                    @if(config('services.twitter.client_id') && config('services.twitter.client_secret'))

                        <div class="social-twitter">
                            <form class="social-form form-horizontal" role="form" method="POST" action="http://staging.jornlr.com/social">
                            <input type="hidden" value="twitter" name="provider" id="provider">
                            <input type="hidden" name="timezone" value="" id="t-userTimezone">

                            <a href="#">
                                <button type="submit">
                                    <i class="fa fa-twitter"></i>&nbsp;&nbsp;{{tr('login_via_twitter')}}
                                </button>
                            </a>
                            </form>
                        </div>

                    @endif

                  

                </div><!--end of social-btn-->          
            </div><!--end of socila-form-->

            

        @endif
		 <p class="new_account">{{tr('new_account')}} <a href="{{route('user.register.form')}}">{{tr('register')}}</a></p>
                <p class="new_account"> <a href="{{ url('/password/reset') }}">{{tr('forgot_password')}}</a></p>  
        </div><!--end of sign-up-->

    </div><!--end of common-form-->  
    </div> 
<div class="col-sm-6" style="padding-right:0;">
	<img class="login-right-img" src="images/login-img.png"/>
    </div>  
    </div>  
    </div>  

</div><!--end of form-background-->
<img src="images/login-left-bottom-final.png"/>
</div>

@endsection

@section('scripts')

<script src="{{asset('assets/js/jstz.min.js')}}"></script>
<script>
    
    $(document).ready(function() {

        var dMin = new Date().getTimezoneOffset();
        var dtz = -(dMin/60);
        // alert(dtz);
        $("#userTimezone, #t-userTimezone,#f-userTimezone,#g-userTimezone").val(jstz.determine().name());
    });

</script>

@endsection
