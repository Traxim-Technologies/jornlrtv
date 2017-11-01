@extends('layouts.user')


@section('meta_tags')

<meta property="og:locale" content="en_US" />
<meta property="og:type" content="article" />
<meta property="og:title" content="{{$video->title}}" />
<meta property="og:description" content="{{$video->description}}" />
<meta property="og:url" content="" />
<meta property="og:site_name" content="@if(Setting::get('site_name')) {{Setting::get('site_name') }} @else {{tr('site_name')}} @endif" />
<meta property="og:image" content="{{$video->default_image}}" />

<meta name="twitter:card" content="summary"/>
<meta name="twitter:description" content="{{$video->description}}"/>
<meta name="twitter:title" content="{{$video->title}}"/>
<meta name="twitter:image:src" content="{{$video->default_image}}"/>

@endsection

@section('styles')


<link rel="stylesheet" href="{{asset('assets/css/star-rating.css')}}">

<link rel="stylesheet" href="{{asset('assets/css/toast.style.css')}}">


<style type="text/css">

.sub-comhead .rating-md {

    font-size: 11px;

}

.thumb-class {

    cursor:pointer;
    text-decoration:none;
}

.common-youtube {
    min-height: 0px !important;
}
textarea[name=comments] {
    resize: none;
}

#timings {
    padding: 5px;
}
.ad_progress {
    position: absolute;
    bottom: 0px;
    width: 100%;
    opacity: 0.8;
    background: #000;
    color: #fff;
    font-size: 12px;
}

.progress-bar-div {
    width: 100%;
    height: 5px;
    background: #e0e0e0;
    /*padding: 3px;*/
    border-radius: 3px;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, .2);
    
}

.progress-bar-fill-div {
    display: block;
    height: 5px;
    background: #cc181e;
    border-radius: 3px;
    /*transition: width 250ms ease-in-out;*/
    /*transition : width 10s ease-in-out;*/

}

.small-box {
    border-radius: 2px;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    display: block;
    margin-bottom: 20px;
    position: relative;
    color: #fff;    
}

.bg-green{
    background-color: #00a65a !important;
}

.bg-aqua {
    background-color: #00c0ef !important;
}

.small-box h3, .small-box p {
    z-index: 5;
}
.small-box h3 {
    font-size: 38px;
    font-weight: bold;
    margin: 0 0 10px;
    padding: 0;
    white-space: nowrap;
}

.small-box > .inner {
    padding: 10px;
}

.small-box p {
    font-size: 15px;
}

.small-box .icon {
    color: rgba(0, 0, 0, 0.15);
    font-size: 90px;
    position: absolute;
    right: 10px;
    top: -10px;
    transition: all 0.3s linear 0s;
    z-index: 0;
}

.small-box > .small-box-footer {
    background: rgba(0, 0, 0, 0.1) none repeat scroll 0 0;
    color: rgba(255, 255, 255, 0.8);
    display: block;
    padding: 3px 0;
    position: relative;
    text-align: center;
    text-decoration: none;
    z-index: 10;
}
</style>

@endsection

