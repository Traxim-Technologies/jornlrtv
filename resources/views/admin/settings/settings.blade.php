@extends('layouts.admin')

@section('title', tr('settings'))

@section('content-header') 


{{ tr('settings')}}

<a href="#" id="help-popover" class="btn btn-danger" style="font-size: 14px;font-weight: 600" title="{{tr('any_help')}}">{{tr('help')}}</a>

<div id="help-content" style="display: none">

    <ul class="popover-list">

        <li><b>{{tr('paypal_')}} </b>{{tr('minimum_amount')}}</li>

        <li><b>{{tr('stripe')}}</b>{{tr('minimum_accepted')}}- <a target="_blank" href="https://stripe.com/docs/currencies">{{tr('check_refrences')}}</a></li>

        <li><b><span class="text-uppercase">{{tr('other_settings')}}</span> - {{tr('multi_channel_status')}} - </b> <span style="color: green">{{tr('checked')}}</span> -{{tr('user_create_n_channels')}}</li>

        <li><b><span class="text-uppercase">{{tr('other_settings')}} </span>- {{tr('multi_channel_status')}} - </b> <span style="color: red">{{tr('un_checked')}}</span> -{{tr('user_create_only_one_channel')}} <span style="color: #a735a7">{{tr('note')}}: {{tr('Previously_note_channel')}} </span></li>

        <li><b><span class="text-uppercase">{{tr('other_settings')}} </span>- {{tr('multi_channel_status')}} - </b> <span style="color: red">{{tr('un_checked')}}</span>{{tr('user_create_only_one_channel')}}<span style="color: #a735a7">{{tr('note')}} : {{tr('Previously_note_channel')}}</span></li>

    </ul>
    
</div>

@endsection

@section('breadcrumb')

    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>

    <li class="active"><i class="fa fa-gears"></i> {{tr('settings')}}</li>

@endsection

