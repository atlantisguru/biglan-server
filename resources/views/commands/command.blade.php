@extends('layout')
@section('title')
{{ __('all.command_center.command_details') }} | BigLan
@endsection
@section('content')
	<div class="row mt-2">
		<div class="col-12">
        	@include('commands.header')
        </div>
    </div>
	<div class="row mt-2">
		<div class="col-12">
        	<div class="card mb-2">
            <div class="card-body">
        		<div>
        			<div class="row mt-1 mb-1">
                    	<div class="col-12">
                    		<h2 class="h5"><span class="badge badge-info">{{ floor(($command->done()/($command->done()+$command->waiting()))*100) }}%</span> @if($command->blocked == 1) <span class="badge badge-danger">{{ __('all.command_center.interrupted') }}</span>  @endif <a href="{{ url('/commands/command/'.$command->id) }}" class="text-dark">{{ $command->alias }}</a> </h2>
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
                			@if($command->description != null)
                				<p><strong>{{ __('all.command_center.notes') }}:</strong><br />{{ $command->description }}</p>
                			@endif
                	<p><strong>{{ __('all.command_center.progress') }}:</strong> {{  $command->done() }} / {{ $command->done()+$command->waiting() }} ({{ floor(($command->done()/($command->done()+$command->waiting()))*100) }}%)</p>
                		</div>
                    </div>
                	<div class="row mt-1 mb-4">
                    	<div class="col-12">
                        	<p><strong>{{ __('all.command_center.results') }}:</strong></p>
                        	<div class="table-responsive">
                            	<table class="table-striped table table-hover" id="results">
                                	<thead class="thead-dark">
                                	<tr>
                                    	<th>{{ __('all.command_center.workstation') }}</th>
                                    	<th>{{ __('all.command_center.result') }}</th>
                                    	<th>{{ __('all.command_center.date_and_time') }}</th>
                                    </tr>
                                    </thead>
                                	<tbody>
                                	@foreach($results as $result)
                                		<tr>
                                    		<td>
											@if(auth()->user()->hasPermission('read-workstation'))
												<a href="{{ url('/workstations/'.$result->wsid) }}" target="_blank"> {{ $result->workstation->alias }} </a>
											@else
												{{ $result->workstation->alias }}
											@endif	
											</td>
                                    		<td>@if($result->result == null)   @else {!! nl2br($result->result) !!}  @endif</td>
                                    		<td>@if($result->result == null)   @else {{ $result->updated_at }}  @endif</td>
                                		</tr>
                                	@endforeach
                                    </tbody>
                                </table>
                            </div>
                    	</div>
                    </div>
                
                	<div class="row mb-1">
                    	<div class="col-12">
                    		@if(auth()->user()->hasPermission('write-batch-command'))	
                                @if($command->blocked == 0 && $command->waiting() > 0)
                            		<a href="javascript:" class="btn btn-danger emergency-stop-btn" data-id="{{ $command->id }}">{{ __('all.command_center.emergency_stop') }}</a>
                                @endif
                        	@endif
                		</div>
                    </div>
                </div>
            </div></div>
        </div>
    </div>
	
@endsection
@section('inject-footer')
	<style type="text/css">
    	
   	</style>
	<link rel="stylesheet" type="text/css" href={{ url("css/jquery.dataTables.min.css") }}>
    <script type="text/javascript" src={{ url("/js/jquery.dataTables.min.js") }}></script>
	<script type="text/javascript">
    $(function() {
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
   	 	
    	$('#results').DataTable();
    });
     </script>                              
@endsection