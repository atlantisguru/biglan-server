<?php $__env->startSection("title"); ?>
<?php echo e(__('all.operating_systems.operating_systems')); ?> | BigLan
<?php $__env->stopSection(); ?>
<?php $__env->startSection("content"); ?>
	<div class="row mt-2">
		<div class="col-6">
			<?php echo e(csrf_field()); ?>

			<a href="javascript:" id="btn-scrape" class="btn btn-sm btn-primary"><i class="fas fa-search"></i> <?php echo e(__('all.button.collect_data')); ?></a>&nbsp;<span class="badge badge-light"><i class="fas fa-info-circle"></i> <?php echo e(__('all.network_printers.helper')); ?></span><br>
		</div>
	</div>
	<div class="row mt-2">
		<div class="col-lg-12 col-sm-12">
				<?php if(count($operatingSystems)!=0): ?>
   				<div class="table-responsive">
				<table id="operatingsystems" class="table table-striped table-hover">
						<thead class="thead-dark">
							<tr>
								<th class="text-center"></th>
            					<th></th>
								<th><?php echo e(__('all.operating_systems.name')); ?></th>
								<th class="text-center"><?php echo e(__('all.operating_systems.release_date')); ?></th>
								<th class="text-center"><?php echo e(__('all.operating_systems.end_of_support')); ?></th>
            					<th class="text-center"></th>
            					<th class="text-center"></th>
            
							</tr>
						</thead>
						<tbody>
					
   				<?php $__currentLoopData = $operatingSystems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $os): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr data-id="<?php echo e($os->id); ?>" <?php if(isset($os->last_support_date)): ?> <?php if($os->last_support_date <= $today): ?> class="text-danger" <?php endif; ?> <?php endif; ?>>
								<td class="text-center">
            						<?php if(isset($os->last_support_date)): ?> 
            							<?php if($os->last_support_date <= $today): ?> 
            								<i class="fas fa-exclamation-circle text-danger" data-toggle="popover" data-html="true" data-trigger="hover" title="<?php echo e(__('all.operating_systems.warning')); ?>" data-content="<?php echo e(__('all.operating_systems.end_of_support_reached', ['os_name' => '<b>'.$os->name.'</b>', 'date' => '<b>'.$os->last_support_date.'</b>'] )); ?>"></i> 
            							<?php endif; ?>
            						<?php else: ?>
            							<i class="fas fa-exclamation-triangle text-warning" data-toggle="popover" data-html="true" data-trigger="hover" title="<?php echo e(__('all.operating_systems.warning')); ?>" data-content="<?php echo e(__('all.operating_systems.end_of_support_needed')); ?>"></i>
            						<?php endif; ?>
            					</td>
								<td class="text-right">
            						<?php if(str_contains($os->name, "Ubuntu") === true ): ?> <i class="fab fa-ubuntu"></i> <?php endif; ?>
            						<?php if(str_contains($os->name, "Windows") === true ): ?> <i class="fab fa-windows"></i> <?php endif; ?>
            						<?php if(str_contains($os->name, "Linux") === true ): ?> <i class="fab fa-linux"></i> <?php endif; ?>
            					</td>
								<td><a href="<?php echo e(url('workstations/filter/keyword/'. $os->name)); ?>" target="_blank"><?php echo e($os->name); ?></a></td>
								<td class="editable text-center" data-field="release_date"><?php echo e($os->release_date ?? ""); ?></td>
								<td class="editable text-center" data-field="last_support_date"><?php echo e($os->last_support_date ?? ""); ?></td>
            					<td class="text-right"><?php echo e($os->counter ?? ""); ?></td>
            					<td class="text-right"><?php echo e(round((($os->counter/$all)*100), 1)); ?>%</td>
            				</tr>
        		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
					</table>
        	</div>
			
            <?php else: ?>
        		<p><?php echo e(__('all.operating_systems.not_found_os')); ?></p>
        	<?php endif; ?>
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
    
    	var editValue = "";
        var payLoad = {};
    
    	$("body").on("click", "#btn-scrape", function(e) {
        	var element = $(this);
        
        	element.addClass("disabled");
        
        	var posting = $.post("<?php echo e(url('operatingsystems/payload')); ?>", { '_token': $('meta[name=csrf-token]').attr('content'), action: "scrapeOperatingSystems" }, "JSONP");
        	posting.done(function(data) {
        		if (data == "OK") {
                	location.reload();
                }
            });
        });
    
    	$(document).on('mouseover', 'table#operatingsystems .editable', function() {
                	$(this).css("cursor","cell");
                });
            
            	$(document).on('mouseleave', 'table#operatingsystems .editable', function() {
                	$(this).css("cursor","default");
                });
            	
            	function saveData(osid, osfield, osvalue) { 
            		payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            		payLoad['id'] = osid;
            		payLoad['action'] = 'editOperatingSystem';
                	payLoad['field'] = osfield;
            		payLoad['value'] = osvalue;
                	var editOperatingSystem = $.post("<?php echo e(url('operatingsystems/payload')); ?>", payLoad, "JSONP");
        			editOperatingSystem.done(function(data) {
            			if(data == "OK") {
                			var saveValue = $('.editing').val();
                        	$('.editing').parents("td").text(saveValue);
                		} else {
            				$('.editing').parents("td").text(editValue);
                		}
            		});
                }
            
            	$(document).on("dblclick", 'table#operatingsystems .editable', function() {
                	$(this).removeClass("editable");
                	editValue = $(this).text();
                	var field = $(this).attr("data-field");
                	if (field == "release_date" || field == "last_support_date") {
                    	$(this).html("<input class='form-control editing' type='date' />");
                    } else {
                   		$(this).html("<input class='form-control editing' type='text' />");
                    }
                	$('.editing').val(editValue).focus();
                });
            	
            	$(document).on('keydown', '.editing', function(e) {
                	if(e.which === 13 && e.shiftKey) {
                    	$(this).parents("td").addClass("editable");
                    	var id = $('.editing').parents("tr").attr("data-id");
                    	var field = $('.editing').parents("td").attr("data-field");
                    	var value = $('.editing').val();
                    	saveData(id, field, value);
                    }
                	
                	if(e.which === 27) {
                	   	$('.editing').parents("td").text(editValue).addClass("editable");
                	}
                });
    
	});
</script>                               
<?php $__env->stopSection(); ?>
<?php echo $__env->make("layout", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/biglan/resources/views/operatingsystems/list.blade.php ENDPATH**/ ?>