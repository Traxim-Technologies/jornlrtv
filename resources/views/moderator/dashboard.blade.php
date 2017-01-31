@extends('layouts.moderator')

@section('title', tr('dashboard'))

@section('content-header', tr('dashboard'))

@section('breadcrumb')
    <li class="active"><i class="fa fa-dashboard"></i> {{tr('dashboard')}}</a></li>
@endsection

<style type="text/css">
  .center-card{
    	width: 30% !important;
	}
  .small-box .icon {
    top: 0px !important;
  }
</style>

@section('content')

	<div class="row">

		<!-- Total Users -->

		<div class="col-lg-3 col-xs-6" style="display:none">

          	<div class="small-box bg-green">
            	<div class="inner">
              		<h3>{{$category_count}}</h3>
              		<p>{{tr('category_count')}}</p>
            	</div>
            	
            	<div class="icon">
              		<i class="fa fa-user"></i>
            	</div>

            	<a target="_blank" href="{{route('moderator.categories')}}" class="small-box-footer">
              		{{tr('more_info')}}
              		<i class="fa fa-arrow-circle-right"></i>
            	</a>
          	</div>
        </div>

		<!-- Total Moderators -->

        <div class="col-lg-3 col-xs-6" style="display:none">

          	<div class="small-box bg-red">
            	<div class="inner">
              		<h3>{{$sub_category_count}}</h3>
              		<p>{{tr('sub_category_count')}}</p>
            	</div>
            	
            	<div class="icon">
              		<i class="fa fa-users"></i>
            	</div>

            	<a target="_blank" href="{{route('moderator.categories')}}" class="small-box-footer">
              		{{tr('more_info')}}
              		<i class="fa fa-arrow-circle-right"></i>
            	</a>
          	</div>
        
        </div>

         <div class="col-lg-3 col-xs-6">

          	<div class="small-box bg-yellow">
            	<div class="inner">
              		<h3>{{$today_videos}}</h3>
              		<p>{{tr('today_videos')}}</p>
            	</div>
            	
            	<div class="icon">
              		<i class="fa fa-video-camera"></i>
            	</div>

            	<a target="_blank" href="{{route('moderator.videos')}}" class="small-box-footer">
              		{{tr('more_info')}}
              		<i class="fa fa-arrow-circle-right"></i>
            	</a>
          	</div>
        
        </div>

	</div>


@endsection


