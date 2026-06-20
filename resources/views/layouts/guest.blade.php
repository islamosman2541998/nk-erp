@php
    $settings = app(\App\Services\SettingService::class);

    $systemName = $settings->get('system_name', 'NK ERP');
    $companyName = $settings->get('company_name', 'نابت وخليفة');
    $companyLogo = $settings->get('company_logo');

    $primaryColor = $settings->get('primary_color', '#073f22');
    $sidebarColor = $settings->get('sidebar_color', '#052f19');
    $accentColor = $settings->get('accent_color', '#c89b3c');
    $backgroundColor = $settings->get('background_color', '#f5f7f3');
@endphp

<!DOCTYPE html>
<html lang="ar"
      dir="rtl"
      style="
        --nk-green: {{ $primaryColor }};
        --nk-green-dark: {{ $sidebarColor }};
        --nk-green-light: {{ $primaryColor }};
        --nk-gold: {{ $accentColor }};
        --nk-bg: {{ $backgroundColor }};
      ">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $systemName }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="nk-auth-body">
    <main class="nk-auth-page">
        <div class="nk-auth-card">
            <div class="nk-auth-brand">
                @if($companyLogo)
                    <img src="{{ asset('storage/' . $companyLogo) }}"
                         alt="{{ $companyName }}"
                         class="nk-auth-logo">
                @else
                    <div class="nk-auth-logo-placeholder">
                        NK
                    </div>
                @endif

                <h1>{{ $companyName }}</h1>
                <p>{{ $systemName }}</p>
            </div>

            {{ $slot }}
        </div>
    </main>
</body>
</html>