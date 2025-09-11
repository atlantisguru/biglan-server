@extends("layout")
@section("title")
{{ __('all.network_printers.new_network_printer') }} | BigLan
@endsection
@section("content")
	
	<div class="row mt-2">
		<div class="col-lg-12 col-sm-12">
			<h4>{{ __('all.network_printers.new_network_printer') }}</h4>
			<form method="POST" class="form-horizontal" action="{{ url('networkprinters/save') }}">
        	<button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-save"></i> {{ __('all.button.save') }}</button>&nbsp;<a href={{ url('networkprinters') }} class="btn btn-sm btn-secondary"><i class="fas fa-times"></i> {{ __('all.button.back_without_save') }}</a>
			{{ csrf_field() }}
			<div class="form-group row mt-2">
            	<div class="col-4">
					<span class="control-label">{{ __('all.network_printers.name') }}</span>
				</div>
				<div class="col-4">
					<input type="text" name="alias" class="form-control">
				</div>
			</div>
        	<div class="form-group row">
				<div class="col-4">
					<span class="control-label">{{ __('all.network_printers.ip_address') }}</span>
				</div>
				<div class="col-4">
					<input type="text" name="ip" class="form-control">
				</div>
			</div>
		</form>
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
    	
	});
</script>                               
@endsection