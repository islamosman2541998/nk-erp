<x-app-layout>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="nk-page-title">أنواع المعاملات</h1>
            <p class="nk-page-subtitle">
                إدارة أنواع المعاملات والمستندات المطلوبة لكل نوع.
            </p>
        </div>

        <a href="{{ route('admin.transaction-types.create') }}" class="nk-btn-main">
            <i class="bi bi-plus-circle"></i>
            إضافة نوع معاملة
        </a>
    </div>

    <div class="row g-4">
        @forelse($transactionTypes as $type)
            <div class="col-lg-6">
                <div class="nk-card h-100">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h5 class="fw-bold text-success mb-2">
                                {{ $type->name }}
                            </h5>

                            @if ($type->description)
                                <p class="text-muted small mb-2">
                                    {{ $type->description }}
                                </p>
                            @endif

                            @if ($type->is_active)
                                <span class="badge bg-success-subtle text-success rounded-pill">
                                    نشط
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger rounded-pill">
                                    غير نشط
                                </span>
                            @endif
                        </div>

                        <span class="badge bg-success-subtle text-success rounded-pill">
                            {{ $type->documentRequirements->count() }} مستند
                        </span>
                    </div>

                    <div class="border-top pt-3">
                        <h6 class="fw-bold mb-3">المستندات المطلوبة:</h6>

                        @if ($type->documentRequirements->isNotEmpty())
                            <div class="list-group list-group-flush">
                                @foreach ($type->documentRequirements as $requirement)
                                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="bi bi-file-earmark-text text-success"></i>
                                            {{ $requirement->name }}
                                        </span>

                                        <div class="d-flex gap-1">
                                            @if ($requirement->is_required)
                                                <span class="badge bg-warning-subtle text-warning">
                                                    مطلوب
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary">
                                                    اختياري
                                                </span>
                                            @endif

                                            @if (!$requirement->is_active)
                                                <span class="badge bg-danger-subtle text-danger">
                                                    غير نشط
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted mb-0">
                                لا توجد مستندات مضافة لهذا النوع.
                            </p>
                        @endif
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.transaction-types.edit', $type) }}"
                            class="btn btn-sm btn-outline-primary rounded-pill">
                            <i class="bi bi-pencil-square"></i>
                            تعديل
                        </a>

                        <form method="POST" action="{{ route('admin.transaction-types.destroy', $type) }}"
                            class="js-confirm-form" data-title="حذف نوع المعاملة"
                            data-text="لو النوع مرتبط بمعاملات سيتم تعطيله بدل الحذف. هل تريد المتابعة؟"
                            data-icon="warning" data-confirm-text="نعم، تابع" data-cancel-text="إلغاء"
                            data-confirm-color="#c0392b">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                                <i class="bi bi-trash"></i>
                                حذف
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="nk-card text-center text-muted">
                    لا توجد أنواع معاملات حتى الآن.
                </div>
            </div>
        @endforelse
    </div>
</x-app-layout>
