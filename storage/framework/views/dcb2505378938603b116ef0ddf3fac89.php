	<?php $__env->startSection('title'); ?>
		<?php echo e(__('all.about.about')); ?> | BigLan
	<?php $__env->stopSection(); ?>
	<?php $__env->startSection('content'); ?>
		<?php echo e(csrf_field()); ?>

     	<div class="row text-center">
     		<div class="col-12">
    			<h1 class="mb-5 mt-5">BigLan Network Monitoring System</h1>
        		<h3 class="mb-3 font-weight-normal"><?php echo e(__("all.about.about")); ?></h3>
     		</div>
     	</div>
     	<div class="row text-center">
     		<div class="col-12">
    			<h5><?php echo e(__('all.about.version')); ?></h5>
        		<p>V2</p>
     		</div>
     	</div>
     	<div class="row text-center">
     		<div class="col-12">
    			<h5><?php echo e(__('all.about.creator')); ?></h5>
        		<p>Bubori Attila (<a href="https://www.linkedin.com/in/attila-bubori-40235935b/" target="_blank">LinkedIn</a>)</p>
     		</div>
     	</div>
     	<div class="row text-center">
     		<div class="col-12">
    			<h5><?php echo e(__('all.about.contributors')); ?></h5>
        		<p class="mb-0">Rédei István</p>
        		<p class="mb-0">Magyar Zoltán (<a href="https://www.linkedin.com/in/zoltán-magyar-411904133/" target="_blank">LinkedIn</a>)</p>
        		<p class="mb-0">Perjési Gergő (<a href="https://www.linkedin.com/in/gergő-perjési-0651681a0/" target="_blank">LinkedIn</a>)</p>
     			<p>Molnár Márk</p>
       		</div>
     	</div>
     	<div class="row text-center">
     		<div class="col-12">
    			<h5><?php echo e(__('all.about.created')); ?></h5>
        		<p>2018-2025</p>
     		</div>
     	</div>
        <div class="row text-center">
     		<div class="col-12">
    			<h5><?php echo e(__('all.about.thanks')); ?></h5>
        		<p><a href="https://laravel.com/" target="_blank"><img class="logo" src="<?php echo e(asset('/images/laravel.png')); ?>"></a></p>
     			<p><a href="https://getbootstrap.com/" target="_blank"><img class="logo" src="<?php echo e(asset('/images/bootstrap.png')); ?>"></a></p>
     			<p><a href="https://jquery.com/" target="_blank"><img src="<?php echo e(asset('/images/jquery.png')); ?>"></a></p>
     			<p><a href="https://fontawesome.com/" target="_blank"><img class="logo" src="<?php echo e(asset('/images/fontawesome.png')); ?>"></a></p>
     			<p><a href="https://datatables.net/" target="_blank"><img class="logo" src="<?php echo e(asset('/images/datatables.png')); ?>"></a></p>
     			<p><a href="https://sigmajs.org/" target="_blank"><img class="logo" src="<?php echo e(asset('/images/sigmajs.png')); ?>"></a></p>
     			<p><a href="https://ckeditor.com/" target="_blank"><img class="logo" src="<?php echo e(asset('/images/ckeditor.png')); ?>"></a></p>
     		</div>
     	</div>
        <div class="row">
        	<div class="col-lg-4"></div>
     		<div class="col-lg-4">
    			<h5><?php echo e(__('all.about.license')); ?></h5>
        		<pre>BigLan Network Monitoring System
Copyright (C) 2018-2025  Bubori Attila
https://biglan.net

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see https://www.gnu.org/licenses/.
    
## Third-party Technologies
This software uses third-party libraries and frameworks, including:
- Laravel (MIT License) https://laravel.com/
- Bootstrap (MIT License) https://getbootstrap.com/
- jQuery (MIT License) https://jquery.com/
- FontAwesome (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) https://fontawesome.com/
- DataTables (MIT License) https://datatables.net/
- Sigma.js (MIT License) https://sigmajs.org/
- CKEditor (MIT License) https://ckeditor.com/
These components are not governed by the AGPL v3 license but retain their
original license terms provided by their respective owners.</pre>
        	</div>
     	</div>
        
        <?php
        
        	$phpversion = phpversion();
        	$mysql_version = DB::scalar('SELECT VERSION()');
			$linuxVersion = shell_exec("lsb_release -a | grep -oP '(?<=^Description:\t).*$'");
			$kernelVersion = shell_exec('uname -r');
        ?>
        <hr>
        <div class="row text-center">
     		<div class="col-12">
    			<p><strong>Linux <?php echo e(__('all.about.version')); ?>:</strong> <?php echo e($linuxVersion); ?> (Kernel: <?php echo e($kernelVersion); ?>)</p>
            	<p><strong>PHP <?php echo e(__('all.about.version')); ?>:</strong> <?php echo e($phpversion); ?></p>
            	<p><strong>MySQL <?php echo e(__('all.about.version')); ?>:</strong> <?php echo e($mysql_version); ?></p>
                
        	</div>
        </div>
        
        
     	
       
 	<?php $__env->stopSection(); ?>
                
    <?php $__env->startSection('inject-footer'); ?>
     <script type="text/javascript">
			

			$(function() {});
	</script>
    <?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/biglan/resources/views/about.blade.php ENDPATH**/ ?>