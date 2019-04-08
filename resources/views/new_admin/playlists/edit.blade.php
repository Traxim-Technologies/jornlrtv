@extends('layouts.admin')

@section('title', tr('edit_playlist'))

@section('content-header', tr('edit_playlist'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('admin.playlists')}}"><i class="fa fa-user"></i> {{tr('playlists')}}</a></li>
    <li class="active">{{tr('edit_playlist')}}</li>
@endsection

@section('styles')

<link rel="stylesheet" href="{{asset('admin-css/plugins/datepicker/datepicker3.css')}}">

@endsection

@section('content')

@include('notification.notify')

@include('new_admin.playlists._form')

@endsection

@section('scripts')

<script src="{{asset('admin-css/plugins/datepicker/bootstrap-datepicker.js')}}"></script> 
<script src="{{asset('assets/js/jstz.min.js')}}"></script>

<script>

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