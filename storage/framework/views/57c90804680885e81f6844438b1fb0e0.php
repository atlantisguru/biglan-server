<!doctype html>
<html lang="hu">
    <head>
    	<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script defer src=<?php echo e(url("js/fontawesome.5.0.6.min.js")); ?>></script>
        <link rel="stylesheet" type="text/css" href=<?php echo e(url("css/bootstrap.4.1.3.min.css")); ?>>
        <style type="text/css">
        .form-signin {
        	max-width: 330px;
        	margin: 0 auto;
        	padding: 15px;
        	width:100%;
        }

        </style>
    <link rel="stylesheet" type="text/css" href="/css/dark-mode.css">
        <title><?php echo e(__('all.register.registration')); ?> | BigLan</title>
    </head>
	<body class="text-center">
  <?php if(Session::has('failed')): ?>
    <div class="pt-4 row justify-content-center">
        <div class="col-xl-4 col-lg-6 col-md-12  alert alert-danger">
       		<?php echo Session::get('failed'); ?>

        </div>
	</div>
    <?php endif; ?>
    <?php if(Session::has('success')): ?>
    <div class="pt-4 row justify-content-center">
        <div class="col-xl-4 col-lg-6 col-md-12 alert alert-success">
       		<?php echo Session::get('success'); ?>

        </div>
	</div>    
    <?php endif; ?>
       
	
		
				
				<form method="POST" class="form-signin">
        <?php echo e(csrf_field()); ?>

                	<h1 class="mb-5">BigLan</h1>
                	<h1 class="h3 mb-3 font-weight-normal"><?php echo e(__('all.register.registration')); ?></h1>
        			<div class="row">
                <div class="col-12">
                        <label class="sr-only" for="email"><?php echo e(__('all.register.email')); ?></label>
                        <input type="text" name="email" class="form-control" id="email" value="<?php echo e(old('email')); ?>" placeholder="<?php echo e(__('all.register.email')); ?>" value="<?php echo e(old('email', '')); ?>" required autofocus>
                        <?php if($errors->has('email')): ?>
        						<div class="text-danger"><i class="fas fa-exclamation-circle"></i> <?php echo e($errors->first('email')); ?></div>
    					<?php endif; ?>
                </div>
                
            </div>
            <div class="row mt-2">
                <div class="col-12">
                        <label class="sr-only" for="password"><?php echo e(__('all.register.password')); ?></label>
                        <input type="password" name="password" class="form-control" id="password" placeholder="<?php echo e(__('all.register.password')); ?>" required>
                        <?php if($errors->has('password')): ?>
        						<div class="text-danger"><i class="fas fa-exclamation-circle"></i> <?php echo e($errors->first('password')); ?></div>
    					<?php endif; ?>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                        <label class="sr-only" for="password2"><?php echo e(__('all.register.password_again')); ?></label>
                            <input type="password" name="password_confirmation" class="form-control" id="password2" placeholder="<?php echo e(__('all.register.password_again')); ?>" required>
                        
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                        <label class="sr-only" for="username"><?php echo e(__('all.register.fullname')); ?></label>
                       	<input type="text" name="username" class="form-control" id="username"  value="<?php echo e(old('username')); ?>" placeholder="<?php echo e(__('all.register.fullname')); ?>" value="<?php echo e(old('username', '')); ?>" required>
                	<?php if($errors->has('username')): ?>
        				<div class="text-danger"><i class="fas fa-exclamation-circle"></i> <?php echo e($errors->first('username')); ?></div>
    				<?php endif; ?>
             	</div>
            </div>
            <div class="row" style="padding-top: 1rem">
                <div class="col-12 pb-2">
                    <button type="submit" class="btn btn-lg btn-primary btn-block"><?php echo e(__('all.register.registration')); ?></button>
                	<p class="mt-2"><a href="<?php echo e(url('/')); ?>"><?php echo e(__('all.register.back_to_login')); ?></a></p>
                	<p class="mt-4 text-muted">BigLan Network Monitoring System<br>
                        <a href="<?php echo e(url('about-public')); ?>">Copyright</a> &copy; 2018-<?php echo date("Y"); ?>
                    </p>
                </div>
            </div>
        		</form>
		<script src=<?php echo e(url("js/jquery.3.3.1.min.js")); ?>></script>
		<script src=<?php echo e(url("js/bootstrap.4.1.3.min.js")); ?>></script>               
    </body>
</html><?php /**PATH /var/www/biglan/resources/views/users/register.blade.php ENDPATH**/ ?>