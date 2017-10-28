<footer>
    <div class="footer1 row">
        <div class="col-sm-2 foot-div">
            <div class="tube-image">
                @if(Setting::get('site_logo'))
                    <img src="{{Setting::get('site_logo')}}">
                @else
                    <img src="{{asset('logo.png')}}">
                @endif
            </div>                                 
        </div><!--end of col-sm-2-->

        <div class="col-sm-10 foot-content">

            <ul class="term">
                <?php $pages = pages();?>
                @if (count($pages) > 0)
                    @foreach($pages as $page) 
                        <li><a target="_blank" href="{{route('page_view', $page->id)}}" style="text-transform: capitalize;">{{$page->title}}</a></li>
                    @endforeach
                @endif
                <li><a target="_blank" href="{{url('/')}}" target="_blank">&#169; 2017 @if(Setting::get('site_name')) {{Setting::get('site_name') }} @else {{tr('site_name')}} @endif</a></li>
            </ul>
        </div>   
    </div><!--end of footer1-->
</footer>
