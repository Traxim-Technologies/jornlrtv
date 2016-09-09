@extends('layouts.moderator')

@section('title', 'Profile')

@section('content-header', 'Profile')

@section('breadcrumb')
    <li><a href="#"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-diamond"></i> {{tr('account')}}</li>
@endsection

@section('content')

@include('notification.notify')


    <div class="row">

        <div class="col-md-4">

            <div class="box box-primary">

                <div class="box-body box-profile">

                    <img class="profile-user-img img-responsive img-circle" src="@if(Auth::guard('moderator')->user()->picture) {{Auth::guard('moderator')->user()->picture}} @else {{asset('admin-css/dist/img/avatar.png')}} @endif" alt="User profile picture">

                    <h3 class="profile-username text-center">{{Auth::guard('moderator')->user()->name}}</h3>

                    <p class="text-muted text-center">Moderator</p>

                    <ul class="list-group list-group-unbordered">
                        <li class="list-group-item">
                            <b>Username</b> <a class="pull-right">{{Auth::guard('moderator')->user()->name}}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Email</b> <a class="pull-right">{{Auth::guard('moderator')->user()->email}}</a>
                        </li>

                        <li class="list-group-item">
                            <b>Mobile</b> <a class="pull-right">{{Auth::guard('moderator')->user()->mobile}}</a>
                        </li>

                        <li class="list-group-item">
                            <b>Address</b> <a class="pull-right">{{Auth::guard('moderator')->user()->address}}</a>
                        </li>
                    </ul>
                
                </div>

            </div>

        </div>

         <div class="col-md-8">
            <div class="nav-tabs-custom">

                <ul class="nav nav-tabs">
                    <li class="active"><a href="#profile" data-toggle="tab">Update Profile</a></li>
                    <li><a href="#image" data-toggle="tab">Upload Image</a></li>
                    <li><a href="#password" data-toggle="tab">Change Password</a></li>
                </ul>
               
                <div class="tab-content">
                   
                    <div class="active tab-pane" id="profile">

                        <form class="form-horizontal" action="{{route('moderator.save.profile')}}" method="POST" enctype="multipart/form-data" role="form">

                            <input type="hidden" name="id" value="{{Auth::guard('moderator')->user()->id}}">

                            <div class="form-group">
                                <label for="name" required class="col-sm-2 control-label">Username</label>

                                <div class="col-sm-10">
                                  <input type="text" class="form-control" id="name"  name="name" value="{{Auth::guard('moderator')->user()->name}}" placeholder="Username">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email" class="col-sm-2 control-label">Email</label>

                                <div class="col-sm-10">
                                  <input type="email" required value="{{Auth::guard('moderator')->user()->email}}" name="email" class="form-control" id="email" placeholder="Email">
                                </div>
                            </div>


                            <div class="form-group">
                                <label for="mobile" class="col-sm-2 control-label">Mobile</label>

                                <div class="col-sm-10">
                                  <input type="text" required value="{{Auth::guard('moderator')->user()->mobile}}" name="mobile" class="form-control" id="mobile" placeholder="Mobile">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="address" class="col-sm-2 control-label">Address</label>

                                <div class="col-sm-10">
                                  <input type="text" required value="{{Auth::guard('moderator')->user()->address}}" name="address" class="form-control" id="address" placeholder="Address">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                  <button type="submit" class="btn btn-danger">Submit</button>
                                </div>
                            </div>

                        </form>
                    </div>

                    <div class="tab-pane" id="image">

                        <form class="form-horizontal" action="{{route('moderator.save.profile')}}" method="POST" enctype="multipart/form-data" role="form">

                            <input type="hidden" name="id" value="{{Auth::guard('moderator')->user()->id}}">

                            @if(count(Auth::guard('moderator')->user()->picture) > 0)
                                <img style="height: 90px; margin-bottom: 15px; border-radius:2em;" src="{{Auth::guard('moderator')->user()->picture}}">
                            @else
                                <img style="height: 90px; margin-bottom: 15px; border-radius:2em;"  src="{{asset('logo.png')}}">
                            @endif

                            <div class="form-group">
                                <label for="picture" class="col-sm-2 control-label">Picture</label>

                                <div class="col-sm-10">
                                  <input type="file" required class="form-control" name="picture" id="picture">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                  <button type="submit" class="btn btn-danger">Submit</button>
                                </div>
                            </div>

                        </form>
                    </div>

                    <div class="tab-pane" id="password">

                        <form class="form-horizontal" action="{{route('moderator.change.password')}}" method="POST" enctype="multipart/form-data" role="form">

                            <input type="hidden" name="id" value="{{Auth::guard('moderator')->user()->id}}">

                            <div class="form-group">
                                <label for="old_password" class="col-sm-3 control-label">Old Password</label>

                                <div class="col-sm-8">
                                  <input required type="password" class="form-control" name="old_password" id="old_password" placeholder="Old Password">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password" class="col-sm-3 control-label">New Password</label>

                                <div class="col-sm-8">
                                  <input required type="password" class="form-control" name="password" id="password" placeholder="New Password">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password" class="col-sm-3 control-label">Confirm Password</label>

                                <div class="col-sm-8">
                                  <input required type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm Password">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                  <button type="submit" class="btn btn-danger">Submit</button>
                                </div>
                            </div>

                        </form>

                    </div>

                </div>

            </div>
        </div>

    </div>

@endsection