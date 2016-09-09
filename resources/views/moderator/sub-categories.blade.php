@extends('layouts.moderator')

@section('title', tr('sub_categories'))

@section('content-header')

	<span style="color:#1d880c !important">{{$category->name}} </span> - {{tr('sub_categories') }}

@endsection

@section('breadcrumb')
    <li><a href="{{route('moderator.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('moderator.categories')}}"><i class="fa fa-suitcase"></i> {{tr('categories')}}</a></li>
    <li class="active"><i class="fa fa-suitcase"></i> {{tr('sub_categories')}}</li>
@endsection

@section('content')

	<div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-body">

            	@if(count($data) > 0)

	              	<table id="example1" class="table table-bordered table-striped">

						<thead>
						    <tr>
						      <th>{{tr('id')}}</th>
						      <th>{{tr('sub_category')}}</th>
						      <th>{{tr('description')}}</th>
						      <th>{{tr('status')}}</th>
						      <th>{{tr('genres')}}</th>
						      <th>{{tr('image')}}</th>
						      <th>{{tr('action')}}</th>
						    </tr>
						</thead>

						<tbody>

							@foreach($data as $i => $sub_category)

								<?php $images = get_sub_category_image($sub_category->id); ?>
					
							    <tr>
							      	<td>{{$i+1}}</td>
							      	<td>{{$sub_category->sub_category_name}}</td>
							      	<td>{{$sub_category->description}}</td>
							      	
							      	<td>
							      		@if($sub_category->is_approved) 
							      			<span class="label label-success">{{tr('approved')}}</span>
							       		@else 
							       			<span class="label label-warning">{{tr('pending')}}</span>
							       		@endif
							       </td>

							      	<td>
							      		<button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#genres{{$i}}">
											{{tr('view_genres')}}
										</button>
							      	</td>
							      	
							      	<td>
							      		<button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#image{{$i}}">
											{{tr('view_images')}}
										</button>
							      	</td>

								    <td>
            							<ul class="admin-action btn btn-default">
            								<li class="dropdown">
								                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
								                  {{tr('action')}} <span class="caret"></span>
								                </a>
								                <ul class="dropdown-menu">
								                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('moderator.edit.sub_category' , array('category_id' => $category->id,'sub_category_id' => $sub_category->id))}}">{{tr('edit_sub_category')}}</a></li>
								                </ul>
              								</li>
            							</ul>
								    </td>
							    </tr>

							    <!-- Modalfor sub category images -->

								<div class="modal fade" id="image{{$i}}" role="dialog">

								    <div class="modal-dialog">
								    	<!-- Modal content-->
								    	<div class="modal-content">

								        	<div class="modal-header">
								          		<button type="button" class="close" data-dismiss="modal">&times;</button>
								          		<h4 class="modal-title">{{$sub_category->sub_category_name}}</h4>
								        	</div>

								        	<div class="modal-body">

								        		@if(count($images) > 0)

									                <div class="row">

									                	@foreach($images as $image)
										                    <div class="col-sm-4">
										                        <img class="img-responsive" src="{{$image->picture}}" alt="SubCategory">
										                    </div>
									                    @endforeach
									                
									                </div>

								                @endif

								        	</div>

								        	<div class="modal-footer">
								          		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								        	</div>
								    	</div>
								      
									</div>
								
								</div>

								<!-- Modalfor sub category images -->

								<div class="modal fade" id="genres{{$i}}" role="dialog">

								    <div class="modal-dialog">
								    	<!-- Modal content-->
								    	<div class="modal-content">

								        	<div class="modal-header">
								          		<button type="button" class="close" data-dismiss="modal">&times;</button>
								          		<h4 class="modal-title">{{$sub_category->sub_category_name}}</h4>
								        	</div>

								        	<div class="modal-body">

								        		@if(count($sub_category->genres) > 0)

									                <div class="row">

									                	@foreach($sub_category->genres as $genre)
									                		<div class="col-lg-12">
										                		<div class="box">
										                			<div class="box-header ui-sortable-handle" style="cursor: move;">

														             	<h3 class="box-title">{{$genre->name}}</h3>
														              	<!-- tools box -->
														              	<div class="pull-right box-tools">
														              		<a title="Delete" href="{{route('moderator.delete.genre' , $genre->id)}}" class="btn btn-danger btn-sm">
													                  			<i class="fa fa-trash"></i>
													                  		</a>
													                  	</div>
															        </div>
										                		</div>
										                  	</div>
									                    @endforeach
									                
									                </div>

								                @endif

								        	</div>

								        	<div class="modal-footer">
								          		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								        	</div>
								    	</div>
								      
									</div>
								
								</div>

							    <script type="text/javascript">
								    $(function () {
								    	$('#image{{$i}}').on('shown.bs.modal', function () {
											  $('#myInput').focus()
										});
									});
							    </script>

							@endforeach
						</tbody>
					</table>
				@else
					<h3 class="no-result">{{tr('no_sub_category_found')}}</h3>
				@endif
            </div>
          </div>
        </div>
    </div>

@endsection


