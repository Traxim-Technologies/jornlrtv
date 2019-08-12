<div class="up-next pt-0">

    <h4 class="sugg-head1">{{tr('playlists')}}</h4>

    <ul class="video-sugg">

        @if(count($play_all->video_tapes) > 0)

            @foreach($play_all->video_tapes as $key=>$playlist)

                <li class="sugg-list row">
                    
                    <div class="main-video">
                        
                        <div class="video-image">
                            
                            <div class="video-image-outer">
                                <a href="{{route('user.playlists.play_all' , ['playlist_id'=>$play_all->playlist_id,'playlist_type'=>'USER','play_next' => $key])}}">
                                    <img src="{{asset('streamtube/images/placeholder.gif')}}" data-src="{{$playlist->video_image}}" class="placeholder" />
                                </a>
                            </div>  
                            
                            @if($playlist->ppv_amount > 0)
                                @if($playlist->should_display_ppv)
                                    <div class="video_amount">
                                        {{tr('pay')}} - {{Setting::get('currency')}}{{$playlist->ppv_amount}}
                                    </div>
                                @endif
                            @endif

                            <div class="video_duration">
                                {{$playlist->duration}}
                            </div>
                        
                        </div>
                        
                        <!--video-image-->

                        <div class="sugg-head">
                            <div class="suggn-title">
                                <h5><a href="{{route('user.playlists.play_all' , ['playlist_id'=>$play_all->playlist_id,'playlist_type'=>'USER','play_next' => $key])}}">{{$playlist->title}}</a></h5>
                            </div>
                            <!--end of sugg-title-->

                            <span class="video_views">
                                <div>
                                    <a href="{{route('user.channel',$playlist->channel_id)}}">{{$playlist->channel_name}}</a>
                                </div>
                                <i class="fa fa-eye"></i> {{$playlist->watch_count}} {{tr('views')}} <b>.</b> 
                                {{ common_date($playlist->created) }} 
                            </span>

                            <br>

                            <span class="stars">
                                <a><i @if($playlist->ratings >= 1) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                <a><i @if($playlist->ratings >= 2) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                <a><i @if($playlist->ratings >= 3) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                <a><i @if($playlist->ratings >= 4) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                <a><i @if($playlist->ratings >= 5) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                            </span>                              
                        
                        </div>
                        <!--end of sugg-head-->

                    </div>
                    <!--end of main-video-->
                
                </li>
                <!--end of sugg-list-->
            @endforeach

            <span id="playlists_videos"></span>

            <div class="clearfix"></div>

            <div class="row" style="margin-top: 20px">

                <div id="playlist_video_content_loader" style="display: none;">

                    <h1 class="text-center"><i class="fa fa-spinner fa-spin" style="color:#ff0000"></i></h1>

                </div>

                <div class="clearfix"></div>

                <button class="pull-right btn btn-info mb-15" onclick="getPlaylistsList()" style="color: #fff">{{tr('view_more')}}</button>

                <div class="clearfix"></div>

           </div>

        @endif
    </ul>
</div>
<!--end of up-next-->