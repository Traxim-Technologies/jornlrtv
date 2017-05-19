@extends('layouts.user')

@section('styles')

<!-- Add css file and inline css here -->
<link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/custom-style.css')}}"> 

<style type="text/css">

.form-control {

	border-radius: 0px;
}

</style>

@endsection 


@section('content')

<div class="y-content">
    <div class="row content-row">

        @include('layouts.user.nav')

        <div class="page-inner col-sm-9 col-md-10">

            @include('notification.notify')

            <form action="{{route('user.save_channel')}}" method="post" enctype="multipart/form-data">

			<div class="branded-page-v2-top-row">
				<div class="branded-page-v2-header channel-header yt-card">
					<div id="gh-banner">
						<div id="c4-header-bg-container" class="c4-visible-on-hover-container  has-custom-banner">
							<div class="hd-banner">
								<div class="hd-banner-image">
									<img src="{{asset('images/default-cover-image.jpg')}}" id="cover_preview" class="st_cover_photo_img" id="cover_preview"/>
								</div>
							</div>
							<div id="header-links">
								<button class="st_button" onclick="$('#cover').click();return false"><i class="fa fa-plus-circle"></i>&nbsp;{{tr('add_cover_photo')}}</button>
							</div>
							<div class="st_photo_div">
								<img class="channel-header-profile-image" src="{{asset('images/default.png')}}" title="Channel Profile Photo" alt="Channel Profile Photo" id="picture_preview">
								<div class="st_photo">
									<button class="st_profile_btn" onclick="$('#picture').click();return false"><i class="fa fa-plus-circle"></i></button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="slide-area recom-area abt-sec des-crt">
				<div class="abt-sec-head description-create">

					<input type="file" style="display: none;" name="picture" id="picture" onchange="loadFile(this, 'picture_preview');">
            		<input type="file" style="display: none;" name="cover" id="cover" onchange="loadFile(this, 'cover_preview');">
            		
            		<input type="hidden" name="id" id="id">

            		<input type="hidden" name="user_id" value="{{Auth::user()->id}}">
					
					<div class="des-box">

						<h5>{{tr('title')}}</h5>

						<input type="text" name="name" id="title" class="form-control" />

						<h5>{{tr('description')}}</h5>

						<textarea class="form-control description" id="description" name="description"></textarea>
						<div class="btn-create">
							<button type="reset" name="reset" class="btn btn-danger ">{{tr('reset')}}</button>
							<button id="done-create" name="submit" class="btn btn-primary">{{tr('submit')}}</button>
						</div>

					</div>

				</div>
			</div>

			</form>

        </div>
    </div>
</div>

@endsection

@section('scripts')

<!-- Add Js files and inline js here -->

<script type="text/javascript">
function loadFile(event, id){
    // alert(event.files[0]);
    var reader = new FileReader();
    reader.onload = function(){
      var output = document.getElementById(id);
      // alert(output);
      output.src = reader.result;
      //$("#c4-header-bg-container .hd-banner-image").css("background-image", "url("+this.result+")");
    };
    reader.readAsDataURL(event.files[0]);
}
</script>

@endsection