@extends('layouts.user')

@section('styles')

<!-- Add css file and inline css here -->

@endsection

@section('content')

    <div class="y-content">
        <div class="row content-row">

            @include('layouts.user.nav')

            <div class="page-inner col-sm-9 col-md-10">

                @include('notification.notify')

            </div>

        </div>

    </div>

@endsection

@section('scripts')

<!-- Add Js files and inline js here -->

@endsection