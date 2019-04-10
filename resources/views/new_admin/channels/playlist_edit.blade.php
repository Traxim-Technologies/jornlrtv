@extends('layouts.admin')

@section('title', tr('edit_playlist'))

@section('content-header', tr('edit_playlist'))

@section('breadcrumb')
    <li><a href="{{route('admin.channels.index')}}"><i class="fa fa-suitcase"></i> {{tr('channels')}}</a></li> 

    <li><a href="{{ route('admin.channels.view',['channel_id' => $channel_details->id] ) }}"><i class="fa fa-suitcase"></i> {{ $channel_details->name }}</a></li>

    <li class="active">{{tr('edit_playlist')}}</li>

@endsection

@section('content')

<div class="row">

    <div class="col-md-12">

        @include('notification.notify')

        <div class="box box-primary">

            <div class="box-header label-primary">
                <b style="font-size:18px;">{{ tr('edit_playlist') }}</b>
                <a href="{{ route('admin.channels.index') }}" class="btn btn-default pull-right">{{ tr('channels') }}</a>
            </div>

            @include('new_admin.channels._form_playlist')

        </div>

    </div>

</div>



@endsection

@section('scripts')
    <script src="https://cdn.ckeditor.com/4.5.5/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace( 'description' );
    </script>
@endsection