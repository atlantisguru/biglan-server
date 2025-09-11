@extends ('layout')
	@section('title')
		{{ __('all.users.users') }} | BigLan
	@endsection
	@section('content')
		<div class="row mt-2">
			<div class="col-12">
			<div class="table-responsive table-hover table-borderless">
			<table class="table">
				<tr>
					<th class="text-center">{{ __('all.users.status') }}</th>
					<th>{{ __('all.users.username') }}</th>
					<th>{{ __('all.users.email') }}</th>
					<th>{{ __('all.users.last_login') }}</th>
					<th></th>
				</tr>
			@foreach($users as $user)
				<tr>
					<td class="text-center">
						@if($user->confirmed)
							<i class="fas fa-check text-success"></i>
						@else
							<i class="fas fa-times text-muted"></i>
						@endif
					</td>
					<td>{{ $user->username }}</td>
					<td>{{ $user->email }}</td>
					<td>{{ $user->last_login }}</td>
					<td>
							@if(auth()->user()->hasPermission('read-user-permissions'))
								<a class="btn btn-sm btn-primary mr-1" href="{{ url('users/permissions/'.$user->token) }}">{{ __('all.users.permissions') }}</a>
							@endif
							@if(auth()->user()->hasPermission('read-user-activities'))
								<a class="btn btn-sm btn-primary mr-1" href="{{ url('users/activities/'.$user->token) }}">{{ __('all.users.activities') }}</a>
							@endif
							@if($user->id != auth()->user()->id && auth()->user()->hasPermission('write-user-status'))
								@if($user->confirmed)
                        			<a href="{{ url('users/status/' .$user->token) }}" class="btn btn-sm btn-light mr-1" href="#">{{ __('all.users.disable') }}</a>
                        		@else
                        			<a href="{{ url('users/status/' .$user->token) }}" class="btn btn-sm btn-light mr-1" href="#">{{ __('all.users.enable') }}</a>
                        		@endif
							@endif
                        
					</td>
				</tr>
			@endforeach
			</table>
			</div>
			</div>
        </div>
         <div class="row mt-2">
		<div class="col-12">
        	{{ $users->appends(Request::query())->links() }}
    	</div>
    </div>    

				
	@endsection
	@section('inject-footer')
	<script type="text/javascript">
    	$(function() {
    		
        	
        });
    </script>
	@endsection