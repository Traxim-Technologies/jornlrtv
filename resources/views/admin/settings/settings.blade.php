@extends('layouts.admin')

@section('title', tr('settings'))

@section('content-header', tr('settings'))

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
                    <li><a href="#other_settings" data-toggle="tab">{{tr('other_settings')}}</a></li>
                    <!-- <li><a href="#s3_settings" data-toggle="tab">{{tr('s3_settings')}}</a></li> -->
                    <li><a href="#social_settings" data-toggle="tab">{{tr('social_settings')}}</a></li>
                    <li><a href="#paypal_settings" data-toggle="tab">{{tr('paypal_settings')}}</a></li>
                </ul>
               
                <div class="tab-content">
                   
                    <div class="active tab-pane" id="site_settings">

                        <form action="{{(Setting::get('admin_delete_control') == 1) ? '' : route('admin.save.settings')}}" method="POST" enctype="multipart/form-data" role="form">

                            <div class="box-body">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sitename">{{tr('site_name')}}</label>
                                        <input type="text" class="form-control" name="site_name" value="{{ Setting::get('site_name')  }}" id="sitename" placeholder="Enter sitename">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tagname">{{tr('tag_name')}}</label>
                                        <input type="text" class="form-control" name="tag_name" value="{{Setting::get('tag_name')  }}" id="tagname" placeholder="Tag Name">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        @if(Setting::get('site_logo'))
                                            <img style="height: 50px; width:75px;margin-bottom: 15px; border-radius:2em;" src="{{Setting::get('site_logo')}}">
                                        @endif

                                        <label for="site_logo">{{tr('site_logo')}}</label>
                                        <input type="file" id="site_logo" name="site_logo" accept="image/png, image/jpeg">
                                        <p class="help-block">Please enter .png images only.</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        @if(Setting::get('site_icon'))
                                            <img style="height: 50px; width:75px; margin-bottom: 15px; border-radius:2em;" src="{{Setting::get('site_icon')}}">
                                        @endif
                                        <label for="site_icon">{{tr('site_icon')}}</label>
                                        <input type="file" id="site_icon" name="site_icon" accept="image/png, image/jpeg">
                                        <p class="help-block">Please enter .png images only.</p>
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
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="streaming_url">{{tr('streaming_url')}}</label>
                                        <input type="text" value="{{ Setting::get('streaming_url')}}" class="form-control" name="streaming_url" id="streaming_url" placeholder="Enter Streaming URL">
                                    </div> 
                                </div>

                                @if(Setting::get('is_subscription'))

                                    <div class="col-lg-3">
                                         <div class="form-group">
                                            <label for="amount">{{tr('amount')}}</label>
                                            <input type="text" class="form-control" value="{{Setting::get('amount')  }}" name="amount" id="amount" placeholder="{{tr('amount')}}" pattern="[0-9]{1,}">
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="expiry_days">{{tr('expiry_days')}}</label>
                                            <input type="text" class="form-control" value="{{Setting::get('expiry_days')  }}" name="expiry_days" id="expiry_days" placeholder="{{tr('expiry_days')}}" pattern="[0-9]{1,}">
                                        </div>   
                                    </div>

                                @endif

                                 <div class="col-lg-3">
                                     <div class="form-group">
                                        <label for="viewers_count_per_video">{{tr('viewers_count_per_video')}}</label>
                                        <input type="number" step="any" min="1" pattern="[0-9]+(.[0-9]{0,2})?%?" title="This must be a number with up to 2 decimal places and/or %" class="form-control" value="{{Setting::get('viewers_count_per_video')  }}" name="viewers_count_per_video" id="viewers_count_per_video" placeholder="{{tr('viewers_count_per_video')}}" pattern="[0-9]{1,}">
                                    </div>
                                </div>
                                
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="amount_per_video">{{tr('amount_per_video')}}</label>
                                        <input type="number" step="any" min="0.1" pattern="[0-9]+(.[0-9]{0,2})?%?" title="This must be a number with up to 2 decimal places and/or %" class="form-control" value="{{Setting::get('amount_per_video')  }}" name="amount_per_video" id="amount_per_video" placeholder="{{tr('amount_per_video')}}" pattern="[0-9]{1,}">
                                    </div>   
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="multi_channel_status">{{tr('multi_channel_status')}}</label>

                                        <br>

                                        <input type="checkbox" name="multi_channel_status" @if(Setting::get('multi_channel_status') ) checked @endif id="multi_channel_status" style="vertical-align: middle;"> {{tr('enable_channel_status')}}
                                        
                                    </div>   
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="post_max_size">{{tr('post_max_size_label')}}</label>
                                        <input type="text" class="form-control" name="post_max_size" value="{{ Setting::get('post_max_size')  }}" id="post_max_size" placeholder="{{tr('post_max_size_label')}}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="upload_max_size">{{tr('max_upload_size_label')}}</label>
                                        <input type="text" class="form-control" name="upload_max_size" value="{{Setting::get('upload_max_size')  }}" id="upload_max_size" placeholder="{{tr('max_upload_size_label')}}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="payment_type">{{tr('payment_type')}}</label>

                                        <?php $type = Setting::get('payment_type') ;?>
                                        <select id="payment_type" name="payment_type" class="form-control">
                                            <option value="">{{tr('payment_type')}}</option>
                                            
                                            <option value="paypal" @if($type == 'paypal') selected @endif>Paypal</option>

                                            <option value="stripe" @if($type == 'stripe') selected @endif >Stripe</option>
                                            
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
                                        <label for="body_end_scripts">{{tr('body_end_scripts')}}</label>
                                        <textarea class="form-control" id="body_end_scripts" name="body_end_scripts">{{Setting::get('body_end_scripts')}}</textarea>
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

                    <div class="tab-pane" id="s3_settings1">

                        <form action="{{ (Setting::get('admin_delete_control') == 1) ? '' : route('admin.save.common-settings')}}" method="POST" enctype="multipart/form-data" role="form">
                            <div class="box-body">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="s3_key">{{tr('S3_KEY')}}</label>
                                        <input type="text" class="form-control" name="S3_KEY" id="s3_key" placeholder="{{tr('S3_KEY')}}" value="{{$result['S3_KEY']}}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="s3_secret">{{tr('S3_SECRET')}}</label>    
                                        <input type="text" class="form-control" name="S3_SECRET" id="s3_secret" placeholder="{{tr('S3_SECRET')}}" value="{{$result['S3_SECRET']}}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="s3_region">{{tr('S3_REGION')}}</label>    
                                        <input type="text" class="form-control" name="S3_REGION" id="s3_region" placeholder="{{tr('S3_REGION')}}" value="{{$result['S3_REGION']}}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="s3_bucket">{{tr('S3_BUCKET')}}</label>    
                                        <input type="text" class="form-control" name="S3_BUCKET" id="s3_bucket" placeholder="{{tr('S3_BUCKET')}}" value="{{$result['S3_BUCKET']}}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="s3_ses_region">{{tr('S3_SES_REGION')}}</label>    
                                        <input type="text" class="form-control" name="S3_SES_REGION" id="s3_ses_region" placeholder="{{tr('S3_SES_REGION')}}" value="{{$result['S3_SES_REGION']}}">
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

                    <div class="tab-pane" id="paypal_settings">

                        <form action="{{ (Setting::get('admin_delete_control') == 1) ? '' : route('admin.save.common-settings')}}" method="POST" enctype="multipart/form-data" role="form">
                            <div class="box-body">
    
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