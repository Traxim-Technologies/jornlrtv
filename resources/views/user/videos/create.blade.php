@extends('layouts.user')
 
@section('styles')
<link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/wizard.css')}}">

<link rel="stylesheet" href="{{asset('admin-css/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css')}}">
@endsection

@section('content')

<div class="y-content">

  <div class="row content-row">

		@include('layouts.user.nav')

    <div class="page-inner">
        <!--      Wizard container        -->
          <div class="col-sm-10">
                <div class="wizard-container">
                    <div class="card wizard-card" data-color="red" id="wizard">
                        <form action="{{route('user.video_save')}}" method="post" id="video_form" enctype="multipart/form-data">
                    <!--        You can switch " data-color="blue" "  with one of the next bright colors: "green", "orange", "red", "purple"             -->

                          <div class="wizard-header">
                              <h3 class="wizard-title">
                                {{tr('upload_video')}}
                              </h3>
                               <h5>{{tr('video_short_notes')}}</h5>
                          </div>
                          <div class="wizard-navigation">
                              <ul>
                                  <li><a href="#details" data-toggle="tab">{{tr('video_details')}}</a></li>
                                  <li><a href="#captain" data-toggle="tab">{{tr('upload_video')}}</a></li>
                                  <li><a href="#select_image" data-toggle="tab">{{tr('select_image')}}</a></li>
                              </ul>
                          </div>

                            <div class="tab-content">
                                <div class="tab-pane" id="details">
                                  <div class="row">
                                    <div class="col-sm-12">
                                        <h4 class="info-text"> Let's start with the basic details.</h4>
                                        <input type="hidden" name="channel_id" id="channel_id" value="{{$id}}">

                                        <input type="hidden" name="id" id="main_id">
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-12">
                                        <label for="name" class="control-label">{{tr('title')}}</label>
                                        <div>
                                            <input type="text" required class="form-control" id="title" name="title" placeholder="{{tr('video_title')}}">
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                       
                                        <label for="video" class="control-label">{{tr('sub_title')}}</label>
                                        <div class="clearfix"></div>
                                        <div>
                                        <input type="file" id="subtitle" name="subtitle" style="width: 100%;overflow: hidden;" accept="text/plain">
                                        <p class="help-block">{{tr('subtitle_validate')}}</p>

                                        </div>
                                       
                                    </div>

                                    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            <label for="datepicker" class="">{{tr('18_users')}} * </label>

                                            <div class="clearfix"></div>

                                            <input type="checkbox" name="age_limit" value="1"> {{tr('yes')}}

                                            <p class="help-block">{{tr('age_limit_note')}}</p>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                      <div class="form-group">
                                            <label for="name" class="control-label">{{tr('publish_type')}}</label>&nbsp;&nbsp;
                                             <div class="clearfix"></div>
                                             <div>
                                              <input type="radio" onchange="checkPublishType(this.value)" name="video_publish_type" id="video_publish_type" value="{{PUBLISH_NOW}}" checked class='video_publish_type_class' required> {{tr('publish_now')}} &nbsp;
                                              <input type="radio" onchange="checkPublishType(this.value)" name="video_publish_type" id="video_publish_type" value="{{PUBLISH_LATER}}" class="video_publish_type_class" required /> {{tr('publish_later')}}
                                             </div>
                                       </div>
                                    </div>
                                    <div class="col-sm-2">
                                      <div class="form-group" style="display: none;" id="publish_time_div">
                                          <label for="datepicker" class="">{{tr('publish_time')}} * </label>
                                          <input type="text" name="publish_time" placeholder="dd-mm-yyyy hh:ii" class="form-control pull-right" id="datepicker">
                                      </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <label for="name" class="control-label">{{tr('description')}}</label>
                                        <div>
                                            <textarea placeholder="Description...." rows="5" required class="form-control" id="description" name="description" ></textarea>
                                        </div>
                                    </div>
                                   
                                  </div>
                                </div>
                                <div class="tab-pane" id="captain">
                                    <h4 class="info-text">{{tr('do_upload')}}</h4>
                                    <div class="row">
                                        <div class="dropify-wrapper" onclick="$('#video_file').click();return false;">
                                          <div class="dropify-message">
                                            <span class="file-icon">
                                              <i class="fa fa-cloud-upload"></i>
                                            </span>
                                            <p>{{tr('click_here')}}</p>
                                          </div>
                                          <div class="dropify-preview">
                                              <span class="dropify-render">
                                              </span>
                                          </div>
                                        </div>

                                        <input id="video_file" type="file" name="video" style="display: none;" accept="video/mp4" onchange="$('#submit_btn').click();" required>

                                        <br>
                                        <div class="progress" class="col-sm-12">
                                            <div class="bar"></div >
                                            <div class="percent">0%</div >
                                        </div>

                                        <input type="submit" name="submit" id="submit_btn" style="display: none">
                                    </div>
                                </div>
                                <div class="tab-pane" id="select_image">
                                    <div class="row">
                                        <h4 class="info-text">{{tr('select_image_short_notes')}}</h4>
                                        <div class="col-sm-12" id="select_image_div">

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="wizard-footer">
                                <div class="pull-right">

                                      <input type='button' class='btn btn-abort btn-fill btn-warning btn-wd' name='abort' value="{{tr('abort')}}" id="abort_btn" onclick="abortVideo();"/>

                                      <input type='button' class='btn btn-next btn-fill btn-danger btn-wd' name='next' value='Next' id="next_btn"/>

                                      <input type='button' class='btn btn-finish btn-fill btn-danger btn-wd' name='finish' value='Finish' onclick="redirect()" />
                                  </div>
                                  <div class="pull-left">
                                      <input type='button' class='btn btn-previous btn-fill btn-default btn-wd' name='previous' value='Previous' />
                                  </div>
                                  <div class="clearfix"></div>
                            </div>
                        </form>
                    </div>
                </div> <!-- wizard container -->
            </div>
      <div class="sidebar-back"></div> 
    </div>
    </div>
    		
