	<?php $__env->startSection('title'); ?>
		<?php echo e(__('all.users.activities')); ?> | BigLan
	<?php $__env->stopSection(); ?>
	<?php $__env->startSection('content'); ?>
		<div class="row mt-2">
			<div class="col-12">
				<a href="<?php echo e(url('users')); ?>" class="btn btn-light btn-sm"><i class="fas fa-arrow-circle-up"></i> <?php echo e(__('all.button.back')); ?></a>
			</div>
		</div>
		<div class="row mt-2">
			<div class="col-12">
				<h5><?php echo e(__('all.users.user_activities', [ 'username' => $user->username])); ?></h5>	
			</div>
		</div>
		<div class="row mt-2">
			<div class="col-12">
			<div class="table-responsive table-hover table-bordered table-striped table-sm">
			<table class="table">
				<tr>
					<th><?php echo e(__('all.users.user_activities_datetime')); ?></th>
					<th><?php echo e(__('all.users.user_activities_event')); ?></th>
					<th><?php echo e(__('all.users.user_activities_description')); ?></th>
					<th><?php echo e(__('all.users.user_activities_ip')); ?></th>
					<th><?php echo e(__('all.users.user_activities_info')); ?></th>
				</tr>
			<?php $__currentLoopData = $userActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<tr>
					<td><?php echo e($activity->created_at); ?></td>
					<td><?php echo e($activity->activity); ?></td>
					<td><?php echo e($activity->description); ?></td>
					<td><?php echo e($activity->ip); ?></td>
					<td><?php echo e($activity->browser); ?></td>
				</tr>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</table>
			</div>
			</div>
        </div>
         <div class="row mt-2">
		<div class="col-12">
        	<?php echo e($userActivities->appends(Request::query())->links()); ?>

    	</div>
    </div>    

				
	<?php $__env->stopSection(); ?>
	<?php $__env->startSection('inject-footer'); ?>
	<script type="text/javascript">
    	$(function() {
    		
        	
        });
    </script>
	<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/biglan/resources/views/users/activities.blade.php ENDPATH**/ ?>