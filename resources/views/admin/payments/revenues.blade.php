@extends('layouts.admin')

@section('title', tr('revenue_system'))

@section('content-header',tr('revenue_system'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-credit-card"></i> {{tr('revenue_system')}}</li>
@endsection

@section('content')

@include('notification.notify')


<div class="row">

	<div class="col-md-6">
	    <div class="box">

	        <div class="box-header with-border">
	            
	            <h3 class="box-title">{{tr('subscription_payments')}}</h3>

	            <div class="box-tools pull-right">
	                
	                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
	                </button>
	                
	                <!-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
	            </div>
	        </div>

	        <!-- /.box-header -->

	        <div class="box-body">
	            <div class="row">

	                <div class="col-md-12">
	                    <p class="text-center">
	                        <strong></strong>
	                    </p>
	                    
	                    <div class="chart-responsive">
	                        <canvas id="user_subscription" height="200px"></canvas>
	                    </div>
	                </div>
	            </div>
	        
	        </div>

	        <div class="box-footer no-padding">
	            <ul class="nav nav-pills nav-stacked">
	                <li>
	                    <a href="#">
	                        <strong class="text-red">{{tr('total_amount')}}</strong>
	                        <span class="pull-right text-red">
	                            <i class="fa fa-angle-right"></i> ${{$subscription_total}}
	                        </span>
	                    </a>
	                </li>

	          </ul>
	        </div>
	    </div>                          
	                
	    
	</div>

	<div class="col-md-6">
	    <div class="box">

	        <div class="box-header with-border">
	            
	            <h3 class="box-title">{{tr('live_payments')}}</h3>

	            <div class="box-tools pull-right">
	                
	                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
	                </button>
	                
	                <!-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
	            </div>
	        </div>

	        <!-- /.box-header -->

	        <div class="box-body">
	            <div class="row">

	                <div class="col-md-12">
	                    <p class="text-center">
	                        <strong></strong>
	                    </p>
	                    
	                    <div class="chart-responsive">
	                        <canvas id="live_payments" height="200px"></canvas>
	                    </div>
	                </div>
	            </div>
	        
	        </div>

	        <div class="box-footer no-padding">
	            <ul class="nav nav-pills nav-stacked">
	                <li>
	                    <a href="#">
	                        <strong class="text-red">{{tr('total_amount')}}</strong>
	                        <span class="pull-right text-red">
	                            <i class="fa fa-angle-right"></i> ${{$total_live_amount}}
	                        </span>
	                    </a>
	                </li>

	                <li>
	                    <a href="#">
	                        <strong class="text-green">{{tr('total_admin_amount')}} </strong>
	                        <span class="pull-right text-green">
	                            <i class="fa fa-angle-right"></i> ${{$admin_live_amount}}
	                        </span>
	                    </a>
	                </li>

	                <li>
	                    <a href="#">
	                        <strong class="text-yellow">{{tr('total_user_amount')}}</strong>
	                        <span class="pull-right text-yellow">
	                            <i class="fa fa-angle-right"></i> ${{$user_live_amount}}
	                        </span>
	                    </a>
	                </li>
	          </ul>
	        </div>
	    </div>                          
	                
	    
	</div>

	<div class="clearfix"></div>

	<div class="col-md-6">
	    <div class="box">

	        <div class="box-header with-border">
	            
	            <h3 class="box-title">{{tr('ppv_payments')}}</h3>

	            <div class="box-tools pull-right">
	                
	                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
	                </button>
	                
	                <!-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
	            </div>
	        </div>

	        <!-- /.box-header -->

	        <div class="box-body">
	            <div class="row">

	                <div class="col-md-12">
	                    <p class="text-center">
	                        <strong></strong>
	                    </p>
	                    
	                    <div class="chart-responsive">
	                        <canvas id="registerChart" height="200px"></canvas>
	                    </div>
	                </div>
	            </div>
	        
	        </div>

	        <div class="box-footer no-padding">
	            <ul class="nav nav-pills nav-stacked">
	                <li>
	                    <a href="#">
	                        <strong class="text-red">{{tr('total_amount')}}</strong>
	                        <span class="pull-right text-red">
	                            <i class="fa fa-angle-right"></i> ${{$total_ppv_amount}}
	                        </span>
	                    </a>
	                </li>

	                <li>
	                    <a href="#">
	                        <strong class="text-green">{{tr('total_admin_amount')}} </strong>
	                        <span class="pull-right text-green">
	                            <i class="fa fa-angle-right"></i> ${{$admin_ppv_amount}}
	                        </span>
	                    </a>
	                </li>

	                <li>
	                    <a href="#">
	                        <strong class="text-yellow">{{tr('total_user_amount')}}</strong>
	                        <span class="pull-right text-yellow">
	                            <i class="fa fa-angle-right"></i> ${{$user_ppv_amount}}
	                        </span>
	                    </a>
	                </li>
	          </ul>
	        </div>
	    </div>                          
	                
	    
	</div>

</div>
@endsection


@section('scripts')



<script type="text/javascript">

//-------------
  //- PIE CHART -
  //-------------
  // Get context with jQuery - using jQuery's .get() method.
  var pieChartCanvas = $("#user_subscription").get(0).getContext("2d");
  var pieChart = new Chart(pieChartCanvas);
  var PieData = [
    {
      value: {{$subscription_total}},
      color: "#00a65a",
      highlight: "#00a65a",
      label: "Admin Amount"
    },

  ];
  var pieOptions = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke: true,
    //String - The colour of each segment stroke
    segmentStrokeColor: "#fff",
    //Number - The width of each segment stroke
    segmentStrokeWidth: 1,
    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 50, // This is 0 for Pie charts
    //Number - Amount of animation steps
    animationSteps: 100,
    //String - Animation easing effect
    animationEasing: "easeOutBounce",
    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate: true,
    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale: false,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
    //String - A tooltip template
    tooltipTemplate: "<%=label%> - $<%=value %>"
  };
  //Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart.Doughnut(PieData, pieOptions);

//-------------
  //- PIE CHART -
  //-------------
  // Get context with jQuery - using jQuery's .get() method.
  var pieChartCanvas = $("#registerChart").get(0).getContext("2d");
  var pieChart = new Chart(pieChartCanvas);
  var PieData = [
    {
      value: {{$admin_ppv_amount}},
      color: "#00a65a",
      highlight: "#00a65a",
      label: "Admin Commission"
    },
    {
      value: {{$user_ppv_amount}},
      color: "#f39c12",
      highlight: "#f39c12",
      label: "User Commission"
    }
  ];
  var pieOptions = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke: true,
    //String - The colour of each segment stroke
    segmentStrokeColor: "#fff",
    //Number - The width of each segment stroke
    segmentStrokeWidth: 1,
    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 50, // This is 0 for Pie charts
    //Number - Amount of animation steps
    animationSteps: 100,
    //String - Animation easing effect
    animationEasing: "easeOutBounce",
    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate: true,
    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale: false,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
    //String - A tooltip template
    tooltipTemplate: "<%=label%> - $<%=value %>"
  };
  //Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart.Doughnut(PieData, pieOptions);
  //-----------------
  //- END PIE CHART -
  //-----------------

//-------------
  //- PIE CHART -
  //-------------
  // Get context with jQuery - using jQuery's .get() method.
  var subscribe_canvas = $("#live_payments").get(0).getContext("2d");
  var subscribeChart = new Chart(subscribe_canvas);
  var subscribeData = [
    {
      value: {{$admin_live_amount}},
      color: "#00a65a",
      highlight: "#00a65a",
      label: "Admin Commission"
    },
    {
      value: {{$user_live_amount}},
      color: "#f39c12",
      highlight: "#f39c12",
      label: "User Commission"
    }
  ];
  var subscribeOptions = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke: true,
    //String - The colour of each segment stroke
    segmentStrokeColor: "#fff",
    //Number - The width of each segment stroke
    segmentStrokeWidth: 1,
    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 50, // This is 0 for Pie charts
    //Number - Amount of animation steps
    animationSteps: 100,
    //String - Animation easing effect
    animationEasing: "easeOutBounce",
    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate: true,
    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale: false,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
    //String - A tooltip template
    tooltipTemplate: "<%=label%> - $<%=value %>"
  };
  //Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  subscribeChart.Doughnut(subscribeData, subscribeOptions);
  //-----------------
  //- END PIE CHART -
  //-----------------

 
</script>

@endsection