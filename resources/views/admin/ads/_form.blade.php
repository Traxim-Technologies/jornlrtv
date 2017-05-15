@include('notification.notify')

	<div class="row">

    <div class="col-md-12">

        <div class="box box-primary">

            <div class="box-header label-primary">
                <b style="font-size:18px;">{{tr('create_ad')}}</b>
                <a href="{{route('admin.ads_index')}}" class="btn btn-default pull-right">{{tr('video_ads')}}</a>
            </div>

            <form  action="{{route('admin.save_ads')}}" method="POST" enctype="multipart/form-data" role="form">


                <div class="box-body">

                	<div class="col-md-6">
	                    @if($vModel->video)
	                        <?php $url = $vModel->video; ?>
	                        <div id="main-video-player"></div>
	                    @else
	                        <div class="image">
	                            <img src="{{asset('error.jpg')}}" alt="{{Setting::get('site_name')}}">
	                        </div>
	                    @endif
	            	</div>

	            	<div class="clearfix"></div>

                    <div class="col-md-12 form-group" style="margin-top: 10px;">

                    	<div class="row">

                    		<div class="col-md-2">

                    			<label>{{tr('ad_type')}}</label>

                    			<input type="hidden" name="pre_ad_video_time" id="pre_ad_video_time" />

                    			<br>

                    			<input type="checkbox" name="pre_ad_type" id="pre_ad_type" value="{{PRE_AD}}"> {{tr('pre_ad')}}

                    		</div>


                    		<div class="col-md-3">

                    			<label>{{tr('ad_time')}}</label>

                    			<input type="text" name="pre_ad_time" id="pre_ad_time" class="form-control">

                    		</div>


                    		<div class="col-md-6">

                    			<label>{{tr('image')}}</label>

                    			<input type="file" name="pre_ad_file" id="pre_ad_file" accept="image/png,image/jpeg">

                    		</div>


                    	</div>
                        
                    </div>


                    <div class="col-md-12 form-group" style="margin-top: 10px;">

                    	<div class="row">

                    		<div class="col-md-2">

                    			<label>{{tr('ad_type')}}</label>

                    			<input type="hidden" name="post_ad_video_time" id="post_ad_video_time" />

                    			<br>

                    			<input type="checkbox" name="post_ad_type" id="post_ad_type" value="{{POST_AD}}"> {{tr('post_ad')}}

                    		</div>


                    		<div class="col-md-3">

                    			<label>{{tr('ad_time')}}</label>

                    			<input type="text" name="post_ad_time" id="post_ad_time" class="form-control">

                    		</div>


                    		<div class="col-md-6">

                    			<label>{{tr('image')}}</label>

                    			<input type="file" name="post_ad_file" id="post_ad_file" accept="image/png,image/jpeg">

                    		</div>


                    	</div>
                        
                    </div>


                    @include('admin.ads._sub_form')

                    <div id="questionAdd"></div>

                    <input type="hidden" name="totalQuestion" id="totalIndex" value="0">



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

<script src="{{asset('jwplayer/jwplayer.js')}}"></script>

<script>jwplayer.key="{{envfile('JWPLAYER_KEY')}}";</script>

<script type="text/javascript">
    
    jQuery(document).ready(function(){


            console.log('Inside Video');
                
            console.log('Inside Video Player');

            @if($url)

                var playerInstance = jwplayer("main-video-player");


                
                    var videoPath = "{{$videoPath}}";
                    var videoPixels = "{{$video_pixels}}";

                    var path = [];

                    var splitVideo = videoPath.split(',');

                    var splitVideoPixel = videoPixels.split(',');


                    for (var i = 0 ; i < splitVideo.length; i++) {
                        path.push({file : splitVideo[i], label : splitVideoPixel[i]});
                    }
                    playerInstance.setup({
                        sources: path,
                        image: "{{$vModel->default_image}}",
                        width: "100%",
                        height: "200px !important",
                        aspectratio: "16:9",
                        primary: "flash",
                        controls : true,
                        "controlbar.idlehide" : false,
                        controlBarMode:'floating',
                        "controls": {
                          "enableFullscreen": false,
                          "enablePlay": false,
                          "enablePause": false,
                          "enableMute": true,
                          "enableVolume": true
                        },
                        // autostart : true,
                        "sharing": {
                            "sites": ["reddit","facebook","twitter"]
                          }
                    });
                
                

            @endif

           
    });


function addQuestion(index) {

    index = $('#totalIndex').val();

    $.ajax({
        type : "post",
        url : "{{route('admin.add.between_ads')}}",
        data : {index:index},
        success : function(data) {

            $('#questionAdd').append(data);

            index = parseInt($('#totalIndex').val())+1;

            $('#totalIndex').val(index);

        },
        error : function(data) {

        }


    });
}

function removeQuestion(index) {

    console.log("Remove Ad");

    $('#adsDiv_'+index).hide();

}
</script>


@endsection