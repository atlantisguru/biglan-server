<?php $__env->startSection("title"); ?>
<?php echo e(__('all.notification_center.notification_center')); ?> | BigLan
<?php $__env->stopSection(); ?>
<?php $__env->startSection("content"); ?>
	<div class="row mt-2">
		<div class="col-12">
			<?php echo e(csrf_field()); ?>

			<?php if(auth()->user()->hasPermission('write-notification')): ?>
				<a href=<?php echo e(url('notifications/new')); ?> class="btn btn-sm btn-primary mr-2"><i class="fas fa-plus"></i> <?php echo e(__('all.button.new_notification')); ?></a>
            <?php endif; ?>
           	<?php if(auth()->user()->hasPermission('read-notifications-eventlog')): ?>
				<a href=<?php echo e(url('notifications/logs')); ?> class="btn btn-sm btn-outline-secondary mr-2"><i class="fas fa-info"></i> <?php echo e(__('all.notification_center.eventlog')); ?></a>
            <?php endif; ?>
            <a href=<?php echo e(url('notifications/dashboard')); ?> class="btn btn-sm btn-outline-secondary mr-2"><i class="fas fa-tachometer-alt"></i> <?php echo e(__('all.notification_center.dashboard_view')); ?></a>
		</div>
	</div>
	<div class="row mt-2">
		<div class="col-lg-12 col-sm-12">
			<div class="table-responsive">
				<?php if(count($notifications)>0): ?>
   					
				<table class="table table-striped table-hover">
						<thead class="thead-dark">
							<tr>
								<th class="text-center"><?php echo e(__('all.notification_center.status')); ?></th>
								<th><?php echo e(__('all.notification_center.name')); ?></th>
								<th><?php echo e(__('all.notification_center.type')); ?></th>
								<th><?php echo e(__('all.notification_center.parameters')); ?></th>
								<th><?php echo e(__('all.notification_center.description')); ?></th>
            					<th><?php echo e(__('all.notification_center.value')); ?></th>
            					<th class="text-center"><?php echo e(__('all.notification_center.active')); ?>?</th>
							</tr>
						</thead>
						<tbody>
					
   				<?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr class="notification-element <?php if($notification->monitored == 0): ?> text-muted <?php endif; ?>" data-id="<?php echo e($notification->id); ?>">
								<td class="text-center notification-element-status" data-field="triggered">
										<?php if($notification->triggered == 1): ?>  
											<i class="fas fa-times <?php if($notification->monitored == 0): ?> text-muted <?php else: ?> text-danger <?php endif; ?>" title="Alert"></i> 
										<?php else: ?> 
											<i class="fas fa-check <?php if($notification->monitored == 0): ?> text-muted <?php else: ?> text-success <?php endif; ?>" title="Idle"></i>
										<?php endif; ?>
								</td>
								<td><a href="javascript:void(0)" class="details" data-toggle="modal" data-target="#details"><?php echo e($notification->alias); ?></a></td>
								<td>
            						<?php
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
            						?>
            						<i class="fas fa-info-circle text-info" data-toggle="popover" data-trigger="hover" title="<?php echo e($type); ?>" data-content="<?php echo e($info); ?>"></i>
            						<?php echo e($type); ?>

								</td>
								<td><?php echo e((strlen($notification->target) > 50) ? mb_substr($notification->target, 0, 50, "UTF-8") . "..." : $notification->target); ?></td>
								<td><?php echo e((strlen($notification->description) > 50) ? mb_substr($notification->description, 0, 50, "UTF-8") . "..." : $notification->description); ?></td>
								<td data-field="value">
                                	<span title="<?php echo e($notification->updated_at); ?>"><?php echo e($notification->last_value); ?><?php if(isset($notification->unit)): ?><?php echo e($notification->unit); ?><?php endif; ?></span>
								</td>
                                <?php if(auth()->user()->hasPermission('write-notification')): ?>
									<td data-field="monitored" class="text-center"><?php if($notification->monitored): ?> <a href="javascript:void(0)" data-id="<?php echo e($notification->id); ?>" data-monitored="0" class="fas fa-bell text-primary notification-monitored-icon" title="<?php echo e(__('all.notification_center.activated')); ?>"></a> <?php else: ?> <a href="javascript:void(0)" data-id="<?php echo e($notification->id); ?>" data-monitored="1" class="fas fa-bell-slash text-muted notification-monitored-icon" title="<?php echo e(__('all.notification_center.disabled')); ?>"></a> <?php endif; ?> </td>
								<?php else: ?>
                                	<td data-field="monitored" class="text-center"><?php if($notification->monitored): ?> <i class="fas fa-bell text-primary notification-monitored-icon" title="<?php echo e(__('all.notification_center.activated')); ?>"></i> <?php else: ?> <i data-id="<?php echo e($notification->id); ?>" data-monitored="1" class="fas fa-bell-slash text-muted notification-monitored-icon" title="<?php echo e(__('all.notification_center.disabled')); ?>"></i> <?php endif; ?> </td>
                                <?php endif; ?>
                         	</tr>
			        
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
					</table>
            <?php else: ?>
                                <p><?php echo e(__('all.notification_center.notification_not_found')); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
