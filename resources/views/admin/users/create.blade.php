<x-app-layout>
    <div class="mb-4">
        <h1 class="nk-page-title">إضافة مستخدم جديد</h1>
        <p class="nk-page-subtitle">
            إنشاء حساب جديد وتحديد الدور والصلاحيات المناسبة له.
        </p>
    </div>

    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf

        <div class="nk-card mb-4">
            <h5 class="fw-bold text-success mb-4">بيانات المستخدم</h5>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">اسم المستخدم <span class="text-danger">*</span></label>
                    <input type="text"
                           name="name"
                           class="form-control"
                           value="{{ old('name') }}"
                           required>

                    @error('name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                    <input type="email"
                           name="email"
                           class="form-control"
                           value="{{ old('email') }}"
                           required
                           dir="ltr">

                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-2">
                    <label class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                    <input type="password"
                           name="password"
                           class="form-control"
                           required
                           dir="ltr">

                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-2">
                    <label class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                    <input type="password"
                           name="password_confirmation"
                           class="form-control"
                           required
                           dir="ltr">
                </div>
            </div>
        </div>

        <div class="nk-card mb-4">
            <h5 class="fw-bold text-success mb-4">الدور</h5>

            <div class="row g-3">
                @foreach($roles as $role)
                    <div class="col-md-3">
                        <label class="nk-role-option">
                            <input type="checkbox"
                                   name="roles[]"
                                   value="{{ $role->name }}"
                                   class="form-check-input"
                                   @checked(collect(old('roles', []))->contains($role->name))>

                           <span>{{ config('roles.labels.' . $role->name, $role->name) }}</span>
                        </label>
                    </div>
                @endforeach
            </div>

            @error('roles')
                <div class="text-danger small mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div class="nk-card mb-4">
            <div class="mb-4">
                <h5 class="fw-bold text-success mb-1">صلاحيات مباشرة إضافية</h5>
                <p class="text-muted small mb-0">
                    استخدمها فقط لو المستخدم محتاج صلاحيات خاصة بجانب الدور.
                </p>
            </div>

            @include('admin.shared.permissions-grid', [
                'permissions' => $permissions,
                'selectedPermissions' => old('permissions', []),
            ])
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary rounded-pill">
                رجوع
            </a>

            <button type="submit" class="nk-btn-main">
                <i class="bi bi-save"></i>
                حفظ المستخدم
            </button>
        </div>
    </form>
</x-app-layout>