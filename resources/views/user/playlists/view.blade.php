@extends('layouts.user')

@section('content')

<div class="y-content">
   
   <div class="row content-row">

      @include('layouts.user.nav')
         
      <div class="history-content page-inner col-sm-9 col-md-10">
        
        @include('notification.notify')


            <div class="slide-area1 col-sm-4 col-md-4">
                
                <div class="new-history">
                
                    <div class="content-head">

                        @if($video_tapes)
                   
                            <a href="{{route('user.single', $video_tapes[0]->video_tape_id)}}">
                            
                                <img src="{{asset('streamtube/images/placeholder.gif')}}" data-src="{{$video_tapes[0]->video_image}}" class="slide-img1 placeholder" />

                            </a>

                        @else

                   
                            <a href="#">
                            
                                <img src="{{asset('streamtube/images/playlist.jpg')}}" data-src="{{asset('streamtube/images/playlist.jpg')}}" class="slide-img1 placeholder" />

                            </a>

                        @endif



                            <h3> {{$playlist_details->title }} </h3>
                            <p> {{tr('total_videos')}} : {{$playlist_details->total_videos }} </p>

                            <p> {{tr('last_updated')}} : {{ date('d M y', strtotime($playlist_details->updated_at)) }}</p>
                            <hr>

                            <a href="{{route('user.profile')}}"><img class="img-circle " width="70px" src="{{$playlist_details->user_picture}}" style="margin-right: 10px">
                                <h4 style="display: inline;">{{$playlist_details->user_name}}</h4>
                            </a>

                        

                    </div>

                </div>
             
            </div>

            <div class="slide-area1 col-sm-8 col-md-8">
                
                <div class="new-history">
                      
                    <div class="content-head">
                    
                        <div>
                          
                            <h4 class="bold no-margin-top">
                               {{tr('playlist_videos')}} - {{$playlist_details->title}}
                            </h4>
                 
                        </div>              
                    
                    </div>

                    @if(count($video_tapes) > 0)

                        <ul class="history-list">

                           @foreach($video_tapes as $i => $video_tape_details)

                            <li class="sub-list row">

                                <div class="main-history">

                                    <div class="history-image">

                                        <a href="{{route('user.single', $video_tape_details->video_tape_id)}}">
                                            <img src="{{asset('streamtube/images/placeholder.gif')}}" data-src="{{$video_tape_details->video_image}}" class="slide-img1 placeholder" />
                                        </a>

                                        @if($video_tape_details->ppv_amount > 0)
                                          <!--  @---if(!$video_tape_details->pay_per_view_status)
                                                <div class="video_amount">

                                                {{tr('pay')}} - {{Setting::get('currency')}}{{$video_tape_details->ppv_amount}}

                                                </div>
                                            @---endif -->

                                        @endif

                                        <div class="video_duration">
                                            {{$video_tape_details->duration}}
                                        </div> 

                                    </div>

                                    <div class="history-title">
                                       
                                       <div class="history-head row">
                                          
                                          <div class="cross-title1">
                                             
                                             <h5><a href="{{route('user.single', $video_tape_details->video_tape_id)}}">{{$video_tape_details->title}}</a></h5>
                                             
                                             <span class="video_views">
                                                 <div><a href="{{route('user.channel',$video_tape_details->channel_id)}}">{{$video_tape_details->channel_name}}</a></div>
                                                 <i class="fa fa-eye"></i> {{$video_tape_details->watch_count}} {{tr('views')}} <b></b> 
                                             </span> 

                                          </div> 

                                          <div class="cross-mark1">
                                            
                                             <a onclick="return confirm(&quot;{{ substr($video_tape_details->title, 0 , 15)}}.. {{tr('user_playlist_video_remove_confirm') }}&quot;)" href="{{route('user.playlists.video_remove' , ['video_tape_id' => $video_tape_details->video_tape_id, 'playlist_id' => $playlist_details->playlist_id])}}"><i class="fa fa-times" aria-hidden="true"></i></a>
                                          </div>
                                          <!--end of cross-mark-->  

                                            <!-- @todo save to playlist : on click pop playlist to add and create playlist -->
                                       </div> <!--end of history-head--> 

                                       <div class="description">
                                            <?php //$video_tape_details->description ?>
                                       </div><!--end of description--> 
                                                                            
                                    </div><!--end of history-title--> 
                                       
                                </div><!--end of main-history-->
                           
                            </li>   
                            
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
                          
                       </ul>

                    @else
                       
                       <!-- <img src="{{asset('images/no-result.jpg')}}" class="img-responsive auto-margin"> -->

                    @endif
                      
                </div>

                <div class="sidebar-back"></div> 
            
            </div>

              
        <!-- <div class="new-history">
              
            <div class="content-head">
            
               <div>
                  
                  <center><h4 class="bold no-margin-top">
                       {{tr('playlist_videos')}} - {{$playlist_details->title}}
                  </h4></center>
         
               </div>              
            
            </div>
       
            <img src="{{asset('images/no-result.jpg')}}" class="img-responsive auto-margin">
    
        </div> -->

      </div>

   </div>

