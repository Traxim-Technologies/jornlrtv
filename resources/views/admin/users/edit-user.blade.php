@extends('layouts.admin')

@section('title', tr('edit_user'))

@section('content-header', tr('edit_user'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('admin.users')}}"><i class="fa fa-user"></i> {{tr('users')}}</a></li>
    <li class="active">{{tr('edit_user')}}</li>
@endsection

@section('styles')

<link rel="stylesheet" href="{{asset('admin-css/plugins/datepicker/datepicker3.css')}}">

@endsection

@section('content')

@include('notification.notify')

    <div class="row">

        <div class="col-md-10">

            <div class="box box-primary">

                <div class="box-header label-primary">
                    <b style="font-size:18px;">{{tr('edit_user')}}</b>
                    <a href="{{route('admin.add.user')}}" class="btn btn-default pull-right">{{tr('add_user')}}</a>
                </div>

                <form class="form-horizontal" action="{{route('admin.save.user')}}" method="POST" enctype="multipart/form-data" role="form">

                    <div class="box-body">
                        <input type="hidden" name="id" value="{{$user->id}}">

                        <div class="form-group">
                            <label for="email" class="col-sm-2 control-label">{{tr('email')}}</label>
                            <div class="col-sm-10">
                                <input type="email" required class="form-control" value="{{$user->email}}" id="email" name="email" placeholder="{{tr('email')}}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="username" class="col-sm-2 control-label">{{tr('username')}}</label>

                            <div class="col-sm-10">
                                <input type="text" required name="name" value="{{$user->name}}" class="form-control" id="username" placeholder="{{tr('name')}}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="username" class="col-sm-2 control-label">{{tr('dob')}}</label>

                            <div class="col-sm-10">
                               <input type="text" name="dob" class="form-control" placeholder="{{tr('enter_dob')}}" id="dob" required autocomplete="off" value="{{$user->dob}}">
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="mobile" class="col-sm-2 control-label">{{tr('mobile')}}</label>

                            <div class="col-sm-10">
                                <input type="text" required name="mobile" value="{{$user->mobile}}" class="form-control" id="mobile" placeholder="{{tr('mobile')}}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mobile" class="col-sm-2 control-label">{{tr('description')}}</label>

                            <div class="col-sm-10">
                                <textarea type="text" name="description" class="form-control" id="description" placeholder="Description">{{$user->description}}</textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mobile" class="col-sm-2 control-label">{{tr('picture')}}</label>

                            <div class="col-sm-3">
                                <input type="file" name="picture" id="picture" onchange="loadFile(this, 'picture_preview')" style="width: 200px;" accept="image/jpeg, image/png"/>
                                <br>
                                <img id="picture_preview" style="width: 150px;height: 150px;" src="{{$user->picture}}" />
                            </div>
                        
                            
                        </div>

                    </div>

                    <div class="box-footer">
                        <button type="reset" class="btn btn-danger">{{tr('cancel')}}</button>
                        <button type="submit" class="btn btn-success pull-right">{{tr('submit')}}</button>
                    </div>
                    <input type="hidden" name="timezone" value="" id="userTimezone">
                </form>
            
            </div>

        </div>

    </div>

@endsection

@section('scripts')
<script src="{{asset('admin-css/plugins/datepicker/bootstrap-datepicker.js')}}"></script> 


<script src="{{asset('assets/js/jstz.min.js')}}"></script>
<script>
    
    $(document).ready(function() {

        var dMin = new Date().getTimezoneOffset();
        var dtz = -(dMin/60);
        // alert(dtz);
        $("#userTimezone").val(jstz.determine().name());
    });

    $('#dob').datepicker({
        autoclose:true,
        format : 'dd-mm-yyyy',
        endDate: "dateToday"
    });

function loadFile(event, id){
    // alert(event.files[0]);
    var reader = new FileReader();
    reader.onload = function(){
      var output = document.getElementById(id);
      // alert(output);
      output.src = reader.result;
      //$("#c4-header-bg-container .hd-banner-image").css("background-image", "url("+this.result+")");
    };
    reader.readAsDataURL(event.files[0]);
}
</script>

@endsection