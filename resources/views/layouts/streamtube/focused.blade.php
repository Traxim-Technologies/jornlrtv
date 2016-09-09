<!DOCTYPE html>
<html>

<head>
    <title>{{Setting::get('site_name' , "Live Stream")}}</title>
    
    <meta name="viewport" content="width=device-width,  initial-scale=1">
    <link rel="stylesheet" href="{{asset('streamtube/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('streamtube/css/jquery-ui.css')}}">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome/css/font-awesome.min.css">
    <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,700' rel='stylesheet' type='text/css'> 
    <link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/slick.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/slick-theme.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/style.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/responsive.css')}}">

    <link rel="shortcut icon" type="image/png" href="{{Setting::get('site_icon' , asset('img/favicon.png'))}}"/>

    <style type="text/css">
        .ui-autocomplete{
          z-index: 99999;
        }
    </style>

    @yield('styles')

</head>

<body>

    @include('layouts.user.focused-nav')

    @yield('content')

    <footer class="signup-footer">
        <div class="footer1 row">
            <div class="col-sm-3 foot-div">
                <div class="tube-image">
                    <img src="images/logo.png">
                </div>                                 
            </div><!--end of col-sm-3-->

            <div class="col-sm-9 foot-content">
                <ul class="about">
                    <li><a href="#">About</a></li>
                    <li><a href="#">Copyright</a></li>
                    <li><a href="#">Press</a></li>
                    <li><a href="#">Creators</a></li>
                    <li><a href="#">Advertise</a></li>
                    <li><a href="#">Developers</a></li>
                    <li><a href="#">+YouTube</a></li>
                </ul>
            
                <ul class="term">
                    <li><a href="#">Terms</a></li>
                    <li><a href="#">Privacy</a></li>
                    <li><a href="#">Policy&amp;Safety</a></li>
                    <li><a href="#">Send Feedback</a></li>
                    <li><a href="#">Try Something New!</a></li>
                    <li><a href="#">&#169; 2016 YouTube, LLC</a></li>
                </ul>
            </div>   
        </div><!--end of footer1-->
    </footer>    
    
    <script src="{{asset('streamtube/js/jquery.min.js')}}"></script>
    <script src="{{asset('streamtube/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('streamtube/js/jquery-ui.js')}}"></script>
    <script type="text/javascript" src="{{asset('streamtube/js/jquery-migrate-1.2.1.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('streamtube/js/slick.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('streamtube/js/script.js')}}"></script>

    <script type="text/javascript">

        jQuery(document).ready( function () {
            //autocomplete
            jQuery("#auto_complete_search").autocomplete({
                source: "{{route('search')}}",
                minLength: 1,
                select: function(event, ui){

                    // set the value of the currently focused text box to the correct value

                    if (event.type == "autocompleteselect"){
                        
                        // console.log( "logged correctly: " + ui.item.value );

                        var username = ui.item.value;

                        if(ui.item.value == 'View All') {

                            // console.log('View AALLLLLLLLL');

                            window.location.href = "{{route('search', array('q' => 'all'))}}";

                        } else {
                            // console.log("User Submit");

                            jQuery('#auto_complete_search').val(ui.item.value);

                            jQuery('#userSearch').submit();
                        }

                    }                        
                }      // select

            }); 

        });

</script>

    @yield('scripts')
</body>



</html>