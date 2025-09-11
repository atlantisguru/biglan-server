@extends ('layout')
	@section('title')
		{{ __('all.button.new_subnet') }} | BigLan
	@endsection
	@section('content')
		<div class="row mt-2">
			<div class="col-12">
				<h4>{{ __('all.button.new_subnet') }}</h4>
				<form class="form-horizontal row" method="POST" action="{{ url('subnets/save') }}">
				
					{{ csrf_field() }}
					<div class="col-12 mt-2 mb-2">
                    		<button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> {{ __('all.button.save') }}</button>
                    		<a href="{{ URL::previous() }}" class="btn btn-light btn-sm"><i class="fas fa-arrow-circle-up"></i> {{ __('all.button.back_without_save') }}</a>
                    </div>
					<div class="col-6">
                    	@foreach($errors as $error)
                    		<li></li>
                    	@endforeach
                    	<h5>{{ __('all.subnet.parameters') }}</h5>
                    	<div class="form-group row">
                    		<label class="col-3">{{ __('all.subnet.alias') }}</label>
                    		<div class="col-9">
                    			<input type="text" name="alias" class="form-control" required>
                    		</div>
                    	</div>
                    	<div class="form-group row">
                    		<label class="col-3">{{ __('all.subnet.ip_mask') }}</label>
                    		<div class="col-9">
                    			<input type="text" name="identifier" placeholder="000.000.000.000/00" class="form-control" required>
                    		</div>
                    	</div>
                    	<div class="form-group row">
                    		<label class="col-3">{{ __('all.subnet.gateway') }}</label>
                    		<div class="col-9">
                    			<input type="text" name="gateway" placeholder="000.000.000.000" class="form-control" required>
                    		</div>
                    	</div>
                    	<div class="form-group row">
                    		<label class="col-3">{{ __('all.subnet.short_description') }}</label>
                    		<div class="col-9">
                    			<input type="text" name="description" class="form-control">
                    		</div>
                    	</div>
                    </div>
                </form>
      		</div>
		</div>
	@endsection
    @section('inject-footer')
    <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
                    <style>
  
  </style>
    <script type="text/javascript" src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
 	<script type="text/javascript">
    $(function() {
    	
    	        
	});                                
    </script>                               
    @endsection