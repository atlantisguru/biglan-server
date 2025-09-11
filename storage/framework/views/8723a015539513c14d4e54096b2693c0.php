	<?php $__env->startSection('title'); ?>
		<?php echo e(__('all.dashboard.dashboard')); ?> | BigLan
	<?php $__env->stopSection(); ?>
	<?php $__env->startSection('content'); ?>
		<?php echo e(csrf_field()); ?>

		<?php if(auth()->user()->hasPermission('read-blocks')): ?>
		<div class="row mt-2">
			
			<div class="col-6 col-lg-2 mb-2 dashboard-header-block" id="workstations">
				<h6 class="text-nowrap"><a href="<?php echo e(url("workstations")); ?>"><?php echo e(__('all.dashboard.workstations')); ?></a> (<span id="ws-all"><span class="dynamic">-</span></span>)</h6>
				<div id="ws-online" style="display:none"><i class="fas fa-desktop text-success"></i> <a href="<?php echo e(url('workstations/filter/online')); ?>" target="blank"><span class="dynamic">-</span><small class="text-muted"> <?php echo e(__('all.dashboard.online')); ?></small></a></div>
				<div id="ws-idle" style="display:none"><i class="fas fa-desktop text-warning"></i> <a href="<?php echo e(url('workstations/filter/idle')); ?>" target="blank"><span class="dynamic">-</span><small class="text-muted"> <?php echo e(__('all.dashboard.locked')); ?></small></a></div>
				<div id="ws-offline" style="display:none"><i class="fas fa-desktop text-muted"></i> <a href="<?php echo e(url('workstations/filter/offline')); ?>" target="blank"><span class="dynamic">-</span><small class="text-muted"> <?php echo e(__('all.dashboard.offline')); ?></small></a></div>
				<div id="ws-heartbeatloss" style="display:none"><i class="fas fa-desktop text-danger"></i> <a href="<?php echo e(url('workstations/filter/heartbeatLoss')); ?>" target="blank"><span class="dynamic">-</span><small class="text-muted"> <?php echo e(__('all.dashboard.unreachable')); ?></small></a></div>
			</div>
			
			<div class="col-6 col-lg-2 mb-2 dashboard-header-block" id="privacy">
				<h6 class="text-nowrap"><?php echo e(__('all.dashboard.security_risks')); ?></h6>
					<div id="ip-conflict" style="display:none"><i class="fas fa-arrow-alt-circle-right text-danger blink"></i> <a href="<?php echo e(url('workstations/filter/ipconflict')); ?>" target="blank"><span class="dynamic">-</span><small class="text-muted"> <?php echo e(__('all.dashboard.ip_conflict')); ?></small></a></div>
					<div id="mac-conflict" style="display:none"><i class="fas fa-arrow-alt-circle-right text-danger blink"></i> <a href="<?php echo e(url('workstations/filter/macconflict')); ?>" target="blank"><span class="dynamic">-</span><small class="text-muted"> <?php echo e(__('all.dashboard.mac_conflict')); ?></small></a></div>
					<div id="teamviewer" style="display:none"><i class="fas fa-exclamation-triangle text-primary"></i> <a href="<?php echo e(url('workstations/filter/teamviewer')); ?>" target="blank"><span class="dynamic">-</span><small class="text-muted"> <?php echo e(__('all.dashboard.teamviewer_connected')); ?></small></a></div>
					<div id="anydesk" style="display:none"><i class="fas fa-exclamation-triangle text-danger"></i> <a href="<?php echo e(url('workstations/filter/anydesk')); ?>" target="blank"><span class="dynamic">-</span><small class="text-muted"> <?php echo e(__('all.dashboard.anydesk_connected')); ?></small></a></div>
					<div id="rdp" style="display:none"><i class="fab fa-windows text-primary"></i> <a href="<?php echo e(url('workstations/filter/rdp')); ?>" target="blank"><span class="dynamic">-</span><small class="text-muted"> RDP</small></a></div>
					<div id="vnc" style="display:none"><i class="fas fa-exclamation-triangle text-warning"></i> <a href="<?php echo e(url('workstations/filter/vnc')); ?>" target="blank"><span class="dynamic">-</span><small class="text-muted"> <?php echo e(__('all.dashboard.vnc_connected')); ?></small></a></div>
					<div id="usb" style="display:none"><i class="fab fa-usb text-muted"></i> <a href="<?php echo e(url('workstations/filter/usb')); ?>" target="blank"><span class="dynamic">-</span><small class="text-muted"> <?php echo e(__('all.dashboard.usb_connected')); ?></small></a></div>
					<!-- <div id="invalid-hostnames" style="display:none"><i class="fas fa-bug text-warning"></i> <a href="<?php echo e(url('workstations/filter/invalidhostnames')); ?>" target="blank"><span class="dynamic">-</span><small class="text-muted"> <?php echo e(__('all.dashboard.invalid_hostnames')); ?></small></a></div> -->
			</div>
			
			<div class="col-6 col-lg-2 mb-2 dashboard-header-block" id="printers">
				<h6 class="text-nowrap"><a href="<?php echo e(url("networkprinters")); ?>"><?php echo e(__('all.dashboard.network_printers')); ?></a> (<span id="printers"><span class="dynamic">-</span></span>)</h6>
					<?php if(filter_var($printerSupplyURL->value, FILTER_VALIDATE_URL)): ?>
						<div><i class="fas fa-external-link-square-alt"></i> <a href="<?php echo $printerSupplyURL->value; ?>" target="_blank"><?php echo e(__('all.dashboard.order_printer_supply')); ?></a></div>
					<?php endif; ?>
                    <div id="printers-black-toner-5" style="display:none"><i class="fas fa-print text-danger"></i> <span class="dynamic">-</span><small class="text-muted"> toner &le; 5%</small></div>
					<div id="printers-black-toner-20" style="display:none"><i class="fas fa-print text-warning"></i> <span class="dynamic">-</span><small class="text-muted"> toner &le; 10%</small></div>
			</div>
        
        	
        	<?php if(count($blocks) > 0): ?>
        		<?php $__currentLoopData = $blocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        			<div data-id="<?php echo e($block->id); ?>" class="col-6 col-lg-2 mb-2 dashboard-header-block custom-header-block">
							<?php if($block->type == "notifications"): ?>
        						<h6 class="text-nowrap"><?php echo e(__('all.dashboard.notifications')); ?></h6>
        						<?php $__currentLoopData = $block->data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        							<div id="<?php echo e($data["name"]); ?>"><?php if($data["triggered"] == 0): ?><i class="fas fa-check text-success" title="<?php echo e(__('all.notification_center.idle')); ?>"></i> <?php else: ?> <i class="fas fa-times text-danger" title="<?php echo e(__('all.notification_center.alert')); ?>"></i> <?php endif; ?> <small class="text-muted"> <?php echo e((strlen($data["alias"]) > 25) ? mb_substr($data["alias"], 0, 25, "UTF-8") . "..." : $data["alias"]); ?> <span class="notification-value"><?php echo e($data["value"]); ?></span><?php echo e($data["unit"]); ?> </small></div>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        					<?php endif; ?>
        					<?php if($block->type == "links"): ?>
        						<h6 class="text-nowrap"><?php echo e(__('all.dashboard.links')); ?></h6>
        						<?php $__currentLoopData = $block->data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        							<div><i class="fas fa-external-link-square-alt"></i> <a href='<?php echo e($data->url); ?>' target="_blank"><small><?php echo e($data->name); ?></small></a></div>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        					<?php endif; ?>
        			</div>
        		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        	<?php endif; ?>
        	<?php if(auth()->user()->hasPermission('write-blocks')): ?>
			<?php if(count($blocks) < 3): ?>
		    	<div class="col-6 col-lg-2 mb-2 d-none" id="add-header-block" data-toggle="modal" data-target="#add-block-modal">
        			<span><i class="fas fa-plus"></i> <?php echo e(__('all.dashboard.add_module')); ?></span>
        		</div>
          	<?php endif; ?>
        	<?php endif; ?>

		</div>
		<?php endif; ?>
        
        <a href="#" class="btn shadow btn-primary float-icon"  data-toggle="modal" data-target="#mobile" ><i class="fas fa-search"></i> <span class="vertical-separator">|</span> 2 x CTRL</a>
		
        <?php if(auth()->user()->hasPermission('read-eventstream') || auth()->user()->hasPermission('read-interventionstream')): ?>
  		<ul class="nav nav-tabs mt-2" id="myTab" role="tablist">
        	<?php if(auth()->user()->hasPermission('read-eventstream')): ?>
  			<li class="nav-item">
    			<a class="nav-link" id="eventlog-tab" data-toggle="tab" href="#eventlog" role="tab" aria-controls="eventlog" aria-selected="true"><strong><?php echo e(__('all.dashboard.eventlog')); ?></strong></a>
  			</li>
        	<?php endif; ?>
        	<?php if(auth()->user()->hasPermission('read-interventionstream')): ?>
  			<li class="nav-item">
    			<a class="nav-link" id="operationlog-tab" data-toggle="tab" href="#operationlog" role="tab" aria-controls="operationlog" aria-selected="true"><strong><?php echo e(__('all.dashboard.interventions')); ?></strong></a>
  			</li>
        	<?php endif; ?>
        </ul>
        <div class="tab-content" id="myTabContent">
  			<?php if(auth()->user()->hasPermission('read-eventstream')): ?>
  			<div class="tab-pane fade" id="eventlog" role="tabpanel" aria-labelledby="eventlog-tab">
        		<div class="row mt-3" style="height:450px;overflow-y:scroll;overflow-x: hidden">
					<div id="events" class="col-12"></div>
				</div>
        	</div>
			<?php endif; ?>
        	<?php if(auth()->user()->hasPermission('read-interventionstream')): ?>
  			<div class="tab-pane fade" id="operationlog" role="tabpanel" aria-labelledby="operationlog-tab">
        		<div class="row mt-3" style="height:450px;overflow-y:scroll;overflow-x: hidden">
					<div id="operations" class="col-12"><?php echo e(__('all.dashboard.interventions')); ?></div>
				</div>
        	</div>
        	<?php endif; ?>
		</div>
        <hr />
        <?php endif; ?>
       
     <?php if(auth()->user()->hasPermission('read-intervention-suggestions')): ?>   
     <?php if(count($interventions) > 0): ?>
		<div class="row mt-2">
        	<div class="col-12 mb-2">
			<span class="h5"><?php echo e(__('all.dashboard.suggested_interventions')); ?></span>
        	</div>
			<?php $__currentLoopData = $interventions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $intervention): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<div class=" col-lg-3 col-md-4 col-sm-12">
        	<div class="card shadow-sm mt-2">
        		<div class="card-body">
        			<button type="button" class="intervention close" data-dismiss="modal" aria-label="Close" data-hash="<?php echo e($intervention['hash']); ?>">×</button>
        			<h5 class="card-title">
        				<span class="badge badge-info"><?php echo e($intervention["count"]); ?></span> <?php echo $intervention["name"]; ?>

        			</h5>
    				<p class="card-text">
                    <?php if(isset($intervention["shortDescription"])): ?>
                    	<small class="text-muted"><?php echo e($intervention["shortDescription"] ?? ""); ?></small>
                    <?php endif; ?>
        				<ul>
        						<?php $__currentLoopData = $intervention["description"]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $desc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        							<li class="text-muted"><small><?php echo e($desc); ?></small></li>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        						</ul>
        			</p>
    				
        			<a target="_blank" href="<?php echo e(url('/workstations/filter/' . $intervention['hash'])); ?>" class="btn btn-sm btn-primary"><?php echo e(__('all.button.view')); ?></a>
  					
        		</div>
			</div>
        	</div>
        	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			
		</div>
     <?php endif; ?>   
     <?php endif; ?>
	<div class="row mb-4"></div>
