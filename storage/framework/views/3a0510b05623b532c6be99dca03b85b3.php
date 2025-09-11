<form class="form-inline">
	<div class="form-group">
		<a href="<?php echo e(url('/articles')); ?>" class="text-dark text-decoration-none mr-4" ><?php echo e(__('all.articles.articles')); ?></a>
		<?php if(auth()->user()->hasPermission('write-post')): ?>
			<a href="<?php echo e(url('/articles/new')); ?>" class="btn btn-primary btn-sm mr-2"><i class="fas fa-plus"></i> <?php echo e(__('all.articles.new_post')); ?></a>
		<?php endif; ?>
		<div class="input-group">
  			<input type="text" class="form-control form-control-sm" style="min-width:400px" placeholder="" name="q" value="<?php echo e(app('request')->input('q')); ?>" >
        	<div class="input-group-append">
    			<button class="btn btn-primary btn-sm" type="button" id="search-btn"><i class="fas fa-search"></i></button>
  			</div>
        </div>
    </div>
</form><?php /**PATH /var/www/biglan/resources/views/articles/header.blade.php ENDPATH**/ ?>