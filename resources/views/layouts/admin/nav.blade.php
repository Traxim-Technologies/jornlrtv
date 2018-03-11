<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="@if(Auth::guard('admin')->user()->picture){{Auth::guard('admin')->user()->picture}} @else {{asset('admin-css/dist/img/avatar.png')}} @endif" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{Auth::guard('admin')->user()->name}}</p>
                <a href="{{route('admin.profile')}}">{{ tr('admin') }}</a>
            </div>
            <div class="clearfix" style="height: 10px;clear:both"></div>
        </div>

        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">

            <li id="dashboard">
              <a href="{{route('admin.dashboard')}}">
                <i class="fa fa-dashboard"></i> <span>{{tr('dashboard')}}</span>
              </a>
              
            </li>

            <li class="treeview" id="users">

                <a href="#">
                    <i class="fa fa-user"></i> <span>{{tr('users')}}</span> <i class="fa fa-angle-left pull-right"></i>
                </a>

                <ul class="treeview-menu">
                    <li id="add-user"><a href="{{route('admin.add.user')}}"><i class="fa fa-circle-o"></i>{{tr('add_user')}}</a></li>
                    <li id="view-user"><a href="{{route('admin.users')}}"><i class="fa fa-circle-o"></i>{{tr('view_users')}}</a></li>
                </ul>
    
            </li>

            <li class="treeview" id="channels">
                <a href="{{route('admin.channels')}}">
                    <i class="fa fa-tv"></i> <span>{{tr('channels')}}</span> <i class="fa fa-angle-left pull-right"></i>
                </a>

                <ul class="treeview-menu">
                    <li id="add-channel"><a href="{{route('admin.add.channel')}}"><i class="fa fa-circle-o"></i>{{tr('add_channel')}}</a></li>
                    <li id="view-channels"><a href="{{route('admin.channels')}}"><i class="fa fa-circle-o"></i>{{tr('view_channels')}}</a></li>
                     <li id="subscribers"><a href="{{route('admin.subscribers')}}"><i class="fa fa-circle-o"></i>{{tr('subscribers')}}</a></li>
                </ul>

            </li>

            <li class="treeview" id="videos">
                
                <a href="{{route('admin.videos')}}">
                    <i class="fa fa-video-camera"></i> <span>{{tr('videos')}}</span> <i class="fa fa-angle-left pull-right"></i>
                </a>

                <ul class="treeview-menu">

                    <li id="add-video">
                        <a href="{{route('admin.add.video')}}">
                            <i class="fa fa-circle-o"></i>{{tr('add_video')}}
                        </a>
                    </li>

                    <li id="view-videos">
                        <a href="{{route('admin.videos')}}">
                            <i class="fa fa-circle-o"></i>{{tr('view_videos')}}
                        </a>
                    </li>

                    @if(Setting::get('is_spam'))

                        <li id="spam_videos">
                            <a href="{{route('admin.spam-videos')}}">
                                <i class="fa fa-flag"></i>{{tr('spam_videos')}}
                            </a>
                        </li>

                    @endif

                    <li id="reviews">
                        <a href="{{route('admin.reviews')}}">
                            <i class="fa fa-star"></i>{{tr('reviews')}}
                        </a>
                    </li>


                </ul>

            </li>

            <li class="treeview" id="videos_ads">

                <a href="{{route('admin.ad_index')}}">
                    <i class="fa fa-bullhorn"></i> <span>{{tr('ads')}}</span> <i class="fa fa-angle-left pull-right"></i>
                </a>

                <ul class="treeview-menu">
                    <li id="create-ad-videos"><a href="{{route('admin.ad_create')}}"><i class="fa fa-circle-o"></i>{{tr('create_ad')}}</a></li>
                    <li id="view-ads"><a href="{{route('admin.ad_index')}}"><i class="fa fa-circle-o"></i>{{tr('view_ads')}}</a></li>
                    <li id="ad-videos"><a href="{{route('admin.ad_videos')}}"><i class="fa fa-circle-o"></i>{{tr('ads')}}</a></li>
                </ul>

            </li>

            @if(Setting::get('is_banner_ad'))

                <li class="treeview" id="bannerads_nav">
                    <a href="{{route('admin.banner-ads.index')}}">
                        <i class="fa fa-university"></i> <span>{{tr('banner_ads')}}</span> <i class="fa fa-angle-left pull-right"></i>
                    </a>

                    <ul class="treeview-menu">
                       
                        <li id="bannerads-create"><a href="{{route('admin.banner-ads.create')}}"><i class="fa fa-circle-o"></i>{{tr('create_banner_ad')}}</a></li>
                    
                        <li id="bannerads-index"><a href="{{route('admin.banner-ads.index')}}"><i class="fa fa-circle-o"></i>{{tr('banner_ads')}}</a></li>

                    </ul>

                </li>

            @endif

            @if(Setting::get('is_banner_video'))

            <li class="treeview" id="banner-videos">
                <a href="{{route('admin.banner.videos')}}">
                    <i class="fa fa-university"></i> <span>{{tr('banner_videos')}}</span> <i class="fa fa-angle-left pull-right"></i>
                </a>

                <ul class="treeview-menu">
                    @if(get_banner_count() < 6)
                        <li id="add-banner-video"><a href="{{route('admin.add.banner.video')}}"><i class="fa fa-circle-o"></i>{{tr('add_video')}}</a></li>
                    @endif
                    <li id="view-banner-videos"><a href="{{route('admin.banner.videos')}}"><i class="fa fa-circle-o"></i>{{tr('view_videos')}}</a></li>
                </ul>

            </li>

            @endif

            <li class="treeview" id="subscriptions">

                <a href="#">
                    <i class="fa fa-key"></i> <span>{{tr('subscriptions')}}</span> <i class="fa fa-angle-left pull-right"></i>
                </a>

                <ul class="treeview-menu">

                    <li id="subscriptions-add"><a href="{{route('admin.subscriptions.create')}}"><i class="fa fa-circle-o"></i>{{tr('add_subscription')}}</a></li>
                    <li id="subscriptions-view"><a href="{{route('admin.subscriptions.index')}}"><i class="fa fa-circle-o"></i>{{tr('view_subscriptions')}}</a></li>
                </ul>
            </li>

            @if(Setting::get('redeem_control'))

            <li id="redeems">
                <a href="{{route('admin.users.redeems')}}">
                    <i class="fa fa-trophy"></i> <span>{{tr('redeems')}}</span> 
                </a>
            </li>

            @endif

            <li class="treeview" id="payments">

                <a href="#">
                    <i class="fa fa-key"></i> <span>{{tr('payments')}}</span> <i class="fa fa-angle-left pull-right"></i>
                </a>

                <ul class="treeview-menu">

                    <li id="payments-dashboard">
                        <a href="{{route('admin.revenues')}}">
                            <i class="fa fa-circle-o"></i>
                            {{tr('revenues')}}
                        </a>
                    </li>

                    <li id="payments-subscriptions">
                        <a href="{{route('admin.subscription.payments')}}">
                            <i class="fa fa-credit-card"></i>
                            {{tr('subscription_payments')}}
                        </a>
                    </li>

                    <li id="payments-ppv">
                        <a href="{{route('admin.ppv_payments')}}">
                            <i class="fa fa-circle-o"></i>
                            {{tr('ppv_payments')}}
                        </a>
                    </li>
                </ul>
            </li>


            <li id="settings">
                <a href="{{route('admin.settings')}}">
                    <i class="fa fa-gears"></i> <span>{{tr('settings')}}</span>
                </a>
            </li>

            @if(Setting::get('admin_language_control'))
            <li id="languages">
                <a href="{{route('admin.languages.index')}}">
                    <i class="fa fa-globe"></i> <span>{{tr('languages')}}</span>
                </a>
            </li>
            @endif

            <li id="custom-push">
                <a href="{{route('admin.push')}}">
                    <i class="fa fa-send"></i> <span>{{tr('custom_push')}}</span>
                </a>
            </li>

            <li class="treeview" id="viewpages">
                <a href="{{route('admin.pages.index')}}">
                    <i class="fa fa-book"></i> <span>{{tr('pages')}}</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li id="add_page"><a href="{{route('admin.pages.create')}}"><i class="fa fa-circle-o"></i>{{tr('add_page')}}</a></li>
                    <li id="view_pages"><a href="{{route('admin.pages.index')}}"><i class="fa fa-circle-o"></i>{{tr('view_pages')}}</a></li>
                </ul>
            </li>

            <li id="profile">
                <a href="{{route('admin.profile')}}">
                    <i class="fa fa-diamond"></i> <span>{{tr('account')}}</span>
                </a>
            </li>


            <li id="help">
                <a href="{{route('admin.help')}}">
                    <i class="fa fa-question-circle"></i> <span>{{tr('help_1')}}</span>
                </a>
            </li>

            <li>
                <a href="{{route('admin.logout')}}">
                    <i class="fa fa-sign-out"></i> <span>{{tr('sign_out')}}</span>
                </a>
            </li>

        </ul>

    </section>

    <!-- /.sidebar -->

</aside>