<?php if(auth()->user()->hasPermission('write-blocks')): ?>
<!-- Dashboard Header Modal -->
<div class="modal fade" id="add-block-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo e(__('all.dashboard.add_module')); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row mt-2">  	
      		<div class="col">
          		<strong><?php echo e(__('all.dashboard.block_type')); ?>:</strong>
          		<select name="block-type-selector" id="block-type-selector" class="form-control">
          			<option value="notifications"><?php echo e(__('all.dashboard.notifications')); ?></option>
          			<option value="links"><?php echo e(__('all.dashboard.links')); ?></option>
          		</select>
          	</div>
        </div>
        <?php
              $notifications = \App\Models\Notifications::orderBy("type", "ASC")->orderBy("alias", "ASC")->get();  
        ?>
        <div class="row mt-2">
          	<div class="col">
        		<strong><?php echo e(__('all.dashboard.block_content')); ?>:</strong>
        	</div>
        </div>
        <div class="row mt-1 notifications-selector">
          	<div class="col">
                <?php for($i = 0; $i < 5; $i++): ?>
                <select class="form-control mb-2" name="select-notifications-block">
        			<option value="0">Empty</option>
                	<?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        				<option value="<?php echo e($notification->id); ?>"><?php echo e($notification->alias); ?></option>
        			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php endfor; ?>
            </div>
        </div>
        <div class="row links-selector">
          	<div class="col">
                <?php for($i = 0; $i < 5; $i++): ?>
                <div class="row mb-2 select-link-block">
                	<div class="col-6">
                		<input type="text" class="form-control" name="link-name" placeholder="<?php echo e(__('all.dashboard.link_name')); ?>">
                	</div>
                	<div class="col-6">
                		<input type="text" class="form-control" name="link-url" placeholder="<?php echo e(__('all.dashboard.link_url')); ?>">
                	</div>
                </div>
                <?php endfor; ?>
      		</div>
        </div>
      </div>
      <div class="modal-footer">
        <a href="javascript:void(0)" class="btn btn-primary" id="save-block"><?php echo e(__('all.button.save')); ?></a>
      	<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(__('all.button.close')); ?></button>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

	<?php $__env->stopSection(); ?>
                
    <?php $__env->startSection('inject-footer'); ?>
     <script type="text/javascript">
			

			$(function() {
            
            	initializeHeaderBlocks();
            	refresh();
            
				var timer = setInterval( function(){ refresh(); }, 30000);
            
            	$("#myTab .nav-link:first, #myTabContent .tab-pane:first").addClass("active").addClass("show");
            
            	function initializeHeaderBlocks() {
                
                	var countHeaderBlocks = $(".dashboard-header-block").length;
                	$(".links-selector").hide();
                	if(countHeaderBlocks < 6) {
                    	$("#add-header-block").removeClass("d-none");
                    } else {
                    	$("#add-header-block").addClass("d-none");
                    }
                
                }
            
            	$("#block-type-selector").on("change", function() {
                	var selectedValue = $(this).val();
                	$(".links-selector, .notifications-selector").hide();
                	switch(selectedValue) {
                    	case "notifications":
                    		$(".notifications-selector").show();
                    		break;
                    	case "links":
                    		$(".links-selector").show();
                    		break;
                    	default:
                    		break;
                    }
                });
            
            	$("#save-block").on("click", function() {
                
                	var blockType = $("#block-type-selector").val();
                	var payLoad = {};
                	payLoad['_token'] = $('meta[name="csrf-token"]').attr('content');
                	payLoad['action'] = "saveBlock";
                	payLoad['type'] = blockType;
                
                	switch(blockType) {
                    	case "notifications":
                    		var selectedNotifications = [];
							$('select[name="select-notifications-block"]').each(function() {
    							selectedNotifications.push({id: $(this).val()});
							});	
                    		payLoad['data'] = selectedNotifications;
                    		break;
                    	case "links":
                    		var linkPairs = [];
                    		$('input[name="link-name"]').each(function(index) {
                            	var linkName = $(this).val();
    							var linkUrl = $('input[name="link-url"]').eq(index).val();
    							if (linkName != "" && linkUrl != "") {
                            		linkPairs.push({name: linkName, url: linkUrl});
                                }
							});
							payLoad['data'] = linkPairs;
                    		break;
                    	default:
                    		break;
                    }
                
                	var posting = $.post("<?php echo e(url('dashboard/payload')); ?>", payLoad, "JSONP");
        			posting.done(function(data) {
            			if (data == "OK") {
                        	location.reload();
                        }
              	
        			});
                
                });
            
            	$(".custom-header-block").on("mouseenter", function() {
            			$('h6:first', this).append(" <a href='javascript:void(0)' class='delete-block'><i class='fas fa-trash-alt text-danger'></i></a>");
       			});
            
            	$(".custom-header-block").on("mouseleave", function() {
            			$('.delete-block').remove();
       			});
            
            	$("body").on("click", ".delete-block", function() {
                
                	payLoad = {};
                	payLoad['_token'] = $('meta[name="csrf-token"]').attr('content');
                	payLoad['action'] = "deleteBlock";
                	payLoad['id'] = $(this).parents(".custom-header-block").attr("data-id");
                	
                	var posting = $.post("<?php echo e(url('dashboard/payload')); ?>", payLoad, "JSONP");
        			posting.done(function(data) {
            			if (data == "OK") {
                        	location.reload();
                        }
              	
        			});
                
                });
            
            	$("button.intervention.close").on("click", function() {
                	var confirm = window.confirm("<?php echo e(__('all.dashboard.filter_are_you_sure_delete')); ?>");
            		if(!confirm) {
            			return;
            		}
                	payLoad = {};
                	payLoad['_token'] = $('meta[name="csrf-token"]').attr('content');
                	payLoad['action'] = "deleteFilter";
                	payLoad['hash'] = $(this).attr("data-hash");
                	
                	var posting = $.post("<?php echo e(url('workstations/payload')); ?>", payLoad, "JSONP");
        			posting.done(function(data) {
            			if (data == "OK") {
                        	location.reload();
                        }
              	
        			});
                
                });
                
                function refresh() {
   					
                	payLoad = {};
                	payLoad['_token'] = $('meta[name="csrf-token"]').attr('content');
                	payLoad['action'] = "viewGeneralStatistics";
                	var getStatistics = $.post("<?php echo e(url('dashboard/payload')); ?>", payLoad, "JSONP");
        			getStatistics.done(function(data, status) {
            		
                    	var notifications = data["blocks"];
                    	$.each(notifications, function(index, value) {
    						$.each(value["data"], function(index, value) {
                            	
                            	$("#" + value["name"] + " .notification-value").text(value["value"]);
                            
                            	if(value["triggered"] == 0) {
                                	$("#" + value["name"] + " i").attr("class", "fas fa-check text-success").attr("title", "<?php echo e(__('all.notification_center.idle')); ?>");
                                } else {
                                	$("#" + value["name"] + " i").attr("class", "fas fa-times text-danger").attr("title", "<?php echo e(__('all.notification_center.alert')); ?>");
                                }
                            
                            });
                        });
                    
                    	var dashboard = data["dashboard"];
                    	$.each(dashboard, function(index, value) {
                        	if(value > 0) {
                            	$("#" + index).show();
                            } else {
                            	$("#" + index).hide();
                            }
                        	$("#" + index + " .dynamic").text(value);
                        });
    				
                    	$("#events").html("");
                    	for(i = 0 ; i < data["event_stream"].length; i++) {
                        	if (data["event_stream"][i].type == "ws" && data["event_stream"][i].event != "crossplatform effect") {
                        		$("#events").append("<div class='event pl-1'>" + data["event_stream"][i].datetime + " (<a target='_blank' href='/workstations/" + data["event_stream"][i]["id"] + "'>" + data["event_stream"][i]["name"] + "</a>) " + ((data["event_stream"][i].event == "SYSTEM")?"<strong>":"") + data["event_stream"][i].event + ((data["event_stream"][i].description != null)?", " + data["event_stream"][i].description:"") + ((data["event_stream"][i].event == "SYSTEM")?"</strong>":"") + "</div>");
                            }
                            if (data["event_stream"][i].event == "crossplatform effect") {
                        		$("#events").append("<div class='event pl-1 bg-info text-white'>" + data["event_stream"][i].datetime + " (<a target='_blank' class='text-white' href='/workstations/" + data["event_stream"][i]["id"] + "'>" + data["event_stream"][i]["name"] + "</a>) " + data["event_stream"][i].event + ((data["event_stream"][i].description != null)?", " + data["event_stream"][i].description:"") + "</div>");
                            }
                        }
                    	$("#events .event:even").not(".bg-danger").not(".bg-success").not(".bg-info").addClass("bg-light");
                    	
                    	$("#operations").html("");
                    	var compare_date = "";
                    	for(i = 0 ; i < data["operation_stream"].length; i++) {
                        	var this_date = data["operation_stream"][i].timestamp.split('T')[0];
                        	if (compare_date != this_date) {
                            	$("#operations").append("<div class='header pl-1 mt-2'><strong>" + dayNameFromDate(this_date) + ", "+ this_date +"</strong></div>");
                            	compare_date = this_date;
                            }
                        	$("#operations").append("<div class='event pl-1'>" + " (<a target='_blank' href='/workstations/" + data["operation_stream"][i]["id"] + "'>" + data["operation_stream"][i]["name"] + "</a>) " + ((data["operation_stream"][i].description != null) ? data["operation_stream"][i].description:"") + "</div>");
                        }
                    	$("#operations .event:even").addClass("bg-light");
                    	
                    	function dayNameFromDate(date) {
                        
                        	var d = new Date(date);
							var weekday = new Array(7);
                        	weekday[0] = "<?php echo e(__('all.dashboard.sunday')); ?>";
							weekday[1] = "<?php echo e(__('all.dashboard.monday')); ?>";
							weekday[2] = "<?php echo e(__('all.dashboard.tuesday')); ?>";
							weekday[3] = "<?php echo e(__('all.dashboard.wednesday')); ?>";
							weekday[4] = "<?php echo e(__('all.dashboard.thursday')); ?>";
							weekday[5] = "<?php echo e(__('all.dashboard.friday')); ?>";
							weekday[6] = "<?php echo e(__('all.dashboard.saturday')); ?>";
                        	return weekday[d.getDay()];
                        
                        }
                    
                    	$('link[rel=icon]').remove();
    						var canvas = document.createElement('canvas');
    						canvas.width = 16;canvas.height = 16;
    						var ctx = canvas.getContext('2d');
    						var img = new Image();
    						img.src = 'favicon.ico';
    						img.onload = function() {
        						ctx.drawImage(img, 0, 0);
        						
                            	if (data["dashboard"]["ws-heartbeatloss"] > 0) {
                        			ctx.fillStyle = "#F60000";
                                	if (data["dashboard"]["ws-heartbeatloss"] < 10) {
                                    	ctx.fillRect(9, 6, 7, 10);
       									ctx.fillStyle = '#FFFFFF';
        								ctx.font = '10px sans-serif';
        								ctx.fillText(data["dashboard"]["ws-heartbeatloss"], 10, 14);
                                    } else {
                                    	ctx.fillRect(3, 6, 13, 10);
       									ctx.fillStyle = '#FFFFFF';
        								ctx.font = '10px sans-serif';
        								ctx.fillText(data["dashboard"]["ws-heartbeatloss"], 4, 14);
                                   	}
                                	
                                }
        						var link = document.createElement('link');
       							link.type = 'image/x-icon';
        						link.rel = 'icon';
        						link.href = canvas.toDataURL("image/x-icon");
        						document.getElementsByTagName('head')[0].appendChild(link);
                        	}
                    
                    }).fail(function(data, status) {
                    		
                    });
                
                }
            
            	function setCookie(name,value,days) {
    				var expires = "";
    				if (days) {
        				var date = new Date();
        				date.setTime(date.getTime() + (days*24*60*60*1000));
        				expires = "; expires=" + date.toUTCString();
   					}
    				document.cookie = name + "=" + (value || "")  + expires + "; path=/";
				}
				
            	function getCookie(name) {
    				var nameEQ = name + "=";
    				var ca = document.cookie.split(';');
    				for(var i=0;i < ca.length;i++) {
        				var c = ca[i];
        				while (c.charAt(0)==' ') c = c.substring(1,c.length);
        				if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    				}
    				return null;
				}
            });
		
		</script>
        <style type="text/css">
        	.blink{
		animation: blink 3s infinite;
	 }
	 
	 @keyframes blink{
		0%{opacity: 1;}
		50%{opacity: 0;}
		100%{ opacity: 1;}
	 }

	#add-header-block {
    	color: #DDD;
    	text-align: center;
    	display: grid;
  		align-items: center;
    }

	#add-header-block span {
    	font-size: 14pt;
    }

	#add-header-block:hover {
    	color: #777;
        cursor: pointer;
    }

	.float-icon {
            position: fixed;
    		font-size: 14pt;
            bottom: 40px;
            right: 40px;
    		z-index: 1000;
    }

	.vertical-separator {
    	color: #AAA;
    }

        </style>
    <?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/biglan/resources/views/dashboard.blade.php ENDPATH**/ ?>