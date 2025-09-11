@extends ('layout')
	@section('title')
		{{ __('all.help.help') }} | BigLan
	@endsection
	@section('content')
		{{ csrf_field()}}
     	<div class="row text-center">
        	<div class="col-12">
     			<h4 class="mt-2 mb-2">{{ __('all.help.help') }}</h1>
        	</div>
     	</div>
        <div class="row text-center">
        	<div class="col-12 d-flex justify-content-center align-items-center">
    			{{ __('all.help.helper_text') }}
        	</div>
     	</div>
    @endsection
                
    @section('inject-footer')
     <script type="text/javascript">
			

			$(function() {});
	</script>
    @endsection