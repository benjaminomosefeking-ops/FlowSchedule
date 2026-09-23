<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'FlowSchedule' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@500;600;700&family=IBM+Plex+Mono:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    @stack('styles')
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    <style>
        .flow-page-shell {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .flow-page-shell > footer {
            margin-top: auto;
        }
    </style>
</head>
<body>
    <div class="flow-page-shell">
        {{ $slot }}
    </div>
    @stack('scripts')
</body>
</html>