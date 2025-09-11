@extends('layout')
@section('title')
	{{ __('all.command_center.script_storage') }} | BigLan
@endsection
@section('content')
	{{ csrf_field()	}}
	<div class="row mt-2">
		<div class="col-12">
        	@include('commands.header')
        </div>
    </div>
	<div class="row">
    	<div class="col-lg-6 col-md-10 col-sm-12 mx-auto">
    	<form>
  			<div class="form-group">
            	<input name="command_id" id="command_id" type="hidden" />
    			<label for="scripts">{{ __('all.command_center.predefined_scripts') }}</label>
    			<select class="form-control" name="scripts" id="scripts">
                	<option value="">{{ __('all.command_center.select_script') }}</option>
                	@foreach($scripts as $script)
                		<option value='{{ $script->id }}'>{{ $script->alias }}</option>
                	@endforeach
            	</select>
  			</div>
        	<div class="form-group">
    			<label for="command">{{ __('all.command_center.script') }}</label>
    			<textarea class="form-control" id="command" name="command" rows="10" disabled></textarea>
                @if(auth()->user()->hasPermission('delete-script'))
                	<a href="javascript:void(0)" class="btn btn-danger d-none mt-2" id="delete-command">{{ __('all.command_center.delete_script_from_database') }}</a>
                @endif
  			</div>
        	
        </form>
        </div>
	</div>
@endsection
@section('inject-footer')
	<script type="text/javascript">
    $(function() {
    	
    	@if(auth()->user()->hasPermission('read-script'))
    	$("#scripts").on("change", function() {
        	$("#delete-command").removeClass("d-none");
        	var id = $(this).val();
        	var payLoad = {};
            payLoad['_token'] = $('meta[name="csrf-token"]').attr('content');
            payLoad['action'] = "viewScript";
            payLoad['id'] = $(this).val();
        	var posting = $.post("{{ url('commands/payload') }}", payLoad, "JSONP");
        	posting.done(function(data) {
            		$("#command").val(data);
               		//location.reload();
            	
            });
        });
    	@endif
    
        @if(auth()->user()->hasPermission('delete-script'))
    	$("#delete-command").on("click", function() {
        	var confirm = window.confirm("{{ __('all.command_center.are_you_sure_delete_script') }}");
            if(!confirm) {
            	return;
            }
        	var id = $("#scripts").val();
        	var payLoad = {};
            payLoad['_token'] = $('meta[name="csrf-token"]').attr('content');
            payLoad['action'] = "deleteScript";
            payLoad['id'] = id;
        	var posting = $.post("{{ url('commands/payload') }}", payLoad, "JSONP");
        	posting.done(function(data) {
            	if (data == "OK") {
            		$("#scripts option[value='"+id+"']").remove();
            		$("#scripts").val($("#scripts option:first").val());
            		$("#command").val("");
                	$("#delete-command").addClass("d-none");
                }
            });
        });
    	@endif
    
    });
    
	</script>                              
@endsection