@extends ('layout')
	@section('title')
	{{ __('all.workstations.create_filter') }} | BigLan
	@endsection
	@section('content')
		<div class="row mt-2">
			<div class="col-12">
				<h4>{{ __('all.workstations.create_filter') }}</h4>
            	<form class="form-horizontal row" method="POST" action="{{ url('workstations/savefilter') }}">
				
					{{ csrf_field() }}
					<div class="col-12 mt-2 mb-2">
                    		<button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> {{ __('all.button.save') }}</button>
                    		<a href="{{ url('workstations') }}" class="btn btn-light btn-sm"><i class="fas fa-arrow-circle-up"></i> {{ __('all.button.back_without_save') }}</a>
                    </div>
					<div class="col-12">
                    	<div class="form-group align-items-center">
                    		<div class="input-group">
        						<div class="input-group-prepend">
          							<div class="input-group-text">{{ __('all.workstations.filter_name') }}:</div>
        						</div>
                    			<div class="input-group-append">
          							<input type="text" name="filter_name" class="form-control" value="{{ old('filter_name') }}">
                    				
                    			</div>
                    		</div>
                    		@if($errors->has('filter_name'))
        						<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('filter_name') }}</div>
    						@endif
                    	</div>
                    </div>
                    <div class="col-12">
                    	<div class="form-group align-items-center">
                    		<div class="input-group">
        						<div class="input-group-prepend">
          							<div class="input-group-text">{{ __('all.workstations.filter_short_description') }}:</div>
        						</div>
                    			<div class="input-group-append">
          							<input type="text" size="70" name="filter_short_description" class="form-control" value="{{ old('filter_short_description') }}">
                    			</div>
                    		</div>
                    		@if($errors->has('filter_short_description'))
        						<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('filter_short_description') }}</div>
    						@endif
                    	</div>
                    </div>
                    <div class="col-12">
                    	<div class="form-group align-items-center">
                    		<div class="input-group">
        						<div class="input-group-prepend">
          							<div class="input-group-text">{{ __('all.workstations.filter_brand_model') }}</div>
        						</div>
                    			<div class="input-group-append">
          							<select class="form-control" name="brand_modificator">
                    					<option value="contains" @if(old('brand_modificator') === 'contains') selected @endif>{{ __('all.workstations.filter_contains') }}</option>
                    					<option value="not-contains" @if(old('brand_modificator') === 'not-contains') selected @endif>{{ __('all.workstations.filter_not_contains') }}</option>
                    					<option value="exactly" @if(old('brand_modificator') === 'exactly') selected @endif>{{ __('all.workstations.filter_exactly') }}</option>
                    				</select>
                    			</div>
                    			<div class="input-group-append">
          							<input type="text" name="brand_value"  value="{{ old('brand_value') }}" class="form-control">
                    			</div>
                    		</div>
                    	</div>
                    </div>
                    <div class="col-12">
                    	<div class="form-group align-items-center">
                    		<div class="input-group">
        						<div class="input-group-prepend">
          							<div class="input-group-text">{{ __('all.workstations.filter_hostname') }}</div>
        						</div>
                    			<div class="input-group-append">
          							<select class="form-control" name="hostname_modificator">
                    					<option value="contains" @if(old('hostname_modificator') === 'contains') selected @endif>{{ __('all.workstations.filter_contains') }}</option>
                    					<option value="not-contains" @if(old('hostname_modificator') === 'not-contains') selected @endif>{{ __('all.workstations.filter_not_contains') }}</option>
                    					<option value="exactly" @if(old('hostname_modificator') === 'exactly') selected @endif>{{ __('all.workstations.filter_exactly') }}</option>
                    				</select>
                    			</div>
                    			<div class="input-group-append">
          							<input type="text" name="hostname_value" value="{{ old('hostname_value') }}" class="form-control">
                    			</div>
                    		</div>
                    	</div>
                    </div>
                    <div class="col-12">
                    	<div class="form-group align-items-center">
                    		<div class="input-group">
        						<div class="input-group-prepend">
          							<div class="input-group-text">{{ __('all.workstations.filter_workgroup') }}</div>
        						</div>
                    			<div class="input-group-append">
          							<select class="form-control" name="workgroup_modificator">
                    					<option value="contains" @if(old('workgroup_modificator') === 'contains') selected @endif>{{ __('all.workstations.filter_contains') }}</option>
                    					<option value="not-contains" @if(old('workgroup_modificator') === 'not-contains') selected @endif>{{ __('all.workstations.filter_not_contains') }}</option>
                    					<option value="exactly" @if(old('workgroup_modificator') === 'exactly') selected @endif>{{ __('all.workstations.filter_exactly') }}</option>
                    				</select>
                    			</div>
                    			<div class="input-group-append">
          							<input type="text" name="workgroup_value" value="{{ old('workgroup_value') }}" class="form-control">
                    			</div>
                    		</div>
                    	</div>
                    </div>
                    <div class="col-12">
                    	<div class="form-group">
                    		<div class="input-group">
        						<div class="input-group-prepend">
          							<div class="input-group-text">{{ __('all.workstations.filter_cpu_score') }}</div>
        						</div>
                    			<div class="input-group-append">
          							<select class="form-control" name="cpuscore_modificator">
                    					<option value="more" @if(old('cpuscore_modificator') === 'more') selected @endif>{{ __('all.workstations.filter_more_than') }}</option>
                    					<option value="less" @if(old('cpuscore_modificator') === 'less') selected @endif>{{ __('all.workstations.filter_less_than') }}</option>
                    				</select>
                    			</div>
                    			<div class="input-group-append">
          							<input type="text" name="cpuscore_value" value="{{ old('cpuscore_value') }}" class="form-control">
                    			</div>
                    		</div>
                    		@if($errors->has('cpuscore_value'))
        						<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('cpuscore_value') }}</div>
    						@endif
                    	</div>
                    	
                    </div>
                    <div class="col-12">
                    	<div class="form-group align-items-center">
                    		<div class="input-group">
        						<div class="input-group-prepend">
          							<div class="input-group-text">{{ __('all.workstations.filter_cpu_age') }}</div>
        						</div>
                    			<div class="input-group-append">
          							<select class="form-control" name="cpuage_modificator">
                    					<option value="more" @if(old('cpuage_modificator') === 'more') selected @endif>{{ __('all.workstations.filter_more_than') }}</option>
                    					<option value="less" @if(old('cpuage_modificator') === 'less') selected @endif>{{ __('all.workstations.filter_less_than') }}</option>
                                    	<option value="exactly" @if(old('cpuage_modificator') === 'exactly') selected @endif>{{ __('all.workstations.filter_exactly') }}</option>
                                   </select>
                    			</div>
                    			<div class="input-group-prepend">
          							<input type="text" name="cpuage_value" value="{{ old('cpuage_value') }}" class="form-control">
                    			</div>
                    			<div class="input-group-append">
          							<div class="input-group-text">{{ __('all.workstations.filter_years') }}</div>
        						</div>
                    		</div>
                    		@if($errors->has('cpuage_value'))
        						<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('cpuage_value') }}</div>
    						@endif
                    	</div>
                    </div>
                    <div class="col-12">
                    	<div class="form-group align-items-center">
                    		<div class="input-group">
        						<div class="input-group-prepend">
          							<div class="input-group-text">{{ __('all.workstations.filter_memory') }}</div>
        						</div>
                    			<div class="input-group-append">
          							<select class="form-control" name="memory_modificator">
                    					<option value="more" @if(old('memory_modificator') === 'more') selected @endif>{{ __('all.workstations.filter_more_than') }}</option>
                    					<option value="less" @if(old('memory_modificator') === 'less') selected @endif>{{ __('all.workstations.filter_less_than') }}</option>
                    					<option value="exactly" @if(old('memory_modificator') === 'exactly') selected @endif>{{ __('all.workstations.filter_exactly') }}</option>
                    				</select>
                    			</div>
                    			<div class="input-group-prepend">
          							<input type="text" name="memory_value" value="{{ old('memory_value') }}" class="form-control">
                    			</div>
                    			<div class="input-group-append">
          							<div class="input-group-text">GB</div>
        						</div>
                    		</div>
                    		@if($errors->has('memory_value'))
        						<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('memory_value') }}</div>
    						@endif
                    	</div>
                    </div>
                    <div class="col-12">
                    	<div class="form-group align-items-center">
                    		<div class="input-group">
        						<div class="input-group-prepend">
          							<div class="input-group-text">{{ __('all.workstations.filter_os_drive_free_space') }}</div>
        						</div>
                    			<div class="input-group-append">
          							<div class="input-group-text">{{ __('all.workstations.filter_less_or_equal') }}</div>
        						</div>
                    			<div class="input-group-prepend">
          							<input type="text" name="os_drive_free_space" value="{{ old('os_drive_free_space') }}" class="form-control">
                    			</div>
                    			<div class="input-group-append">
          							<div class="input-group-text">GB</div>
        						</div>
                    		</div>
                    		@if($errors->has('os_drive_free_space'))
        						<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('os_drive_free_space') }}</div>
    						@endif
                    	</div>
                    </div>
                    <div class="col-12">
                    	<div class="form-group align-items-center">
                    		<div class="input-group">
        						<div class="input-group-prepend">
          							<div class="input-group-text">{{ __('all.workstations.filter_os_name') }}: </div>
        						</div>
                    			<div class="input-group-append">
          							<input type="text" name="osname_value"  value="{{ old('osname_value') }}" class="form-control">
                    			</div>
                    		</div>
                    	</div>
                    </div>
                    <div class="col-12">
                    	<div class="form-group align-items-center">
                    		<div class="input-group">
        						<div class="input-group-prepend">
          							<div class="input-group-text">{{ __('all.workstations.filter_os_update') }}</div>
        						</div>
                    			<div class="input-group-append">
          							<input type="text" name="osupdate_value"  value="{{ old('osupdate_value') }}" class="form-control">
                    			</div>
                    			<div class="input-group-append">
          							<div class="input-group-text">{{ __('all.workstations.filter_months') }}</div>
        						</div>
                    		</div>
                    		@if($errors->has('osupdate_value'))
        						<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('osupdate_value') }}</div>
    						@endif
                    	</div>
                    </div>
                    <div class="col-12">
                    	<div class="form-group align-items-center">
                    		<div>{{ __('all.workstations.filter_disk') }}</div>
        					<div class="form-check form-check-inline">
  								<input class="form-check-input" type="checkbox" id="ssd-checkbox" name="disk[]" value="ssd">
  								<label class="form-check-label" for="ssd-checkbox">SSD</label>
							</div>
                            
							<div class="form-check form-check-inline">
  								<input class="form-check-input" type="checkbox" id="hdd-checkbox" name="disk[]" value="hdd">
  								<label class="form-check-label" for="hdd-checkbox">HDD</label>
							</div>
							<div class="form-check form-check-inline">
  								<input class="form-check-input" type="checkbox" id="other-checkbox" name="disk[]" value="unspecified">
  								<label class="form-check-label" for="other-checkbox">{{ __('all.workstations.filter_unspecified') }}</label>
							</div>
                    	</div>
                    </div>
                   	<div class="col-12">
                    	<div class="form-group align-items-center">
                    		<div class="">{{ __('all.workstations.filter_hw_type') }}</div>
        					<div class="form-check form-check-inline">
  								<input class="form-check-input" type="checkbox" id="desktop-checkbox" name="type[]" value="desktop">
  								<label class="form-check-label" for="desktop-checkbox">{{ __('all.workstations.filter_desktop') }}</label>
							</div>
							<div class="form-check form-check-inline">
  								<input class="form-check-input" type="checkbox" id="laptop-checkbox" name="type[]" value="laptop">
  								<label class="form-check-label" for="laptop-checkbox">{{ __('all.workstations.filter_laptop') }}</label>
							</div>
							<div class="form-check form-check-inline">
  								<input class="form-check-input" type="checkbox" id="server-checkbox" name="type[]" value="server">
  								<label class="form-check-label" for="server-checkbox">{{ __('all.workstations.filter_server') }}</label>
							</div>
                    	</div>
                    </div>
                    <div class="col-12">
                    	<div class="form-group align-items-center">
                    		<div class="input-group">
        						<div class="input-group-prepend">
          							<select class="form-control" name="label_modificator">
                    					<option value="contains" @if(old('label_modificator') === 'contains') selected @endif>{{ __('all.workstations.filter_contains') }}</option>
                    					<option value="not-contains" @if(old('label_modificator') === 'not-contains') selected @endif>{{ __('all.workstations.filter_not_contains') }}</option>
                    				</select> 
                    			</div>
                                <div class="input-group-prepend">
          							<select class="form-control" name="label_connection">
                    					<option value="or" @if(old('label_connection') === 'or') selected @endif>{{ __('all.workstations.filter_any_of_them') }}</option>
                    					<option value="and"@if(old('label_connection') === 'and') selected @endif>{{ __('all.workstations.filter_all_of_them') }}</option>
                    				</select> 
                    			</div>
                                <div class="input-group-prepend">
          							<div class="input-group-text">{{ __('all.workstations.filter_labels') }}</div>
        						</div>
                    			<div class="input-group-append">
          							<input type="text" name="label_value"  value="{{ old('label_value') }}" class="form-control">
                    			</div>
                    		</div>
                    	</div>
                    </div>
                    <div class="col-12">
                    	<div class="form-group align-items-center">
                    		<div class="form-check form-check-inline">
  								<input class="form-check-input" type="checkbox" id="ipv6-checkbox" name="ipv6" value="has-ipv6" @if(old('ipv6')) checked @endif>
  								<label class="form-check-label" for="ipv6-checkbox">{{ __('all.workstations.filter_has_ipv6') }}</label>
							</div>
						</div>
                    </div>
                    <div class="col-12">
                    	<div class="form-group align-items-center">
                    		<div class="form-check form-check-inline">
  								<input class="form-check-input" type="checkbox" id="missing-serial-checkbox" name="serial" value="no-serial" @if(old('serial')) checked @endif>
  								<label class="form-check-label" for="missing-serial-checkbox">{{ __('all.workstations.filter_serial_missing') }}</label>
							</div>
						</div>
                    </div>
                    <div class="col-12">
                    	<div class="form-group align-items-center">
                    		<div class="form-check form-check-inline">
  								<input class="form-check-input" type="checkbox" id="missing-inventory-checkbox" name="inventory" value="no-inventory" @if(old('inventory')) checked @endif>
  								<label class="form-check-label" for="missing-inventory-checkbox">{{ __('all.workstations.filter_inventory_missing') }}</label>
							</div>
                    	</div>
                    </div>
                    <div class="col-12">
                    	<div class="form-group align-items-center">
                    		<div class="form-check form-check-inline">
  								<input class="form-check-input" type="checkbox" id="adminuser-checkbox" name="admin_account" value="user-is-admin" @if(old('admin_account')) checked @endif>
  								<label class="form-check-label" for="adminuser-checkbox">{{ __('all.workstations.filter_has_admin_account') }}</label>
							</div>
                    	</div>
                    </div>
                    <div class="col-12">
                    	<div class="form-group align-items-center">
                    		<div class="form-check form-check-inline">
  								<input class="form-check-input" type="checkbox" id="support-checkbox"  name="support" value="no-support" @if(old('support')) checked @endif>
  								<label class="form-check-label" for="support-checkbox">{{ __('all.workstations.filter_support_ended') }}</label>
							</div>
                    	</div>
                    </div>
                    <div class="col-12">
                    	<div class="form-group align-items-center">
                    		<div class="input-group">
        						<div class="input-group-prepend">
          							<div class="input-group-text">{{ __('all.workstations.filter_offline_since') }} </div>
        						</div>
                    			<div class="input-group-append">
          							<input type="text" name="offline_value"  value="{{ old('offline_value') }}" class="form-control">
                    			</div>
                    			<div class="input-group-append">
          							<div class="input-group-text">{{ __('all.workstations.filter_days') }}</div>
        						</div>
                    		</div>
                            @if($errors->has('offline_value'))
        						<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('offline_value') }}</div>
    						@endif
                    	</div>
                    </div>
                   	<div class="col-12">
                    	<div class="form-group align-items-center">
                    		<div class="input-group">
        						<div class="input-group-prepend">
          							<div class="input-group-text">{{ __('all.workstations.filter_boottime_more_than') }} </div>
        						</div>
                    			<div class="input-group-append">
          							<input type="text" name="boottime_value"  value="{{ old('boottime_value') }}" class="form-control">
                    			</div>
                    			<div class="input-group-append">
          							<div class="input-group-text">{{ __('all.workstations.filter_seconds') }}</div>
        						</div>
                    		</div>
                            @if($errors->has('boottime_value'))
        						<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('boottime_value') }}</div>
    						@endif
                    	</div>
                    </div>
                   	<div class="col-12">
                    	<div class="form-group align-items-center">
                    		<div class="input-group">
        						<div class="input-group-prepend">
          							<div class="input-group-text">{{ __('all.workstations.filter_uptime_more_than') }} </div>
        						</div>
                    			<div class="input-group-append">
          							<input type="text" name="uptime_value"  value="{{ old('uptime_value') }}" class="form-control">
                    			</div>
                    			<div class="input-group-append">
          							<div class="input-group-text">{{ __('all.workstations.filter_days') }}</div>
        						</div>
                    		</div>
                            @if($errors->has('uptime_value'))
        						<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('uptime_value') }}</div>
    						@endif
                    	</div>
                    </div>
            	</form>        
			</div>
		</div>
	@endsection
    @section('inject-footer')
                    <style>
  </style>
    <script type="text/javascript">
    $(function() {
    	
    	        
	});                                
    </script>                               
    @endsection