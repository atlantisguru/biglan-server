@extends ('layout')
	@section('title')
		{{ __('all.api_tokens.new_api_token') }} | BigLan
	@endsection
	@section('content')
	<div class="row mt-2">
	<div class="col-12">
		<h4>{{ __('all.api_tokens.new_api_token') }}</h4>
		<form method="POST" class="form-horizontal" action="{{ url('apitokens/save') }}">
			<div class="row">
				<div class="col-12 mb-2 mt-2">
        			<button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> {{ __('all.button.save') }}</button>&nbsp;&nbsp;<a href="{{ url('/apitokens') }}" class="btn btn-light btn-sm"><i class="fas fa-arrow-circle-up"></i> {{ __('all.button.back_without_save') }}</a>
            	</div>
			</div>
        	{{ csrf_field() }}
			<div class="form-group row">
				<div class="col-2">
					<span class="control-label">{{ __('all.api_tokens.name') }}</span>
				</div>
				<div class="col-3">
					<input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            		@if($errors->has('name'))
        				<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('name') }}</div>
    				@endif
				</div>
			</div>
        	<div class="form-group row">
				<div class="col-2">
					<span class="control-label">{{ __('all.api_tokens.token') }}</span>
				</div>
				<div class="col-3">
					<input type="text" name="token" class="form-control" value="{{ old('token', $token) }}" required>
            		@if($errors->has('token'))
        				<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('token') }}</div>
    				@endif
				</div>
			</div>
        	<div class="form-group row">
				<div class="col-2">
					<span class="control-label">{{ __('all.api_tokens.max_uses') }}</span>
				</div>
				<div class="col-3">
					<input type="number" name="max_uses" class="form-control" value="{{ old('max_uses') }}">
					<small>({{ __('all.api_tokens.max_uses_helper') }})</small>
					@if($errors->has('max_uses'))
        				<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('max_uses') }}</div>
    				@endif
				</div>
			</div>
        	<div class="form-group row">
				<div class="col-2">
					<span class="control-label">{{ __('all.api_tokens.expires') }}</span>
				</div>
				<div class="col-3">
					<input type="datetime-local" name="expires" class="form-control" value="{{ old('expires') }}">
            		<small>({{ __('all.api_tokens.expires_helper') }})</small>
					@if($errors->has('expires'))
        				<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('expires') }}</div>
    				@endif
				</div>
			</div>
        </form>
	</div>
	</div>
	@endsection