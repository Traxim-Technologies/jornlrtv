<?php use App\AdsDetail; ?>

@include('notification.notify')

	<div class="row">

    <div class="col-md-12">

        <div class="box box-primary">

            <div class="box-header label-primary">
                <b style="font-size:18px;">@yield('title')</b>
                <a href="{{route('admin.ads_index')}}" class="btn btn-default pull-right">{{tr('video_ads')}}</a>
            </div>

            <form  action="{{route('admin.save_ads')}}" method="POST" enctype="multipart/form-data" role="form">

                <input type="hidden" name="video_tape_id" id="video_tape_id" value="{{$vModel->id}}">

                <input type="hidden" name="video_ad_id" id="id" value="{{$model->id}}">

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

                                <input type="hidden" name="pre_ad_type_id" id="pre_ad_id" value="{{$preAd->id}}">

                                <br>

                    			<input type="checkbox" name="pre_ad_type" id="pre_ad_type" value="{{PRE_AD}}"
                                @if($preAd->ad_type == PRE_AD) checked @endif onchange="getCheckBoxValue(this.id, this.value)"> {{tr('pre_ad')}}

                    		</div>


                    		<div class="col-md-3">

                    			<label>{{tr('ad_time')}}</label>

                    			<input type="text" name="pre_ad_time" id="pre_ad_time" class="form-control" value="{{$preAd->ad_time}}">

                    		</div>


                    		<div class="col-md-6">

                    			<label>{{tr('image')}}</label>

                    			<input type="file" name="pre_ad_file" id="pre_ad_file" accept="image/png,image/jpeg" onchange="loadFile(this, 'pre_ad_preview')">

                                <br>

                                <img src="{{$preAd->file ? $preAd->file : asset('images/default-ad.jpg')}}" style="width:100px;height: 100px;" id="pre_ad_preview"/>

                    		</div>


                    	</div>
                        
                    </div>


                    <div class="col-md-12 form-group" style="margin-top: 10px;">

                    	<div class="row">

                    		<div class="col-md-2">

                    			<label>{{tr('ad_type')}}</label>

                                <input type="hidden" name="post_ad_type_id" id="post_ad_id" value="{{$postAd->id}}">

                                <br>

                    			<input type="checkbox" name="post_ad_type" id="post_ad_type" value="{{POST_AD}}" @if($postAd->ad_type == POST_AD) checked @endif onchange="getCheckBoxValue(this.id, this.value)"> {{tr('post_ad')}}

                    		</div>


                    	   <div class="col-md-3">

                    			<label>{{tr('ad_time')}}</label>

                    			<input type="text" name="post_ad_time" id="post_ad_time" class="form-control" value="{{$postAd->ad_time}}">

                    		</div>


                    		<div class="col-md-6">

                    			<label>{{tr('image')}}</label>

                    			<input type="file" name="post_ad_file" id="post_ad_file" accept="image/png,image/jpeg" onchange="loadFile(this, 'post_ad_preview')">


                                <br>
                                
                                <img src="{{$postAd->file ? $postAd->file : asset('images/default-ad.jpg')}}" style="width:100px;height: 100px;" id="post_ad_preview"/>

                    		</div>


                    	</div>
                        
                    </div>      



                    @if(count($betweenAd) > 0 && $model->id)

                        @foreach($betweenAd as $index => $b_ad) 

                            @include('admin.ads._sub_form')

                        @endforeach

                    @else 



                        <?php $b_ad = new AdsDetail; ?>

                        @include('admin.ads._sub_form')


                    @endif


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
                
                 // console.log("Duration" + playerInstance.getDuration());
                

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

    $('#adsDiv_'+index).find('input:text').val('');

    /*var e = $('#adsDiv_'+index);
    e.wrap('<form>').closest('form').get(0).reset();
    e.unwrap();    
*/
}

function loadFile(event, id){
    var reader = new FileReader();
    reader.onload = function(){
      var output = document.getElementById(id);
      output.src = reader.result;
       //$("#imagePreview").css("background-image", "url("+this.result+")");
    };
    reader.readAsDataURL(event.files[0]);
}

function getCheckBoxValue(id, ad_type) {
 
    if(!($('#' + id).is(":checked"))) {

        if(ad_type == 1) {

            $('#pre_ad_time').val('');

            $("#pre_ad_preview").hide();

            var e = $('#pre_ad_file');
            e.wrap('<form>').closest('form').get(0).reset();
            e.unwrap(); 

        } 

        if(ad_type == 2) {

            $('#post_ad_time').val('');

            $("#post_ad_preview").hide();

            var e = $('#post_ad_file');
            e.wrap('<form>').closest('form').get(0).reset();
            e.unwrap(); 

        }

    }

}
</script>


@endsection