<div class="row mb-2">
	<div class="col-12">
		<a href="<?php echo e(url('/commands')); ?>" class="mr-4 text-decoration-none text-dark"><?php echo e(__('all.command_center.command_center')); ?></a>
		<?php if(auth()->user()->hasPermission('write-batch-command')): ?>
    		<a href="<?php echo e(url('/commands/new')); ?>" class="btn btn-primary btn-sm mr-2"><i class="fas fa-plus"></i> <?php echo e(__('all.command_center.new_command')); ?></a>
		<?php endif; ?>
		<?php if(auth()->user()->hasPermission('read-script')): ?>
    		<a href="<?php echo e(url('/commands/scripts')); ?>" class="btn btn-sm btn-outline-secondary mr-2"><i class="fas fa-terminal"></i> <?php echo e(__('all.command_center.script_storage')); ?></a>
    	<?php endif; ?>
	</div>
</div><?php /**PATH /var/www/biglan/resources/views/commands/header.blade.php ENDPATH**/ ?>