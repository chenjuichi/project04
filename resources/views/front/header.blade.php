<!-- HEADER -->
<!-- Fonts -->
<link rel="dns-prefetch" href="//fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

<!-- Styles -->
<link rel="stylesheet" href="{{ asset('css/all.css') }}">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

<!-- Javascripts -->
<script src="{{ asset('js/app.js') }}" defer></script>
<script>
    (function() {
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };
    })();
</script>

<script>
    (function() {
        window.Data = {
            link: "{{ $data ?? '' }}"

        };
    })();
</script>
<!-- HEADER -->