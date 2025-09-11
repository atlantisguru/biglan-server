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
        <title>{{ __('all.about.about') }} | BigLan</title>
    </head>
	<body class="text-center">	
     	<div class="row text-center">
     		<div class="col-12">
    			<h1 class="mb-5 mt-5">BigLan Network Monitoring System</h1>
        		<h3 class="mb-3 font-weight-normal">{{ __("all.about.about") }}</h3>
     		</div>
     	</div>
     	<div class="row text-center">
     		<div class="col-12">
    			<h5>{{ __('all.about.version') }}</h5>
        		<p>V2</p>
     		</div>
     	</div>
     	<div class="row text-center">
     		<div class="col-12">
    			<h5>{{ __('all.about.creator') }}</h5>
        		<p>Bubori Attila</p>
     		</div>
     	</div>
     	<div class="row text-center">
     		<div class="col-12">
    			<h5>{{ __('all.about.contributors') }}</h5>
        		<p class="mb-0">Rédei István</p>
        		<p class="mb-0">Magyar Zoltán</p>
        		<p class="mb-0">Perjési Gergő</p>
     			<p>Molnár Márk</p>
       		</div>
     	</div>
     	<div class="row text-center">
     		<div class="col-12">
    			<h5>{{ __('all.about.created') }}</h5>
        		<p>2018-2025</p>
     		</div>
     	</div>
        <div class="row text-center">
     		<div class="col-12">
    			<h5>{{ __('all.about.thanks') }}</h5>
        		<p><a href="https://laravel.com/" target="_blank"><img class="logo" src="{{ asset('/images/laravel.png') }}"></a></p>
     			<p><a href="https://getbootstrap.com/" target="_blank"><img class="logo" src="{{ asset('/images/bootstrap.png') }}"></a></p>
     			<p><a href="https://jquery.com/" target="_blank"><img src="{{ asset('/images/jquery.png') }}"></a></p>
     			<p><a href="https://fontawesome.com/" target="_blank"><img class="logo" src="{{ asset('/images/fontawesome.png') }}"></a></p>
     			<p><a href="https://datatables.net/" target="_blank"><img class="logo" src="{{ asset('/images/datatables.png') }}"></a></p>
     			<p><a href="https://sigmajs.org/" target="_blank"><img class="logo" src="{{ asset('/images/sigmajs.png') }}"></a></p>
     			<p><a href="https://ckeditor.com/" target="_blank"><img class="logo" src="{{ asset('/images/ckeditor.png') }}"></a></p>
     		</div>
     	</div>
        <div class="row">
        	<div class="col-lg-4"></div>
     		<div class="col-lg-4">
    			<h5>{{ __('all.about.license') }}</h5>
        		<pre>BigLan Network Monitoring System
Copyright (C) 2018-2025  Bubori Attila
https://biglan.net

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see https://www.gnu.org/licenses/.
    
## Third-party Technologies
This software uses third-party libraries and frameworks, including:
- Laravel (MIT License) https://laravel.com/
- Bootstrap (MIT License) https://getbootstrap.com/
- jQuery (MIT License) https://jquery.com/
- FontAwesome (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) https://fontawesome.com/
- DataTables (MIT License) https://datatables.net/
- Sigma.js (MIT License) https://sigmajs.org/
- CKEditor (MIT License) https://ckeditor.com/
These components are not governed by the AGPL v3 license but retain their
original license terms provided by their respective owners.</pre>
        	</div>
     	</div>
        <script src="{{ url('js/bootstrap.4.1.3.min.js') }}"></script>
    </body>
</html>