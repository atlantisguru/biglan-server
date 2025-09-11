@extends('layout')
@section('title')
	{{ __('all.command_center.new_command') }} | BigLan
@endsection
@section('content')
	<div class="row mt-2">
		<div class="col-12">
        	@include('commands.header')
        </div>
    </div>
	<div class="row mt-2">
		<div class="col-12" id="status">
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
                		<option value='{{ $script->code }}'>{{ $script->alias }}</option>
                	@endforeach
            	</select>
  			</div>
        	<div class="form-group">
    			<label for="command">{{ __('all.command_center.script') }}</label>
    			<textarea class="form-control" id="command" name="command" rows="3" disabled></textarea>
  			</div>
        	<div class="form-group">
    			<label for="description">{{ __('all.command_center.notes') }}</label>
    			<textarea class="form-control" id="description" name="description" rows="3"></textarea>
  			</div>
        	<div class="form-group">
            	<label>{{ __('all.command_center.workstations') }}</label>
            	<div class="row">
                	<div class="col">
    					<select name="ws_selected" class="form-control" size="8" multiple>
            			</select>
                    	(<span class="ws_selected_counter"></span> {{ __('all.command_center.workstation') }})
  					</div>
                	<div class="col-2">
    					<div class="mt-4 mb-4"><a href="javascript:" class="btn btn-lg btn-success" id="add-button"><i class="fas fa-arrow-left"></i></a></div>
                    	<div><a href="javascript:" class="btn btn-lg btn-danger"  id="remove-button"><i class="fas fa-arrow-right"></i></a></div>
                    </div>
                	<div class="col">
                    	<select name="ws_haystack" class="form-control" size="8" multiple>
                			@foreach($workstations as $workstation)
                				<option value='{{ $workstation->id }}'>{{ $workstation->alias }} ({{ $workstation->update_channel }}{{ $workstation->service_version}})</option>
                			@endforeach
            			</select>
                    	(<span class="ws_haystack_counter"></span> {{ __('all.command_center.workstation') }})
                    </div>
                </div>
  			</div>
        	<div class="form-group">
            	<label for="target_date">{{ __('all.command_center.earliest_run_time') }}</label>
            	<div class="input-group">
            		<input type="date" class="form-control" name="target_date" placeholder="ÉÉÉÉ.HH.NN" required>
            		<input type="time" class="form-control" name="target_time" placeholder="ÓÓ:PP" required>
                </div>
          	</div>
        	<div class="form-group">
            	<a href="javascript:" class="btn btn-primary" id="save-command-button">{{ __('all.button.save') }}</a>
          	</div>
        </form>
        </div>
	</div>
@endsection
@section('inject-footer')
	<script type="text/javascript">
    $(function() {
    	reCounter();
    
    	$("#scripts").on("change", function() {
        	var command = $(this).val();
        	$("#command").val(command);
        });
    
    	
    	$("#add-button").on("click", function() {
        	var selected = $("select[name=ws_haystack] option:selected");
        	selected.each(function() {
            	var ws = $(this).clone();
            	$("select[name=ws_selected]").append(ws);
        		$(this).remove();
            });
        	reOrder($("select[name=ws_selected]"));
        	reCounter();
        });
    
    	$("#remove-button").on("click", function() {
        	var selected = $("select[name=ws_selected] option:selected");
        	selected.each(function() {
            	var ws = $(this).clone();
            	$("select[name=ws_haystack]").append(ws);
        		$(this).remove();
            });
        	reOrder($("select[name=ws_haystack]"));
        	reCounter();
        });
    
    	function reOrder(element) {
        	var sel = element;
			var selected = sel.val(); // cache selected value, before reordering
			var opts_list = sel.find('option');
			opts_list.sort(function(a, b) { return $(a).text().localeCompare(b.text); });
			sel.html('').append(opts_list);
			sel.val(selected); // set cached selected value
        }
    
    	function reCounter() {
        	var selected = $("select[name=ws_selected] > option").length;
        	var haystack = $("select[name=ws_haystack] > option").length;
        	$(".ws_selected_counter").text(selected);
        	$(".ws_haystack_counter").text(haystack);
        }
    
    	$("#save-command-button").on("click", function() {
        	
        	$("#status").html("");
    
    		var payLoad = {};
            var workstations = [];
			$('select[name=ws_selected] > option').each(function(i, e) {
    			workstations.push($(this).val());
            });
            
    		payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
    		payLoad['action'] = "saveCommand";
    		payLoad['command_id'] = $('input[name=command_id]').val();
            payLoad['alias'] = $("select[name=scripts] > option:selected").text();
    		payLoad['command'] = $('textarea[name=command]').val();
        	payLoad['description'] = $('textarea[name=description]').val();
        	payLoad['workstations'] = workstations.join();
    		payLoad['date'] = $("input[name=target_date").val();
    		payLoad['time'] = $("input[name=target_time").val();
    		var saveCommand = $.post("{{ url('commands/payload') }}", payLoad, "JSONP");
        	saveCommand.done(function(data) {
            	console.log(data);
               	if (data.status == "error") {
                	$("#status").append("<div class='alert alert-danger alert-dismissible fade show'><button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span></button></div>");
                	for(i = 0; i < data.errors.length; i++) {
                    	$("#status .alert").append(data.errors[i] + "<br>");
                	}
                }
            	if (data.status == "ok") {
                	$("#status").append("<div class='alert alert-success alert-dismissible fade show'><button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span></button></div>");
                	$("#status .alert").append("{{ __('all.command_center.success') }}");
                    $("#command_id").val(data.command_id);
                }
            });
        
        });
    
    });
    
	</script>                              
@endsection