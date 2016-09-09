@extends('layouts.user.focused')

@section('content')

<div class="form-background">
    <div class="common-form login-common">

        <div class="social-form">
            <div class="signup-head">
                <h3>Login</h3>
            </div><!--end  of signup-head-->

            <div class="social-btn">
                <div class="social-fb">
                    <form class="social-form form-horizontal" role="form" method="POST" action="{{ route('SocialLogin') }}">
                        <input type="hidden" value="facebook" name="provider" id="provider">
                        <a href="#">
                            <button type="submit">
                                <i class="fa fa-facebook"></i>{{tr('login_via_fb')}}
                            </button>
                        </a>
                    </form>
                </div>
                <div class="social-google">
                    <form class="social-form form-horizontal" role="form" method="POST" action="{{ route('SocialLogin') }}">
                        <input type="hidden" value="google" name="provider" id="provider">
                        <a href="#">
                            <button type="submit">
                                <i class="fa fa-google-plus"></i>{{tr('login_via_google')}}
                            </button>
                        </a>
                    </form>
                </div>
            </div><!--end of social-btn-->          
        </div><!--end of socila-form-->

        <p class="col-xs-12 divider1">OR</p>

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
                                $errors->first('password')
                            @endif
                        </strong>
                    </p>
                </div>
            @endif

            <form class="signup-form login-form" role="form" method="POST" action="{{ url('/login') }}">
                <div class="form-group">
                    <label for="exampleInputEmail1">{{tr('email')}}</label>
                    <input type="email" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter Your email">

                    
                </div>

                <div class="form-group">
                    <label for="exampleInputPassword1">{{tr('password')}}</label>
                    <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Enter Password">

                    <span class="form-error">
                        @if ($errors->has('password'))
                            <strong>{{ $errors->first('password') }}</strong>
                        @endif
                    </span>

                </div>

                <div class="change-pwd">
                    <button type="submit" class="btn btn-primary signup-submit">{{tr('submit')}}</button>
                </div>  
                <p>{{tr('new_account')}} <a href="{{route('user.register.form')}}">{{tr('register')}}</a></p>
                <p> <a href="{{ url('/password/reset') }}">{{tr('forgot_password')}}</a></p>           
            </form>
        </div><!--end of sign-up-->

    </div><!--end of common-form-->     
</div><!--end of form-background-->

@endsection