@section('content')

    <div class="y-content">

        <div class="row y-content-row">

            @include('layouts.user.nav')

            <div class="page-inner col-sm-9 col-md-10 profile-edit">
            
                <div class="profile-content">

                    @include('notification.notify')

                    <div class="row no-margin">

                        <div class="col-sm-12 col-md-8 play-video">

                            @include('user.videos.streaming')

                            <div class="main-content">
                                <div class="video-content">
                                    <div class="details">
                                        <div class="video-title">
                                            <div class="title row">
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12">
                                                    <h3>{{$video->title}}</h3>
                                                    <div class="views pull-left">
                                                        {{$video->watch_count}} {{tr('views')}}
                                                    </div>
                                                    <div class="pull-right">
                                                       <?php /*( <i class="fa fa-commenting"> <span id="video_comment_count">{{get_video_comment_count($video->admin_video_id)}}</span></i> {{tr('comments')}} ) */?>

                                                            @if (Auth::check())
                                                            <a class="thumb-class" onclick="likeVideo({{$video->admin_video_id}})"><i class="fa fa-thumbs-up fa"></i>&nbsp;<span id="like_count">{{$like_count}}</span></a>&nbsp;&nbsp;&nbsp;

                                                            <a class="thumb-class" onclick="dislikeVideo({{$video->admin_video_id}})"><i class="fa fa-thumbs-down"></i>&nbsp;<span id="dislike_count">{{$dislike_count}}</span></a>

                                                            @else 
                                                            
                                                            <a class="thumb-class"><i class="fa fa-thumbs-up"></i>&nbsp;<span>{{$like_count}}</span></a>&nbsp;&nbsp;&nbsp;

                                                            <a class="thumb-class"><i class="fa fa-thumbs-down"></i>&nbsp;<span>{{$dislike_count}}</span></a>

                                                            @endif
                                                                
                                                    </div>
                                                   <!--  <h3>Channel Name</h3> -->
                                                    <div class="clearfix"></div>
                                                    <h4 class="video-desc">{{$video->description}}</h4>
                                                    <hr>
                                                </div>

                                                <div class="col-lg-12">

                                                    <div class="more-content">
                                        
                                                    <div class="share-details">

                                                        <div class="wishlist_form"> 
                                                            <form name="add_to_wishlist" method="post" id="add_to_wishlist" action="{{route('user.add.wishlist')}}">
                                                                @if(Auth::check())

                                                                    <input type="hidden" value="{{$video->admin_video_id}}" name="admin_video_id">

                                                                    @if(count($wishlist_status) == 1 && $wishlist_status)


                                                                        <input type="hidden" id="status" value="0" name="status">

                                                                        <input type="hidden" id="wishlist_id" value="{{$wishlist_status->id}}" name="wishlist_id">

                                                                        @if($flaggedVideo == '')
                                                                        <div class="mylist">
                                                                            <button style="background-color:rgb(229, 45, 39);" type="submit" id="added_wishlist" data-toggle="tooltip" title="{{tr('added_wishlist')}}">
                                                                                <div class="added_to_wishlist" id="check_id">
                                                                                    <i class="fa fa-times-circle"></i>
                                                                                    <span>{{tr('wishlist')}}</span>
                                                                                </div>

                                                                                <span class="wishlist_heart_remove">
                                                                                    <i class="fa fa-heart"></i>
                                                                                </span>
                                                                            </button> 
                                                                        </div>
                                                                        @endif
                                                                    @else

                                                                        <input type="hidden" id="status" value="1" name="status">

                                                                        <input type="hidden" id="wishlist_id" value="" name="wishlist_id">
                                                                        @if($flaggedVideo == '')
                                                                            <div class="mylist">
                                                                                <button type="submit" id="added_wishlist" data-toggle="tooltip" title="{{tr('add_to_wishlist')}}">
                                                                                    <div class="add_to_wishlist" id="check_id">
                                                                                        <i class="fa fa-plus-circle"></i>
                                                                                        <span>{{tr('wishlist')}}</span>
                                                                                    </div>

                                                                                    <span class="wishlist_heart">
                                                                                        <i class="fa fa-heart"></i>
                                                                                    </span>
                                                                                </button> 
                                                                            </div>
                                                                        @endif
                                                                    @endif
                                                                
                                                                @else
                                                                    <!-- Login Popup -->
                                                                @endif

                                                            </form>
                                                        </div>

                                                        <div class="share">
                                                            <a class="share-fb" target="_blank" href="http://www.facebook.com/sharer.php?u={{route('user.single',$video->admin_video_id)}}">
                                                                
                                                                <i class="fa fa-facebook"></i><!-- {{tr('share_on_fb')}} -->
                                                                
                                                            </a>

                                                            <a class="share-twitter" target="_blank" href="http://twitter.com/share?text={{$video->title}}...&url={{route('user.single',$video->admin_video_id)}}">
                                                               
                                                                <i class="fa fa-twitter"></i><!-- {{tr('share_on_twitter')}} -->
                                                                
                                                            </a> 

                                                            <input name="embed_link" class="form-control" id="embed_link" type="hidden" value="{{$embed_link}}">

                                                            <a class="btn btn-sm btn-success" data-toggle="modal" data-target="#copy-embed" style="margin-left: 8px; margin-top: -1px;" title="{{tr('copy_embedded_link')}}">

                                                                <i class="fa fa-link"></i>

                                                            </a>

                                                        </div><!--end of share-->

                                                        <!-- ==============MODAL STARTS=========== -->

                                                        
                                                        <!-- =========MODAL ENDS=========== -->
                                                        <div class="share">


                                                            @if(Auth::check())
                                                                @if(Setting::get('is_spam')
                                                                 && Auth::user()->id != $video->channel_created_by)

                                                                    @if($flaggedVideo == '')
                                                                        <button onclick="showReportForm();" type="button" class="report-button" title="{{tr('report')}}">
                                                                        <i class="fa fa-flag"></i> 
                                                                        <span class="report_class">
                                                                        {{tr('report')}}
                                                                        </span>
                                                                        </button>
                                                                    @else 
                                                                        <a href="{{route('user.remove.report_video', $flaggedVideo->id)}}" class="btn btn-warning unmark" title="{{tr('remove_report')}}">
                                                                            <i class="fa fa-flag"></i> 
                                                                            <span class="report_class">
                                                                            {{tr('remove_report')}}
                                                                            </span>
                                                                        </a>
                                                                    @endif

                                                                @endif

                                                            @endif
                                                        
                                                        </div>

                                                        <div class="stars ratings text-center">

                                                            <!-- <div class="views">
                                                                <i class="fa fa-eye" style="color: #b31217;font-size:13px;"></i>&nbsp;{{$video->watch_count}} {{tr('views')}}
                                                            </div> -->
                                                            <div class="clearfix"></div>
                                                           
                                                                <!-- <a href="#"><i @if($video->ratings >= 1) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                                <a href="#"><i @if($video->ratings >= 2) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                                <a href="#"><i @if($video->ratings >= 3) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                                <a href="#"><i @if($video->ratings >= 4) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                                <a href="#"><i @if($video->ratings >= 5) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a> -->
                                                            </center>
                                                        

                                                             <!-- <div>
                                                                <?php /*( <i class="fa fa-commenting"> <span id="video_comment_count">{{get_video_comment_count($video->admin_video_id)}}</span></i> {{tr('comments')}} ) */?>

                                                                @if (Auth::check())
                                                                <a class="thumb-class" onclick="likeVideo({{$video->admin_video_id}})"><i class="fa fa-thumbs-up"></i>&nbsp;<span id="like_count">{{$like_count}}</span></a>&nbsp;&nbsp;&nbsp;

                                                                <a class="thumb-class" onclick="dislikeVideo({{$video->admin_video_id}})"><i class="fa fa-thumbs-down"></i>&nbsp;<span id="dislike_count">{{$dislike_count}}</span></a>

                                                                @else 


                                                                 <a class="thumb-class"><i class="fa fa-thumbs-up"></i>&nbsp;<span>{{$like_count}}</span></a>&nbsp;&nbsp;&nbsp;

                                                                <a class="thumb-class"><i class="fa fa-thumbs-down"></i>&nbsp;<span>{{$dislike_count}}</span></a>

                                                                @endif
                                                                
                                                            </div> -->
                                                        </div><!--end of stars-->

                                                    </div><!--end of share-details-->                               
                                                </div>

                                                <div class="clearfix"></div>

                                                <hr>
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12 top zero-padding bottom">
                                                    <div class="channel-img">
                                                        <img src="{{asset('images/default.png')}}" class="img-responsive img-circle">
                                                    </div>
                                                    <span class="username"><a href="#">{{$video->title}}</a></span>
                                                    <h5 class="rating no-margin top">
                                                        <a href="#" class="rating1"><i @if($video->ratings >= 1) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                        <a href="#" class="rating1"><i @if($video->ratings >= 2) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                        <a href="#" class="rating1"><i @if($video->ratings >= 3) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                        <a href="#" class="rating1"><i @if($video->ratings >= 4) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                        <a href="#" class="rating1"><i @if($video->ratings >= 5) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                    </h5>
                                                    <div class="pull-right sub-btn">

                                                        @if(Auth::check())
                                                            @if($video->get_channel->user_id != Auth::user()->id)

                                                                @if (!$subscribe_status)

                                                                <a class="btn btn-sm btn-success text-uppercase" href="{{route('user.subscribe.channel', array('user_id'=>Auth::user()->id, 'channel_id'=>$video->channel_id))}}">{{tr('subscribe')}} &nbsp; {{$subscriberscnt}}</a>

                                                                @else 

                                                                    <a class="btn btn-sm btn-danger text-uppercase" href="{{route('user.unsubscribe.channel', array('subscribe_id'=>$subscribe_status))}}" onclick="return confirm('Are you sure want to Unsubscribe from the channel?')" style="background: rgb(229, 45, 39) !important">{{tr('un_subscribe')}} &nbsp; {{$subscriberscnt}}</a>

                                                                @endif
                                                           
                                                           @else

                                                                <a class="btn btn-sm btn-danger text-uppercase" href="{{route('user.channel.subscribers', array('channel_id'=>$video->channel_id))}}" style="background: rgb(229, 45, 39) !important"><i class="fa fa-users"></i>&nbsp; {{tr('subscribers')}} - {{$subscriberscnt}}</a>


                                                           @endif
                                                        
                                                        
                                                        @endif

                                                    </div>
                                                    <div class="clearfix"></div>
                                                
                                                </div>
                                                
                                                <div class="clearfix"></div>


                                                @if(Setting::get('is_spam'))


                                                    @if (!$flaggedVideo)
                                                        <div class="more-content" style="display: none;" id="report_video_form">
                                                            <form name="report_video" method="post" id="report_video" action="{{route('user.add.spam_video')}}">
                                                                <b>Report this Video ?</b>
                                                                <br>
                                                                @foreach($report_video as $report) 
                                                                    <div class="report_list">
                                                                        <input type="radio" name="reason" value="{{$report->value}}" required> {{$report->value}}
                                                                    </div>
                                                                @endforeach
                                                                <input type="hidden" name="video_tape_id" value="{{$video->admin_video_id}}" />
                                                                <p class="help-block"><small>If you report this video, you won't see again the same video in anywhere in your account except "Spam Videos". If you want to continue to report this video as same. Click continue and proceed the same.</small></p>
                                                                <div class="pull-right">
                                                                    <button class="btn btn-success btn-sm">{{tr('submit')}}</button>
                                                                </div>
                                                                <div class="clearfix"></div>
                                                            </form>
                                                        </div>
                                                    
                                                    @endif

                                                @endif
                                                

                                                </div>

                                                
                                               <?php /* @if(Auth::check())
                                                    @if(Setting::get('is_spam') && Auth::user()->id != $video->channel_details->user_id) */?>
                                                            
                                                        

                                                   <?php /*@endif

                                                @endif */?>
                                                
                                            </div>



                                            <hr>

                                            <!-- <div class="col-lg-12">
                                                <div class="video-description">
                                                    <h4>{{tr('description')}}</h4>
                                                    <p>{{$video->description}}</p>
                                                </div>
                                            </div> --><!--end of video-description-->

                                            <div class="clearfix"></div>                                       
                                        </div><!--end of video-title-->                                                             
                                    </div><!--end of details-->

                                    

                                    
                                    <!--end of more-content-->

                                <!--end of video-content-->

                                
                                
                                <div class="v-comments">

                                    <div class="pull-left"> 
                                    @if(count($comments) > 0) 
                                        <h3>{{tr('comments')}}
                                            <span class="c-380" id="comment_count">{{count($comments)}}</span>
                                        </h3> 
                                    @endif
                                    </div>

