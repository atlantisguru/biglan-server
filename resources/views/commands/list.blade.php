@extends('layout')
@section('title')
{{ __('all.command_center.command_center') }} | BigLan
@endsection
@section('content')

@php
	$userPermissions = auth()->user()->permissions();
@endphp

	<div class="row mt-2">
		<div class="col-12">
        	@include('commands.header')
        </div>
    </div>
	<div class="row mt-2">
		<div class="col-12">
        	@if($commands->count() == 0)
        		<div>{{ __('all.command_center.no_command_found') }}</div>
        	@endif
        	@foreach($commands as $command)
			@php
				$done = $command->commands()->whereNotNull("result")->count();
				$waiting = $command->commands()->whereNull("result")->count();
				$all = $done+$waiting;
			@endphp
			@if($all > 0)
        	<div class="card mb-2">
            <div class="card-body">
        		<div>
        			<div class="row mt-1 mb-1">
                    	<div class="col-12">
							<h2 class="h5"><span class="badge badge-info">{{ floor(($done/($done+$waiting))*100) }}%</span> @if($command->blocked == 1) <span class="badge badge-danger">{{ __('all.command_center.interrupted') }}</span>  @endif <a href="{{ url('/commands/command/'.$command->id) }}" class="text-dark">{{ $command->alias }}</a> </h2>
                		</div>
                    </div>
                	<div class="row">
                    	<div class="col-12">
                    		<small class="text-muted text-uppercase">{{ $command->user->username }} | {{ __('all.command_center.created') }}: {{ \Carbon\Carbon::parse($command->created_at)->format("Y.m.d.") }} </small>
                		</div>
                    </div>
                	<div class="row mt-1">
                    	<div class="col-12">
                        	<p><strong>{{ __('all.command_center.earliest_run_time') }}:</strong> {{ \Carbon\Carbon::parse($command->run_after_at)->format("Y.m.d. H:i") }}</p>
                        	<p><strong>{{ __('all.command_center.script') }}:</strong><br /><i>{!! $command->command !!}</i></p>
                			<p><strong>{{ __('all.command_center.progress') }}:</strong> {{  $done }} / {{ $done+$waiting }} ({{ floor(($done/($done+$waiting))*100) }}%)</p>
                		</div>
                    </div>
                	<div class="row mb-1">
                    	<div class="col-12">
                    		<a href="{{ url('/commands/command/'.$command->id) }}" class="btn btn-outline-secondary">{{ __('all.command_center.details') }}</a>
							@if(in_array('write-batch-command', $userPermissions))
								@if($command->blocked == 0 && $waiting > 0)
									<a href="javascript:" class="btn btn-danger emergency-stop-btn" data-id="{{ $command->id }}">{{ __('all.command_center.emergency_stop') }}</a>
								@endif	
							@endif 
                		</div>
                    </div>
                </div>
            </div></div>
            @endif
        	@endforeach
            
        </div>
    </div>
	<div class="row mt-4">
		<div class="col-12">
           {{ $commands->links() }}
		</div>
    </div>
@endsection
@section('inject-footer')
	<script type="text/javascript">
    $(function() {
    
    	@if(in_array('write-batch-command', $userPermissions))
    	$(".emergency-stop-btn").on("click", function() {
        	
        	var command_id = $(this).attr("data-id");
    		var payLoad = {};
            payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
    		payLoad['action'] = "emergencyStop";
    		payLoad['command_id'] = command_id;
            
        	var emergencyStop = $.post("{{ url('commands/payload') }}", payLoad, "JSONP");
        	
        	emergencyStop.done(function(data) {
            	location.reload();
            });
        
        });
    	@endif
    
    });
	
    </script>                              
@endsection