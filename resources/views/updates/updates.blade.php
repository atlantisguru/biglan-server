@extends("layout")
@section("title")
{{ __('all.updates.updates') }} | BigLan
@endsection
@section("content")

@php
	$userPermissions = auth()->user()->permissions();
@endphp

	<div class="row mt-2">
		<div class="col-6">
			{{ csrf_field()}}
			@if(in_array('read-updates', $userPermissions))
   				<a href="javascript:" class="btn btn-sm btn-primary mr-2" id="upload-btn"><i class="fas fa-plus"></i> {{ __('all.updates.upload') }}</a>
            @endif
        </div>
	</div>
	<div class="row mt-2">
		<div class="col-lg-12 col-sm-12">
			<div class="table-responsive">
				@if(count($updates) > 0)
   				<table class="table table-striped table-hover" id="updates">
						<thead class="thead-dark">
							<tr>
            					<th>{{ __('all.updates.channel') }}</th>
            					<th>{{ __('all.updates.version') }}</th>
            					<th>{{ __('all.updates.notes') }}</th>
            					<th>{{ __('all.updates.created') }}</th>
            					<th>{{ __('all.updates.counter') }}</th>
            					<th>{{ __('all.updates.actions') }}</th>
            				</tr>
						</thead>
						<tbody>
						@php
            				$firstA = true;
            				$firstB = true;
            				$firstD = true;
            			@endphp
   				@foreach($updates as $update)
							<tr data-id={{ $update->id }}>
								<td class="text-center">{{ $update->channel }}</td>
								<td>{!! $update->version !!}</td>
								<td>{!! $update->description !!}</td>
								<td>{{ $update->created_at }}</td>
								<td class="text-center">{{ $update->counter }}</td>
								<td class="">
                        		
            					@if(($firstA && $update->channel == "a") || ($firstB && $update->channel == "b") || ($firstD && $update->channel == "d"))
            						@if($update->counter == 0 && $update->active == 0)
            							<a href="javascript:" data-id={{ $update->id }} class="btn btn-success btn-sm m-1 deploy-btn">{{ __('all.updates.deploy') }}</a>
                        				<a href="javascript:" data-id={{ $update->id }} class="btn btn-danger btn-sm m-1 delete-btn">{{ __('all.updates.delete') }}</a>
            						@endif
            						@if($update->counter > 0 && $update->active == 0)
                   						<a href="javascript:" data-id={{ $update->id }} class="btn btn-success btn-sm m-1 deploy-btn">{{ __('all.updates.deploy') }}</a>
            						@endif
            						@if($update->counter >= 0 && $update->active == 1)
                   						<a href="javascript:" data-id={{ $update->id }} class="btn btn-danger btn-sm m-1 revoke-btn">{{ __('all.updates.revoke') }}</a>
            						@endif
                        			@if($update->channel == 'a')
                        				@php $firstA = false; @endphp
                        			@endif
                                    @if($update->channel == 'b')
                        				@php $firstB = false; @endphp
                        			@endif
                                    @if($update->channel == 'd')
                        				@php $firstD = false; @endphp
                        			@endif
                                @endif
                                </td>
							</tr>
			    @endforeach
						</tbody>
					</table>
            		
            	@else
                	<p>{{ __('all.updates.update_not_found') }}</p>
				@endif
			</div>
		</div>
	</div>

    @if(in_array('upload-update', $userPermissions))             
    <!-- The Modal -->
	<div class="modal" id="upload-form">
  		<div class="modal-dialog">
    		<div class="modal-content">

      			<!-- Modal Header -->
      			<div class="modal-header">
        			<h4 class="modal-title">{{ __('all.updates.upload_file') }}</h4>
        			<button type="button" class="close" data-dismiss="modal">&times;</button>
      			</div>

      			<!-- Modal body -->
      			<div class="modal-body">
                    	<div class="row">
                    		<div class="col-12">
                    			{{ __('all.updates.select_file') }}:
                    			<input type="file" id="file" name="file" class="form-control">
                    		</div>
                    	</div>
                        <div class="row mt-2">
        					<div class="col-12">
                                {{ __('all.updates.channel') }}:
                    			<select name="channel" id="channel" class="form-control">
                                	<option value="b">beta</option>
                                	<option value="a">alpha</option>
                                	<option value="d">developer</option>
                                </select>
                    		</div>
                    	</div>
                        <div class="row mt-2">
        					<div class="col-12">
                    			{{ __('all.updates.notes') }}:
                    			<textarea rows="5" name="notes" id="notes" class="form-control"></textarea>
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
    				$('#updates').DataTable({
                    	"pageLength": 25,
                    	"order": [[3, 'desc']]
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
            			payLoad['channel'] = $('select[name="channel"]').val()
                		payLoad['notes'] = $('textarea[name="notes"]').val()
                	
            			var uploadFile = $.post("{{ url('updates') }}", payLoad, "JSONP");
        				uploadFile.done(function(data) {
                			$("#upload-file-btn").html("{{ __('all.button.upload') }}");
                			$("#upload-file-btn").removeClass("disabled");
            				$('#upload-form').modal('hide');
                			location.reload();
                		});
                		uploadFile.fail(function(jqXHR, textStatus, errorThrown) {
        					$("#upload-file-btn").html("{{ __('all.button.upload') }}");
        					$("#upload-file-btn").removeClass("disabled");
                    	});
                	};
            		
                	reader.readAsDataURL(file);
            
            	});
            
            	$(".delete-btn").on("click", function() {
            		var confirm = window.confirm("{{ __('all.updates.are_you_sure_delete_update') }}");
           			if(!confirm) {
            			return;
            		}
            		var payLoad = {};
            		payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            		payLoad['action'] = 'deleteUpdate';
            		payLoad['id'] = $(this).attr("data-id");
            		var deleteUpdate = $.post("{{ url('updates') }}", payLoad, "JSONP");
        			deleteUpdate.done(function(data) {
                		if(data=="OK") {
                    		location.reload();
                    	}
                	});
            	});
            
            	$(".deploy-btn").on("click", function() {
            		var confirm = window.confirm("{{ __('all.updates.are_you_sure_deploy_update') }}");
           			if(!confirm) {
            			return;
            		}
            		var payLoad = {};
            		payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            		payLoad['action'] = 'deployUpdate';
            		payLoad['id'] = $(this).attr("data-id");
            		var deployUpdate = $.post("{{ url('updates') }}", payLoad, "JSONP");
        			deployUpdate.done(function(data) {
                		if(data=="OK") {
                    		location.reload();
                    	}
                	});
            	});
            
            	$(".revoke-btn").on("click", function() {
            		var confirm = window.confirm("{{ __('all.updates.are_you_sure_revoke_update') }}");
           			if(!confirm) {
            			return;
            		}
            		var payLoad = {};
            		payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            		payLoad['action'] = 'revokeUpdate';
            		payLoad['id'] = $(this).attr("data-id");
            		var revokeUpdate = $.post("{{ url('updates') }}", payLoad, "JSONP");
        			revokeUpdate.done(function(data) {
                		if(data=="OK") {
                    		location.reload();
                    	}
                	});
            	});
            
			});
        </script>
@endsection