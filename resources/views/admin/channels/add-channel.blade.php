@extends('layouts.admin')

@section('title', tr('add_channel'))

@section('content-header', tr('add_channel'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('admin.channels')}}"><i class="fa fa-suitcase"></i> {{tr('channels')}}</a></li>
    <li class="active">{{tr('add_channel')}}</li>
@endsection

@section('content')

@include('notification.notify')

    <div class="row">

        <div class="col-md-10">

            <div class="box box-primary">

                <div class="box-header label-primary">
                    <b style="font-size:18px;">{{tr('add_channel')}}</b>
                    <a href="{{route('admin.channels')}}" class="btn btn-default pull-right">{{tr('channels')}}</a>
                </div>

                <form class="form-horizontal" action="{{route('admin.save.channel')}}" method="POST" enctype="multipart/form-data" role="form">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">{{tr('user_name')}} *</label>
                            <div class="col-sm-10">
                                
                                <select id="user_id" name="user_id" class="form-control" required>
                                    <option value="">{{tr('select_user')}}</option>
                                    @foreach($users as $user)
                                        <option value="{{$user->id}}">{{$user->name}}</option>
                                    @endforeach
                                </select>
                               
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">{{tr('name')}} *</label>
                            <div class="col-sm-10">
                                <input type="text" required class="form-control" id="name" name="name" placeholder="{{tr('channel_name')}}" minlength="6" title="Min length must be an 6 character">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="col-sm-2 control-label">{{tr('description')}} *</label>
                            <div class="col-sm-10">
                                <textarea required class="form-control" id="description" name="description" placeholder="{{tr('description')}}" required></textarea> 
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="picture" class="col-sm-2 control-label">{{tr('picture')}} *</label>
                            <div class="col-sm-10">
                                <input type="file" required accept="image/png, image/jpeg" id="picture" name="picture" placeholder="{{tr('picture')}}">
                                <p class="help-block">{{tr('image_validate')}} {{tr('image_square')}}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="cover" class="col-sm-2 control-label">{{tr('cover')}} *</label>
                            <div class="col-sm-10">
                                <input type="file" required accept="image/png, image/jpeg" id="cover" name="cover" placeholder="{{tr('cover')}}">
                                <p class="help-block">{{tr('image_validate')}} {{tr('image_square')}}</p>
                            </div>
                        </div>

                    </div>

                    <div class="box-footer">
                        <button type="reset" class="btn btn-danger">{{tr('cancel')}}</button>
                        <button type="submit" class="btn btn-success pull-right">{{tr('submit')}}</button>
                    </div>
                </form>
            
            </div>

        </div>

    </div>

@endsection