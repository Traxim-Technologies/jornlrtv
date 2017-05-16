@extends( 'layouts.user' )

@section( 'styles' )

<!-- Add css file and inline css here -->

<link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/custom-style.css')}}"> 


<style>
    #c4-header-bg-container {
        background-image: url({{$channel->cover}});
    }
    
    @media screen and (-webkit-min-device-pixel-ratio: 1.5),
    screen and (min-resolution: 1.5dppx) {
        #c4-header-bg-container {
            background-image: url({{$channel->cover}});
        }
    }
    
    #c4-header-bg-container .hd-banner-image {
        background-image: url({{$channel->cover}});
    }
</style>


@endsection 


@section('content')

<div class="y-content">
    <div class="row content-row">

        @include('layouts.user.nav')

        <div class="page-inner col-sm-9 col-md-10">

            @include('notification.notify')

            <div class="branded-page-v2-top-row">


                <div class="branded-page-v2-header channel-header yt-card">


                    <div id="gh-banner">
                    

                        <div id="c4-header-bg-container" class="c4-visible-on-hover-container  has-custom-banner">
                            <div class="hd-banner">
                                <div class="hd-banner-image "></div>
                            </div>
                            <div id="header-links">
                                <ul class="about-secondary-links">
                                    <li class="channel-links-item">
                                        <a href="http://www.facebook.com/adele" rel="me nofollow" target="_blank" title="Facebook" class="about-channel-link yt-uix-redirect-link about-channel-link-with-icon">
                                            <img src="//s2.googleusercontent.com/s2/favicons?domain_url=http%3A%2F%2Fwww.facebook.com%2Fadele&amp;feature=youtube_channel" class="about-channel-link-favicon" alt="" width="16" height="16">
                                        </a>
                                    
                                    </li>

                                    <li class="channel-links-item">
                                        <a href="http://twitter.com/adele" rel="me nofollow" target="_blank" title="Twitter" class="about-channel-link yt-uix-redirect-link about-channel-link-with-icon">
                                            <img src="//s2.googleusercontent.com/s2/favicons?domain_url=http%3A%2F%2Ftwitter.com%2Fadele&amp;feature=youtube_channel" class="about-channel-link-favicon" alt="" width="16" height="16">
                                    </a>
                                    
                                    </li>

                                    <li class="channel-links-item">
                                        <a href="http://instagram.com/adele" rel="me nofollow" target="_blank" title="Instagram" class="about-channel-link yt-uix-redirect-link about-channel-link-with-icon">
                                            <img src="//s2.googleusercontent.com/s2/favicons?domain_url=http%3A%2F%2Finstagram.com%2Fadele&amp;feature=youtube_channel" class="about-channel-link-favicon" alt="" width="16" height="16">
                                        </a>
                                    </li>

                                    <li class="channel-links-item">
                                        <a href="http://www.adele.tv/" rel="me nofollow" target="_blank" title="Official Website" class="about-channel-link yt-uix-redirect-link about-channel-link-with-icon">
                                            <img src="//s2.googleusercontent.com/s2/favicons?domain_url=http%3A%2F%2Fwww.adele.tv%2F&amp;feature=youtube_channel" class="about-channel-link-favicon" alt="" width="16" height="16">
                                        </a>
                                    </li>

                                </ul>

                                <!-- <ul class="about-custom-links">
                                    <li class="channel-links-item">
                                        <a href="http://www.vevo.com/artist/adele" rel="me nofollow" target="_blank" title="Vevo" class="about-channel-link yt-uix-redirect-link about-channel-link-with-icon">
        <img src="//s2.googleusercontent.com/s2/favicons?domain_url=http%3A%2F%2Fwww.vevo.com%2Fartist%2Fadele&amp;feature=youtube_channel" class="about-channel-link-favicon" alt="" width="16" height="16">
        <span class="about-channel-link-text">
          Vevo
        </span>
    </a>
                                

                                    </li>

                                </ul> -->
                            </div>


                            <a class="channel-header-profile-image spf-link">
                              <img class="channel-header-profile-image" src="{{$channel->picture}}" title="{{$channel->name}}" alt="{{$channel->name}}">
                            </a>
                        
                        </div>

                    </div>
                    <div class="primary-header-contents clearfix " id="c4-primary-header-contents">
                        <div class="primary-header-upper-section-wrapper clearfix">
                            <div class="primary-header-upper-section">
                                <div class="primary-header-upper-section-block">
                                    <h1 class="branded-page-header-title">
        <span class="qualified-channel-title ellipsized has-badge">
            <span class="qualified-channel-title-wrapper">
            <span dir="ltr" class="qualified-channel-title-text">
            <a dir="ltr" class="spf-link branded-page-header-title-link yt-uix-sessionlink" title="{{$channel->name}}" data-sessionlink="ei=UH4JWdjaHNaYogPEt5TYAg">{{$channel->name}}
            </a></span></span>
        </span>
      </h1>
                                







                                </div>
                            </div>

                        </div>

                    </div>

                    <div id="channel-subheader" class="clearfix branded-page-gutter-padding appbar-content-trigger">
                        <ul id="channel-navigation-menu" class="clearfix nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#home1" class="yt-uix-button  spf-link  yt-uix-sessionlink yt-uix-button-epic-nav-item yt-uix-button-size-default" aria-controls="home" role="tab" data-toggle="tab"><span class="yt-uix-button-content">Home</span></a>
                            </li>
                            <li role="presentation">
                                <a href="#videos" class="yt-uix-button  spf-link  yt-uix-sessionlink yt-uix-button-epic-nav-item yt-uix-button-size-default" aria-controls="videos" role="tab" data-toggle="tab"><span class="yt-uix-button-content">Videos</span> </a>
                            </li>
                            <li role="presentation">
                                <a href="#about" class="yt-uix-button  spf-link  yt-uix-sessionlink yt-uix-button-epic-nav-item yt-uix-button-size-default" aria-controls="about" role="tab" data-toggle="tab"><span class="yt-uix-button-content">{{tr('about_tab')}}</span> </a>
                            </li>
                        </ul>
                    </div>


                </div>



            </div>

            <ul class="tab-content">

                <li role="tabpanel" class="tab-pane active" id="home1">
                    <div class="feed-item-dismissable">
                        <div class="feed-item-main feed-item-no-author">
                            <div class="feed-item-main-content">
                                <div class="shelf-wrapper clearfix">
                                    <div class="big-section-main">

                                        <h2 class="branded-page-module-title">
          <span class="branded-page-module-title-text">
      What to watch next
    </span>

  </h2>
                                    






                                        <div class="lohp-shelf-content row">
                                            <div class="lohp-large-shelf-container col-md-6">
                                                <div class="slide-box recom-box big-box-slide">
                                                    <div class="slide-image recom-image hbb">
                                                        <a href="http://demo.streamhash.com/video/55"><img src="http://demo.streamhash.com/uploads/41ae6130255ead312bbe946be259b7a6b02b54e5.jpg"></a>
                                                    </div>
                                                    <!--end of slide-image-->

                                                    <div class="video-details recom-details">
                                                        <div class="video-head">
                                                            <a href="http://demo.streamhash.com/video/55"> Blippbuilder Tutorial AR Creator for Education</a>
                                                        </div>
                                                        <div class="sugg-description">
                                                            <p>Duration: 00:14:00<span class="content-item-time-created lohp-video-metadata-item" title="11 months ago"><i class="fa fa-clock-o" aria-hidden="true"></i> 11 months ago</span>
                                                            </p>
                                                        </div>
                                                        <!--end of sugg-description-->

                                                        <span class="stars">
                                        <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i class="fa fa-star" aria-hidden="true"></i></a>
                                    </span>
                                                    







                                                    </div>
                                                    <!--end of video-details-->
                                                </div>
                                            </div>
                                            <div class="lohp-medium-shelves-container col-md-6">
                                                <div class="col-md-6">

                                                    <div class="slide-box recom-box big-box-slide">
                                                        <div class="slide-image recom-image hbb">
                                                            <a href="http://demo.streamhash.com/video/55"><img src="http://demo.streamhash.com/uploads/41ae6130255ead312bbe946be259b7a6b02b54e5.jpg"></a>
                                                        </div>
                                                        <!--end of slide-image-->

                                                        <div class="video-details recom-details">
                                                            <div class="video-head">
                                                                <a href="http://demo.streamhash.com/video/55"> Blippbuilder Tutorial AR Creator for Education</a>
                                                            </div>
                                                            <div class="sugg-description">
                                                                <p>Duration: 00:14:00<span class="content-item-time-created lohp-video-metadata-item" title="11 months ago"><i class="fa fa-clock-o" aria-hidden="true"></i> 11 months ago</span>
                                                                </p>
                                                            </div>
                                                            <!--end of sugg-description-->

                                                            <span class="stars">
                                        <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i class="fa fa-star" aria-hidden="true"></i></a>
                                    </span>
                                                        







                                                        </div>
                                                        <!--end of video-details-->
                                                    </div>
                                                </div>

                                                <div class="col-md-6">

                                                    <div class="slide-box recom-box big-box-slide">
                                                        <div class="slide-image recom-image hbb">
                                                            <a href="http://demo.streamhash.com/video/55"><img src="http://demo.streamhash.com/uploads/41ae6130255ead312bbe946be259b7a6b02b54e5.jpg"></a>
                                                        </div>
                                                        <!--end of slide-image-->

                                                        <div class="video-details recom-details">
                                                            <div class="video-head">
                                                                <a href="http://demo.streamhash.com/video/55"> Blippbuilder Tutorial AR Creator for Education</a>
                                                            </div>
                                                            <div class="sugg-description">
                                                                <p>Duration: 00:14:00<span class="content-item-time-created lohp-video-metadata-item" title="11 months ago"><i class="fa fa-clock-o" aria-hidden="true"></i> 11 months ago</span>
                                                                </p>
                                                            </div>
                                                            <!--end of sugg-description-->

                                                            <span class="stars">
                                        <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i class="fa fa-star" aria-hidden="true"></i></a>
                                    </span>
                                                        







                                                        </div>
                                                        <!--end of video-details-->
                                                    </div>
                                                </div>

                                                <div class="col-md-6">

                                                    <div class="slide-box recom-box big-box-slide">
                                                        <div class="slide-image recom-image hbb">
                                                            <a href="http://demo.streamhash.com/video/55"><img src="http://demo.streamhash.com/uploads/41ae6130255ead312bbe946be259b7a6b02b54e5.jpg"></a>
                                                        </div>
                                                        <!--end of slide-image-->

                                                        <div class="video-details recom-details">
                                                            <div class="video-head">
                                                                <a href="http://demo.streamhash.com/video/55"> Blippbuilder Tutorial AR Creator for Education</a>
                                                            </div>
                                                            <div class="sugg-description">
                                                                <p>Duration: 00:14:00<span class="content-item-time-created lohp-video-metadata-item" title="11 months ago"><i class="fa fa-clock-o" aria-hidden="true"></i> 11 months ago</span>
                                                                </p>
                                                            </div>
                                                            <!--end of sugg-description-->

                                                            <span class="stars">
                                        <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i class="fa fa-star" aria-hidden="true"></i></a>
                                    </span>
                                                        







                                                        </div>
                                                        <!--end of video-details-->
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="menu-container">
                                    </div>


                                </div>



                            </div>
                        </div>
                    </div>



                </li>



                <li role="tabpanel" class="tab-pane" id="videos">
                    <div class="slide-area recom-area">
                        <!-- <div class="box-head recom-head">
                            <h3>Tutorial</h3>
                        </div> -->

                        <div class="recommend-list row">

                            
                            <div class="slide-box recom-box">
                                <div class="slide-image recom-image">
                                    <a href="http://demo.streamhash.com/video/55"><img src="http://demo.streamhash.com/uploads/41ae6130255ead312bbe946be259b7a6b02b54e5.jpg"></a>
                                </div>
                                <!--end of slide-image-->

                                <div class="video-details recom-details">
                                    <div class="video-head">
                                        <a href="http://demo.streamhash.com/video/55"> Blippbuilder Tutorial AR Creator for Education</a>
                                    </div>
                                    <div class="sugg-description">
                                        <p>Duration: 00:14:00<span class="content-item-time-created lohp-video-metadata-item" title="11 months ago"><i class="fa fa-clock-o" aria-hidden="true"></i> 11 months ago</span>
                                        </p>
                                    </div>
                                    <!--end of sugg-description-->

                                    <span class="stars">
                                        <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i style="color:gold" class="fa fa-star" aria-hidden="true"></i></a>
                                       <a href="#"><i class="fa fa-star" aria-hidden="true"></i></a>
                                    </span>
                                
                                </div>
                                <!--end of video-details-->
                            </div>

                        </div>
                        <!--end of recommend-list-->
                        <div class="row">
                            <div class="col-md-12">
                                <div id="paglink" align="center"></div>
                            </div>
                        </div>
                    </div>

                </li>
                <li role="tabpanel" class="tab-pane" id="about">

                    <div class="slide-area recom-area abt-sec">
                        <div class="abt-sec-head">
                            <h5>
                            @if($channel->description) 
                                {{$channel->description}}
                            @else
                                {{tr('no_channel_description_found')}}
                            @endif
                            </h5>
                        </div>
                    </div>


                </li>
            </ul>

        </div>

    </div>

</div>


@endsection

@section( 'scripts' )

<!-- Add Js files and inline js here -->

@endsection