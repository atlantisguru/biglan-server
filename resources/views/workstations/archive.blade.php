<!DOCTYPE html>
<html lang="hu">
  <head>
    <meta charset="utf-8">
    <title>{{ $workstation->alias }} | {{ $workstation->hostname }}</title>
	<style type="text/css">
		body {
				font-family: Sans-Serif;
				color: #333;
				padding: 5px 20px;
		}
		td {
			padding: 7px 0px;
		}
		.bold {
			font-weight: bold;
		}
		.section-header {
			text-align: center;
			padding: 10px;
			color: #FFF;
			background-color: #004ba0;
		}
		tr.event:nth-child(even) {
			background-color: #EDEDED;
		}
		
	</style>
  </head>
  <body>
	<h2>BigLan Archive Report</h2>
	<span>{{ \Carbon\Carbon::now()->format("Y.m.d H:i:s") }}, {{ Auth::user()->username }}</span>
	<table>
		<tr>
			<td colspan="3"><h3 class="section-header">{{ __('all.workstations.general') }}</h3></td>
		</tr>
		<tr>
			<td class="bold">
				WSID
			</td>
			<td colspan="2">
				{{ $workstation->id }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				UUID
			</td>
			<td colspan="2">
				{{ $workstation->uuid }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.workstations.motherboard_serial') }}
			</td>
			<td colspan="2">
				{{ $workstation->mboard_serial }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.workstations.alias') }}
			</td>
			<td colspan="2">
				{{ $workstation->alias }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.workstations.hostname') }}
			</td>
			<td colspan="2">
				{{ $workstation->hostname }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.workstations.workgroup') }}
			</td>
			<td colspan="2">
				{{ $workstation->workgroup }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.workstations.inventory_id') }}
			</td>
			<td colspan="2">
				{{ $workstation->inventory_id ?? "N/A" }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.workstations.registered') }}
			</td>
			<td colspan="2">
				{{ $workstation->created_at }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.workstations.last_online') }}
			</td>
			<td colspan="2">
				{{ $workstation->heartbeat }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.workstations.brand_model') }}
			</td>
			<td colspan="2">
				{{ $workstation->hardware }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.workstations.serial') }}
			</td>
			<td colspan="2">
				{{ $workstation->serial ?? "N/A" }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.workstations.operating_system') }}
			</td>
			<td colspan="2">
				{{ $workstation->os }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.workstations.processor') }}
			</td>
			<td colspan="2">
				{{ $workstation->cpu }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.workstations.released') }}
			</td>
			<td colspan="2">
				{{ $workstation->cpu_release_date }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.workstations.os_partition_size') }} ({{ __('all.workstations.os_partition_free_space') }})
			</td>
			<td colspan="2">
				{{ $workstation->os_drive_size }}GB ({{ $workstation->os_drive_free_space }}GB)
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.workstations.memory') }} ({{ __('all.workstations.memory_max') }} - {{ __('all.workstations.memory_slots') }})
			</td>
			<td colspan="2">
				{{ $workstation->ram}}MB @if(isset($workstation->ram_slots) && isset($workstation->ram_max_capacity)) (Max: @if ($workstation->ram_max_capacity/1024 < 1024) {{$workstation->ram_max_capacity/1024 }}GB @else {{ $workstation->ram_max_capacity/1024/1024 }}GB @endif - {{$workstation->ram_slots}} slot) @endif
			</td>
		</tr>
    	<tr>
			<td class="bold">
				{{ __('all.workstations.labels') }}
			</td>
			<td colspan="2">
				@foreach($workstation->labels()->get() as $label)
                                        		{{ $label->name }}<br>
                                        	@endforeach
			</td>
		</tr>
    	<tr>
			<td class="bold">
				{{ __('all.workstations.local_user_accounts') }}
			</td>
			<td colspan="2">
				@foreach($workstation->accounts()->get() as $account)
                                    	{{$account->username}} @if( $account->is_admin) ({{ __('all.workstations.administrator') }}) @endif<br>
                                    	@endforeach
        	</td>
		</tr>

		<tr>
			<td colspan="3"><h3 class="section-header">Hardware ({{ __('all.workstations.other') }})</h3></td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.workstations.memory_modules') }}
			</td>
			<td colspan="2">
				@if($workstation->memories()->count() > 0)	
                                        	@foreach($workstation->memories()->get() as $memory)
                                        		<div>{{$memory->manufacturer}} {{ $memory->capacity }}MB x{{ $memory->speed }}MHz {{ $memory->type() }} ({{ $memory->slot }})</div>
                                        	@endforeach
                                        @endif
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.workstations.monitors') }}
			</td>
			<td colspan="2">
				@if($workstation->monitors()->first() != null)	
                    @foreach($workstation->monitors()->get() as $monitor)
                    	{{$monitor->manufacturer}} {{$monitor->name}} (s/n:{{$monitor->serial}}) lelt.azon.:{{ $monitor->inventory_id ?? "N/A" }}<br>
                    @endforeach
                @endif
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.workstations.disks') }}
			</td>
			<td colspan="2">
				@foreach($workstation->hdds()->get() as $hdd)
                   {{$hdd->model}} ({{ $hdd->capacity }}GB) {{ $hdd->status }} (s/n:{{$hdd->serial}})<br>
                @endforeach
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.workstations.printers') }}
			</td>
			<td colspan="2">
				@foreach($workstation->printers()->get() as $printer)
                 	@if($printer->default == 1) (alapértelemezett) @endif @if($printer->shared == 1) (Megosztott) @endif {{$printer->name}} ({{ $printer->port }})<br>
                @endforeach
			</td>
		</tr>
		<tr>
			<td colspan="3"><h3 class="section-header">{{ __('all.workstations.network_data') }}</h3></td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.workstations.default_mac') }}
			</td>
			<td colspan="2">
				{{ $workstation->first_mac }}
			</td>
		</tr>
        <tr>
			<td class="bold">
				{{ __('all.workstations.ip_addresses') }}
			</td>
			<td colspan="2">
				@foreach($workstation->ips() as $ip)
                                    		{{$ip['ip']}}<br>
                @endforeach
            </td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.workstations.dns_addresses') }}
			</td>
			<td colspan="2">
				@foreach($workstation->dns()->get() as $dns)
                                        	{{$dns->ip}}<br>
                                        @endforeach
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.workstations.endpoint_network_connection') }}
			</td>
			<td colspan="2">
				{{ $conn }}
			</td>
		</tr>
    	<tr>
			<td colspan="3"><h3 class="section-header">{{ __('all.workstations.events') }}</h3></td>
		</tr>
    	@foreach($events as $event)
		<tr class="event">
			<td>
				{{ $event->created_at }}
			</td>
			<td>
				{{ $event->event }}
			</td>
			<td>
				{{ $event->description }}
        	</td>
		</tr>
		@endforeach
    	
	</table>
  </body>
</html>