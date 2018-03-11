@extends('layouts.admin')

@section('title', tr('add_subscription'))

@section('content-header', tr('add_subscription'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('admin.subscriptions.index')}}"><i class="fa fa-key"></i> {{tr('subscriptions')}}</a></li>
    <li class="active">{{tr('add_subscription')}}</li>
@endsection

@section('content')

@include('notification.notify')

    <div class="row">

        <div class="col-md-10 ">

            <div class="box box-primary">

                <div class="box-header label-primary">

                    <b>{{tr('add_subscription')}}</b>

                    <a href="{{route('admin.subscriptions.index')}}" style="float:right" class="btn btn-default">{{tr('view_subscriptions')}}</a>
                </div>

                <form class="form-horizontal" action="{{route('admin.subscriptions.save')}}" method="POST" enctype="multipart/form-data" role="form">


                    <div class="box-body">

                        <div class="col-md-12">

                        <div class="form-group">
                            <label for="title" class="">{{tr('title')}}</label>

                            <input type="text" required name="title" class="form-control" id="title" value="{{old('title')}}" placeholder="{{tr('title')}}">
                        </div>

                        <div class="form-group">

                            <label for="image" class="">
                                {{tr('image')}} 
                                <br><span class="text-red"><b>{{tr('subscription_image_note')}}</b></span>
                            </label>

                            <input type="file" required name="image" class="form-control" id="image" value="{{old('image')}}" placeholder="{{tr('image')}}" accept="image/png, image/jpeg" onchange="loadFile(this, 'image_preview')">

                            <br>

                            <img id="image_preview" style="width:100px;height:100px;display: none;">
                        </div>

                        <div class="form-group">
                        
                            <label for="plan" class="">{{tr('plan')}} <br><span class="text-red"><b>{{tr('plan_note')}}</b></span></label>
                                <input type="number" min="1" max="12" pattern="[0-9][0-2]{2}"  required name="plan" class="form-control" id="plan" value="{{old('plan')}}" title="{{tr('month_of_plans')}}" placeholder="{{tr('plan')}}">

                        </div>

                        <div class="form-group">
                            <label for="amount" class="">{{tr('amount')}}</label>

                            <!-- <div class="col-sm-10"> -->
                                <input type="number" required name="amount" class="form-control" id="amount" placeholder="{{tr('amount')}}" step="any">
                            <!-- </div> -->
                        </div>

                        <div class="form-group">

                            <label for="description" class="">{{tr('description')}}</label>

                            <!-- <div class="col-sm-10"> -->

                                <textarea id="ckeditor" name="description" required class="form-control" placeholder="{{tr('description')}}.">{{old('description')}}</textarea>

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
        function loadFile(event, id){

           $("#"+id).show();

            // alert(event.files[0]);
            var reader = new FileReader();
            reader.onload = function(){
              var output = document.getElementById(id);
              // alert(output);
              output.src = reader.result;
               //$("#imagePreview").css("background-image", "url("+this.result+")");
            };
            reader.readAsDataURL(event.files[0]);
        }
    </script>
@endsection