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
	<style>
	.data-table tbody tr td:nth-child(2),
	.result-table tbody tr td:nth-child(3),
	.detail-table tbody tr td:nth-child(2),
	.detail-table tbody tr td:last-child,
	.panel-heading	{
		white-space: -moz-pre-wrap !important;  /* Mozilla, since 1999 */
		white-space: -webkit-pre-wrap;          /* Chrome & Safari */ 
		white-space: -pre-wrap;                 /* Opera 4-6 */
		white-space: -o-pre-wrap;               /* Opera 7 */
		white-space: pre-wrap;                  /* CSS3 */
		word-wrap: break-word;                  /* Internet Explorer 5.5+ */
		word-break: break-all;
		white-space: normal;
	}
	</style>
	
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