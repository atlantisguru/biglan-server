<?php $__env->startSection('title'); ?>
	<?php echo e(__('all.articles.articles')); ?> | BigLan
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<?php
	$userPermissions = auth()->user()->permissions();
?>

	<div class="row mt-2">
		<div class="col-12">
        	<?php echo $__env->make('articles.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>
	
	<div class="row mt-2">
	    	<?php if($articles->count() == 0): ?>
				<div class="col-lg-6 col-sm-12">
        			<div><?php echo e(__('all.articles.no_post_found')); ?></div>
				</div>
        	<?php endif; ?>
		<?php if($articles->count() > 0): ?>
			
    	<div class="col-lg-4 col-12">
			<div class="card mt-1 mb-1 shadow-sm">
            	<div class="card-body">
			<p class="card-text">
        	<?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        		<a href="<?php echo e(url('/articles?cat='.$category->id)); ?>" class="btn btn-sm mb-1"><?php echo e($category->name); ?> <span class="badge badge-light"><?php echo e($category->count); ?></span></a>
        	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</p>
			</div>
			</div>
        </div>
		<?php endif; ?>
		<?php $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        	<div class="col-lg-4 col-12">
        		<div class="card shadow-sm mt-1 mb-1">
            		<div class="card-body">
			
					    	<?php $__currentLoopData = $article->categories(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        		<span class="badge badge-category"><a href="<?php echo e(url('/articles?cat='.$category->category->id)); ?>" class="text-decoration-none text-white"><?php echo e($category->category->name); ?></a></span>
                			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <h5 class="card-title"><a href="<?php echo e(url('/articles/article/'.$article->id)); ?>" class="text-dark"><?php echo e($article->title); ?></a></h5>
					<p class="card-text"><small class="text-muted text-uppercase"><?php echo e($article->user->username); ?> | <?php echo e(\Carbon\Carbon::parse($article->created_at)->format("Y.m.d.")); ?> <?php if($article->created_at != $article->updated_at): ?> (<?php echo e(__('all.articles.updated')); ?>: <?php echo e(\Carbon\Carbon::parse($article->updated_at)->format("Y.m.d.")); ?>) <?php endif; ?> <?php if($article->comments()->count() > 0): ?> | <i class="far fa-comment"></i> <?php echo e($article->comments()->count()); ?> hozzászólás <?php endif; ?> </small></p>
                	<p class="card-text"><?php echo Str::limit(strip_tags($article->body), $limit = 150, $end = '...'); ?></p>
					<?php if(in_array('read-post', $userPermissions)): ?>
                		<a href="<?php echo e(url('/articles/article/'.$article->id)); ?>" class="btn btn-outline-secondary"><?php echo e(__('all.articles.read_more')); ?></a>
                	<?php endif; ?>
            		</div>
				</div>
			</div>
		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	</div>    
    <div class="row mt-2">
		<div class="col-12">
        	<?php echo e($articles->appends(Request::query())->links()); ?>

    	</div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('inject-footer'); ?>
	<style type="text/css">
    	.badge-category:nth-of-type(3n+1) {
  			background: #28a745;
        	color: #FFF;
		}

    	.badge-category:nth-of-type(3n+2) {
  			background: #007bff;
        	color: #FFF;
		}
    	
    	.badge-category:nth-of-type(3n+3) {
  			background: #9b59b6;
        	color: #FFF;
		}
    
    	
   	</style>
 	<script type="text/javascript">
    $(function() {});
     </script>                              
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/biglan/resources/views/articles/list.blade.php ENDPATH**/ ?>