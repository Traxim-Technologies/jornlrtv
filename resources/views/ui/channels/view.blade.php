@extends('layouts.user')

@section('styles')

<!-- Add css file and inline css here -->
<link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/custom-style.css')}}"> @endsection @section('content')

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

								<ul class="about-custom-links">
									<li class="channel-links-item">
										<a href="http://www.vevo.com/artist/adele" rel="me nofollow" target="_blank" title="Vevo" class="about-channel-link yt-uix-redirect-link about-channel-link-with-icon">
        <img src="//s2.googleusercontent.com/s2/favicons?domain_url=http%3A%2F%2Fwww.vevo.com%2Fartist%2Fadele&amp;feature=youtube_channel" class="about-channel-link-favicon" alt="" width="16" height="16">
        <span class="about-channel-link-text">
          Vevo
        </span>
    </a>
									





									</li>

								</ul>
							</div>


							<a class="channel-header-profile-image-container spf-link" href="/user/AdeleVEVO">
      <img class="channel-header-profile-image" src="https://yt3.ggpht.com/-Pmv3XiLq6i0/AAAAAAAAAAI/AAAAAAAAAAA/PzA830mDGNo/s100-c-k-no-mo-rj-c0xffffff/photo.jpg" title="AdeleVEVO" alt="AdeleVEVO">
    </a>
						
						</div>
					</div>
					<div class="primary-header-contents clearfix " id="c4-primary-header-contents">
						<div class="primary-header-upper-section-wrapper clearfix">
							<div class="primary-header-upper-section">
								<div class="primary-header-upper-section-block">
									<h1 class="branded-page-header-title">
        <span class="qualified-channel-title ellipsized has-badge"><span class="qualified-channel-title-wrapper"><span dir="ltr" class="qualified-channel-title-text"><a dir="ltr" href="/user/AdeleVEVO" class="spf-link branded-page-header-title-link yt-uix-sessionlink" title="AdeleVEVO" data-sessionlink="ei=UH4JWdjaHNaYogPEt5TYAg">AdeleVEVO</a></span></span>
        </span>
      </h1>
								</div>
							</div>

						</div>
					</div>
					

				</div>
			</div>
           										<div class="slide-area recom-area abt-sec des-crt">
						<div class="abt-sec-head description-create">
							<h5>Channel Description</h5>
							<div class="des-box">
     <textarea class="form-control description" id="description" name="Description"></textarea>
         <div class="btn-create">
					<button id="done-create" name="Done" class="btn btn-primary create-btn-btn">Done</button>
					<button id="cancel-create" name="cancel" class="btn btn-primary create-btn-btn">cancel</button>
					</div>

    </div>
    
						</div>
					</div>

            </div>
        </div>
    </div>

@endsection

@section('scripts')

<!-- Add Js files and inline js here -->

@endsection