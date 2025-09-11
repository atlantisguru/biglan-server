@extends ('layout')
	@section('title')
		{{ $workstation->alias }} | BigLan
	@endsection
	@section('content')
    
	@php
		$userPermissions = auth()->user()->permissions();
	@endphp

		<div class="row mt-2">
			<div class="col-12">
				<div class="row" id="alias">
					<div class="col-12">
    					<span class="h3">
    						 @php
                        	$icon = "fa-desktop";
                        	if($workstation->type == "server") {
                        		$icon = "fa-server";
                        	}
                        	if($workstation->type == "laptop") {
                        		$icon = "fa-laptop";
                        	}
                        	switch($workstation->status()) {
                        		case "online":
                        			$status = "text-success";
                        			break;
                        		case "idle":
                        			$status = "text-warning";
                        			break;
                        		case "offline":
                        			$status = "text-muted";
                        			break;
                        		case "heartbeatLoss":
                        			$status = "text-danger";
                        			break;
                        		default:
                        			$status = "text-muted";
                        			break;
                        	}
							$scoreClass = "badge-danger";
							if ($workstation->score > 0 && $workstation->score < 50) {
                            	$scoreClass = "badge-danger";
							}
							if ($workstation->score >= 50 && $workstation->score < 75) {
                            	$scoreClass = "badge-warning";
							}
							if ($workstation->score >= 75) {
                            	$scoreClass = "badge-success";
							}
						@endphp
                        
             			<i class="fas {{ $icon }} {{ $status }}"></i>
    					</span>
						<span class="mb-0 h3" data-table="workstations" data-field="alias">{{ $workstation->alias }}</span>
                        @if(in_array('write-workstation', $userPermissions))
                        	<a href="javascript:void(0)" class="edit"><i class="fas fa-edit"></i></a>
                        @endif
					</div>
				</div>
				<strong>WSID: {{ $workstation->id }}</strong> | {{ __('all.workstations.identification_chance') }}: <span class="badge {{ $scoreClass }}">{{ $workstation->score ?? "N/A" }}@if(isset($workstation->score))%@endif</span> | <span class="text-muted">{{ $workstation->hostname }}</span> ({{ __('all.workstations.inventory_id') }}: <span data-table="workstations" data-field="inventory_id">{{ $workstation->inventory_id }}</span>
                @if(in_array('write-workstation', $userPermissions))
                   <a href="javascript:void(0)" class="edit"><i class="fas fa-edit"></i></a>
                @endif
                )<br>
                <div>
					<span class="text-muted">{{ __('all.workstations.last_online') }}:</span> {{ \Carbon\Carbon::parse($workstation->heartbeat)->format("Y.m.d H:i:s") }} | <span class="text-muted"> @if($workstation->service_version == 0) <span class="text-danger"><i class="fas fa-hand-pointer"></i> {{ __('all.workstations.manual') }} </span> {{ __('all.workstations.registered_manually') }} @else {{$workstation->update_channel}} {{ $workstation->service_version }} @endif</span>
				</div>
         	</div>
        </div>

		<div class="row mt-2 text-muted">
			<div class="col-xl-1 col-lg-2 col-md-3 col-sm-4 col-4 text-center pb-3 ws-action-icon">
            	<a href="javascript:" data-panel="profile" data-toggle="modal" data-target="#profilePanel"><i class="fas fa-3x fa-clipboard-list"></i><br>{{ __('all.workstations.data_sheet') }}</a>
        	</div>
        	<div class="col-xl-1 col-lg-2 col-md-3 col-sm-4 col-4 text-center pb-3 ws-action-icon">
            	<a href="javascript:" data-panel="events" data-toggle="modal" data-target="#eventsPanel"><i class="far fa-3x fa-calendar"></i><br>{{ __('all.workstations.events') }}</a>
        	</div>
        	<div class="col-xl-1 col-lg-2 col-md-3 col-sm-4 col-4 text-center pb-3 ws-action-icon">
            	<a href="javascript:" data-panel="operations" data-toggle="modal" data-target="#operationsPanel"><i class="fas fa-3x fa-hammer"></i><br>{{ __('all.workstations.interventions') }}</a>
        	</div>
        	<div class="col-xl-1 col-lg-2 col-md-3 col-sm-4 col-4 text-center pb-3 ws-action-icon">
            	<a href="javascript:" data-panel="connection" data-toggle="modal" data-target="#connectionsPanel"><i class="fas fa-3x fa-link"></i><br>{{ __('all.workstations.connections') }}</a>
        	</div>
        	@if($isLAN)
        		<div class="col-xl-1 col-lg-2 col-md-3 col-sm-4 col-4 text-center pb-3 ws-action-icon">
            		<a href="javascript:" data-panel="control" data-toggle="modal" data-target="#controlPanel"><i class="fas fa-3x fa-terminal"></i><br>{{ __('all.workstations.consol') }}</a>
        		</div>
        	@endif
        	<div class="col-xl-1 col-lg-2 col-md-3 col-sm-4 col-4 text-center pb-3 ws-action-icon">
            	<a href="javascript:" data-panel="network" data-toggle="modal" data-target="#networkPanel"><i class="fas fa-3x fa-network-wired"></i><br>{{ __('all.workstations.network') }}</a>
        	</div>
        	<div class="col-xl-1 col-lg-2 col-md-3 col-sm-4 col-4 text-center pb-3 ws-action-icon" id="printerIcon">
            	<a href="javascript:" data-panel="printstats" data-toggle="modal" data-target="#printerPanel"><i class="fas fa-3x fa-chart-bar"></i><br>{{ __('all.workstations.printer_statistics') }}</a>
        	</div>
        	<div class="col-xl-1 col-lg-2 col-md-3 col-sm-4 col-4 text-center pb-3 ws-action-icon">
            	<a href="javascript:" data-panel="actions" data-toggle="modal" data-target="#actionsPanel"><i class="fas fa-3x fa-ellipsis-v"></i><br>{{ __('all.workstations.actions') }}</a>
        	</div>
       </div>
        			
       <div class="modal fade" id="profilePanel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  			<div class="modal-dialog modal-lg" role="document">
    			<div class="modal-content">
      				<div class="modal-header">
        				<h5 class="modal-title">{{ $workstation->alias }}</h5>
        				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          					<span aria-hidden="true">×</span>
        				</button>
      				</div>
      				<div class="modal-body">
                		<div class="row">
       	                 	<div class="col-12">
        						<strong>{{ __('all.workstations.identification_details') }}</strong><br>
                				{{ __('all.workstations.product_serial') }}: <span data-table="workstations" data-field="product_serial">{{ $workstation->product_serial}}</span> @if(in_array('write-workstation', $userPermissions)) <a href="javascript:void(0)" class="edit"><i class="fas fa-edit"></i></a> @endif<br>
                    			{{ __('all.workstations.motherboard_serial') }}: <span data-table="workstations" data-field="mboard_serial">{{ $workstation->mboard_serial}}</span> @if(in_array('write-workstation', $userPermissions)) <a href="javascript:void(0)" class="edit"><i class="fas fa-edit"></i></a> @endif<br>
                    			UUID: <span data-table="workstations" data-field="uuid">{{ $workstation->uuid}}</span> @if(in_array('write-workstation', $userPermissions)) <a href="javascript:void(0)" class="edit"><i class="fas fa-edit"></i></a> @endif<br>
                    			{{ __('all.workstations.first_mac_address') }}: <span data-table="workstations" data-field="first_mac">{{ $workstation->first_mac}}</span> @if(in_array('write-workstation', $userPermissions)) <a href="javascript:void(0)" class="edit"><i class="fas fa-edit"></i></a> @endif
                            	<hr />
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 mb-2">
                        		<strong>{{ __('all.workstations.brand_model') }}</strong><br>
                				<span  data-table="workstations" data-field="hardware">{{ $workstation->hardware}}</span> @if(in_array('write-workstation', $userPermissions)) <a href="javascript:void(0)" class="edit"><i class="fas fa-edit"></i></a> @endif<br>
                    			<div>{{ __('all.workstations.registered') }}: {{ \Carbon\Carbon::parse($workstation->created_at)->format("Y.m.d H:i:s") }}</div>
                				{{ __('all.workstations.type') }}:
								@if(in_array('write-workstation', $userPermissions))
                				<select id="device-type" name="type" data-id="{{ $workstation->id }}" data-table="workstations" data-field="type" class="inline-form form-control-sm">
                					<option value="desktop">{{ __('all.workstations.desktop') }}</option>
                					<option value="laptop" @if($workstation->type == "laptop") SELECTED @endif>{{ __('all.workstations.laptop') }}</option>
                					<option value="server" @if($workstation->type == "server") SELECTED @endif>{{ __('all.workstations.server') }}</option>
                				</select>
                                @else
                                	{{ $workstation->type }}
                                @endif
                			</div>
                			<div class="col-lg-6 col-md-6 col-sm-12 mb-2">
                      	  		<strong>{{ __('all.workstations.processor') }}</strong><br>
                				{{ $workstation->cpu }}<br>
								<a href="https://www.cpubenchmark.net/cpu.php?cpu={{ $workstation->cpu }}" target="_blank">CPU Benchmark:</a> <span data-table="workstations" data-field="cpu_score">{{ $workstation->cpu_score ?? "N/A" }}</span> @if(in_array('write-workstation', $userPermissions)) <a href="javascript:void(0)" class="edit"><i class="fas fa-edit"></i></a> @endif<br>
                           		{{ __('all.workstations.processor_released') }}: <span class="text-muted" data-table="workstations" data-field="cpu_release_date">{{ $workstation->cpu_release_date }}</span> @if(in_array('write-workstation', $userPermissions)) <a href="javascript:void(0)"  class="edit"><i class="fas fa-edit"></i></a> @endif
                			</div>
                        	<div class="col-lg-6 col-md-6 col-sm-12 mb-2">
                            	<strong>{{ __('all.workstations.memory') }}</strong><br>
                				{{ $workstation->ram}}MB @if(isset($workstation->ram_slots) && isset($workstation->ram_max_capacity)) (Max: @if ($workstation->ram_max_capacity/1024 < 1024) {{$workstation->ram_max_capacity/1024 }}GB @else {{ $workstation->ram_max_capacity/1024/1024 }}GB @endif - {{$workstation->ram_slots}} slot) @endif
                                @if($workstation->memories()->count() > 0)	
                                   	@foreach($workstation->memories()->get() as $memory)
                                   		<div>{{$memory->manufacturer}} {{ $memory->capacity }}MB x{{ $memory->speed }}MHz {{ $memory->type() }} ({{ $memory->slot }})</div>
                                   	@endforeach
                                @endif
                			</div>
                            <div class="col-lg-6 col-md-6 col-sm-12 mb-2">
                        		<strong>{{ __('all.workstations.operating_system') }}</strong><br>
                				@if($workstation->fast_startup == 1) <i class="fas fa-bolt text-warning" title="{{ __('all.workstations.hyberboot_active') }}"></i>  @endif {{ $workstation->os}}
								@if (isset($workstation->os_activated))
									@if($workstation->os_activated == 0) <div><span class="text-danger"><i class="fas fa-times"></i> {{ __('all.workstations.os_not_activated') }}</span>  </div>@endif
									@if($workstation->os_activated == 1) <div><span class="text-success"><i class="fas fa-check"></i> {{ __('all.workstations.os_activated') }}</span>  </div>@endif
                                @endif
                				<div>{{ __('all.workstations.last_os_update') }}: {{ $workstation->wu_installed}}</div>
                                <div>{{ __('all.workstations.last_os_update_search') }}: {{ $workstation->wu_checked}}</div>
                            	<div>{{ __('all.workstations.workgroup') }}: <strong>{{ $workstation->workgroup ?? "" }}</strong></div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 mb-2">
                        		<strong>{{ __('all.workstations.monitors') }} ({{ $workstation->monitors()->count() }})</strong><br>
                				@if($workstation->monitors()->first() != null)	
                                   	@foreach($workstation->monitors()->get() as $monitor)
                                   		<div>{{$monitor->manufacturer}} {{$monitor->name}} ({{$monitor->serial}}) <i class="fas fa-warehouse text-muted"></i> <span data-table="ws_monitors" data-id="{{ $monitor->id }}" data-field="inventory_id">{{ $monitor->inventory_id ?? "N/A" }}</span> @if(in_array('write-workstation', $userPermissions)) <a href="javascript:void(0)" class="edit"><i class="fas fa-edit"></i></a> @endif</div>
                                   	@endforeach
                                @endif
                			</div>
                            <div class="col-lg-6 col-md-6 col-sm-12 mb-2">
                        		<strong>{{ __('all.workstations.last_boot_time') }}:</strong><br>
                				{{ $workstation->bootup_at }}
								@if($workstation->startup_at != null) <div>{{ __('all.workstations.service_started') }}: {{ $workstation->startup_at }} </div>@endif
                            	@if($workstation->boot_time != null) <div>{{ __('all.workstations.boot_time') }}: {{ $workstation->boot_time }} {{ __('all.workstations.filter_seconds') }} </div>@endif
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 mb-2">
                        		<strong>{{ __('all.workstations.disks') }} ({{$workstation->hdds()->count()}})</strong><br>
                				@foreach($workstation->hdds()->get() as $hdd)
                                	<div><span class="badge badge-secondary">{{$hdd->mediatype}}</span> {{$hdd->model}} ({{ $hdd->capacity }}GB) {{ $hdd->status }}</div>
                                @endforeach
                			</div>
                		    <div class="col-lg-6 col-md-6 col-sm-12 mb-2">
                        		<strong>{{ __('all.workstations.os_partition_size') }}</strong><br>
                				{{ $workstation->os_drive_size}}GB ({{ __('all.workstations.os_partition_free_space') }}: {{ $workstation->os_drive_free_space}}GB)
                			</div>
                            <div class="col-lg-6 col-md-6 col-sm-12 mb-2">
                        		<strong>{{ __('all.workstations.labels') }}</strong> @if(in_array('write-workstation', $userPermissions)) <a href="javascript:" class="btn btn-primary btn-sm py-0 my-0" data-toggle="modal" data-target="#newLabelModal"><i class="fas fa-plus"></i> {{ __('all.button.new_label') }}</a> @endif<br>
                				<div class="ws-labels">
                                	@foreach($workstation->labels as $label)
                                		<span class="badge {{ ($label->prop == "SYSTEM")?'badge-danger':'badge-secondary' }} mr-1"> @if(in_array('write-workstation', $userPermissions)) <a href="javascript:" class="text-light delete-label" data-id="{{ $label->id }}"><i class="fa fa-times"></i></a> @endif {{ $label->name }}</span>
                                	@endforeach
                                </div>
                			</div>
                            <div class="col-lg-6 col-md-6 col-sm-12 mb-2">
                            	<strong>{{ __('all.workstations.ip_addresses') }} ({{$workstation->ips()->count()}})</strong><br>
                               	@foreach($workstation->dns as $dns)
                                   	<div><strong>DNS: {{$dns->ip}}</strong></div>
                                @endforeach
                                   	<div>MAC: {{ $workstation->active_mac }}</div>
                                @foreach($workstation->ips as $ip)
                                	<div> @if(in_array('write-workstation', $userPermissions)) <a href="javascript:void(0)"  class="text-danger delete-ip-btn" data-id="{{ $ip->id }}"><i class='fas fa-times-circle'></i></a> @endif {{$ip->ip}}</div>
                                @endforeach
                			</div>
                            <div class="col-lg-6 col-md-6 col-sm-12 mb-2">
                            	<strong>{{ __('all.workstations.local_user_accounts') }} ({{$workstation->accounts()->count()}})</strong><br>
                				@foreach($workstation->accounts as $account)
                              		<div @if( $account->is_admin) class="text-danger" data-id="" @endif>{{$account->username}} @if( $account->is_admin) ({{ __('all.workstations.administrator') }}) @endif</div>
                               	@endforeach
                		    </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 mb-2">
                        		<strong>{{ __('all.workstations.printers') }} ({{$workstation->printers()->count()}})</strong><br>
                                @foreach($workstation->printers as $printer)
                                	<div>@if($workstation->status() == "online" || $workstation->status() == "idle") @if(in_array('write-workstation', $userPermissions)) <a href='javascript:' class='control-btn text-danger delete-printer-btn' data-command='Remove-Printer -Name "{{$printer->name}}"'><i class='fas fa-times-circle'></i></a> @endif  @endif  @if($printer->default == 1) <span class="text-success"><i class="fas fa-check-circle" title="Alapértelmezett nyomtató"></i></span> @endif @if(strpos($printer->port, "USB") !== false) <span class="text-muted"><i class="fab fa-usb" title="USB"></i></span> @endif @if(preg_match('/\b((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])\.){3}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])\b/', $printer->port) == true) <span class="text-muted"><i class="fas fa-network-wired" title="Ethernet"></i></span> @endif @if($printer->shared == 1) <span class="text-muted"><i class="fas fa-share-alt-square"  title="Megosztott nyomtató"></i></span> @endif <span title="{{ $printer->port }}">{{$printer->name}}</span></div>
                                @endforeach
                			</div>
                		</div>
               		</div>
				</div>
  			</div>
		</div>
        
        	
		<div class="modal fade" id="eventsPanel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  			<div class="modal-dialog modal-lg" role="document">
    			<div class="modal-content">
      				<div class="modal-header">
        				<h5 class="modal-title"><i class="fas fa-circle text-danger blink"></i><span class="text-muted">LIVE</span>  {{ $workstation->alias }}</h5>
        				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          					<span aria-hidden="true">×</span>
        				</button>
      				</div>
      				<div class="modal-body">
                		<div class="row">
       	                 	<div class="col-12">
                            	        		<div class="table-responsive">
                			<table class="table table-striped">
                				<thead class="thead-dark">
                                    <tr>
                						<th>
                                    		{{ __('all.workstations.event_time') }}
                                    	</th>
                                    	<th>
                                    		{{ __('all.workstations.event') }}
                                    	</th>
                                    	<th>
                                    		{{ __('all.workstations.event_description') }}
                                    	</th>
                                   </tr>
                                </thead>
                                <tbody>
                            		@php
                            		$events = $workstation->events()->take(30)->orderBy('created_at','DESC')->get();
                            		@endphp
                                    @foreach($events as $event)
                                    	<tr {{ @(($event->event == "SYSTEM") ?  "class=font-weight-bold"  : "") }}>
                                    		<td class="filterable-cell">{{ \Carbon\Carbon::parse($event->created_at)->format("Y-m-d H:i:s") }}</td>
                                    		<td>{{ $event->event }}</td>
                                    		<td>{{ $event->description }}</td>
                                    	</tr>
                                    @endforeach
                                </tbody>
                            </table>
                         
                         </div>
        
       						</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		
		<div class="modal fade" id="operationsPanel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  			<div class="modal-dialog modal-lg" role="document">
    			<div class="modal-content">
      				<div class="modal-header">
        				<h5 class="modal-title">{{ $workstation->alias }}</h5>
        				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          					<span aria-hidden="true">×</span>
        				</button>
      				</div>
      				<div class="modal-body">
                		<div class="row">
                		 <div class="table-responsive">
                         	@if(in_array('write-intervention', $userPermissions)) <a href="javascript:" class="btn btn-sm btn-primary context-action ml-2" data-action='service' data-id='{{ $workstation->id }}' id="btn-new-fix"><i class="fas fa-plus"></i> {{ __('all.button.new_intervention') }}</a> @endif
                			<table class="table table-striped mt-2">
                				<thead class="thead-dark">
                                    <tr>
                						<th>
                                    		{{ __('all.workstations.intervention_time') }}
                                    	</th>
                                    	<th>
                                    		{{ __('all.workstations.intervention_description') }}
                                    	</th>
                                   </tr>
                                </thead>
                                <tbody>
                                    	
                                    
                                    @foreach($workstation->interventions()->take(30)->orderBy('created_at','DESC')->get() as $intervention)
                                    	<tr>
                                    		<td class="filterable-cell">{{ \Carbon\Carbon::parse($intervention->created_at)->format("Y.m.d H:i") }}</td>
                                    		<td>{{ $intervention->description }}</td>
                                    	</tr>
                                    @endforeach
                                    
                                    
                                </tbody>
                            </table>
                         
                         </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

		<div class="modal fade" id="networkPanel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  			<div class="modal-dialog modal-lg" role="document">
    			<div class="modal-content">
      				<div class="modal-header">
        				<h5 class="modal-title">{{ $workstation->alias }}</h5>
        				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          					<span aria-hidden="true">×</span>
        				</button>
      				</div>
      				<div class="modal-body">
                		<div class="row">
       	                 	<div class="col-12">
                               <form class="form-horizontal mt-4">
                          		{{ csrf_field()}}
							 <div class="form-group row">
                                <div class="col-2">
                                    <label class="form-control-label">{{ __('all.workstations.endpoint_network_connection') }}</label>
                                </div>
                            	<div class="col-10">
                                    <select class="form-control" name="target" id="network-connection">
                            		
                                    	@foreach($networkdevices as $networkdevice)
                                    		<option value="{{ $networkdevice->id }}" @if(isset($connection)) @if($networkdevice->id == (int) filter_var($connection, FILTER_SANITIZE_NUMBER_INT)) SELECTED @endif @endif>{{$networkdevice->alias}} ({{$networkdevice->hardware}} - {{$networkdevice->ports}}P)</option>
                                    	@endforeach
                                    </select>
                                </div>
                            </div>
                           <div class="form-group row">
                                <div class="col-12">
                           			<a href="javascript:void(0)" id="save-network-connection"  class="btn btn-primary"><i class="fas fa-spinner d-none loading-icon"></i> {{ __('all.button.save') }}</a>
                             		
                                </div>
                           </div>
                        </form>            

       						</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		
		
		<div class="modal fade" id="controlPanel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  			<div class="modal-dialog modal-lg" role="document">
    			<div class="modal-content">
      				<div class="modal-header">
        				<h5 class="modal-title">{{ $workstation->alias }}</h5>
        				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          					<span aria-hidden="true">×</span>
        				</button>
      				</div>
      				<div class="modal-body">
                		<div class="row">
       	                 	<div class="col-12">
   	                    	<div class="mt-2" id="control-buttons">
                			<div class="input-group">
                            	<div class="input-group-prepend">
                            	    <a href="javascript:" data-command="hello" class="btn btn-info control-btn">"Hi"</a>
                                	<a href="javascript:" data-command="power" class="btn btn-dark control-btn" title="{{ __('all.workstations.turn_on_off_computer') }}"><i class="fas fa-power-off"></i></a>
                                	<a href="javascript:" data-command="Restart-Computer -Force" class="btn  btn-warning control-btn" title="{{ __('all.workstations.restart_computer') }}"><i class="fas fa-redo-alt"></i></a>
                                	<a href="javascript:" data-command="net stop spooler;net start spooler;" class="btn btn-dark control-btn" title="{{ __('all.workstations.restart_spooler') }}"><i class="fas fa-print"></i> <i class="fas fa-redo-alt"></i></a>
                            		<a href="javascript:" data-command="Clear-Spooler" class="btn btn-dark control-btn" title="{{ __('all.workstations.clear_and_restart_spooler') }}"><i class="fas fa-print"></i> <i class="fas fa-broom"></i></a>
                            	</div>
                                <select id="script-storage" class="custom-select">
                            	@foreach($scripts as $script)
                            		<option value="{{ $script->code }}">{{ $script->alias }}</option>
                            	@endforeach
                            	</select>
                                <div class="input-group-append">
                                    <a href="javascript:" id="execute-script" class="btn btn-primary"><i class="fas fa-terminal"></i> {{ __('all.button.run') }}</a>
                                </div>
                            </div>
                            
						</div>
                    	<div class="col-12 mt-4" id="console-container" style="height: 70%;overflow-y:scroll;background-color:#000;color:#FFF">
                            <p>BigLan Remote Console (Powershell/CMD)<br>Hello {{ Auth::user()->username }}! {{ __('all.workstations.console_fun') }}</p>
                            <div id="history"></div>
                            <p><span style="width:100%">PS>></span><span id="console" contenteditable="true" style="outline: none;"></span></p>
                        </div>

       						</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		
		<div class="modal fade" id="actionsPanel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  			<div class="modal-dialog modal-lg" role="document">
    			<div class="modal-content">
      				<div class="modal-header">
        				<h5 class="modal-title">{{ $workstation->alias }}</h5>
        				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          					<span aria-hidden="true">×</span>
        				</button>
      				</div>
      				<div class="modal-body">
                		<div class="row">
       	                 	<div class="col-12">
                            	@if(in_array('delete-workstation', $userPermissions))
                            		<a href="javascript:void(0)" class="btn btn-danger m-2" id="archive" data-id="{{ $workstation->id }}">{{ __('all.button.archive') }}</a>
                            	@endif
                    			<div id="waiting" class="m-2"><i class="fas fa-circle-notch fa-spin"></i> {{ __('all.workstations.archive_in_progress') }}</div>
       						</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                             
        <div class="modal fade" id="connectionsPanel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  			<div class="modal-dialog modal-lg" role="document">
    			<div class="modal-content">
      				<div class="modal-header">
        				<h5 class="modal-title">{{ __('all.workstations.connections') }}</h5>
        				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          					<span aria-hidden="true">×</span>
        				</button>
      				</div>
      				<div class="modal-body">
                		<div class="row">
       	                 	<div class="col-12 table-responsive">
                            		<table id="connection-table" class="table table-striped">
                             			<thead>
                             				<th></th>
                             				<th>{{ __('all.workstations.conn_type') }}</th>
                             				<th>{{ __('all.workstations.conn_value') }}</th>
                             				<th>{{ __('all.workstations.conn_notes') }}</th>
                             				<th>{{ __('all.workstations.conn_action') }}</th>
                             			</thead>
                             		@php
                             			$types = [
                             				"vnc" => ["name" => "RealVNC",
                                                      "url" => "com.realvnc.vncviewer.connect://",
                                                      "action" => __('all.workstations.connect')
                                                      ],
                             				"anydesk" => ["name" => "Anydesk",
                                                      "url" => "anydesk:",
                                                      "action" => __('all.workstations.connect')
                                                      ],
                             				"teamviewer" => ["name" => "Teamviewer",
                                                      "url" => "https://start.teamviewer.com/",
                                                      "action" => __('all.workstations.connect')
                                                      ],
                             				"phone" => ["name" => __('all.workstations.phone'),
                                                      "url" => "tel:",
                                                      "action" =>	__('all.workstations.call')
                                                      ],
                             				"email" => ["name" => __('all.workstations.email'),
                                                      "url" => "mailto:",
                                                      "action" => __('all.workstations.send')
                                                      ],
                             				"location" => ["name" => __('all.workstations.location'),
                                                      "url" => "https://www.google.com/maps?q=",
                                                      "action" => __('all.workstations.navigate')
                                                      ],
                             				"url" => ["name" => "URL",
                                                      "url" => "",
                                                      "action" => __('all.workstations.open')
                                                      ]
                             			];
                             		@endphp
                                    <tbody>
                             		@foreach($connections as $connection)
                            			<tr data-id="{{ $connection->id }}">
                             				<td><a href="javascript:" data-id="{{ $connection->id }}" class="delete-connection text-danger"><i class="fas fa-trash-alt"></i></a></td><td>{{$types[$connection->type]["name"]}}</td><td>{{ $connection->value }}</td><td>{{ $connection->notes }}</td><td><a class="btn btn-sm btn-secondary" href="{{$types[$connection->type]["url"]}}{{ $connection->value }}">{{$types[$connection->type]["action"]}}</a></td>
                                        </tr>
                             		@endforeach
                                	</tbody>
                                    </table>            
                                @if($connections->count() == 0)
                             		<p id="connection-not-found">{{ __('all.workstations.no_information_found') }}</p>      
                                @endif
                                <div id="connection-form">
                                    <form class="form-inline">        
                                		<select id="connection-type" name="connection_type" class="form-control mr-2">
                                            <option value="vnc">RealVNC</option>
                                            <option value="anydesk">AnyDesk</option>
                                            <option value="teamviewer">TeamViewer</option>
                                            <option value="phone">{{ __('all.workstations.phone') }}</option>
                                            <option value="email">{{ __('all.workstations.email') }}</option>
                             				<option value="location">{{ __('all.workstations.location') }}</option>
                                    		<option value="url">URL</option>
                                    	</select>
                                    	<input type="text" name="connection_value" id="connection-value" class="form-control mr-2" placeholder="IP[:PORT]" autocomplete="off">
                                        <input type="text" name="connection_notes" id="connection-notes" class="form-control mr-2" placeholder="{{ __('all.workstations.conn_notes') }}" autocomplete="off">
                                        <a href="javascript:" class="btn btn-sm btn-primary mr-2" id="save-connection">{{ __('all.workstations.save') }}</a>
                                        <a href="javascript:" class="btn btn-sm btn-light mr-2" id="cancel-connection">{{ __('all.workstations.cancel') }}</a>
                                    </form>
                                </div>
                                <div class="mt-3">
                                	<a href="javascript:" class="btn btn-sm btn-primary" id="add-connection">{{ __('all.workstations.add_connection') }}</a>
                                </div>
                            	
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
              
        <!-- printer statistics modal start -->                            
        <div class="modal fade" id="printerPanel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  			<div class="modal-dialog modal-lg" role="document">
    			<div class="modal-content">
      				<div class="modal-header">
        				<h5 class="modal-title">{{ __('all.workstations.printer_statistics') }}</h5>
        				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          					<span aria-hidden="true">×</span>
        				</button>
      				</div>
      				<div class="modal-body">
                		<div class="row">
                        	<div class="col-12">
                            	<p class="monthlystat"></p>
                                <div id="print-graph">
                                        			
                                </div>
                            </div>
                    	</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- printer statistics modal end -->                            
                             
        <!-- script saver modal -->                    
        <div class="modal fade" id="scriptStorageModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  			<div class="modal-dialog" role="document">
    			<div class="modal-content">
      				<div class="modal-header">
        				<h5 class="modal-title" id="exampleModalLabel">{{ __('all.workstations.script_storage') }}</h5>
        				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          					<span aria-hidden="true">×</span>
        				</button>
      				</div>
      				<div class="modal-body">
        				{{ __('all.workstations.script_name') }}: <input type="text" name="code-alias" class="form-control">
                        {{ __('all.workstations.script') }}: <input type="text" class="form-control" name="code" value="">
      				</div>
      				<div class="modal-footer">
        				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('all.button.cancel') }}</button>
        				<button type="button" id="btn-save-script" class="btn btn-primary">{{ __('all.button.save') }}</button>
      				</div>
    			</div>
  			</div>
		</div>
                            
        <!-- script saver modal end -->
        
     	<!-- new label modal -->
                        
		 <div class="modal fade" id="newLabelModal" tabindex="-1" role="dialog" aria-labelledby="newLabelModalLabel" aria-hidden="true">
  			<div class="modal-dialog" role="document">
    			<div class="modal-content">
      				<div class="modal-header">
        				<h5 class="modal-title" id="newLabelModalLabel">{{ __('all.button.new_label') }}</h5>
        				<button type="button" class="close" data-dismiss="modal" aria-label="{{ __('all.button.close') }}">
          					<span aria-hidden="true">×</span>
        				</button>
      				</div>
      				<div class="modal-body">
        				{{ __('all.workstations.label_text') }}: <input type="text" name="label-name" class="form-control">
                    </div>
      				<div class="modal-footer">
        				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('all.button.cancel') }}</button>
        				<button type="button" id="btn-save-label" class="btn btn-primary" data-dismiss="modal">{{ __('all.button.save') }}</button>
      				</div>
    			</div>
  			</div>
		</div>
                        
	@endsection
    @section('inject-footer')
	<style type="text/css">
    #print-graph {
        	height: 200px;
        	border: 1px solid #DDD;
        }
	
		.graph-bar {
        	background-color: #000;
        	width: 3%;
        	margin-left: 0.33%;
        	height: 100%;
        	float: left;
        	font-size: 10px;
        	text-align: center;
        }

		.graph-value {
        	background-color: #EEE;
        	width: 100%;
        	float: left;
        	
        }
    </style>
	
	
 	<script type="text/javascript">
    $(function() {
    	
    	
    	var boolCheckNewEvents = false;
    	var online = false;
    	var pingTimer;
    	var elapsedTimer = 0;
    	var timer = setInterval(function(){
        							checkStatus();
        							if (boolCheckNewEvents) {
                                    	checkNewEvents();
                                    }
        						}, 5000);
    
    	var cmdPufferIndex = 0;
    	var cmdPuffer = [];
    	
    	checkStatus();

    	$(document).on("click", '#printerIcon', function() {
        
        	$("#print-graph").html("");
        	payLoad = {};
        	payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
        	payLoad['action'] = 'printGraph';
            payLoad['wsid'] = {{ $workstation->id }};
            var showPrints = $.post("{{ url('workstations/payload') }}", payLoad, "JSONP");
        	showPrints.done(function(data) {
            //console.log(data);
            	for(var i = 1; i <= 30; i++) {
                	var height = 101-((Math.ceil((data.printarray[i]["pages"])/data.max*100)));
                	if ((data.printarray[i]["pages"] == 0)) {height = 100;}
                	$("#print-graph").append("<div class='graph-bar' title='"+ data.printarray[i]["date"] +" (" +data.printarray[i]["pages"]+" {{ __('all.workstations.pages') }}, " +data.printarray[i]["counter"]+" {{ __('all.workstations.prints') }})'><div class='graph-value' style='height:" + height + "%'></div></div>");    
                }
            $(".monthlystat").html("{{ __('all.workstations.monthly_prints') }}: "+ data.allcounter +"<br>{{ __('all.workstations.monthly_pages') }}: "+ data.allpages);
            });
        });
    
    
    	$("#waiting, #connection-form").hide();

    	$("#controlPanel").on("shown.bs.modal", function(){
    	
        	$("#console-container").height(($("#controlPanel .modal-dialog").scrollTop() + $("#controlPanel .modal-dialog").height() - $("#console-container").offset().top)+"px");
        
        });
    	
    	$("#eventsPanel").on("shown.bs.modal", function(){
        	boolCheckNewEvents = true;
        });
    
    	$("#eventsPanel").on("hidden.bs.modal", function(){
        	boolCheckNewEvents = false;
        });
    
    	$("#eventsPanel .modal-body").on("scroll", function() {
        	var top = $(this).scrollTop();
        	var height = $(this).innerHeight();
        	var scrollHeight = $(this).prop("scrollHeight");
        	//console.log(top+height, scrollHeight);
        	if (Math.round(top + height) >= scrollHeight - 2) {
            	checkOlderEvents();
            }
        });
        
    	$("#add-connection").on('click', function (e) {
        	$("#connection-form").show();
    	});
    
    	$("#cancel-connection").on('click', function (e) {
        	$("#connection-form").hide();
    	});
    
		function getWsConnections() {
        
        	payLoad = {};
        	payLoad["_token"] = $('meta[name=csrf-token]').attr('content');
            payLoad["action"] = "getWorkstationConnections";
        	payLoad["wsid"] = {{ $workstation->id }};
        	var posting = $.post("{{ url('workstations/payload') }}", payLoad , "JSONP");
        	posting.done(function(data) {
            	//console.log(data);
            	if(data!="ERROR") {
                	$("#connection-table tbody").html("");	
                	for(i = 0; i < data.length; i++) {
                    	$("#connection-table tbody").append("<tr data-id='"+data[i].id+"'>");
                        $("#connection-table tbody").append("<td><a href='javascript:' data-id='"+data[i].id+"' class='delete-connection text-danger'><i class='fas fa-trash-alt'></i></a></td><td>"+data[i].name+"</td><td>"+data[i].value+"</td><td>"+data[i].notes+"</td><td><a class='btn btn-sm btn-secondary' href='"+data[i].url+"'>"+data[i].action+"</a></td>");
                        $("#connection-table tbody").append("</tr>");
                    }
                	$("#connection-not-found").hide();
                	$("#connection-form").hide();
                }
        	});
        
        }

    
    	$("#save-connection").on('click', function (e) {
        	payLoad = {};
        	payLoad["_token"] = $('meta[name=csrf-token]').attr('content');
            payLoad["action"] = "saveWorkstationConnection";
        	payLoad["type"] = $("#connection-type").val();
        	payLoad["value"] = $("#connection-value").val();
        	payLoad["notes"] = $("#connection-notes").val();
        	payLoad["wsid"] = {{ $workstation->id }};
        	var posting = $.post("{{ url('workstations/payload') }}", payLoad , "JSONP");
        	posting.done(function(data) {
            	if(data=="OK") {
                	getWsConnections();
                	//$("#connection-table").append('<tr><td></td><td>'+payLoad["type"]+'</td><td>'+payLoad["value"]+'</td><td>'+payLoad["notes"]+'</td><td></td></tr>');
                	$("#connection-not-found").hide();
                	$("#connection-form").hide();
                }
        	});
    	});
    
    	$("body").on("click", ".delete-connection", function() {
        	var confirm = window.confirm("{{ __('all.workstations.are_you_sure_delete_connection') }}");
            if(!confirm) {
            	return;
            }	
        
        	var connection = $(this);
        	var payLoad = {};
        	payLoad["_token"] = $('meta[name=csrf-token]').attr('content');
        	payLoad["action"] = "deleteWorkstationConnection";
        	payLoad["id"] = $(this).attr("data-id");
        
        	var deleteConnection = $.post("{{ url('workstations/payload') }}", payLoad, "JSONP");
        	deleteConnection.done(function(data) {
        		if (data == "OK") {
                	//console.log(data);
                	//connection.parents("tr").remove();
                	getWsConnections();
                } else {
                	//console.log(data);
                }
        	});
       	});
    	
    	$("#connection-type").on('change', function (e) {
        	//console.log($("#connection-type").find(":selected").val());
        	var selectedValue = $("#connection-type").find(":selected").val()
        	switch(selectedValue) {
            	case "vnc":
            		$("#connection-value").attr("placeholder", "IP[:PORT]");
            		break;
            	case "anydesk":
            		$("#connection-value").attr("placeholder", "AnyDesk ID");
            		break;
            	case "teamviewer":
            		$("#connection-value").attr("placeholder", "TeamViewer ID");
            		break;
            	case "phone":
            		$("#connection-value").attr("placeholder", "+36XXXXXXXX,XXX");
            		break;
            	case "email":
            		$("#connection-value").attr("placeholder", "email@domain.com");
            		break;
            	case "location":
            		$("#connection-value").attr("placeholder", "address or coordinates");
            		break;
            	case "url":
            		$("#connection-value").attr("placeholder", "https://");
            		break;
            	default:
            		break;
            }
        	
    	});
    
    	
    
    	$("#console-container").on('click', function (e) {
        	$("#console").focus();
    	});
    
    	$(document).on("click", ".control-btn", function() {
        	var confirm = window.confirm("{{ __('all.workstations.are_you_sure_run') }}");
            if(!confirm) {
            	return;
            }
            
        	var btn = $(this);
        	
        	if (btn.hasClass("delete-printer-btn")) {
           		btn.parent("div").hide();
        	}
            
        	var command = btn.attr("data-command");
        	if (command == "power") {
            	if (online) {
                	executeCommand("Stop-Computer -Force");
                } else {
                	WOL();
                }
            } else {
        		$("#console").text(command);
        		executeCommand(command);
            }
        });
    
    	function ping() {
        
        	clearTimeout(pingTimer);
        	var posting = $.post("{{ url('workstations/payload') }}", { '_token': $('meta[name=csrf-token]').attr('content'), action: 'ping', wsid: {{ $workstation->id }}}, "JSONP");
        	posting.done(function(data) {
            	//console.log(data);
            	if (data == "0") {
              		$("#history").append("|");
                } else {
                	$("#history").append(".");
                }
        		$("#console-container").scrollTop($("#console-container").prop("scrollHeight"));
            	if (elapsedTimer < 60 && online == 0) {
					pingTimer = setTimeout(function() { ping(); }, 2000);      	
            	} else {
            		$(".control-btn[data-command=power]").html("<i class='fas fa-power-off'></i>");
            		$("#history").append("]<br>");
        			$("#console-container").scrollTop($("#console-container").prop("scrollHeight"));
            		elapsedTimer = 0;
        		}
        	});
        	elapsedTimer = elapsedTimer + 2;
        }
    
    	function WOL() {
        	$(".control-btn[data-command=power]").html("<i class='fas fa-circle-notch fa-spin'></i>");
        	var posting = $.post("{{ url('workstations/payload') }}", { '_token': $('meta[name=csrf-token]').attr('content'), action: 'WOL', wsid: {{ $workstation->id }}}, "JSONP");
        	posting.done(function(data) {
            	if (data == 0) {
            		$("#history").append("WOL [");
        			$("#console-container").scrollTop($("#console-container").prop("scrollHeight"));
            		ping();
                }
            	if (data == 1) {
            		$("#history").append("<p>Error: No MAC found to this host.</p>");
        			$("#console-container").scrollTop($("#console-container").prop("scrollHeight"));
                	$(".control-btn[data-command=power]").html("<i class='fas fa-power-off'></i>");
                }
            
            });
        }
    
    	$("#execute-script").on("click", function() {
			var confirm = window.confirm("Biztos, hogy lefuttatod ezt a szkriptet?");
            if(!confirm) {
            	return;
            }
            
        	var command = $("#script-storage").val();
        	$("#console").text(command);
        	executeCommand(command);
        });
    
    	function makeOffline() {
        	$(".control-btn, #script-storage, #execute-script").addClass("disabled");
        	$(".control-btn[data-command=power]").removeClass("disabled").removeClass("btn-danger").addClass("btn-dark");
        	$("#console").hide();
        	$("#console-container").css({ "color":"#777" });
        	$(".fas.fa-desktop, .fas.fa-laptop, .fas.fa-server").removeClass("text-success").removeClass("text-danger").removeClass("text-warning").addClass("text-muted");
        }
    
    	function makeOnline() {
        	$(".control-btn, #script-storage, #execute-script").removeClass("disabled");
        	$(".control-btn[data-command=power]").removeClass("disabled").removeClass("btn-dark").addClass("btn-danger");
        	$("#console").show();
        	$("#console-container").css({ "color":"#FFF" });
        	$(".fas.fa-desktop, .fas.fa-laptop, .fas.fa-server").removeClass("text-muted").removeClass("text-danger").removeClass("text-warning").addClass("text-success");
        }
    
    	
    	$("#save-network-connection").on("click", function() {
        	$("#save-network-connection").addClass("disabled");
        	$("#save-network-connection .loading-icon").removeClass("d-none");
        	
        	payLoad = {};
        	payLoad["_token"] = $('meta[name=csrf-token]').attr('content');
            payLoad["action"] = "saveWorkstationNetworkConnection";
        	payLoad["connection"] = $("#network-connection").val();
        	payLoad["wsid"] = {{ $workstation->id }};
        	var posting = $.post("{{ url('workstations/payload') }}", payLoad , "JSONP");
        	posting.done(function(data) {
            	setTimeout(function (){
                	$("#save-network-connection").removeClass("disabled");
                	$("#save-network-connection .loading-icon").addClass("d-none");
                }, 1000);
            	
        	});
        });
    
    	function checkStatus() {
        	var posting = $.post("{{ url('workstations/payload') }}", { '_token': $('meta[name=csrf-token]').attr('content'), action: 'checkStatus', id: {{ $workstation->id }}}, "JSONP");
        	posting.done(function(data) {
            	//console.log(data);
               	if(data == "online") {
                	makeOnline();
                	online = true;
                }
            	
            	if(data == "offline") {
                	makeOffline();
                	online = false;
                }
            	
            	if(data == "idle") {
                	makeOnline();
                	online = true;
                	$(".fas.fa-desktop, .fas.fa-laptop, .fas.fa-server").removeClass("text-muted").removeClass("text-success").removeClass("text-danger").addClass("text-warning");
                }
            
            	if(data == "hearbeatLoss") {
                	$(".fas.fa-desktop, .fas.fa-laptop, .fas.fa-server").removeClass("text-muted").removeClass("text-success").removeClass("text-warning").addClass("text-danger");
                }
            
        	});
        }
    
    	function checkNewEvents() {
        	$("#eventsPanel table tbody tr.newEvent").removeClass("newEvent");
        	var lastKnownDateTime = $("#eventsPanel table tbody tr:first td:first").text();
        	//console.log(lastKnownDateTime);
        	var posting = $.post("{{ url('workstations/payload') }}", { '_token': $('meta[name=csrf-token]').attr('content'), action: 'checkNewEvents', lastdate: lastKnownDateTime,id: {{ $workstation->id }}}, "JSONP");
        	posting.done(function(data) {
            	for(var i = 0; i < data.length; i++) {
                	$("#eventsPanel table tbody").prepend("<tr class='newEvent " + ((data[i].event == "SYSTEM")?"font-weight-bold":"") + "'><td>"+ data[i].formatted_at +"</td><td>"+ data[i].event +"</td><td>"+ ((data[i].description != null)?data[i].description:"") +"</td></tr>");
                }
        	});
        }
    
    	function checkOlderEvents() {
        	var oldestDateTime = $("#eventsPanel table tbody tr:last td:first").text();
        	//console.log($("#eventsPanel table tbody tr:last td:last"));
        	var posting = $.post("{{ url('workstations/payload') }}", { '_token': $('meta[name=csrf-token]').attr('content'), action: 'checkOlderEvents', lastdate: oldestDateTime,id: {{ $workstation->id }}}, "JSONP");
        	posting.done(function(data) {
            	//console.log(data);
            	for(var i = 0; i < data.length; i++) {
                	$("#eventsPanel table tbody").append("<tr "+  ((data[i].event == "SYSTEM")?"class=font-weight-bold":"") +"><td>"+ data[i].formatted_at +"</td><td>"+ data[i].event +"</td><td>"+ ((data[i].description != null)?data[i].description:"") +"</td></tr>");
                }
        	});
        }
    
    	$("#console").on("keyup", function(e) {
        	if(e.keyCode == 13) {
            	cmdPuffer.push($("#console").text());
            	cmdPufferIndex = cmdPuffer.length - 1;
            	executeCommand();
            }
        	if (cmdPuffer.length > 0) {
        		if(e.keyCode == 38) {
                	$("#console").text(cmdPuffer[cmdPufferIndex]);
                	cmdPufferIndex = cmdPufferIndex - 1;
            	}
        		if(e.keyCode == 40) {
                	cmdPufferIndex = cmdPufferIndex + 1;
            		$("#console").text(cmdPuffer[cmdPufferIndex]);
                }
        		if(cmdPufferIndex < 0) {
            		cmdPufferIndex = 0;
            	}
            
            	if(cmdPufferIndex >= cmdPuffer.length) {
            		cmdPufferIndex = cmdPuffer.length - 1;
            	}
        	}
                        
        });
    
    	$(document).on("click", ".btn-save-script", function() {
        	var code = $(this).attr("data-content");
        	$("input[name='code']").val(code);
        	$('#scriptStorageModal').modal("show");
        });
    
    	$("#btn-save-script").on("click", function() {
        	var command = $("input[name='code']").val();
        	var alias = $("input[name='code-alias']").val();
        	var posting = $.post("{{ url('workstations/payload') }}", { '_token': $('meta[name=csrf-token]').attr('content'), action: 'saveCommand', alias: alias, command: command}, "JSONP");
        	posting.done(function(data) {
            	$("#scriptStorageModal").modal("hide");
        	});
        });
    
    	function executeCommand(command) {
        	if(!command) {
            	command = $("#console").text();
            }
        	//console.log(command);
        	var posting = $.post("{{ url('workstations/payload') }}", { '_token': $('meta[name=csrf-token]').attr('content'), action: 'sendCommand', command: command, id: {{ $workstation->id }}}, "JSONP");
        	posting.fail(function() {
            	$("#history").append("<p>PS>>" + command + "</p>");
            	$("#history").append("<p>BigLan Server>> <span class='text-danger'>TCP response timed out.</span></p>");
            	$("#console-container").scrollTop($("#console-container").prop("scrollHeight"));
              	$("#console").text("");
            	$("#console").focus();
            }).done(function(data) {
            	$("#history").append("<p>PS>>" + command + " <a href='javascript:' class='btn-save-script' data-content='"+command+"' title='Save Script to Storage'><i class='fas fa-save text-primary'></i></a></p>");
            	$("#history").append("<pre style='color:#FFF'>" + data + "</pre>");
               	$("#console-container").scrollTop($("#console-container").prop("scrollHeight"));
              	$("#console").text("");
            	$("#console").focus();
        	});
        }
    
    	String.prototype.trimtab = function (){ 
  			return this.replace(/[\t]/g,); 
		}                   
                           
        String.prototype.explode = function (separator, limit)
		{
    		const array = this.split(separator);
    		if (limit !== undefined && array.length >= limit)
    			{
        			array.push(array.splice(limit - 1).join(separator));
    			}
    		return array;
		};
    	    
    	$("#btn-edit-alias").on("click", function() {
        	$("#alias").hide();
        	$("#alias-form").show();
        });
    
    	$("#cancel").on("click", function() {
        	$("#alias").show();
        	$("#alias-form").hide();
        });
    	
    	$("#save").on("click", function() {
        	
        	var posting = $.post("{{ url('workstations/payload') }}", { '_token': $('meta[name=csrf-token]').attr('content'), action: "editAlias", wsid: {{ $workstation->id }}, alias: $("#input-alias").val() }, "JSONP");
        	posting.done(function(data) {
        		$("#alias span").text(data.alias);
        		$("#alias").show();
        		$("#alias-form").hide();
        	});
       	});
    
    	$(".delete-ip-btn").on("click", function() {
        	var confirm = window.confirm("{{ __('all.workstations.are_you_sure_delete_ip') }}");
            if(!confirm) {
            	return;
            }	
        
        	var ip = $(this);
        	var payLoad = {};
        	payLoad["_token"] = $('meta[name=csrf-token]').attr('content');
        	payLoad["action"] = "deleteIP";
        	payLoad["id"] = $(this).attr("data-id");
        
        	var deleteIP = $.post("{{ url('subnets/payload') }}", payLoad, "JSONP");
        	deleteIP.done(function(data) {
        		if (data == "OK") {
                	ip.parent().remove();
                } else {
                	//console.log(data);
                }
        	});
       	});
    
    	
    
    	var originalContent = "";
    
    	//edit static text
    	$(".edit").on("click", function() {
        	var element = $(this).prev("span");
        	var table = element.attr("data-table");
        	var field = element.attr("data-field");
        	var id = element.attr("data-id");
        	var value = element.text();
        
        	originalContent = value;
        	element.html("<input type='text' class='form-control' value='"+value+"' name='"+field+"' data-table='"+table+"' data-id='"+id+"'>");
        	$("input[name="+field+"]").focus();
        	$(this).hide();
        });
    
    	$("#device-type").on("change", function() {
        	var element = $(this);
        	var table = element.attr("data-table");
        	var field = element.attr("data-field");
        	var id = element.attr("data-id");
        	var value = element.val();
        	var posting = $.post("{{ url('workstations/payload') }}", { '_token': $('meta[name=csrf-token]').attr('content'), action: "editWorkstation", id: id, table: table, wsid: {{ $workstation->id }}, field: field, value: value }, "JSONP");
        	posting.done(function(data) {
            	
            });
        });
    
    	$("body").on("keyup", ".form-control", function(e) {
        	var element = $(this);
        	var table = element.attr("data-table");
        	var id = element.attr("data-id");
        	var field = element.attr("name");
        	var value = element.val();
        	if (e.keyCode == 13) {
            	var posting = $.post("{{ url('workstations/payload') }}", { '_token': $('meta[name=csrf-token]').attr('content'), action: "editWorkstation", id: id, table: table, wsid: {{ $workstation->id }}, field: field, value: value }, "JSONP");
        		posting.done(function(data) {
                	//console.log(data);
        			element.parent("span").next(".edit").show();
            		element.parent("span").text(value);
            	});
            }
        	if (e.keyCode == 27) {
            	element.parent("span").next(".edit").show();
            	element.parent("span").text(originalContent);
            	originalContent = "";
            }
        });
    	
        $("#btn-save-label").on("click", function() {
        	var payLoad = {};
            payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            payLoad['wsid'] = {{ $workstation->id }};
            payLoad['action'] = 'createWsLabel';
            payLoad['name'] = $("#newLabelModal input[name='label-name']").val();
            var createWsLabel = $.post("{{ url('workstations/payload') }}", payLoad, "JSONP");
        	createWsLabel.done(function(data) {
               	$(".ws-labels").html("");
            	for(var i=0; i < data.labels.length; i++) {
                	$(".ws-labels").append("<span class='badge badge-secondary mr-1'><a href='javascript:' class='text-light delete-label' data-id='" + data.labels[i].id + "'><i class='fa fa-times'></i> </a> " + data.labels[i].name + "</span>");
                }
            });
        });
                           
        $(document).on("click", ".delete-label", function() {
        	var payLoad = {};
            payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            payLoad['wsid'] = {{ $workstation->id }};
            payLoad['action'] = 'deleteWsLabel';
            payLoad['id'] = $(this).attr("data-id");
            var deleteWsLabel = $.post("{{ url('workstations/payload') }}", payLoad, "JSONP");
        	deleteWsLabel.done(function(data) {
            	$(".ws-labels").html("");
            	for(var i=0; i < data.labels.length; i++) {
                	$(".ws-labels").append("<span class='badge badge-secondary mr-1'><a href='javascript:' class='text-light delete-label' data-id='" + data.labels[i].id + "'><i class='fa fa-times'></i></a>" + data.labels[i].name + "</span>");
                }
            });
        });
    
    	$(document).on("click", "#archive", function() {
        	var button = $(this);
        	button.hide();
        	$("#waiting").show();
        	var payLoad = {};
            payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            payLoad['wsid'] = {{ $workstation->id }};
            payLoad['action'] = 'archiveWorkstation';
            var archiveWorkstation = $.post("{{ url('workstations/payload') }}", payLoad, "JSONP");
        	archiveWorkstation.done(function(data) {
            	if(data == "OK") {
                	window.location.replace("{{ url('documents') }}");
                } else {
            		$("#waiting").hide();
        	    	button.show();
                }
            });
        });
                           
	});                                
    </script>
	<style type="text/css">
    
    	#controlPanel .modal-dialog {
        	height: 80%;
    	}
    
    	#controlPanel .modal-dialog .modal-content {
        	height: auto;
        	min-height:100%;
    	}
    
    	#eventsPanel .modal-dialog, #operationsPanel .modal-dialog {
        	height: 90%;
    	}
    
    	#eventsPanel .modal-content, #operationsPanel .modal-content {
        	height: 100%;
    	}
    
    	#eventsPanel .modal-body, #operationsPanel .modal-body {
        	height: 100%;
        	overflow-y: scroll;
    	}
    
    	.newEvent {
  			animation: highlight 2000ms ease-out;
		}	
		
    	@keyframes highlight {
  			0% {
    			background-color: #ffc107;
  				}
  			100 {
    			background-color: white;
  				}
		}
    	
    	.blink{
			animation: blink 3s infinite;
	 	}
	 
	 	@keyframes  blink{
			0%{opacity: 1;}
			50%{opacity: 0;}
			100%{ opacity: 1;}
	 	}

    </style>
    @endsection