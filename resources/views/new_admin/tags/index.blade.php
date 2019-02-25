@extends('layouts.admin')

@section('title', tr('tags'))

@section('content-header', tr('tags'))

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i>{{ tr('home') }}</a></li>
    <li class="active"><i class="fa fa-tag"></i> {{ tr('tags') }}</li>
@endsection

@section('content')

	<div class="row">
	
		<div class="col-xs-12">			
			@include('notification.notify')
		</div>
        

        <div class="col-xs-12 text-right">       	
        	<button class="btn btn-success" type="button" onclick="$('#display_form').toggle()">{{ tr('create_tag') }}</button>
        </div>

        <div class="col-xs-12" style="{{ $tag_details->id ? '' : 'display: none' }}" id="display_form">        	
          	
          	<div class="box box-primary">

          		<div class="box-body">

	          		<form class="form-inline" action="{{ route('admin.tags.save') }}" method="post" enctype="multipart/form-data">

	      				<input type="hidden" name="tag_id" value="{{ $tag_details->id }}">
		        		
		        		<div class="col-xs-8 col-sm-8">    
		        			<label for="name">{{tr('name')}}</label>
		        			<input type="text" name="name" value="{{ $tag_details->name }}" required class="form-control" placeholder="{{ tr('name') }}"  title="Enter only alphabets" maxlength="15">
		        		</div>

		        		<div class="col-xs-4 col-sm-4">
		        			<input type="submit" name="button" value="{{ tr('submit') }}" class="btn btn-success">
		        		</div>

	          		</form>

          		</div>

          	</div>

        </div>

        <div class="clearfix"></div>

        <br>

    </div>

	<div class="row">

        <div class="col-xs-12"> 

          	<div class="box box-primary">

	          	<div class="box-header label-primary">
	                <b>{{ tr('categories') }}</b>
	            </div>
	            
	            <div class="box-body">

	              	<table id="example1" class="table table-bordered table-striped">

						<thead>
						    <tr>
						      	<th>{{ tr('id') }}</th>
						      	<th>{{ tr('name') }}</th>
						      	<th>{{ tr('status') }}</th>
						      	<th>{{ tr('action') }}</th>
						    </tr>
						</thead>

						<tbody>
						
							@foreach($tags as $i => $tag_details)

							    <tr>
							      	<td>{{ $i+1 }}</td>

							      	<td class="text-capitalize">{{ $tag_details->name }}</td>
							      
							      	<td class="text-center">
						      			@if($tag_details->status)
							      			<span class="label label-success">{{ tr('approved') }}</span>
							      		@else
							      			<span class="label label-warning">{{ tr('pending') }}</span>
							      		@endif
							      	</td>

						      		<td class="text-center">
						      		
					      				<a href="{{ route('admin.tags' ,['tag_id' => $tag_details->id]) }}" class="btn  btn-xs btn-primary" title="Edit">
				              				<i class="fa fa-edit"></i>
				              			</a>

						      			<a href="{{ route('admin.tags.delete' ,['tag_id' => $tag_details->id]) }}" class="btn  btn-xs btn-danger" title="Delete" onclick="return confirm(&quot;{{ tr('admin_tag_delete_confirmation', $tag_details->name) }}&quot;)" ><i class="fa fa-trash"></i></a>	
				              			@if($tag_details->status == APPROVED)

				              				<a href="{{ route('admin.tags.status' ,['tag_id' => $tag_details->id]) }}" class="btn  btn-xs btn-warning" title="Decline this Category" onclick="return confirm(&quot;{{ tr('admin_tag_decline_confirmation', $tag_details->name) }}&quot;)" ><i class="fa fa-times"></i></a>
				              			@else

					              			<a href="{{ route('admin.tags.status' ,['tag_id' => $tag_details->id]) }}" class="btn  btn-xs btn-success" title="Approve this Category" onclick="return confirm(&quot;{{ tr('admin_tag_approve_confirmation',$tag_details->name) }}&quot;)"><i class="fa fa-check"></i></a>
				              			@endif

			              				<!-- <a href="{{ route('admin.tags.videos' ,['tag_id' => $tag_details->id]) }}" class="btn  btn-xs btn-success" title="Tagged Videos"><i class="fa fa-video-camera"></i></a> -->

			              				<a href="{{ route('admin.videos.list' ,['tag_id' => $tag_details->id]) }}" class="btn  btn-xs btn-success" title="Tagged Videos"><i class="fa fa-video-camera"></i></a>
						      		
						      		</td>
							      	
							    </tr>

							@endforeach

						</tbody>
					
					</table>
				
	            </div>

          	</div>

        </div>

    </div>

@endsection



@section('scripts')

<!-- Add Js files and inline js here -->

<script type="text/javascript">
function loadFile(event, id){
    // alert(event.files[0]);
    var reader = new FileReader();
    reader.onload = function(){
      var output = document.getElementById(id);
      // alert(output);
      output.src = reader.result;
      //$("#c4-header-bg-container .hd-banner-image").css("background-image", "url("+this.result+")");
    };
    reader.readAsDataURL(event.files[0]);
}
</script>

@endsection