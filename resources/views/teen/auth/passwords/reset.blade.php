@extends('layouts.user.focused')

@section('content')

<div class="login-box">
    <h4>{{tr('forgot_password')}}</h4>   
                
    <form method="post" data-abide="bhwxrp-abide" novalidate="">
        <div class="form-group">
            <label for="email">{{tr('email')}}</label>
            <input type="email" name = "email" class="form-control" placeholder="{{tr('email')}}" id="email">
            <span class="form-error">email is required</span>
        </div>                                   
      <button type="submit" class="btn btn-default">{{tr('reset_now')}}</button>
    </form>  

    <p class="help"><a href="{{route('user.register.form')}}">{{tr('new_account')}}</a></p>
    <p class="help"><a href="{{ route('user.login.form') }}">{{tr('login')}}</a></p>
</div>

@endsection