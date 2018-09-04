@extends( 'layouts.user' )


@section( 'styles' )

<link rel="stylesheet" type="text/css" href="{{asset('streamtube/css/custom-style.css')}}"> 

@endsection

@section('content')

<div class="y-content">

	<div class="row content-row">

		@include('layouts.user.nav')

		<div class="page-inner col-sm-9 col-md-10">

			@include('notification.notify')

			<div class="sub-history">
				<h3 class="no-margin text-left">{{tr('ppv_history_user')}}</h3>
			
				<div class="row">
					@if(count($response->data) > 0)

						@foreach($response->data as $temp)

							<div class="col-xs-12 col-sm-12 col-md-4 top">
								<div class="ppv-card">
									<div class="height-120">
										<div class="ppv-video" style="background-image: url({{$temp->picture}});"></div>
										<div class="ppv-title">{{$temp->title}}</div>
										<span class="ppv-view"><i class="fa fa-money"></i> {{$temp->currency}}{{$temp->amount}}</span>
									</div>
									<div class="ppv-details">
										<!-- <p>Paid Status: &nbsp;
											@if($temp->amount > 0) 
											<span class="green-clr">{{tr('paid')}}</span>
											@else 
											<span class="grey-clr">{{tr('pending')}}</span> 
											@endif
										</p>

										<p>{{tr('payment_id')}}: &nbsp;<span class="grey-clr">{{$temp->payment_id}}</span></p>
										<p class="no-margin">{{tr('paid_at')}}: &nbsp;<span class="grey-clr">{{$temp->paid_date}}</span></p>-->
										<p>
											<span class="ppv-small-head">paid status</span>
											@if($temp->amount > 0) 
											<span class="label label-info pull-right">{{tr('paid')}}</span>
											@else 
											<span class="label label-warning pull-right">{{tr('pending')}}</span> 
											@endif
										</p>
										<p class="ppv-small-head">coupon code</p>
										<h4 class="ppv-text overflow">NEWUSER12</h4>
										<p class="ppv-small-head">coupon amount</p>
										<h4 class="ppv-text overflow">$10.00</h4>
										<p class="ppv-small-head">video amount</p>
										<h4 class="ppv-text overflow">$100.00</h4>
										<p class="ppv-small-head">payment ID</p>
										<h4 class="ppv-text overflow">{{$temp->payment_id}}</h4>
										<p class="ppv-small-head">paid at</p>
										<h4 class="ppv-text">{{$temp->paid_date}}</h4>
										<p class="ppv-small-head">coupon reason</p>
										<h4 class="ppv-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod</h4>
									</div>
								</div>
							</div>

						@endforeach


                        <div class="row">
                            <div class="col-md-12">
                                <div align="center" id="paglink"><?php echo $response->pagination; ?></div>
                            </div>
                        </div>

					@else

						<img src="{{asset('images/no-result.jpg')}}" class="img-responsive auto-margin">

					@endif
				</div>

			</div>
		</div>

	</div>
</div>

@endsection