@extends('layouts.admin')

@section('title', tr('view_category'))

@section('content-header', tr('view_category'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('admin.categories.list')}}"><i class="fa fa-key"></i> {{tr('categories')}}</a></li>
    <li class="active"><i class="fa fa-eye"></i>&nbsp;{{tr('view_category')}}</li>
@endsection

@section('styles')

<style type="text/css">
    .user-block .username, .user-block .description, .user-block .comment {
        margin-left: 0px;
    }
</style>

@endsection

@section('content')

	@include('notification.notify')

	<div class="row">

        <div class="col-xs-12">

            <div class="panel">
                
                <div class="panel-body">

                    <div class="post">
                        
                        <div class="user-block">

                           <!--  <img class="img-circle img-bordered-sm" src="{{$data->user ?  $data->user->picture : asset('placeholder.png') }}" alt="User Image"> -->

                            <span class="username">
                                <a href="">{{$data->name}}</a>
                            </span>

                            <span class="description">{{$data->created_at->diffForHumans()}}</span>

                        </div>

                        <div class="row margin-bottom">

                            <div class="col-sm-4">

                            <img src="{{$data->picture}}" class="img-responsive">
                                
                            </div>

                            <div class="col-sm-8">

                                <div class="row">

                                    <div class="col-sm-6">

                                        <div class="header">

                                            <h4><b>{{tr('title')}}</b></h4>

                                            <label>{{$data->title}}</label>

                                        </div>

                                    </div>

                                   
                                    <div class="col-sm-6">

                                        <div class="header">

                                            <h4><b>{{tr('status')}}</b></h4>

                                            @if($data->status)

                                                <label class="text-green"><b>{{tr('approved')}}</b></label>

                                            @else

                                                <label class="text-navyblue"><b>{{tr('pending')}}</b></label>
                                            @endif

                                        </div>

                                    </div>


                                    <div class="col-sm-12">

                                        <h3><b>{{tr('description')}}</b></h3>

                                        <p><?= $data->description ?></p>

                                    </div>
                            	
                            	</div>
                        
                       		</div>

                    	</div>
                
                	</div>

            	</div>

        	</div>

    	</div>

    </div>

@endsection


