<!DOCTYPE html>
<html>

<head>
    <title>{{Setting::get('site_name' , "Start Streaming")}}</title>
    
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

    @include('layouts.user.footer')    
    
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

    <!-- Start of LiveChat (www.livechatinc.com) code -->
    <script type="text/javascript">
    window.__lc = window.__lc || {};
    window.__lc.license = 8284281;
    (function() {
    var lc = document.createElement('script'); lc.type = 'text/javascript'; lc.async = true;
    lc.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'cdn.livechatinc.com/tracking.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(lc, s);
    })();
    </script>
    <!-- End of LiveChat code -->
</body>



</html>