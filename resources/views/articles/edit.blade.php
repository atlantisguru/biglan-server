@extends('layout')
@section('title')
	{{ __('all.articles.articles') }} | BigLan
@endsection
@section('content')
	<div class="row mt-2">
		<div class="col-12">
        	@include('articles.header')
        </div>
    </div>
	<div class="row mt-2">
		<div class="col-12" id="status">
		</div>
    </div>
	<div class="row">
		<div class="col-9">
        	<input type="hidden" name="artid" id="artid" @if(isset($article)) value="{{ $article->id }}" @endif>
        	<strong>{{ __('all.articles.title') }}</strong>
			<input type="text" class="form-control h3" name="title" placeholder="" @if(isset($article)) value="{{ $article->title }}" @endif>
        	<strong>{{ __('all.articles.content') }}</strong>
            <textarea class="inputStyle" id="editor" name="content"> @if(isset($article)) {{ $article->body }} @endif</textarea>
		</div>
    	<div class="col-3">
        	<div class="mb-4">
        		<a href="javascript:" class="btn btn-primary mr-2" id="btn-save-article">{{ __('all.articles.publish') }}</a> <a href="{{ url('/articles') }}" class="btn btn-light">{{ __('all.button.cancel') }}</a>
            </div>
        	<p class="h4">{{ __('all.articles.categories') }}</p>
        	<input name="filter" id="filter" class="form-control" placeholder="{{ __('all.articles.filter') }}">
        	<div  id="category-list" class="mt-2">
        		@foreach($categories as $category)
        		<div class="form-check">
  					<input class="form-check-input" type="checkbox" name="category" value="{{ $category->id }}" id="check{{ $category->id }}" @if(isset($article) && in_array($category->id, $article->categories()->pluck("category_id")->toArray())) CHECKED  @endif>
  					<label class="form-check-label" for="check{{ $category->id }}">
    					{{ $category->name }}
  					</label>
				</div>
        	@endforeach
        	</div>
        	<div class="mt-4">
            	<span class="mb-4">{{ __('all.articles.new_category') }}</span>
            	<div id="category-status"></div>
            	<div class="input-group">
  					<input type="text" class="form-control" placeholder="{{ __('all.articles.category_name') }}" name="category-name" aria-describedby="basic-addon1">
        			<div class="input-group-append">
    					<button class="btn btn-primary" type="button" id="btn-save-category"><i class="fas fa-check"></i></button>
  					</div>
        		</div>
        	</div>
        
		</div>
    </div>
    <div class="row">
    	&nbsp;
    </div>
@endsection
@section('inject-footer')
	<style type="text/css">
    	.badge-purple {
        	background: #9b59b6;
        	color: #FFF;
    	}
    
    	.ck-editor__editable_inline {
        	min-height: 30em;
    	}
    	.checkbox-2x {
    		transform: scale(2);
    		-webkit-transform: scale(2);
		}
    	
    	#category-list {
        	height: 14em;
        	overflow: auto;
    	}
   	</style>
    <script src="{{ url('js/ckeditor5/build/ckeditor.js') }}"></script>
    <script>
  		ClassicEditor.create( document.querySelector( '#editor' ), {
    		language: 'hu',
    		toolbar: ['heading', '|', 'bold', 'italic', 'underline', 'fontColor', '|', 'alignment', 'bulletedList', 'numberedList','|', 'blockQuote', 'link', 'mediaEmbed', 'insertTable', 'horizontalLine', '|', 'undo', 'redo'],
    	}).then( newEditor => {
    		editor = newEditor;
    	}).catch( error => {
    		console.error( error );
		});
	</script>
    
	<script type="text/javascript">
    $(function() {
    
    jQuery.expr[':'].contains = function(a, i, m) {
  		return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
	};
    
   	var contentChanged = false;
    $("#btn-save-article").addClass("disabled");
    
    $("input[name=title]").on("keyup", function() {
    	contentChanged = true;
    	$("#btn-save-article").removeClass("disabled");
    });
                                       
    $("input[name=category]").on("keyup", function() {
    	contentChanged = true;
    	$("#btn-save-article").removeClass("disabled");
    });
    
    editor.model.document.on( 'change', () => {
    	contentChanged = true;
		$("#btn-save-article").removeClass("disabled");
    });
    
    $("a:not(#btn-save-article,.ck), button:not(.ck, #btn-save-category)").on("click", function() {
    	if(contentChanged == true) {
        	return confirm('Biztosan elhagyod az oldalt? A nem mentett változások el fognak veszni!');
        }
    });
    
    $("#filter").on("keyup", function() {
    	var phrase = $(this).val();
    	console.log(phrase);
    	$("#category-list .form-check").hide();
    	$("#category-list label:contains("+phrase+")").parent().show();
    	if (phrase == "") {
        	$("#category-list .form-check").show();
    	}
    });
    
    $("#btn-save-article").on("click", function() {
        	$("#status").html("");
    
    		var payLoad = {};
            var categories = [];
			$('input[name=category]:checked').each(function(i, e) {
    			categories.push($(this).val());
            });
            
    		payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
    		payLoad['action'] = "saveArticle";
    		payLoad['artid'] = $('input[name=artid]').val();
            payLoad['title'] = $('input[name=title]').val();
    		payLoad['body'] = editor.getData();
    		payLoad['categories'] = categories.join();
    		payLoad['notification'] = $('#notification:checked').val();
    		var saveArticle = $.post("{{ url('articles/payload') }}", payLoad, "JSONP");
        	saveArticle.done(function(data) {
            	//console.log(data);
               	if (data.status == "error") {
                	$("#status").append("<div class='alert alert-danger alert-dismissible fade show'><button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span></button></div>");
                	for(i = 0; i < data.errors.length; i++) {
                    	$("#status .alert").append(data.errors[i] + "<br>");
                	}
                }
            	if (data.status == "ok") {
                	$("#status").append("<div class='alert alert-success alert-dismissible fade show'><button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span></button></div>");
                	$("#status .alert").append("Bejegyzés sikeresen mentve");
                    $("#artid").val(data.artid);
                	$('#notification:checked').prop( "checked", false );
                	$("#btn-save-article").addClass("disabled");
                	contentChanged = false;
                }
            });
        });
    
    	$("#btn-save-category").on("click", function() {
        	
        	$("#category-status").html("");	
        
    		var payLoad = {};
            payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
    		payLoad['action'] = "saveCategory";
    		payLoad['name'] = $('input[name=category-name]').val();
    		var saveCategory = $.post("{{ url('articles/payload') }}", payLoad, "JSONP");
        	saveCategory.done(function(data) {
               	//console.log(data);
               	if (data.status == "error") {
                	$("#category-status").append("<div class='alert alert-danger alert-dismissible fade show'><button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span></button></div>");
                	for(i = 0; i < data.errors.length; i++) {
                    	$("#category-status .alert").append(data.errors[i] + "<br>");
                	}
                }
            	if (data.status == "ok") {
                	$("#category-list").append('<div class="form-check"><input class="form-check-input" type="checkbox" name="category" value="' + data.catid + '" id="check' + data.catid + '" CHECKED><label class="form-check-label" for="check' + data.catid + '">' + data.catname + '</label></div>');
                	$('input[name=category-name]').val("");
                }
            	if (data.status == "exists") {
                	$("#check" + data.catid).prop('checked', true);
                	$('input[name=category-name]').val("");
                }
            });
        });
    
    });
    
	</script>                              
@endsection