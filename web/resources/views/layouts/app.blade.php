<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Qualtrics LTI Tool</title>

    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
    <link rel="stylesheet" href="/custom/custom.css">

    <script src="{{ asset('/js/manifest.js') }}"></script>
    <script src="{{ asset('/js/vendor.js') }}"></script>
    <script src="{{ asset('/js/app.js') }}"></script>
</head>
<body>

<div class="container">
    @yield('content')
</div>

</body>
</html>
