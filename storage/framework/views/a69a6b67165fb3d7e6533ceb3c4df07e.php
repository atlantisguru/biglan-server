<?php $__env->startSection("title"); ?>
<?php echo e(__('all.notification_center.notification_center')); ?> | BigLan
<?php $__env->stopSection(); ?>
<?php $__env->startSection("content"); ?>
	<div class="row mt-2">
		<div class="col-12">
			<?php echo e(csrf_field()); ?>

			<a href=<?php echo e(url('notifications')); ?> class="btn btn-sm btn-primary mr-2"><i class="far fa-arrow-alt-circle-left"></i> <?php echo e(__('all.button.back')); ?></a>
            <?php if(auth()->user()->hasPermission('read-notifications-eventlog')): ?>
            	<a href=<?php echo e(url('notifications/logs')); ?> class="btn btn-sm btn-outline-secondary"><i class="fas fa-info"></i> <?php echo e(__('all.notification_center.eventlog')); ?></a>
			<?php endif; ?>
        </div>
	</div>
	<div class="row mt-2">
		<div class="col-lg-12 col-sm-12">
			
				<?php if(count($notifications) > 0): ?>
            		<div class="row">
            			
   					<?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            			<div class="col-lg-3 col-sm-6">
            			<?php if($notification->monitored): ?>
            				<?php if($notification->triggered): ?>
            					<button data-id="<?php echo e($notification->id); ?>" type="button" class="btn btn-sm btn-danger m-1">
            						 <i class="fas fa-times"></i> <?php echo e($notification->alias); ?> <?php if($notification->type == "sensor-value"): ?> <span class="value badge badge-light"><?php echo e($notification->last_value); ?></span> <?php endif; ?>
								</button>
            				<?php else: ?>
            					<button data-id="<?php echo e($notification->id); ?>" type="button" class="btn btn-sm btn-success m-1">
  									<i class="fas fa-check"></i> <?php echo e($notification->alias); ?> <?php if($notification->type == "sensor-value"): ?> <span class="value badge badge-light"><?php echo e($notification->last_value); ?></span> <?php endif; ?>
								</button>
            				<?php endif; ?>	
            			<?php else: ?>
            				<button data-id="<?php echo e($notification->id); ?>" type="button" class="btn btn-sm btn-secondary m-1">
  								<i class="fas fa-bell-slash"></i> <?php echo e($notification->alias); ?> <?php if($notification->type == "sensor-value"): ?> <span class="value badge badge-light"><?php echo e($notification->last_value); ?></span> <?php endif; ?>
							</button>
                        <?php endif; ?>
            		</div>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            		</div>
            
            	<?php else: ?>
                	<p><?php echo e(__('all.notification_center.notification_not_found')); ?></p>
                <?php endif; ?>
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
    
    	function getNotificationStatuses() {
        	var posting = $.post("<?php echo e(url('notifications/payload')); ?>", { '_token': $('meta[name=csrf-token]').attr('content'), action: 'getNotificationStatuses'}, "JSONP");
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make("layout", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/biglan/resources/views/notifications/dashboard.blade.php ENDPATH**/ ?>