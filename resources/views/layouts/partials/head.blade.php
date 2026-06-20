<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ app(\App\Services\SettingService::class)->get('system_name', 'NK ERP') }}</title>

    <style>
        html {
            background: var(--nk-bg);
        }

        body {
            margin: 0;
            background: var(--nk-bg);
            font-family: 'IBM Plex Sans Arabic', Arial, sans-serif;
        }

        .nk-layout {
            min-height: 100vh;
            display: flex;
            background: var(--nk-bg);
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>