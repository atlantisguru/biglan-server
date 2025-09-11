<?php $__env->startSection("title"); ?>
<?php echo e(__('all.api_tokens.api_tokens')); ?> | BigLan
<?php $__env->stopSection(); ?>
<?php $__env->startSection("content"); ?>

<?php
	$userPermissions = auth()->user()->permissions();
?>

	<div class="row mt-2">
		<div class="col-6">
			<?php echo e(csrf_field()); ?>

			<?php if(in_array('write-api-tokens', $userPermissions)): ?>
   				<a href=<?php echo e(url('/apitokens/new')); ?> class="btn btn-sm btn-primary mr-2"><i class="fas fa-plus"></i> <?php echo e(__('all.api_tokens.btn_create')); ?></a>
            <?php endif; ?>
        </div>
	</div>
	<div class="row mt-2">
		<div class="col-lg-12 col-sm-12">
			<div class="table-responsive">
				<?php if(count($tokens) > 0): ?>
   				<table class="table table-striped table-hover" id="tokens">
						<thead class="thead-dark">
							<tr>
            					<th class="text-center"><?php echo e(__('all.api_tokens.active')); ?></th>
            					<th><?php echo e(__('all.api_tokens.name')); ?></th>
            					<th><?php echo e(__('all.api_tokens.token')); ?></th>
            					<th><?php echo e(__('all.api_tokens.type')); ?></th>
            					<th><?php echo e(__('all.api_tokens.id')); ?></th>
            					<th class="text-center"><?php echo e(__('all.api_tokens.uses')); ?></th>
            					<th class="text-center"><?php echo e(__('all.api_tokens.expires')); ?></th>
            					<th class="text-center"><?php echo e(__('all.api_tokens.last_use')); ?></th>
            					<th class="text-center"><?php echo e(__('all.api_tokens.actions')); ?></th>
            				</tr>
            
						</thead>
						<tbody>
					
   				<?php $__currentLoopData = $tokens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $token): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr <?php if(!$token->is_active): ?> class="text-muted"  <?php endif; ?>>
								<td class="text-center">
            						<?php if($token->is_active): ?>
            							<i class="fas fa-check"></i>
            						<?php else: ?>
            							<i class="fas fa-times"></i>
            						<?php endif; ?>
            					</td>
								<td>
            						<?php echo e($token->name); ?>

            					</td>
								<td>
            						<?php echo e($token->decrypted_token); ?>

									<?php if($token->is_active): ?>
                                    	<a href="javascript:" class="copy-btn" data-text-to-copy="<?php echo e($token->decrypted_token); ?>" title="<?php echo e(__('all.api_tokens.copy')); ?>"><i class="far fa-clone"></i></a>
                                		<span class="copy-feedback" style="margin-left: 5px; font-size: 0.8em; color: green;"></span>
                                	<?php endif; ?>
            					</td>
                                <td>
            						<?php echo e($token->tokenable_type ?? "-"); ?>

            					</td>
                                <td>
            						<?php echo e($token->tokenable_id ?? "-"); ?>

            					</td>
                                <td class="text-center">
                                	<?php if(isset($token->max_uses)): ?>
            							<?php echo e($token->uses_count); ?> / <?php echo $token->max_uses ?? "&infin;"; ?>

									<?php else: ?>
                                    	&infin;
                                    <?php endif; ?>
            					</td>
                                <td class="text-center">
            						<?php echo $token->expires_at ?? "&infin;"; ?>

            					</td>
                           		<td class="text-center">
            						<?php echo e($token->last_used_at ?? "N/A"); ?>

            					</td>
                                <td class="text-center">
                                	<?php if($token->is_active): ?>
            							<form action="<?php echo e(route('apitokens.revoke', ['id' => $token->id])); ?>" method="POST">
                                			<?php echo csrf_field(); ?>
                                			<button type="submit" class="btn btn-danger btn-sm"><?php echo e(__('all.api_tokens.revoke_btn')); ?></button>
                                		</form>
                                	<?php endif; ?>
            					</td>
                                
                         	</tr>
			    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
					</table>
            		
            	<?php else: ?>
                	<p><?php echo e(__('all.api_tokens.token_not_found')); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
    
	

<?php $__env->stopSection(); ?>
<?php $__env->startSection('inject-footer'); ?>
        <link rel="stylesheet" type="text/css" href=<?php echo e(url("css/jquery.dataTables.min.css")); ?>>
        <script type="text/javascript" src=<?php echo e(url("js/jquery.dataTables.min.js")); ?>></script>
        <script type="text/javascript">
                                
        async function copyTextToClipboard(text) {
        	try {
            	await navigator.clipboard.writeText(text);
            	return true;
        	} catch (err) {
            	return false;
        	}
    	}

		function showFeedback(buttonElement, message, color = 'green') {
        	const $feedbackSpan = $(buttonElement).next('.copy-feedback');
        	if ($feedbackSpan.length) {
            	$feedbackSpan.text(message);
             	$feedbackSpan.css('color', color);
             	setTimeout(() => {
                	$feedbackSpan.text('');
             	}, 2000);
        	}
    	}
                                
        $(function() {
    		$('#tokens').DataTable({
            	"pageLength": 100
            });
            
        	$('.copy-btn').on('click', async function(event) {
            	event.preventDefault();

            	const $button = $(this);
            	const textToCopy = $button.data('text-to-copy');
				
            	if (textToCopy) {
                
                	const success = await copyTextToClipboard(textToCopy);

                	if (success) {
                    	showFeedback($button, "<?php echo e(__('all.api_tokens.copied')); ?>", 'green');
                	} else {
                    	showFeedback($button, "<?php echo e(__('all.api_tokens.failed')); ?>", 'red');
                	}

            	}
        	
            });
            
		});
	</script>                           	                              
<?php $__env->stopSection(); ?>
<?php echo $__env->make("layout", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/biglan/resources/views/apitokens/list.blade.php ENDPATH**/ ?>