</div>
<!-- <div class="overlay">
    <div id="loading-img"></div>
</div> -->
@endsection

@section('scripts')

<script src="{{asset('admin-css/plugins/bootstrap-datetimepicker/js/moment.min.js')}}"></script> 
<script type="text/javascript" src="{{asset('streamtube/js/jquery.bootstrap.js')}}"></script>
<script type="text/javascript" src="{{asset('streamtube/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('streamtube/js/material-bootstrap-wizard.js')}}"></script>
<script src="{{asset('admin-css/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js')}}"></script> 

<script src="{{asset('streamtube/js/jquery-form.js')}}"></script>

<script>

     $("#abort_btn").hide();

    function abortVideo() {

      var id = $("#main_id").val();

      /*if (id != '' && id != undefined) {


      } else {*/

        window.location.reload(true);

      //}

    }

   function redirect() {

      var e = $('#video_file');
      e.wrap('<form>').closest('form').get(0).reset();
      e.unwrap();

      var formData = new FormData($("#video_form")[0]);

      $.ajax({

          method : 'post',
          url : "{{route('user.upload_video_image')}}",
          data : formData,
          async: false,
          cache: false,
          contentType: false,
          processData: false,
          success : function(data) {
              if (data.id)  {
                  window.location.href = '/channel/'+$("#channel_id").val();
              } else {
                  console.log(data);
              }
          }
      });

      // window.location.href = '/channel/'+$("#channel_id").val();
   } 

   function removePicture(idx) {

      $("#image_div_id_"+idx).show();

      $("#preview_image_div_"+idx).hide();

      $("#preview_"+idx).hide();

      var e = $('#img_'+idx);
      e.wrap('<form>').closest('form').get(0).reset();
      e.unwrap();


      return false;

   }

   function loadFile(event, id, idx){

       $("#image_div_id_"+idx).hide();

       $("#preview_image_div_"+idx).show();

       $("#preview_"+idx).show();

       $("#remove_circle_"+idx).show();

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

    function saveAsDefault(main_id, value, idx, count, image) {

        for(var i = 0; i < count; i++) {

          $("#btn_"+i).removeClass('btn-success'); 

          $("#btn_"+i).addClass('btn-danger');

          $("#btn_"+i).html("Make Default"); 

        }

        if ($("#btn_"+idx).find('btn-danger')) {

          $("#btn_"+idx).removeClass('btn-danger');

          $("#btn_"+idx).addClass('btn-success');

          $("#btn_"+idx).html("Marked Default"); 

        } else {

          $("#btn_"+idx).removeClass('btn-success');

          $("#btn_"+idx).addClass('btn-danger');

          $("#btn_"+idx).html("Make Default"); 
        }

        console.log(value);

        console.log(idx);

        $.ajax({

          type: "post",

          url : "{{route('user.save_default_img')}}",

          data : {id : value, idx : idx, img : image, video_tape_id : main_id},

          success : function(data) {

              console.log("Object "+data);

              console.log(data);
          },

          error:function(data) {

            console.log(data);

          }

        })

    }

    function checkPublishType(val){
        $("#publish_time_div").hide();
        $("#datepicker").prop('required',false);
        $("#datepicker").val("");
        if(val == 2) {
            $("#publish_time_div").show();
            $("#datepicker").prop('required',true);
        }
    }
    var now = new Date();

    now.setHours(now.getHours())
    $('#datepicker').datetimepicker({
        autoclose:true,
        format : 'dd-mm-yyyy hh:ii',
        startDate:now,
    });


    /*$('form').submit(function () {
       window.onbeforeunload = null;
    });
    window.onbeforeunload = function() {
         return "Data will be lost if you leave the page, are you sure?";
    };*/


    var bar = $('.bar');
    var percent = $('.percent');

    $('form').ajaxForm({
        beforeSend: function() {
            // alert("BeforeSend");
            var percentVal = '0%';
            bar.width(percentVal)
            percent.html(percentVal);
            $("#next_btn").val("Wait Progressing...");
            $("#next_btn").attr('disabled', true);
            $("#video_file").attr('disabled', true);
            $("#abort_btn").show();
        },
        uploadProgress: function(event, position, total, percentComplete) {
            console.log(total);
            console.log(position);
            console.log(event);
            var percentVal = percentComplete + '%';
            bar.width(percentVal)
            percent.html(percentVal);
            if (percentComplete == 100) {
                $("#next_btn").val("Video Uploading...");
                // $(".overlay").show();
                $("#next_btn").attr('disabled', true);
                $("#video_file").attr('disabled', true);
            }
        },
        complete: function(xhr) {
            bar.width("100%");
            percent.html("100%");
           //  $(".overlay").show();
            $("#next_btn").val("Next");
            $("#next_btn").attr('disabled', false);
            $("#video_file").removeAttr('disabled');
            console.log(xhr);
            $("#abort_btn").hide();
        },
        error : function(xhr) {
            console.log(xhr);
        },
        success : function(xhr) {
            // $(".overlay").hide();
            if(xhr.data) {

                console.log("inside " +xhr.data);

                $("#select_image_div").html(xhr.path);

                $("#main_id").val(xhr.data.id);

                $("#abort_btn").hide();

                $(".btn-next").click();

            } else {
                console.log(xhr);
            }
        }
    }); 


</script>

@endsection