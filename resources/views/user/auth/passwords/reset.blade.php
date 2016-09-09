@extends('layouts.user.focused')

@section('content')

<section class="registration">
    <div class="row secBg">
        <div class="large-12 columns">
            <div class="login-register-content">

                <div class="row collapse borderBottom">
                    <div class="medium-6 large-centered medium-centered">
                        <div class="page-heading text-center" style="margin-top:20px;padding:0px !important">
                            <h3>{{tr('forgot_password')}}</h3>
                        </div>
                    </div>
                </div>

                <div class="row" data-equalizer="fldb3f-equalizer" data-equalize-on="medium" id="test-eq" data-resize="mmu5g8-eq" data-events="resize">
                    <div class="large-4 medium-6 large-centered medium-centered columns">
                        <div class="register-form">
                            <h5 class="text-center">Enter Email</h5>
                            <form method="post" data-abide="bhwxrp-abide" novalidate="">
                                <div class="input-group">
                                    <span class="input-group-label"><i class="fa fa-user"></i></span>
                                    <input type="email" placeholder="Enter your email" required="">
                                    <span class="form-error">email is required</span>
                                </div>
                                <button class="button expanded" type="submit" name="submit">reset Now</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
