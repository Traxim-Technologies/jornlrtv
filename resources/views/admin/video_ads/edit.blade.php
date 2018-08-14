@extends('layouts.admin')

@section('title', tr('edit_ad'))

@section('content-header', tr('edit_ad'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('admin.ads-details.index')}}"><i class="fa fa-bullhorn"></i>{{tr('video_ads')}}</a></li>
    <li class="active"><i class="fa fa-bullhorn"></i> {{tr('edit_ad')}}</li>
@endsection

@section('content')

@include('admin.video_ads._form')
    
@endsection

