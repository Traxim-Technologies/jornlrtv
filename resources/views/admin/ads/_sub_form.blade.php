<div class="col-md-12 form-group" style="margin-top: 10px;" id="adsDiv_{{$index}}">

     <div class="row">

          <div class="col-md-2">

               <label>{{tr('ad_type')}}</label>

               <br>

               <input type="checkbox" name="between_ad_type[{{$index}}]" id="between_ad_type" value="{{BETWEEN_AD}}" @if($b_ad->ad_type == BETWEEN_AD) checked @endif> {{tr('between_ad')}}

          </div>


          <div class="col-md-3">

               <label>{{tr('ad_time')}}</label>

               <input type="text" name="between_ad_time[{{$index}}]" id="between_ad_time" class="form-control" value="{{$b_ad->ad_time}}">

          </div>

          <div class="col-md-3">

               <label>{{tr('video_time')}}</label>

               <input type="text" class="form-control" name="between_ad_video_time[{{$index}}]" id="between_ad_video_time" value="{{$b_ad->video_time}}"/>

          </div>

          <div class="col-md-3">

               <label>{{tr('image')}}</label>

               <input type="file" name="between_ad_file[{{$index}}]" id="between_ad_file" accept="image/png,image/jpeg">

          </div>

          <div class="col-md-1">

               @if($index == 0)

                    <a href="javascript:void(0);" onclick="addQuestion({{$index}})"><i class="fa fa-plus-circle" title="Add Question"></i></a>

               @endif

               @if($index != 0)
                
                    <a href="javascript:void(0);" onclick="removeQuestion({{$index}})"><i class="fa fa-minus-circle" title="Remove Question"></i></a>

               @endif


          </div>


     </div>
    
</div>

