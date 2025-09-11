<?php $__env->startSection("title"); ?>
<?php echo e(__('all.subnet.ip_table')); ?> | BigLan
<?php $__env->stopSection(); ?>
<?php $__env->startSection("content"); ?>
	<?php if(auth()->user()->hasPermission('write-subnetwork')): ?>
	<div class="row mt-2">
		<div class="col-6">
			<a href=<?php echo e(url('subnets/new')); ?> class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> <?php echo e(__('all.button.new_subnet')); ?></a>
		</div>
	</div>
	<?php endif; ?>
	<div class="row mt-2">
				<?php if(count($subnets)==0): ?>
					<div class="col-lg-6 col-sm-12">
   						<p><?php echo e(__('all.subnet.no_subnet_found')); ?></p>
					</div>
				<?php endif; ?>
				<?php $__currentLoopData = $subnets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subnet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<div class="col-lg-6 col-sm-12">
					<div class="table-responsive">
					<table class="table table-striped table-hover">
					<thead class="thead-dark" data-toggle="collapse" href="#collapse<?php echo e($subnet->id); ?>" aria-expanded="true" aria-controls="collapse<?php echo e($subnet->id); ?>">
						<tr>
							<th>
            						<?php echo e($subnet->identifier); ?>/<?php echo e($subnet->mask); ?> (<?php echo e($subnet->alias); ?>) <?php echo e(__('all.subnet.gateway')); ?>: <?php echo e($subnet->gateway); ?>

  							</th>
                            <th class="align-items-end">
                            
                            </th>
						</tr>
						
					</thead>
					<tbody class="collapse hide" id="collapse<?php echo e($subnet->id); ?>">
					<tr>
							<th><?php echo e(__('all.subnet.ip_address')); ?></th>
							<th><?php echo e(__('all.subnet.device')); ?></th>
					</tr>
                    <?php $__currentLoopData = $subnetIPs[$subnet->id]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    	<tr  >
                            <td><?php echo e($ip["ip"]); ?></td>
                            <td <?php if(isset($ip["alias"])): ?> 
                            		<?php if(strpos($ip["alias"], "???") !== false): ?> class="bg-danger text-light" 
            						<?php else: ?>
            							<?php if(strpos($ip["alias"], "üres") !== false): ?> class="bg-success text-light" <?php endif; ?>
            						<?php endif; ?>
                            		<?php endif; ?>	>
                            	<?php if(isset($ip["wsid"])): ?>
                            		<i class="fas fa-desktop"></i> <a href=<?php echo e(url("workstations/".$ip["wsid"])); ?> target="_blank">
                            	<?php endif; ?>
                            	<?php if(isset($ip["prid"])): ?>
                            		<i class="fas fa-print"></i> <a href=<?php echo e(url("networkprinters")); ?> target="_blank">
                            	<?php endif; ?>
                            	<span data-ip=<?php echo e($ip["ip"]); ?>><?php echo e($ip["alias"] ?? ""); ?></span> <?php if(!isset($ip["wsid"]) && !isset($ip["prid"])): ?> <a href="javascript:void(0)" class="edit"><i class="fas fa-edit"></i></a> <?php endif; ?>
								<?php if(isset($ip["wsid"]) || isset($ip["prid"])): ?>
                            		</a>
                            	<?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
					</table>
					</div>
					</div>
                 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('inject-footer'); ?>
    <style>
    .table th {
    	padding: 0.3rem;                            
    }
	
	.table thead {
    	cursor: pointer
    }
    </style>                            	
 	<script type="text/javascript">
    $(function() {
    	
    	var originalContent = "";
    
    	//edit static text
    	$(".edit").on("click", function() {
        	var element = $(this).prev("span");
        	var table = "ip_tables";
        	var ip = element.attr("data-ip");
        	var field = "alias";
        	var value = element.text();
        	originalContent = value;
        	element.html("<input type='text' class='form-control' value='"+value+"' name='"+field+"' data-ip='"+ip+"'>");
        	$("input[name="+field+"]").focus();
        	$(this).hide();
        });
    
    	$("body").on("keyup", ".form-control", function(e) {
        	var element = $(this);
        	var table = "ip_tables";
        	var ip = element.attr("data-ip");
        	var field = "alias";
        	var value = element.val();
        	if (e.keyCode == 13) {
            	var posting = $.post("<?php echo e(url('subnets/payload')); ?>", { '_token': $('meta[name=csrf-token]').attr('content'), action: "changeIP", ip: ip, field: field, alias: value }, "JSONP");
        		posting.done(function(data) {
                	if (data == "OK") {
        				element.parent("span").next(".edit").show();
            			element.parent("span").text(value);
                    } else {
                    	element.parent("span").next(".edit").show();
            			element.parent("span").text(originalContent);
                    }
            	});
            }
        	if (e.keyCode == 27) {
            	element.parent("span").next(".edit").show();
            	element.parent("span").text(originalContent);
            	originalContent = "";
            }
        });
    
    	$("body").on("focusout", ".form-control", function() {
        	var element = $(this).prev("span");
        
        		element.parent("span").next(".edit").show();
            	element.parent("span").text(originalContent);
            	//originalContent = "";
        });
        
	});                                
    </script>                               
<?php $__env->stopSection(); ?>
<?php echo $__env->make("layout", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/biglan/resources/views/subnets/list.blade.php ENDPATH**/ ?>