<div class="modal" id="details">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h4 class="modal-title"><?php echo e(__('all.notification_center.notification_details')); ?></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body">
      	<div class="row mt-2">
        	<div class="col-12">
        		<strong><?php echo e(__('all.notification_center.alias')); ?></strong>
        		<p id="alias" class="editable"></p>
        		<input type="hidden" name="notification_id" id="notification_id">
        	</div>
        	<div class="col-12">
        		<strong><?php echo e(__('all.notification_center.name')); ?></strong>
        		<p id="name" class="editable"></p>
        	</div>
        	<div class="col-12">
        		<strong><?php echo e(__('all.notification_center.type')); ?></strong>
        		<p id="type"></p>
        	</div>
        	<div class="col-12">
        		<strong><?php echo e(__('all.notification_center.description')); ?></strong>
        		<p id="description" class="editable"></p>
        	</div>
        	<div class="col-12">
        		<strong><?php echo e(__('all.notification_center.parameters')); ?></strong>
        		<div id="target"></div>
        	</div>
        	<?php if(auth()->user()->hasPermission('delete-notification')): ?>
        	<div class="col-12 mt-4">
        		<a href="javascript:void(0)" id="btn-delete" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> <?php echo e(__('all.button.delete')); ?> (x2)</a>&nbsp;<a href="javascript:void(0)" id="btn-save" class="d-none btn btn-success btn-sm"><i class="fas fa-check"></i> <?php echo e(__('all.button.save')); ?></a>
        	</div>
        	<?php endif; ?>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo e(__('all.button.close')); ?></button>
      </div>

    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('inject-footer'); ?>
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
    
    	<?php if(auth()->user()->hasPermission('write-notification')): ?>
    	$("body").on("click", ".notification-monitored-icon", function(e) {
        	var confirm = window.confirm("<?php echo e(__('all.notification_center.are_you_sure_active')); ?>");
            if(!confirm) {
            	return;
            }
        	var element = $(this);
        	var nid = element.attr("data-id");
        	var monitored = element.attr("data-monitored");
           	var posting = $.post("<?php echo e(url('notifications/payload')); ?>", { '_token': $('meta[name=csrf-token]').attr('content'), action: "changeNotificationMonitoredStatus", nid: nid, monitored: monitored }, "JSONP");
        	posting.done(function(data) {
        		if(data == 1) {
                	element.removeClass("fa-bell-slash").removeClass("text-muted").addClass("fa-bell").addClass("text-primary");
                	element.attr('data-monitored', '0')
                	element.attr("title", "<?php echo e(__('all.notification_center.activated')); ?>");
                	element.parents(".notification-element").removeClass("text-muted");
                	element.parents(".notification-element").find(".fa-times").addClass("text-danger").removeClass("text-muted");
                	element.parents(".notification-element").find(".fa-check").addClass("text-success").removeClass("text-muted");
                }
            	if(data == 0) {
                	element.removeClass("fa-bell").removeClass("text-primary").addClass("fa-bell-slash").addClass("text-muted");
                	element.attr('data-monitored', '1');
                	element.attr("title", "<?php echo e(__('all.notification_center.disabled')); ?>");
                	element.parents(".notification-element").addClass("text-muted");
                	element.parents(".notification-element").find(".fa-times").removeClass("text-danger").addClass("text-muted");
                	element.parents(".notification-element").find(".fa-check").removeClass("text-success").addClass("text-muted");
                }
            });
        });
    	<?php endif; ?>
    
    	function getNotificationStatuses() {
        	var posting = $.post("<?php echo e(url('notifications/payload')); ?>", { '_token': $('meta[name=csrf-token]').attr('content'), action: 'getNotificationStatuses'}, "JSONP");
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
                
                	var alertText = "<?php echo e(__('all.notification_center.alert')); ?>";
                	var idleText = "<?php echo e(__('all.notification_center.idle')); ?>";
                	var activatedText = "<?php echo e(__('all.notification_center.activated')); ?>";
                	var disabledText = "<?php echo e(__('all.notification_center.disabled')); ?>";
                
                
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
        	
        	var posting = $.post("<?php echo e(url('notifications/payload')); ?>", payLoad, "JSONP");
        	posting.done(function(data) {
            	notification_type = data["type"];
            	$("#alias").text(data["alias"]);
            	$("#name").text(data["name"]);
            	
            	$("#type").text("<?php echo e(__('all.notification_center.other')); ?>");
                $("#target").html("<strong><?php echo e(__('all.notification_center.expression')); ?></strong><p>"+data["target"]+"</p>");
                
            	if (data["type"] === "http-status-code") {
                	$("#type").text("<?php echo e(__('all.notification_center.http_status_code')); ?>");
                	$("#target").html("<strong><?php echo e(__('all.notification_center.website')); ?></strong><p id='website' class='editable'>"+data["target"]["website"]+"</p><strong><?php echo e(__('all.notification_center.expression')); ?></strong><p id='expression' class='editable'>"+data["target"]["expression"]+"</p>");
                }
            
            	if (data["type"] === "socket-polling") {
                	$("#type").text("<?php echo e(__('all.notification_center.socket_polling')); ?>");
                	$("#target").html("<strong><?php echo e(__('all.notification_center.ip_address')); ?></strong><p id='ip' class='editable'>"+data["target"]["ip"]+"</p><strong><?php echo e(__('all.notification_center.port')); ?></strong><p id='port' class='editable'>"+data["target"]["port"]+"</p>");
                }
            	if (data["type"] === "ping") {
                	$("#type").text("<?php echo e(__('all.notification_center.ping')); ?>");
                	$("#target").html("<strong><?php echo e(__('all.notification_center.ip_address')); ?></strong><p id='ip' class='editable'>"+data["target"]+"</p>");
                }
            	if (data["type"] === "mass-heartbeat-loss") {
                	$("#type").text("<?php echo e(__('all.notification_center.mass_heartbeat_loss')); ?>");
                	$("#target").html("<p id='expression' class='editable'>"+data["target"]+"</p>");
                }
            	if (data["type"] === "biglan-command") {
                	$("#type").text("<?php echo e(__('all.notification_center.biglan_command')); ?>");
                	$("#target").html("<strong>WSID</strong><p id='wsid' class='editable'>"+data["target"]["wsid"]+"</p><strong><?php echo e(__('all.notification_center.biglan_command')); ?></strong><p id='command' class='editable'>"+data["target"]["command"]+"</p><strong><?php echo e(__('all.notification_center.expression')); ?></strong><p id='expression' class='editable'>"+data["target"]["expression"]+"</p>");
                }
            	if (data["type"] === "snmp") {
                	$("#type").text("SNMP");
                	$("#target").html("<strong><?php echo e(__('all.notification_center.ip_address')); ?></strong><p id='ip' class='editable'>"+data["target"]["ip"]+"</p><strong>OID</strong><p id='oid' class='editable'>"+data["target"]["oid"]+"</p><strong><?php echo e(__('all.notification_center.expression')); ?></strong><p id='expression' class='editable'>"+data["target"]["expression"]+"</p>");
                }
            	if (data["type"] === "sensor-value") {
                	$("#type").text("<?php echo e(__('all.notification_center.sensor_value')); ?>");
                	$("#target").html("<strong><?php echo e(__('all.notification_center.expression')); ?></strong><p id='expression' class='editable'>"+data["target"]+"</p><strong><?php echo e(__('all.notification_center.unit')); ?></strong><p id='unit' class='editable'>"+data["unit"]+"</p>");
                }
            
            	$("#description").text(data["description"]);
            	
            	
            });
        
        });
    
    	<?php if(auth()->user()->hasPermission('write-notification')): ?>
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
        
        	var posting = $.post("<?php echo e(url('notifications/payload')); ?>", payLoad, "JSONP");
        	posting.done(function(data) {
            	if (data === "OK") {
          			$("#btn-save").addClass("d-none");
                	location.reload();
                }
            });
        
        });
        <?php endif; ?>
    	
    
    	<?php if(auth()->user()->hasPermission('delete-notification')): ?>
        
        $("body").on("click", "#btn-delete", function() {
    		payLoad = {};
        	payLoad["action"] = "deleteNotification";
        	payLoad["id"] = notification_id;
        	payLoad["_token"] = $('meta[name=csrf-token]').attr('content');
        	var posting = $.post("<?php echo e(url('notifications/payload')); ?>", payLoad, "JSONP");
        	posting.done(function(data) {
            	if (data === "OK") {
          			location.reload();
                }
            });
        
        });
    	<?php endif; ?>
        
    	$('[data-toggle="popover"]').popover();
    	
	});
</script>                               
<?php $__env->stopSection(); ?>
<?php echo $__env->make("layout", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/biglan/resources/views/notifications/list.blade.php ENDPATH**/ ?>