<header class="nk-navbar">
    <div>
        <div class="nk-navbar-title">
            مرحبًا، {{ auth()->user()?->name }}
        </div>

        <div class="nk-navbar-subtitle">
            {{ now()->translatedFormat('l d F Y') }}
        </div>
    </div>

    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.transactions.create') }}" class="btn btn-success rounded-pill">
            <i class="bi bi-plus-circle"></i>
            معاملة جديدة
        </a>

        <div class="dropdown">
            <button class="btn btn-outline-secondary rounded-pill dropdown-toggle"
                    type="button"
                    data-bs-toggle="dropdown">
                <i class="bi bi-person-circle"></i>
                الحساب
            </button>

            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <div class="dropdown-item-text">
                        {{ auth()->user()?->email }}
                    </div>
                </li>

                <li>
                    <hr class="dropdown-divider">
                </li>

                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button type="submit" class="dropdown-item text-danger">
                            تسجيل الخروج
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>