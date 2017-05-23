@extends('layouts.user')

@section('styles')

<style type="text/css">

.switch {
    display: inline-block;
    height: 34px;
    position: relative;
    width: 60px;
}
.switch input {
    display: none;
}
.slider {
    background-color: #ccc;
    bottom: 0;
    cursor: pointer;
    left: 0;
    position: absolute;
    right: 0;
    top: 0;
    transition: all 0.4s ease 0s;
}
.slider::before {
    background-color: white;
    bottom: 4px;
    content: "";
    height: 26px;
    left: 4px;
    position: absolute;
    transition: all 0.4s ease 0s;
    width: 26px;
}
input:checked + .slider {
    background-color: #51af33;
}
input:focus + .slider {
    box-shadow: 0 0 1px #2196f3;
}
input:checked + .slider::before {
    transform: translateX(26px);
}
.slider.round {
    border-radius: 34px;
}
.slider.round::before {
    border-radius: 50%;
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
                    <div class="col-sm-7 profile-view">
                        <div class="edit-profile profile-view">
                            <div class="profile-details">
                                <div class="sub-profile">
                                    <h4 class="edit-head">{{tr('profile')}}</h4>

                                    <div class="image-profile">
                                        @if(Auth::user()->picture)
                                            <img src="{{Auth::user()->picture}}">
                                        @else
                                            <img src="{{asset('placeholder.png')}}">
                                        @endif                                
                                    </div><!--end of image-profile-->

                                    <div class="profile-title">
                                        <h3>{{Auth::user()->name}}</h3>
                                        
                                        @if(Auth::user()->login_by == 'manual')
                                            <h4>{{Auth::user()->email}}</h4>
                                        @endif
                                        
                                        @if(Auth::user()->user_type)
                                            <?php $subscription_details = get_expiry_days(Auth::user()->id);?>
                                            <p style="color:#cc181e">The Pack will Expiry within <b>{{$subscription_details['days']}} days (Paid ${{$subscription_details['amount']}})</b></p>
                                        @endif
                                        <p>{{Auth::user()->mobile}}</p>  
                                        <p>{{Auth::user()->description}}</p>
                                    </div><!--end of profile-title-->
                                    <form>
                                    <br>
                                        <div class="change-pwd edit-pwd edit-pro-btn">

                                            <!-- @if(envfile('PAYPAL_ID') && envfile('PAYPAL_SECRET'))

                                                <a href="{{route('paypal' , Auth::user()->id)}}" class="btn btn-warning">{{tr('payment')}}</a>
                                                 
                                            @endif -->

                                            
                                            <label class="switch" style="margin-right: 10px;" title="{{Auth::user()->ads_status ? tr('disable_ad') : tr('enable_ad')}}">
                                                <input id="change_adstatus_id" type="checkbox" @if(Auth::user()->ads_status) checked @endif onchange="change_adstatus(this.value)">
                                                <div class="slider round"></div>
                                            </label>

                                            <div class="clearfix"></div>

                                             <a href="{{route('user.subscriptions')}}" class="btn btn-warning">{{tr('subscriptions')}}</a>


                                            <a href="{{route('user.update.profile')}}" class="btn btn-primary">{{tr('edit_profile')}}</a>
                                            
                                            @if(Auth::user()->login_by == 'manual')
                                                <a href="{{route('user.change.password')}}"
                                            class="btn btn-danger">{{tr('change_password')}}</a>

                                            @endif
                                        </div> 
                                    </form>                                
                                </div><!--end of sub-profile-->                            
                            </div><!--end of profile-details-->                           
                        </div><!--end of edit-profile-->

                       
                    </div><!--profile-view end--> 


                    <?php // $wishlist = wishlist(Auth::user()->id); ?>
                    
                    @if(count($wishlist->data) > 0)
                        
                        <div class="mylist-profile col-sm-5">
                            <h4 class="mylist-head">{{tr('wishlist')}}</h4>

                            <ul class="history-list profile-history">

                                @foreach($wishlist->data as $i => $video)

                                    <li class="sub-list row no-margin">
                                        <div class="main-history">
                                            <div class="history-image">
                                                <a href="{{route('user.single' , $video->video_tape_id)}}"><img src="{{$video->video_tape->default_image}}"></a>                        
                                            </div><!--history-image-->

                                            <div class="history-title">
                                                <div class="history-head row">
                                                    <div class="cross-title">
                                                        <h5><a href="{{route('user.single' , $video->video_tape_id)}}">{{$video->video_tape->title}}</a></h5>
                                                        <p class="duration">{{tr('duration')}}: {{$video->video_tape->duration}}</p>
                                                    </div> 
                                                    <div class="cross-mark">
                                                        <a onclick="return confirm('Are you sure?');" href="{{route('user.delete.wishlist' , array('wishlist_id' => $video->id))}}"><i class="fa fa-times" aria-hidden="true"></i></a>
                                                    </div><!--end of cross-mark-->                       
                                                </div> <!--end of history-head--> 

                                                

                                                <?php 

                                                /* <div class="description">
                                                    <p>{{$video->video_tape->description}}</p>
                                                </div> 

                                                 <span class="stars">
                                                    <a href="#"><i @if($video->ratings > 1) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                    <a href="#"><i @if($video->ratings > 2) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                    <a href="#"><i @if($video->ratings > 3) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                    <a href="#"><i @if($video->ratings > 4) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                    <a href="#"><i @if($video->ratings > 5) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                </span>   */?>                                                    
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

<script>
    
    function change_adstatus(val) {

        var url = "{{route('user.ad_request')}}";

        var id = "{{Auth::user()->id}}";
        var token = "{{Auth::user()->token}}";

        $.ajax({
            url : url,
            method : "POST",
            data : {id : id , token : token, status : val},
            success : function(result) {
                console.log(result);

                if (result == true) {
                    window.location.reload();
                }
            }

        });

    }
</script>
@endsection