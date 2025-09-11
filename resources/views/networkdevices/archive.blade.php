<!DOCTYPE html>
<html lang="hu">
  <head>
    <meta charset="utf-8">
    <title>{{ $networkdevice->alias }} - {{$networkdevice->ports}}P</title>
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
			<td colspan="3"><h3 class="section-header">{{ __('all.network_devices.network_device') }}</h3></td>
		</tr>
		<tr>
			<td class="bold">
				ID
			</td>
			<td colspan="2">
				{{ $networkdevice->id }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.network_devices.name') }}
			</td>
			<td colspan="2">
				{{ $networkdevice->alias }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.network_devices.brand_model') }}
			</td>
			<td colspan="2">
				{{ $networkdevice->hardware }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.network_devices.serial') }}
			</td>
			<td colspan="2">
				{{ $networkdevice->serial }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.network_devices.mac') }}
			</td>
			<td colspan="2">
				{{ $networkdevice->mac }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.network_devices.type') }}
			</td>
			<td colspan="2">
				{{ $networkdevice->type ?? "N/A" }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.network_devices.ip') }}
			</td>
			<td colspan="2">
				{{ $networkdevice->ip ?? "N/A" }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.network_devices.ports') }}
			</td>
			<td colspan="2">
				{{ $networkdevice->ports ?? "N/A" }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.network_devices.speed') }}
			</td>
			<td colspan="2">
				{{ $networkdevice->speed ?? "N/A" }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.network_devices.registered') }}
			</td>
			<td colspan="2">
				{{ $networkdevice->created_at }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.network_devices.network') }}
			</td>
			<td colspan="2">
				{{ $conn }}
			</td>
		</tr>
	</table>
  </body>
</html>