@extends ('layout')
	@section('title')
	{{ __("all.workstations.workstations") }} | BigLan
	@endsection
	@section('content')
	@php
		$userPermissions = auth()->user()->permissions();
	@endphp
	<div class="row mt-2">
		<div class="col-12">
			@if(in_array('write-workstation', $userPermissions))
				<a href="{{ url('workstations/new') }}" class="btn btn-sm btn-primary mr-3"><i class="fas fa-plus"></i> {{ __("all.button.new_workstation") }}</a>
			@endif
			<a href="{{ url('workstations/createfilter') }}" class="btn btn-sm btn-light"><i class="fas fa-filter"></i> {{ __("all.button.create_filter") }}</a>
		</div>
	</div>
	<div class="row mt-2">
             
	<div class="col-12">
    
	<div class="table-responsive">
		<table class="table table-hover" id="workstations">
			<thead class="thead-dark">
				<tr>
					<th></th>
					<th>{{ __("all.workstations.alias") }}</th>
					<th>IP</th>
             		<th class="text-center">{{ __("all.workstations.workgroup") }}</th>
					<th class="text-center">{{ __("all.workstations.last_online") }}<br><small class="text-muted"></small></th>
					<th class="text-center">{{ __("all.workstations.os_version") }}<br><small class="text-muted">{{ __("all.workstations.last_os_update") }}</small></th>
				</tr>
			</thead>
			<tbody>
				@foreach($workstations as $workstation)
					<tr>
						<td>
             			@if($workstation->fast_startup == 1) <i class="fas fa-bolt text-warning"></i> @endif
                        @php
                        	$icon = "fa-desktop";
                        	if($workstation->type == "server") {
                        		$icon = "fa-server";
                        	}
                        	if($workstation->type == "laptop") {
                        		$icon = "fa-laptop";
                        	}
                        	switch($workstation->status()) {
                        		case "online":
                        			$status = "text-success";
                        			break;
                        		case "idle":
                        			$status = "text-warning";
                        			break;
                        		case "offline":
                        			$status = "text-muted";
                        			break;
                        		case "heartbeatLoss":
                        			$status = "text-danger";
                        			break;
                        		default:
                        			$status = "text-muted";
                        			break;
                        	}
                        @endphp
                        
             			<i class="fas {{ $icon }} {{ $status }}"></i>
             			
             			</td>
             			<td>
                        	
                        	@if(in_array('read-workstation', $userPermissions))
             					<a href="{{ url('workstations/'.$workstation->id) }}">{{ $workstation->alias }} @if($workstation->inventory_id != null) ({{$workstation->inventory_id}}) @endif</a>
             				@else
                        		{{ $workstation->alias }} @if($workstation->inventory_id != null) ({{$workstation->inventory_id}}) @endif
                        	@endif
                            @if(!isset($workstation->cpu_points))
                        		<span class="badge bg-light text-muted small">CPU: {{ $workstation->cpu_score }}</span>
                            @endif
                            <br>
                            <span class="badge bg-info text-light small">S/N: {{ $workstation->serial }}</span>
                            @foreach($workstation->labels as $label) 
            					<span class="badge {{ ($label->prop == "SYSTEM")?'badge-danger':'badge-secondary' }} mr-1"> {!! ($label->prop == "SYSTEM")?'<i class="fas fa-robot"></i>':'' !!}  {{ $label->name }}</span>
                        	@endforeach
                        	@if(count($workstation->labels) > 0)
                        		<br />
                        	@endif
                        	@if(isset($workstation->brand))
                        		<span class="badge badge-warning"><b>Brand:</b> {{ $workstation->brand }}</span>
                        	@endif
             				@if(isset($workstation->uptime_days))
                        		<span class="badge badge-warning"><b>Uptime:</b> {{ $workstation->uptime_days }} {{ __('all.workstations.filter_days') }}</span>
                        	@endif
                        	@if(isset($workstation->offline_days))
                        		<span class="badge badge-warning"><b>Offline:</b> {{ $workstation->offline_days }} {{ __('all.workstations.filter_days') }}</span>
                        	@endif
             				@if(isset($workstation->disk))
                        		<span class="badge badge-warning"><b>Disk:</b> {{ $workstation->disk }}</span>
                        	@endif
             				@if(isset($workstation->freespace))
                        		<span class="badge badge-warning"><b>Free Space:</b> {{ $workstation->freespace }}GB</span>
                        	@endif
             				@if(isset($workstation->memory))
                        		<span class="badge badge-warning"><b>RAM:</b> {{ $workstation->memory }}GB</span>
                        	@endif
             				@if(isset($workstation->boot_seconds))
                        		<span class="badge badge-warning"><b>Boot:</b> {{ $workstation->boot_seconds }} {{ __('all.workstations.filter_seconds') }}</span>
                        	@endif
                            @if(isset($workstation->cpu_age) || isset($workstation->cpu_points))
                        		<span class="badge badge-warning"><b>CPU:</b> {{ $workstation->cpu }}</span>
                        	@endif
                            @if(isset($workstation->cpu_age))
                        		<span class="badge badge-warning"><b>CPU:</b> {{ $workstation->cpu_age }} {{ __('all.workstations.filter_years') }}</span>
                        	@endif
                            @if(isset($workstation->cpu_points))
                        		<span class="badge badge-warning"><b>CPU:</b> {{ $workstation->cpu_points }} {{ __('all.workstations.filter_points') }}</span>
                        	@endif
             				@if(isset($workstation->os_updated_months))
                        		<span class="badge badge-warning"><b>OS Updated:</b> {{ $workstation->os_updated_months }} {{ __('all.workstations.filter_months') }}</span>
                        	@endif
             			</td>
						<td>
                        	@foreach($workstation->ips as $ip)
                        		{{ $ip->ip }}<br>
							@endforeach
                        </td>
                        <td class="text-center">{{ $workstation->workgroup }}</td>
						<td class="text-center">{{ \Carbon\Carbon::parse($workstation->heartbeat)->format("Y.m.d H:i") }}<br><small class="text-muted">{{ $workstation->update_channel }}{{ $workstation->service_version }}</small></td>
						<td class="text-center">{{ $workstation->os }}<br><small class="text-muted">@if($workstation->wu_installed != null) {{  \Carbon\Carbon::parse($workstation->wu_installed)->format("Y.m.d H:i") }} @else N/A @endif</small></td>
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
    				$('#workstations').DataTable( {
                    	"pageLength": 50,
    					"search": {
    						"search": "\"{{ $keyword }}\""
  						}
  					});
			});
        </script>
		
    @endsection