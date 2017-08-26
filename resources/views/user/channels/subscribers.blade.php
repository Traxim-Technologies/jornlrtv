@extends('layouts.user')

@section('styles')

<style>

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

                        <div><h4>{{tr('subscribers')}}</h4></div>

                    </div>

                    @if(count($subscribers) > 0)

                        <div class="row">

                            <div class="col-md-12">

                                <table class="table">

                                    <thead>
                                        <tr>
                                            <th>{{tr('s_no')}}</th>
                                            <th>{{tr('channel_name')}}</th>
                                            <th>{{tr('user_name')}}</th>
                                            <th>{{tr('created_at')}}</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @foreach($subscribers as $i => $subscriber)

                                            <tr>

                                                <td>{{++$i}}</td>
                                                <td>{{$subscriber->channel_name}}</td>
                                                <td>{{$subscriber->user_name}}</td>
                                                <td>{{$subscriber->created_at->diffForHumans()}}</td>
                                            </tr>

                                        @endforeach
                                    
                                    </tbody>
                                
                                </table>

                                 @if($subscribers)
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div align="center" id="paglink"><?php echo $subscribers->links(); ?></div>
                                        </div>
                                    </div>
                                @endif

                            </div>

                        </div>

                    @endif
            
                </div>
            
            </div>
    
        </div>
    </div>

@endsection