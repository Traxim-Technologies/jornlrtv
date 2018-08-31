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

            <th >{{tr('video_title')}}</th>

            <th>{{tr('username')}}</th>

            <th >{{tr('payment_id')}}</th>

            <th >{{tr('type_of_subscription')}}</th>

            <th >{{tr('type_of_user')}}</th>

            <th >{{tr('admin_amount')}}</th>

            <th >{{tr('user_amount')}}</th>

            <th >{{tr('amount')}}</th>

            <th >{{tr('reason')}}</th>

            <th >{{tr('expiry_date')}}</th>

            <th >{{tr('status')}}</th>

            <th >{{tr('description')}}</th>

            <th >{{tr('created')}}</th>

            <th >{{tr('updated')}}</th>
        </tr>

        <!--- HEADER END  -->

        @foreach($data as $i => $ppv_details)

            <tr @if($i % 2 == 0) class="row_col_design" @endif >

                <td>{{$i+1}}</td>

                <td>{{$ppv_details->videoTapeDetails ? $ppv_details->videoTapeDetails->title : " - "}}</td>

                <td>{{$ppv_details->userDetails ? $ppv_details->userDetails->name : " "}}</td>

                <td>{{$ppv_details->payment_id}}</td>

                <td> 
                    @if($ppv_details->type_of_subscription == ONE_TIME_PAYMENT)

                        {{tr('one_time_payment')}}

                    @else 

                        {{tr('recurring_payment')}}

                    @endif
                </td>

                <td> 
                    @if($ppv_details->type_user == NORMAL_USER)

                        {{tr('normal_user')}}

                    @elseif($ppv_details->type_user == PAID_USER) 

                        {{tr('paid_users')}}

                    @else

                        {{tr('both_users')}}

                    @endif
                </td>

                <td>{{Setting::get('currency')}} {{$ppv_details->admin_ppv_amount}}</td>

                <td>{{Setting::get('currency')}} {{$ppv_details->user_ppv_amount}}</td>

                <td>{{Setting::get('currency')}} {{$ppv_details->amount}}</td>

                <td>{{$ppv_details->reason}}</td>

                <td>{{convertTimeToUSERzone($ppv_details->expiry_date, Auth::guard('admin')->user()->timezone, 'd-m-Y H:i a')}}</td>

                <td> 
                    @if($ppv_details->amount <= 0)

                        {{tr('not_paid')}}

                    @else  

                        {{tr('paid')}}

                    @endif
                </td>

                <td>{{$ppv_details->description}}</td>

                <td>{{convertTimeToUSERzone($ppv_details->created_at, Auth::guard('admin')->user()->timezone, 'd-m-Y H:i a')}}</td>

                <td>{{convertTimeToUSERzone($ppv_details->updated_at, Auth::guard('admin')->user()->timezone, 'd-m-Y H:i a')}}</td>


            </tr>
        @endforeach
    </table>

</body>

</html>