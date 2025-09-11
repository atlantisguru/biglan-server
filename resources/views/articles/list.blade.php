@extends('layout')
@section('title')
	{{ __('all.articles.articles') }} | BigLan
@endsection
@section('content')

@php
	$userPermissions = auth()->user()->permissions();
@endphp

	<div class="row mt-2">
		<div class="col-12">
        	@include('articles.header')
        </div>
    </div>
	
	<div class="row mt-2">
	    	@if($articles->count() == 0)
				<div class="col-lg-6 col-sm-12">
        			<div>{{ __('all.articles.no_post_found') }}</div>
				</div>
        	@endif
		@if($articles->count() > 0)
			
    	<div class="col-lg-4 col-12">
			<div class="card mt-1 mb-1 shadow-sm">
            	<div class="card-body">
			<p class="card-text">
        	@foreach($categories as $category)
        		<a href="{{ url('/articles?cat='.$category->id) }}" class="btn btn-sm mb-1">{{ $category->name }} <span class="badge badge-light">{{ $category->count }}</span></a>
        	@endforeach
			</p>
			</div>
			</div>
        </div>
		@endif
		@foreach($articles as $article)
        	<div class="col-lg-4 col-12">
        		<div class="card shadow-sm mt-1 mb-1">
            		<div class="card-body">
			
					    	@foreach($article->categories() as $category)
                        		<span class="badge badge-category"><a href="{{ url('/articles?cat='.$category->category->id) }}" class="text-decoration-none text-white">{{ $category->category->name }}</a></span>
                			@endforeach
                    <h5 class="card-title"><a href="{{ url('/articles/article/'.$article->id) }}" class="text-dark">{{ $article->title }}</a></h5>
					<p class="card-text"><small class="text-muted text-uppercase">{{ $article->user->username }} | {{ \Carbon\Carbon::parse($article->created_at)->format("Y.m.d.") }} @if($article->created_at != $article->updated_at) ({{ __('all.articles.updated') }}: {{ \Carbon\Carbon::parse($article->updated_at)->format("Y.m.d.") }}) @endif @if($article->comments()->count() > 0) | <i class="far fa-comment"></i> {{ $article->comments()->count() }} hozzászólás @endif </small></p>
                	<p class="card-text">{!! Str::limit(strip_tags($article->body), $limit = 150, $end = '...') !!}</p>
					@if(in_array('read-post', $userPermissions))
                		<a href="{{ url('/articles/article/'.$article->id) }}" class="btn btn-outline-secondary">{{ __('all.articles.read_more') }}</a>
                	@endif
            		</div>
				</div>
			</div>
		@endforeach
	</div>    
    <div class="row mt-2">
		<div class="col-12">
        	{{ $articles->appends(Request::query())->links() }}
    	</div>
    </div>
@endsection
@section('inject-footer')
	<style type="text/css">
    	.badge-category:nth-of-type(3n+1) {
  			background: #28a745;
        	color: #FFF;
		}

    	.badge-category:nth-of-type(3n+2) {
  			background: #007bff;
        	color: #FFF;
		}
    	
    	.badge-category:nth-of-type(3n+3) {
  			background: #9b59b6;
        	color: #FFF;
		}
    
    	
   	</style>
 	<script type="text/javascript">
    $(function() {});
     </script>                              
@endsection