<!--                                     <div class="pull-right">

                                        @if(Auth::check())
                                            @if($video->get_channel->user_id != Auth::user()->id)

                                                @if (!$subscribe_status)

                                                <a class="btn btn-sm btn-success text-uppercase" href="{{route('user.subscribe.channel', array('user_id'=>Auth::user()->id, 'channel_id'=>$video->channel_id))}}"><i class="fa fa-envelope"></i>&nbsp;{{tr('subscribe')}} - {{$subscriberscnt}}</a>

                                                @else 

                                                    <a class="btn btn-sm btn-danger text-uppercase" href="{{route('user.unsubscribe.channel', array('subscribe_id'=>$subscribe_status))}}" onclick="return confirm('Are you sure want to Unsubscribe from the channel?')" style="background: rgb(229, 45, 39) !important"><i class="fa fa-times"></i>&nbsp;{{tr('un_subscribe')}} - {{$subscriberscnt}}</a>

                                                @endif
                                           
                                           @else

                                                <a class="btn btn-sm btn-danger text-uppercase" href="{{route('user.channel.subscribers', array('channel_id'=>$video->channel_id))}}" style="background: rgb(229, 45, 39) !important"><i class="fa fa-users"></i>&nbsp; {{tr('subscribers')}} - {{$subscriberscnt}}</a>


                                           @endif
                                        
                                        
                                        @endif

                                    </div> -->

                                    <div class="clearfix"></div>

                                    <br>

                                    @if(count($comments) > 0) 

                                    <p class="small">{{tr('comment_note')}}</p>

                                    @endif
                                    
                                    <div class="com-content">
                                        @if(Auth::check())

                                            <div class="image-form">
                                                <div class="comment-box1">
                                                    <div class="com-image">
                                                        <img style="width:48px;height:48px; border-radius:24px;" src="{{Auth::user()->picture}}">                                    
                                                    </div><!--end od com-image-->
                                                    
                                                    <div id="comment_form">
                                                        <div>
                                                            <form method="post" id="comment_sent" name="comment_sent" action="{{route('user.add.comment')}}">

                                                                <input type="hidden" value="{{$video->admin_video_id}}" name="admin_video_id">

                                                                @if($comment_rating_status)
                                                                 <input id="rating_system" name="rating" type="number" class="rating comment_rating" min="1" max="5" step="1">
                                                                 @endif

                                                                <textarea rows="10" id="comment" name="comments" placeholder="{{tr('add_comment_msg')}}"></textarea>

                                                                <button class="btn pull-right btn-sm btn-success top-btn-space" type="submit">{{tr('comment')}}</button>

                                                                <div class="clearfix"></div>
                                                            </form>
                                                        </div>                                      
                                                    </div>  <!--end of comment-form-->
                                                </div>                                                              
                                            
                                            </div>

                                        @endif

                                        @if(count($comments) > 0)
                                        
                                            <div class="feed-comment">

                                                <span id="new-comment"></span>

                                                @foreach($comments as $c =>  $comment)

                                                    <div class="display-com">
                                                        <div class="com-image">
                                                            <img style="width:48px;height:48px; border-radius: 24px !important;" src="{{$comment->picture}}">                                    
                                                        </div><!--end od com-image-->

                                                        <div class="display-comhead">
                                                            <span class="sub-comhead">
                                                                <a href="#"><h5 style="float:left">{{$comment->username}}</h5></a>
                                                                <a href="#" class="text-none"><p>{{$comment->diff_human_time}}</p></a>
                                                                <p><input id="view_rating" name="rating" type="number" class="rating view_rating" min="1" max="5" step="1" value="{{$comment->rating}}"></p>
                                                                <p class="com-para">{{$comment->comment}}</p>
                                                            </span>             
                                                            
                                                        </div><!--display-comhead-->                                        
                                                    </div><!--display-com-->

                                                @endforeach

                                            </div>

                                        @else
                                            <div class="feed-comment">

                                                <span id="new-comment"></span>
                                            </div>
                                            <!-- <p>{{tr('no_comments')}}</p> -->
                                        
                                        @endif
                                            
                                    </div>

                                    </div>

                                </div>            
                            </div>

                            <!--end of main-content-->

                        </div>

                        <!--end of col-sm-8 and play-video-->

                        <div class="col-sm-12 col-md-4 side-video custom-side">
                            <div class="up-next">
                                <h4 class="sugg-head1">{{tr('suggestions')}}</h4>

                                <ul class="video-sugg"> 

                                    @foreach($suggestions->data as $suggestion)
                                    
                                        <li class="sugg-list row">
                                            <div class="main-video">
                                                 <div class="video-image">
                                                    <div class="video-image-outer">
                                                        <a href="{{route('user.single' , $suggestion->admin_video_id)}}"><img src="{{$suggestion->default_image}}"></a>
                                                    </div>  
                                                    <div class="video_duration">
                                                        {{$suggestion->duration}}
                                                    </div> 
                                                 </div><!--video-image-->

                                                <div class="sugg-head">
                                                    <div class="suggn-title">                                          
                                                        <h5><a href="{{route('user.single' , $suggestion->admin_video_id)}}">{{$suggestion->title}}</a></h5>
                                                    </div><!--end of sugg-title-->

                                                    <span class="video_views">
                                                        <i class="fa fa-eye"></i> {{$suggestion->watch_count}} {{tr('views')}} <?php /*<b>.</b> 
                                                        {{$suggestion->created_at->diffForHumans()}} */?>
                                                    </span> 
                                                    <br>
                                                    <span class="stars">
                                                        <a href="#"><i @if($suggestion->ratings >= 1) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                        <a href="#"><i @if($suggestion->ratings >= 2) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                        <a href="#"><i @if($suggestion->ratings >= 3) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                        <a href="#"><i @if($suggestion->ratings >= 4) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                        <a href="#"><i @if($suggestion->ratings >= 5) style="color:gold" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                                    </span>                              
                                                </div><!--end of sugg-head-->
                                    
                                            </div><!--end of main-video-->
                                        </li><!--end of sugg-list-->
                                    @endforeach
                                   
                                    
                                </ul>
                            </div><!--end of up-next-->
                                                
                        </div><!--end of col-sm-4-->

                    </div>
                </div>
            
            </div>

        </div><!--y-content-row-->
    </div>


