@extends('layouts.user.focused')

@section('content')

    <div class="form-background">
        <div class="common-form login-common forgot">

            <div class="social-form">
                <div class="signup-head">
                    <h3>{{tr('forgot_password')}}</h3>
                </div><!--end  of signup-head-->        
            </div><!--end of socila-form-->

            <div class="sign-up login-page">
                <form class="signup-form login-form" >
                    <div class="form-group">
                        <label for="exampleInputEmail1">{{tr('email')}}</label>
                        <input type="email" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter Your email">
                    </div>

                    <div class="change-pwd">
                        <button type="submit" class="btn btn-primary signup-submit">{{tr('email')}}</button>
                    </div>          
                    <p>Already Have an Account? <a href="{{route('user.login.form')}}">{{tr('login')}}</a></p>  
                </form>
            </div><!--end of sign-up-->
        </div><!--end of common-form-->     
    </div><!--end of form-background-->

@endsection
