<x-app-layout>
    @php
        $roleLabel = config('roles.labels.' . $role->name, $role->name);
    @endphp

    <div class="mb-4">
        <h1 class="nk-page-title">تعديل الدور</h1>
        <p class="nk-page-subtitle">
            تعديل بيانات الدور وإدارة الصلاحيات المرتبطة به.
        </p>
    </div>

    <form method="POST" action="{{ route('admin.roles.update', $role) }}">
        @csrf
        @method('PUT')

        <div class="nk-card mb-4">
            <h5 class="fw-bold text-success mb-4">بيانات الدور</h5>

            @if($role->name === 'CEO')
                <div class="alert alert-warning rounded-4 mb-4">
                    دور <strong>{{ $roleLabel }}</strong> هو مدير النظام الرئيسي، وسيحصل دائمًا على كل الصلاحيات تلقائيًا.
                </div>
            @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">
                        اسم الدور الداخلي <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                           name="name"
                           class="form-control"
                           value="{{ old('name', $role->name) }}"
                           required
                           dir="ltr"
                           @readonly($role->name === 'CEO')>

                    @error('name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror

                    <div class="form-text">
                        هذا هو الاسم الداخلي المستخدم داخل النظام والصلاحيات.
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">
                        الاسم الظاهر للمستخدم
                    </label>

                    <input type="text"
                           class="form-control"
                           value="{{ $roleLabel }}"
                           readonly>

                    <div class="form-text">
                        يتم عرض هذا الاسم داخل لوحة التحكم بدل الاسم الإنجليزي.
                    </div>
                </div>
            </div>
        </div>

        <div class="nk-card mb-4">
            <div class="mb-4">
                <h5 class="fw-bold text-success mb-1">صلاحيات الدور</h5>

                @if($role->name === 'CEO')
                    <p class="text-muted small mb-0">
                        هذا الدور يحصل على كل الصلاحيات تلقائيًا ولا يحتاج لتحديد يدوي.
                    </p>
                @else
                    <p class="text-muted small mb-0">
                        حدد الصلاحيات التي يحصل عليها أي مستخدم يحمل دور
                        <strong>{{ $roleLabel }}</strong>.
                    </p>
                @endif
            </div>

            @include('admin.shared.permissions-grid', [
                'permissions' => $permissions,
                'selectedPermissions' => old(
                    'permissions',
                    $role->name === 'CEO'
                        ? $permissions->pluck('name')->toArray()
                        : $role->permissions->pluck('name')->toArray()
                ),
            ])

            @error('permissions')
                <div class="text-danger small mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary rounded-pill">
                رجوع
            </a>

            <button type="submit" class="nk-btn-main">
                <i class="bi bi-save"></i>
                حفظ التعديلات
            </button>
        </div>
    </form>
</x-app-layout>