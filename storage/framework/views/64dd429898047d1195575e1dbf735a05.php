<?php $__env->startSection("title"); ?>
<?php echo e(__('all.updates.updates')); ?> | BigLan
<?php $__env->stopSection(); ?>
<?php $__env->startSection("content"); ?>

<?php
	$userPermissions = auth()->user()->permissions();
?>

	<div class="row mt-2">
		<div class="col-6">
			<?php echo e(csrf_field()); ?>

			<?php if(in_array('read-updates', $userPermissions)): ?>
   				<a href="javascript:" class="btn btn-sm btn-primary mr-2" id="upload-btn"><i class="fas fa-plus"></i> <?php echo e(__('all.updates.upload')); ?></a>
            <?php endif; ?>
        </div>
	</div>
	<div class="row mt-2">
		<div class="col-lg-12 col-sm-12">
			<div class="table-responsive">
				<?php if(count($updates) > 0): ?>
   				<table class="table table-striped table-hover" id="updates">
						<thead class="thead-dark">
							<tr>
            					<th><?php echo e(__('all.updates.channel')); ?></th>
            					<th><?php echo e(__('all.updates.version')); ?></th>
            					<th><?php echo e(__('all.updates.notes')); ?></th>
            					<th><?php echo e(__('all.updates.created')); ?></th>
            					<th><?php echo e(__('all.updates.counter')); ?></th>
            					<th><?php echo e(__('all.updates.actions')); ?></th>
            				</tr>
						</thead>
						<tbody>
						<?php
            				$firstA = true;
            				$firstB = true;
            				$firstD = true;
            			?>
   				<?php $__currentLoopData = $updates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $update): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr data-id=<?php echo e($update->id); ?>>
								<td class="text-center"><?php echo e($update->channel); ?></td>
								<td><?php echo $update->version; ?></td>
								<td><?php echo $update->description; ?></td>
								<td><?php echo e($update->created_at); ?></td>
								<td class="text-center"><?php echo e($update->counter); ?></td>
								<td class="">
                        		
            					<?php if(($firstA && $update->channel == "a") || ($firstB && $update->channel == "b") || ($firstD && $update->channel == "d")): ?>
            						<?php if($update->counter == 0 && $update->active == 0): ?>
            							<a href="javascript:" data-id=<?php echo e($update->id); ?> class="btn btn-success btn-sm m-1 deploy-btn"><?php echo e(__('all.updates.deploy')); ?></a>
                        				<a href="javascript:" data-id=<?php echo e($update->id); ?> class="btn btn-danger btn-sm m-1 delete-btn"><?php echo e(__('all.updates.delete')); ?></a>
            						<?php endif; ?>
            						<?php if($update->counter > 0 && $update->active == 0): ?>
                   						<a href="javascript:" data-id=<?php echo e($update->id); ?> class="btn btn-success btn-sm m-1 deploy-btn"><?php echo e(__('all.updates.deploy')); ?></a>
            						<?php endif; ?>
            						<?php if($update->counter >= 0 && $update->active == 1): ?>
                   						<a href="javascript:" data-id=<?php echo e($update->id); ?> class="btn btn-danger btn-sm m-1 revoke-btn"><?php echo e(__('all.updates.revoke')); ?></a>
            						<?php endif; ?>
                        			<?php if($update->channel == 'a'): ?>
                        				<?php $firstA = false; ?>
                        			<?php endif; ?>
                                    <?php if($update->channel == 'b'): ?>
                        				<?php $firstB = false; ?>
                        			<?php endif; ?>
                                    <?php if($update->channel == 'd'): ?>
                        				<?php $firstD = false; ?>
                        			<?php endif; ?>
                                <?php endif; ?>
                                </td>
							</tr>
			    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
					</table>
            		
            	<?php else: ?>
                	<p><?php echo e(__('all.updates.update_not_found')); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>

    <?php if(in_array('upload-update', $userPermissions)): ?>             
    <!-- The Modal -->
	<div class="modal" id="upload-form">
  		<div class="modal-dialog">
    		<div class="modal-content">

      			<!-- Modal Header -->
      			<div class="modal-header">
        			<h4 class="modal-title"><?php echo e(__('all.updates.upload_file')); ?></h4>
        			<button type="button" class="close" data-dismiss="modal">&times;</button>
      			</div>

      			<!-- Modal body -->
      			<div class="modal-body">
                    	<div class="row">
                    		<div class="col-12">
                    			<?php echo e(__('all.updates.select_file')); ?>:
                    			<input type="file" id="file" name="file" class="form-control">
                    		</div>
                    	</div>
                        <div class="row mt-2">
        					<div class="col-12">
                                <?php echo e(__('all.updates.channel')); ?>:
                    			<select name="channel" id="channel" class="form-control">
                                	<option value="b">beta</option>
                                	<option value="a">alpha</option>
                                	<option value="d">developer</option>
                                </select>
                    		</div>
                    	</div>
                        <div class="row mt-2">
        					<div class="col-12">
                    			<?php echo e(__('all.updates.notes')); ?>:
                    			<textarea rows="5" name="notes" id="notes" class="form-control"></textarea>
                    		</div>
                    	</div>
                </div>

      			<!-- Modal footer -->
      			<div class="modal-footer">
                    <button type="button" id="upload-file-btn" class="btn btn-primary"><?php echo e(__('all.button.upload')); ?></button>
        			<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(__('all.button.close')); ?></button>
      			</div>

    		</div>
  		</div>
	</div>
	<?php endif; ?>        
            
