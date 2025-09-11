<!doctype html>
<html lang="hu">
    <head>
    	<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="{{ url('css/fontawesome.5.6.3.css') }}">
        <link rel="stylesheet" href="{{ url('css/bootstrap.4.1.3.min.css') }}">
		<link rel="stylesheet" type="text/css" href="css/dark-mode.css">
        <title>{{ __('all.downloads.downloads') }} | BigLan</title>
    </head>
		<body class="text-center">
    <div class="row  justify-content-center">
	<div class="col-lg-6 col-md-6 col-sm-12">
		{{ csrf_field() }}
    				<h1 class="mb-5">BigLan</h1>
                	<h1 class="h3 mb-3 font-weight-normal">{{ __('all.downloads.downloads') }}</h1>
                	<p class="mb-1" id="os"></p>
                	
                    <table class="table table-striped table-hover"  id="downloads">
                    	<thead class="thead-dark">
                    		<tr>
                    			<th>{{ __('all.downloads.alias') }}</th>
                    			<th>{{ __('all.downloads.size') }}</th>
                    			<th>{{ __('all.downloads.created') }}</th>
                    			<th>{{ __('all.downloads.actions') }}</th>
                    		</tr>
                    	</thead>
                    	<tbody>
                    	@foreach($downloads as $download)
                    		<tr>
                    			<td>{{ $download->alias }}</td>
                    			<td>
                    				@if(isset($download->size))
                                       	@if($download->size < 1024)
                                        	{{ round($download->size, 2) }}KB
                                        @endif
                                        @if($download->size > 1024 && $download->size/1024 < 1024)
                                        	{{ round($download->size/1024, 2) }}MB
                                        @endif
                                        @if($download->size/1024 > 1024 && $download->size/1024/1024 < 1024)
                                        	{{ round($download->size/1024/1024, 2) }}GB
                                        @endif
                                    @else
                                        "N/A"
                                    @endif
                    			</td>
                    			<td>
                    				{{ $download->created_at }}
                    			</td>
                    			<td><a href={{ url('/downloads/' . $download->filename ) }}><i class="fas fa-download"></i></a></td>
                    		</tr>
                    	@endforeach
                    	</tbody>
                    </table>
                    
                    <p class="mb-1"></p>
    				<a href="{{ url('/') }}" class="btn btn-lg btn-primary mt-3">{{ __('all.downloads.back_to_login') }}</a>
                	<p class="mt-4 text-muted">BigLan Network Monitoring System<br>
                        <a href="{{ url('about-public') }}">Copyright</a> &copy; 2018-@php echo date("Y"); @endphp
                    </p>
         </div>           
   </div>
			
		
		<script type="text/javascript">
		if (navigator.userAgent.indexOf("WOW64") != -1 || navigator.userAgent.indexOf("Win64") != -1 ){
   			document.getElementById("os").innerHTML = "{{ __('all.downloads.64bit_system') }}";
		} else {
   			document.getElementById("os").innerHTML = "{{ __('all.downloads.32bit_system') }}";
		}
	</script>
    	<script src="{{ url('js/jquery.3.3.1.min.js') }}"></script>
        <script src="{{ url('js/bootstrap.4.1.3.min.js') }}"></script>
    	<link rel="stylesheet" type="text/css" href={{ url("css/jquery.dataTables.min.css") }}>
        <script type="text/javascript" src={{ url("js/jquery.dataTables.min.js") }}></script>
        <script type="text/javascript">
        	$(function() {
    				$('#downloads').DataTable({
                    	"pageLength": 25
                    });
			});
        </script>
    	              
    </body>
</html>