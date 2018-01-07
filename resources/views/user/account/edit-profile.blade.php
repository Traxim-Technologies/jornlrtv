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

<link rel="stylesheet" href="{{asset('admin-css/plugins/datepicker/datepicker3.css')}}">

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
                                  <!--  <div class="edit-image-change">
                                        <span class="fa-stack fa-lg">
                                            <i class="fa fa-square fa-stack-2x"></i>
                                            <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                        </span>
                                   </div>  -->
                                   <p class="help-block">{{tr('image_validate')}} {{tr('image_square')}}</p>
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

                                        <?php

                                        if (!empty(Auth::user()->dob) && Auth::user()->dob != "0000-00-00") {

                                            $dob = date('d-m-Y', strtotime(Auth::user()->dob));

                                        } else {

                                            $dob = "00-00-0000";
                                        }


                                        ?>


                                        <div class="form-group">
                                            <label for="mobile">{{tr('dob')}}</label>
                                            <input type="text" value="{{$dob}}" name="dob" class="form-control" placeholder="{{tr('enter_dob')}}" maxlength="13" id="dob">
                                        </div>

                                              
                                        <div class="form-group">
                                            <label for="about">{{tr('about_me')}}</label>
                                            <textarea name="description" class="form-control" id="about" rows="3">{{Auth::user()->description}}</textarea>
                                        </div>
                                              
                                        <div class="change-pwd save-pro-btn">
                                            <button type="submit" class="btn btn-primary">{{tr('submit')}}</button>

                                            @if (Auth::user()->login_by == 'manual')
                                            <a href="{{route('user.change.password')}}" class="btn btn-danger">{{tr('change_password')}}</a>
                                            @endif

                                        </div>                                              

                                    </form>
                                </div><!--end of editform-content-->
                                    
                            </div><!--end of edit-form-->                           
                        </div><!--end of edit-profile-->
                    </div><!--profile-view end-->  


                    
                    @if(count($wishlist->items) > 0)
                        
                        <div class="mylist-profile col-sm-5">
                            <h4 class="mylist-head">{{tr('wishlist')}}</h4>

                            <ul class="history-list profile-history">

                                @foreach($wishlist->items as $i => $video)

                                    <li class="sub-list row no-margin">
                                        <div class="main-history">
                                            <div class="history-image">
                                                <a href="{{$video->url}}"><img src="{{$video->video_image}}"></a>      
                                                @if($video->ppv_amount > 0)
                                                    @if(!$video->ppv_status)
                                                        <div class="video_amount">

                                                        {{tr('pay')}} - {{Setting::get('currency')}}{{$video->ppv_amount}}

                                                        </div>
                                                    @endif
                                                @endif
                                                <div class="video_duration">
                                                    {{$video->duration}}
                                                </div>                   
                                            </div><!--history-image-->

                                            <div class="history-title">
                                                <div class="history-head row">
                                                    <div class="cross-title1">
                                                        <h5><a href="{{$video->url}}">{{$video->title}}</a></h5>
                                                         <span class="video_views">
                                                            <div><a href="{{route('user.channel',$video->channel_id)}}">{{$video->channel_name}}</a></div>
                                                            <i class="fa fa-eye"></i> {{$video->watch_count}} {{tr('views')}} 
                                                            <b>.</b> 
                                                            {{$video->created_at}}
                                                        </span>
                                                    </div> 
                                                    <div class="cross-mark1">
                                                        <a onclick="return confirm('Are you sure?');" href="{{route('user.delete.wishlist' , array('video_tape_id' => $video->video_tape_id))}}"><i class="fa fa-times" aria-hidden="true"></i></a>
                                                    </div><!--end of cross-mark-->                       
                                                </div> <!--end of history-head--> 

                                            

                                                <span class="stars">
                                                    <a><i @if($video->ratings >= 1) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                    <a><i @if($video->ratings >= 2) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                    <a><i @if($video->ratings >= 3) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                    <a><i @if($video->ratings >= 4) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                    <a><i @if($video->ratings >= 5) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
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

            <div class="sidebar-back"></div> 
        </div>
    </div>
</div>

@endsection

@section('scripts')

<script src="{{asset('admin-css/plugins/datepicker/bootstrap-datepicker.js')}}"></script> 


<script type="text/javascript">


var max_age_limit = "{{Setting::get('max_register_age_limit' , 18)}}";

max_age_limit = max_age_limit ? "-"+max_age_limit+"y" : "-15y";

$('#dob').datepicker({
    autoclose:true,
    format : 'dd-mm-yyyy',
    endDate: max_age_limit,
});


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