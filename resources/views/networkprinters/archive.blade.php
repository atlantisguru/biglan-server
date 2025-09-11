<!DOCTYPE html>
<html lang="hu">
  <head>
    <meta charset="utf-8">
    <title>{{ $networkPrinter->alias }} | {{ $networkPrinter->brand }}</title>
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
			<td colspan="3"><h3 class="section-header">{{ __('all.network_printers.network_printer') }}</h3></td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.network_printers.inventory_id') }}
			</td>
			<td colspan="2">
				{{ $networkPrinter->inventory_id ?? "N/A" }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.network_printers.name') }}
			</td>
			<td colspan="2">
				{{ $networkPrinter->alias ?? "N/A" }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.network_printers.ip_address') }}
			</td>
			<td colspan="2">
				{{ $networkPrinter->ip ?? "N/A" }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.network_printers.registered') }}
			</td>
			<td colspan="2">
				{{ $networkPrinter->created_at }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.network_printers.last_updated') }}
			</td>
			<td colspan="2">
				{{ $networkPrinter->updated_at }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.network_printers.brand_model') }}
			</td>
			<td colspan="2">
				{{ $networkPrinter->brand }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.network_printers.serial') }}
			</td>
			<td colspan="2">
				{{ $networkPrinter->serial ?? "N/A" }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.network_printers.note') }}
			</td>
			<td colspan="2">
				{{ $networkPrinter->notes ?? "N/A" }}
			</td>
		</tr>
        </table>
        <table>
		<tr>
			<td colspan="6"><h3 class="section-header">{{ __('all.network_printers.statistics') }}</h3></td>
		</tr>
    	<tr class="event">
			<td>
				<strong>{{ __('all.network_printers.date_time') }}</strong>
			</td>
			<td>
				<strong>{{ __('all.network_printers.black_toner_ink_level') }}</strong>
			</td>
			<td>
				<strong>{{ __('all.network_printers.cyan_toner_ink_level') }}</strong>
			</td>
			<td>
				<strong>{{ __('all.network_printers.magenta_toner_ink_level') }}</strong>
			</td>
			<td>
				<strong>{{ __('all.network_printers.yellow_toner_ink_level') }}</strong>
			</td>
			<td>
				<strong>{{ __('all.network_printers.counter') }}</strong>
			</td>
		</tr>
    	
    	@foreach($networkPrinterStatistics as $statistic)
		<tr class="event">
			<td>
				{{ $statistic->created_at }}
			</td>
			<td>
            	@if(isset($statistic->black_toner_level) && isset($statistic->black_toner_max) && $statistic->black_toner_level >= 0 && $networkPrinter->black_toner_max > 0)
					{{ round($statistic->black_toner_level/$statistic->black_toner_max*100) }}%
            	@endif
			</td>
			<td>
				@if(isset($statistic->cyan_toner_level) && isset($statistic->cyan_toner_max) && $statistic->cyan_toner_level >=0 && $networkPrinter->cyan_toner_max > 0)
					{{ round($statistic->cyan_toner_level/$statistic->cyan_toner_max*100) }}%
				@endif
            </td>
			<td>
				@if(isset($statistic->magenta_toner_level) && isset($statistic->magenta_toner_max) && $statistic->magenta_toner_level >=0 && $networkPrinter->magenta_toner_max > 0)
					{{ round($statistic->magenta_toner_level/$networkPrinter->magenta_toner_max*100) }}%
				@endif
            </td>
			<td>
				@if(isset($statistic->yellow_toner_level) && isset($statistic->yellow_toner_max) && $statistic->yellow_toner_level >=0 && $networkPrinter->yellow_toner_max > 0)
					{{ round($statistic->yellow_toner_level/$networkPrinter->yellow_toner_max*100) }}%
				@endif
            </td>
			<td>
            	{{ $statistic->print_counter }}
			</td>
		</tr>
		@endforeach
        </table>
    	<table>
    	<tr>
			<td colspan="2"><h3 class="section-header">{{ __('all.network_printers.events') }}</h3></td>
		</tr>
    	<tr class="event">
			<td>
				<strong>{{ __('all.network_printers.date_time') }}</strong>
			</td>
			<td>
				<strong>{{ __('all.network_printers.event') }}</strong>
			</td>
		</tr>
    	@foreach($networkPrinterEvents as $event)
		<tr class="event">
			<td>
				{{ $event->created_at }}
			</td>
			<td>
				{{ $event->event }}
			</td>
		</tr>
		@endforeach
    	
	</table>
  </body>
</html>