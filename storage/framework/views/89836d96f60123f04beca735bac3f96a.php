	<?php $__env->startSection('title'); ?>
		<?php echo e(__('all.help.help')); ?> | BigLan
	<?php $__env->stopSection(); ?>
	<?php $__env->startSection('content'); ?>
		<?php echo e(csrf_field()); ?>

     	<div class="row text-center">
        	<div class="col-12">
     			<h4 class="mt-2 mb-2"><?php echo e(__('all.help.help')); ?></h1>
        	</div>
     	</div>
        <div class="row text-center">
        	<div class="col-12 d-flex justify-content-center align-items-center">
    			<?php echo e(__('all.help.helper_text')); ?>

        	</div>
     	</div>
    <?php $__env->stopSection(); ?>
                
    <?php $__env->startSection('inject-footer'); ?>
     <script type="text/javascript">
			

			$(function() {});
	</script>
    <?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/biglan/resources/views/help.blade.php ENDPATH**/ ?>