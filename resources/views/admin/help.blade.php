@extends('layouts.admin')

@section('title', tr('help'))

@section('content-header', tr('help'))

@section('content')

<div class="row">

    <div class="col-md-12">

    	<div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">{{tr('help')}}</h3>
            </div>

            <div class="box-body">

            	<div class="card">


			       	<div class="card-head style-primary">
			            <header>Hi there!</header>
			        </div>

            		<div class="card-body help">
		                <p>
		                  We would like to thank you for choosing Start Streaming. Kudos from our team!!
		                </p>

		                <p>
		                  If you want to make any changes to your site, drop a mail to info@startstreaming.co or Skype us @ info@startstreaming.co and we will help you out! 
		                </p>

		                <a href="https://www.facebook.com/StartVideoStreaming/" target="_blank"><img class="aligncenter size-full wp-image-159 help-image" src="http://default.startstreaming.info/helpsocial/Facebook.png" alt="Facebook-100" width="100" height="100" /></a>
		                &nbsp;

		                <a href="https://twitter.com/startstream" target="_blank"><img class="size-full wp-image-155 alignleft help-image" src="http://default.startstreaming.info/helpsocial/twitter.png " alt="http://twitter.com/startstreaming" width="100" height="100" /></a>
		                &nbsp;

		                <a href="#" target="_blank"> <img class="wp-image-158 alignleft help-image" src="http://default.startstreaming.info/helpsocial/skype.png" alt="skype" width="100" height="100" /></a>
		                &nbsp;

		                <a href="mailto:mail@aravinth.net" target="_blank"><img class="size-full wp-image-153 alignleft help-image" src="http://default.startstreaming.info/helpsocial/mail.png" alt="Message-100" width="100" height="100" /></a>

			             &nbsp;


			             <p>We have this team of innate developers and dedicated team of support to sort out the things for your benefits. Tell us what you like about Start Streaming and we may suggest you the best solution for you :)</p>

              			<a href="#" target="_blank"><img class="aligncenter help-image size-full wp-image-160" src="http://default.startstreaming.info/helpsocial/Money-Box-100.png" alt="Money Box-100" width="100" height="100" /></a>

						<p>Cheers!</p>

            		</div>

        		</div>
    		
    		</div>
        </div>

    </div>

</div>



@endsection