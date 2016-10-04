<!-- footer -->
                
<?php /** <footer>
    <div class="row">

        <div class="large-3 medium-6 columns">                        
            <div class="widgetBox">
                <div class="widgetTitle">
                    <h5>About Betube</h5>
                </div><!--widgetTitle end-->

                <div class="textwidget">
                    Betube video wordpress theme lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s book.
                </div><!--textwidget end-->
            </div><!--widgetBox end-->
        </div><!--large-3 end-->

        <div class="large-3 medium-6 columns">
            <div class="widgetBox">
                <div class="widgetTitle">
                    <h5>Recent Videos</h5>
                </div><!--widgetTitle end-->

                <div class="widgetContent">
                    <div class="media-object">
                        <div class="media-object-section">
                            <div class="recent-img">
                                <img src= "{{asset('placeholder.png')}}" alt="recent">
                                <a href="#" class="hover-posts">
                                    <span><i class="fa fa-play"></i></span>
                                </a>
                            </div><!--recent-img end-->
                        </div><!--media-object-section end-->

                        <div class="media-object-section">
                            <div class="media-content">
                                <h6><a href="#">The lorem Ipsumbeen the industry's standard.</a></h6>
                                <p><i class="fa fa-user"></i><span>admin</span><i class="fa fa-clock-o"></i><span>5 january 16</span></p>
                            </div><!--media-content end-->
                        </div><!--media-object-section end-->
                    </div><!--media-object end-->

                    <div class="media-object">
                        <div class="media-object-section">
                            <div class="recent-img">
                                <img src= "{{asset('placeholder.png')}}" alt="recent">
                                <a href="#" class="hover-posts">
                                    <span><i class="fa fa-play"></i></span>
                                </a>
                            </div><!--media-content end-->
                        </div><!--media-object-section end-->

                        <div class="media-object-section">
                            <div class="media-content">
                                <h6><a href="#">The lorem Ipsumbeen the industry's standard.</a></h6>
                                <p><i class="fa fa-user"></i><span>admin</span><i class="fa fa-clock-o"></i><span>5 january 16</span></p>
                            </div><!--media-content end-->
                        </div><!--media-object-section end-->
                    </div><!--media-object end-->

                    <div class="media-object">
                        <div class="media-object-section">
                            <div class="recent-img">
                                <img src= "{{asset('placeholder.png')}}" alt="recent">
                                <a href="#" class="hover-posts">
                                    <span><i class="fa fa-play"></i></span>
                                </a>
                            </div><!--media-content end-->
                        </div><!--media-object-section end-->

                        <div class="media-object-section">
                            <div class="media-content">
                                <h6><a href="#">The lorem Ipsumbeen the industry's standard.</a></h6>
                                <p><i class="fa fa-user"></i><span>admin</span><i class="fa fa-clock-o"></i><span>5 january 16</span></p>
                            </div><!--media-content end-->
                        </div><!--media-object-section end-->
                    </div><!--media-object end-->

                </div><!--Widgetcontent end-->

            </div><!--Widgetbox end-->
        </div><!--large-3 end-->

        <div class="large-3 medium-6 columns">
            <div class="widgetBox">
                <div class="widgetTitle">
                    <h5>Tags</h5>
                </div><!--widgetTitle end-->

                <div class="tagcloud">
                    <a href="#">3D Videos</a>
                    <a href="#">Videos</a>
                    <a href="#">HD</a>
                    <a href="#">Movies</a>
                    <a href="#">Sports</a>
                    <a href="#">3D</a>
                    <a href="#">Movies</a>
                    <a href="#">Animation</a>
                    <a href="#">HD</a>
                    <a href="#">Music</a>
                    <a href="#">Recreation</a>
                </div><!--tagcloud end-->
            </div><!--Widgetbox end-->
        </div><!--large-3 end-->

        <div class="large-3 medium-6 columns">
            <div class="widgetBox">
                <div class="widgetTitle">
                    <h5>Subscribe Now</h5>
                </div><!--widgetTitle end-->

                <div class="widgetContent">
                    <form data-abide novalidate method="post">
                        <p>Subscribe to get exclusive videos</p>
                        <div class="input">
                            <input type="text" placeholder="Enter your full Name" required>
                            <span class="form-error">
                                Yo, you had better fill this out, it's required.
                            </span>
                        </div>
                        <div class="input">
                            <input type="email" id="email" placeholder="Enter your email addres" required>
                            <span class="form-error">
                              I'm required!
                            </span>
                        </div>
                        <button class="button" type="submit" value="Submit">Sign up Now</button>
                    </form>

                    <div class="social-links">
                        <h5>We’re a Social Bunch</h5>
                        <a class="secondary-button" href="#"><i class="fa fa-facebook"></i></a>
                        <a class="secondary-button" href="#"><i class="fa fa-twitter"></i></a>
                        <a class="secondary-button" href="#"><i class="fa fa-google-plus"></i></a>
                        <a class="secondary-button" href="#"><i class="fa fa-instagram"></i></a>
                        <a class="secondary-button" href="#"><i class="fa fa-vimeo"></i></a>
                        <a class="secondary-button" href="#"><i class="fa fa-youtube"></i></a>
                        <a class="secondary-button" href="#"><i class="fa fa-flickr"></i></a>

                    </div><!--social-links end-->
                </div><!--widget-content-->

            </div><!--widget-box end-->
        </div><!--large-3 end-->
    </div>
    <!--footer row-->
    <a href="#" id="back-to-top" title="Back to top"><i class="fa fa-angle-double-up"></i></a>
</footer> */ ?>

<!-- footer -->

<div id="footer-bottom">
    <div class="logo text-center">
        <a href="{{route('user.dashboard')}}"><img src="@if(Setting::get('site_logo')) {{Setting::get('site_logo') }} @else {{asset('logo.png')}} @endif" alt="logo"></a>
    </div>

    <div class="btm-footer-text text-center">
        <p>2016 © {{Setting::get('site_name' , 'Start Streaming')}}</p>
    </div>
</div>

<!--footer-bottom end-->