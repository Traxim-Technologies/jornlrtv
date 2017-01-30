@extends('layouts.user')

@section('content')

    <div class="form-background">
        <div class="common-form login-common">

            @include('notification.notify')

            <div class="social-form">
                <div class="signup-head">
                    <h3>{{tr('change_password')}}</h3>
                </div><!--end  of signup-head-->        
            </div><!--end of socila-form-->

            <div class="sign-up login-page">            
                <form class="signup-form login-form" method="post" action="{{ route('user.profile.password') }}">

                    <div class="form-group">
                        <label for="exampleInputPassword1">{{tr('old_password')}}</label>
                        <input type="password" name="old_password" class="form-control" id="exampleInputPassword1" placeholder="Enter Old Password">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputPassword1">{{tr('new_password')}}</label>
                        <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Enter New Password">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputPassword1">{{tr('confirm_password')}}</label>
                        <input type="password" name="password_confirmation" class="form-control" id="exampleInputPassword1" placeholder="Confirm Password">
                    </div>

                    <div class="change-pwd">
                        <button type="submit" class="btn btn-primary signup-submit">{{tr('submit')}}</button>
                    </div>              
                </form>
            </div><!--end of sign-up-->

        </div><!--end of common-form-->     
    </div><!--end of form-background-->

@endsection
