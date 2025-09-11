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
<div id="article">
	<div class="row mt-2">
		<div class="col-12">
        	<h4>{{ $article->title }}</h4>
        </div>
    </div>
	<div class="row bg-light border-bottom border-top">
		<div class="col-12">
        	<span class="text-uppercase">{{ $article->user->username }}</span> | {{ \Carbon\Carbon::parse($article->created_at)->format("Y.m.d.") }} @if($article->created_at != $article->updated_at) ({{ __('all.articles.updated') }}: {{ \Carbon\Carbon::parse($article->updated_at)->format("Y.m.d.") }}) @endif  | <i class="far fa-comment"></i> {{ $article->comments()->count() }} {{ __('all.articles.comment') }}
        </div>
    </div>
	<div class="row mt-4 mb-4">
		<div class="col-12">
        	{!! $article->body !!}
        </div>
    </div>
	<div class="row mt-4 mb-4 bg-light border-bottom border-top">
		<div class="col-6">
        	{{ __('all.articles.tags') }}:
        	@foreach($article->categories() as $category)
        		<a href="{{ url('/articles?cat='.$category->category->id) }}" class="text-decoration-none">#{{ $category->category->name }}</a>
        	@endforeach
        </div>
    	<div class="col-6">
        	<a href="javascript:" id="print-article" class="text-decoration-none"><i class="fas fa-print"></i> {{ __('all.articles.print') }}</a> | <a href="{{ url('/articles/edit/'.$article->id) }}" class="text-decoration-none"><i class="far fa-edit"></i> {{ __('all.articles.edit') }}</a>
        </div>
    </div>
</div>
@if(auth()->user()->hasPermission('read-comment'))
<div id="comments">
	<div class="row mt-4 mb-1">
		<div class="col-12">
        	<h4>{{ __('all.articles.comments') }}</h4>
        </div>
    </div>
	<div class="row mt-1 mb-2">
		@foreach($article->comments() as $comment)
    	<div class="col-12">
      		<div class="card mt-2">
  				<div class="card-body">
                	<h5 class="card-title">{{ $comment->user->username }}</h5>
                	<h6 class="card-subtitle mb-2 text-muted">{{ \Carbon\Carbon::parse($comment->created_at)->format("Y.m.d. H:i") }}</h6>
    				<p class="card-text">{!! nl2br($comment->comment) !!}</p>
  				</div>
			</div>
      	</div>
    	@endforeach
    </div>
    @if(auth()->user()->hasPermission('write-comment'))
	<div class="row mt-1 mb-4">
		<div class="col-12">
        	{{ __('all.articles.your_comment') }}:
        	<span id="comment-status"></span>
			<textarea class="form-control" rows="5" id="new-comment-text"></textarea>
        	<a herf="javascript:" id="btn-save-comment" class="btn btn-primary mt-1">{{ __('all.articles.send') }}</a>
        </div>
    </div>
    @endif
</div>
@endif
@endsection
@section('inject-footer')
	<style type="text/css">
    </style>
	<script type="text/javascript">
    $(function() {
    
    	$("#print-article").on("click", function() {
    		
        	var divToPrint=document.getElementById('article');
			var newWin=window.open('','Print-Window');
			newWin.document.open();
			newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');
			newWin.document.close();
			setTimeout(function(){newWin.close();},10);	
    	
    	});
    
    	@if(auth()->user()->hasPermission('write-comment'))
    	$("#btn-save-comment").on("click", function() {
        
        	$("#comment-status").html("");	
        
    		var payLoad = {};
            payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
    		payLoad['action'] = "saveComment";
    		payLoad['article_id'] = {{ $article->id }};
    		payLoad['comment'] = $('#new-comment-text').val();
        	var saveComment = $.post("{{ url('articles/payload') }}", payLoad, "JSONP");
        	saveComment.done(function(data) {
               	if (data.status == "error") {
                	$("#comment-status").append("<div class='alert alert-danger alert-dismissible fade show'><button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span></button></div>");
                	for(i = 0; i < data.errors.length; i++) {
                    	$("#comment-status .alert").append(data.errors[i] + "<br>");
                	}
                }
            	if (data.status == "ok") {
                	location.reload();
                }
            });
        
        });
    	@endif
    
    });
	</script>                              
@endsection