@extends('layouts.admin')

@section('title', tr('edit_subscription'))

@section('content-header', tr('edit_subscription'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('admin.subscriptions.index')}}"><i class="fa fa-key"></i> {{tr('subscriptions')}}</a></li>
    <li class="active">{{tr('edit_subscription')}}</li>
@endsection

@section('content')

@include('notification.notify')

    <div class="row">

        <div class="col-md-10 ">

            <div class="box box-primary">

                <div class="box-header label-primary">
                    <b>{{tr('edit_subscription')}}</b>
                    <a href="{{route('admin.subscriptions.index')}}" style="float:right" class="btn btn-default">{{tr('view_subscriptions')}}</a>
                </div>

                <form class="form-horizontal" action="{{route('admin.subscriptions.save')}}" method="POST" enctype="multipart/form-data" role="form">
                    <input type="hidden" name="id" value="{{$data->id}}">

                    <div class="box-body">

                        <div class="col-md-12">

                        <div class="form-group">
                            <label for="title" class="">{{tr('title')}}</label>

                            <!-- <div class="col-sm-10"> -->
                                <input type="text" name="title" class="form-control" id="title" value="{{isset($data) ? $data->title : old('title')}}" placeholder="{{tr('title')}}">
                            <!-- </div> -->
                        </div>

                        <div class="form-group">
                        
                            <label for="image" class="">{{tr('image')}}</label>

                            <!-- <div class="col-sm-10"> -->
                                <input type="file" name="image" class="form-control" id="image" value="{{old('image')}}" placeholder="{{tr('image')}}">
                            <!-- </div> -->
                        </div>

                       
                        <div class="form-group">

                            <label for="plan" class="">{{tr('plan')}} <br><span class="text-red"><b>{{tr('plan_note')}}</b></span></label>

                            <!-- <div class="col-sm-10"> -->
                                <input type="number" min="1" max="12" pattern="[0-9][0-2]{2}"  name="plan" class="form-control" id="plan" value="{{isset($data) ? $data->plan : old('plan')}}" title="Please enter the plan months. Max : 12 months" placeholder="plan">
                            <!-- </div> -->
                        </div>

                        <div class="form-group">

                            <label for="amount" class="">{{tr('amount')}}</label>

                            <!-- <div class="col-sm-10"> -->
                                <input type="text" value="{{isset($data) ? $data->amount : old('amount')}}" name="amount" class="form-control" id="amount" placeholder="amount" pattern="[0-9]{1,}">
                            <!-- </div> -->
                        </div>

                        <div class="form-group">

                            <label for="description" class="">{{tr('description')}}</label>

                            <!-- <div class="col-sm-10"> -->

                                <textarea id="ckeditor" name="description" class="form-control" placeholder="{{tr('description')}}.">{{isset($data) ? $data->description : old('description')}}</textarea>

                            <!-- </div> -->
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

@section('scripts')
    <script src="http://cdn.ckeditor.com/4.5.5/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace( 'ckeditor' );
    </script>
@endsection