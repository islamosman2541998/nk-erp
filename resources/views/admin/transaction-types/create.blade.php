<x-app-layout>
    <div class="mb-4">
        <h1 class="nk-page-title">إضافة نوع معاملة</h1>
        <p class="nk-page-subtitle">
            إنشاء نوع معاملة جديد يمكن استخدامه داخل النظام.
        </p>
    </div>

    <div class="nk-card">
        <form method="POST" action="{{ route('admin.transaction-types.store') }}">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">اسم نوع المعاملة <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">الترتيب</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked id="is_active">
                        <label class="form-check-label" for="is_active">
                            نشط
                        </label>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label">الوصف</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.transaction-types.index') }}" class="btn btn-outline-secondary rounded-pill">
                    رجوع
                </a>

                <button type="submit" class="nk-btn-main">
                    حفظ
                </button>
            </div>
        </form>
    </div>
</x-app-layout>