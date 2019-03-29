
<div class="row">

    <div class="col-md-12">
        
        @include('notification.notify')

        <div class="box box-primary">

            <div class="box-header label-primary">
                <b style="font-size:18px;">@yield('title')</b>
                <a href="{{route('admin.custom.live.index')}}" class="btn btn-default pull-right">{{tr('custom_live_videos')}}</a>
            </div>

            <form action="{{route('admin.custom.live.save')}}" method="POST" enctype="multipart/form-data" role="form">

                <div class="box-body">

                    <input type="hidden" name="custom_live_video_id" id="id" value="{{$custom_live_video_details->id}}">

                    <input type="hidden" name="timezone" value="" id="userTimezone">                        
                    <div class="col-md-12">

                        <div class="form-group">
                            <label for="title">{{tr('title')}} *</label>
                            
                            <input type="text" maxlength="255" required class="form-control" id="title" name="title" placeholder="{{tr('title')}}" value="{{$custom_live_video_details->title}}">
                        </div>
                    </div>

                    <div class="clearfix"></div>
                    
                        <div class="col-md-6">

                            <div class="form-group"> 
                                <label for="rtmp_video_url">{{tr('rtmp_video_url')}} *</label>
                                <input type="text" required name="rtmp_video_url" class="form-control" id="rtmp_video_url" placeholder="{{tr('rtmp_video_url')}}" value="{{$custom_live_video_details->rtmp_video_url}}">
                            </div>                        
                        </div>                        
                        
                        <div class="col-md-6">
                            <div class="form-group">   

                                <label for="hls_video_url">{{tr('hls_video_url')}} *</label>

                                <input type="text" required name="hls_video_url" class="form-control" id="hls_video_url" placeholder="{{tr('hls_video_url')}}" value="{{$custom_live_video_details->hls_video_url}}">
                            </div>
                        </div>
                    
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">{{tr('description')}} *</label>
                                <textarea name="description" id="description" style="width: 100%;padding:5px;">{{$custom_live_video_details->description}}</textarea>
                            </div>
                        </div>
                        
                        <div class="clearfix"></div>
                       
                        <div class="col-md-12">
                            <div class="form-group">

                                <label for="image">{{tr('image')}} *</label>

                                <input type="file" id="image" accept="image/png,image/jpeg" name="image" placeholder="{{tr('image')}}" style="display:none" onchange="loadFile(this,'default_img')">
                                
                                <div>
                                    <img src="{{($custom_live_video_details->id) ? $custom_live_video_details->image : asset('images/320x150.png')}}" style="width:150px;height:75px;" onclick="$('#image').click();return false;" id="default_img"/>
                                </div>
                                
                                <p class="help-block">{{tr('image_validate')}} {{tr('rectangle_image')}}</p>

                            </div>
                        </div>
                        
                    </div>

                <div class="box-footer">

                    <button type="reset" class="btn btn-danger">{{tr('cancel')}}</button>
                    <button type="submit" class="btn btn-success pull-right">{{tr('submit')}}</button>
                </div>

            </form>
        
        </div>

    </div>

</div>


@section('scripts')

<script>
function loadFile(event, id){
    // alert(event.files[0]);
    var reader = new FileReader();
    reader.onload = function(){
      var output = document.getElementById(id);
      // alert(output);
      output.src = reader.result;
       //$("#imagePreview").css("background-image", "url("+this.result+")");
    };
    reader.readAsDataURL(event.files[0]);
}

</script>

@endsection