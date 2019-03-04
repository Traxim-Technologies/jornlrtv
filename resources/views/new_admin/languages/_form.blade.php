<form  action="{{ route('admin.languages.save') }}" method="POST" enctype="multipart/form-data" role="form">

	<div class="box-body">

		<input type="hidden" name="language_id" value="{{ $language_details->id }}">

	    <div class="form-group">
	        <label for="name">{{ tr('short_name') }}</label>
	        <input type="text" class="form-control" name="folder_name" id="folder_name" placeholder="{{ tr('example_language') }}" required maxlength="4" value="{{ $language_details->folder_name }}">
	    </div>

	    <div class="form-group">
	        <label for="name">{{ tr('language') }}</label>
	        <div>
	            <input type="text" class="form-control" name="language" id="language" placeholder="{{ tr('example_language2') }}" required maxlength="64" value="{{ $language_details->language }}">
	        </div>
	    </div>

	    <div class="form-group">
	        <label for="name">{{ tr('auth_file') }}</label>
	        <input type="file" id="auth_file" name="auth_file" placeholder="{{ tr('picture') }}">
	        <br>	        
	        <ul class="ace-thumbnails clearfix">
	            <li>
	                <div class="flip-container">
	                    <div class="flipper">
	                           <img style="width:100px;height:90px" alt="Php" src="{{ asset('common/img/php.png') }}">
	                    </div>
	                </div>
	                <div class="tools tools-bottom">
	                    <a href="{{ route('admin.languages.download', array('f_n'=>$value->folder_name, 'file_name'=>'auth')) }}">
	                        <i class="fa fa-download"></i>
	                    </a>
	                </div>
	            </li>
	        </ul>
	    </div>

	    <div class="form-group">
	        <label for="name">{{ tr('messages_file') }}</label>
	        <input type="file" id="messages_file" name="messages_file" placeholder="{{ tr('picture') }}">
	         <br>
	        <ul class="ace-thumbnails clearfix">
	            <li>
	                <div class="flip-container">
	                    <div class="flipper">
	                           <img style="width:100px;height:90px" alt="Php" src="{{ asset('common/img/php.png') }}">
	                    </div>
	                </div>
	                <div class="tools tools-bottom">
	                    <a href="{{ route('admin.languages.download', array('f_n'=>$value->folder_name, 'file_name'=>'messages')) }}">
	                        <i class="fa fa-download"></i>
	                    </a>
	                </div>
	            </li>
	        </ul>
	    </div>

	    <div class="form-group">
	        <label for="name">{{ tr('pagination_file') }}</label>
	        <input type="file" id="pagination_file" name="pagination_file" placeholder="{{ tr('picture') }}">
	         <br>
	        <ul class="ace-thumbnails clearfix">
	            <li>
	                <div class="flip-container">
	                    <div class="flipper">
	                           <img style="width:100px;height:90px" alt="Php" src="{{ asset('common/img/php.png') }}">
	                    </div>
	                </div>
	                <div class="tools tools-bottom">
	                    <a href="{{ route('admin.languages.download', array('f_n'=>$value->folder_name, 'file_name'=>'pagination')) }}">
	                        <i class="fa fa-download"></i>
	                    </a>
	                </div>
	            </li>
	        </ul>
	    </div>

	    <div class="form-group">
	        <label for="name">{{ tr('passwords_file') }}</label>
	        <input type="file" id="passwords_file" name="passwords_file" placeholder="{{ tr('picture') }}">
	         <br>
	        <ul class="ace-thumbnails clearfix">
	            <li>
	                <div class="flip-container">
	                    <div class="flipper">
	                           <img style="width:100px;height:90px" alt="Php" src="{{ asset('common/img/php.png') }}">
	                    </div>
	                </div>
	                <div class="tools tools-bottom">
	                    <a href="{{ route('admin.languages.download', array('f_n'=>$value->folder_name, 'file_name'=>'passwords')) }}">
	                        <i class="fa fa-download"></i>
	                    </a>
	                </div>
	            </li>
	        </ul>
	    </div>

	    <div class="form-group">
	        <label for="name">{{ tr('validation_file') }}</label>
	        <input type="file" id="validation_file" name="validation_file" placeholder="{{ tr('picture') }}">
	         <br>
	        <ul class="ace-thumbnails clearfix">
	            <li>
	                <div class="flip-container">
	                    <div class="flipper">
	                           <img style="width:100px;height:90px" alt="Php" src="{{ asset('common/img/php.png') }}">
	                    </div>
	                </div>
	                <div class="tools tools-bottom">
	                    <a href="{{ route('admin.languages.download', array('f_n'=>$value->folder_name, 'file_name'=>'validation')) }}">
	                        <i class="fa fa-download"></i>
	                    </a>
	                </div>
	            </li>
	        </ul>
	    </div>

	</div>

	<div class="box-footer">

	    <button type="reset" class="btn btn-danger">{{ tr('cancel') }}</button>
	    
	    <button type="button" class="btn btn-success pull-right" @if(Setting::get('admin_delete_control')) disabled  @endif></button>
	   
	</div>

</form>