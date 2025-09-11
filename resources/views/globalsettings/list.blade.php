@extends("layout")
@section("title")
{{ __('all.nav.global_settings') }} | BigLan
@endsection
@section("content")
	<div class="row mt-2">
		<div class="col-6">
			@if(auth()->user()->hasPermission('write-global-settings'))
    			<button type="submit" form="settings" class="btn btn-sm btn-primary mr-2"><i class="fas fa-save"></i> {{ __('all.button.save') }}</button>
			@endif
			@if(auth()->user()->hasPermission('read-global-settings-eventlog'))
    			<a href={{ url('globalsettings/logs') }} class="btn btn-sm btn-outline-secondary mr-2"><i class="fas fa-info"></i> {{ __('all.button.view_global_settings_log') }}</a>
			@endif
		</div>
	</div>
	<div class="row mt-2">
		<div class="col-lg-12 col-sm-12">
			<div class="table-responsive">
				@if(count($settings) > 0)
            	<form method="post" id="settings" action="globalsettings/save">
   				{{ csrf_field()}}
			
            	<table class="table table-striped table-hover" id="networkprinters">
						<thead class="thead-dark">
							<tr>
            					<th>{{ __('all.global_settings.th_name') }}</th>
            					<th>{{ __('all.global_settings.th_value') }}</th>
							</tr>
						</thead>
						<tbody>
					
   				@foreach($settings as $setting)
							<tr data-id={{ $setting->id }}>
								<td><strong>{{ $setting->name }}</strong><br><i>({{ $setting->description }})</i></td>
								<td>
                					@if(auth()->user()->hasPermission('write-global-settings'))
    									@if($setting->type === "string")
                							<input type="text" name="{{ $setting->name }}" class="form-control" value="{{ $setting->value }}">
            							@endif
                						@if($setting->type === "boolean")
            								<div class="form-group">
  												<select name="{{ $setting->name }}" class="form-control">
    												<option value="1" @if((int)$setting->value === 1) selected @endif>{{ __('all.button.yes') }}</option>
    												<option value="0" @if((int)$setting->value === 0) selected @endif>{{ __('all.button.no') }}</option>
  												</select>
											</div>
            							@endif
                					@else
                						{{$setting->value}}
									@endif
            					</td>
							</tr>
			    @endforeach
						</tbody>
					</table>
            		</form>
            	@else
                	<p>{{ __('all.global_settings.global_settings_not_found') }}</p>
				@endif
			</div>
		</div>
	</div>

@endsection
@section('inject-footer')
    <style>
    	.table td {
            padding: 0.3rem;
        }

		
    </style>                            	
 	<script type="text/javascript">
    $(function() {
    	
    	$('[data-toggle="popover"]').popover();
    	
	});
</script>                               
@endsection