	<?php $__env->startSection('title'); ?>
		<?php echo e(__('all.nav.my_settings')); ?> | BigLan
	<?php $__env->stopSection(); ?>
	<?php $__env->startSection('content'); ?>
		<div class="row mt-2">
			<div class="col-12">
            		<h2><?php echo e(__('all.user_settings.my_settings')); ?></h2>
            </div>
        </div>
        <div class="row mt-2">
			<div class="col-12">
            		<h4><?php echo e(__('all.user_settings.theme')); ?></h4>
            </div>
        	<div class="col-12">
            	<form id="switchTheme" class="col-12">
            		<div class="radio">
  						<label><input type="radio" name="theme" value="light" <?php if(Auth::user()->theme == null): ?> checked <?php endif; ?>> <?php echo e(__('all.user_settings.light')); ?></label>
					</div>
					<div class="radio">
  						<label><input type="radio" name="theme" value="dark" <?php if(Auth::user()->theme == "dark"): ?> checked <?php endif; ?>> <?php echo e(__('all.user_settings.dark')); ?></label>
					</div>
		        </form>
        	</div>
        </div>
		<div class="row mt-2">
			<div class="col-12">
            		<h4><?php echo e(__('all.user_settings.language')); ?></h4>
            </div>
        	<div class="col-12">
            	<form id="switchLanguage" class="col-12">
					<?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            			<div class="radio">
  							<label><input type="radio" name="language" value="<?php echo e($lang); ?>" <?php if(Auth::user()->language == $lang): ?> checked <?php endif; ?>> <?php echo e(__('all.languages.'.$lang)); ?></label>
						</div>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
					<div class="radio">
  						<label><input type="radio" name="language" value="null" <?php if(Auth::user()->language == null): ?> checked <?php endif; ?>> <?php echo e(__('all.user_settings.default')); ?></label>
					</div>
				</form>
        	</div>
        </div>
            

				
	<?php $__env->stopSection(); ?>
	<?php $__env->startSection('inject-footer'); ?>
	<script type="text/javascript">
    	$(function() {
    		
        	$("#switchTheme input").on("change", function() {
        	    var theme = $("input[name=theme]:checked", "#switchTheme").val();
            	var posting = $.post("<?php echo e(url('settings')); ?>", { '_token': $('meta[name=csrf-token]').attr('content'), 'settings': 'switchTheme', 'theme': theme } , "JSONP");
        		posting.done(function(data) {
                	location.reload();
                });
        	});
        
        	$("#switchLanguage input").on("change", function() {
        	    var language = $("input[name=language]:checked", "#switchLanguage").val();
            	var posting = $.post("<?php echo e(url('settings')); ?>", { '_token': $('meta[name=csrf-token]').attr('content'), 'settings': 'switchLanguage', 'language': language } , "JSONP");
        		posting.done(function(data) {
                	location.reload();
                });
        	});
        	
        });
    </script>
	<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/biglan/resources/views/users/settings.blade.php ENDPATH**/ ?>