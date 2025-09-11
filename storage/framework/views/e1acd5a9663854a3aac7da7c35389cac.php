	<?php $__env->startSection('title'); ?>
		<?php echo e(__('all.users.users')); ?> | BigLan
	<?php $__env->stopSection(); ?>
	<?php $__env->startSection('content'); ?>
		<div class="row mt-2">
			<div class="col-12">
			<div class="table-responsive table-hover table-borderless">
			<table class="table">
				<tr>
					<th class="text-center"><?php echo e(__('all.users.status')); ?></th>
					<th><?php echo e(__('all.users.username')); ?></th>
					<th><?php echo e(__('all.users.email')); ?></th>
					<th><?php echo e(__('all.users.last_login')); ?></th>
					<th></th>
				</tr>
			<?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<tr>
					<td class="text-center">
						<?php if($user->confirmed): ?>
							<i class="fas fa-check text-success"></i>
						<?php else: ?>
							<i class="fas fa-times text-muted"></i>
						<?php endif; ?>
					</td>
					<td><?php echo e($user->username); ?></td>
					<td><?php echo e($user->email); ?></td>
					<td><?php echo e($user->last_login); ?></td>
					<td>
							<?php if(auth()->user()->hasPermission('read-user-permissions')): ?>
								<a class="btn btn-sm btn-primary mr-1" href="<?php echo e(url('users/permissions/'.$user->token)); ?>"><?php echo e(__('all.users.permissions')); ?></a>
							<?php endif; ?>
							<?php if(auth()->user()->hasPermission('read-user-activities')): ?>
								<a class="btn btn-sm btn-primary mr-1" href="<?php echo e(url('users/activities/'.$user->token)); ?>"><?php echo e(__('all.users.activities')); ?></a>
							<?php endif; ?>
							<?php if($user->id != auth()->user()->id && auth()->user()->hasPermission('write-user-status')): ?>
								<?php if($user->confirmed): ?>
                        			<a href="<?php echo e(url('users/status/' .$user->token)); ?>" class="btn btn-sm btn-light mr-1" href="#"><?php echo e(__('all.users.disable')); ?></a>
                        		<?php else: ?>
                        			<a href="<?php echo e(url('users/status/' .$user->token)); ?>" class="btn btn-sm btn-light mr-1" href="#"><?php echo e(__('all.users.enable')); ?></a>
                        		<?php endif; ?>
							<?php endif; ?>
                        
					</td>
				</tr>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</table>
			</div>
			</div>
        </div>
         <div class="row mt-2">
		<div class="col-12">
        	<?php echo e($users->appends(Request::query())->links()); ?>

    	</div>
    </div>    

				
	<?php $__env->stopSection(); ?>
	<?php $__env->startSection('inject-footer'); ?>
	<script type="text/javascript">
    	$(function() {
    		
        	
        });
    </script>
	<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/biglan/resources/views/users/users.blade.php ENDPATH**/ ?>