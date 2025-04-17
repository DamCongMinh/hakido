<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Guest Layout</title>
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    </head>
    <body class="bg-gray-100">
        <div class="min-h-screen flex items-center justify-center">
            {{ $slot }}
        </div>
    </body>
</html>