@php
    $settings = app(\App\Services\SettingService::class);

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

@include('layouts.partials.head')

<body>
    <div class="nk-layout">
        @include('layouts.partials.sidebar')

        <div class="nk-main">
            @include('layouts.partials.navbar')

            <main class="nk-content">
             

                {{ $slot }}
            </main>

            @include('layouts.partials.footer')
        </div>
    </div>

    @include('layouts.partials.scripts')
@stack('scripts')
</body>


</html>