<?php

    $ads_timing = $video_timings = [] ;

    if(count($ads) > 0 && $ads != null) {

        foreach ($ads->between_ad as $key => $obj) {
            
            $video_timings[] = $obj->video_time;

            $ads_timing[] = $obj->ad_time;
        }
    }

?>
<div class="modal fade modal-top" id="copy-embed" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content content-modal">
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-7 col-lg-7 modal-bg-img zero-padding hidden-xs" style="background-image: url({{$video->default_image ? $video->default_image : asset('images/landing-9.png')}});">
                   <h4 class="video-title1">{{$video->title}}</h4> 
                </div>
                <div class="col-xs-12 col-sm-6 col-md-5 col-lg-5 right-space">
                    <div class="copy-embed">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title hidden-xs">Embed Video</h4>
                            <h4 class="modal-title visible-xs">{{$video->title}}</h4>
                        </div>

                        <div class="modal-body">

                           <form onsubmit="return false;">

                                <div class="form-group">
                                    <textarea class="form-control" rows="5" id="comment">{{$embed_link}}</textarea>
                                </div>

                            </form>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-danger pull-right " onclick="copyTextToClipboard();" style="border-radius: 0">Copy</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')

    <script type="text/javascript" src="{{asset('assets/js/star-rating.js')}}"></script>


    <script type="text/javascript" src="{{asset('assets/js/toast.script.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.video-y-menu').addClass('hidden');
        }); 
        function showReportForm() {
            var divId = document.getElementById('report_video_form').style.display;
            if (divId == 'none') {
                $('#report_video_form').show(500);
            } else {
                $('#report_video_form').hide(500);
            }
        }


        // Reset the rating.
        // $('#rating_system').rating('reset');

        // $('#rating_system').rating('clear');

         $('.view_rating').rating({disabled: true, showClear: false});
    </script>

    <script src="{{asset('jwplayer/jwplayer.js')}}"></script>

    <script>jwplayer.key="{{Setting::get('JWPLAYER_KEY')}}";</script>

    <script type="text/javascript">
        
        jQuery(document).ready(function(){  

                // Opera 8.0+
                var isOpera = (!!window.opr && !!opr.addons) || !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
                // Firefox 1.0+
                var isFirefox = typeof InstallTrigger !== 'undefined';
                // At least Safari 3+: "[object HTMLElementConstructor]"
                var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
                // Internet Explorer 6-11
                var isIE = /*@cc_on!@*/false || !!document.documentMode;
                // Edge 20+
                var isEdge = !isIE && !!window.StyleMedia;
                // Chrome 1+
                var isChrome = !!window.chrome && !!window.chrome.webstore;
                // Blink engine detection
                var isBlink = (isChrome || isOpera) && !!window.CSS;


            //hang on event of form with id=myform
            jQuery("form[name='add_to_wishlist']").submit(function(e) {

                //prevent Default functionality
                e.preventDefault();

                //get the action-url of the form
                var actionurl = e.currentTarget.action;

                //do your own request an handle the results
                jQuery.ajax({
                        url: actionurl,
                        type: 'post',
                        dataType: 'json',
                        data: jQuery("#add_to_wishlist").serialize(),
                        success: function(data) {
                           if(data.success == true) {

                                jQuery("#added_wishlist").html("");

                                /*var display_style = document.getElementById('check_id').style.display;

                                alert(display_style);*/

                                if(data.status == 1) {
                                    jQuery('#status').val("0");

                                    jQuery('#wishlist_id').val(data.wishlist_id); 
                                    jQuery("#added_wishlist").css({'font-family':'arial','background-color':'#b31217','color' : '#FFFFFF'});

                                    if (jQuery(window).width() > 640) {
                                        var append = '<i class="fa fa-times-circle">&nbsp;&nbsp;{{tr('wishlist')}}';
                                    } else {
                                        var append = '<i class="fa fa-heart">';
                                    }
                                    jQuery("#added_wishlist").append(append);
                                    
                                } else {
                                    jQuery('#status').val("1");
                                    jQuery('#wishlist_id').val("");
                                    jQuery("#added_wishlist").css({'font-family':'arial','background':'','color' : ''});
                                    if (jQuery(window).width() > 640) {
                                        var append = '<i class="fa fa-plus-circle">&nbsp;&nbsp;{{tr('wishlist')}}';
                                    } else {
                                        var append = '<i class="fa fa-heart">';
                                    }
                                   
                                    jQuery("#added_wishlist").append(append);
                                    
                                }
                           } else {
                                console.log('Wrong...!');
                           }
                        }
                });

            });

            $('#comment').keydown(function(event) {
                if (event.keyCode == 13) {
                    $(this.form).submit()
                    return false;
                 }
            }).focus(function(){
                if(this.value == "Write your comment here..."){
                     this.value = "";
                }

            }).blur(function(){
                if(this.value==""){
                     this.value = "";
                }
            });

            jQuery("form[name='comment_sent']").submit(function(e) {

                //prevent Default functionality
                e.preventDefault();

                //get the action-url of the form
                var actionurl = e.currentTarget.action;

                var form_data = jQuery("#comment").val();

                if(form_data) {

                    //do your own request an handle the results
                    jQuery.ajax({
                            url: actionurl,
                            type: 'post',
                            dataType: 'json',
                            data: jQuery("#comment_sent").serialize(),
                            success: function(data) {

                               if(data.success == true) {

                                @if(Auth::check())
                                    jQuery('#comment').val("");
                                    jQuery('#no_comment').hide();
                                    var comment_count = 0;
                                    var count = 0;
                                    comment_count = jQuery('#comment_count').text();
                                    var count = parseInt(comment_count) + 1;
                                    jQuery('#comment_count').text(count);
                                    jQuery('#video_comment_count').text(count);

                                    // var stars = 0;

                                    var first_star = data.comment.rating >= 1 ? "color:#ff0000" : "";

                                    var second_star = data.comment.rating >= 2 ? "color:#ff0000" : "";

                                    var third_star = data.comment.rating >= 3 ? "color:#ff0000" : "";

                                    var fourth_star = data.comment.rating >= 4 ? "color:#ff0000" : "";

                                    var fifth_star = data.comment.rating >= 5 ? "color:#ff0000" : "";

                                   var stars = '<span class="stars">'+
                                    '<a href="#"><i style="'+first_star+'" class="fa fa-star" aria-hidden="true"></i></a>'+
                                    '<a href="#"><i style="'+second_star+'" class="fa fa-star" aria-hidden="true"></i></a>'+
                                    '<a href="#"><i style="'+third_star+'" class="fa fa-star" aria-hidden="true"></i></a>'+
                                    '<a href="#"><i style="'+fourth_star+'" class="fa fa-star" aria-hidden="true"></i></a>'+
                                    '<a href="#"><i style="'+fifth_star+'" class="fa fa-star" aria-hidden="true"></i></a></span>';   

                                    /**
                                    <p><input id="view_rating" name="rating" type="number" class="rating view_rating" min="1" max="5" step="1" value="'+data.comment.rating+'"></p>
                                    **/

                                    $('.comment_rating').rating('clear');

                                    jQuery('#new-comment').prepend('<div class="display-com"><div class="com-image"><img style="width:48px;height:48px" src="{{Auth::user()->picture}}"></div><div class="display-comhead"><span class="sub-comhead"><a href="#"><h5 style="float:left">{{Auth::user()->name}}</h5></a><a href="#"><p>'+data.date+'</p></a><p>'+stars+'</p><p class="com-para">'+data.comment.comment+'</p></span></div></div>');
                                @endif
                               } else {
                                    console.log('Wrong...!');
                               }
                            }
                    });
                } else {
                    return false;
                }

            });

           //  jQuery("form[name='watch_main_video']").submit(function(e) {

                //prevent Default functionality
               //  e.preventDefault();

               //  jQuery('#watch_main_video_button').fadeOut();

                    var playerInstance = jwplayer("main-video-player");  



                        // if(isOpera || isSafari) {

                           /* alert("dd");

                            jQuery('#main_video_setup_error').show();
                            jQuery('#main-video-player').hide();

                            var hasFlash = false;
                            try {
                                var fo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
                                if (fo) {
                                    hasFlash = true;
                                }
                            } catch (e) {
                                if (navigator.mimeTypes
                                        && navigator.mimeTypes['application/x-shockwave-flash'] != undefined
                                        && navigator.mimeTypes['application/x-shockwave-flash'].enabledPlugin) {
                                    hasFlash = true;
                                }
                            }

                            alert(hasFlash);

                            if (hasFlash == false) {
                                jQuery('#flash_error_display').show();
                                return false;
                            }

                                jQuery('#main_video_setup_error').css("display", "block");


                            confirm('The video format is not supported in this browser. Please option some other browser.');*/

                        // } else {


                            if(!jQuery.browser.mobile) {

                            } else {

                                // $('#mainVideo').show();
                                
                                console.log('You are using a mobile device!');

                                <?php // $videoStreamUrl = $hls_video; ?>

                                jQuery.ajax({
                                        url: "{{route('test')}}",
                                        type: 'post',
                                        data: {'test' : "test"},
                                        success: function(data) {

                                        }
                                    });
                            }

                            @if($videoStreamUrl) 

                                playerInstance.setup({
                                    // file: "{{$videoStreamUrl}}",
                                    sources: [{
                                        file: "{{$videoStreamUrl}}"
                                      }, {
                                        file : "{{$video->video}}"
                                      }],
                                    image: "{{$video->default_image}}",
                                    width: "100%",
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
                                    },
                                     tracks : [{
                                      file : "{{$video->subtitle}}",
                                      kind : "captions",
                                      default : true,
                                    }]
                                   /* advertising: {
                                        client: 'vast',
                                        schedule: {
                                            adbreak1: {
                                                offset: "pre",
                                                tag: '//adserver.com/vastResponse1.xml'
                                            },
                                            adbreak2: {
                                                offset: 5,
                                                tag: '//adserver.com/vastResponse2.xml',
                                                type: 'nonlinear'
                                            } 
                                        }
                                    }*/
                                
                                });

                            @else

                                if(jQuery.browser.mobile) {

                                    $('#mainVideo').show();
                                    
                                    console.log('You are using a mobile device!');

                                    var path = "{{$hls_video}}";

                                    jQuery.ajax({
                                        url: "{{route('test')}}",
                                        type: 'post',
                                        data: {'test' : "test"},
                                        success: function(data) {

                                        }
                                    });

                                } else {

                                    var videoPath = "{{$videoPath}}";
                                    var videoPixels = "{{$video_pixels}}";

                                    var path = [];

                                    var splitVideo = videoPath.split(',');

                                    var splitVideoPixel = videoPixels.split(',');


                                    for (var i = 0 ; i < splitVideo.length; i++) {

                                        path.push({file : splitVideo[i], label : splitVideoPixel[i]});
                                    }

                                    //alert("HELELo");
                                }


                                playerInstance.setup({
                                    
                                    sources: path,
                                    image: "{{$video->default_image}}",
                                    width: "100%",
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
                                      },
                                       events : {

                                        onComplete : function(event) {

                                            console.log(jwplayer().getPosition());



                                            if (playerInstance.getState() == 'complete') {

                                               jQuery.ajax({
                                                    url: "{{route('user.add.history')}}",
                                                    type: 'post',
                                                    data: {'admin_video_id' : "{{$video->admin_video_id}}"},
                                                    success: function(data) {

                                                       if(data.success == true) {

                                                        console.log('Added to history');

                                                        /*var watch_count = 0;
                                                        var count = 0;
                                                        watch_count = jQuery('#watch_count').text();
                                                        var count = parseInt(watch_count) + 1;
                                                        jQuery('#watch_count').text(count);

                                                        console.log('Added to history');*/

                                                       } else {
                                                            console.log('Wrong...!');
                                                       }
                                                    }
                                                });

                                           }

                                       }

                                   },

                                    tracks : [{
                                      file : "{{$video->subtitle}}",
                                      kind : "captions",
                                      default : true,
                                    }]
                                
                                });

                            @endif

                            playerInstance.on('setupError', function() {

                                jQuery("#main-video-player").css("display", "none");
                                jQuery('#trailer_video_setup_error').hide();
                               

                                var hasFlash = false;
                                try {
                                    var fo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
                                    if (fo) {
                                        hasFlash = true;
                                    }
                                } catch (e) {
                                    if (navigator.mimeTypes
                                            && navigator.mimeTypes['application/x-shockwave-flash'] != undefined
                                            && navigator.mimeTypes['application/x-shockwave-flash'].enabledPlugin) {
                                        hasFlash = true;
                                    }
                                }

                                if (hasFlash == false) {
                                    jQuery('#flash_error_display').show();
                                    return false;
                                }

                                jQuery('#main_video_setup_error').css("display", "block");

                                confirm('The video format is not supported in this browser. Please option some other browser.');
                            
                            });
                        // }
                    
                    
                       //  jQuery('#main_video_error').show();
                        

                    jQuery("#main-video-player").show();

                    console.log(jwplayer().getPosition());

                    var intervalId;


                    var timings = "{{($ads) ? count($ads->between_ad) : 0}}";

                    // var adtimings = 5;

                    var time = 0;

                    // console.log("Timings " + timings.length);

                    function timer(){

                         intervalId = setInterval(function(){

                            var video_time = Math.round(playerInstance.getPosition());


                            console.log("Video Timing out"+video_time);

                            @if($ads)

                                @if(count($ads->between_ad) > 0)

                                    @foreach($ads->between_ad as $i => $obj) {

                                        var video_timing = "{{$obj->video_time}}";

                                        console.log("Video Timing "+video_timing);

                                        var a = video_timing.split(':'); // split it at the colons

                                         if (a.length == 3) {
                                             var seconds = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]);
                                         } else {
                                             var seconds = parseInt(a[0]) * 60 + parseInt(a[1]);
                                         }

                                         console.log("Seconds "+seconds);

                                        if (video_time == seconds && time != video_time) {

                                             jwplayer().pause();

                                             time = video_time;

                                             stop();

                                             // $('#main-video-player').hide();

                                             // jQuery("#main-video-player").css("display", "none");

                                             $("#main-video-player").css({'visibility':'hidden', 'width' : '0%'});

                                             $('#main_video_ad').show();

                                             $("#ad_image").attr("src","{{$obj->assigned_ad->file}}");

                                            @if($obj->assigned_ad->ad_url)

                                             $('.click_here_ad').html("<a target='_blank' href='{{$obj->assigned_ad->ad_url}}'>{{tr('click_here_url')}}</a>");

                                             $('.click_here_ad').show();

                                             @endif


                                             adsPage("{{$obj->ad_time}}");

                                        }
                                    }

                                    @endforeach

                                @endif


                                @if($ads->post_ad)

                                    if (playerInstance.getState() == "complete") {

                                        // $('#main-video-player').hide();

                                        // jQuery("#main-video-player").css("display", "none");

                                        $("#main-video-player").css({'visibility':'hidden', 'width' : '0%'});

                                        $('#main_video_ad').show();

                                        $("#ad_image").attr("src","{{$ads->post_ad->assigned_ad->file}}");

                                        @if($ads->post_ad->assigned_ad->ad_url)

                                        $('.click_here_ad').html("<a target='_blank' href='{{$ads->post_ad->assigned_ad->ad_url}}'>{{tr('click_here_url')}}</a>");

                                        $('.click_here_ad').show();

                                        @endif

                                        stop();

                                        adsPage("{{$ads->post_ad->ad_time}}");

                                    }
                                @endif
                                
                            @endif


                         }, 1000);

                    }

                    function stop(){
                       clearInterval(intervalId);
                    }


                    var adCount = 0;

                    function adsPage(adtimings){

                        // $("#progress").html("");

                        // $(".progress-bar-fill-div").css('width', '0%');

                         // adtimings = adtimings * 60;

                        $(".seconds").html(adtimings+" sec");

                        $("#progress").html('<div class="progress-bar-div">'+
                                '<span class="progress-bar-fill-div" style="width: 0%"></span>'+
                                '</div>');

                        $(".progress-bar-fill-div").css('transition', 'width '+adtimings+'s ease-in-out');

                        $('.progress-bar-fill-div').delay(1000).queue(function () {

                            console.log("playig");

                            $(this).css('width', '100%')
                        });
 
                        // var width = (100/(adtimings));

                        intervalId = setInterval(function(){

                            adCount += 1;

                            $(".seconds").html((adtimings - adCount) +" sec");

                            // console.log(width * adCount);

                            console.log("Ad Count " +adCount);

                            // $(".progress-bar-fill-div").css('width', (width * adCount)+'%');
                            
                            if (adCount == adtimings) {

                                 $(this).css('width', '100%')

                                adCount = 0;

                                stop();

                                $(".click_here_ad").hide();

                                $('#main_video_ad').hide();

                                $("#main-video-player").css({'visibility':'visible', 'width' : '100%'});

                                if (playerInstance.getState() != "complete") {

                                    jwplayer().play();

                                    timer();

                                }

                            }

                         }, 1000);

                    }



                    jwplayer().on('displayClick', function(e) {

                        console.log("state pos "+jwplayer().getState());


                        jQuery.ajax({
                            url: "{{route('user.add.watch_count')}}",
                            type: 'post',
                            data: {'video_tape_id' : "{{$video->admin_video_id}}"},
                            success: function(data) {

                               if(data.success == true) {

                                console.log('Watch count Incremented');

                                /*var watch_count = 0;
                                var count = 0;
                                watch_count = jQuery('#watch_count').text();
                                var count = parseInt(watch_count) + 1;
                                jQuery('#watch_count').text(count);

                                console.log('Added to history');*/

                               } else {
                                    console.log('Wrong...!');
                               }
                            }
                        });



                        if (jwplayer().getState() == 'idle') {

                            @if($ads)

                                @if($ads->pre_ad)

                                     $("#main-video-player").css({'visibility':'hidden', 'width' : '0%'});

                                     $('#main_video_ad').show();

                                     $("#ad_image").attr("src","{{$ads->pre_ad->assigned_ad->file}}");

                                     @if($ads->pre_ad->assigned_ad->ad_url)

                                         $('.click_here_ad').html("<a target='_blank' href='{{$ads->pre_ad->assigned_ad->ad_url}}'>{{tr('click_here_url')}}</a>");

                                         $(".click_here_ad").show();

                                     @endif

                                     jwplayer().pause();

                                     adsPage("{{$ads->pre_ad->ad_time}}");

                                @endif

                            @endif

                           
                            
                               

                        }

                        @if($ads)
                            @if (((count($ads->between_ad) > 0) || !empty($ads->post_ad)) && empty($ads->pre_ad)) 
                                timer();
                            @endif
                        @endif

                    })



                    

            // });

        });


            function copyTextToClipboard() {

               var textArea = document.createElement( "textarea" );
               textArea.value = $("#embed_link").val();
               document.body.appendChild( textArea );

               textArea.select();

               $("#embed_link").select();

               try {
                  var successful = document.execCommand( 'copy' );
                  var msg = successful ? 'successful' : 'unsuccessful';
                  console.log('Copying text command was ' + msg);

                  addToast();
                 // alert('Copied Embedded Link');
               } catch (err) {
                  console.log('Oops, unable to copy');
               }

               document.body.removeChild( textArea );
            }

        function likeVideo(video_id) {

            $.ajax({
                url : "{{route('user.video.like')}}",
                data : {video_tape_id : video_id},
                type: "post",
                success : function(data) {

                    if (data.success) {

                        $("#like_count").html(data.like_count);

                        $("#dislike_count").html(data.dislike_count);

                    } else {

                        console.log(data.error_messages);
                    }

                },

                error : function(data) {


                },
            })
        }

          function dislikeVideo(video_id) {

            $.ajax({
                url : "{{route('user.video.disLike')}}",
                type: "post",
                data : {video_tape_id : video_id},
                success : function(data) {

                    if(data.success) {

                        $("#like_count").html(data.like_count);

                        $("#dislike_count").html(data.dislike_count);

                    } else {

                        console.log(data.error_messages);

                    }

                },

                error : function(data) {


                },
            })
        }

        function addToast(){
                $.Toast("Embedded Link", "Link Cpoied Successfully.", "success", {
                    has_icon:false,
                    has_close_btn:true,
                    stack: true,
                    fullscreen:true,
                    timeout:1000,
                    sticky:false,
                    has_progress:true,
                    rtl:false,
                });
            }
    </script>

@endsection
