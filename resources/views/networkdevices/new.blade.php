@extends ('layout')
	@section('title')
		{{ __('all.network_devices.new_network_device') }} | BigLan
	@endsection
	@section('content')
	<div class="row mt-2">
	<div class="col-12">
		<h4>{{ __('all.network_devices.new_network_device') }}</h4>
		<form method="POST" class="form-horizontal" action="{{ url('networkdevices/save') }}">
			<div class="row">
				<div class="col-12 mb-2 mt-2">
        			<button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> {{ __('all.button.save') }}</button>&nbsp;&nbsp;<a href="{{ url('/networkdevices') }}" class="btn btn-light btn-sm"><i class="fas fa-arrow-circle-up"></i>{{ __('all.button.back_without_save') }}</a>
            	</div>
			</div>
        	{{ csrf_field() }}
			<div class="form-group row">
				<div class="col-2">
					<span class="control-label">{{ __('all.network_devices.name') }}</span>
				</div>
				<div class="col-2">
					<input type="text" name="alias" class="form-control" required>
				</div>
			</div>
        	<div class="form-group row">
				<div class="col-2">
					<span class="control-label">{{ __('all.network_devices.brand_model') }}</span>
				</div>
				<div class="col-2">
					<input type="text" name="hardware" class="form-control">
				</div>
			</div>
        	<div class="form-group row">
				<div class="col-2">
					<span class="control-label">{{ __('all.network_devices.serial') }}</span>
				</div>
				<div class="col-2">
					<input type="text" name="serial" class="form-control" required>
				</div>
			</div>
        	<div class="form-group row">
				<div class="col-2">
					<span class="control-label">{{ __('all.network_devices.ip_address') }}</span>
				</div>
				<div class="col-2">
					<input type="text" name="ip" class="form-control">
				</div>
			</div>
        	<div class="form-group row">
				<div class="col-2">
					<span class="control-label">{{ __('all.network_devices.mac_address') }}</span>
				</div>
				<div class="col-2">
					<input type="text" name="mac" class="form-control">
				</div>
			</div>
        	<div class="form-group row">
				<div class="col-2">
					<span class="control-label">{{ __('all.network_devices.type') }}</span>
				</div>
				<div class="col-2">
					<select name="type" class="form-control">
                    	<option value="switch">Switch</option>
                    	<option value="modem">Modem</option>
                    	<option value="router">Router</option>
                    </select>
                </div>
			</div>
        	<div class="form-group row">
				<div class="col-2">
					<span class="control-label">{{ __('all.network_devices.ports') }}</span>
				</div>
				<div class="col-2">
					<input type="number" name="ports" class="form-control" required>
				</div>
			</div>
        </form>
	</div>
	</div>
	@endsection