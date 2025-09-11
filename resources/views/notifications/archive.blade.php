<!DOCTYPE html>
<html lang="hu">
  <head>
    <meta charset="utf-8">
    <title>{{ $notification->alias }} | {{ $notification->name }}</title>
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
			<td colspan="2"><h3 class="section-header">{{ __('all.notification_center.notification_center') }}</h3></td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.notification_center.name') }}
			</td>
			<td colspan="2">
				{{ $notification->alias  OR "N/A" }} ({{ $notification->name }})
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.notification_center.type') }}
			</td>
			<td colspan="2">
				{{ $notification->type  OR "N/A" }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.notification_center.parameters') }}
			</td>
			<td colspan="2">
				{{ $notification->target  OR "N/A" }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.notification_center.description') }}
			</td>
			<td colspan="2">
				{{ $notification->description  OR "N/A" }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.notification_center.last_value') }}
			</td>
			<td colspan="2">
				{{ $notification->last_value  OR "N/A" }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.notification_center.registered') }}
			</td>
			<td colspan="2">
				{{ $notification->created_at }}
			</td>
		</tr>
		<tr>
			<td class="bold">
				{{ __('all.notification_center.last_changed') }}
			</td>
			<td colspan="2">
				{{ $notification->updated_at }}
			</td>
		</tr>
        </table>
        <table>
		<tr>
			<td colspan="4"><h3 class="section-header">{{ __('all.notification_center.events') }}</h3></td>
		</tr>
    	<tr class="event">
			<td>
				<strong>{{ __('all.notification_center.date_and_time') }}</strong>
			</td>
			<td>
				<strong>{{ __('all.notification_center.status') }}</strong>
			</td>
			<td>
				<strong>{{ __('all.notification_center.event') }}</strong>
			</td>
			<td>
				<strong>{{ __('all.notification_center.description') }}</strong>
			</td>
			
		</tr>
    	
    	@foreach($events as $event)
		<tr class="event">
			<td>
				{{ $event->created_at }}
			</td>
			<td>
            	@if($event->status == 0)
            		Nyugalomban
            	@endif
 			
            	@if($event->status == 1)
            		Riasztásban
            	@endif
 			</td>
			<td>
            	{{ $event->event }}
			</td>
			<td>
            	{!! $event->description !!}
			</td>
		</tr>
		@endforeach
    	</table>
    	
  </body>
</html>