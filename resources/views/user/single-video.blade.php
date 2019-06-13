@extends('layouts.user')
@section('meta_tags')
<meta property="og:locale" content="en_US" />
<meta property="og:type" content="article" />
<meta property="og:title" content="{{$video->title}}" />
<meta property="og:description" content="<?= $video->title ?>" />
<meta property="og:url" content="" />
<meta property="og:site_name" content="@if(Setting::get('site_name')) {{Setting::get('site_name') }} @else {{tr('site_name')}} @endif" />
<meta property="og:image" content="{{$video->default_image}}" />
<meta name="twitter:card" content="summary"/>
<meta name="twitter:description" content="<?= $video->title ?>"/>
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
   .common-streamtube {
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
   th {
   border-top: none;
   }
</style>
@endsection
@section('content')
<div class="y-content">

   <div class="row y-content-row">
      @include('layouts.user.nav')
      <div class="page-inner col-sm-9 col-md-10 profile-edit">
         <div class="profile-content mar-0">
            @include('notification.notify')
            <div class="row no-margin">
               <div class="col-sm-12 col-md-8 play-video">
                  <div class="single-video-sec">
                     @include('user.videos.streaming')
                  </div>
                  <div class="main-content">
                     <div class="video-content">
                        <div class="details">
                           <div class="video-title">
                              <div class="title row">
                                 <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12 zero-padding">
                                    <h3>{{$video->title}}</h3>
                                    <div class="views pull-left">
                                       {{number_format_short($video->watch_count)}} {{tr('views')}}
                                    </div>
                                    <div class="pull-right relative">
                                       @if (Auth::check())
                                       <a class="thumb-class" onclick="likeVideo({{$video->video_tape_id}})"><i class="material-icons">thumb_up</i>&nbsp;<span id="like_count">{{number_format_short($like_count)}}</span></a>&nbsp;&nbsp;&nbsp;
                                       <a class="thumb-class" onclick="dislikeVideo({{$video->video_tape_id}})"><i class="material-icons ali-midd-20">thumb_down</i>&nbsp;<span id="dislike_count">{{number_format_short($dislike_count)}}</span></a>
                                       @else 
                                       <a class="thumb-class" data-toggle="modal" data-target="#login_error"><i class="material-icons">thumb_up</i>&nbsp;<span>{{number_format_short($like_count)}}</span></a>&nbsp;&nbsp;&nbsp;
                                       <a class="thumb-class" data-toggle="modal" data-target="#login_error"><i class="material-icons ali-midd-20">thumb_down</i>&nbsp;<span>{{number_format_short($dislike_count)}}</span></a>
                                       @endif

                                       <a  class="share-new" data-toggle="modal" data-target="#popup1">
                                          <i class="material-icons">share</i>&nbsp;Share
                                          <!--  <p class="hidden-xs">share</p> -->
                                       </a>

                                       <a class="share-new global_playlist_id" id="{{$video->video_tape_id}}" href="#" style="display: none;">
                                          <i class="material-icons">playlist_add</i>&nbsp;Save
                                       </a>
         
                                       <form name="add_to_wishlist" method="post" id="add_to_wishlist" action="{{route('user.add.wishlist')}}" class="add-wishlist">
                                          @if(Auth::check())
                                          
                                          <input type="hidden" value="{{$video->video_tape_id}}" name="video_tape_id">
                                          
                                          @if(count($wishlist_status) == 1 && $wishlist_status)
                                          
                                          
                                          <input type="hidden" id="status" value="0" name="status">
                                          
                                          <input type="hidden" id="wishlist_id" value="{{$wishlist_status->id}}" name="wishlist_id">
                                          
                                          @if($flaggedVideo == '')
                                          <div class="mylist">
                                          <button  type="submit" id="added_wishlist" data-toggle="tooltip" title="{{tr('added_wishlist')}}">
                                          <div class="added_to_wishlist" id="check_id">
                                          <i class="fa fa-heart" style="color: #b31217"></i>
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
                                          <i class="fa fa-heart"></i>
                                          </div>
                                          
                                          <span class="wishlist_heart">
                                          <i class="fa fa-heart"></i>
                                          </span>
                                          </button> 
                                          </div>
                                          @endif
                                          @endif
                                          
                                          
                                          
                                          @endif
                                          
                                       </form>

                                    </div>
                                    <!--  <h3>Channel Name</h3> -->
                                    <div class="clearfix"></div>
                                    <!-- <h4 class="video-desc">{{$video->description}}</h4> -->
                                    <hr>
                                 </div>
                                 <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12 top zero-padding ">
                                    <div class="row1">
                                       <div class="col-xs-12 col-md-7 col-sm-7 col-lg-7" >
                                          <div class="channel-img">
                                             <img src="{{$video->channel_picture}}" class="img-responsive img-circle" style="height: 100%;width: 100%">
                                          </div>
                                          <div class="username"><a href="{{route('user.channel',$video->channel_id)}}">{{$video->channel_name}}</a></div>
                                          <h5 class="rating no-margin mt-5">
                                             <span class="rating1"><i @if($video->ratings >= 1) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></span>
                                             <span class="rating1"><i @if($video->ratings >= 2) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></span>
                                             <span class="rating1"><i @if($video->ratings >= 3) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></span>
                                             <span class="rating1"><i @if($video->ratings >= 4) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></span>
                                             <span class="rating1"><i @if($video->ratings >= 5) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></span>
                                          </h5>
                                       </div>
                                       <div class="col-xs-12 col-md-5 col-sm-5 col-lg-5" >
                                          <div class="pull-right ">
                                             @if(Auth::check())
                                             @if(Setting::get('is_spam')
                                             && Auth::user()->id != $video->channel_created_by)
                                             @if($flaggedVideo == '')
                                             <!-- <button onclick="showReportForm();" type="button" class="report-button bottom-space" title="{{tr('report')}}">
                                                <i class="fa fa-flag"></i> 
                                                </button> -->
                                             <button  type="button" class="btn btn-danger report-button bottom-space" title="{{tr('report')}}" data-toggle="modal" data-target="#report-form">
                                             <i class="fa fa-flag"></i> 
                                             </button>
                                             @else 
                                             <a href="{{route('user.remove.report_video', $flaggedVideo->video_tape_id)}}" class="btn btn-info unmark bottom-space" title="{{tr('remove_report')}}">
                                             <i class="fa fa-flag"></i> 
                                             </a>
                                             @endif
                                             @endif
                                             @endif
                                          </div>
                                          <div class="pull-right ">
                                             @if(Auth::check())
                                             @if($video->get_channel->user_id != Auth::user()->id)
                                             @if (!$subscribe_status)
                                             <a class="btn btn-sm bottom-space btn-info text-uppercase" href="{{route('user.subscribe.channel', array('user_id'=>Auth::user()->id, 'channel_id'=>$video->channel_id))}}">{{tr('subscribe')}} &nbsp; {{$subscriberscnt}}</a>
                                             @else 
                                             <a class="btn btn-sm bottom-space btn-danger text-uppercase" href="{{route('user.unsubscribe.channel', array('subscribe_id'=>$subscribe_status))}}" onclick="return confirm('Are you sure want to Unsubscribe from the channel?')" style="background: rgb(229, 45, 39) !important">{{tr('un_subscribe')}} &nbsp; {{$subscriberscnt}}</a>
                                             @endif
                                             @else
                                             <a class="btn btn-sm bottom-space btn-danger text-uppercase" href="{{route('user.channel.subscribers', array('channel_id'=>$video->channel_id))}}" style="background: rgb(229, 45, 39) !important"><i class="fa fa-users"></i>&nbsp; {{tr('subscribers')}} - {{$subscriberscnt}}</a>
                                             @endif
                                             @endif
                                          </div>
                                       </div>
                                    </div>
                                    <div class="clearfix"></div>
                                 </div>
                                 <div class="clearfix"></div>
                                 <div>
                                    <h4 class="video-desc"><?= $video->description?></h4>
                                    <div class="tag-and-category">
	                                    <div class="row m-0">
	                                    	<div class="col-lg-3 col-md-3 col-sm-4 col-xs-4 p-0 mt-10">
	                                    		<p class="category-name" style="float: none !important;font-size: 15px !important;">category</p>
	                                    	</div>
	                                    	<div class="col-lg-9 col-md-9 col-sm-8 col-xs-8 p-0 mt-10">
	                                    		<a href="{{route('user.categories.view', $video->category_unique_id)}}" target="_blank" class="category-name blue-link">{{$video->category_id ? $video->category_name :  ""}}</a>
	                                    	</div>
	                                    </div>
	                                    @if(count($tags) > 0)
                                       	<div class="row m-0">
                                       		<div class="col-lg-3 col-md-3 col-sm-4 col-xs-4 p-0 mt-10">
                                          		<p class="category-name" style="float: none !important;font-size: 15px !important;">{{tr('tags')}}</p>
                                          	</div>
                                          	<div class="col-lg-9 col-md-9 col-sm-8 col-xs-8 p-0 mt-10">
                                            <?php 
                                                $tags_list = [];
                                                
                                                foreach($tags as $i => $tag) {
                                                
                                                	$tags_list[] = '<a href="'.route('user.tags.videos', array('id'=>$tag->tag_id)).'" target="_blank" class="category-name blue-link">'.$tag->tag_name.'</a>';
                                                
                                                }
                                                
                                            ?>
                                          	<?= $tags_list ? implode(', ', $tags_list) : '' ?>
                                          	</div>
                                        </div>
	                                    @endif
                                    </div>
                                 </div>
                                 <div class="clearfix"></div>
                                 @if(Setting::get('is_spam'))
                                 @if (!$flaggedVideo)
                                 <div class="more-content" style="display: none;" id="report_video_form">
                                    <form name="report_video" method="post" id="report_video" action="{{route('user.add.spam_video')}}">
                                       <b>{{tr('report_this_video')}}</b>
                                       <br>
                                       <!-- @foreach($report_video as $report) 
                                       <div class="report_list">
                                          <input type="radio" name="reason" value="{{$report->value}}" required> {{$report->value}}
                                       </div>
                                       @endforeach -->

                                       @foreach($report_video as $report) 
                                       <div class="report_list">
                                          <label class="radio1">
                                             <input id="radio1" type="radio" name="reason" checked="" value="{{$report->value}}" required>
                                             <span class="outer"><span class="inner"></span></span>{{$report->value}}
                                          </label>
                                       </div>
                                       <!-- <div class="clearfix"></div> -->
                                       @endforeach

                                       <input type="hidden" name="video_tape_id" value="{{$video->video_tape_id}}" />
                                       <p class="help-block"><small>If you report this video, you won't see again the same video in anywhere in your account except "Spam Videos". If you want to continue to report this video as same. Click continue and proceed the same.</small></p>
                                       <div class="pull-right">
                                          <button class="btn btn-info btn-sm">{{tr('submit')}}</button>
                                       </div>
                                       <div class="clearfix"></div>
                                    </form>
                                 </div>
                                 @endif
                                 @endif
                                 <div class="modal fade" id="report-form" role="dialog">
                                    <div class="modal-dialog">
                                       <!-- Modal content-->
                                       <div class="modal-content">
                                          <div class="modal-header">
                                             <button type="button" class="close" data-dismiss="modal">&times;</button>
                                             <h4 class="modal-title">{{tr('report_this_video')}}</h4>
                                          </div>
                                          <div class="modal-body">
                                             @if(Setting::get('is_spam'))
                                             @if (!$flaggedVideo)
                                             <div class="more-content" id="report_video_form">
                                                <form name="report_video" method="post" id="report_video" action="{{route('user.add.spam_video')}}">
                                                   @foreach($report_video as $report)  
                                                   <div class="report_list">
                                                      <label class="radio1">
                                                         <input id="radio1" type="radio" name="reason" checked="" value="{{$report->value}}" required>
                                                         <span class="outer"><span class="inner"></span></span>{{$report->value}}
                                                      </label>
                                                   </div>
                                                   <div class="clearfix"></div>
                                                   @endforeach

                                                   <input type="hidden" name="video_tape_id" value="{{$video->video_tape_id}}" />
                                                   <p class="help-block"><small>{{tr('single_video_content')}}</small></p>
                                                   <div class="pull-right">
                                                      <button class="btn btn-info btn-sm">{{tr('submit')}}</button>
                                                   </div>
                                                   <div class="clearfix"></div>
                                                </form>
                                             </div>
                                             @endif
                                             @endif
                                          </div>
                                       </div>
                                       <!-- modal content ends -->
                                    </div>
                                 </div>
                                 <div class="modal fade" id="login_error" role="dialog">
                                    <div class="modal-dialog modal-sm">
                                       <!-- Modal content-->
                                       <div class="modal-content">
                                          <div class="modal-header">
                                             <button type="button" class="close" data-dismiss="modal">&times;</button>
                                             <h4 class="modal-title">{{tr('authentication_error')}}</h4>
                                          </div>
                                          <div class="modal-body">
                                             <div class="row">
                                                <div class="col-lg-12">
                                                   {{tr('login_notes')}}   
                                                   <div class="clearfix"></div>
                                                   <br>
                                                   <div class="text-center">
                                                      <a href="{{route('user.login.form')}}"><button class="btn btn-sm btn-danger">{{tr('login')}}</button></a>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                       <!-- modal content ends -->
                                    </div>
                                 </div>
                              </div>
                              <div class="hr-class">
                                 <hr>
                              </div>
                              <div class="clearfix"></div>
                           </div>
                           <!--end of video-title-->                                                             
                        </div>
                        <!--end of details-->
                        <div class="v-comments">
                           <div class="pull-left">
                              @if(count($comments) > 0) 
                              <h3 class="mb-15"><span class="c-380" id="comment_count">{{count($comments)}}</span>&nbsp;{{tr('comments')}}</h3>
                              @endif
                           </div>
                           <div class="clearfix"></div>
                           @if(count($comments) > 0) 
                           <!-- <p class="small mb-15">{{tr('comment_note')}}</p> -->
                           @endif
                           <div class="com-content">
                              @if(Auth::check())
                              @if(Auth::user()->id != $video->channel_created_by)
                              <div class="image-form">
                                 <div class="comment-box1">
                                    <div class="com-image">
                                       <img style="width:50px;height:50px; border-radius:25px;object-fit: cover;object-position: center;" src="{{Auth::user()->picture}}">                                    
                                    </div>
                                    <!--end od com-image-->
                                    <div id="comment_form">
                                       <div>
                                          <form method="post" id="comment_sent" name="comment_sent" action="{{route('user.add.comment')}}">
                                             <input type="hidden" value="{{$video->video_tape_id}}" name="video_tape_id">
                                             @if($comment_rating_status)
                                             <input id="rating_system" name="ratings" type="number" class="rating comment_rating" min="1" max="5" step="1">
                                             @endif
                                             <textarea rows="10" id="comment" name="comments" placeholder="{{tr('add_comment_msg')}}"></textarea>
                                             <p class="underline"></p>
                                             <button class="btn pull-right btn-sm btn-info btn-lg top-btn-space" type="submit" id="comment_btn">{{tr('comment')}}</button>
                                             <div class="clearfix"></div>
                                          </form>
                                       </div>
                                    </div>
                                    <!--end of comment-form-->
                                 </div>
                              </div>
                              @endif
                              @endif
                              @if(count($comments) > 0)
                              <div class="feed-comment">
                                 <span id="new-comment"></span>
                                 @foreach($comments as $c =>  $comment)
                                 <div class="display-com">
                                    <div class="com-image">
                                       <img style="width:50px;height:50px; border-radius:25px;object-fit: cover;object-position: center;" src="{{$comment->picture}}">                                    
                                    </div>
                                    <!--end od com-image-->
                                    <div class="display-comhead">
                                       <span class="sub-comhead">
                                          <a>
                                             <h5 style="float:left">{{$comment->username}}</h5>
                                          </a>
                                          <a class="text-none">
                                             <p>{{$comment->diff_human_time}}</p>
                                          </a>
                                          <p><input id="view_rating" name="rating" type="number" class="rating view_rating" min="1" max="5" step="1" value="{{$comment->rating}}"></p>
                                          <p class="com-para">{{$comment->comment}}</p>
                                       </span>
                                    </div>
                                    <!--display-comhead-->                                        
                                 </div>
                                 <!--display-com-->
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
                  <div class="up-next pt-0">
                     <h4 class="sugg-head1">{{tr('suggestions')}}</h4>
                     <ul class="video-sugg">
                        @if(count($suggestions->items) > 0)
                        @foreach($suggestions->items as $suggestion)
                        <li class="sugg-list row">
                           <div class="main-video">
                              <div class="video-image">
                                 <div class="video-image-outer">
                                    <a href="{{$suggestion->url}}">
                                       <!-- <img src="{{$suggestion->video_image}}"> -->
                                       <img src="{{asset('streamtube/images/placeholder.gif')}}" data-src="{{$suggestion->video_image}}" class="placeholder" />
                                    </a>
                                 </div>
                                 @if($suggestion->ppv_amount > 0)
                                 @if(!$suggestion->ppv_status)
                                 <div class="video_amount">
                                    {{tr('pay')}} - {{Setting::get('currency')}}{{$suggestion->ppv_amount}}
                                 </div>
                                 @endif
                                 @endif
                                 <div class="video_duration">
                                    {{$suggestion->duration}}
                                 </div>
                              </div>
                              <!--video-image-->
                              <div class="sugg-head">
                                 <div class="suggn-title">
                                    <h5><a href="{{$suggestion->url}}">{{$suggestion->title}}</a></h5>
                                 </div>
                                 <!--end of sugg-title-->
                                 <span class="video_views">
                                    <div><a href="{{route('user.channel',$suggestion->channel_id)}}">{{$suggestion->channel_name}}</a></div>
                                    <i class="fa fa-eye"></i> {{$suggestion->watch_count}} {{tr('views')}} <b>.</b> 
                                    {{$suggestion->created_at}} 
                                 </span>
                                 <br>
                                 <span class="stars">
                                 <a><i @if($suggestion->ratings >= 1) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                 <a><i @if($suggestion->ratings >= 2) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                 <a><i @if($suggestion->ratings >= 3) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                 <a><i @if($suggestion->ratings >= 4) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                 <a><i @if($suggestion->ratings >= 5) style="color:#ff0000" @endif class="fa fa-star" aria-hidden="true"></i></a>
                                 </span>                              
                              </div>
                              <!--end of sugg-head-->
                           </div>
                           <!--end of main-video-->
                        </li>
                        <!--end of sugg-list-->
                        @endforeach
                        @endif
                     </ul>
                  </div>
                  <!--end of up-next-->
               </div>
               <!--end of col-sm-4-->
            </div>
         </div>
         <div class="sidebar-back"></div>
      </div>
   </div>
   <!--y-content-row-->
</div>
<?php
   $ads_timing = $video_timings = [];
   
   if(count($ads) > 0 && $ads != null) {
   
       foreach ($ads->between_ad as $key => $obj) {
   
           $video_timings[] = $obj->video_time;
   
           $ads_timing[] = $obj->ad_time;
   
       }
   }
   
   ?>
<div class="modal modal-top1"  role="dialog" id="popup1">
   <div class="modal-dialog modal-sm">
      <div class="modal-content">
         <div>
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal">&times;</button>
               <h4 class="modal-title share-title">{{tr('share')}}</h4>
            </div>
            <div class="modal-body">
               <div>
                  <a class="share-fb btn btn-primary btn-sm" target="_blank" href="http://www.facebook.com/sharer.php?u={{route('user.single',$video->video_tape_id)}}">
                  <i class="fa fa-facebook"></i>
                  </a>
                  <a class="share-twitter btn btn-info btn-sm" style="margin-left: 8px;" target="_blank" href="http://twitter.com/share?text={{$video->title}}...&url={{route('user.single',$video->video_tape_id)}}">
                  <i class="fa fa-twitter"></i>
                  </a> 
                  <input name="embed_link" class="form-control" id="embed_link" type="hidden" value="{{$embed_link}}">
                  <a class="btn btn-sm btn-success" data-toggle="modal" data-target="#copy-embed" style="margin-left: 8px; margin-top: -1px;" title="{{tr('copy_embedded_link')}}" id="copy-embed1">
                  <i class="fa fa-link"></i>
                  </a>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- PLAYLIST POPUPSTART -->

<div class="modal fade global_playlist_id_modal" id="global_playlist_id_{{$video->video_tape_id}}" role="dialog">
   
   <div class="modal-dialog">
      <!-- Modal content-->
      
      <div class="modal-content">
         
         <div class="modal-header">

            <button type="button" class="close" data-dismiss="modal">&times;</button>

            <h4 class="modal-title">{{tr('save_to')}}</h4>

         </div>

         <div class="modal-body">

            <div class="more-content" id="user-playlists-form">

               <form name="user-playlists" method="post" id="user-playlists" action="{{route('user.add.spam_video')}}">
                  
                  <input type="hidden" name="video_tape_id" value="{{$video->video_tape_id}}" />

                  <?php /** @foreach($playlists as $report)  

                     <div class="report_list">

                        <label class="playlist-container">One
                           
                           <input type="checkbox">
                           
                           <span class="playlist-checkmark"></span>

                        </label>

                     </div>

                     <div class="clearfix"></div>
                  @endforeach */ ?>

                  <div class="pull-right">
                     <button class="btn btn-info btn-sm">{{tr('submit')}}</button>
                  </div>

                  <div class="clearfix"></div>

               </form>
            
            </div>
            
         </div>
      </div>
      <!-- modal content ends -->
   </div>

</div>

<!-- <div class="modal modal-top1"  role="dialog" id="global_playlist_id_{{$video->video_tape_id}}">
  
   <div class="modal-dialog modal-sm">

      <div class="modal-content">
         <form action="" method="POST">
               <div class="modal-header">

                  <button type="button" class="close" data-dismiss="modal">&times;</button>

                  <h4 class="modal-title share-title">{{tr('save_to')}}</h4>

               </div>

               <div class="modal-body">
                  
                  <div>

                     <label class="playlist-container">One
                        <input type="checkbox" checked="checked">
                        <span class="playlist-checkmark"></span>
                     </label>
                     <label class="playlist-container">Two
                        <input type="checkbox">
                        <span class="playlist-checkmark"></span>
                     </label>
                     <label class="playlist-container">Three
                        <input type="checkbox">
                        <span class="playlist-checkmark"></span>
                     </label>
                     <label class="playlist-container">Four
                        <input type="checkbox">
                        <span class="playlist-checkmark"></span>
                     </label>

                  </div>

               </div>

         </form>
      </div>
   
   </div>

</div>
 -->
<!-- PLAYLIST POPUPEND -->


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
                     <h4 class="modal-title hidden-xs">{{tr('embed_video')}}</h4>
                     <h4 class="modal-title visible-xs">{{$video->title}}</h4>
                  </div>
                  <div class="modal-body">
                     <form onsubmit="return false;">
                        <div class="form-group">
                           <textarea class="form-control" rows="5" id="embed_link_url" readonly>{{$embed_link}}</textarea>
                           <p class="underline1"></p>
                        </div>
                     </form>
                  </div>
                  <div class="modal-footer">
                     <button class="btn btn-danger pull-right " onclick="copyTextToClipboard();" >{{tr('copy')}}</button>
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
<script src="{{asset('jwplayer/jwplayer.js')}}"></script>
<!-- wishlist animation -->
<script type="text/javascript">
   $(document).ready(function(){
   	$(".heart").on('click touchstart', function(){
   	  $(this).toggleClass('is_animating');
   	});
   
   	$(".heart").on('animationend', function(){
   	  $(this).toggleClass('is_animating');
   	});
   });
</script>
<!-- wishlist animation -->
<script type="text/javascript">

   jwplayer.key="{{Setting::get('JWPLAYER_KEY')}}";
   
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
   
   $('.view_rating').rating({disabled: true, showClear: false});
   
   $('.comment_rating').rating({showClear: false});
   
   $(document).on('ready', function() {

       $("#copy-embed1").on( "click", function() {
           $('#popup1').modal('hide'); 
       });

      $('.global_playlist_id').on('click', function(event){

         event.preventDefault();

         var video_tape_id = $(this).attr('id');

         $('#global_playlist_id_'+video_tape_id).modal('show'); 

      });
   });
   
   
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
                           jQuery("#added_wishlist").css({'font-family':'arial','background-color':'transparent','color' : '#b31217'});
   
                           if (jQuery(window).width() > 640) {
                           var append = '<i class="fa fa-heart">';
                           // var append = '<i class="fa fa-times-circle">&nbsp;&nbsp;{{tr('wishlist')}}';
                           } else {
                           var append = '<i class="fa fa-heart">';
                           }
                           jQuery("#added_wishlist").append(append);
   
                       } else {
   
                           jQuery('#status').val("1");
                           jQuery('#wishlist_id').val("");
                           jQuery("#added_wishlist").css({'font-family':'arial','background':'','color' : ''});
                           if (jQuery(window).width() > 640) {
                           var append = '<i class="fa fa-heart">';
                           // var append = '<i class="fa fa-plus-circle">&nbsp;&nbsp;{{tr('wishlist')}}';
                           } else {
                           var append = '<i class="fa fa-heart">';
                           }
   
                           jQuery("#added_wishlist").append(append);
   
                       }
   
                  } else {
   
                       // console.log('Wrong...!');
   
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
   
           var form_data = $.trim(jQuery("#comment").val());
   
           if(form_data) {
   
               $("#comment_btn").html("Sending...");
   
               $("#comment_btn").attr('disabled', true);
   
   
               //do your own request an handle the results
               jQuery.ajax({
                   url: actionurl,
                   type: 'post',
                   dataType: 'json',
                   data: jQuery("#comment_sent").serialize(),
                   success: function(data) {
   
                       $("#comment_btn").html("Comment");
   
                       $("#comment_btn").attr('disabled', false);
   
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
                               '<a><i style="'+first_star+'" class="fa fa-star-o comment-stars" aria-hidden="true"></i></a>'+
                               '<a><i style="'+second_star+'" class="fa fa-star-o comment-stars" aria-hidden="true"></i></a>'+
                               '<a><i style="'+third_star+'" class="fa fa-star-o comment-stars" aria-hidden="true"></i></a>'+
                               '<a><i style="'+fourth_star+'" class="fa fa-star-o comment-stars" aria-hidden="true"></i></a>'+
                               '<a><i style="'+fifth_star+'" class="fa fa-star-o comment-stars" aria-hidden="true"></i></a></span>';   
   
                               /**
                               <p><input id="view_rating" name="rating" type="number" class="rating view_rating" min="1" max="5" step="1" value="'+data.comment.rating+'"></p>
                               **/
   
                               if (data.comment.rating > 1) {
   
                               $('.comment_rating').rating('clear');
   
                               window.location.reload();
   
                               }
   
                               jQuery('#new-comment').prepend('<div class="display-com"><div class="com-image"><img style="width:48px;height:48px;  border-radius:24px;" src="{{Auth::user()->picture}}"></div><div class="display-comhead"><span class="sub-comhead"><a><h5 style="float:left">{{Auth::user()->name}}</h5></a><a><p>'+data.date+'</p></a><p>'+stars+'</p><p class="com-para">'+data.comment.comment+'</p></span></div></div>');
                           @endif
                       } else {
                           // console.log('Wrong...!');
                       }
                   }
               });
           } else {
   
               alert("Please fill the comment field");
   
               return false;
   
           }
   
       });
   
       var playerInstance = jwplayer("main-video-player");  
   
   
       var path = [];
   
       @if($videoStreamUrl) 
   
           path.push({file : "{{$videoStreamUrl}}", label : "Original"});
   
           path.push({file : "{{$video->video}}", label : "Original"});
   
       @else
   
           if(jQuery.browser.mobile) {
   
               $('#mainVideo').show();
   
               // console.log('You are using a mobile device!');
   
               path.push({file : "{{$hls_video}}", label : "Original"});
   
           } else {
   
           	@if(count($videoPath) > 0 && $videoPath != '')
   
               @foreach($videoPath as $path)
   
                   path.push({file : "{{$path->file}}", label : "{{$path->label}}"});
   
               @endforeach
   
               @endif
   
           }
   
       @endif
   
       var pre_ad_status = 1;
   
       var post_ad_status = 1;
   
       var between_ad_status = 0;
   
       var OnPlayStatus = 0;
   
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
           autostart : true,
           "sharing": {
            "sites": ["facebook","twitter"]
           },
           events : {
   
               onReady : function(event) {
   
                   console.log("onready");
   
               },
   
               onTime:function(event) {
   
                   // Between Ad Play
   
                   var video_time = Math.round(playerInstance.getPosition());
   
                   @if($ads)
   
                       @if(count($ads->between_ad) > 0)
   
                           @foreach($ads->between_ad as $i => $obj) 
   
                               var video_timing = "{{$obj->video_time}}";
   
                               // console.log("Video Timing "+video_timing);
   
                               var a = video_timing.split(':'); // split it at the colons
   
                               if (a.length == 3) {
                                   var seconds = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]);
                               } else {
                                   var seconds = parseInt(a[0]) * 60 + parseInt(a[1]);
                               }


                               // If the user again clicked in between seconds, it wil check whether ad is present or not. if it is enable the ad
                               if (video_time < seconds) {

                                  between_ad_status = 0;

                               }
   
                               // console.log("Seconds "+seconds);
   
                               if (video_time == seconds && between_ad_status != video_time) {
   
                                   between_ad_status = video_time;
   
                                   jwplayer().pause();
   
                                   stop();
   
                                   $("#ad_image").attr("src","{{$obj->assigned_ad->file}}");
   
                                   $("#main-video-player").css({'visibility':'hidden', 'width' : '0%'});
   
                                   $('#main_video_ad').show();
   
                                   @if($obj->assigned_ad->ad_url)
   
                                       $('.click_here_ad').html("<a target='_blank' href='{{$obj->assigned_ad->ad_url}}'>{{tr('click_here_url')}}</a>");
   
                                       $('.click_here_ad').show();
   
                                   @endif
   
   
                                   adsPage("{{$obj->ad_time}}");
   
                               }


                           @endforeach
   
                       @endif
                
   
                   @endif
               },
   
               onBeforePlay : function(event) {
   
   
               },
               onPlay : function(event) {
   
                   // between_ad_status = 0;
   
               },
   
               onComplete : function(event) {
   
                   console.log("onComplete Fn");

                   between_ad_status = 0;
   
                   @if(Auth::check())
   
                       jQuery.ajax({
                           url: "{{route('user.add.history')}}",
                           type: 'post',
                           data: {'video_tape_id' : "{{$video->video_tape_id}}"},
                           success: function(data) {
                               if(data.success == true) {
   
                                   if (data.navigateback) {
   
                                       window.location.reload(true);
   
                                   }
   
                               } else {
                                      
                               }
                           }
                       });
                       
                   @endif
   
                   // For post ad, once video completed the ad will execute
   
                   if (post_ad_status) {
   
                       @if($ads)
   
                       @if($ads->post_ad)
   
                           $("#ad_image").attr("src","{{$ads->post_ad->assigned_ad->file}}");
   
                           $("#main-video-player").css({'visibility':'hidden', 'width' : '0%'});
   
                           $('#main_video_ad').show();
   
                           @if($ads->post_ad->assigned_ad->ad_url)
   
                               $('.click_here_ad').html("<a target='_blank' href='{{$ads->post_ad->assigned_ad->ad_url}}'>{{tr('click_here_url')}}</a>");
   
                               $('.click_here_ad').show();
   
                           @endif
   
                           stop();
   
                           post_ad_status = 0;
   
                           adsPage("{{$ads->post_ad->ad_time}}");
                           
                       @endif
   
                       @endif
   
                   }
   
               }
   
           },
   
           tracks : [{
               file : "{{$video->subtitle ? $video->subtitle : ''}}",
               kind : "captions",
               default : true,
           }],
   
       });
   
       // For Pre Ad , Every first frame the ad will execute
   
       playerInstance.on('firstFrame', function() {
   
           console.log("firstFrame");
   
           post_ad_status = 1;
   
          // OnPlayStatus += 1;
   
           // if (pre_ad_status) {
   
               @if($ads)
   
                   @if($ads->pre_ad)
   
                       $("#ad_image").attr("src","{{$ads->pre_ad->assigned_ad->file}}");
   
                       $("#main-video-player").css({'visibility':'hidden', 'width' : '0%'});
   
                       $('#main_video_ad').show();
   
                       @if($ads->pre_ad->assigned_ad->ad_url)
   
                           $('.click_here_ad').html("<a target='_blank' href='{{$ads->pre_ad->assigned_ad->ad_url}}'>{{tr('click_here_url')}}</a>");
   
                           $(".click_here_ad").show();
   
                       @endif
   
                       jwplayer().pause();
   
                       pre_ad_status = 0;
   
                       adsPage("{{$ads->pre_ad->ad_time}}");
   
                   @endif
   
               @endif
   
           // }
   
       });
   
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
   
   
       jQuery("#main-video-player").show();
   
       // console.log(jwplayer().getPosition());
   
       var intervalId;
   
       var timings = "{{($ads) ? count($ads->between_ad) : 0}}";
   
       var time = 0;
   
       function timer(){
   
           intervalId = setInterval(function(){
   
               //
   
           }, 1000);
   
       }
   
       function stop(){
           clearInterval(intervalId);
       }
   
   
       var adCount = 0;
   
       function adsPage(adtimings){
   
           // alert("timings..!");
   
           $(".seconds").html(adtimings+" sec");
   
           $("#progress").html('<div class="progress-bar-div">'+
           '<span class="progress-bar-fill-div" style="width: 0%"></span>'+
           '</div>');
   
           $(".progress-bar-fill-div").css('transition', 'width '+adtimings+'s ease-in-out');
   
           $('.progress-bar-fill-div').delay(1000).queue(function () {
   
               // console.log("playig");
   
               $(this).css('width', '100%');
   
           });
   
   
           intervalId = setInterval(function(){
   
               adCount += 1;
   
               $(".seconds").html((adtimings - adCount) +" sec");
   
               // console.log("Ad Count " +adCount);
   
               // console.log("Ad Timings "+adtimings);
   
               if (adCount == adtimings) {
   
                   $(this).css('width', '100%')
   
                   adCount = 0;
   
                   stop();
   
                   $(".click_here_ad").hide();
   
                   $("#ad_image").attr("src", "");
   
                   $('#main_video_ad').hide();
   
                   $("#main-video-player").css({'visibility':'visible', 'width' : '100%'});
   
                   if (playerInstance.getState() != "complete") {
   
                       jwplayer().play();
   
                      // timer();
   
                   }
   
               }
   
           }, 1000);
   
       }
   
   });
   
   
   function copyTextToClipboard() {
   
       $("#embed_link_url").select();
   
       try {
   
           var successful = document.execCommand( 'copy' );
   
           var msg = successful ? 'successful' : 'unsuccessful';
   
           // console.log('Copying text command was ' + msg);
   
           addToast();
           // alert('Copied Embedded Link');
       } catch (err) {
           // console.log('Oops, unable to copy');
       }
   
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
   
                   // console.log(data.error_messages);
   
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
   
                   // console.log(data.error_messages);
   
               }
           },
   
           error : function(data) {
           },
       })
   }
   
   function addToast(){
       $.Toast("Embedded Link", "Link Copied Successfully.", "success", {
           has_icon:false,
           has_close_btn:true,
           stack: false,
           fullscreen:true,
           timeout:1000,
           sticky:false,
           has_progress:true,
           rtl:false,
       });
   }

   function user_subscribe_channel(user_id, channel_id) {
         alert(user_id);
         $.ajax({
         type : "post",
         url : "{{ route('user.subscribe.channel')}}",
         data : {user_id:user_id, channel_id:channel_id},
         success : function(data) {

           alert(data);

         },
            error : function(data) {

         }

    });

   }
</script>
@endsection