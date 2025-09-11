@extends ('layout')
	@section('title')
		{{ __('all.users.activities') }} | BigLan
	@endsection
	@section('content')
		<div class="row mt-2">
			<div class="col-12">
				<a href="{{ url('users') }}" class="btn btn-light btn-sm"><i class="fas fa-arrow-circle-up"></i> {{ __('all.button.back') }}</a>
			</div>
		</div>
		<div class="row mt-2">
			<div class="col-12">
				<h5>{{ __('all.users.user_activities', [ 'username' => $user->username]) }}</h5>	
			</div>
		</div>
		<div class="row mt-2">
			<div class="col-12">
			<div class="table-responsive table-hover table-bordered table-striped table-sm">
			<table class="table">
				<tr>
					<th>{{ __('all.users.user_activities_datetime') }}</th>
					<th>{{ __('all.users.user_activities_event') }}</th>
					<th>{{ __('all.users.user_activities_description') }}</th>
					<th>{{ __('all.users.user_activities_ip') }}</th>
					<th>{{ __('all.users.user_activities_info') }}</th>
				</tr>
			@foreach($userActivities as $activity)
				<tr>
					<td>{{ $activity->created_at }}</td>
					<td>{{ $activity->activity }}</td>
					<td>{{ $activity->description }}</td>
					<td>{{ $activity->ip }}</td>
					<td>{{ $activity->browser }}</td>
				</tr>
			@endforeach
			</table>
			</div>
			</div>
        </div>
         <div class="row mt-2">
		<div class="col-12">
        	{{ $userActivities->appends(Request::query())->links() }}
    	</div>
    </div>    

				
	@endsection
	@section('inject-footer')
	<script type="text/javascript">
    	$(function() {
    		
        	
        });
    </script>
	@endsection