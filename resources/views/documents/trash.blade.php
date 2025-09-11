@extends ('layout')

@section('title')
	{{ __('all.documents.documents') }} | BigLan
@endsection

@section('content')
@php
	$userPermissions = auth()->user()->permissions();
@endphp

	<div class="row mt-2">
	<div class="col-12 nav-bar">
		<a href="{{ url("documents") }}" class="btn btn-primary btn-sm"><i class="fas fa-arrow-circle-up"></i> {{ __('all.button.back') }}</a>
	</div>
	</div>
	<div class="row">
	@php
		$documents = \App\Models\Documents::where("deleted", 1)->orderBy("created_at", "DESC")->paginate(20);
	@endphp
	</div>
	<div class="row mt-2">
    <div class="col-12">
		<div class="table-responsive">
			<table class="table table-striped  table-sm table-hover">
    		<thead  class="thead-dark">
				<tr>
    				<th></th>
    				<th>{{ __('all.documents.name') }}</th>
    				<th>{{ __('all.documents.keywords') }}</th>
    				<th>{{ __('all.documents.original_date') }}</th>
    				<th>{{ __('all.documents.size') }}</th>
    				<th>{{ __('all.documents.username') }}</th>
    				<th>{{ __('all.documents.created') }}</th>
    				<th>{{ __('all.documents.actions') }}</th>
    			</tr>
    		</thead>
    		<tbody>
				@foreach($documents as $document)
    			<tr>
    				<td>
    					@php
    						$extension = last(explode(".", $document->filename));
    					@endphp
                        @switch($extension)
                        	@case("pdf")
                        		<i class="far fa-file-pdf text-danger"></i>
                        		@break
                        	@case("png")
                        		<i class="far fa-file-image text-info"></i>
                        		@break
                        	@case("jpg")
                        		<i class="far fa-file-image text-info"></i>
                        		@break
                        	@case("doc")
                        		<i class="far fa-file-alt text-primary"></i>
                        		@break
                        	@case("docx")
                        		<i class="far fa-file-alt text-primary"></i>
                        		@break
                        	@case("xls")
                        		<i class="fas fa-table text-success"></i>
                        		@break
                        	@case("xlsx")
                        		<i class="fas fa-table text-success"></i>
                        		@break
                        	@default
                        		@break
                       	@endswitch
    				</td>
    				<td>
    					<a target="_blank" href="documents/{{ $document->filename }}">{{ $document->title }}</a>
    				</td>
                    <td>
                        <span class="text-muted">{{ Str::limit($document->keywords, $limit = 30, $end = '...') }}</span>
    				</td>
                    <td>
                        {{ \Carbon\Carbon::parse($document->signed_at)->format("Y.m.d.") }}    
                    </td>    
                    <td>
    					{{
    ($document->filesize <= 1024)
        ? $document->filesize."B"
        : (($document->filesize/1024 <= 1024)
            ? round($document->filesize/1024, 2)."kB"
            : round($document->filesize/1024/1024, 2)."MB")
}}
    				</td>
    				<td>
                    	{!! $document->uploader ? $document->uploader->username : '' !!}
    				</td>
    				<td>
                    	{{ \Carbon\Carbon::parse($document->created_at)->format("Y.m.d. H:i") }}
    				</td>
                    <td>
                    	@if(in_array('write-document', $userPermissions))
                    		<a href="javascript:" data-id="{{ $document->id }}" class="restore-document btn btn-outline-success btn-sm">{{ __('all.documents.restore') }}</a>
                    	@endif
                    	@if(in_array('delete-document', $userPermissions))
                    		<a href="javascript:" data-id="{{ $document->id }}" class="delete-document btn btn-outline-danger btn-sm">{{ __('all.button.delete') }}</a>
                    	@endif
                    </td>
    			</tr>
              
    			@endforeach
                    </tbody>
                    
			</table>
		</div>
	</div>
	</div>
    <div class="row">
    	<div class="col">
                    {{ $documents->appends(Request::query())->links() }}
		</div>
    </div>
@endsection

@section('inject-footer')
    <script type="text/javascript">
   		$(function() {
        	$(".restore-document").on("click", function() {
            	var documentId = $(this).attr("data-id");
            	var payLoad = {};
            	payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            	payLoad['action'] = 'restoreDocument';
            	payLoad['id'] = documentId;
            	var restoreDocument = $.post("{{ url('api/v2') }}", payLoad, "JSONP");
        		restoreDocument.done(function(data) {
                	location.reload();
                });
            });
        
        	$(".delete-document").on("click", function() {
            	var documentId = $(this).attr("data-id");
            	var payLoad = {};
            	payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            	payLoad['action'] = 'deleteDocument';
            	payLoad['id'] = documentId;
            	var deleteDocument = $.post("{{ url('api/v2') }}", payLoad, "JSONP");
        		deleteDocument.done(function(data) {
                	location.reload();
                });
            });
        
        });
    </script>
@endsection