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

        <div class="row mt-2" id="change-password">
            <div class="col-12">
                <h4>{{ __('all.user_settings.change_password') }}</h4>
            </div>
            <div class="col-12 col-md-6">
                <form id="changePasswordForm">
                    <div class="form-group">
                        <label>{{ __('all.user_settings.current_password') }}</label>
                        <input type="password" name="current_password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>{{ __('all.user_settings.new_password') }}</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>{{ __('all.user_settings.new_password_confirm') }}</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                    <div id="changePasswordMessage"></div>
                    <button type="submit" class="btn btn-primary btn-sm">{{ __('all.user_settings.change_password') }}</button>
                </form>
            </div>
        </div>

        <div class="row mt-2" id="change-email">
            <div class="col-12">
                <h4>{{ __('all.user_settings.change_email') }}</h4>
            </div>
            <div class="col-12 col-md-6">
                <form id="changeEmailForm">
                    <div class="form-group">
                        <label>{{ __('all.user_settings.current_password') }}</label>
                        <input type="password" name="current_password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>{{ __('all.user_settings.new_email') }}</label>
                        <input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}">
                    </div>
                    <div id="changeEmailMessage"></div>
                    <button type="submit" class="btn btn-primary btn-sm">{{ __('all.user_settings.change_email') }}</button>
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

            $("#changePasswordForm").on("submit", function(e) {
                e.preventDefault();
                var form = $(this);
                var posting = $.post("{{ url('settings') }}", {
                    '_token': $('meta[name=csrf-token]').attr('content'),
                    'settings': 'changePassword',
                    'current_password': form.find('[name=current_password]').val(),
                    'password': form.find('[name=password]').val(),
                    'password_confirmation': form.find('[name=password_confirmation]').val()
                });
                posting.done(function(data) {
                    var msg = $("#changePasswordMessage");
                    if (data.success) {
                        msg.html('<div class="alert alert-success mt-2">' + data.message + '</div>');
                        form[0].reset();
                    } else {
                        msg.html('<div class="alert alert-danger mt-2">' + data.message + '</div>');
                    }
                });
            });

            $("#changeEmailForm").on("submit", function(e) {
                e.preventDefault();
                var form = $(this);
                var posting = $.post("{{ url('settings') }}", {
                    '_token': $('meta[name=csrf-token]').attr('content'),
                    'settings': 'changeEmail',
                    'current_password': form.find('[name=current_password]').val(),
                    'email': form.find('[name=email]').val()
                });
                posting.done(function(data) {
                    var msg = $("#changeEmailMessage");
                    if (data.success) {
                        msg.html('<div class="alert alert-success mt-2">' + data.message + '</div>');
                        form.find('[name=current_password]').val('');
                    } else {
                        msg.html('<div class="alert alert-danger mt-2">' + data.message + '</div>');
                    }
                });
            });

        });
    </script>
	@endsection
