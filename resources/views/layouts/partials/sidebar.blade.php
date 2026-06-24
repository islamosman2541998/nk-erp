<aside class="nk-sidebar">
    <div class="nk-sidebar-brand">
        @php
            $settingService = app(\App\Services\SettingService::class);
            $companyLogo = $settingService->get('company_logo');
            $companyName = $settingService->get('company_name', 'نابت وخليفة');
        @endphp

        @if ($companyLogo)
            <img src="{{ asset('storage/' . $companyLogo) }}" alt="{{ $companyName }}" class="nk-brand-logo-img">
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

        @can('manage users')
            <a href="{{ route('admin.users.index') }}"
                class="nk-menu-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i>
                المستخدمين
            </a>
        @endcan

        @can('manage roles')
            <a href="{{ route('admin.roles.index') }}"
                class="nk-menu-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                <i class="bi bi-shield-lock"></i>
                الأدوار والصلاحيات
            </a>
        @endcan

        @can('view clients')
            <a href="{{ route('admin.clients.index') }}"
                class="nk-menu-link {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                العملاء
            </a>
        @endcan

        @canany(['view transactions', 'view assigned transactions'])
            <a href="{{ route('admin.transactions.index') }}"
                class="nk-menu-link {{ request()->routeIs('admin.transactions.*') && !request()->routeIs('admin.transactions.archived') ? 'active' : '' }}">
                <i class="bi bi-folder2-open"></i>
                المعاملات
            </a>
        @endcanany
        @can('view contracts')
            <a href="{{ route('admin.contracts.index') }}"
                class="nk-menu-link {{ request()->routeIs('admin.contracts.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i>
                العقود
            </a>
        @endcan

        @can('view transaction types')
            <a href="{{ route('admin.transaction-types.index') }}"
                class="nk-menu-link {{ request()->routeIs('admin.transaction-types.*') ? 'active' : '' }}">
                <i class="bi bi-list-check"></i>
                أنواع المعاملات
            </a>
        @endcan

        {{-- @canany(['view archive', 'view transactions'])
            <a href="{{ route('admin.transactions.archived') }}"
                class="nk-menu-link {{ request()->routeIs('admin.transactions.archived') ? 'active' : '' }}">
                <i class="bi bi-archive"></i>
                الأرشيف
            </a>
        @endcanany --}}
        {{-- @canany(['view archive', 'view attachments'])
            <a href="{{ route('admin.archive.attachments') }}"
                class="nk-menu-link {{ request()->routeIs('admin.archive.attachments') ? 'active' : '' }}">
                <i class="bi bi-paperclip"></i>
                أرشيف المرفقات
            </a>
        @endcanany --}}
        {{-- @can('view reports')
            <a href="{{ route('admin.reports.financial') }}"
                class="nk-menu-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line"></i>
                التقارير المالية
            </a>
        @endcan --}}
        {{-- @can('view reports')
            <a href="{{ route('admin.reports.transactions') }}"
                class="nk-menu-link {{ request()->routeIs('admin.reports.transactions') ? 'active' : '' }}">
                <i class="bi bi-clipboard-data"></i>
                تقرير المعاملات
            </a>
        @endcan
        @can('view audit logs')
            <a href="{{ route('admin.audit-logs.index') }}"
                class="nk-menu-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                <i class="bi bi-activity"></i>
                سجل العمليات
            </a>
        @endcan --}}
        @can('edit settings')
            <a href="{{ route('admin.settings.edit') }}"
                class="nk-menu-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear"></i>
                إعدادات النظام
            </a>
        @endcan
    </nav>
</aside>
