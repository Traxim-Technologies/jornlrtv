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
		                  We'd like to thank you for deciding to use the Start Streaming. We enjoyed creating it and hope you enjoy using it to achieve your goals :)
		                </p>

		                <p>
		                  If you want something changed to suit your venture's needs better, drop us a line:
		                </p>

		                <a href="https://www.facebook.com/StartVideoStreaming/" target="_blank"><img class="aligncenter size-full wp-image-159 help-image" src="http://default.startstreaming.info/helpsocial/Facebook.png" alt="Facebook-100" width="100" height="100" /></a>
		                &nbsp;

		                <a href="https://twitter.com/startstream" target="_blank"><img class="size-full wp-image-155 alignleft help-image" src="http://default.startstreaming.info/helpsocial/twitter.png " alt="http://twitter.com/startstreaming" width="100" height="100" /></a>
		                &nbsp;

		                <a href="#" target="_blank"> <img class="wp-image-158 alignleft help-image" src="http://default.startstreaming.info/helpsocial/skype.png" alt="skype" width="100" height="100" /></a>
		                &nbsp;

		                <a href="mailto:mail@aravinth.net" target="_blank"><img class="size-full wp-image-153 alignleft help-image" src="http://default.startstreaming.info/helpsocial/mail.png" alt="Message-100" width="100" height="100" /></a>

			             &nbsp;


			             <p>As you know, we at Start Streaming believe in the honorware system. We share our products with anyone who asks for it without any commitments what-so-ever. But, if you think our product has added value to your venture, please consider <a href="#" target="_blank">paying</a> us the price of the product.  It will help us buy more Redbulls, create more features and launch the next version of Start Streaming for you to upgrade :) </p>

              			<a href="#" target="_blank"><img class="aligncenter help-image size-full wp-image-160" src="http://default.startstreaming.info/helpsocial/Money-Box-100.png" alt="Money Box-100" width="100" height="100" /></a>

						<p>Cheers!</p>

            		</div>

        		</div>
    		
    		</div>
        </div>

    </div>

</div>



@endsection