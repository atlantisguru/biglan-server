@extends ('layout')
	@section('title')
		{{ __('all.nav.my_settings') }} | BigLan
	@endsection
	@section('content')
		<div class="row mt-2">
			<div class="col-12">
            		<h2>{{ __('all.user_settings.my_settings') }}</h2>
            </div>
        </div>
        <div class="row mt-2">
			<div class="col-12">
            		<h4>{{ __('all.user_settings.theme') }}</h4>
            </div>
        	<div class="col-12">
            	<form id="switchTheme" class="col-12">
            		<div class="radio">
  						<label><input type="radio" name="theme" value="light" @if(Auth::user()->theme == null) checked @endif> {{ __('all.user_settings.light') }}</label>
					</div>
					<div class="radio">
  						<label><input type="radio" name="theme" value="dark" @if(Auth::user()->theme == "dark") checked @endif> {{ __('all.user_settings.dark') }}</label>
					</div>
		        </form>
        	</div>
        </div>
		<div class="row mt-2">
			<div class="col-12">
            		<h4>{{ __('all.user_settings.language') }}</h4>
            </div>
        	<div class="col-12">
            	<form id="switchLanguage" class="col-12">
					@foreach($languages as $lang)
            			<div class="radio">
  							<label><input type="radio" name="language" value="{{ $lang }}" @if(Auth::user()->language == $lang) checked @endif> {{ __('all.languages.'.$lang) }}</label>
						</div>
					@endforeach	
					<div class="radio">
  						<label><input type="radio" name="language" value="null" @if(Auth::user()->language == null) checked @endif> {{ __('all.user_settings.default') }}</label>
					</div>
				</form>
        	</div>
        </div>
            

				
	@endsection
	@section('inject-footer')
	<script type="text/javascript">
    	$(function() {
    		
        	$("#switchTheme input").on("change", function() {
        	    var theme = $("input[name=theme]:checked", "#switchTheme").val();
            	var posting = $.post("{{ url('settings') }}", { '_token': $('meta[name=csrf-token]').attr('content'), 'settings': 'switchTheme', 'theme': theme } , "JSONP");
        		posting.done(function(data) {
                	location.reload();
                });
        	});
        
        	$("#switchLanguage input").on("change", function() {
        	    var language = $("input[name=language]:checked", "#switchLanguage").val();
            	var posting = $.post("{{ url('settings') }}", { '_token': $('meta[name=csrf-token]').attr('content'), 'settings': 'switchLanguage', 'language': language } , "JSONP");
        		posting.done(function(data) {
                	location.reload();
                });
        	});
        	
        });
    </script>
	@endsection