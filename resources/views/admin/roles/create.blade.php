<x-app-layout>
    <div class="mb-4">
        <h1 class="nk-page-title">إضافة دور جديد</h1>
        <p class="nk-page-subtitle">
            إنشاء دور جديد وتحديد الصلاحيات التي يحصل عليها المستخدمون المرتبطون به.
        </p>
    </div>

    <form method="POST" action="{{ route('admin.roles.store') }}">
        @csrf

        <div class="nk-card mb-4">
            <h5 class="fw-bold text-success mb-4">بيانات الدور</h5>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">
                        اسم الدور <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                           name="name"
                           class="form-control"
                           value="{{ old('name') }}"
                           required
                           placeholder="مثال: Sales Manager">

                    @error('name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror

                    <div class="form-text">
                        يفضل كتابة اسم الدور بالإنجليزية حتى يتوافق مع نظام الصلاحيات.
                    </div>
                </div>
            </div>
        </div>

        <div class="nk-card mb-4">
            <div class="mb-4">
                <h5 class="fw-bold text-success mb-1">صلاحيات الدور</h5>
                <p class="text-muted small mb-0">
                    حدد الصلاحيات التي سيحصل عليها أي مستخدم يحمل هذا الدور.
                </p>
            </div>

            @include('admin.shared.permissions-grid', [
                'permissions' => $permissions,
                'selectedPermissions' => old('permissions', []),
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
                حفظ الدور
            </button>
        </div>
    </form>
</x-app-layout>