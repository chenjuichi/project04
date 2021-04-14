<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @include('front.header')    <!-- 2021-04-06 add, -->
</head>
<body>
    <div id="app">
        @include('front.nav')    <!-- 2021-04-06 add, -->

        @yield('content')
    </div>

    @include('front.footer')    <!-- 2021-04-06 add, -->
</body>
</html>
