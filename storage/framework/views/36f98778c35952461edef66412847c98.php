<?php $__env->startSection("title"); ?>
<?php echo e(__('all.network_printers.new_network_printer')); ?> | BigLan
<?php $__env->stopSection(); ?>
<?php $__env->startSection("content"); ?>
	
	<div class="row mt-2">
		<div class="col-lg-12 col-sm-12">
			<h4><?php echo e(__('all.network_printers.new_network_printer')); ?></h4>
			<form method="POST" class="form-horizontal" action="<?php echo e(url('networkprinters/save')); ?>">
        	<button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-save"></i> <?php echo e(__('all.button.save')); ?></button>&nbsp;<a href=<?php echo e(url('networkprinters')); ?> class="btn btn-sm btn-secondary"><i class="fas fa-times"></i> <?php echo e(__('all.button.back_without_save')); ?></a>
			<?php echo e(csrf_field()); ?>

			<div class="form-group row mt-2">
            	<div class="col-4">
					<span class="control-label"><?php echo e(__('all.network_printers.name')); ?></span>
				</div>
				<div class="col-4">
					<input type="text" name="alias" class="form-control">
				</div>
			</div>
        	<div class="form-group row">
				<div class="col-4">
					<span class="control-label"><?php echo e(__('all.network_printers.ip_address')); ?></span>
				</div>
				<div class="col-4">
					<input type="text" name="ip" class="form-control">
				</div>
			</div>
		</form>
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
    	
    	$('[data-toggle="popover"]').popover();
    	
	});
</script>                               
<?php $__env->stopSection(); ?>
<?php echo $__env->make("layout", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/biglan/resources/views/networkprinters/new.blade.php ENDPATH**/ ?>