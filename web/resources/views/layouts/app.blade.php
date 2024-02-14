<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Qualtrics LTI Tool</title>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="/custom/custom.css">

    @yield('head_extras')
</head>
<body>

<div class="container">
    @yield('content')
</div>

</body>
</html>