<?php $__env->stopSection(); ?>
<?php $__env->startSection('inject-footer'); ?>
        <link rel="stylesheet" type="text/css" href=<?php echo e(url("css/jquery.dataTables.min.css")); ?>>
        <script type="text/javascript" src=<?php echo e(url("js/jquery.dataTables.min.js")); ?>></script>
        <script type="text/javascript">
        	$(function() {
    				$('#updates').DataTable({
                    	"pageLength": 25,
                    	"order": [[3, 'desc']]
                    });
            
            	$("#upload-btn").on("click", function(){
            
            		$("#upload-form").modal("show");
            
            	});
            
            	$("#upload-file-btn").on("click", function() {
            		$("#upload-file-btn").html('<i class="fas fa-spinner fa-spin"></i> ' + $("#upload-file-btn").text());
            		$("#upload-file-btn").addClass("disabled");
            		var payLoad = {};
            		payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            		payLoad['action'] = 'uploadFile';
            		var file = $('#file').prop('files')[0];
            		const reader = new FileReader();
    				reader.onload = (e) => {
      					const data = e.target.result.split(',')[1];
                		payLoad["file"] = data; 
                		payLoad["filename"] = file.name;
            			payLoad['channel'] = $('select[name="channel"]').val()
                		payLoad['notes'] = $('textarea[name="notes"]').val()
                	
            			var uploadFile = $.post("<?php echo e(url('updates')); ?>", payLoad, "JSONP");
        				uploadFile.done(function(data) {
                			$("#upload-file-btn").html("<?php echo e(__('all.button.upload')); ?>");
                			$("#upload-file-btn").removeClass("disabled");
            				$('#upload-form').modal('hide');
                			location.reload();
                		});
                		uploadFile.fail(function(jqXHR, textStatus, errorThrown) {
        					$("#upload-file-btn").html("<?php echo e(__('all.button.upload')); ?>");
        					$("#upload-file-btn").removeClass("disabled");
                    	});
                	};
            		
                	reader.readAsDataURL(file);
            
            	});
            
            	$(".delete-btn").on("click", function() {
            		var confirm = window.confirm("<?php echo e(__('all.updates.are_you_sure_delete_update')); ?>");
           			if(!confirm) {
            			return;
            		}
            		var payLoad = {};
            		payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            		payLoad['action'] = 'deleteUpdate';
            		payLoad['id'] = $(this).attr("data-id");
            		var deleteUpdate = $.post("<?php echo e(url('updates')); ?>", payLoad, "JSONP");
        			deleteUpdate.done(function(data) {
                		if(data=="OK") {
                    		location.reload();
                    	}
                	});
            	});
            
            	$(".deploy-btn").on("click", function() {
            		var confirm = window.confirm("<?php echo e(__('all.updates.are_you_sure_deploy_update')); ?>");
           			if(!confirm) {
            			return;
            		}
            		var payLoad = {};
            		payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            		payLoad['action'] = 'deployUpdate';
            		payLoad['id'] = $(this).attr("data-id");
            		var deployUpdate = $.post("<?php echo e(url('updates')); ?>", payLoad, "JSONP");
        			deployUpdate.done(function(data) {
                		if(data=="OK") {
                    		location.reload();
                    	}
                	});
            	});
            
            	$(".revoke-btn").on("click", function() {
            		var confirm = window.confirm("<?php echo e(__('all.updates.are_you_sure_revoke_update')); ?>");
           			if(!confirm) {
            			return;
            		}
            		var payLoad = {};
            		payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            		payLoad['action'] = 'revokeUpdate';
            		payLoad['id'] = $(this).attr("data-id");
            		var revokeUpdate = $.post("<?php echo e(url('updates')); ?>", payLoad, "JSONP");
        			revokeUpdate.done(function(data) {
                		if(data=="OK") {
                    		location.reload();
                    	}
                	});
            	});
            
			});
        </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make("layout", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/biglan/resources/views/updates/updates.blade.php ENDPATH**/ ?>