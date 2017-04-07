<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@if(Setting::get('site_name')) {{Setting::get('site_name') }} @else {{tr('site_name')}} @endif</title>

        <meta name="description" content="">
        <meta name="author" content="">

        <link href="{{asset('adult/css/bootstrap.min.css')}}" rel="stylesheet">
        <link rel="stylesheet" href="{{asset('adult/css/jquery-ui.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('streamtube/fonts/font-awesome/css/font-awesome.min.css')}}">
        <link href="{{asset('adult/css/style.css')}}" rel="stylesheet">

        <link rel="shortcut icon" type="image/png" href="{{Setting::get('site_icon' , asset('img/favicon.png'))}}"/>

        <style type="text/css">
            .ui-autocomplete{
              z-index: 99999;
            }
        </style>

        @if(Setting::get('google_analytics'))
            <?php echo Setting::get('google_analytics'); ?>
        @endif

        <meta property="og:locale" content="en_US" />
        <meta property="og:type" content="article" />
        <meta property="og:title" content="@if(Setting::get('site_name')) {{Setting::get('site_name') }} @else {{tr('site_name')}} @endif />
        <meta property="og:description" content="The best solution to start up a video streaming venture!" />
        <meta property="og:url" content="" />
        <meta property="og:site_name" content="@if(Setting::get('site_name')) {{Setting::get('site_name') }} @else {{tr('site_name')}} @endif />
        <meta property="og:image" content="{{Setting::get('site_icon')}}" />

        <meta name="twitter:card" content="summary"/>
        <meta name="twitter:description" content="The best solution to start up a video streaming venture!"/>
        <meta name="twitter:title" content="@if(Setting::get('site_name')) {{Setting::get('site_name') }} @else {{tr('site_name')}} @endif/>
        <meta name="twitter:image:src" content="@if(Setting::get('site_icon')) {{ Setting::get('site_icon') }} @else {{asset('favicon.png') }} @endif"/>

        @yield('styles')

    </head>

    <body>

        <div class="container-fluid">

            @include('layouts.user.nav')

            <div class="row page-content">

                <div class="container">

                    <div class="row">

                        @yield('content')

                    </div>

                </div>

            </div>

            @include('layouts.user.footer')

        </div>

        <script src="{{asset('adult/js/jquery.min.js')}}"></script>
        <script src="{{asset('adult/js/bootstrap.min.js')}}"></script>
        <script src="{{asset('assets/bootstrap/js/jquery-ui.js')}}"></script>
        <script src="{{asset('adult/js/scripts.js')}}"></script>

        @yield('scripts')


        <script type="text/javascript">
                $("#{{$page}}").addClass("active");
        </script>

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

                                window.location.href = "{{route('search-all', array('q' => 'all'))}}";

                            } else {
                                // console.log("User Submit");

                                jQuery('#auto_complete_search').val(ui.item.value);
                                jQuery('[name=key]').val(ui.item.value);

                                jQuery('#userSearch').submit();
                            }

                        }
                    }      // select

                });

            });

        </script>

        <!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/586a34df1aad8833902f6578/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->

    </body>
</html>
