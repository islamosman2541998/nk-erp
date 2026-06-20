<x-app-layout>
    <div class="mb-4">
        <h1 class="nk-page-title">تعديل المستخدم</h1>
        <p class="nk-page-subtitle">
            تعديل بيانات المستخدم وأدواره وصلاحياته.
        </p>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PUT')

        <div class="nk-card mb-4">
            <h5 class="fw-bold text-success mb-4">بيانات المستخدم</h5>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">اسم المستخدم <span class="text-danger">*</span></label>
                    <input type="text"
                           name="name"
                           class="form-control"
                           value="{{ old('name', $user->name) }}"
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
                           value="{{ old('email', $user->email) }}"
                           required
                           dir="ltr">

                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-2">
                    <label class="form-label">كلمة مرور جديدة</label>
                    <input type="password"
                           name="password"
                           class="form-control"
                           dir="ltr">

                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-2">
                    <label class="form-label">تأكيد كلمة المرور</label>
                    <input type="password"
                           name="password_confirmation"
                           class="form-control"
                           dir="ltr">
                </div>
            </div>

            <div class="form-text mt-2">
                اترك كلمة المرور فارغة إذا كنت لا تريد تغييرها.
            </div>
        </div>

        <div class="nk-card mb-4">
            <h5 class="fw-bold text-success mb-4">الدور</h5>

            @php
                $selectedRoles = collect(old('roles', $user->roles->pluck('name')->toArray()));
            @endphp

            <div class="row g-3">
                @foreach($roles as $role)
                    <div class="col-md-3">
                        <label class="nk-role-option">
                            <input type="checkbox"
                                   name="roles[]"
                                   value="{{ $role->name }}"
                                   class="form-check-input"
                                   @checked($selectedRoles->contains($role->name))>

                           <span>{{ config('roles.labels.' . $role->name, $role->name) }}</span>
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="nk-card mb-4">
            <div class="mb-4">
                <h5 class="fw-bold text-success mb-1">صلاحيات مباشرة إضافية</h5>
                <p class="text-muted small mb-0">
                    هذه الصلاحيات تُضاف للمستخدم مباشرة بجانب صلاحيات الدور.
                </p>
            </div>

            @include('admin.shared.permissions-grid', [
                'permissions' => $permissions,
                'selectedPermissions' => old(
                    'permissions',
                    $user->permissions->pluck('name')->toArray()
                ),
            ])
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary rounded-pill">
                رجوع
            </a>

            <button type="submit" class="nk-btn-main">
                <i class="bi bi-save"></i>
                حفظ التعديلات
            </button>
        </div>
    </form>
</x-app-layout>