@extends('layouts.admin')

@section('title', tr('view_ads'))

@section('content-header', tr('view_ads'))

@section('styles')

<style>
hr {
    margin-bottom: 10px;
    margin-top: 10px;
}
</style>

@endsection

@section('breadcrumb')

    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>

    <li><a href="{{route('admin.ads-details.index')}}"><i class="fa fa-bullhorn"></i> {{tr('view_ads')}}</a></li>

    <li class="active">{{tr('view_ads')}}</li>

@endsection 

@section('content')

<div class="col-md-12">

    <div class="nav-tabs-custom">

        <ul class="nav nav-tabs">
            <a style="margin: 4px !important" href="{{route('admin.ads-details.edit' , ['ads_detail_id' => $ads_detail_details->id])}}" class="btn btn-warning pull-right" title="{{tr('edit')}}" ><b><i class="fa fa-edit"></i></b></a>
            <li class="active"><a href="#preview_ad" data-toggle="tab" aria-expanded="true">{{tr('preview_ad')}}</a></li>
            
        </ul>

        <div class="tab-content">
            
            <div class="tab-pane active" id="preview_ad">

                <div class="col-lg-6">

                    <h4>{{$ads_detail_details->name}} {{tr('details')}} (<a href="{{$ads_detail_details->ad_url}}" target="_blank">{{tr('click_here_url')}}</a>)</h4>

                    <ul class="timeline timeline-inverse">

                      <li>

                        <i class="fa fa-bullhorn bg-blue"></i>

                        <div class="timeline-item">

                            <span class="time"><i class="fa fa-clock-o"></i> {{$ads_detail_details->ad_time}} ({{tr('in_sec')}})</span>

                            <h3 class="timeline-header">
                                
                              {{tr('details')}}</h3>

                            <div class="timeline-body">                                
                                <img src="{{$ads_detail_details->file}}" style="width: 100%;">
                            </div>

                        </div>

                      </li>                
                    
                    </ul>

                </div>

                <div class="clearfix"></div>

            </div>

          <!-- /.tab-pane -->
        </div>

    </div>

</div>

<div class="clearfix"></div>

@endsection


