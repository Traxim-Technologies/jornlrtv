@extends('layouts.moderator.focused')

@section('title', 'Login')

@section('content')

    <div class="login-box-body" style="height:350px">


        <form class="form-layout" role="form" method="POST" action="{{ url('/moderator/login') }}">
            {{ csrf_field() }}

            <div class="text-center mb15">
               <img class="adm-log-logo" style="width:50%;height:auto" src="{{Setting::get('site_logo', asset('logo.png') )}}" />
            </div>

            <p class="text-center mb30"></p>

            <div class="form-inputs">
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <input type="email" class="form-control input-lg" name="email" value="{{ old('email') }}" placeholder="Email">

                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif

                </div>

                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">

                    <input type="password" class="form-control input-lg" name="password" placeholder="Password">

                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif

                </div>


            </div>

            <div class="col-md-6 col-md-offset-3">
                <button class="btn btn-success btn-block mb15" type="submit">
                    <h5><span><i class="fa fa-btn fa-sign-in"></i> Login</span></h5>
                </button>
            </div>

            <!-- <div class="form-group">
                    <a style="margin-left:100px" class="btn btn-link" href="{{ url('/moderator/password/reset') }}">Reset Password</a>
            </div> -->

        </form>

    </div>

@endsection