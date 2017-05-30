<div class="col-md-12 form-group" style="margin-top: 10px;" id="adsDiv_{{$index}}">

     <div class="row">

          <div class="col-md-2">

               <label>{{tr('ad_type')}}</label>

               <input type="hidden" name="between_ad_type_id[{{$index}}]" id="between_ad_type_id" value="{{$b_ad->id}}">

               <br>

               <input type="checkbox" name="between_ad_type[{{$index}}]" id="between_ad_type" value="{{BETWEEN_AD}}" @if($b_ad->ad_type == BETWEEN_AD) checked @endif> {{tr('between_ad')}}

          </div>


          <div class="col-md-3">

               <label>{{tr('ad_time')}} ({{tr('in_sec')}})</label>

               <input type="text" name="between_ad_time[{{$index}}]" id="between_ad_time" class="form-control" value="{{$b_ad->ad_time}}">

          </div>

          <div class="col-md-3">

               <label>{{tr('video_time')}}</label>

               <input type="text" class="form-control" name="between_ad_video_time[{{$index}}]" id="between_ad_video_time" value="{{$b_ad->video_time}}" placeholder="00:00:00" />

          </div>

          <div class="col-md-3">

               <label>{{tr('ad')}}</label>

               <select id="ad_id_{{$index}}" name="between_parent_ad_id[{{$index}}]" class="form-control">
                     <option value="">{{tr('select_ad')}}</option>
                     @foreach($ads as $ad)
                         <option value="{{$ad->id}}" @if($ad->id == $b_ad->ad_id) selected @endif>{{$ad->name}}</option>
                     @endforeach
                 </select>

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

