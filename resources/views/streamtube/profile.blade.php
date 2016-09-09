@extends('layouts.user')

@section('content')

<div class="y-content">
    <div class="row y-content-row">
        @include('layouts.user.nav')

        <div class="page-inner col-sm-9 col-md-10 profile-edit">
            
            <div class="profile-content">
                <div class="row no-margin">
                    <div class="col-sm-7 profile-view">
                        <div class="edit-profile profile-view">
                            <div class="profile-details">
                                <div class="sub-profile">
                                    <h4 class="edit-head">{{tr('profile')}}</h4>

                                    <div class="image-profile">
                                        <img src="{{Auth::user()->picture}}">                                
                                    </div><!--end of image-profile-->

                                    <div class="profile-title">
                                        <h3>{{Auth::user()->name}}</h3>
                                        <h4>{{Auth::user()->email}}</h4>
                                        <p>{{Auth::user()->mobilr}}</p>  
                                        <p>{{Auth::user()->description}}</p>                                
                                    </div><!--end of profile-title-->
                                    <form>
                                    <br>
                                        <div class="change-pwd edit-pwd edit-pro-btn">
                                            <a href="{{route('user.update.profile')}}" class="btn btn-primary">{{tr('edit_profile')}}</a>
                                            
                                            <a href="{{route('user.change.password')}}"
                                            class="btn btn-danger">{{tr('change_password')}}</a>
                                        </div> 
                                    </form>                                
                                </div><!--end of sub-profile-->                            
                            </div><!--end of profile-details-->                           
                        </div><!--end of edit-profile-->

                       
                    </div><!--profile-view end--> 


                    <?php $wishlist = wishlist(Auth::user()->id); ?>
                    
                    @if(count($wishlist))
                        
                        <div class="mylist-profile col-sm-5">
                            <h4 class="mylist-head">{{tr('wishlist')}}</h4>

                            <ul class="history-list profile-history">

                                @foreach($wishlist as $i => $video)

                                    <li class="sub-list row no-margin">
                                        <div class="main-history">
                                            <div class="history-image">
                                                <a href="{{route('user.single' , $video->admin_video_id)}}"><img src="{{$video->default_image}}"></a>                        
                                            </div><!--history-image-->

                                            <div class="history-title">
                                                <div class="history-head row">
                                                    <div class="cross-title">
                                                        <h5><a href="{{route('user.single' , $video->admin_video_id)}}">{{$video->title}}</a></h5>
                                                        <p class="duration">{{tr('duration')}}: {{$video->duration}}</p>
                                                    </div> 
                                                    <div class="cross-mark">
                                                        <a onclick="return confirm('Are you sure?');" href="{{route('user.delete.wishlist' , array('wishlist_id' => $video->wishlist_id))}}"><i class="fa fa-times" aria-hidden="true"></i></a>
                                                    </div><!--end of cross-mark-->                       
                                                </div> <!--end of history-head--> 

                                                <!-- <div class="description">
                                                    <p>{{$video->description}}</p>
                                                </div> --><!--end of description--> 

                                                <span class="stars">
                                                    <a href="#"><i @if($video->rating > 1) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                    <a href="#"><i @if($video->rating > 2) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                    <a href="#"><i @if($video->rating > 3) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                    <a href="#"><i @if($video->rating > 4) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                    <a href="#"><i @if($video->rating > 5) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
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