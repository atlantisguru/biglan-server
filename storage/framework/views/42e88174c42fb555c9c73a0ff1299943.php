<?php $__env->startSection("title"); ?>
	<?php echo e(__('all.notification_center.eventlog')); ?> | BigLan
<?php $__env->stopSection(); ?>
<?php $__env->startSection("content"); ?>
	<div class="row mt-2">
		
		<div class="col-6">
			<h4><?php echo e(__('all.notification_center.eventlog')); ?></h4>
			<?php echo e(csrf_field()); ?>

			<a href=<?php echo e(url('notifications')); ?> class="btn btn-sm btn-primary"><i class="far fa-arrow-alt-circle-left"></i> <?php echo e(__('all.button.back')); ?></a>
		</div>
	</div>
	<div class="row mt-2">
        
		<div class="col-lg-12 col-sm-12">
			<div class="table-responsive">
				<?php if(count($notificationLogs) == 0): ?>
   					<p><?php echo e(__('all.notification_center.notification_event_not_found')); ?></p>
				<?php endif; ?>
				<table class="table table-striped table-hover">
						<thead class="thead-dark">
							<tr>
								<th><?php echo e(__('all.notification_center.date_and_time')); ?></th>
								<th><?php echo e(__('all.notification_center.status')); ?></th>
            					<th><?php echo e(__('all.notification_center.name')); ?></th>
            					<th><?php echo e(__('all.notification_center.event')); ?></th>
								<th><?php echo e(__('all.notification_center.description')); ?></th>
							</tr>
						</thead>
						<tbody>
					
   				<?php $__currentLoopData = $notificationLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
            					<td><?php echo e($log->created_at); ?></td>
								<td class="text-center">
										<?php if(!isset($log->status)): ?>  
											<i class="fas fa-info text-muted" title="<?php echo e(__('all.notification_center.info')); ?>"></i>
										<?php else: ?>
            								<?php if($log->status == 1): ?>  
												<i class="fas fa-times text-danger" title="<?php echo e(__('all.notification_center.alert')); ?>"></i> 
											<?php else: ?>  
												<i class="fas fa-check text-success" title="<?php echo e(__('all.notification_center.idle')); ?>"></i>
											<?php endif; ?>
            							<?php endif; ?>
								</td>
								<td><?php echo e($log->notification->alias); ?></td>
								<td><?php echo e($log->event); ?></td>
								<td style="width: 50%"><?php echo strip_tags($log->description, '<pre>'); ?></td>
							</tr>
			        
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
					</table>
            
			</div>
		</div>
	</div>
    <div class="row mt-4">
		<div class="col-12">
           <?php echo e($notificationLogs->links()); ?>

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
    	
       
	});                                
    </script>                               
<?php $__env->stopSection(); ?>
<?php echo $__env->make("layout", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/biglan/resources/views/notifications/logs.blade.php ENDPATH**/ ?>