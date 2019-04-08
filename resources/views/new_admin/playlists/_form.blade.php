@include('notification.notify')

<div class="row">

    <div class="col-md-10">

        <div class="box box-primary">

            <div class="box-header label-primary">
                <b style="font-size:18px;">@yield('title')</b>
                <a href="{{route('admin.playlists.index')}}" class="btn btn-default pull-right">{{tr('view_playlists')}}</a>
            </div>

            <form class="form-horizontal" action="{{route('admin.playlists.save')}}" method="POST" enctype="multipart/form-data" role="form">

                <div class="box-body">

                    <input type="hidden" name="playlist_detail_id" value="{{$playlist_details->id}}">

                    <div class="row">

                        <div class="col-lg-3 text-center">
                            
                            <input type="file" name="picture" id="picture" onchange="loadFile(this, 'picture_preview')" style="width: 200px;display: none" accept="image/jpeg, image/png" />

                            <img id="picture_preview" style="width: 150px;height: 150px;cursor: pointer;" src="{{$playlist_details->picture ? $playlist_details->picture : asset('placeholder.png')}}" onclick="return $('#picture').click()" />

                        </div>

                        <div class="col-lg-9">
                            
                            <div class="form-group">

                                <div class="col-lg-12">

                                    <input type="text" required name="title" value="{{$playlist_details->title ? $playlist_details->title : old('title')}}" class="form-control" id="" placeholder="{{tr('title')}} *" title="{{tr('title')}}">

                                </div>

                            </div>

                        </div>

                        <div class="col-lg-9">

                            <textarea type="text" name="description" class="form-control" id="description" placeholder="{{tr('description')}}" maxlength="255">{{$playlist_details->description ? $playlist_details->description  :old('description')}}</textarea>

                        </div>

                    </div>

                    <div class="clearfix"></div>

                    <br>


                </div>

                <div class="box-footer">
                    <a href="" class="btn btn-danger">{{tr('reset')}}</a>
                    <button type="submit" class="btn btn-success pull-right">{{tr('submit')}}</button>
                </div>
                <input type="hidden" name="timezone" value="" id="userTimezone">
            </form>
        
        </div>

    </div>

</div>
