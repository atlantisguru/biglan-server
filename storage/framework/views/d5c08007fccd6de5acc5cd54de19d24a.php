	<?php $__env->startSection('title'); ?>
	<?php echo e(__("all.workstations.workstations")); ?> | BigLan
	<?php $__env->stopSection(); ?>
	<?php $__env->startSection('content'); ?>
	<?php
		$userPermissions = auth()->user()->permissions();
	?>
	<div class="row mt-2">
		<div class="col-12">
			<?php if(in_array('write-workstation', $userPermissions)): ?>
				<a href="<?php echo e(url('workstations/new')); ?>" class="btn btn-sm btn-primary mr-3"><i class="fas fa-plus"></i> <?php echo e(__("all.button.new_workstation")); ?></a>
			<?php endif; ?>
			<a href="<?php echo e(url('workstations/createfilter')); ?>" class="btn btn-sm btn-light"><i class="fas fa-filter"></i> <?php echo e(__("all.button.create_filter")); ?></a>
		</div>
	</div>
	<div class="row mt-2">
             
	<div class="col-12">
    
	<div class="table-responsive">
		<table class="table table-hover" id="workstations">
			<thead class="thead-dark">
				<tr>
					<th></th>
					<th><?php echo e(__("all.workstations.alias")); ?></th>
					<th>IP</th>
             		<th class="text-center"><?php echo e(__("all.workstations.workgroup")); ?></th>
					<th class="text-center"><?php echo e(__("all.workstations.last_online")); ?><br><small class="text-muted"></small></th>
					<th class="text-center"><?php echo e(__("all.workstations.os_version")); ?><br><small class="text-muted"><?php echo e(__("all.workstations.last_os_update")); ?></small></th>
				</tr>
			</thead>
			<tbody>
				<?php $__currentLoopData = $workstations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $workstation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<tr>
						<td>
             			<?php if($workstation->fast_startup == 1): ?> <i class="fas fa-bolt text-warning"></i> <?php endif; ?>
                        <?php
                        	$icon = "fa-desktop";
                        	if($workstation->type == "server") {
                        		$icon = "fa-server";
                        	}
                        	if($workstation->type == "laptop") {
                        		$icon = "fa-laptop";
                        	}
                        	switch($workstation->status()) {
                        		case "online":
                        			$status = "text-success";
                        			break;
                        		case "idle":
                        			$status = "text-warning";
                        			break;
                        		case "offline":
                        			$status = "text-muted";
                        			break;
                        		case "heartbeatLoss":
                        			$status = "text-danger";
                        			break;
                        		default:
                        			$status = "text-muted";
                        			break;
                        	}
                        ?>
                        
             			<i class="fas <?php echo e($icon); ?> <?php echo e($status); ?>"></i>
             			
             			</td>
             			<td>
                        	
                        	<?php if(in_array('read-workstation', $userPermissions)): ?>
             					<a href="<?php echo e(url('workstations/'.$workstation->id)); ?>"><?php echo e($workstation->alias); ?> <?php if($workstation->inventory_id != null): ?> (<?php echo e($workstation->inventory_id); ?>) <?php endif; ?></a>
             				<?php else: ?>
                        		<?php echo e($workstation->alias); ?> <?php if($workstation->inventory_id != null): ?> (<?php echo e($workstation->inventory_id); ?>) <?php endif; ?>
                        	<?php endif; ?>
                            <?php if(!isset($workstation->cpu_points)): ?>
                        		<span class="badge bg-light text-muted small">CPU: <?php echo e($workstation->cpu_score); ?></span>
                            <?php endif; ?>
                            <br>
                            <span class="badge bg-info text-light small">S/N: <?php echo e($workstation->serial); ?></span>
                            <?php $__currentLoopData = $workstation->labels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> 
            					<span class="badge <?php echo e(($label->prop == "SYSTEM")?'badge-danger':'badge-secondary'); ?> mr-1"> <?php echo ($label->prop == "SYSTEM")?'<i class="fas fa-robot"></i>':''; ?>  <?php echo e($label->name); ?></span>
                        	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        	<?php if(count($workstation->labels) > 0): ?>
                        		<br />
                        	<?php endif; ?>
                        	<?php if(isset($workstation->brand)): ?>
                        		<span class="badge badge-warning"><b>Brand:</b> <?php echo e($workstation->brand); ?></span>
                        	<?php endif; ?>
             				<?php if(isset($workstation->uptime_days)): ?>
                        		<span class="badge badge-warning"><b>Uptime:</b> <?php echo e($workstation->uptime_days); ?> <?php echo e(__('all.workstations.filter_days')); ?></span>
                        	<?php endif; ?>
                        	<?php if(isset($workstation->offline_days)): ?>
                        		<span class="badge badge-warning"><b>Offline:</b> <?php echo e($workstation->offline_days); ?> <?php echo e(__('all.workstations.filter_days')); ?></span>
                        	<?php endif; ?>
             				<?php if(isset($workstation->disk)): ?>
                        		<span class="badge badge-warning"><b>Disk:</b> <?php echo e($workstation->disk); ?></span>
                        	<?php endif; ?>
             				<?php if(isset($workstation->freespace)): ?>
                        		<span class="badge badge-warning"><b>Free Space:</b> <?php echo e($workstation->freespace); ?>GB</span>
                        	<?php endif; ?>
             				<?php if(isset($workstation->memory)): ?>
                        		<span class="badge badge-warning"><b>RAM:</b> <?php echo e($workstation->memory); ?>GB</span>
                        	<?php endif; ?>
             				<?php if(isset($workstation->boot_seconds)): ?>
                        		<span class="badge badge-warning"><b>Boot:</b> <?php echo e($workstation->boot_seconds); ?> <?php echo e(__('all.workstations.filter_seconds')); ?></span>
                        	<?php endif; ?>
                            <?php if(isset($workstation->cpu_age) || isset($workstation->cpu_points)): ?>
                        		<span class="badge badge-warning"><b>CPU:</b> <?php echo e($workstation->cpu); ?></span>
                        	<?php endif; ?>
                            <?php if(isset($workstation->cpu_age)): ?>
                        		<span class="badge badge-warning"><b>CPU:</b> <?php echo e($workstation->cpu_age); ?> <?php echo e(__('all.workstations.filter_years')); ?></span>
                        	<?php endif; ?>
                            <?php if(isset($workstation->cpu_points)): ?>
                        		<span class="badge badge-warning"><b>CPU:</b> <?php echo e($workstation->cpu_points); ?> <?php echo e(__('all.workstations.filter_points')); ?></span>
                        	<?php endif; ?>
             				<?php if(isset($workstation->os_updated_months)): ?>
                        		<span class="badge badge-warning"><b>OS Updated:</b> <?php echo e($workstation->os_updated_months); ?> <?php echo e(__('all.workstations.filter_months')); ?></span>
                        	<?php endif; ?>
             			</td>
						<td>
                        	<?php $__currentLoopData = $workstation->ips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        		<?php echo e($ip->ip); ?><br>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </td>
                        <td class="text-center"><?php echo e($workstation->workgroup); ?></td>
						<td class="text-center"><?php echo e(\Carbon\Carbon::parse($workstation->heartbeat)->format("Y.m.d H:i")); ?><br><small class="text-muted"><?php echo e($workstation->update_channel); ?><?php echo e($workstation->service_version); ?></small></td>
						<td class="text-center"><?php echo e($workstation->os); ?><br><small class="text-muted"><?php if($workstation->wu_installed != null): ?> <?php echo e(\Carbon\Carbon::parse($workstation->wu_installed)->format("Y.m.d H:i")); ?> <?php else: ?> N/A <?php endif; ?></small></td>
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
    				$('#workstations').DataTable( {
                    	"pageLength": 50,
    					"search": {
    						"search": "\"<?php echo e($keyword); ?>\""
  						}
  					});
			});
        </script>
		
    <?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/biglan/resources/views/workstations/list.blade.php ENDPATH**/ ?>