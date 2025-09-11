@extends("layout")
@section("title")
{{ __('all.notification_center.notification_center') }} | BigLan
@endsection
@section("content")
	<div class="row mt-2">
		<div class="col-12">
			{{ csrf_field()}}
			@if(auth()->user()->hasPermission('write-notification'))
				<a href={{ url('notifications/new') }} class="btn btn-sm btn-primary mr-2"><i class="fas fa-plus"></i> {{ __('all.button.new_notification') }}</a>
            @endif
           	@if(auth()->user()->hasPermission('read-notifications-eventlog'))
				<a href={{ url('notifications/logs') }} class="btn btn-sm btn-outline-secondary mr-2"><i class="fas fa-info"></i> {{ __('all.notification_center.eventlog') }}</a>
            @endif
            <a href={{ url('notifications/dashboard') }} class="btn btn-sm btn-outline-secondary mr-2"><i class="fas fa-tachometer-alt"></i> {{ __('all.notification_center.dashboard_view') }}</a>
		</div>
	</div>
	<div class="row mt-2">
		<div class="col-lg-12 col-sm-12">
			<div class="table-responsive">
				@if(count($notifications)>0)
   					
				<table class="table table-striped table-hover">
						<thead class="thead-dark">
							<tr>
								<th class="text-center">{{ __('all.notification_center.status') }}</th>
								<th>{{ __('all.notification_center.name') }}</th>
								<th>{{ __('all.notification_center.type') }}</th>
								<th>{{ __('all.notification_center.parameters') }}</th>
								<th>{{ __('all.notification_center.description') }}</th>
            					<th>{{ __('all.notification_center.value') }}</th>
            					<th class="text-center">{{ __('all.notification_center.active') }}?</th>
							</tr>
						</thead>
						<tbody>
					
   				@foreach($notifications as $notification)
							<tr class="notification-element @if($notification->monitored == 0) text-muted @endif" data-id="{{ $notification->id }}">
								<td class="text-center notification-element-status" data-field="triggered">
										@if($notification->triggered == 1)  
											<i class="fas fa-times @if($notification->monitored == 0) text-muted @else text-danger @endif" title="Alert"></i> 
										@else 
											<i class="fas fa-check @if($notification->monitored == 0) text-muted @else text-success @endif" title="Idle"></i>
										@endif
								</td>
								<td><a href="javascript:void(0)" class="details" data-toggle="modal" data-target="#details">{{ $notification->alias }}</a></td>
								<td>
            						@php
            							switch($notification->type) {
                                        	case "socket-polling":
                                        		$info = __('all.notification_center.socket_polling_description');
                                        		$type = __('all.notification_center.socket_polling');
                                        		break;
                                        	case "sensor-value":
                                        		$info = __('all.notification_center.sensor_value_description');
                                        		$type = __('all.notification_center.sensor_value');
                                        		break;
                                        	case "ping":
                                        		$info = __('all.notification_center.ping_description');
                                        		$type = __('all.notification_center.ping');
                                        		break;
                                        	case "mass-heartbeat-loss":
                                        		$info = __('all.notification_center.mass_heartbeat_loss_description');
                                        		$type = __('all.notification_center.mass_heartbeat_loss');
                                        		break;
                                        	case "biglan-command":
                                        		$info = __('all.notification_center.biglan_command_description');
                                        		$type = __('all.notification_center.biglan_command');
                                        		break;
                                        	case "snmp":
                                        		$info = __('all.notification_center.snmp_description');
                                        		$type = "SNMP";
                                        		break;
                                        	case "http-status-code":
                                        		$info = __('all.notification_center.http_status_code_description');
                                        		$type = __('all.notification_center.http_status_code');
                                        		break;
                                        	default:
                                        		$info = "¯\_(ツ)_/¯";
                                        		$type = __('all.notification_center.other');
                                        }
            						@endphp
            						<i class="fas fa-info-circle text-info" data-toggle="popover" data-trigger="hover" title="{{ $type }}" data-content="{{ $info }}"></i>
            						{{ $type }}
								</td>
								<td>{{ (strlen($notification->target) > 50) ? mb_substr($notification->target, 0, 50, "UTF-8") . "..." : $notification->target }}</td>
								<td>{{ (strlen($notification->description) > 50) ? mb_substr($notification->description, 0, 50, "UTF-8") . "..." : $notification->description }}</td>
								<td data-field="value">
                                	<span title="{{ $notification->updated_at }}">{{ $notification->last_value }}@if(isset($notification->unit)){{ $notification->unit }}@endif</span>
								</td>
                                @if(auth()->user()->hasPermission('write-notification'))
									<td data-field="monitored" class="text-center">@if($notification->monitored) <a href="javascript:void(0)" data-id="{{ $notification->id }}" data-monitored="0" class="fas fa-bell text-primary notification-monitored-icon" title="{{ __('all.notification_center.activated') }}"></a> @else <a href="javascript:void(0)" data-id="{{ $notification->id }}" data-monitored="1" class="fas fa-bell-slash text-muted notification-monitored-icon" title="{{ __('all.notification_center.disabled') }}"></a> @endif </td>
								@else
                                	<td data-field="monitored" class="text-center">@if($notification->monitored) <i class="fas fa-bell text-primary notification-monitored-icon" title="{{ __('all.notification_center.activated') }}"></i> @else <i data-id="{{ $notification->id }}" data-monitored="1" class="fas fa-bell-slash text-muted notification-monitored-icon" title="{{ __('all.notification_center.disabled') }}"></i> @endif </td>
                                @endif
                         	</tr>
			        
				@endforeach
						</tbody>
					</table>
            @else
                                <p>{{ __('all.notification_center.notification_not_found') }}</p>
				@endif
			</div>
		</div>
	</div>
