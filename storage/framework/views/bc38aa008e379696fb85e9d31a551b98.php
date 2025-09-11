	<?php $__env->startSection('title'); ?>
		<?php echo e(__('all.users.permissions')); ?> | BigLan
	<?php $__env->stopSection(); ?>
	<?php $__env->startSection('content'); ?>
		 
        	<form class="form-horizontal row" method="POST" action="<?php echo e(url('users/savePermissions')); ?>">
        		
					<?php echo e(csrf_field()); ?>

					<div class="col-12 mt-2 mb-2">
                    		<?php if(auth()->user()->hasPermission('write-user-permissions')): ?>
                    			<button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> <?php echo e(__('all.button.save')); ?></button>
                    		<?php endif; ?>
                    		<a href="<?php echo e(url('users')); ?>" class="btn btn-light btn-sm"><i class="fas fa-arrow-circle-up"></i> <?php echo e(__('all.button.back')); ?></a>
                    </div>
                    <?php if(auth()->user()->hasPermission('write-user-permissions')): ?>
                    	<input type="hidden" name="token" value="<?php echo e($user->token); ?>">
                    <?php endif; ?>
		
        	<div class="col-12">
		
				<h5><?php echo e(__('all.users.user_permissions', ['username' => $user->username])); ?></h5>	
				<?php if(Session::has('success')): ?>
    				<div class="alert alert-success alert-dismissible fade show" role="alert">
       					<?php echo Session::get('success'); ?>

						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    						<span aria-hidden="true">&times;</span>
  						</button>
        			</div>
				<?php endif; ?>
			</div>
        
                    
                    <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			
			<div class="col-lg-3 col-12 mb-3">
				<div class="card shadow-sm">
					<div class="card-header">
						<?php echo e($permission["group-name"]); ?>

					</div>
					<div class="card-body">
                    	<?php $__currentLoopData = $permission["rights"]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $right): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    	<div class="form-check">
  							<input class="form-check-input" type="checkbox" name="permissions[]" value="<?php echo e($right['name']); ?>" id="<?php echo e($right['name']); ?>" <?php if(in_array($right['name'], $userPermissions)): ?> CHECKED <?php endif; ?> <?php if(!auth()->user()->hasPermission('write-user-permissions')): ?> DISABLED <?php endif; ?>>
  							<label class="form-check-label" for="<?php echo e($right['name']); ?>">
    							<?php echo e($right['alias']); ?>

  							</label>
						</div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</div>
				</div>
			</div>
			
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </form>
                
            
        		
	<?php $__env->stopSection(); ?>
	<?php $__env->startSection('inject-footer'); ?>
	<script type="text/javascript">
    	$(function() {
    		
        	
        });
    </script>
	<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/biglan/resources/views/users/permissions.blade.php ENDPATH**/ ?>