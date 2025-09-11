<!doctype html>
<html lang="hu">
    <head>
    	<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="{{ url('css/fontawesome.5.6.3.css') }}">
        <link rel="stylesheet" href="{{ url('css/bootstrap.4.1.3.min.css') }}">
    	<link rel="stylesheet" type="text/css" href="{{ url("/css/dark-mode.css") }}">
    	<style type="text/css">
        .form-signin {
        	max-width: 330px;
        	margin: 0 auto;
        	padding: 15px;
        	width:100%;
        }

        </style>
        <title>{{ __("all.login.login") }} | BigLan</title>
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
                	<h3 class="h3 mb-3 font-weight-normal">{{ __("all.login.login") }}</h3>
   
    
                    <div class="row">
                <div class="col-12">
                        <label class="sr-only" for="email">{{ __("all.login.email") }}</label>
                        <input type="text" name="email" class="form-control" id="email" value="{{ old('email') }}" placeholder='{{ __("all.login.email") }}' required autofocus>
                        
                </div>
                
            </div>
            <div class="row mt-2">
                <div class="col-12">
                        <label class="sr-only" for="password">{{ __("all.login.password") }}</label>
                            <input type="password" name="password" class="form-control" id="password" placeholder='{{ __("all.login.password") }}' required>
                        
                </div>
                
            </div>
            <div class="row">
                <div class="col-12" style="padding-top: .35rem">
                    <div class="form-check mb-2 mr-sm-2 mb-sm-0">
                        <label class="form-check-label">
                            <input class="form-check-input" name="remember" type="checkbox" >
                            <span style="padding-bottom: .15rem"> {{ __("all.login.remember_me") }}</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="row" style="padding-top: 1rem">
                <div class="col-12 pb-2">
                    <button type="submit" class="btn btn-lg btn-primary btn-block">{{ __("all.button.login") }}</button>
                    <p class="mt-2"><a href="/lostpassword">{{ __("all.login.forgot_password") }}</a>@if($enableRegistration == 1) &nbsp;|&nbsp;<a href="/register">{{ __("all.login.registration") }}</a></p> @endif
                    <p class="mt-2"><a href="/downloads">{{ __("all.login.downloads") }}</a></p>
                	<p class="mt-4 text-muted">BigLan Network Monitoring System<br>
                        <a href="{{ url('about-public') }}">Copyright</a> &copy; 2018-@php echo date("Y"); @endphp
                    </p>
                </div>
            </div>
        		</form>
			
		
		<script src="{{ url('js/jquery.3.3.1.min.js') }}"></script>
        <script src="{{ url('js/bootstrap.4.1.3.min.js') }}"></script>
    </body>
</html>