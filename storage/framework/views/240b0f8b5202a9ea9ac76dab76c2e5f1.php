<?php $__env->startSection('title'); ?>
<?php echo e(__('all.command_center.command_center')); ?> | BigLan
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<?php
	$userPermissions = auth()->user()->permissions();
?>

	<div class="row mt-2">
		<div class="col-12">
        	<?php echo $__env->make('commands.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>
	<div class="row mt-2">
		<div class="col-12">
        	<?php if($commands->count() == 0): ?>
        		<div><?php echo e(__('all.command_center.no_command_found')); ?></div>
        	<?php endif; ?>
        	<?php $__currentLoopData = $commands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $command): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<?php
				$done = $command->commands()->whereNotNull("result")->count();
				$waiting = $command->commands()->whereNull("result")->count();
				$all = $done+$waiting;
			?>
			<?php if($all > 0): ?>
        	<div class="card mb-2">
            <div class="card-body">
        		<div>
        			<div class="row mt-1 mb-1">
                    	<div class="col-12">
							<h2 class="h5"><span class="badge badge-info"><?php echo e(floor(($done/($done+$waiting))*100)); ?>%</span> <?php if($command->blocked == 1): ?> <span class="badge badge-danger"><?php echo e(__('all.command_center.interrupted')); ?></span>  <?php endif; ?> <a href="<?php echo e(url('/commands/command/'.$command->id)); ?>" class="text-dark"><?php echo e($command->alias); ?></a> </h2>
                		</div>
                    </div>
                	<div class="row">
                    	<div class="col-12">
                    		<small class="text-muted text-uppercase"><?php echo e($command->user->username); ?> | <?php echo e(__('all.command_center.created')); ?>: <?php echo e(\Carbon\Carbon::parse($command->created_at)->format("Y.m.d.")); ?> </small>
                		</div>
                    </div>
                	<div class="row mt-1">
                    	<div class="col-12">
                        	<p><strong><?php echo e(__('all.command_center.earliest_run_time')); ?>:</strong> <?php echo e(\Carbon\Carbon::parse($command->run_after_at)->format("Y.m.d. H:i")); ?></p>
                        	<p><strong><?php echo e(__('all.command_center.script')); ?>:</strong><br /><i><?php echo $command->command; ?></i></p>
                			<p><strong><?php echo e(__('all.command_center.progress')); ?>:</strong> <?php echo e($done); ?> / <?php echo e($done+$waiting); ?> (<?php echo e(floor(($done/($done+$waiting))*100)); ?>%)</p>
                		</div>
                    </div>
                	<div class="row mb-1">
                    	<div class="col-12">
                    		<a href="<?php echo e(url('/commands/command/'.$command->id)); ?>" class="btn btn-outline-secondary"><?php echo e(__('all.command_center.details')); ?></a>
							<?php if(in_array('write-batch-command', $userPermissions)): ?>
								<?php if($command->blocked == 0 && $waiting > 0): ?>
									<a href="javascript:" class="btn btn-danger emergency-stop-btn" data-id="<?php echo e($command->id); ?>"><?php echo e(__('all.command_center.emergency_stop')); ?></a>
								<?php endif; ?>	
							<?php endif; ?> 
                		</div>
                    </div>
                </div>
            </div></div>
            <?php endif; ?>
        	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            
        </div>
    </div>
	<div class="row mt-4">
		<div class="col-12">
           <?php echo e($commands->links()); ?>

		</div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('inject-footer'); ?>
	<script type="text/javascript">
    $(function() {
    
    	<?php if(in_array('write-batch-command', $userPermissions)): ?>
    	$(".emergency-stop-btn").on("click", function() {
        	
        	var command_id = $(this).attr("data-id");
    		var payLoad = {};
            payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
    		payLoad['action'] = "emergencyStop";
    		payLoad['command_id'] = command_id;
            
        	var emergencyStop = $.post("<?php echo e(url('commands/payload')); ?>", payLoad, "JSONP");
        	
        	emergencyStop.done(function(data) {
            	location.reload();
            });
        
        });
    	<?php endif; ?>
    
    });
	
    </script>                              
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/biglan/resources/views/commands/list.blade.php ENDPATH**/ ?>