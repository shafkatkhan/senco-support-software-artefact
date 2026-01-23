<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Senco Support Software</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/fontawesome.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" type="text/css" />
</head>
<body>

@include('components.nav')
@include('components.top_nav')

@yield('content')

<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/script.js') }}"></script>
</body>
</html>