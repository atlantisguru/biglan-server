<?php $__env->startSection('title'); ?>
	<?php echo e(__('all.command_center.script_storage')); ?> | BigLan
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
	<?php echo e(csrf_field()); ?>

	<div class="row mt-2">
		<div class="col-12">
        	<?php echo $__env->make('commands.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>
	<div class="row">
    	<div class="col-lg-6 col-md-10 col-sm-12 mx-auto">
    	<form>
  			<div class="form-group">
            	<input name="command_id" id="command_id" type="hidden" />
    			<label for="scripts"><?php echo e(__('all.command_center.predefined_scripts')); ?></label>
    			<select class="form-control" name="scripts" id="scripts">
                	<option value=""><?php echo e(__('all.command_center.select_script')); ?></option>
                	<?php $__currentLoopData = $scripts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $script): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                		<option value='<?php echo e($script->id); ?>'><?php echo e($script->alias); ?></option>
                	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            	</select>
  			</div>
        	<div class="form-group">
    			<label for="command"><?php echo e(__('all.command_center.script')); ?></label>
    			<textarea class="form-control" id="command" name="command" rows="10" disabled></textarea>
                <?php if(auth()->user()->hasPermission('delete-script')): ?>
                	<a href="javascript:void(0)" class="btn btn-danger d-none mt-2" id="delete-command"><?php echo e(__('all.command_center.delete_script_from_database')); ?></a>
                <?php endif; ?>
  			</div>
        	
        </form>
        </div>
	</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('inject-footer'); ?>
	<script type="text/javascript">
    $(function() {
    	
    	<?php if(auth()->user()->hasPermission('read-script')): ?>
    	$("#scripts").on("change", function() {
        	$("#delete-command").removeClass("d-none");
        	var id = $(this).val();
        	var payLoad = {};
            payLoad['_token'] = $('meta[name="csrf-token"]').attr('content');
            payLoad['action'] = "viewScript";
            payLoad['id'] = $(this).val();
        	var posting = $.post("<?php echo e(url('commands/payload')); ?>", payLoad, "JSONP");
        	posting.done(function(data) {
            		$("#command").val(data);
               		//location.reload();
            	
            });
        });
    	<?php endif; ?>
    
        <?php if(auth()->user()->hasPermission('delete-script')): ?>
    	$("#delete-command").on("click", function() {
        	var confirm = window.confirm("<?php echo e(__('all.command_center.are_you_sure_delete_script')); ?>");
            if(!confirm) {
            	return;
            }
        	var id = $("#scripts").val();
        	var payLoad = {};
            payLoad['_token'] = $('meta[name="csrf-token"]').attr('content');
            payLoad['action'] = "deleteScript";
            payLoad['id'] = id;
        	var posting = $.post("<?php echo e(url('commands/payload')); ?>", payLoad, "JSONP");
        	posting.done(function(data) {
            	if (data == "OK") {
            		$("#scripts option[value='"+id+"']").remove();
            		$("#scripts").val($("#scripts option:first").val());
            		$("#command").val("");
                	$("#delete-command").addClass("d-none");
                }
            });
        });
    	<?php endif; ?>
    
    });
    
	</script>                              
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/biglan/resources/views/commands/scripts.blade.php ENDPATH**/ ?>