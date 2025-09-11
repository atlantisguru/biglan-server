@extends("layout")
@section("title")
	{{ __('all.notification_center.eventlog') }} | BigLan
@endsection
@section("content")
	<div class="row mt-2">
		
		<div class="col-6">
			<h4>{{ __('all.notification_center.eventlog') }}</h4>
			{{ csrf_field()}}
			<a href={{ url('notifications') }} class="btn btn-sm btn-primary"><i class="far fa-arrow-alt-circle-left"></i> {{ __('all.button.back') }}</a>
		</div>
	</div>
	<div class="row mt-2">
        
		<div class="col-lg-12 col-sm-12">
			<div class="table-responsive">
				@if(count($notificationLogs) == 0)
   					<p>{{ __('all.notification_center.notification_event_not_found') }}</p>
				@endif
				<table class="table table-striped table-hover">
						<thead class="thead-dark">
							<tr>
								<th>{{ __('all.notification_center.date_and_time') }}</th>
								<th>{{ __('all.notification_center.status') }}</th>
            					<th>{{ __('all.notification_center.name') }}</th>
            					<th>{{ __('all.notification_center.event') }}</th>
								<th>{{ __('all.notification_center.description') }}</th>
							</tr>
						</thead>
						<tbody>
					
   				@foreach($notificationLogs as $log)
							<tr>
            					<td>{{ $log->created_at }}</td>
								<td class="text-center">
										@if(!isset($log->status))  
											<i class="fas fa-info text-muted" title="{{ __('all.notification_center.info') }}"></i>
										@else
            								@if($log->status == 1)  
												<i class="fas fa-times text-danger" title="{{ __('all.notification_center.alert') }}"></i> 
											@else  
												<i class="fas fa-check text-success" title="{{ __('all.notification_center.idle') }}"></i>
											@endif
            							@endif
								</td>
								<td>{{ $log->notification->alias }}</td>
								<td>{{ $log->event }}</td>
								<td style="width: 50%">{!! strip_tags($log->description, '<pre>') !!}</td>
							</tr>
			        
				@endforeach
						</tbody>
					</table>
            
			</div>
		</div>
	</div>
    <div class="row mt-4">
		<div class="col-12">
           {{ $notificationLogs->links() }}
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