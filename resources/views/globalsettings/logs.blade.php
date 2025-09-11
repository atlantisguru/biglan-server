@extends("layout")
@section("title")
	{{ __('all.global_settings.title_global_settings_log') }} | BigLan
@endsection
@section("content")
	<div class="row mt-2">
		<div class="col-6">
			{{ csrf_field()}}
			<a href={{ url('globalsettings') }} class="btn btn-sm btn-primary"><i class="far fa-arrow-alt-circle-left"></i> {{ __('all.button.back_global_settings') }}</a>
		</div>
	</div>
	<div class="row mt-2">
		<div class="col-lg-12 col-sm-12">
			<div class="table-responsive">
				@if(count($settingsLogs) > 0)
   				<table class="table table-striped table-hover">
						<thead class="thead-dark">
							<tr>
								<th>{{ __('all.global_settings.th_datetime') }}</th>
								<th>{{ __('all.global_settings.th_settings') }}</th>
            					<th>{{ __('all.global_settings.th_event') }}</th>
							</tr>
						</thead>
						<tbody>
					
   				@foreach($settingsLogs as $log)
							<tr>
            					<td>{{ $log->created_at }}</td>
								<td>{{ $log->globalsettings->name }}</td>
								<td>{!! $log->event !!}</td>
							</tr>
			        
				@endforeach
						</tbody>
					</table>
            	@else
            			<p>{{ __('all.global_settings.global_settings_log_not_found') }}</p>
				@endif
			</div>
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
    	
       
	});                                
    </script>                               
@endsection