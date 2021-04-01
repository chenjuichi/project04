<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Page is not found</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css')}}">

    <!-- Javascripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script>
        (function() {
            window.Laravel = {
                csrfToken: '{{ csrf_token() }}'
            };
        })();
    </script>
</head>

<body>
    <div class="container">
        <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20 ">
            <h1 class="_text_center">You don't have enough permission to access this page.</h1>
        </div>
    </div>
</body>

</html>