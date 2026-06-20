<x-guest-layout>
    <div class="nk-auth-header">
        <h2>تسجيل الدخول</h2>
        <p>ادخل بيانات حسابك للوصول إلى لوحة التحكم.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success rounded-4">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="nk-auth-form">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">البريد الإلكتروني</label>
            <input id="email"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror"
                   required
                   autofocus
                   autocomplete="username"
                   dir="ltr"
                   placeholder="example@email.com">

            @error('email')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">كلمة المرور</label>
            <input id="password"
                   type="password"
                   name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   required
                   autocomplete="current-password"
                   dir="ltr"
                   placeholder="••••••••">

            @error('password')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div class="form-check">
                <input id="remember_me"
                       type="checkbox"
                       name="remember"
                       class="form-check-input">

                <label class="form-check-label" for="remember_me">
                    تذكرني
                </label>
            </div>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="nk-auth-link">
                    نسيت كلمة المرور؟
                </a>
            @endif
        </div>

        <button type="submit" class="nk-auth-submit">
            <i class="bi bi-box-arrow-in-right"></i>
            دخول النظام
        </button>
    </form>
</x-guest-layout>