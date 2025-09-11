<?php $__env->startSection("title"); ?>
	<?php echo e(__('all.global_settings.title_global_settings_log')); ?> | BigLan
<?php $__env->stopSection(); ?>
<?php $__env->startSection("content"); ?>
	<div class="row mt-2">
		<div class="col-6">
			<?php echo e(csrf_field()); ?>

			<a href=<?php echo e(url('globalsettings')); ?> class="btn btn-sm btn-primary"><i class="far fa-arrow-alt-circle-left"></i> <?php echo e(__('all.button.back_global_settings')); ?></a>
		</div>
	</div>
	<div class="row mt-2">
		<div class="col-lg-12 col-sm-12">
			<div class="table-responsive">
				<?php if(count($settingsLogs) > 0): ?>
   				<table class="table table-striped table-hover">
						<thead class="thead-dark">
							<tr>
								<th><?php echo e(__('all.global_settings.th_datetime')); ?></th>
								<th><?php echo e(__('all.global_settings.th_settings')); ?></th>
            					<th><?php echo e(__('all.global_settings.th_event')); ?></th>
							</tr>
						</thead>
						<tbody>
					
   				<?php $__currentLoopData = $settingsLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
            					<td><?php echo e($log->created_at); ?></td>
								<td><?php echo e($log->globalsettings->name); ?></td>
								<td><?php echo $log->event; ?></td>
							</tr>
			        
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
					</table>
            	<?php else: ?>
            			<p><?php echo e(__('all.global_settings.global_settings_log_not_found')); ?></p>
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
    	
       
	});                                
    </script>                               
<?php $__env->stopSection(); ?>
<?php echo $__env->make("layout", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/biglan/resources/views/globalsettings/logs.blade.php ENDPATH**/ ?>