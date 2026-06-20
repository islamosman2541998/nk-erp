<aside class="nk-sidebar">
    <div class="nk-sidebar-brand">
        @php
            $settingService = app(\App\Services\SettingService::class);
            $companyLogo = $settingService->get('company_logo');
            $companyName = $settingService->get('company_name', 'نابت وخليفة');
        @endphp

        @if ($companyLogo)
            <img src="{{ asset('storage/' . $companyLogo) }}" alt="{{ $companyName }}" class=" h-25 w-50 nk-brand-logo-img">
        @else
            <div class="nk-brand-logo">NK</div>
        @endif

        <div>
            <h6 class="nk-brand-title">{{ $companyName }}</h6>
            <div class="nk-brand-subtitle">
                نظام إدارة المعاملات
            </div>
        </div>
    </div>

    <nav class="nk-sidebar-menu">
        <a href="{{ route('dashboard') }}" class="nk-menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            لوحة التحكم
        </a>

        @can('view clients')
            <a href="{{ route('admin.clients.index') }}"
                class="nk-menu-link {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                العملاء
            </a>
        @endcan

        @can('view transactions')
            <a href="{{ route('admin.transactions.index') }}"
                class="nk-menu-link {{ request()->routeIs('admin.transactions.*') && !request()->routeIs('admin.transactions.archived') ? 'active' : '' }}">
                <i class="bi bi-folder2-open"></i>
                المعاملات
            </a>
        @endcan

        <a href="{{ route('admin.transaction-types.index') }}"
            class="nk-menu-link {{ request()->routeIs('admin.transaction-types.*') ? 'active' : '' }}">
            <i class="bi bi-list-check"></i>
            أنواع المعاملات
        </a>

        @can('view transactions')
            <a href="{{ route('admin.transactions.archived') }}"
                class="nk-menu-link {{ request()->routeIs('admin.transactions.archived') ? 'active' : '' }}">
                <i class="bi bi-archive"></i>
                الأرشيف
            </a>
        @endcan

        <a href="{{ route('admin.settings.edit') }}"
            class="nk-menu-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <i class="bi bi-gear"></i>
            إعدادات النظام
        </a>
    </nav>
</aside>
