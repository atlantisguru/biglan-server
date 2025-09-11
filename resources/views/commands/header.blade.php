<div class="row mb-2">
	<div class="col-12">
		<a href="{{ url('/commands') }}" class="mr-4 text-decoration-none text-dark">{{ __('all.command_center.command_center') }}</a>
		@if(auth()->user()->hasPermission('write-batch-command'))
    		<a href="{{ url('/commands/new') }}" class="btn btn-primary btn-sm mr-2"><i class="fas fa-plus"></i> {{ __('all.command_center.new_command') }}</a>
		@endif
		@if(auth()->user()->hasPermission('read-script'))
    		<a href="{{ url('/commands/scripts') }}" class="btn btn-sm btn-outline-secondary mr-2"><i class="fas fa-terminal"></i> {{ __('all.command_center.script_storage') }}</a>
    	@endif
	</div>
</div>