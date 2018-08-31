<!DOCTYPE html>
<html>

<head>
    <title>{{$title}}</title>
</head>
<style type="text/css">

    table{
        font-family: arial, sans-serif;
        border-collapse: collapse;
    }

    .first_row_design{
        background-color: #8B0000;
        color: #ffffff;
    }

    .row_col_design{
        background-color: #cccccc;
    }

    th{
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
        font-weight: bold;

    }

    td {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;

    }
    
</style>

<body>

    <table>

        <!-- HEADER START  -->

        <tr class="first_row_design">

            <th>{{tr('s_no')}}</th>

            <th >{{tr('subscription')}}</th>

            <th>{{tr('username')}}</th>

            <th >{{tr('payment_id')}}</th>

            <th >{{tr('amount')}}</th>

            <th >{{tr('plan')}}</th>

            <th >{{tr('reason')}}</th>

            <th >{{tr('expiry_date')}}</th>

            <th >{{tr('status')}}</th>

            <th >{{tr('created')}}</th>

            <th >{{tr('updated')}}</th>
        </tr>

        <!--- HEADER END  -->

        @foreach($data as $i => $subscription_details)

            <tr @if($i % 2 == 0) class="row_col_design" @endif >

                <td>{{$i+1}}</td>

                <td>
            
                    {{$subscription_details->getSubscription ? $subscription_details->getSubscription->title : ""}}
                   
                </td>

                <td> @if($subscription_details->user) {{$subscription_details->user->name}} @endif</td>

                <td>{{$subscription_details->payment_id}}</td>

                <td>{{Setting::get('currency')}} {{$subscription_details->amount}}</td>

                <td>{{$subscription_details->getSubscription ? $subscription_details->getSubscription->plan : ""}} </td>

                <td>{{$subscription_details->reason}}</td>

                <td>{{convertTimeToUSERzone($subscription_details->expiry_date, Auth::guard('admin')->user()->timezone, 'd-m-Y H:i a')}}</td>

                <td> 
                    @if($subscription_details->status)

                        {{tr('paid')}}

                    @else  

                        {{tr('not_paid')}}

                    @endif
                </td>

                <td>{{convertTimeToUSERzone($subscription_details->created_at, Auth::guard('admin')->user()->timezone, 'd-m-Y H:i a')}}</td>

                <td>{{convertTimeToUSERzone($subscription_details->updated_at, Auth::guard('admin')->user()->timezone, 'd-m-Y H:i a')}}</td>


            </tr>
        @endforeach
    </table>

</body>

</html>