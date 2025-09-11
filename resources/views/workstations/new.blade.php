@extends ('layout')

@section('title')
	{{ __('all.button.new_workstation') }} | BigLan
@endsection

@section('content')
	<div class="row mt-2">
		<div class="col-12">
			<h4>{{ __('all.button.new_workstation') }}</h4>
			<form class="row" method="POST" action="{{ url('workstations/save') }}">
				
				{{ csrf_field() }}
					
				<div class="col-12 mt-2 mb-2">
               		<button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> {{ __('all.button.save') }}</button>
               		<a href="{{ url('workstations') }}" class="btn btn-light btn-sm"><i class="fas fa-arrow-circle-up"></i> {{ __('all.button.back_without_save') }}</a>
                </div>
                
                <div class="col-lg-3 col-md-6 col-12">
                   	<fieldset class="form-group border p-3">
                   		<legend class="w-auto px-2 h5">{{ __('all.workstations.identification_details') }}</legend>
                    	
                		<div class="form-group">
                           	<label for="product-serial">{{ __('all.workstations.product_serial') }}<br><small class="text-muted"><i class="fas fa-info-circle"></i> {{ __('all.workstations.product_serial_help') }}</small></label>
                            <input type="text" name="product_serial" id="product-serial" class="form-control" value="{{ old('product_serial') }}">
                        	@if($errors->has('product_serial'))
        						<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('product_serial') }}</div>
    						@endif
                		</div>
                    	<div class="form-group">
                           	<label for="mboard-serial">{{ __('all.workstations.motherboard_serial') }}<br><small class="text-muted"><i class="fas fa-info-circle"></i> {{ __('all.workstations.motherboard_serial_help') }}</small></label>
                            <input type="text" name="mboard_serial" id="mboard-serial" class="form-control" value="{{ old('mboard_serial') }}">
                        	@if($errors->has('mboard_serial'))
        						<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('mboard_serial') }}</div>
    						@endif
                		</div>
                    	<div class="form-group">
                           	<label for="uuid">UUID<br><small class="text-muted"><i class="fas fa-info-circle"></i> Universally Unique Identifier</small></label>
                           	<input type="text" name="uuid" id="uuid" class="form-control" placeholder="XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX" value="{{ old('uuid') }}">
                        	@if($errors->has('uuid'))
        						<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('uuid') }}</div>
    						@endif
                		</div>
                    	<div class="form-group">
                           	<label for="first-mac">{{ __('all.workstations.first_mac_address') }}<br><small class="text-muted"><i class="fas fa-info-circle"></i> {{ __('all.workstations.first_mac_address_help') }}</small></label>
                           	<input type="text" name="first_mac" id="first-mac" class="form-control" placeholder="XXXXXXXXXXXX" value="{{ old('first_mac') }}">
                        	@if($errors->has('first_mac'))
        						<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('first_mac') }}</div>
    						@endif
                		</div>
                        <div class="form-group">
                    		<label>{{ __('all.workstations.identification_chance') }}</label>
  							<div class="progress">
                               	<div id="identification" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">0%</div>
							</div>
                        </div>    
                   	</fieldset>
           		</div>

                <div class="col-lg-3 col-md-6 col-12">
                	<fieldset class="form-group border p-3">
                   		<legend class="w-auto px-2 h5">{{ __('all.workstations.general') }}</legend>
                    	<div class="form-group">
                    		<label>{{ __('all.workstations.alias') }}</label>
                    		<input type="text" name="alias" class="form-control" value="{{ old('alias') }}">
                            @if($errors->has('alias'))
        						<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('alias') }}</div>
    						@endif
                    	</div>
                    	<div class="form-group">
                    		<label>{{ __('all.workstations.hostname') }}</label>
                    		<input type="text" name="hostname" class="form-control" value="{{ old('hostname') }}">
                    	</div>
                    	<div class="form-group">
                    		<label>{{ __('all.workstations.workgroup') }}</label>
                    		<input type="text" name="workgroup" class="form-control" value="{{ old('workgroup') }}">
                    	</div>
                    	<div class="form-group">
                    		<label>{{ __('all.workstations.operating_system') }}</label>
                    		<input type="text" name="os" class="form-control" placeholder="{{ __('all.workstations.start_typing') }}" value="{{ old('os') }}">
                        	@if($errors->has('os'))
        						<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('os') }}</div>
    						@endif
                        </div>
                    	<div class="form-group">
                    		<label>{{ __('all.workstations.brand_model') }}</label>
                    		<input type="text" name="hardware" class="form-control" placeholder="{{ __('all.workstations.start_typing') }}" value="{{ old('hardware') }}">
                    	</div>
                   		<div class="form-group">
                    		<label>{{ __('all.workstations.type') }}</label>
                    		<select name="type" class="form-control">
                    			<option value="desktop" @if(old('type') === 'desktop') selected @endif>{{ __('all.workstations.desktop') }}</option>
                    			<option value="laptop" @if(old('type') === 'laptop') selected @endif>{{ __('all.workstations.laptop') }}</option>
                    			<option value="server" @if(old('type') === 'server') selected @endif>{{ __('all.workstations.server') }}</option>
                    		</select>
                    	</div>
                   	</fieldset>
             	</div>
                
                <div class="col-lg-3 col-md-6 col-12">
                	<fieldset class="form-group border p-3">
                    	<legend class="w-auto px-2 h5">{{ __('all.workstations.technical_details') }}</legend>
                            
                    	<div class="form-group">
                    		<label>{{ __('all.workstations.processor') }}</label>
                    		<input type="text" name="cpu" class="form-control" placeholder="{{ __('all.workstations.start_typing') }}" value="{{ old('cpu') }}">
                    		@if($errors->has('cpu'))
        						<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('cpu') }}</div>
    						@endif
                        </div>
                    	<div class="form-group">
                    		<label>{{ __('all.workstations.processor_released') }}</label>
                    		<input type="text" name="cpu_release_date" placeholder="{{ __('all.workstations.yyyymmdd') }}" class="form-control" value="{{ old('cpu_release_date') }}">
                           	@if($errors->has('cpu_release_date'))
        						<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('cpu_release_date') }}</div>
    						@endif
                    	</div>
                    	<div class="form-group">
                    		<label>{{ __('all.workstations.memory') }}</label>
                    		<div class="input-group">
                    			<input type="number" name="ram" class="form-control" value="{{ old('ram') }}">
                    			<div class="input-group-append">
                    				<span class="input-group-text">MiB</span>
                    			</div>
                           </div>
                    	 	@if($errors->has('ram'))
        						<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('ram') }}</div>
    						@endif
                    	</div>
                    	<div class="form-group">
                    		<label>{{ __('all.workstations.memory_slots') }}</label>
                    		<div class="input-group">
                    			<input type="number" name="ram_slots" class="form-control" value="{{ old('ram_slots') }}">
                    			<div class="input-group-append">
                    				<span class="input-group-text">db</span>
                    			</div>
                            	@if($errors->has('memory_slots'))
        							<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('memory_slots') }}</div>
    							@endif
                    		</div>
                    	</div>
                    	<div class="form-group">
                    		<label>{{ __('all.workstations.memory_max') }}</label>
                    		<div class="input-group">
                    			<input type="number" name="ram_max_capacity" class="form-control" value="{{ old('ram_max_capacity') }}">
                    			<div class="input-group-append">
                    				<span class="input-group-text">GiB</span>
                    			</div>
                            	@if($errors->has('memory_max'))
        							<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('memory_max') }}</div>
    							@endif
                    		</div>
                    	</div>
                   	</fieldset>
              	</div>
                            
                <div class="col-lg-3 col-md-6 col-12">
                	<fieldset class="form-group border p-3">
                    	<legend class="w-auto px-2 h5">{{ __('all.workstations.network_details') }}</legend>
                    	
                        <div class="form-group">
                    		<label>{{ __('all.workstations.ip_address') }}</label>
                    		<input type="text" name="ip[]" class="form-control" placeholder="000.000.000.000" pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$" value="{{ isset(old('ip')[0]) ? old('ip')[0] : '' }}">
                            @if($errors->has('ip.0'))
        						<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('ip.0') }}</div>
    						@endif
                    	</div>
                    	<div class="form-group">
                    		<label>{{ __('all.workstations.ip_address') }}</label>
                    		<input type="text" name="ip[]" class="form-control" placeholder="000.000.000.000" pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$" value="{{ isset(old('ip')[0]) ? old('ip')[1] : '' }}">
                    		@if($errors->has('ip.1'))
        						<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('ip.1') }}</div>
    						@endif
                        </div>
                    	<div class="form-group">
                    		<label>{{ __('all.workstations.ip_address') }}</label>
                    		<input type="text" name="ip[]" class="form-control" placeholder="000.000.000.000" pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$" value="{{ isset(old('ip')[2]) ? old('ip')[2] : '' }}">
                    		@if($errors->has('ip.2'))
        						<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('ip.2') }}</div>
    						@endif
                        </div>
                    	<div class="form-group">
                    		<label>{{ __('all.workstations.dns_address') }}</label>
                    		<input type="text" name="dns[]" class="form-control" placeholder="000.000.000.000" pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$" value="{{ isset(old('dns')[0]) ? old('dns')[0] : '' }}">
                    		@if($errors->has('dns.0'))
        						<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('dns.0') }}</div>
    						@endif
                        </div>
                    	<div class="form-group">
                    		<label>{{ __('all.workstations.dns_address') }}</label>
                    		<input type="text" name="dns[]" class="form-control" placeholder="000.000.000.000" pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$" value="{{ isset(old('dns')[1]) ? old('dns')[1] : '' }}">
                    		@if($errors->has('dns.1'))
        						<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('dns.1') }}</div>
    						@endif
                        </div>
                 	</fieldset>         
           		</div>
                            
                <div class="col-lg-3 col-md-6 col-12">
                	<fieldset class="form-group border p-3">
                    	<legend class="w-auto px-2 h5">{{ __('all.workstations.disks') }}</legend>        
                        
                       	<fieldset class="form-group border p-3 disk">
                    		<legend class="w-auto px-2 h6">{{ __('all.workstations.disk') }}</legend>
                            
                            <div class="form-group">    
                    			<label>{{ __('all.workstations.serial') }}</label>
                    			<input type="text" name="disk_serial[]" class="form-control" value="{{ isset(old('disk_serial')[0]) ? old('disk_serial')[0] : '' }}">
                    		</div>
                            <div class="form-group">    
                    			<label>{{ __('all.workstations.disk_type') }}</label>
                    			<select name="disk_type[]" class="form-control">
                                	<option value="unspecified" @if(old('disk_type')[0] ?? '' === 'unspecified') selected @endif>Unspecified</option>
                                	<option value="hdd" @if(old('disk_type')[0] ?? '' === 'hdd') selected @endif>HDD</option>
                                	<option value="ssd" @if(old('disk_type')[0] ?? '' === 'ssd') selected @endif>SSD</option>
                                </select>
                    		</div>
                            <div class="form-group">
                            	<label>{{ __('all.workstations.brand_model') }}</label>
                    			<input type="text" name="disk_model[]" class="form-control" value="{{ isset(old('disk_model')[0]) ? old('disk_model')[0] : '' }}">
                    		</div>
                            <div class="form-group">
                            	<label>{{ __('all.workstations.disk_capacity') }}</label>
                    			<div class="input-group">
                    				<input type="number" name="disk_capacity[]" class="form-control" value="{{ isset(old('disk_capacity')[0]) ? old('disk_capacity')[0] : '' }}">
                    				<div class="input-group-append">
                    					<span class="input-group-text">GiB</span>
                    				</div>
                    				@if($errors->has('disk_capacity.0'))
        								<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('disk_capacity.0') }}</div>
    								@endif
                        		</div>
                            </div>
                   		</fieldset>
                    	
                            <a href="javascript:void(0)" id="btn-new-disk" class="btn btn-primary"><i class="fas fa-plus"></i> {{ __('all.workstations.add_disk') }}</a>
               			
                 	</fieldset>
              	</div>           
                      
                <div class="col-lg-3 col-md-6 col-12">
                	<fieldset class="form-group border p-3">
                   		<legend class="w-auto px-2 h5">{{ __('all.workstations.monitors') }}</legend>         
                          		
                        <fieldset class="form-group border p-3 monitor">
                    		<legend class="w-auto px-2 h6">{{ __('all.workstations.monitor') }}</legend>      
                    			
                            <div class="form-group">
                            	<label>{{ __('all.workstations.manufacturer_code') }}</label>
                    			<input type="text" name="monitor_manufacturer[]" placeholder="{{ __('all.workstations.start_typing') }}" class="form-control" value="{{ isset(old('monitor_manufacturer')[0]) ? old('monitor_manufacturer')[0] : '' }}">
                    		</div>
                            <div class="form-group">
                            	<label>{{ __('all.workstations.brand_model') }}</label>
                    			<input type="text" name="monitor_name[]" class="form-control" value="{{ isset(old('monitor_name')[0]) ? old('monitor_name')[0] : '' }}">
                    		</div>
                            <div class="form-group">
                            	<label>{{ __('all.workstations.serial') }}</label>
                    			<input type="text" name="monitor_serial[]" class="form-control" value="{{ isset(old('monitor_serial')[0]) ? old('monitor_serial')[0] : '' }}">
                    		</div>
                     	</fieldset>
                                
                        <a href="javascript:void(0)" id="btn-new-monitor" class="btn btn-primary"><i class="fas fa-plus"></i> {{ __('all.workstations.add_monitor') }}</a>
                            
             		</fieldset>
    			</div>
          	</form>
      	</div>
	</div>
