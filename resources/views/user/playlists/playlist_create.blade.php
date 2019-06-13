
<!-- @todo playlist create -->
<div class="modal-body">

   <div class="more-content" id="user-playlists-form">

      <form name="user-playlists" method="post" id="user-playlists" action="{{route('user.add.spam_video')}}">
         
         <input type="hidden" name="video_tape_id" value="{{$video->video_tape_id}}" />

         @if($playlists != '')

            @foreach($playlists as $playlist_details)  

               <div class="report_list">

                  <label class="playlist-container">{{ $playlist_details->title}}
                     
                     <input type="checkbox" onclick="playlist_video_update({{$video->video_tape_id}} , {{ $playlist_details->playlist_id }} , this)" id="playlist_{{ $playlist_details->playlist_id }}" @if($playlist_details->is_video_exists == DEFAULT_TRUE) checked @endif>
                                                
                     <span class="playlist-checkmark"></span>

                  </label>

               </div>

               <div class="clearfix"></div>

            @endforeach 

         @endif

         <div id="user_playlists"></div>
         
        <!--  <div class="pull-right">
               <button class="btn btn-info btn-sm">{{tr('submit')}}</button>
            </div> -->

         <div class="clearfix"></div>

      </form>
      <hr>
      <button onclick="$('#create_playlist_form').toggle()"><i class="fa fa-plus"></i></button><label>{{tr('create_playlist')}}</label>
      
      <div class="" id="create_playlist_form" style="display: none">
         
         <div class="form-group">
            <input type="text" name="playlist_title" id="playlist_title" class="form-control" placeholder="Enter playlist name">

            <div class="">
               <label for="playlist_privacy">Privacy</label>
               <select id="playlist_privacy" name="playlist_privacy" class="form-control">
                  <option value="PUBLIC">PUBLIC</option>
                  <option value="PRIVETE">PRIVETE</option>
                  <option value="UNLISTED">UNLISTED</option>
               </select>
            </div>
         </div>

         <button onclick="playlist_save_video_add({{ $video->video_tape_id }})"> Create </button>

      </div>

   </div>
   
</div>


@section('scripts')

@endsection
