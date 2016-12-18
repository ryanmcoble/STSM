<!DOCTYPE html>
<html>
    <head>
        <title>Shopify Theme Settings Builder</title>

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <link rel="stylesheet" href="{{ asset('assets/css/normalize.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
        
        <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}">

        <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    </head>
    <body class="{{ $body_class }}">

    	@include('header')

        <div class="container">
            @yield('content')
        </div>

        @include('scripts')
    </body>
</html>