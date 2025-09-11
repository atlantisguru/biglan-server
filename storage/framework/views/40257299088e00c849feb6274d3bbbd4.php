<nav class="navbar shadow-sm navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="<?php echo e(url('dashboard')); ?>">BigLan</a><a href="javascript:void(0)"></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <i class="fas fa-bars"></i>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
	<?php if(Auth::check()): ?>
    <ul class="navbar-nav mr-auto nav">
		<?php if(auth()->user()->hasPermission('read-subnetworks')): ?>
		<li class="nav-item">
        	<a class="nav-link" href="<?php echo e(url("subnets")); ?>"><?php echo e(__('all.nav.ip_table')); ?></a>
      	</li>
		<?php endif; ?>
		<?php if(auth()->user()->hasPermission('read-notifications')): ?>
      	<li class="nav-item">
        	<a class="nav-link" href="<?php echo e(url("notifications")); ?>"><?php echo e(__('all.nav.notification_center')); ?> <span id="notifications" class="badge badge-danger d-none"></span></a>
      	</li>
		<?php endif; ?>
		<?php if(auth()->user()->hasPermission('read-topology')): ?>
    	<li class="nav-item">
        	<a class="nav-link" href="<?php echo e(url("topology")); ?>"><?php echo e(__('all.nav.topology')); ?></a>
      	</li>
		<?php endif; ?>
		<?php if(auth()->user()->hasPermission('read-batch-command')): ?>
		<li class="nav-item">
        	<a class="nav-link" href="<?php echo e(url("commands")); ?>"><?php echo e(__('all.nav.command_center')); ?></a>
      	</li>
		<?php endif; ?>
    	<?php if(auth()->user()->hasPermission('read-articles')): ?>
		<li class="nav-item">
        	<a class="nav-link" href="<?php echo e(url("articles")); ?>"><?php echo e(__('all.nav.articles')); ?></a>
		</li>
		<?php endif; ?>
		<?php if(auth()->user()->hasPermission('read-documents')): ?>
      	<li class="nav-item">
        	<a class="nav-link" href="<?php echo e(url("documents")); ?>"><?php echo e(__('all.nav.documents')); ?></a>
      	</li>
		<?php endif; ?>
				
		<li class="nav-item dropdown">
    		<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><?php echo e(__('all.nav.assets')); ?>  <span class="badge badge-danger unreachable-counter d-none"></span></a>
    		<div class="dropdown-menu  dropdown-menu-right">
				<?php if(auth()->user()->hasPermission('read-workstations')): ?>
    				<a class="dropdown-item" href="<?php echo e(url("workstations")); ?>"><?php echo e(__('all.nav.workstations')); ?> <span class="badge badge-danger unreachable-counter d-none"></span></a>
      			<?php endif; ?>
				<?php if(auth()->user()->hasPermission('read-network-printers')): ?>
					<a class="dropdown-item" href="<?php echo e(url("networkprinters")); ?>"><?php echo e(__('all.nav.network_printers')); ?></a>
      			<?php endif; ?>
				<?php if(auth()->user()->hasPermission('read-network-devices')): ?>
					<a class="dropdown-item" href="<?php echo e(url("networkdevices")); ?>"><?php echo e(__('all.nav.network_devices')); ?></a>
      			<?php endif; ?>
				<?php if(auth()->user()->hasPermission('read-operating-systems')): ?>
    				<a class="dropdown-item" href="<?php echo e(url("operatingsystems")); ?>"><?php echo e(__('all.nav.operating_systems')); ?></a>
      			<?php endif; ?>
				<?php if(auth()->user()->hasPermission('read-monitors')): ?>
    				<a class="dropdown-item" href="<?php echo e(url("workstations/displays")); ?>"><?php echo e(__('all.nav.monitors')); ?></a>
        		<?php endif; ?>
				<?php if(auth()->user()->hasPermission('read-printers')): ?>
    				<a class="dropdown-item" href="<?php echo e(url("workstations/printers")); ?>"><?php echo e(__('all.nav.local_printers')); ?></a>
      			<?php endif; ?>
			</div>
  		</li>

    </ul>
	<ul class="navbar-nav ml-auto nav">
		<li class="nav-item dropdown">
    		<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user-circle"></i> <?php echo __('all.nav.username', ['username' => Auth::user()->username]); ?></a>
    		<div class="dropdown-menu  dropdown-menu-right">
      			<a class="dropdown-item" href="<?php echo e(url("settings")); ?>"><?php echo e(__('all.nav.my_settings')); ?></a>
      			<?php if(auth()->user()->hasPermission('read-global-settings')): ?>
    				<a class="dropdown-item" href="<?php echo e(url("globalsettings")); ?>"><?php echo e(__('all.nav.global_settings')); ?></a>
				<?php endif; ?>
				<?php if(auth()->user()->hasPermission('read-api-tokens')): ?>
    				<a class="dropdown-item" href="<?php echo e(url("apitokens")); ?>"><?php echo e(__('all.nav.api_tokens')); ?></a>
				<?php endif; ?>
				<?php if(auth()->user()->hasPermission('read-downloads')): ?>
    				<a class="dropdown-item" href="<?php echo e(url("downloads")); ?>"><?php echo e(__('all.nav.downloads')); ?></a>
				<?php endif; ?>
				<?php if(auth()->user()->hasPermission('read-updates')): ?>
    				<a class="dropdown-item" href="<?php echo e(url("updates")); ?>"><?php echo e(__('all.nav.updates')); ?></a>
				<?php endif; ?>
				<?php if(auth()->user()->hasPermission("read-users") === true): ?>
              		<a class="dropdown-item" href="<?php echo e(url("users")); ?>"><?php echo e(__('all.nav.users')); ?></a>
    			<?php endif; ?>
				<div class="dropdown-divider"></div>
            	<a class="dropdown-item" href="<?php echo e(url("help")); ?>"><?php echo e(__('all.nav.help')); ?></a>
            	<a class="dropdown-item" href="<?php echo e(url("about")); ?>"><?php echo e(__('all.nav.about')); ?></a>
            	<div class="dropdown-divider"></div>
            	<a class="dropdown-item" href="<?php echo e(url("logout")); ?>"><?php echo e(__('all.nav.logout')); ?></a>
            </div>
  		</li>
    </ul>
	<?php endif; ?>
  </div>
</nav><?php /**PATH /var/www/biglan/resources/views/nav.blade.php ENDPATH**/ ?>