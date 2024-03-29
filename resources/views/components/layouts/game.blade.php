<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ app('site')->getTitle() }}</title>
        <link rel="icon" href="{{ asset('images/icon.jpg') }}" type="image/x-icon" />
        @vite(['resources/css/app.scss'])
    </head>

    <body class="bg-background text-text">
        <x-toasts />
        <x-tooltipper />

        {{ $slot }}

        @vite(['resources/js/app.js'])
    </body>
</html>
