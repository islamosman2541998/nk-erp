<x-app-layout>
    <div class="mb-4">
        <h1 class="nk-page-title">تعديل نوع المعاملة</h1>
        <p class="nk-page-subtitle">
            تعديل بيانات نوع المعاملة وإدارة المستندات المطلوبة له.
        </p>
    </div>

    <div class="nk-card mb-4">
        <form method="POST" action="{{ route('admin.transaction-types.update', $transactionType) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">اسم نوع المعاملة <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control"
                        value="{{ old('name', $transactionType->name) }}" required>
                    @error('name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">الترتيب</label>
                    <input type="number" name="sort_order" class="form-control"
                        value="{{ old('sort_order', $transactionType->sort_order) }}">
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                            @checked(old('is_active', $transactionType->is_active))>
                        <label class="form-check-label" for="is_active">
                            نشط
                        </label>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label">الوصف</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $transactionType->description) }}</textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.transaction-types.index') }}" class="btn btn-outline-secondary rounded-pill">
                    رجوع
                </a>

                <button type="submit" class="nk-btn-main">
                    <i class="bi bi-save"></i>
                    حفظ التعديلات
                </button>
            </div>
        </form>
    </div>

    <div class="nk-card mb-4">
        <h5 class="fw-bold text-success mb-3">إضافة مستند مطلوب</h5>

        <form method="POST" action="{{ route('admin.transaction-type-documents.store', $transactionType) }}">
            @csrf

            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">اسم المستند</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">الوصف</label>
                    <input type="text" name="description" class="form-control">
                </div>

                <div class="col-md-2">
                    <label class="form-label">الترتيب</label>
                    <input type="number" name="sort_order" class="form-control" value="0">
                </div>

                <div class="col-md-1">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_required" value="1" checked
                            id="new_required">
                        <label class="form-check-label" for="new_required">
                            مطلوب
                        </label>
                    </div>
                </div>

                <div class="col-md-1">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked
                            id="new_active">
                        <label class="form-check-label" for="new_active">
                            نشط
                        </label>
                    </div>
                </div>

                <div class="col-md-1">
                    <button type="submit" class="btn btn-success w-100 rounded-pill">
                        إضافة
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="nk-card">
        <h5 class="fw-bold text-success mb-3">مستندات هذا النوع</h5>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>اسم المستند</th>
                        <th>الوصف</th>
                        <th>الترتيب</th>
                        <th>مطلوب</th>
                        <th>نشط</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($transactionType->documentRequirements as $document)
                        <tr>
                            <td>
                                <form id="update-document-{{ $document->id }}" method="POST"
                                    action="{{ route('admin.transaction-type-documents.update', $document) }}">
                                    @csrf
                                    @method('PUT')
                                </form>

                                <input type="text" name="name" form="update-document-{{ $document->id }}"
                                    class="form-control form-control-sm" value="{{ $document->name }}" required>
                            </td>

                            <td>
                                <input type="text" name="description" form="update-document-{{ $document->id }}"
                                    class="form-control form-control-sm" value="{{ $document->description }}">
                            </td>

                            <td>
                                <input type="number" name="sort_order" form="update-document-{{ $document->id }}"
                                    class="form-control form-control-sm" value="{{ $document->sort_order }}">
                            </td>

                            <td class="text-center">
                                <input type="checkbox" name="is_required" value="1"
                                    form="update-document-{{ $document->id }}" class="form-check-input"
                                    @checked($document->is_required)>
                            </td>

                            <td class="text-center">
                                <input type="checkbox" name="is_active" value="1"
                                    form="update-document-{{ $document->id }}" class="form-check-input"
                                    @checked($document->is_active)>
                            </td>

                            <td>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="submit" form="update-document-{{ $document->id }}"
                                        class="btn btn-sm btn-outline-primary rounded-pill">
                                        حفظ
                                    </button>

                                    <form method="POST"
                                        action="{{ route('admin.transaction-type-documents.destroy', $document) }}"
                                        class="js-confirm-form" data-title="حذف المستند"
                                        data-text="هل أنت متأكد من حذف هذا المستند من نوع المعاملة؟"
                                        data-icon="warning" data-confirm-text="نعم، احذف" data-cancel-text="إلغاء"
                                        data-confirm-color="#c0392b">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                                            <i class="bi bi-trash"></i>
                                            حذف
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                لا توجد مستندات لهذا النوع حتى الآن.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