</div>

@endsection

@section('scripts')

<script>

    var stopPageScroll = false;

    var searchDataLength = "{{count($video_tapes)}}";

    function getPlaylistsList() {

        if (searchDataLength > 0) {

            playlists_videos(searchDataLength);

        }
    }

    function playlists_videos(cnt) {

        $.ajax({

            type: "post",
            async: false,
            url: "{{route('user.playlists.view')}}",
            data: {
                skip: cnt,
                playlist_id: "{{$playlist_details->playlist_id}}",
                is_json: 1
            },

            beforeSend: function() {

                $("#playlist_video_content_loader").fadeIn();
            },

            success: function(response) {

                $.each(response.data.video_tapes, function(key,videoTapeDetails) { 

                    // console.log(JSON.stringify(videoTapeDetails));

                    var redirect_url = "/video/"+videoTapeDetails.video_tape_id;

                    var channel_redirect_url = "/channel/"+videoTapeDetails.channel_id;

                    var messageTemplate = '';

                    messageTemplate = '<li class="sub-list row">';

                    messageTemplate += '<div class="main-history">';

                    messageTemplate += '<div class="history-image">';

                    messageTemplate += '<a href="'+redirect_url+'">';

                    messageTemplate += '<img src="'+videoTapeDetails.default_image+'" data-src="'+videoTapeDetails.default_image+'" class="slide-img1 placeholder" />';

                    messageTemplate += '</a>';

                    if(videoTapeDetails.ppv_amount > 0) {
                        
                        if(!videoTapeDetails.pay_per_view_status) {

                            messageTemplate += '<div class="video_amount">';
                            
                            messageTemplate += 'PAY -'+videoTapeDetails.currency+' '+videoTapeDetails.ppv_amount;

                            messageTemplate += '</div>';

                        }
                    }

                    messageTemplate += '<div class="video_duration">'+videoTapeDetails.duration+'</div>';

                    messageTemplate += '</div>';

                    messageTemplate += '<div class="history-title">';

                    messageTemplate += '<div class="history-head row">';

                    messageTemplate += '<div class="cross-title1">';

                    messageTemplate += '<h5><a href="'+redirect_url+'">'+videoTapeDetails.title+'</a></h5>';

                    messageTemplate += '<span class="video_views">';

                    messageTemplate += '<div><a href="'+channel_redirect_url+'">'+videoTapeDetails.channel_name+'</a></div>';

                    messageTemplate += '<i class="fa fa-eye"></i> '+videoTapeDetails.watch_count+' Views </span>';

                    messageTemplate += '</div>';

                    messageTemplate += '<div class="cross-mark1">';

                    var user_playlist_delete_confirm = "{{tr('user_playlist_video_remove_confirm') }}";

                    var playlist_remove_url = "/playlists/video_remove?video_tape_id="+videoTapeDetails.video_tape_id+"&playlist_id="+{{$playlist_details->playlist_id}}+'"';

                    messageTemplate += '<a onclick="return confirm(&quot;'+user_playlist_delete_confirm+'&quot;);" href="'+playlist_remove_url+'"> <i class="fa fa-times" aria-hidden="true"></i> </a>';

                    messageTemplate += '</div>';

                    messageTemplate += '</div>';

                    messageTemplate += '</div>';

                    messageTemplate += '</div>';

                    messageTemplate += '</li>';
                    
                    $('#playlists_videos').append(messageTemplate);

                });

                if (response.data.video_tapes.length == 0) {

                    stopPageScroll = true;

                } else {

                    stopPageScroll = false;

                    searchDataLength = parseInt(searchDataLength) + response.data.video_tapes.length;

                }

            },

            complete: function() {

                $("#playlist_video_content_loader").fadeOut();

            },

            error: function(data) {

            },

        });

    }

</script>
@endsection

