@extends('layouts.user')
@section('styles')
<style type="text/css">
.history-image{ 
    width: 30% !important;
}
.history-title {
    width: 65% !important;
}
</style>
@endsection
@section('content')

<div class="y-content">
    <div class="row y-content-row">

        @include('layouts.user.nav')

        <div class="page-inner col-sm-9 col-md-10 profile-edit">


            <div class="profile-content">
               
                <div class="row no-margin">

                    @include('notification.notify')

                    <div class="col-sm-7 profile-view">
                        <div class="edit-profile profile-view">
                            <div class="edit-form">

                                <h4 class="edit-head">{{tr('edit_profile')}}</h4>
                                
                                <div class="image-profile edit-image">
                                    @if(Auth::user()->picture)
                                    <img src="{{Auth::user()->picture}}" id="img_profile">
                                    @else
                                        <img src="{{asset('placeholder.png')}}" id="img_profile">
                                    @endif                               
                                </div><!--end of image-profile-->

                                <div class="editform-content"> 
                                    <form  action="{{ route('user.profile.save') }}" method="POST" enctype="multipart/form-data">

                                         <div class="form-group">
                                            <label for="exampleInputFile">{{tr('upload_image')}}</label>
                                            <input type="file" name="picture" class="form-control-file" accept="image/png, image/jpeg" id="exampleInputFile" aria-describedby="fileHelp" onchange="loadFile(this,'img_profile')">
                                            <p class="help-block">{{tr('image_validate')}} {{tr('image_square')}}</p>
                                        </div>

                                        <div class="form-group">
                                            <label for="username">{{tr('username')}}</label>
                                            <input required value="{{Auth::user()->name}}" name="name" type="text" class="form-control" id="username" aria-describedby="emailHelp" placeholder="Enter username">
                                        </div>

                                        @if(Auth::user()->login_by == 'manual')

                                            <div class="form-group">
                                                <label for="email">{{tr('email')}}</label>
                                                <input type="email" value="{{Auth::user()->email}}" name="email" disabled class="form-control" id="email" aria-describedby="emailHelp" placeholder="Enter email">
                                            
                                            </div>

                                        @endif

                                        <div class="form-group">
                                            <label for="mobile">{{tr('mobile')}}</label>
                                            <input type="mobile" value="{{Auth::user()->mobile}}" name="mobile" class="form-control" id="mobile" aria-describedby="emailHelp" placeholder="Enter mobile" maxlength="13">
                                            <small style="color:brown">{{tr('mobile_note')}}</small>
                                        </div>
                                              
                                        <div class="form-group">
                                            <label for="about">{{tr('about_me')}}</label>
                                            <textarea name="description" class="form-control" id="about" rows="3">{{Auth::user()->description}}</textarea>
                                        </div>
                                              
                                        <div class="change-pwd save-pro-btn">
                                            <button type="submit" class="btn btn-primary">{{tr('submit')}}</button>

                                            <a href="{{route('user.change.password')}}" class="btn btn-danger">{{tr('change_password')}}</a>

                                        </div>                                              

                                    </form>
                                </div><!--end of editform-content-->
                                    
                            </div><!--end of edit-form-->                           
                        </div><!--end of edit-profile-->
                    </div><!--profile-view end-->  


                    
                    @if(count($wishlist->data) > 0)
                        
                        <div class="mylist-profile col-sm-5">
                            <h4 class="mylist-head">{{tr('wishlist')}}</h4>

                            <ul class="history-list profile-history">

                                @foreach($wishlist->data as $i => $video)

                                    <li class="sub-list row no-margin">
                                        <div class="main-history">
                                            <div class="history-image">
                                                <a href="{{route('user.single' , $video->video_tape_id)}}"><img src="{{$video->video_tape->default_image}}"></a>      
                                                <div class="video_duration">
                                                    {{$video->video_tape->duration}}
                                                </div>                   
                                            </div><!--history-image-->

                                            <div class="history-title">
                                                <div class="history-head row">
                                                    <div class="cross-title">
                                                        <h5><a href="{{route('user.single' , $video->video_tape_id)}}">{{$video->video_tape->title}}</a></h5>
                                                         <span class="video_views">
                                                            <i class="fa fa-eye"></i> {{$video->video_tape->watch_count}} {{tr('views')}} 
                                                            <?php /*<b>.</b> 
                                                            {{$history->video_tape->created_at->diffForHumans()}}*/?>
                                                        </span>
                                                    </div> 
                                                    <div class="cross-mark">
                                                        <a onclick="return confirm('Are you sure?');" href="{{route('user.delete.wishlist' , array('wishlist_id' => $video->id))}}"><i class="fa fa-times" aria-hidden="true"></i></a>
                                                    </div><!--end of cross-mark-->                       
                                                </div> <!--end of history-head--> 

                                                <?php /*<div class="description">
                                                    <p>{{$video->description}}</p>
                                                </div> --><!--end of description-->  */?>

                                                <span class="stars">
                                                    <a href="#"><i @if($video->video_tape->ratings >= 1) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                    <a href="#"><i @if($video->video_tape->ratings >= 2) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                    <a href="#"><i @if($video->video_tape->ratings >= 3) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                    <a href="#"><i @if($video->video_tape->ratings >= 4) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                    <a href="#"><i @if($video->video_tape->ratings >= 5) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                </span>                                                       
                                            </div><!--end of history-title--> 
                                        </div><!--end of main-history-->
                                    </li>

                                @endforeach

               
                            </ul>                                
                        
                        </div><!--end of mylist-profile-->

                    @endif

                </div><!--end of profile-content row-->
            
            </div>

        </div>
    </div>
</div>

@endsection

@section('scripts')

<script type="text/javascript">
function loadFile(event, id){
    // alert(event.files[0]);
    var reader = new FileReader();
    reader.onload = function(){
      var output = document.getElementById(id);
      output.src = reader.result;
       //$("#imagePreview").css("background-image", "url("+this.result+")");
    };
    reader.readAsDataURL(event.files[0]);
}
</script>

@endsection