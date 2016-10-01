@extends('layouts.user.focused')

@section('content')

<div class="form-background forgot-password-reset">

    <div class="common-form login-common">

        <div class="row">
            <div class="col-md-12">
                @include('notification.notify')
            </div>
        </div>

        <div class="social-form">
            <div class="signup-head">
                <h3>We hope we will see again!!</h3>

                 <p>
                    <strong style="color: brown">Note:</strong> Once you deleted account, you will lose your history and wishlist details.
                </p>
            </div><!--end  of signup-head-->         
        </div><!--end of socila-form-->

        <div class="sign-up login-page">

            @if($errors->has('password'))
                <div data-abide-error="" class="alert callout">
                    <p>
                        <i class="fa fa-exclamation-triangle"></i> 
                        <strong> 
                            @if($errors->has('password')) 
                                $errors->first('password')
                            @endif
                        </strong>
                    </p>
                </div>
            
            @endif

            <form class="signup-form login-form" role="form" method="POST" action="{{ route('user.delete.account.process') }}">

                <div class="form-group">
                    <label for="password">{{tr('password')}}</label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="Enter Password">

                    <span class="form-error">
                        @if ($errors->has('password'))
                            <strong>{{ $errors->first('password') }}</strong>
                        @endif
                    </span>

                </div>

                <div class="change-pwd">
                    <button type="submit" class="btn btn-primary signup-submit">{{tr('delete')}}</button>
                </div>  
    
            </form>
        </div><!--end of sign-up-->

    </div><!--end of common-form-->     
</div><!--end of form-background-->

@endsection
