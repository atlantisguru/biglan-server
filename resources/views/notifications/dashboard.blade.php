@extends("layout")
@section("title")
{{ __('all.notification_center.notification_center') }} | BigLan
@endsection
@section("content")
	<div class="row mt-2">
		<div class="col-12">
			{{ csrf_field()}}
			<a href={{ url('notifications') }} class="btn btn-sm btn-primary mr-2"><i class="far fa-arrow-alt-circle-left"></i> {{ __('all.button.back') }}</a>
            @if(auth()->user()->hasPermission('read-notifications-eventlog'))
            	<a href={{ url('notifications/logs') }} class="btn btn-sm btn-outline-secondary"><i class="fas fa-info"></i> {{ __('all.notification_center.eventlog') }}</a>
			@endif
        </div>
	</div>
	<div class="row mt-2">
		<div class="col-lg-12 col-sm-12">
			
				@if(count($notifications) > 0)
            		<div class="row">
            			
   					@foreach($notifications as $notification)
            			<div class="col-lg-3 col-sm-6">
            			@if($notification->monitored)
            				@if($notification->triggered)
            					<button data-id="{{ $notification->id }}" type="button" class="btn btn-sm btn-danger m-1">
            						 <i class="fas fa-times"></i> {{ $notification->alias }} @if($notification->type == "sensor-value") <span class="value badge badge-light">{{ $notification->last_value }}</span> @endif
								</button>
            				@else
            					<button data-id="{{ $notification->id }}" type="button" class="btn btn-sm btn-success m-1">
  									<i class="fas fa-check"></i> {{ $notification->alias }} @if($notification->type == "sensor-value") <span class="value badge badge-light">{{ $notification->last_value }}</span> @endif
								</button>
            				@endif	
            			@else
            				<button data-id="{{ $notification->id }}" type="button" class="btn btn-sm btn-secondary m-1">
  								<i class="fas fa-bell-slash"></i> {{ $notification->alias }} @if($notification->type == "sensor-value") <span class="value badge badge-light">{{ $notification->last_value }}</span> @endif
							</button>
                        @endif
            		</div>
					@endforeach
            		</div>
            
            	@else
                	<p>{{ __('all.notification_center.notification_not_found') }}</p>
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
    	
    	var notification_id, notification_type;
    
    	var timer = setInterval(function(){
        							getNotificationStatuses();
        						}, 15000);
    
    	function getNotificationStatuses() {
        	var posting = $.post("{{ url('notifications/payload') }}", { '_token': $('meta[name=csrf-token]').attr('content'), action: 'getNotificationStatuses'}, "JSONP");
        	posting.done(function(data) {
            	//console.log(data);
            	$(data).each(function( ) {
  					var id = this.id;
                	var triggered = this.triggered;
                	var monitored = this.monitored;
                	
                	$("button[data-id="+id+"]").find("span.value").html(this.last_value);
                
                	if (monitored == 1) {
                    
                    	if (triggered == 1) {
                        	$("button[data-id="+id+"]").removeClass("btn-success").removeClass("btn-secondary").addClass("btn-danger");            
                    		$("button[data-id="+id+"] i").removeClass("fa-bell-slash").removeClass("fa-check").addClass("fa-times");
                        } else {
                       	 	$("button[data-id="+id+"]").removeClass("btn-danger").removeClass("btn-secondary").addClass("btn-success");          
                    		$("button[data-id="+id+"] i").removeClass("fa-bell-slash").removeClass("fa-times").addClass("fa-check");
                        }
                	
                    } else {
                    	$("button[data-id="+id+"]").removeClass("btn-danger").removeClass("btn-success").addClass("btn-secondary");
                    	$("button[data-id="+id+"] i").removeClass("fa-times").removeClass("fa-check").addClass("fa-bell-slash");
                    }
                
				});
        	});
        }
    	
    	$('[data-toggle="popover"]').popover();
    	
	});
</script>                               
@endsection