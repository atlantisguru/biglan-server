<?php $__env->startSection("title"); ?>
<?php echo e(__('all.nav.global_settings')); ?> | BigLan
<?php $__env->stopSection(); ?>
<?php $__env->startSection("content"); ?>
	<div class="row mt-2">
		<div class="col-6">
			<?php if(auth()->user()->hasPermission('write-global-settings')): ?>
    			<button type="submit" form="settings" class="btn btn-sm btn-primary mr-2"><i class="fas fa-save"></i> <?php echo e(__('all.button.save')); ?></button>
			<?php endif; ?>
			<?php if(auth()->user()->hasPermission('read-global-settings-eventlog')): ?>
    			<a href=<?php echo e(url('globalsettings/logs')); ?> class="btn btn-sm btn-outline-secondary mr-2"><i class="fas fa-info"></i> <?php echo e(__('all.button.view_global_settings_log')); ?></a>
			<?php endif; ?>
		</div>
	</div>
	<div class="row mt-2">
		<div class="col-lg-12 col-sm-12">
			<div class="table-responsive">
				<?php if(count($settings) > 0): ?>
            	<form method="post" id="settings" action="globalsettings/save">
   				<?php echo e(csrf_field()); ?>

			
            	<table class="table table-striped table-hover" id="networkprinters">
						<thead class="thead-dark">
							<tr>
            					<th><?php echo e(__('all.global_settings.th_name')); ?></th>
            					<th><?php echo e(__('all.global_settings.th_value')); ?></th>
							</tr>
						</thead>
						<tbody>
					
   				<?php $__currentLoopData = $settings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr data-id=<?php echo e($setting->id); ?>>
								<td><strong><?php echo e($setting->name); ?></strong><br><i>(<?php echo e($setting->description); ?>)</i></td>
								<td>
                					<?php if(auth()->user()->hasPermission('write-global-settings')): ?>
    									<?php if($setting->type === "string"): ?>
                							<input type="text" name="<?php echo e($setting->name); ?>" class="form-control" value="<?php echo e($setting->value); ?>">
            							<?php endif; ?>
                						<?php if($setting->type === "boolean"): ?>
            								<div class="form-group">
  												<select name="<?php echo e($setting->name); ?>" class="form-control">
    												<option value="1" <?php if((int)$setting->value === 1): ?> selected <?php endif; ?>><?php echo e(__('all.button.yes')); ?></option>
    												<option value="0" <?php if((int)$setting->value === 0): ?> selected <?php endif; ?>><?php echo e(__('all.button.no')); ?></option>
  												</select>
											</div>
            							<?php endif; ?>
                					<?php else: ?>
                						<?php echo e($setting->value); ?>

									<?php endif; ?>
            					</td>
							</tr>
			    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
					</table>
            		</form>
            	<?php else: ?>
                	<p><?php echo e(__('all.global_settings.global_settings_not_found')); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('inject-footer'); ?>
    <style>
    	.table td {
            padding: 0.3rem;
        }

		
    </style>                            	
 	<script type="text/javascript">
    $(function() {
    	
    	$('[data-toggle="popover"]').popover();
    	
	});
</script>                               
<?php $__env->stopSection(); ?>
<?php echo $__env->make("layout", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/biglan/resources/views/globalsettings/list.blade.php ENDPATH**/ ?>