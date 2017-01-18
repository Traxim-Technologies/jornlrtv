@extends('layouts.user.focused')

@section('content')

<div class="form-background">
        <div class="common-form">
            
            <div class="social-form">
                <div class="signup-head">
                    <h3>{{tr('signup')}}</h3>
                </div><!--end  of signup-head-->

                <div class="social-btn">

                    @if(config('services.facebook.client_id') && config('services.facebook.client_secret'))
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
                    
                    @endif

                    @if(config('services.twitter.client_id') && config('services.twitter.client_secret'))

                        <div class="social-twitter">
                            <form class="social-form form-horizontal" role="form" method="POST" action="{{ route('SocialLogin') }}">
                                <input type="hidden" value="twitter" name="provider" id="provider">
                                <a href="#">
                                    <button type="submit">
                                        <i class="fa fa-twitter"></i>{{tr('login_via_twitter')}}
                                    </button>
                                </a>
                            </form>
                        </div>

                    @endif

                    @if(config('services.google.client_id') && config('services.google.client_secret'))

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
                        
                    @endif

                </div><!--end of social-btn-->          
            </div><!--end of socila-form-->

            <p class="col-xs-12 divider1">OR</p>

            <div class="sign-up">

                <form class="signup-form" role="form" method="POST" action="{{ url('/register') }}">

                    {!! csrf_field() !!}

                    @if($errors->has('email') || $errors->has('name') || $errors->has('password_confirmation') ||$errors->has('password'))
                        <div data-abide-error="" class="alert callout">
                            <p>
                                <i class="fa fa-exclamation-triangle"></i> 
                                <strong> 
                                    @if($errors->has('email')) 
                                        {{ $errors->first('email') }}
                                    @endif

                                    @if($errors->has('name')) 
                                        {{ $errors->first('name') }}
                                    @endif

                                    @if($errors->has('password')) 
                                        {{$errors->first('password')}}
                                    @endif

                                    @if($errors->has('password_confirmation'))
                                        {{ $errors->has('password_confirmation') }}
                                    @endif

                                </strong>
                            </p>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="exampleInputEmail1">{{tr('name')}}</label>
                        <input type="text" name="name" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter name">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">{{tr('email')}}</label>
                        <input type="email" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputPassword1">{{tr('password')}}</label>
                        <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputPassword1">{{tr('confirm_password')}}</label>
                        <input type="password" name="password_confirmation" class="form-control" id="exampleInputPassword1" placeholder="Confirm Your Password">
                    </div>

                    <div class="change-pwd">
                        <button type="submit" class="btn btn-primary signup-submit">{{tr('submit')}}</button>
                    </div>  
                    <p>Already Have an Account? <a href="{{route('user.login.form')}}">{{tr('login')}}</a></p>         
                </form>
            </div><!--end of sign-up-->
        </div><!--end of common-form-->     
    </div><!--form-background end-->

@endsection
