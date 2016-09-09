@extends('layouts.user')

@section('content')


 <!--breadcrumbs-->

 <section id="breadcrumb">
    <div class="row">
        <div class="large-12 columns">
            <nav aria-label="You are here:" role="navigation">
                <ul class="breadcrumbs">
                    <li><i class="fa fa-home"></i><a href="{{route('user.dashboard')}}">{{tr('home')}}</a></li>
                    <li><span class="show-for-sr">Current: </span>{{tr('profile')}}</a></li>
                </ul>
            </nav>
        </div>
    </div>
</section>

<!--end breadcrumbs-->

<div class="row">
    <!-- left sidebar -->

    @include('layouts.user.user-sidebar')

    <!-- end sidebar -->

    <!-- right side content area -->
    <div class="large-8 columns mar-top-space">
        <!-- single post description -->

        <section class="singlePostDescription">
            <div class="row secBg">
                <div class="large-12 columns">
                    <div class="heading">
                        <i class="fa fa-user"></i>
                        <h4>{{tr('description')}}</h4>
                    </div>
                    <div class="description">

                        <p>{{Auth::user()->description}}</p>

                        <div class="email profile-margin">
                            <button><i class="fa fa-envelope"></i>{{tr('email')}}</button>
                            <span class="inner-btn">{{Auth::user()->email}}</span>
                        </div>
                        
                        <div class="email profile-margin">
                            <button><i class="fa fa-envelope"></i>{{tr('address')}}</button>
                            @if(Auth::user()->address) <span class="inner-btn">{{Auth::user()->address}}</span> @endif
                        </div>
                        <div class="phone profile-margin">
                            <button><i class="fa fa-phone"></i>{{tr('mobile')}}</button>
                            
                            @if(Auth::user()->mobile) 
                                <span class="inner-btn">
                                    {{Auth::user()->mobile}}
                                </span>
                            @endif
                            
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- End single post description -->
    </div><!-- end left side content area -->

</div>

<!--end left-sidebar row-->

@endsection