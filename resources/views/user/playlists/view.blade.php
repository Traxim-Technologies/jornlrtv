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

                        @if(Auth::check())
                        <!-- <a href="{{route('user.profile')}}"> -->
                            <img class="img-circle " width="70px" src="{{$playlist_details->user_picture}}" style="margin-right: 10px">
                            <h4 style="display: inline;">{{ $playlist_details->user_name }}</h4>
                        <!-- </a> -->
                        @endif

                        @if($playlist_details->is_my_channel && $playlist_type == PLAYLIST_TYPE_CHANNEL) 

                        <a class="share-new global_playlist_id pull-right" id="{{ $playlist_details->channel_id, PLAYLIST_TYPE_CHANNEL }}"><i class="fa fa-edit"></i><h4>{{ tr('edit') }}</h4></a>

                        @endif

                        @if($playlist_type == PLAYLIST_TYPE_USER ) 

                            <!-- <a class="share-new global_playlist_id pull-right" id="{{ $playlist_details->playlist_id, PLAYLIST_TYPE_USER }}"><i class="fa fa-edit"></i><h4>{{ tr('edit') }}</h4></a> -->
                           
                            <a class="share-new  pull-right" id="{{ $playlist_details->playlist_id, PLAYLIST_TYPE_USER }}"><h4>{{ tr('playlist_add_video') }}</h4></a>

                        @endif

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
                                            
                                            @if(Auth::check())
                                            
                                                @if($playlist_details->user_id == Auth::user()->id)                                                 
                                                <div class="cross-mark1">
                                                        
                                                    <a onclick="return confirm(&quot;{{ substr($video_tape_details->title, 0 , 15)}}.. {{tr('user_playlist_video_remove_confirm') }}&quot;)" href="{{route('user.playlists.video_remove' , ['video_tape_id' => $video_tape_details->video_tape_id, 'playlist_id' => $playlist_details->playlist_id])}}"><i class="fa fa-times" aria-hidden="true"></i></a>

                                                </div>
                                                @endif

                                                <!-- @if($playlist_details->is_my_channel && $playlist_type == PLAYLIST_TYPE_CHANNEL) 

                                                <button class="share-new global_playlist_id pull-right btn btn-info" style="color: #fff" id="{{ $playlist_details->channel_id, PLAYLIST_TYPE_CHANNEL }}">{{ tr('edit') }}</button>

                                                @endif

                                                @if($playlist_type == PLAYLIST_TYPE_USER ) 

                                                    <button class="share-new global_playlist_id pull-right btn btn-info" style="color: #fff" id="{{ $playlist_details->playlist_id, PLAYLIST_TYPE_USER }}">{{ tr('edit') }}</button>

                                                @endif -->

                                            @endif

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

        <!-- PLAYLIST POPUPSTART -->
        @if($playlist_type == PLAYLIST_TYPE_USER ) 
        <div class="modal fade global_playlist_id_modal" id="global_playlist_id_{{$playlist_details->playlist_id }}" role="dialog"> @endif

        @if($playlist_details->is_my_channel && $playlist_type == PLAYLIST_TYPE_CHANNEL) 

        <div class="modal fade global_playlist_id_modal" id="global_playlist_id_{{$playlist_details->channel_id }}" role="dialog"> @endif

            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">

                    <!-- if user logged in let create, update playlist -->

                    @if(Auth::check())

                        <div class="modal-header">

                            <button type="button" class="close" data-dismiss="modal">&times;</button>

                            <h4 class="modal-title">{{tr('save_to')}}</h4>

                        </div>

                        <div class="modal-footer">

                            <div class="more-content">

                                <div onclick="$('#create_playlist_form').toggle()">

                                    <label><i class="fa fa-plus"></i> {{tr('edit_playlist')}}</label>

                                </div>

                                <div class="" id="create_playlist_form" style="display: none">

                                    <div class="form-group">

                                        <input type="text" name="playlist_title" id="playlist_title" class="form-control" placeholder="{{tr('playlist_name_placeholder')}}" required value="{{ $playlist_details->title}}">

                                        <label for="video" class="control-label">{{tr('videos')}}</label>

                                        <div>

                                            <select id="video_tapes_id" name="video_tapes_id[]" class="form-control select2" data-placeholder="{{tr('select_video_tapes')}}" multiple style="width: 100% !important" required>

                                                @if(count($videos) > 0) 

                                                    @foreach($videos as $video_tapes_details) 

                                                        @if($video_tapes_details->is_approved == YES)

                                                        <option value="{{ $video_tapes_details->video_tape_id}}" @if($video_tapes_details->exist_in_playlists) selected @endif> {{ $video_tapes_details->title }}</option>

                                                        @endif 

                                                    @endforeach 

                                                @endif

                                            </select>

                                        </div>

                                        <div class="" style="display: none;">

                                            <label for="playlist_privacy">Privacy</label>
                                           
                                            <select id="playlist_privacy" name="playlist_privacy" class="form-control">
                                                <option value="PUBLIC">PUBLIC</option>
                                                <option value="PRIVETE">PRIVETE</option>
                                                <option value="UNLISTED">UNLISTED</option>
                                            </select>
                                        
                                        </div>
                                    
                                    </div>

                                    <button class="btn btn-primary" onclick='playlist_save({{$playlist_details->channel_id}} ,{{$playlist_details->playlist_id}})'>{{ tr('save')}}
                                    </button>

                                </div>

                            </div>

                        </div>

                        <!-- if user not logged in ask for login -->

                    @else

                        <div class="menu4 top nav-space">

                            <p>{{tr('signid_for_playlist')}}</p>

                            <a href="{{route('user.login.form')}}" class="btn btn-sm btn-primary">{{tr('login')}}</a>

                        </div>

                    @endif

                </div>
                <!-- modal content ends -->

            </div>

        </div>

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

    $(document).on('ready', function() {

        $("#copy-embed1").on("click", function() {
        
            $('#popup1').modal('hide');
        
        });

        $('.global_playlist_id').on('click', function(event) {

            event.preventDefault();

            var id = $(this).attr('id');

            $('#global_playlist_id_' + id).modal('show');

        });

    });


    function playlist_save(channel_id, playlist_id) {

        var title = $("#playlist_title").val();

        var privacy = $("#playlist_privacy").val();

        var video_tapes_id = $("#video_tapes_id").val();
       
        var playlist_type = '{{$playlist_type}}';

        var playlist_id = playlist_id ;

        alert(playlist_id);
        
        if (title == '') {

            alert("Title for playlist required");

        }

        if (video_tapes_id == null) {

            alert("Please Choose videos to create playlist");

        } else {

            $.ajax({

                url: "{{ route('user.channel.playlists.save') }}",

                data: {
                    title: title,
                    channel_id: channel_id,
                    privacy: privacy,
                    video_tapes_id: video_tapes_id,
                    playlist_id: playlist_id,
                    playlist_type: playlist_type
                },

                type: "post",

                success: function(data) {

                    if (data.success) {

                        $('#playlist_title').removeAttr('value');

                        $('#video_tapes_id').val(null).trigger('change');

                        $('#global_playlist_id_' + channel_id).modal('hide');
                       
                        $('#no_playlist').hide();

                        $('#new_playlist').append(data.new_playlist_content);

                        alert(data.message);

                        location.reload();

                    } else {

                        alert(data.error_messages);

                    }

                },

                error: function(data) {

                },

            })

        }

    }

</script>

@endsection

