<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
	
    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body>
	<div class="container">
		<ul class="nav justify-content-end">
			<li class="nav-item">
				<a class="nav-link" href="/">Home</a>
			</li>
		</ul>
		<div id="app">
			@yield('content')
		</div>

		<!-- Scripts -->
		<script src="{{ asset('js/app.js') }}"></script>
	</div>
</body>
</html>