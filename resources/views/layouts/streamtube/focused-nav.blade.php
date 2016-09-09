<div class="youtube-nav signup-nav">
    <div class="row">
        <div class="test you-image">
            <a href="{{route('user.dashboard')}}" class="y-image"><img src="{{Setting::get('site_logo' , asset('logo.png'))}}"></a>
        </div><!--test end-->

        <div class="y-button">
        <a href="{{route('user.register.form')}}" class="y-signin">{{tr('signup')}}</a>
        
        </div><!--y-button end-->

    </div><!--end of row-->
</div><!--end of youtube-nav-->