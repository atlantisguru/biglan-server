@extends("layout")
@section("title")
{{ __('all.downloads.downloads') }} | BigLan
@endsection
@section("content")

@php
	$userPermissions = auth()->user()->permissions();
@endphp

	<div class="row mt-2">
		<div class="col-6">
			{{ csrf_field()}}
			@if(in_array('upload-download', $userPermissions))
   				<a href="javascript:" class="btn btn-sm btn-primary mr-2" id="upload-btn"><i class="fas fa-plus"></i> {{ __('all.downloads.upload') }}</a>
            @endif
        </div>
	</div>
	<div class="row mt-2">
		<div class="col-lg-12 col-sm-12">
			<div class="table-responsive">
				@if(count($downloads) > 0)
   				<table class="table table-striped table-hover" id="downloads">
						<thead class="thead-dark">
							<tr>
            					<th>{{ __('all.downloads.public') }}</th>
            					<th>{{ __('all.downloads.alias') }}</th>
            					<th>{{ __('all.downloads.filename') }}</th>
            					<th>{{ __('all.downloads.size') }}</th>
            					<th>{{ __('all.downloads.counter') }}</th>
            					<th>{{ __('all.downloads.created') }}</th>
            					<th>{{ __('all.downloads.actions') }}</th>
            				</tr>
						</thead>
						<tbody>
					
   				@foreach($downloads as $download)
							<tr data-id={{ $download->id }}>
								<td class="text-center">
            						<input type="checkbox" data-id={{ $download->id }} class="publish" @if($download->published) checked="checked" @endif>
            					</td>
								<td class="editable" data-field="alias">{{ $download->alias }}</td>
								<td>{{ $download->filename }}</td>
								<td class="text-right">
            						@if(isset($download->size))
                                      	@if($download->size < 1024)
                                        	{{ round($download->size, 2) }}KB
                                        @endif
                                        @if($download->size > 1024 && $download->size/1024 < 1024)
                                        	{{ round($download->size/1024, 2) }}MB
                                        @endif
                                        @if($download->size/1024 > 1024 && $download->size/1024/1024 < 1024)
                                        	{{ round($download->size/1024/1024, 2) }}GB
                                        @endif
                                    @else
                                        "N/A"
                                    @endif
								</td>
            					<td class="text-center">{{ $download->counter }}</td>
								<td>{{ $download->created_at }}</td>
								<td class="text-right">
            						<a href={{ url("/downloads/".$download->filename) }} class="btn btn-primary btn-sm">{{ __('all.button.download') }}</a>
            						<a href="javascript:" data-id={{ $download->id }} class="btn btn-danger btn-sm delete-file">{{ __('all.button.delete') }}</a>
            					</td>
							</tr>
			    @endforeach
						</tbody>
					</table>
            		
            	@else
                	<p>{{ __('all.downloads.download_not_found') }}</p>
				@endif
			</div>
		</div>
	</div>
    
	 @if(in_array('upload-download', $userPermissions))             
    <!-- The Modal -->
	<div class="modal" id="upload-form">
  		<div class="modal-dialog">
    		<div class="modal-content">

      			<!-- Modal Header -->
      			<div class="modal-header">
        			<h4 class="modal-title">{{ __('all.downloads.upload_file') }}</h4>
        			<button type="button" class="close" data-dismiss="modal">&times;</button>
      			</div>

      			<!-- Modal body -->
      			<div class="modal-body">
                    	<div class="row">
                    		<div class="col-12">
                    			{{ __('all.downloads.select_file') }}:
                    			<input type="file" id="file" name="file" class="form-control">
                    		</div>
                    	</div>
                        <div class="row mt-2">
        					<div class="col-12">
                    			<input type="checkbox" name="published" id="published" class=""> <label>{{ __('all.downloads.public') }}</label>
                    		</div>
                    	</div>
                        <div class="row mt-2">
        					<div class="col-12">
                    			{{ __('all.downloads.alias') }}:
                    			<input type="text" name="alias" id="alias" class="form-control">
                    		</div>
                    	</div>
                </div>

      			<!-- Modal footer -->
      			<div class="modal-footer">
                    <button type="button" id="upload-file-btn" class="btn btn-primary">{{ __('all.button.upload') }}</button>
        			<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('all.button.close') }}</button>
      			</div>

    		</div>
  		</div>
	</div>
	@endif

@endsection
@section('inject-footer')
        <link rel="stylesheet" type="text/css" href={{ url("css/jquery.dataTables.min.css") }}>
        <script type="text/javascript" src={{ url("js/jquery.dataTables.min.js") }}></script>
        <script type="text/javascript">
        	$(function() {
    				$('#downloads').DataTable({
                    	"pageLength": 25
                    });
			});

			$("#upload-btn").on("click", function(){
            
            	$("#upload-form").modal("show");
            
            });

			$("#upload-file-btn").on("click", function() {
            	$("#upload-file-btn").html('<i class="fas fa-spinner fa-spin"></i> ' + $("#upload-file-btn").text());
            	$("#upload-file-btn").addClass("disabled");
            	var payLoad = {};
            	payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            	payLoad['action'] = 'uploadFile';
            	var file = $('#file').prop('files')[0];
            	const reader = new FileReader();
    			reader.onload = (e) => {
      				const data = e.target.result.split(',')[1];
                	payLoad["file"] = data; 
                	payLoad["filename"] = file.name;
            		payLoad['alias'] = $('input[name="alias"]').val()
                	payLoad['published'] = $('#published').is(':checked');
                	
            		var uploadFile = $.post("{{ url('downloads') }}", payLoad, "JSONP");
        			uploadFile.done(function(data) {
                		$("#upload-file-btn").html("{{ __('all.button.upload') }}");
                		$("#upload-file-btn").removeClass("disabled");
            			$('#upload-form').modal('hide');
                		location.reload();
                	});
                	uploadFile.fail(function(jqXHR, textStatus, errorThrown) {
        				//console.error("Error during file upload:", textStatus, errorThrown);
        				$("#upload-file-btn").html("{{ __('all.button.upload') }}");
        				$("#upload-file-btn").removeClass("disabled");
                    });
                };
            	reader.readAsDataURL(file);
            
            });

			$(".publish").on("click", function() {
            	$(this).addClass("disabled");
            	var payLoad = {};
            	payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            	payLoad['action'] = 'publishFile';
            	payLoad['id'] = $(this).attr("data-id");
            	payLoad['published'] = $(this).is(':checked');
            	var publishFile = $.post("{{ url('downloads') }}", payLoad, "JSONP");
        		publishFile.done(function(data) {
                	$(".publish.disabled").removeClass("disabled");
                	//console.log(data);
                });
            
            });

			$(".delete-file").on("click", function() {
            	var confirm = window.confirm("{{ __('all.downloads.are_you_sure_delete_file') }}");
           		if(!confirm) {
            		return;
            	}
            	var payLoad = {};
            	payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            	payLoad['action'] = 'deleteFile';
            	payLoad['id'] = $(this).attr("data-id");
            	var deleteFile = $.post("{{ url('downloads') }}", payLoad, "JSONP");
        		deleteFile.done(function(data) {
                	if(data=="OK") {
                    	location.reload();
                    }
                });
            
            });


        </script>                           	                              
@endsection