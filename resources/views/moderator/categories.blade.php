@extends('layouts.moderator')

@section('title', tr('categories'))

@section('content-header', tr('categories'))

@section('breadcrumb')
    <li><a href="{{route('moderator.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-suitcase"></i> {{tr('categories')}}</li>
@endsection

@section('content')

	<div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-body">

            	@if(count($categories) > 0)

	              	<table id="example1" class="table table-bordered table-striped">

						<thead>
						    <tr>
						      <th>{{tr('id')}}</th>
						      <th>{{tr('category')}}</th>
						      <th>{{tr('picture')}}</th>
						      <th>{{tr('is_series')}}</th>
						      <th>{{tr('status')}}</th>
						      <th>{{tr('action')}}</th>
						    </tr>
						</thead>

						<tbody>
							@foreach($categories as $i => $category)
					
							    <tr>
							      	<td>{{$i+1}}</td>
							      	<td>{{$category->name}}</td>
							      	<td>
	                                	<img style="height: 30px;" src="{{$category->picture}}">
	                            	</td>

	                            	<td>
							      		@if($category->is_series) 
							      			<span class="label label-success">{{tr('yes')}}</span>
							       		@else 
							       			<span class="label label-warning">{{tr('no')}}</span>
							       		@endif
							       	</td>

							      <td>
							      		@if($category->is_approved) 
							      			<span class="label label-success">{{tr('approved')}}</span>
							       		@else 
							       			<span class="label label-warning">{{tr('pending')}}</span>
							       		@endif
							       </td>
							      <td>
            							<ul class="admin-action btn btn-default">
            								<li class="dropdown">
								                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
								                  {{tr('action')}} <span class="caret"></span>
								                </a>
								                <ul class="dropdown-menu">
								                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('moderator.edit.category' , array('id' => $category->id))}}">{{tr('edit_category')}}</a></li>
								                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('moderator.delete.category' , $category->id)}}">{{tr('delete_category')}}</a></li>

													<li class="divider" role="presentation"></li>

								                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('moderator.add.sub_category' , array('category' => $category->id))}}">{{tr('add_sub_category')}}</a></li>
								                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('moderator.sub_categories' , array('category' => $category->id))}}">{{tr('view_sub_categories')}}</a></li>
								                </ul>
              								</li>
            							</ul>
							      </td>
							    </tr>
							@endforeach
						</tbody>
					</table>
				@else
					<h3 class="no-result">No results found</h3>
				@endif
            </div>
          </div>
        </div>
    </div>

@endsection


