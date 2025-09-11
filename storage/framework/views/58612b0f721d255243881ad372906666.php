	<?php $__env->startSection('title'); ?>
		<?php echo e(__('all.network_devices.network_devices')); ?> | BigLan
	<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<?php
	$userPermissions = auth()->user()->permissions();
?>

	<div class="row mt-2">
	<div class="col-12">
		<?php echo e(csrf_field()); ?>

		<?php if(in_array('write-network-device', $userPermissions)): ?>
			<a href="<?php echo e(url('networkdevices/new')); ?>" class="btn btn-primary btn-sm mb-2 mr-2"><i class="fas fa-plus"></i> <?php echo e(__('all.button.new_network_device')); ?></a>
        	<span class="badge badge-light mr-2"><i class="fas fa-info-circle"></i> <?php echo e(__('all.network_printers.helper')); ?></span><br>
		<?php endif; ?>
     <div class="table-responsive">
		<table class="table table-hover table-striped" id="networkdevices">
			<thead class="thead-dark">
				<tr>
					<th></th>
        			<th><?php echo e(__('all.network_devices.name')); ?></th>
					<th><?php echo e(__('all.network_devices.brand_model')); ?></th>
					<th class="text-center"><?php echo e(__('all.network_devices.serial')); ?></th>
					<th class="text-center"><?php echo e(__('all.network_devices.type')); ?></th>
					<th class="text-center"><?php echo e(__('all.network_devices.ip_address')); ?></th>
					<th class="text-center"><?php echo e(__('all.network_devices.mac_address')); ?></th>
					<th class="text-center"><?php echo e(__('all.network_devices.ports')); ?></th>
                	<th class="text-center"><?php echo e(__('all.network_devices.speed')); ?></th>
                	
				</tr>
			</thead>
			<tbody>
				<?php $__currentLoopData = $networkdevices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $networkdevice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<tr data-id="<?php echo e($networkdevice->id); ?>" <?php if($networkdevice->active != 1): ?> class="text-muted" <?php endif; ?>>
						<td><a  href="javascript:void(0)" class="details" data-panel="details" data-toggle="modal" data-target="#deviceDetails"><i class="fas fa-chart-bar"></i></a></td>
						<td data-field="alias" class="editable"><?php echo e($networkdevice->alias); ?></td>
						<td data-field="hardware" class="editable"><?php echo e($networkdevice->hardware); ?></td>
						<td data-field="serial" class="text-center editable"><?php echo e($networkdevice->serial); ?></td>
						<td data-field="type" class="text-center editable"><?php echo e($networkdevice->type); ?></td>
						<td data-field="ip" class="text-center editable"><?php if($networkdevice->ip != null): ?> <a href="<?php echo e(url('http://'.$networkdevice->ip)); ?>" target="_blank"><?php echo e($networkdevice->ip); ?></a> <?php endif; ?></td>
						<td data-field="mac" class="text-center editable"><?php echo e($networkdevice->mac); ?></td>
						<td data-field="ports" class="text-center editable"><?php echo e($networkdevice->ports); ?></td>
						<td data-field="speed" class="text-center editable"><?php echo e($networkdevice->speed ?? ""); ?></td>
					</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</tbody>
		</table>
	</div>
	</div>
	</div>

 					<div class="modal fade" id="deviceDetails" tabindex="-1" role="dialog">
  						<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    						<div class="modal-content">
      							<div class="modal-header">
        							<h5 class="modal-title" id="device-name">...</h5>
        							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          								<span aria-hidden="true">&times;</span>
        							</button>
      							</div>
      							<div class="modal-body">
        							    	<div class="row">
                                        		<div class="col-3">
                                        			<span><strong><?php echo e(__('all.network_devices.name')); ?></strong></span>
                                        			<p id="name">-</p>
                                        		</div>
                                        		<div class="col-3">
                                        			<span><strong><?php echo e(__('all.network_devices.brand_model')); ?></strong></span>
                                        			<p id="brand">-</p>
                                        		</div>
                                        		<div class="col-3">
                                        			<span><strong><?php echo e(__('all.network_devices.type')); ?></strong></span>
                                        			<p id="type">-</p>
                                        		</div>
                                        		<div class="col-3">
                                        			<span><strong><?php echo e(__('all.network_devices.serial')); ?></strong></span>
                                        			<p id="serial">-</p>
                                        		</div>
                                        	</div>
                                        	<div class="row">
                                        		<div class="col-3">
                                        			<span><strong><?php echo e(__('all.network_devices.ip_address')); ?></strong></span>
                                        			<p id="ip">-</p>
                                        		</div>
                                        		<div class="col-3">
                                        			<span><strong><?php echo e(__('all.network_devices.mac_address')); ?></strong></span>
                                        			<p id="mac">-</p>
                                        		</div>
                                        		<div class="col-3">
                                        			<span><strong><?php echo e(__('all.network_devices.ports')); ?></strong></span>
                                        			<p id="ports">-</p>
                                        		</div>
                                        		<div class="col-3">
                                        			<span><strong><?php echo e(__('all.network_devices.speed')); ?></strong></span>
                                        			<p id="speed">-</p>
                                        		</div>
                                        	</div>
                                        	<div class="row">
                                        		<div class="col-3">
                                        			<span><strong><?php echo e(__('all.network_devices.network')); ?></strong></span>
                                        			<p id="network">-</p>
                                        		</div>
                                        		<div class="col-3">
                                        			<span><strong><?php echo e(__('all.network_devices.actions')); ?></strong></span>
                                        			<?php if(in_array('delete-network-device', $userPermissions)): ?>
                                        				<p><a href="javascript:void(0)" data-id="" id="btn-archive" class="btn btn-danger btn-sm mt-2"><i class="fas fa-trash"></i> <?php echo e(__('all.button.archive')); ?> (x2)</a></p>
                                        				<p><a href="javascript:void(0)" data-id="" id="btn-active" class="btn btn-primary btn-sm mt-2"><i class=""></i> </a></p>
                                        			<?php endif; ?>
                                        		</div>
                                        	</div>
                                			
								<div class="modal-footer">
        							<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(__('all.button.close')); ?></button>
      							</div>
    						</div>
  						</div>
					</div>


	<?php $__env->stopSection(); ?>
