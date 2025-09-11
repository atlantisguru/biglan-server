@extends("layout")
@section("title")
{{ __('all.operating_systems.operating_systems') }} | BigLan
@endsection
@section("content")
	<div class="row mt-2">
		<div class="col-6">
			{{ csrf_field()}}
			<a href="javascript:" id="btn-scrape" class="btn btn-sm btn-primary"><i class="fas fa-search"></i> {{ __('all.button.collect_data') }}</a>&nbsp;<span class="badge badge-light"><i class="fas fa-info-circle"></i> {{ __('all.network_printers.helper') }}</span><br>
		</div>
	</div>
	<div class="row mt-2">
		<div class="col-lg-12 col-sm-12">
				@if(count($operatingSystems)!=0)
   				<div class="table-responsive">
				<table id="operatingsystems" class="table table-striped table-hover">
						<thead class="thead-dark">
							<tr>
								<th class="text-center"></th>
            					<th></th>
								<th>{{ __('all.operating_systems.name') }}</th>
								<th class="text-center">{{ __('all.operating_systems.release_date') }}</th>
								<th class="text-center">{{ __('all.operating_systems.end_of_support') }}</th>
            					<th class="text-center"></th>
            					<th class="text-center"></th>
            
							</tr>
						</thead>
						<tbody>
					
   				@foreach($operatingSystems as $os)
							<tr data-id="{{ $os->id }}" @if(isset($os->last_support_date)) @if($os->last_support_date <= $today) class="text-danger" @endif @endif>
								<td class="text-center">
            						@if(isset($os->last_support_date)) 
            							@if($os->last_support_date <= $today) 
            								<i class="fas fa-exclamation-circle text-danger" data-toggle="popover" data-html="true" data-trigger="hover" title="{{ __('all.operating_systems.warning') }}" data-content="{{ __('all.operating_systems.end_of_support_reached', ['os_name' => '<b>'.$os->name.'</b>', 'date' => '<b>'.$os->last_support_date.'</b>'] ) }}"></i> 
            							@endif
            						@else
            							<i class="fas fa-exclamation-triangle text-warning" data-toggle="popover" data-html="true" data-trigger="hover" title="{{ __('all.operating_systems.warning') }}" data-content="{{ __('all.operating_systems.end_of_support_needed') }}"></i>
            						@endif
            					</td>
								<td class="text-right">
            						@if(str_contains($os->name, "Ubuntu") === true ) <i class="fab fa-ubuntu"></i> @endif
            						@if(str_contains($os->name, "Windows") === true ) <i class="fab fa-windows"></i> @endif
            						@if(str_contains($os->name, "Linux") === true ) <i class="fab fa-linux"></i> @endif
            					</td>
								<td><a href="{{ url('workstations/filter/keyword/'. $os->name) }}" target="_blank">{{ $os->name }}</a></td>
								<td class="editable text-center" data-field="release_date">{{ $os->release_date ?? "" }}</td>
								<td class="editable text-center" data-field="last_support_date">{{ $os->last_support_date ?? "" }}</td>
            					<td class="text-right">{{ $os->counter ?? "" }}</td>
            					<td class="text-right">{{ round((($os->counter/$all)*100), 1) }}%</td>
            				</tr>
        		@endforeach
						</tbody>
					</table>
        	</div>
			
            @else
        		<p>{{ __('all.operating_systems.not_found_os') }}</p>
        	@endif
			</div>
	</div>

@endsection
@section('inject-footer')
    <style>
    	.table td {
            padding: 0.2rem;
        }

    </style>                            	
 	<script type="text/javascript">
    $(function() {
    	
    	$('[data-toggle="popover"]').popover();
    
    	var editValue = "";
        var payLoad = {};
    
    	$("body").on("click", "#btn-scrape", function(e) {
        	var element = $(this);
        
        	element.addClass("disabled");
        
        	var posting = $.post("{{ url('operatingsystems/payload') }}", { '_token': $('meta[name=csrf-token]').attr('content'), action: "scrapeOperatingSystems" }, "JSONP");
        	posting.done(function(data) {
        		if (data == "OK") {
                	location.reload();
                }
            });
        });
    
    	$(document).on('mouseover', 'table#operatingsystems .editable', function() {
                	$(this).css("cursor","cell");
                });
            
            	$(document).on('mouseleave', 'table#operatingsystems .editable', function() {
                	$(this).css("cursor","default");
                });
            	
            	function saveData(osid, osfield, osvalue) { 
            		payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            		payLoad['id'] = osid;
            		payLoad['action'] = 'editOperatingSystem';
                	payLoad['field'] = osfield;
            		payLoad['value'] = osvalue;
                	var editOperatingSystem = $.post("{{ url('operatingsystems/payload') }}", payLoad, "JSONP");
        			editOperatingSystem.done(function(data) {
            			if(data == "OK") {
                			var saveValue = $('.editing').val();
                        	$('.editing').parents("td").text(saveValue);
                		} else {
            				$('.editing').parents("td").text(editValue);
                		}
            		});
                }
            
            	$(document).on("dblclick", 'table#operatingsystems .editable', function() {
                	$(this).removeClass("editable");
                	editValue = $(this).text();
                	var field = $(this).attr("data-field");
                	if (field == "release_date" || field == "last_support_date") {
                    	$(this).html("<input class='form-control editing' type='date' />");
                    } else {
                   		$(this).html("<input class='form-control editing' type='text' />");
                    }
                	$('.editing').val(editValue).focus();
                });
            	
            	$(document).on('keydown', '.editing', function(e) {
                	if(e.which === 13 && e.shiftKey) {
                    	$(this).parents("td").addClass("editable");
                    	var id = $('.editing').parents("tr").attr("data-id");
                    	var field = $('.editing').parents("td").attr("data-field");
                    	var value = $('.editing').val();
                    	saveData(id, field, value);
                    }
                	
                	if(e.which === 27) {
                	   	$('.editing').parents("td").text(editValue).addClass("editable");
                	}
                });
    
	});
</script>                               
@endsection