@endsection

@section('inject-footer')
    <link rel="stylesheet" type="text/css" href={{ url("css/jquery-ui.css") }}>
   	<style>
  		.ui-autocomplete {
    		max-height: 170px;
    		overflow-y: auto;
    		overflow-x: hidden;
  		}
  
		* html .ui-autocomplete {
    		height: 170px;
  		}
  	</style>
 	<script type="text/javascript" src={{ url("js/jquery-ui.min.js") }}></script>
 	<script type="text/javascript">
    
    $(function() {
    	
    
    	calculateIdentificationChance();
    
    	$("#product-serial, #mboard-serial, #uuid, #first-mac").on("keyup", function() { 
        
        	calculateIdentificationChance();
        
        });
    
    	function calculateIdentificationChance() {
        
        	var fields = 4;
        	var points = 0;
        
        	if ($("#product-serial").val().length > 0) {
            	points = points + 1;
            }
        
        	if ($("#mboard-serial").val().length > 0) {
            	points = points + 1;
            }
        
        	if ($("#uuid").val().length > 0) {
            	points = points + 1;
            }
        
        	if ($("#first-mac").val().length > 0) {
            	points = points + 1;
            }
        
        	var percentage = Math.floor((points/fields)*100);
        
        	$("#identification").css("width", percentage + "%").text(percentage + "%");
        	
        	$("#identification").removeClass("bg-danger bg-warning bg-success");
        
        	if (percentage <= 25) {
            	$("#identification").addClass("bg-danger");
            }
        
        	if (percentage > 25 && percentage <= 50) {
            	$("#identification").addClass("bg-warning");
            }
        
        	if (percentage > 50) {
            	$("#identification").addClass("bg-success");
            }
        
        
        }
    
    	$("#btn-new-disk").on("click", function() {
        
        	var diskClone = $("fieldset.disk:first").clone();
        
        	console.log("click");
        
        	diskClone.insertBefore("#btn-new-disk");
        
        });
    
    	$("#btn-new-monitor").on("click", function() {
        
        	var monitorClone = $("fieldset.monitor:first").clone();
        
        	monitorClone.insertBefore("#btn-new-monitor");
        
        });
    
    
    	$("input[name='os']").autocomplete({
  			source: {!! $os !!},
  			minLength: 1,
        	
  			select: function(event, ui) {
      			event.preventDefault();
      			$("input[name='os']").val(ui.item.label);
  			}
		});
    
    	$("input[name='workgroup']").autocomplete({
  			source: {!! $wg !!},
  			minLength: 1,
  			
  			select: function(event, ui) {
      			event.preventDefault();
      			$("input[name='workgroup']").val(ui.item.label);
  			}
		});
    
    	$("input[name='cpu']").autocomplete({
  			source: {!! $cpu !!},
  			minLength: 1,
  			
  			select: function(event, ui) {
      			event.preventDefault();
      			$("input[name='cpu']").val(ui.item.label);
            	$("input[name='cpu_release_date']").val(ui.item.release);
  			}
		});
    
    	$("input[name='hardware']").autocomplete({
  			source: {!! $hw !!},
  			minLength: 1,
  			
  			select: function(event, ui) {
      			event.preventDefault();
      			$("input[name='hardware']").val(ui.item.label);
  			}
		});
    
    	$("input[name='monitor_manufacturer[]']").autocomplete({
        	source: {!! $mon_man !!},
  			minLength: 1,
  			select: function(event, ui) {
      			event.preventDefault();
      			$(this).val(ui.item.label);
  			}
		});
    	
    	$("input[name='monitor_name[]']").autocomplete({
        	source: {!! $mon_name !!},
  			minLength: 1,
  			select: function(event, ui) {
      			event.preventDefault();
      			$(this).val(ui.item.label);
  			}
		});
    	        
	});

    </script>
    
@endsection