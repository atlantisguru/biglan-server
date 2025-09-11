<!doctype html>
<html lang="hu">
    <head>
    	<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script defer src={{ url("js/fontawesome.5.0.6.min.js") }}></script>
        <link rel="stylesheet" type="text/css" href={{ url("css/bootstrap.4.1.3.min.css") }}>
        <style type="text/css">
        .form-signin {
        	max-width: 330px;
        	margin: 0 auto;
        	padding: 15px;
        	width:100%;
        }

        </style>
    <link rel="stylesheet" type="text/css" href="/css/dark-mode.css">
        <title>{{ __('all.register.registration') }} | BigLan</title>
    </head>
	<body class="text-center">
  @if(Session::has('failed'))
    <div class="pt-4 row justify-content-center">
        <div class="col-xl-4 col-lg-6 col-md-12  alert alert-danger">
       		{!! Session::get('failed') !!}
        </div>
	</div>
    @endif
    @if(Session::has('success'))
    <div class="pt-4 row justify-content-center">
        <div class="col-xl-4 col-lg-6 col-md-12 alert alert-success">
       		{!! Session::get('success') !!}
        </div>
	</div>    
    @endif
       
	
		
				
				<form method="POST" class="form-signin">
        {{ csrf_field() }}
                	<h1 class="mb-5">BigLan</h1>
                	<h1 class="h3 mb-3 font-weight-normal">{{ __('all.register.registration') }}</h1>
        			<div class="row">
                <div class="col-12">
                        <label class="sr-only" for="email">{{ __('all.register.email') }}</label>
                        <input type="text" name="email" class="form-control" id="email" value="{{ old('email') }}" placeholder="{{ __('all.register.email') }}" value="{{ old('email', '') }}" required autofocus>
                        @if($errors->has('email'))
        						<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('email') }}</div>
    					@endif
                </div>
                
            </div>
            <div class="row mt-2">
                <div class="col-12">
                        <label class="sr-only" for="password">{{ __('all.register.password') }}</label>
                        <input type="password" name="password" class="form-control" id="password" placeholder="{{ __('all.register.password') }}" required>
                        @if($errors->has('password'))
        						<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('password') }}</div>
    					@endif
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                        <label class="sr-only" for="password2">{{ __('all.register.password_again') }}</label>
                            <input type="password" name="password_confirmation" class="form-control" id="password2" placeholder="{{ __('all.register.password_again') }}" required>
                        
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                        <label class="sr-only" for="username">{{ __('all.register.fullname') }}</label>
                       	<input type="text" name="username" class="form-control" id="username"  value="{{ old('username') }}" placeholder="{{ __('all.register.fullname') }}" value="{{ old('username', '') }}" required>
                	@if($errors->has('username'))
        				<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('username') }}</div>
    				@endif
             	</div>
            </div>
            <div class="row" style="padding-top: 1rem">
                <div class="col-12 pb-2">
                    <button type="submit" class="btn btn-lg btn-primary btn-block">{{ __('all.register.registration') }}</button>
                	<p class="mt-2"><a href="{{ url('/') }}">{{ __('all.register.back_to_login') }}</a></p>
                	<p class="mt-4 text-muted">BigLan Network Monitoring System<br>
                        <a href="{{ url('about-public') }}">Copyright</a> &copy; 2018-@php echo date("Y"); @endphp
                    </p>
                </div>
            </div>
        		</form>
		<script src={{ url("js/jquery.3.3.1.min.js") }}></script>
		<script src={{ url("js/bootstrap.4.1.3.min.js") }}></script>               
    </body>
</html>