@section('content')

    <div class="row">

    @include('notification.notify')
    
    <div class="col-md-12">
        <div class="nav-tabs-custom">

                <ul class="nav nav-tabs">

                    <li class="active"><a href="#site_settings" data-toggle="tab">{{tr('site_settings')}}</a></li>
                    <li><a href="#video_settings" data-toggle="tab">{{tr('video_settings')}}</a></li>
                    <li><a href="#social_settings" data-toggle="tab">{{tr('social_settings')}}</a></li>
                    <li><a href="#revenue_settings" data-toggle="tab">{{tr('revenue_settings')}}</a></li>
                    <li><a href="#payment_settings" data-toggle="tab">{{tr('payment_settings')}}</a></li>
                    <li><a href="#social_media_app_settings" data-toggle="tab">{{tr('social_media_app_settings')}}</a></li>
                    <li><a href="#email_settings" data-toggle="tab">{{tr('email_settings')}}</a></li>
                    <li><a href="#other_settings" data-toggle="tab">{{tr('other_settings')}}</a></li>                    
                    
                </ul>
               
                <div class="tab-content">
                   
                    <!-- SITE SETTINGS START -->

                    <div class="active tab-pane" id="site_settings">

                        <form action="{{(Setting::get('admin_delete_control') == 1) ? '' : route('admin.save.settings')}}" method="POST" enctype="multipart/form-data" role="form">

                            <div class="box-body">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sitename">{{tr('site_name')}}</label>

                                        <input type="text" class="form-control" name="site_name" value="{{ Setting::get('site_name')  }}" id="sitename" placeholder="{{tr('enter_site_name')}}">
                                    </div>
                                </div>

                               <!--  <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tagname">{{tr('tag_name')}}</label>
                                        <input type="text" class="form-control" name="tag_name" value="{{Setting::get('tag_name')  }}" id="tagname" placeholder="Tag Name">
                                    </div>
                                </div> -->

                                <div class="col-md-3">
                                    <div class="form-group">
                                        @if(Setting::get('site_logo'))
                                            <img style="height: 50px; width:75px;margin-bottom: 15px; border-radius:2em;" src="{{Setting::get('site_logo')}}">
                                        @endif

                                        <label for="site_logo">{{tr('site_logo')}}</label>
                                        <input type="file" id="site_logo" name="site_logo" accept="image/png, image/jpeg">
                                        <p class="help-block">{{tr('image_notes')}}</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        @if(Setting::get('site_icon'))
                                            <img style="height: 50px; width:75px; margin-bottom: 15px; border-radius:2em;" src="{{Setting::get('site_icon')}}">
                                        @endif
                                        <label for="site_icon">{{tr('site_icon')}}</label>
                                        <input type="file" id="site_icon" name="site_icon" accept="image/png, image/jpeg">

                                        <p class="help-block">{{tr('image_notes')}}</p>
                                    </div>
                                </div>

                          </div>
                          <!-- /.box-body -->

                          <div class="box-footer">
                            @if(Setting::get('admin_delete_control') == 1)
                                <button type="submit" class="btn btn-primary" disabled>{{tr('submit')}}</button>
                            @else
                                <button type="submit" class="btn btn-primary">{{tr('submit')}}</button>
                            @endif
                          </div>
                        </form>
                    
                    </div>

                    <!-- SITE SETTINGS END -->

                    <!-- VIDEO SETTINGS START -->

                    <div class="tab-pane" id="video_settings">

                        <form action="{{(Setting::get('admin_delete_control') == 1) ? '' : route('admin.save.settings')}}" method="POST" enctype="multipart/form-data" role="form">
                            
                            <div class="box-body">

                                <div class="col-lg-6">

                                    <div class="form-group">

                                        <label for="streaming_url">{{tr('streaming_url')}}</label>

                                        <p class="example-note">{{tr('exam_rtmp_ip_address')}}</p>

                                        <input type="text" value="{{ Setting::get('streaming_url')}}" class="form-control" name="streaming_url" id="streaming_url" placeholder="{{tr('enter_streaming_url')}}">
                                    </div> 

                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sitename">{{tr('WEBRTC_SOCKET_URL')}}</label>
                                        <p class="example-note">{{tr('exam_ip_address_domain')}}</p>
                                        <input type="text" class="form-control" name="SOCKET_URL" value="{{ Setting::get('SOCKET_URL')  }}" id="SOCKET_URL" placeholder="{{tr('WEBRTC_SOCKET_URL')}}">
                                    </div>
                                </div>



                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="chat_socket_url">{{tr('chat_socket_url')}}</label>

                                        <p class="example-note">{{tr('exam_ip_address_domain_3002')}}</p>

                                        <input type="text" class="form-control" name="chat_socket_url" value="{{ Setting::get('chat_socket_url')  }}" id="chat_socket_url" placeholder="{{tr('chat_socket_url')}}">
                                    </div>
                                </div>

                                
                                 <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sitename">{{tr('delete_video_hour')}}</label>
                                        <br>
                                        <p>{{tr('short_notes_video_hour')}}</p>
                                        <input type="text" class="form-control" name="delete_video_hour" value="{{ Setting::get('delete_video_hour')  }}" id="delete_video_hour" placeholder="{{tr('delete_video_hour')}}" pattern="[0-9]{0,}">
                                    </div>
                                </div>

                          </div>
                          <!-- /.box-body -->
                          <div class="box-footer">
                            @if(Setting::get('admin_delete_control') == 1) 
                                <button type="submit" class="btn btn-primary" disabled>{{tr('submit')}}</button>
                            @else
                                <button type="submit" class="btn btn-primary">{{tr('submit')}}</button>
                            @endif
                          </div>
                        
                        </form>
                    
                    </div>

                    <!-- VIDEO SETTINGS END -->

                    <div class="tab-pane" id="revenue_settings">

                        <form action="{{(Setting::get('admin_delete_control') == 1) ? '' : route('admin.save.settings')}}" method="POST" enctype="multipart/form-data" role="form">
                            
                            <div class="box-body">

                                 <div class="col-lg-12">

                                     <div class="form-group">

                                        <label for="viewers_count_per_video">{{tr('viewers_count_per_video')}}</label>

                                        <p class="example-note">{{tr('viewer_count_limit')}} <i>{{tr('ads_option')}} </i> {{tr('video_count_reached')}} <i>{{tr('amt')}} </i>{{tr('last_content')}}</p>

                                        <input type="number" step="any" min="1" pattern="[0-9]+(.[0-9]{0,2})?%?" title="This must be a number with up to 2 decimal places and/or %" class="form-control" value="{{Setting::get('viewers_count_per_video')  }}" name="viewers_count_per_video" id="viewers_count_per_video" placeholder="{{tr('viewers_count_per_video')}}" pattern="[0-9]{1,}">
                                    </div>
                                </div>

                                
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="amount_per_video">{{tr('amount_per_video')}}</label>

                                        <p class="example-note">{{tr('set_amount_foreach_view')}}<i>
                                        {{tr('ads_option')}} </i> {{tr('video_count_reached')}} <i>{{tr('amt')}}</i> {{tr('last_content')}}</p>

                                        <input type="number" step="any" min="0.1" pattern="[0-9]+(.[0-9]{0,2})?%?" title="This must be a number with up to 2 decimal places and/or %" class="form-control" value="{{Setting::get('amount_per_video')  }}" name="amount_per_video" id="amount_per_video" placeholder="{{tr('amount_per_video')}}" pattern="[0-9]{1,}">
                                    </div>   
                                
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sitename">{{tr('admin_commission')}} {{tr('in_percentage')}}</label>
                                        <input type="text" class="form-control" name="admin_commission" value="{{ Setting::get('admin_commission')  }}"  id="admin_commission" placeholder="{{tr('admin_commission')}}">
                                    </div>
                               
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sitename">{{tr('user_commission')}} {{tr('in_percentage')}}</label>
                                        <input type="text" class="form-control" name="user_commission" value="{{ Setting::get('user_commission')  }}" id="user_commission" placeholder="{{tr('user_commission')}}" disabled>
                                    </div>
                                
                                </div>      

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sitename">{{tr('admin_ppv_commission')}} {{tr('in_percentage')}}</label>
                                        <input type="text" class="form-control" name="admin_ppv_commission" value="{{ Setting::get('admin_ppv_commission')  }}"  id="admin_ppv_commission" placeholder="{{tr('admin_ppv_commission')}}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sitename">{{tr('user_ppv_commission')}} {{tr('in_percentage')}}</label>
                                        <input type="text" class="form-control" name="user_ppv_commission" value="{{ Setting::get('user_ppv_commission')  }}" id="user_ppv_commission" placeholder="{{tr('user_ppv_commission')}}" disabled>
                                    </div>
                                
                                </div>                           

                          </div>
                          <!-- /.box-body -->

                          <div class="box-footer">
                            @if(Setting::get('admin_delete_control') == 1) 
                                <button type="submit" class="btn btn-primary" disabled>{{tr('submit')}}</button>
                            @else
                                <button type="submit" class="btn btn-primary">{{tr('submit')}}</button>
                            @endif
                          </div>
                        </form>
                    
                    </div>

                    <div class="tab-pane" id="other_settings">

                        <form action="{{(Setting::get('admin_delete_control') == 1) ? '' : route('admin.save.settings')}}" method="POST" enctype="multipart/form-data" role="form">
                            <div class="box-body">
                               
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="multi_channel_status">{{tr('multi_channel_status')}}</label>

                                        <br>

                                        <input type="checkbox" name="multi_channel_status" @if(Setting::get('multi_channel_status') ) checked @endif id="multi_channel_status" style="vertical-align: middle;"> {{tr('enable_channel_status')}}
                                        
                                    </div>   
                                </div>

                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="payment_type">{{tr('payment_type')}}</label>

                                        <?php $type = Setting::get('payment_type') ;?>
                                        <select id="payment_type" name="payment_type" class="form-control">
                                            <option value="">{{tr('payment_type')}}</option>
                                            
                                            <option value="paypal" @if($type == 'paypal') selected @endif>{{tr('paypal')}}</option>

                                            <option value="stripe" @if($type == 'stripe') selected @endif >{{tr('stripe')}}</option>
                                            
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="google_analytics">{{tr('google_analytics')}}</label>
                                        <textarea class="form-control" id="google_analytics" name="google_analytics">{{Setting::get('google_analytics')}}</textarea>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="header_scripts">{{tr('header_scripts')}}</label>
                                        <textarea class="form-control" id="header_scripts" name="header_scripts">{{Setting::get('header_scripts')}}</textarea>
                                    </div>
                                </div>  

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="body_scripts">{{tr('body_scripts')}}</label>
                                        <textarea class="form-control" id="body_scripts" name="body_scripts">{{Setting::get('body_scripts')}}</textarea>
                                    </div>
                                </div>   

                          </div>
                          <!-- /.box-body -->

                            <div class="box-footer">
                                @if(Setting::get('admin_delete_control') == 1) 
                                    <button type="submit" class="btn btn-primary" disabled>{{tr('submit')}}</button>
                                @else
                                    <button type="submit" class="btn btn-primary">{{tr('submit')}}</button>
                                @endif
                            </div>
                        </form>
                    
                    </div>


                    <div class="tab-pane" id="social_settings">

                        <form action="{{ (Setting::get('admin_delete_control') == 1) ? '' : route('admin.save.common-settings')}}" method="POST" enctype="multipart/form-data" role="form">
                            <div class="box-body">
                                <h4>{{tr('fb_settings')}}</h4>
                                <hr>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="fb_client_id">{{tr('FB_CLIENT_ID')}}</label>
                                        <input type="text" class="form-control" name="FB_CLIENT_ID" id="fb_client_id" placeholder="{{tr('FB_CLIENT_ID')}}" value="{{$result['FB_CLIENT_ID']}}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="fb_client_secret">{{tr('FB_CLIENT_SECRET')}}</label>    
                                        <input type="text" class="form-control" name="FB_CLIENT_SECRET" id="fb_client_secret" placeholder="{{tr('FB_CLIENT_SECRET')}}" value="{{$result['FB_CLIENT_SECRET']}}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="fb_call_back">{{tr('FB_CALL_BACK')}}</label>    
                                        <input type="text" class="form-control" name="FB_CALL_BACK" id="fb_call_back" placeholder="{{tr('FB_CALL_BACK')}}" value="{{$result['FB_CALL_BACK']}}">
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <h4>{{tr('twitter_settings')}}</h4>
                                <hr>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="twitter_client_id">{{tr('TWITTER_CLIENT_ID')}}</label>
                                        <input type="text" class="form-control" name="TWITTER_CLIENT_ID" id="twitter_client_id" placeholder="{{tr('TWITTER_CLIENT_ID')}}" value="{{$result['TWITTER_CLIENT_ID']}}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="twitter_client_secret">{{tr('TWITTER_CLIENT_SECRET')}}</label>    
                                        <input type="text" class="form-control" name="TWITTER_CLIENT_SECRET" id="twitter_client_secret" placeholder="{{tr('TWITTER_CLIENT_SECRET')}}" value="{{$result['TWITTER_CLIENT_SECRET']}}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="twitter_call_back">{{tr('TWITTER_CALL_BACK')}}</label>    
                                        <input type="text" class="form-control" name="TWITTER_CALL_BACK" id="twitter_call_back" placeholder="{{tr('TWITTER_CALL_BACK')}}" value="{{$result['TWITTER_CALL_BACK']}}">
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <h4>{{tr('google_settings')}}</h4>
                                <hr>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="google_client_id">{{tr('GOOGLE_CLIENT_ID')}}</label>
                                        <input type="text" class="form-control" name="GOOGLE_CLIENT_ID" id="google_client_id" placeholder="{{tr('GOOGLE_CLIENT_ID')}}" value="{{$result['GOOGLE_CLIENT_ID']}}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="google_client_secret">{{tr('GOOGLE_CLIENT_SECRET')}}</label>    
                                        <input type="text" class="form-control" name="GOOGLE_CLIENT_SECRET" id="google_client_secret" placeholder="{{tr('GOOGLE_CLIENT_SECRET')}}" value="{{$result['GOOGLE_CLIENT_SECRET']}}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="google_call_back">{{tr('GOOGLE_CALL_BACK')}}</label>    
                                        <input type="text" class="form-control" name="GOOGLE_CALL_BACK" id="google_call_back" placeholder="{{tr('GOOGLE_CALL_BACK')}}" value="{{$result['GOOGLE_CALL_BACK']}}">
                                    </div>
                                </div>
                                <div class='clearfix'></div>
                            </div>
                            <div class="box-footer">
                                @if(Setting::get('admin_delete_control') == 1) 
                                    <button type="submit" class="btn btn-primary" disabled>{{tr('submit')}}</button>
                                @else
                                    <button type="submit" class="btn btn-primary">{{tr('submit')}}</button>
                                @endif
                          </div>
                        </form>

                    </div>

                    <div class="tab-pane" id="site_url_settings">

                        <form action="{{ (Setting::get('admin_delete_control') == 1) ? '' : route('admin.save.settings')}}" method="POST" enctype="multipart/form-data" role="form">
                            <div class="box-body">
                                <div class="col-md-12">
                                    <div class="form-group">

                                        <label for="viewers_count_per_video">{{tr('video_viewer_count_size_label')}}</label>

                                        <br>

                                        <p class="example-note">{{tr('video_viewer_count_size_label_note')}}</p>

                                        <input type="text" class="form-control" name="viewers_count_per_video" value="{{Setting::get('viewers_count_per_video')  }}" id="viewers_count_per_video" placeholder="{{tr('video_viewer_count_size_label')}}">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="amount_per_video">{{tr('amount_per_video')}}</label>
                                        
                                        <br>
                                        
                                        <p class="example-note">{{tr('amount_per_video_note')}}</p>

                                        <input type="text" class="form-control" name="amount_per_video" value="{{Setting::get('amount_per_video')  }}" min="0.5" id="amount_per_video" placeholder="{{tr('amount_per_video')}}">

                                    </div>
                                </div>

                                <div class="clearfix"></div>
                                
                            </div>
                            
                            <div class="box-footer">
                                @if(Setting::get('admin_delete_control') == 1) 
                                    <button type="submit" class="btn btn-primary" disabled>{{tr('submit')}}</button>
                                @else
                                    <button type="submit" class="btn btn-primary">{{tr('submit')}}</button>
                                @endif
                          </div>
                        </form>

                    </div>

                    <div class="tab-pane" id="social_media_app_settings">

                        <form action="{{ (Setting::get('admin_delete_control') == 1) ? '' : route('admin.save.settings')}}" method="POST" enctype="multipart/form-data" role="form">
                            <div class="box-body">

                                <h3 class="settings-sub-header">{{tr('app_url_settings')}}</h3>
                                <hr>

                                <div class="col-md-6">
                                    <div class="form-group">

                                        <label for="upload_max_size">{{tr('appstore')}}</label>

                                        <input type="url" class="form-control" name="appstore" id="appstore"
                                        value="{{Setting::get('appstore')}}" placeholder="{{tr('appstore')}}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="upload_max_size">{{tr('playstore')}}</label>

                                        <input type="url" class="form-control" name="playstore" value="{{Setting::get('playstore')  }}" id="playstore" placeholder="{{tr('playstore')}}">

                                    </div>
                                </div>

                                <h3 class="settings-sub-header">{{tr('social_media_settings')}}</h3>
                                <hr>

                                <div class="col-md-6">
                                    <div class="form-group">

                                        <label for="upload_max_size">{{tr('facebook_link')}}</label>

                                        <input type="url" class="form-control" name="facebook_link" id="facebook_link"
                                        value="{{Setting::get('facebook_link')}}" placeholder="{{tr('facebook_link')}}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="upload_max_size">{{tr('linkedin_link')}}</label>

                                        <input type="url" class="form-control" name="linkedin_link" value="{{Setting::get('linkedin_link')  }}" id="linkedin_link" placeholder="{{tr('linkedin_link')}}">

                                    </div>
                                </div>

                                 <div class="col-md-6">
                                    <div class="form-group">

                                        <label for="upload_max_size">{{tr('twitter_link')}}</label>

                                        <input type="url" class="form-control" name="twitter_link" value="{{Setting::get('twitter_link')  }}" id="twitter_link" placeholder="{{tr('twitter_link')}}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="upload_max_size">{{tr('google_plus_link')}}</label>
                                        <input type="url" class="form-control" name="google_plus_link" value="{{Setting::get('google_plus_link')  }}" id="google_plus_link" placeholder="{{tr('google_plus_link')}}">
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="upload_max_size">{{tr('pinterest_link')}}</label>
                                        <input type="url" class="form-control" name="pinterest_link" value="{{Setting::get('pinterest_link')  }}" id="pinterest_link" placeholder="{{tr('pinterest_link')}}">
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                
                            </div>
                            <div class="box-footer">
                                @if(Setting::get('admin_delete_control') == 1) 
                                    <button type="submit" class="btn btn-primary" disabled>{{tr('submit')}}</button>
                                @else
                                    <button type="submit" class="btn btn-primary">{{tr('submit')}}</button>
                                @endif
                          </div>
                        </form>
                    
                    </div>


                    <div class="tab-pane" id="other_settings">

                        <form action="{{(Setting::get('admin_delete_control') == 1) ? '' : route('admin.save.settings')}}" method="POST" enctype="multipart/form-data" role="form">
                            
                            <div class="box-body">

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="multi_channel_status">{{tr('multi_channel_status')}}</label>

                                        <br>

                                        <input type="checkbox" name="multi_channel_status" @if(Setting::get('multi_channel_status') ) checked @endif id="multi_channel_status" style="vertical-align: middle;"> {{tr('enable_channel_status')}}
                                        
                                    </div>   
                                </div>

                               
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="payment_type">{{tr('payment_type')}}</label>

                                        <?php $type = Setting::get('payment_type') ;?>
                                        <select id="payment_type" name="payment_type" class="form-control">
                                            <option value="">{{tr('payment_type')}}</option>
                                            
                                            <option value="paypal" @if($type == 'paypal') selected @endif>{{tr('paypal')}}</option>

                                            <option value="stripe" @if($type == 'stripe') selected @endif >{{tr('stripe')}}</option>
                                            
                                        </select>
                                    </div>
                                </div>

                                
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="google_analytics">{{tr('google_analytics')}}</label>
                                        <textarea class="form-control" id="google_analytics" name="google_analytics">{{Setting::get('google_analytics')}}</textarea>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="header_scripts">{{tr('header_scripts')}}</label>
                                        <textarea class="form-control" id="header_scripts" name="header_scripts">{{Setting::get('header_scripts')}}</textarea>
                                    </div>
                                </div>  

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="body_scripts">{{tr('body_scripts')}}</label>
                                        <textarea class="form-control" id="body_scripts" name="body_scripts">{{Setting::get('body_scripts')}}</textarea>
                                    </div>
                                </div>   

                          </div>
                          <!-- /.box-body -->

                          <div class="box-footer">
                            @if(Setting::get('admin_delete_control') == 1) 
                                <button type="submit" class="btn btn-primary" disabled>{{tr('submit')}}</button>
                            @else
                                <button type="submit" class="btn btn-primary">{{tr('submit')}}</button>
                            @endif
                          </div>
                        </form>
                    
                    </div>

                    <div class="tab-pane" id="email_settings">
                        <form action="{{route('admin.email.settings.save')}}" method="POST" enctype="multipart/form-data" role="form">
                            
                            <div class="box-body">

                                <div class="col-md-6">

                                    <div class="form-group">
                                        <label for="paypal_client_id">{{tr('MAIL_DRIVER')}}</label>
                                        <input type="text" value="{{ $result['MAIL_DRIVER']}}" class="form-control" name="MAIL_DRIVER" id="MAIL_DRIVER" placeholder="Enter {{tr('MAIL_DRIVER')}}">
                                    </div>

                                    <div class="form-group">
                                        <label for="MAIL_HOST">{{tr('MAIL_HOST')}}</label>
                                        <input type="text" class="form-control" value="{{$result['MAIL_HOST']}}" name="MAIL_HOST" id="MAIL_HOST" placeholder="{{tr('MAIL_HOST')}}">
                                    </div>

                                    <div class="form-group">
                                        <label for="MAIL_PORT">{{tr('MAIL_PORT')}}</label>
                                        <input type="text" class="form-control" value="{{$result['MAIL_PORT']}}" name="MAIL_PORT" id="MAIL_PORT" placeholder="{{tr('MAIL_PORT')}}">
                                    </div>

                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="MAIL_USERNAME">{{tr('MAIL_USERNAME')}}</label>
                                        <input type="text" class="form-control" value="{{$result['MAIL_USERNAME'] }}" name="MAIL_USERNAME" id="MAIL_USERNAME" placeholder="{{tr('MAIL_USERNAME')}}">
                                    </div>

                                    <div class="form-group">
                                        <label for="MAIL_PASSWORD">{{tr('MAIL_PASSWORD')}}</label>
                                        <input type="password" class="form-control" name="MAIL_PASSWORD" id="MAIL_PASSWORD" placeholder="{{tr('MAIL_PASSWORD')}}" value="{{$result['MAIL_PASSWORD']}}">
                                    </div>

                                    <div class="form-group">
                                        <label for="MAIL_PORT">{{tr('MAIL_ENCRYPTION')}}</label>
                                        <input type="text" class="form-control" value="{{$result['MAIL_ENCRYPTION'] }}" name="MAIL_ENCRYPTION" id="MAIL_ENCRYPTION" placeholder="{{tr('MAIL_ENCRYPTION')}}">
                                    </div>

                                </div>

                          </div>
                          <!-- /.box-body -->

                            <div class="box-footer">
                                @if(Setting::get('admin_delete_control'))
                                    <a href="#" class="btn btn-success pull-right" disabled>{{tr('submit')}}</a>
                                @else
                                    <button type="submit" class="btn btn-success pull-right">{{tr('submit')}}</button>
                                @endif
                            </div>
                        </form>
                    
                    </div>

                    <div class="tab-pane" id="app_url_settings">

                        <form action="{{ (Setting::get('admin_delete_control') == 1) ? '' : route('admin.save.settings')}}" method="POST" enctype="multipart/form-data" role="form">
                            <div class="box-body">
                                <div class="col-md-6">
                                    <div class="form-group">

                                        <label for="upload_max_size">{{tr('appstore')}}</label>

                                        <input type="url" class="form-control" name="appstore" id="appstore"
                                        value="{{Setting::get('appstore')}}" placeholder="{{tr('appstore')}}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="upload_max_size">{{tr('playstore')}}</label>

                                        <input type="url" class="form-control" name="playstore" value="{{Setting::get('playstore')  }}" id="playstore" placeholder="{{tr('playstore')}}">

                                    </div>
                                </div>
                                
                            </div>
                            <div class="box-footer">
                                @if(Setting::get('admin_delete_control') == 1) 
                                    <button type="submit" class="btn btn-primary" disabled>{{tr('submit')}}</button>
                                @else
                                    <button type="submit" class="btn btn-primary">{{tr('submit')}}</button>
                                @endif
                          </div>
                        </form>

                    </div>

                    <div class="tab-pane" id="payment_settings">

                        <form action="{{ (Setting::get('admin_delete_control') == 1) ? '' : route('admin.save.common-settings')}}" method="POST" enctype="multipart/form-data" role="form">
                            <div class="box-body">
                                <h4>{{tr('payment_settings')}}</h4>
                                <hr>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="paypal_id">{{tr('PAYPAL_ID')}}</label>
                                        <input type="text" class="form-control" name="PAYPAL_ID" id="paypal_id" placeholder="{{tr('PAYPAL_ID')}}" value="{{$result['PAYPAL_ID']}}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="paypal_secret">{{tr('PAYPAL_SECRET')}}</label>    
                                        <input type="text" class="form-control" name="PAYPAL_SECRET" id="paypal_secret" placeholder="{{tr('PAYPAL_SECRET')}}" value="{{$result['PAYPAL_SECRET']}}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="paypal_mode">{{tr('PAYPAL_MODE')}}</label>    
                                        <input type="text" class="form-control" name="PAYPAL_MODE" id="paypal_mode" placeholder="{{tr('PAYPAL_MODE')}}" value="{{$result['PAYPAL_MODE']}}">
                                    </div>
                                </div>

                                <div class="clearfix"></div>

                                <h4>{{tr('stripe_settings')}}</h4>
                                <hr>
                                 <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="paypal_id">{{tr('stripe_publishable_key')}}</label>
                                        <input type="text" class="form-control" name="stripe_publishable_key" id="stripe_publishable_key" placeholder="{{tr('stripe_publishable_key')}}" value="{{Setting::get('stripe_publishable_key')}}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="paypal_secret">{{tr('stripe_secret_key')}}</label>
                                        <input type="text" class="form-control" name="stripe_secret_key" id="stripe_secret_key" placeholder="{{tr('stripe_secret_key')}}" value="{{Setting::get('stripe_secret_key')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                @if(Setting::get('admin_delete_control') == 1) 
                                    <button type="submit" class="btn btn-primary" disabled>{{tr('submit')}}</button>
                                @else
                                    <button type="submit" class="btn btn-primary">{{tr('submit')}}</button>
                                @endif
                          </div>
                        </form>

                    </div>

                </div>

            </div>
        </div>
    
    </div>

@endsection

@section('scripts')

<script>
    
    $('#admin_commission').on('keyup' , function() {

        var admin_commission = $('#admin_commission').val();

        if(admin_commission <=100) {

            var user_commission = $('#user_commission');

            var commission = 100 - admin_commission;

            user_commission.val(commission);

        } else {

            $('#admin_commission').val(0);

        }

        

    });
</script>


@endsection