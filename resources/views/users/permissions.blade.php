@extends ('layout')
	@section('title')
		{{ __('all.users.permissions') }} | BigLan
	@endsection
	@section('content')
		 
        	<form class="form-horizontal row" method="POST" action="{{ url('users/savePermissions') }}">
        		
					{{ csrf_field() }}
					<div class="col-12 mt-2 mb-2">
                    		@if(auth()->user()->hasPermission('write-user-permissions'))
                    			<button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> {{ __('all.button.save') }}</button>
                    		@endif
                    		<a href="{{ url('users') }}" class="btn btn-light btn-sm"><i class="fas fa-arrow-circle-up"></i> {{ __('all.button.back') }}</a>
                    </div>
                    @if(auth()->user()->hasPermission('write-user-permissions'))
                    	<input type="hidden" name="token" value="{{ $user->token }}">
                    @endif
		
        	<div class="col-12">
		
				<h5>{{ __('all.users.user_permissions', ['username' => $user->username]) }}</h5>	
				@if(Session::has('success'))
    				<div class="alert alert-success alert-dismissible fade show" role="alert">
       					{!! Session::get('success') !!}
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    						<span aria-hidden="true">&times;</span>
  						</button>
        			</div>
				@endif
			</div>
        
                    
                    @foreach($permissions as $permission)
			
			<div class="col-lg-3 col-12 mb-3">
				<div class="card shadow-sm">
					<div class="card-header">
						{{ $permission["group-name"] }}
					</div>
					<div class="card-body">
                    	@foreach($permission["rights"] as $right)
                    	<div class="form-check">
  							<input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $right['name'] }}" id="{{ $right['name'] }}" @if(in_array($right['name'], $userPermissions)) CHECKED @endif @if(!auth()->user()->hasPermission('write-user-permissions')) DISABLED @endif>
  							<label class="form-check-label" for="{{ $right['name'] }}">
    							{{ $right['alias'] }}
  							</label>
						</div>
                        @endforeach
					</div>
				</div>
			</div>
			
            @endforeach
        </form>
                
            
        		
	@endsection
	@section('inject-footer')
	<script type="text/javascript">
    	$(function() {
    		
        	
        });
    </script>
	@endsection