	<?php $__env->startSection('title'); ?>
		<?php echo e(__('all.workstations.printers')); ?> | BigLan
	<?php $__env->stopSection(); ?>
	<?php $__env->startSection('content'); ?>

	<?php
		$userPermissions = auth()->user()->permissions();
	?>
	
    <div class="row mt-1">
	<div class="col-12">
	<div class="table-responsive">
		<table class="table table-hover" id="printers">
			<thead class="thead-dark">
				<tr>
					<th><?php echo e(__('all.workstations.workstation')); ?></th>
					<th><?php echo e(__('all.workstations.name')); ?></th>
             		<th>Port</th>
					<th><?php echo e(__('all.workstations.registered')); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php $__currentLoopData = $printers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $printer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<?php
						$workstation = $printer->workstation();
					?>

					<tr>
						<td>
						<?php if(in_array('read-workstation', $userPermissions)): ?>
                    		<a href="<?php echo e(url('workstations/'.$printer->wsid)); ?>" target="_blank"><?php echo e($workstation->alias ?? $printer->id); ?></a>
						<?php else: ?>
							<?php echo e($workstation->alias ?? $printer->id); ?>

						<?php endif; ?>
						</td>
             			<td><?php echo e($printer->name ?? ""); ?></td>
						<td><?php echo e($printer->port ?? ""); ?></td>
						<td><?php echo e($printer->updated_at ?? ""); ?></td>
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
    				$('#printers').DataTable({
						"pageLength": 100
					});
			});
        </script>
		
    <?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/biglan/resources/views/workstations/printers.blade.php ENDPATH**/ ?>