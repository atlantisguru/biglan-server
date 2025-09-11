@extends ('layout')
	@section('title')
		{{ __('all.workstations.monitors') }} | BigLan
	@endsection
	@section('content')

	@php
		$userPermissions = auth()->user()->permissions();
	@endphp

	<div class="row mt-1">
	<div class="col-12">
	<div class="table-responsive">
		<table class="table table-hover" id="displays">
			<thead class="thead-dark">
				<tr>
					<th>{{ __('all.workstations.workstation') }}</th>
					<th>{{ __('all.workstations.manufacturer_code') }}</th>
             		<th>{{ __('all.workstations.brand_model') }}</th>
					<th>{{ __('all.workstations.serial') }}</th>
					<th>{{ __('all.workstations.inventory_id') }}</th>
					<th>{{ __('all.workstations.manufacture_year') }}</th>
					<th>{{ __('all.workstations.registered') }}</th>
				</tr>
			</thead>
			<tbody>
				@foreach($displays as $display)
					<tr>
						<td>
							@if(in_array('read-workstation', $userPermissions))
								<a href="{{ url('workstations/'.$display->wsid) }}" target="_blank">{{ $display->workstation()->alias ?? ""}}</a>
							@else
								{{ $display->workstation()->alias ?? ""}}
							@endif
						</td>
             			<td>{{ $display->manufacturer ?? ""}}</td>
             			<td>{{ $display->name ?? ""}}</td>
						<td>{{ $display->serial ?? ""}}</td>
						<td>{{ $display->inventory_id ?? ""}}</td>
             			<td>{{ $display->year ?? ""}}</td>
						<td>{{ $display->updated_at ?? ""}}</td>
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
    				$('#displays').DataTable({
						"pageLength": 100
					});
			});
        </script>
		
    @endsection