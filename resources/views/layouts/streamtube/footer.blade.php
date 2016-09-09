<footer>
    <div class="footer1 row">
        <div class="col-sm-2 foot-div">
            <div class="tube-image">
                <img src="{{Setting::get('site_logo' , asset('logo.png'))}}">
            </div>                                 
        </div><!--end of col-sm-2-->

        <div class="col-sm-10 foot-content">
            <ul class="about">
                <li><a href="#">About</a></li>
                <li><a href="#">Copyright</a></li>
                <li><a href="#">Press</a></li>
                <li><a href="#">Creators</a></li>
                <li><a href="#">Advertise</a></li>
                <li><a href="#">Developers</a></li>
                <li><a href="#">+{{Setting::get('site_name')}}</a></li>
            </ul>
        
            <ul class="term">
                <li><a href="#">Terms</a></li>
                <li><a href="#">Privacy</a></li>
                <li><a href="#">Policy&amp;Safety</a></li>
                <li><a href="#">Send Feedback</a></li>
                <li><a href="#">Try Something New!</a></li>
                <li><a href="#">&#169; 2016 {{Setting::get('site_name')}}, LLC</a></li>
            </ul>
        </div>   
    </div><!--end of footer1-->
</footer>