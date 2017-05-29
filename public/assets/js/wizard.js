$(document).ready(function () {
    //Initialize tooltips
    $('.nav-tabs > li a[title]').tooltip();
    
    //Wizard
    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {

        var $target = $(e.target);
    
        if ($target.parent().hasClass('disabled')) {
            return false;
        }
    });

    $(".next-step").click(function (e) {

        var $active = $('.wizard .nav-tabs li.active');
        $active.next().removeClass('disabled');
        nextTab($active);

    });
    $(".prev-step").click(function (e) {

        var $active = $('.wizard .nav-tabs li.active');
        prevTab($active);

    });
});

function nextTab(elem) {
    $(elem).next().find('a[data-toggle="tab"]').click();
}
function prevTab(elem) {
    $(elem).prev().find('a[data-toggle="tab"]').click();
}

/**
 * Function Name : saveVideoDetails()
 * To save first step of the job details
 * 
 * @var step        Step Position 1
 *
 * @return Json response
 */
function saveVideoDetails(step) {
   var title = $("#title").val();
   var datepicker = $("#datepicker").val();
   var rating = $("#rating").val();
   var description = $("#description").val();
   var reviews = $("#reviews").val();
   var video_publish_type = $("#video_publish_type").val();

   if (title == '') {
        alert('Title Should not be blank');
        return false;
   }
   if (datepicker == '' && video_publish_type == 2) {
        alert('Publish Time Should not be blank');
        return false;
   }

   if (rating == '') {
        alert('Ratings Should not be blank');
        return false;
   }
   if (description == '') {
        alert('Description Should not be blank');
        return false;
   }
   if (reviews == '') {
        alert('Reviews Should not be blank');
        return false;
   }
   $("#"+step).click();
}


/**
 * Function Name : saveCategory()
 * To save second step of the job details
 * 
 * @var category_id Category Id (Dynamic values)
 * @var step        Step Position 2
 *
 * @return Json response
 */
function saveCategory(channel_id, step) {
    $("#channel_id").val(channel_id);
    // displaySubCategory(category_id, step);
    $("#"+step).click();
}


var bar = $('.bar');
var percent = $('.percent');



$('form').ajaxForm({
    beforeSend: function() {
        var percentVal = '0%';
        bar.width(percentVal)
        percent.html(percentVal);
        $("#next_btn").text("Wait Progressing...");
        $("#next_btn").attr('disabled', true);
    },
    uploadProgress: function(event, position, total, percentComplete) {
        console.log(total);
        console.log(position);
        console.log(event);
        var percentVal = percentComplete + '%';
        bar.width(percentVal)
        percent.html(percentVal);
        if (percentComplete == 100) {
            $("#next_btn").text("Video Uploading...");
            $(".overlay").show();
            $("#next_btn").attr('disabled', true);
        }
    },
    complete: function(xhr) {
        bar.width("100%");
        percent.html("100%");
        $(".overlay").hide();
        $("#next_btn").text("Redirecting...");
        $("#next_btn").attr('disabled', false);
        console.log(xhr);
    },
    error : function(xhr) {
        alert(xhr);
    },
    success : function(xhr) {
        console.log(xhr);


        $(".overlay").hide();

        if(xhr.data) {

            console.log("Inside " +xhr.data);

            $("#select_image_div").html(xhr.path);

            $("#next_btn").val("Next");

            $("#next_btn").attr('disabled', false);

            $("#main_id").val(xhr.data.id);

            $("#btn-next").click();

        } else {
            console.log(xhr);
        }
    }
}); 

function redirect() {

      var e = $('#video_file');
      e.wrap('<form>').closest('form').get(0).reset();
      e.unwrap();

      var formData = new FormData($("#video-upload")[0]);

      window.onbeforeunload = null;

      $.ajax({

          method : 'post',
          url : upload_video_image_url,
          data : formData,
          async: false,
          contentType: false,
          processData: false,
          success : function(data) {
              if (data.id)  {
                  console.log(data);
                  window.location.href = '/admin/view/video?id='+data.id;
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

       $("#remove_circle_"+idx).show();

       $("#preview_"+idx).show();

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

          url : save_img_url,

          data : {id : value, idx : idx, img : image, video_tape_id : main_id},

          success : function(data) {

              console.log(data);
          },

          error:function(data) {

            console.log(data);

          }

        })

    }