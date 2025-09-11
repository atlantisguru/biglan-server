	<?php $__env->startSection('title'); ?>
		<?php echo e(__('all.workstations.monitors')); ?> | BigLan
	<?php $__env->stopSection(); ?>
	<?php $__env->startSection('content'); ?>

	<?php
		$userPermissions = auth()->user()->permissions();
	?>

	<div class="row mt-1">
	<div class="col-12">
	<div class="table-responsive">
		<table class="table table-hover" id="displays">
			<thead class="thead-dark">
				<tr>
					<th><?php echo e(__('all.workstations.workstation')); ?></th>
					<th><?php echo e(__('all.workstations.manufacturer_code')); ?></th>
             		<th><?php echo e(__('all.workstations.brand_model')); ?></th>
					<th><?php echo e(__('all.workstations.serial')); ?></th>
					<th><?php echo e(__('all.workstations.inventory_id')); ?></th>
					<th><?php echo e(__('all.workstations.manufacture_year')); ?></th>
					<th><?php echo e(__('all.workstations.registered')); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php $__currentLoopData = $displays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $display): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<tr>
						<td>
							<?php if(in_array('read-workstation', $userPermissions)): ?>
								<a href="<?php echo e(url('workstations/'.$display->wsid)); ?>" target="_blank"><?php echo e($display->workstation()->alias ?? ""); ?></a>
							<?php else: ?>
								<?php echo e($display->workstation()->alias ?? ""); ?>

							<?php endif; ?>
						</td>
             			<td><?php echo e($display->manufacturer ?? ""); ?></td>
             			<td><?php echo e($display->name ?? ""); ?></td>
						<td><?php echo e($display->serial ?? ""); ?></td>
						<td><?php echo e($display->inventory_id ?? ""); ?></td>
             			<td><?php echo e($display->year ?? ""); ?></td>
						<td><?php echo e($display->updated_at ?? ""); ?></td>
					</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</tbody>
		</table>
	</div>
	</div>
	</div>
	<?php $__env->stopSection(); ?>
    
    <?php $__env->startSection('inject-footer'); ?>
    	<link rel="stylesheet" type="text/css" href=<?php echo e(url("css/jquery.dataTables.min.css")); ?>>
        <script type="text/javascript" src=<?php echo e(url("js/jquery.dataTables.min.js")); ?>></script>
        <script type="text/javascript">
        	$(function() {
    				$('#displays').DataTable({
						"pageLength": 100
					});
			});
        </script>
		
    <?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/biglan/resources/views/workstations/displays.blade.php ENDPATH**/ ?>