@extends('layouts.user')

@section('content')

    <div class="y-content">
        <div class="row content-row">

            @include('layouts.user.nav')

            <div class="page-inner col-sm-9 col-md-10">

                @include('notification.notify')


                <a href="{{route('channels.create')}}" class="btn btn-success" target="_blank">Channel Create</a>

                <a href="{{route('channels.view')}}" class="btn btn-success" target="_blank">Channel Single View</a>

                <br>

                <br>

                <a href="{{route('videos.create')}}" class="btn btn-danger" target="_blank">Video Create</a>

                <br>

                <br>

                <a href="{{route('subscriptions.index')}}" class="btn btn-primary" target="_blank">Subscriptions</a>

                <a href="{{route('subscriptions.view')}}" class="btn btn-primary" target="_blank">Subscriptions View</a>
               
            </div>

        </div>
    </div>

@endsection