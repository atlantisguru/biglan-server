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
        <title>{{ __('all.lost_password.lost_password') }} | BigLan</title>
    </head>
		<body class="text-center">
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
                	<h1 class="h3 mb-3 font-weight-normal">{{ __('all.lost_password.lost_password') }}</h1>
        	<div class="row">
                <div class="col-12">
                        <label class="sr-only" for="email">{{ __('all.lost_password.email') }}</label>
                        <input type="text" name="email" class="form-control" id="email" value="{{ old('email') }}" placeholder="{{ __('all.lost_password.email') }}" required autofocus>
                        @if($errors->has('email'))
        						<div class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('email') }}</div>
    					@endif
                </div>
                
            </div>
            <div class="row" style="padding-top: 1rem">
                <div class="col-12 pb-2">
                    <button type="submit" class="btn btn-lg btn-primary btn-block">{{ __('all.lost_password.send_email') }}</button>
                	<p class="mt-2"><a href="{{ url('/') }}">{{ __('all.lost_password.back_to_login') }}</a></p>
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