<?php $__env->startSection('inject-footer'); ?>
<link rel="stylesheet" type="text/css" href=<?php echo e(url("css/jquery.dataTables.min.css")); ?>>
    	<style type="text/css">
    		.table td {
            	padding: 0.2rem!important;
        	}
    	</style>
        <script type="text/javascript" src=<?php echo e(url("js/jquery.dataTables.min.js")); ?>></script>
        <script type="text/javascript">
        	$(function() {
            	var editValue = "";
            	var payLoad = {};
            	
    			$('#networkdevices').DataTable({"pageLength" : 50});
            
            	<?php if(in_array('write-network-device', $userPermissions)): ?>
            	$(document).on('mouseover', 'table#networkdevices .editable', function() {
                	$(this).css("cursor","cell");
                });
            
            	$(document).on('mouseleave', 'table#networkdevices .editable', function() {
                	$(this).css("cursor","default");
                });
            	
            	function saveData(ndid, ndfield, ndvalue) { 
            		payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            		payLoad['id'] = ndid;
            		payLoad['action'] = 'updateData';
                	payLoad['field'] = ndfield;
            		payLoad['value'] = ndvalue;
                	var updateNetworkdevice = $.post("<?php echo e(url('networkdevices')); ?>", payLoad, "JSONP");
        			updateNetworkdevice.done(function(data) {
            			if(data == "OK") {
                			var saveValue = $('.editing').val();
                        	$('.editing').parents("td").text(saveValue);
                        } else {
            				$('.editing').parents("td").text(editValue);
                		}
            		});
                }
            
            	$(document).on("dblclick", 'table#networkdevices .editable', function() {
                	$(this).removeClass("editable");
                	editValue = $(this).text();
                   	$(this).html("<input class='form-control editing' type='text' />");
                	$('.editing').val(editValue).focus();
                });
            
            	            	
            	$(document).on('blur', '.editing', function() {
                   	$('.editing').parents("td").text(editValue).addClass("editable");
                });
            
            	$(document).on('keydown', '.editing', function(e) {
                	if(e.which === 13 && e.shiftKey) {
                    	$(this).parents("td").addClass("editable");
                    	var id = $('.editing').parents("tr").attr("data-id");
                    	var field = $('.editing').parents("td").attr("data-field");
                    	var value = $('.editing').val();
                    	saveData(id, field, value);
                    }
                	
                	if(e.which === 27) {
                	   	$('.editing').parents("td").text(editValue).addClass("editable");
                	}
                });
            <?php endif; ?>
            
            <?php if(in_array('delete-network-device', $userPermissions)): ?>
            $(document).on("dblclick", '#btn-archive', function() {
        		payLoad = {};
        		var id = $(this).attr("data-id");
        		payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            	payLoad['action'] = 'archiveNetworkDevice';
            	payLoad['id'] = id;
            	var deleteNetworkDevice = $.post("<?php echo e(url('networkdevices')); ?>", payLoad, "JSONP");
        		deleteNetworkDevice.done(function() {
            		location.reload();
            	});
            
            });
            <?php endif; ?>
            
             $(document).on("click", '#btn-active', function() {
        		payLoad = {};
        		var id = $(this).attr("data-id");
        		payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            	payLoad['action'] = 'activeNetworkDevice';
            	payLoad['id'] = id;
            	var activeNetworkDevice = $.post("<?php echo e(url('networkdevices')); ?>", payLoad, "JSONP");
        		activeNetworkDevice.done(function() {
            		location.reload();
            	});
            
            });
            
            $(document).on("click", '.details', function() {
        
        		$("#name, #brand, #type, #serial, #ip, #mac, #ports, #speed, #network").text("-");
        		payLoad = {};
        		var id = $(this).parents("tr").attr("data-id");
        		$("#btn-archive, #btn-active").attr("data-id", id);
        		payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            	payLoad['id'] = id;
            	payLoad['action'] = 'getNetworkDevice';
            	var showNetworkDevice = $.post("<?php echo e(url('networkdevices')); ?>", payLoad, "JSONP");
        		showNetworkDevice.done(function(data) {
                	console.log(data);
            		$("#device-name").text(data["name"]);
            		$("#name").text(data["name"]);
            		$("#brand").text(data["brand"]);
            		$("#type").html(data["type"]);
            		$("#serial").text(data["serial"]);
            		$("#ip").text(data["ip"]);
            		$("#mac").text(data["mac"]);
            		$("#ports").text(data["ports"]);
            		$("#speed").text(data["speed"]);
            		$("#network").text(data["network"]);
                	if(data["active"] == 1) {
                		$("#btn-active").text("<?php echo e(__('all.button.deactivate')); ?>");
                    } else {
                    	$("#btn-active").text("<?php echo e(__('all.button.activate')); ?>");
                    }
            	});
        	});
            
       	});
            
			
        </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/biglan/resources/views/networkdevices/list.blade.php ENDPATH**/ ?>