<div class="modal" id="details">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h4 class="modal-title">{{ __('all.notification_center.notification_details') }}</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body">
      	<div class="row mt-2">
        	<div class="col-12">
        		<strong>{{ __('all.notification_center.alias') }}</strong>
        		<p id="alias" class="editable"></p>
        		<input type="hidden" name="notification_id" id="notification_id">
        	</div>
        	<div class="col-12">
        		<strong>{{ __('all.notification_center.name') }}</strong>
        		<p id="name" class="editable"></p>
        	</div>
        	<div class="col-12">
        		<strong>{{ __('all.notification_center.type') }}</strong>
        		<p id="type"></p>
        	</div>
        	<div class="col-12">
        		<strong>{{ __('all.notification_center.description') }}</strong>
        		<p id="description" class="editable"></p>
        	</div>
        	<div class="col-12">
        		<strong>{{ __('all.notification_center.parameters') }}</strong>
        		<div id="target"></div>
        	</div>
        	@if(auth()->user()->hasPermission('delete-notification'))
        	<div class="col-12 mt-4">
        		<a href="javascript:void(0)" id="btn-delete" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> {{ __('all.button.delete') }} (x2)</a>&nbsp;<a href="javascript:void(0)" id="btn-save" class="d-none btn btn-success btn-sm"><i class="fas fa-check"></i> {{ __('all.button.save') }}</a>
        	</div>
        	@endif
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('all.button.close') }}</button>
      </div>

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
    
    	@if(auth()->user()->hasPermission('write-notification'))
    	$("body").on("click", ".notification-monitored-icon", function(e) {
        	var confirm = window.confirm("{{ __('all.notification_center.are_you_sure_active') }}");
            if(!confirm) {
            	return;
            }
        	var element = $(this);
        	var nid = element.attr("data-id");
        	var monitored = element.attr("data-monitored");
           	var posting = $.post("{{ url('notifications/payload') }}", { '_token': $('meta[name=csrf-token]').attr('content'), action: "changeNotificationMonitoredStatus", nid: nid, monitored: monitored }, "JSONP");
        	posting.done(function(data) {
        		if(data == 1) {
                	element.removeClass("fa-bell-slash").removeClass("text-muted").addClass("fa-bell").addClass("text-primary");
                	element.attr('data-monitored', '0')
                	element.attr("title", "{{ __('all.notification_center.activated') }}");
                	element.parents(".notification-element").removeClass("text-muted");
                	element.parents(".notification-element").find(".fa-times").addClass("text-danger").removeClass("text-muted");
                	element.parents(".notification-element").find(".fa-check").addClass("text-success").removeClass("text-muted");
                }
            	if(data == 0) {
                	element.removeClass("fa-bell").removeClass("text-primary").addClass("fa-bell-slash").addClass("text-muted");
                	element.attr('data-monitored', '1');
                	element.attr("title", "{{ __('all.notification_center.disabled') }}");
                	element.parents(".notification-element").addClass("text-muted");
                	element.parents(".notification-element").find(".fa-times").removeClass("text-danger").addClass("text-muted");
                	element.parents(".notification-element").find(".fa-check").removeClass("text-success").addClass("text-muted");
                }
            });
        });
    	@endif
    
    	function getNotificationStatuses() {
        	var posting = $.post("{{ url('notifications/payload') }}", { '_token': $('meta[name=csrf-token]').attr('content'), action: 'getNotificationStatuses'}, "JSONP");
        	posting.done(function(data) {
            	$(data).each(function( ) {
  					var id = this.id;
                	var triggered = this.triggered;
                	var monitored = this.monitored;
                	var value = this.last_value;
                	if (value == null) {
                    	value = "";
                    }
                	var unit = this.unit;
                	if (unit == null) {
                    	unit = "";
                    }
                	
                	var triggeredContent, monitoredContent;	
                
                	var alertText = "{{ __('all.notification_center.alert') }}";
                	var idleText = "{{ __('all.notification_center.idle') }}";
                	var activatedText = "{{ __('all.notification_center.activated') }}";
                	var disabledText = "{{ __('all.notification_center.disabled') }}";
                
                
                	if (triggered == 1) {
                    	triggeredContent = '<i class="fas fa-times '+ ((monitored==1)?'text-danger':'text-muted') +'" title="'+ alertText +'"></i>';
                    } else {
                    	triggeredContent = '<i class="fas fa-check  '+ ((monitored==1)?'text-success':'text-muted') +'" title="'+ idleText +'"></i>';
                    }
                
                	if (monitored == 1) {
                    	monitoredContent = '<a href="javascript:void(0)" data-id="'+id+'" data-monitored="0" class="fas fa-bell text-primary notification-monitored-icon" title="'+ activatedText +'"></a>';
                    } else {
                    	monitoredContent = '<a href="javascript:void(0)" data-id="'+id+'" data-monitored="1" class="fas fa-bell-slash text-muted notification-monitored-icon" title="'+ disabledText +'"></a>';
                    }
                
                	$("tr.notification-element[data-id='"+id+"']").find("[data-field='triggered']").html(triggeredContent).effect( "highlight", {color:"#ffc107"}, 2000 );
                	$("tr.notification-element[data-id='"+id+"']").find("[data-field='monitored']").html(monitoredContent).effect( "highlight", {color:"#ffc107"}, 2000 );
                	$("tr.notification-element[data-id='"+id+"']").find("[data-field='value']").html(value + "" + unit).effect( "highlight", {color:"#ffc107"}, 2000 );
                
				});
        	});
        }
    	
    	$("body").on("click", ".details", function(e) {
        	
        	$("#notification_id").val(notification_id);
        	var payLoad = {};
        
        	var element = $(this);
        	notification_id = element.parents("tr").attr("data-id");
        	
        	payLoad["_token"] = $('meta[name=csrf-token]').attr('content');
        	payLoad["id"] = notification_id;
        	payLoad["action"] = "getNotificationDetails";
        	
        	var posting = $.post("{{ url('notifications/payload') }}", payLoad, "JSONP");
        	posting.done(function(data) {
            	notification_type = data["type"];
            	$("#alias").text(data["alias"]);
            	$("#name").text(data["name"]);
            	
            	$("#type").text("{{ __('all.notification_center.other') }}");
                $("#target").html("<strong>{{ __('all.notification_center.expression') }}</strong><p>"+data["target"]+"</p>");
                
            	if (data["type"] === "http-status-code") {
                	$("#type").text("{{ __('all.notification_center.http_status_code') }}");
                	$("#target").html("<strong>{{ __('all.notification_center.website') }}</strong><p id='website' class='editable'>"+data["target"]["website"]+"</p><strong>{{ __('all.notification_center.expression') }}</strong><p id='expression' class='editable'>"+data["target"]["expression"]+"</p>");
                }
            
            	if (data["type"] === "socket-polling") {
                	$("#type").text("{{ __('all.notification_center.socket_polling') }}");
                	$("#target").html("<strong>{{ __('all.notification_center.ip_address') }}</strong><p id='ip' class='editable'>"+data["target"]["ip"]+"</p><strong>{{ __('all.notification_center.port') }}</strong><p id='port' class='editable'>"+data["target"]["port"]+"</p>");
                }
            	if (data["type"] === "ping") {
                	$("#type").text("{{ __('all.notification_center.ping') }}");
                	$("#target").html("<strong>{{ __('all.notification_center.ip_address') }}</strong><p id='ip' class='editable'>"+data["target"]+"</p>");
                }
            	if (data["type"] === "mass-heartbeat-loss") {
                	$("#type").text("{{ __('all.notification_center.mass_heartbeat_loss') }}");
                	$("#target").html("<p id='expression' class='editable'>"+data["target"]+"</p>");
                }
            	if (data["type"] === "biglan-command") {
                	$("#type").text("{{ __('all.notification_center.biglan_command') }}");
                	$("#target").html("<strong>WSID</strong><p id='wsid' class='editable'>"+data["target"]["wsid"]+"</p><strong>{{ __('all.notification_center.biglan_command') }}</strong><p id='command' class='editable'>"+data["target"]["command"]+"</p><strong>{{ __('all.notification_center.expression') }}</strong><p id='expression' class='editable'>"+data["target"]["expression"]+"</p>");
                }
            	if (data["type"] === "snmp") {
                	$("#type").text("SNMP");
                	$("#target").html("<strong>{{ __('all.notification_center.ip_address') }}</strong><p id='ip' class='editable'>"+data["target"]["ip"]+"</p><strong>OID</strong><p id='oid' class='editable'>"+data["target"]["oid"]+"</p><strong>{{ __('all.notification_center.expression') }}</strong><p id='expression' class='editable'>"+data["target"]["expression"]+"</p>");
                }
            	if (data["type"] === "sensor-value") {
                	$("#type").text("{{ __('all.notification_center.sensor_value') }}");
                	$("#target").html("<strong>{{ __('all.notification_center.expression') }}</strong><p id='expression' class='editable'>"+data["target"]+"</p><strong>{{ __('all.notification_center.unit') }}</strong><p id='unit' class='editable'>"+data["unit"]+"</p>");
                }
            
            	$("#description").text(data["description"]);
            	
            	
            });
        
        });
    
    	@if(auth()->user()->hasPermission('write-notification'))
    	$("body").on("click", ".editable", function(e) {
        	$(this).attr("contentEditable","true");
        });
    
    	$(document).on('blur', '.editable', function() {
        	$(this).attr("contentEditable","false");
        });
    
    	$("body").on("input", ".editable", function() {
    		$("#btn-save").removeClass("d-none");
        });
    
    	$("body").on("click", "#btn-save", function() {
    		payLoad = {};
        	payLoad["action"] = "saveNotification";
        	payLoad["id"] = notification_id;
        	payLoad["description"] = $("#description").text();
        	payLoad["alias"] = $("#alias").text();
        	payLoad["name"] = $("#name").text();
        	payLoad["type"] = notification_type;
        	payLoad["_token"] = $('meta[name=csrf-token]').attr('content');
        	
        	if(payLoad["type"] === "socket-polling" || payLoad["type"] === "ping" || payLoad["type"] == "snmp") {
            	payLoad["ip"] = $("#ip").text();
        	}
        	
        	if(payLoad["type"] === "sensor-value" || payLoad["type"] === "biglan-command" || payLoad["type"] === "mass-heartbeat-loss" || payLoad["type"] === "snmp") {
            	payLoad["expression"] = $("#expression").text();
            	if ($("#unit").text() != 'null' && $("#unit").text() != '') {
            		payLoad["unit"] = $("#unit").text();
                }
        	}
        	
        	if(payLoad["type"] === "biglan-command") {
            	payLoad["wsid"] = $("#wsid").text();
            	payLoad["command"] = $("#command").text();
            }
        
        	if(payLoad["type"] === "http-status-code") {
            	payLoad["website"] = $("#website").text();
            	payLoad["expression"] = $("#expression").text();
            }
        
        	if(payLoad["type"] === "socket-polling") {
            	payLoad["port"] = $("#port").text();
        	}
        
        	if(payLoad["type"] === "snmp") {
            	payLoad["oid"] = $("#oid").text();
        	}
        
        	var posting = $.post("{{ url('notifications/payload') }}", payLoad, "JSONP");
        	posting.done(function(data) {
            	if (data === "OK") {
          			$("#btn-save").addClass("d-none");
                	location.reload();
                }
            });
        
        });
        @endif
    	
    
    	@if(auth()->user()->hasPermission('delete-notification'))
        
        $("body").on("click", "#btn-delete", function() {
    		payLoad = {};
        	payLoad["action"] = "deleteNotification";
        	payLoad["id"] = notification_id;
        	payLoad["_token"] = $('meta[name=csrf-token]').attr('content');
        	var posting = $.post("{{ url('notifications/payload') }}", payLoad, "JSONP");
        	posting.done(function(data) {
            	if (data === "OK") {
          			location.reload();
                }
            });
        
        });
    	@endif
        
    	$('[data-toggle="popover"]').popover();
    	
	});
</script>                               
@endsection