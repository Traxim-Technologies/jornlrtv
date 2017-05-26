@extends('layouts.user')

@section('styles')

<style>
    
/**
 * Circle Styles
 */

.circle {
  position: relative;
  display: block;
  margin: 1em 0;
  background-color: transparent;
  color: #222;
  text-align: center;
}

.circle:after {
  display: block;
  padding-bottom: 100%;
  width: 100%;
  height: 0;
  border-radius: 50%;
  background-color: #f1f1f1;
  content: "";
  box-shadow: 2px 5px 10px grey;
}

.circle__inner {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.circle__wrapper {
    display: table;
    width: 100%;
    height: 100%;
}

.circle__content {
    display: table-cell;
    padding: 1em;
    vertical-align: middle;
    color: #D9230F;
}

@media (min-width: 480px) {
    .circle__content {
        font-size: 1.3em;
    }
}

@media (min-width: 768px) {
  .circle__content {
    font-size: 1.3em;
  }
}

.redeem-content {
    margin:3em 0 1em 0;line-height: 1.8em;
}

table {
    box-shadow: 0px 1px 5px grey !important;
}
thead>tr>th {
    padding: 1% !important;
}
</style>

@endsection

@section('content')

    <div class="y-content">
    
        <div class="row content-row">

            @include('layouts.user.nav')

            <div class="history-content page-inner col-sm-9 col-md-10">

                @include('notification.notify')

                <div class="new-history">

                    <div class="content-head">

                        <div><h4>{{tr('redeems')}}</h4></div>

                    </div>

                    <div class="row">

                        <div class="col-lg-2 col-md-3 col-sm-5 col-xs-5 ">

                            <div class="circle">
                                <div class="circle__inner">
                                    <div class="circle__wrapper">
                                        <div class="circle__content"><b>{{Setting::get('currency')}} {{Auth::user()->userRedeem ? Auth::user()->userRedeem->remaining : "0.00"}}</b></div>
                                    </div>
                                </div>
                            
                            </div>
                        </div>

                        <div class="col-lg-10 col-md-9 col-sm-7 col-xs-7">

                            <p class="redeem-content">{{tr('redeem_content')}}
                            </p>

                            <?php 

                                $remaining = Auth::user()->userRedeem ? Auth::user()->userRedeem->remaining: 0;

                                $min_status = Setting::get('minimum_redeem') < Auth::user()->userRedeem->remaining;
                            ?>

                            @if(count(Auth::user()->userRedeem) > 0 && $min_status)

                                <a href="{{route('user.redeems.send.request')}}" class="btn btn-success">{{tr('send_redeem')}}</a>

                            @else
                                <a href="javascript:void(0);" disabled class="btn btn-success">{{tr('send_redeem')}}</a>

                            @endif
                            
                        </div>
                    
                    </div>

                    @if(count($redeem_requests = Auth::user()->userRedeemRequests) > 0)

                        <div class="row">

                            <div class="col-md-12">

                                <table class="table">

                                    <thead>
                                        <tr>
                                            <th>{{tr('redeem_amount')}}</th>
                                            <th>{{tr('sent_date')}}</th>
                                            <th>{{tr('paid_amount')}}</th>
                                            <th>{{tr('paid_date')}}</th>
                                            <th>{{tr('status')}}</th>
                                            <th>{{tr('action')}}</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @foreach($redeem_requests as $rr => $redeem_request)

                                            <tr>

                                                <td><b>{{Setting::get('currency')}} {{$redeem_request->request_amount}}</b></td>

                                                <td>{{$redeem_request->created_at->diffForHumans()}}</td>
                                                <td><b>{{Setting::get('currency')}} {{$redeem_request->paid_amount}}</b></td>
                                                <td>{{$redeem_request->created_at->diffForHumans()}}</td>

                                                <td>
                                                    <span class="btn btn-primary btn-xs"> <b>

                                                        {{redeem_request_status($redeem_request->status)}}

                                                        </b>

                                                    </span>
                                                </td>

                                                <td>
                                                    @if(in_array($redeem_request->status, [REDEEM_REQUEST_SENT , REDEEM_REQUEST_PROCESSING]))
                                                        <a href="{{route('user.redeems.request.cancel' , ['redeem_request_id' => $redeem_request->id])}}" class="btn btn-danger btn-sm">{{tr('cancel')}}</a>
                                                    @else
                                                        <span class="text-center">-</span>
                                                    @endif
                                                </td>
                                            </tr>

                                        @endforeach
                                    
                                    </tbody>
                                
                                </table>

                            </div>

                        </div>

                    @endif
            
                </div>
            
            </div>
    
        </div>
    </div>

@endsection