<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <x-favicon></x-favicon>
    @hasSection('head_content')
            @yield('head_content')
    @endif
</head>
<body>
    @yield('content')
</body>
</html>