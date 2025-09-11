<form class="form-inline">
	<div class="form-group">
		<a href="{{ url('/articles') }}" class="text-dark text-decoration-none mr-4" >{{ __('all.articles.articles') }}</a>
		@if(auth()->user()->hasPermission('write-post'))
			<a href="{{ url('/articles/new') }}" class="btn btn-primary btn-sm mr-2"><i class="fas fa-plus"></i> {{ __('all.articles.new_post') }}</a>
		@endif
		<div class="input-group">
  			<input type="text" class="form-control form-control-sm" style="min-width:400px" placeholder="" name="q" value="{{ app('request')->input('q') }}" >
        	<div class="input-group-append">
    			<button class="btn btn-primary btn-sm" type="button" id="search-btn"><i class="fas fa-search"></i></button>
  			</div>
        </div>
    </div>
</form>