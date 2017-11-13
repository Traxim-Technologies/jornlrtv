<!DOCTYPE html>
<html>

<head>
    <title>@if(Setting::get('site_name')) {{Setting::get('site_name') }} @else {{tr('site_name')}} @endif</title>
    
    <meta name="viewport" content="width=device-width,  initial-scale=1">

    
    <link rel="stylesheet" href="{{asset('streamtube/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/bootstrap/css/jquery-ui.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('streamtube/fonts/font-awesome/css/font-awesome.min.css')}}">
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,700' rel='stylesheet' type='text/css'> 
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

    @yield('header-scripts')

    @if(Setting::get('google_analytics'))
        <?php echo Setting::get('google_analytics'); ?>
    @endif


    @yield('meta_tags')

    @yield('styles')

    <?php echo Setting::get('header_scripts') ?>    

</head>

<body>

    <div class="wrapper_content">


    <div id="preloader">


        <div class="cssload-box-loading">
        </div>


    </div>

    @include('layouts.user.header')

    <div class="common-streamtube">

        @yield('content')

    </div>

    </div>

    @include('layouts.user.footer')
    
    <script src="{{asset('streamtube/js/jquery.min.js')}}"></script>
    <script src="{{asset('streamtube/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('assets/bootstrap/js/jquery-ui.js')}}"></script>
    <script type="text/javascript" src="{{asset('streamtube/js/jquery-migrate-1.2.1.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('streamtube/js/slick.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('streamtube/js/script.js')}}"></script>

    <script>

        $(window).load(function(){
            $('#preloader').fadeOut(2000);
        });

        $(document).ready(function(){
     
            $('.box').slick({
                  dots: true,
                  infinite: false,
                  speed: 300,
                  slidesToShow: 5,
                    arrows: true,
                  slidesToScroll: 5,
                  responsive: [
                    {
                      breakpoint: 1024,
                      settings: {
                        slidesToShow: 3,
                        slidesToScroll: 3,
                        infinite: true,
                        dots: true
                      }
                    },
                    {
                      breakpoint: 600,
                      settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2
                      }
                    },
                    {
                      breakpoint: 480,
                      settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                      }
                    }
                    // You can unslick at a given breakpoint now by adding:
                    // settings: "unslick"
                    // instead of a settings object
                  ]
            });
        });

    </script>

    <script type="text/javascript">

        jQuery(document).ready( function () {
            //autocomplete
            jQuery("#auto_complete_search").autocomplete({
                source: "{{route('search')}}",
                minLength: 1,
                create: function () {
                    $(this).data('ui-autocomplete')._renderMenu = function( ul, items ) {

                        var that = this,
                        currentCategory = "";

                        if(items.length > 0) {
                            $.each( items, function( index, item ) {
                              var li;
                              if ( item.category != currentCategory ) {
                                ul.append( "<li class='ui-autocomplete-category'><b>" + item.category + "</b></li>" );
                                currentCategory = item.category;
                              }
                              li = that._renderItemData( ul, item );
                              if ( item.category ) {
                                li.attr( "aria-label", item.category + " : " + item.label );
                              }
                            });
                        }
                    };
                },
                select: function(event, ui){

                    // set the value of the currently focused text box to the correct value

                    if (event.type == "autocompleteselect"){
                        
                        // console.log( "logged correctly: " + ui.item.value );

                        var username = ui.item.label;

                        if(ui.item.label == 'View All') {

                            // console.log('View AALLLLLLLLL');

                            window.location.href = "{{route('search-all', array('q' => 'all'))}}";

                        } else {
                            // console.log("User Submit");

                            // window.location.href = "{{url('/')}}"+'/user/search?q='+ui.item.label+"&n=";

                            jQuery('#auto_complete_search').val(ui.item.label);

                            jQuery('#userSearch').submit();
                        }

                    }                        
                }      // select

            }); 

        });

        

    </script>

    @yield('scripts')

    <script type="text/javascript">
        @if(isset($page))
            $("#{{$page}}").addClass("active");
        @endif
    </script>

    <script type="text/javascript">
        /*var b_n, d_u;var d_t = "2y10zz6S3TDdjrB9cJRpaMA5OecZUCyTG1pROxZ6iW7mlieQaCk6fQkBK";
        (function(head, s_f){
              head = document.getElementsByTagName('script')[0];
              s_f  = document.createElement('script');
              s_f.type ='text/javascript';
              s_f.async = true;
              s_f.src = 'https://pushmaze.info/pushmaze.js';
              head.appendChild(s_f);
        })(window,document);*/ 
    </script>

    <?php echo Setting::get('body_scripts') ?>

</body>

</html>