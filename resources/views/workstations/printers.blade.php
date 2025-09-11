@extends ('layout')
	@section('title')
		{{ __('all.workstations.printers') }} | BigLan
	@endsection
	@section('content')

	@php
		$userPermissions = auth()->user()->permissions();
	@endphp
	
    <div class="row mt-1">
	<div class="col-12">
	<div class="table-responsive">
		<table class="table table-hover" id="printers">
			<thead class="thead-dark">
				<tr>
					<th>{{ __('all.workstations.workstation') }}</th>
					<th>{{ __('all.workstations.name') }}</th>
             		<th>Port</th>
					<th>{{ __('all.workstations.registered') }}</th>
				</tr>
			</thead>
			<tbody>
				@foreach($printers as $printer)
					@php
						$workstation = $printer->workstation();
					@endphp

					<tr>
						<td>
						@if(in_array('read-workstation', $userPermissions))
                    		<a href="{{ url('workstations/'.$printer->wsid) }}" target="_blank">{{ $workstation->alias ?? $printer->id }}</a>
						@else
							{{ $workstation->alias ?? $printer->id }}
						@endif
						</td>
             			<td>{{ $printer->name ?? ""}}</td>
						<td>{{ $printer->port ?? ""}}</td>
						<td>{{ $printer->updated_at ?? ""}}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	</div>
	</div>
	@endsection
    
    @section('inject-footer')
    	<link rel="stylesheet" type="text/css" href={{ url("css/jquery.dataTables.min.css") }}>
        <script type="text/javascript" src={{ url("js/jquery.dataTables.min.js") }}></script>
        <script type="text/javascript">
        	$(function() {
    				$('#printers').DataTable({
						"pageLength": 100
					});
			});
        </script>
		
    @endsection