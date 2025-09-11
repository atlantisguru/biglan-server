<nav class="navbar shadow-sm navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="{{ url('dashboard') }}">BigLan</a><a href="javascript:void(0)"></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <i class="fas fa-bars"></i>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
	@if(Auth::check())
    <ul class="navbar-nav mr-auto nav">
		@if(auth()->user()->hasPermission('read-subnetworks'))
		<li class="nav-item">
        	<a class="nav-link" href="{{ url("subnets") }}">{{ __('all.nav.ip_table') }}</a>
      	</li>
		@endif
		@if(auth()->user()->hasPermission('read-notifications'))
      	<li class="nav-item">
        	<a class="nav-link" href="{{ url("notifications") }}">{{ __('all.nav.notification_center') }} <span id="notifications" class="badge badge-danger d-none"></span></a>
      	</li>
		@endif
		@if(auth()->user()->hasPermission('read-topology'))
    	<li class="nav-item">
        	<a class="nav-link" href="{{ url("topology")}}">{{ __('all.nav.topology') }}</a>
      	</li>
		@endif
		@if(auth()->user()->hasPermission('read-batch-command'))
		<li class="nav-item">
        	<a class="nav-link" href="{{ url("commands") }}">{{ __('all.nav.command_center') }}</a>
      	</li>
		@endif
    	@if(auth()->user()->hasPermission('read-articles'))
		<li class="nav-item">
        	<a class="nav-link" href="{{ url("articles") }}">{{ __('all.nav.articles') }}</a>
		</li>
		@endif
		@if(auth()->user()->hasPermission('read-documents'))
      	<li class="nav-item">
        	<a class="nav-link" href="{{ url("documents")}}">{{ __('all.nav.documents') }}</a>
      	</li>
		@endif
				
		<li class="nav-item dropdown">
    		<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">{{ __('all.nav.assets') }}  <span class="badge badge-danger unreachable-counter d-none"></span></a>
    		<div class="dropdown-menu  dropdown-menu-right">
				@if(auth()->user()->hasPermission('read-workstations'))
    				<a class="dropdown-item" href="{{ url("workstations") }}">{{ __('all.nav.workstations') }} <span class="badge badge-danger unreachable-counter d-none"></span></a>
      			@endif
				@if(auth()->user()->hasPermission('read-network-printers'))
					<a class="dropdown-item" href="{{ url("networkprinters") }}">{{ __('all.nav.network_printers') }}</a>
      			@endif
				@if(auth()->user()->hasPermission('read-network-devices'))
					<a class="dropdown-item" href="{{ url("networkdevices") }}">{{ __('all.nav.network_devices') }}</a>
      			@endif
				@if(auth()->user()->hasPermission('read-operating-systems'))
    				<a class="dropdown-item" href="{{ url("operatingsystems") }}">{{ __('all.nav.operating_systems') }}</a>
      			@endif
				@if(auth()->user()->hasPermission('read-monitors'))
    				<a class="dropdown-item" href="{{ url("workstations/displays") }}">{{ __('all.nav.monitors') }}</a>
        		@endif
				@if(auth()->user()->hasPermission('read-printers'))
    				<a class="dropdown-item" href="{{ url("workstations/printers") }}">{{ __('all.nav.local_printers') }}</a>
      			@endif
			</div>
  		</li>

    </ul>
	<ul class="navbar-nav ml-auto nav">
		<li class="nav-item dropdown">
    		<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user-circle"></i> {!! __('all.nav.username', ['username' => Auth::user()->username]) !!}</a>
    		<div class="dropdown-menu  dropdown-menu-right">
      			<a class="dropdown-item" href="{{ url("settings") }}">{{ __('all.nav.my_settings') }}</a>
      			@if(auth()->user()->hasPermission('read-global-settings'))
    				<a class="dropdown-item" href="{{ url("globalsettings") }}">{{ __('all.nav.global_settings') }}</a>
				@endif
				@if(auth()->user()->hasPermission('read-api-tokens'))
    				<a class="dropdown-item" href="{{ url("apitokens") }}">{{ __('all.nav.api_tokens') }}</a>
				@endif
				@if(auth()->user()->hasPermission('read-downloads'))
    				<a class="dropdown-item" href="{{ url("downloads") }}">{{ __('all.nav.downloads') }}</a>
				@endif
				@if(auth()->user()->hasPermission('read-updates'))
    				<a class="dropdown-item" href="{{ url("updates") }}">{{ __('all.nav.updates') }}</a>
				@endif
				@if(auth()->user()->hasPermission("read-users") === true)
              		<a class="dropdown-item" href="{{ url("users") }}">{{ __('all.nav.users') }}</a>
    			@endif
				<div class="dropdown-divider"></div>
            	<a class="dropdown-item" href="{{ url("help") }}">{{ __('all.nav.help') }}</a>
            	<a class="dropdown-item" href="{{ url("about") }}">{{ __('all.nav.about') }}</a>
            	<div class="dropdown-divider"></div>
            	<a class="dropdown-item" href="{{ url("logout") }}">{{ __('all.nav.logout') }}</a>
            </div>
  		</li>
    </ul>
	@endif
  </div>
</nav>