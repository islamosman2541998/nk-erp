<x-app-layout>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="nk-page-title">تفاصيل المعاملة</h1>
            <p class="nk-page-subtitle">
                رقم المعاملة: {{ $transaction->reference_number }}
            </p>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline-secondary rounded-pill">
                رجوع
            </a>

            @can('edit transactions')
                <a href="{{ route('admin.transactions.edit', $transaction) }}" class="btn btn-outline-primary rounded-pill">
                    <i class="bi bi-pencil-square"></i>
                    تعديل
                </a>
            @endcan

            @can('close transactions')
                @if (is_null($transaction->archived_at))
                    <form method="POST" action="{{ route('admin.transactions.archive', $transaction) }}"
                        class="js-confirm-form" data-title="أرشفة المعاملة" data-text="هل تريد أرشفة هذه المعاملة؟"
                        data-icon="question" data-confirm-text="نعم، أرشف" data-cancel-text="إلغاء"
                        data-confirm-color="#073f22">
                        @csrf
                        @method('PATCH')

                        <button type="submit" class="btn btn-outline-dark rounded-pill">
                            <i class="bi bi-archive"></i>
                            أرشفة
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.transactions.unarchive', $transaction) }}"
                        class="js-confirm-form" data-title="إلغاء الأرشفة"
                        data-text="هل تريد إعادة هذه المعاملة إلى المعاملات النشطة؟" data-icon="info"
                        data-confirm-text="نعم، أعدها" data-cancel-text="إلغاء" data-confirm-color="#0b5c32">
                        @csrf
                        @method('PATCH')

                        <button type="submit" class="btn btn-outline-primary rounded-pill">
                            إلغاء الأرشفة
                        </button>
                    </form>
                @endif
            @endcan

            @can('delete transactions')
                <form method="POST" action="{{ route('admin.transactions.destroy', $transaction) }}"
                    onsubmit="return confirm('هل أنت متأكد من حذف هذه المعاملة؟')">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn btn-outline-danger rounded-pill">
                        <i class="bi bi-trash"></i>
                        حذف
                    </button>
                </form>
            @endcan
        </div>
    </div>

    {{-- @if (session('success'))
        <div class="alert alert-success rounded-4">
            {{ session('success') }}
        </div>
    @endif --}}

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="nk-card mb-4">
                <h5 class="fw-bold text-success mb-4">بيانات المعاملة</h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <small class="text-muted">العميل</small>
                        <div class="fw-bold">{{ $transaction->client?->name }}</div>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted">نوع المعاملة</small>
                        <div class="fw-bold">{{ $transaction->transactionType?->name }}</div>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted">الحالة</small>
                        <div class="fw-bold">{{ $transaction->status }}</div>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted">اسم المشروع</small>
                        <div class="fw-bold">{{ $transaction->project_name ?? '-' }}</div>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted">رقم الطلب في المركز</small>
                        <div class="fw-bold">{{ $transaction->center_request_number ?? '-' }}</div>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted">رقم التصريح</small>
                        <div class="fw-bold">{{ $transaction->permit_number ?? '-' }}</div>
                    </div>
                </div>
            </div>


        </div>

        <div class="col-lg-4">
            <div class="nk-card">
                <h5 class="fw-bold text-success mb-3">الفريق المسؤول</h5>

                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted">المسؤول الرئيسي</span>
                    <strong>{{ $transaction->assignedUser?->name ?? '-' }}</strong>
                </div>

                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted">المدير الفني</span>
                    <strong>{{ $transaction->technicalManager?->name ?? '-' }}</strong>
                </div>

                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted">المنسق</span>
                    <strong>{{ $transaction->coordinator?->name ?? '-' }}</strong>
                </div>

                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted">المسؤول المالي</span>
                    <strong>{{ $transaction->financialUser?->name ?? '-' }}</strong>
                </div>
            </div>
        </div>

    </div>
    {{-- @include('admin.transactions.partials.financial-summary-card') --}}
    @include('admin.transactions.partials.contract-card')
    {{-- @include('admin.transactions.partials.payments-card') --}}
    {{-- @include('admin.transactions.partials.expenses-card') --}}
    {{-- @include('admin.transactions.partials.commissions-card') --}}
    <div class="nk-card">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h5 class="fw-bold text-success mb-1">المستندات المطلوبة</h5>
                <p class="text-muted small mb-0">
                    يمكنك تحديث أكثر من مستند وحفظهم دفعة واحدة.
                </p>
            </div>
        </div>
        <div class="border rounded-4 p-3 mb-4 bg-light">
            <h6 class="fw-bold text-success mb-3">إضافة مستند جديد لهذه المعاملة</h6>

            <form method="POST" action="{{ route('admin.transactions.documents.store', $transaction) }}"
                enctype="multipart/form-data">
                @csrf

                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">اسم المستند</label>
                        <input type="text" name="name" class="form-control" placeholder="مثال: خطاب موافقة"
                            required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="ناقص">ناقص</option>
                            <option value="تم الرفع">تم الرفع</option>
                            <option value="مرفوض">مرفوض</option>
                            <option value="تمت المراجعة">تمت المراجعة</option>
                            <option value="معتمد">معتمد</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">رفع ملف</label>
                        <input type="file" name="file" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">رابط Drive</label>
                        <input type="url" name="drive_link" class="form-control"
                            placeholder="https://drive.google.com/...">
                    </div>

                    <div class="col-md-1">
                        <button type="submit" class="btn btn-success w-100">
                            إضافة
                        </button>
                    </div>

                    <div class="col-12">
                        <label class="form-label mt-2">ملاحظات</label>
                        <input type="text" name="notes" class="form-control"
                            placeholder="ملاحظات خاصة بالمستند">
                    </div>
                </div>
            </form>
        </div>
        <form method="POST" action="{{ route('admin.transactions.documents.bulk-update', $transaction) }}"
            enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th style="min-width: 180px;">المستند</th>
                            <th style="min-width: 140px;">الحالة</th>
                            <th style="min-width: 220px;">رفع ملف</th>
                            <th style="min-width: 220px;">رابط Drive</th>
                            <th style="min-width: 220px;">ملاحظات</th>
                            <th style="min-width: 120px;">مسح الملف</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($transaction->documents as $document)
                            <tr>
                                <td class="fw-bold">
                                    {{ $document->name }}

                                    @if ($document->file_path)
                                        <div class="mt-1">
                                            <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank"
                                                class="small text-success">
                                                عرض الملف الحالي
                                            </a>
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <select name="documents[{{ $document->id }}][status]"
                                        class="form-select form-select-sm">
                                        @foreach (['ناقص', 'تم الرفع', 'مرفوض', 'تمت المراجعة', 'معتمد'] as $status)
                                            <option value="{{ $status }}" @selected($document->status === $status)>
                                                {{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <td>
                                    <input type="file" name="documents[{{ $document->id }}][file]"
                                        class="form-control form-control-sm">
                                </td>

                                <td>
                                    <input type="url" name="documents[{{ $document->id }}][drive_link]"
                                        class="form-control form-control-sm"
                                        value="{{ old('documents.' . $document->id . '.drive_link', $document->drive_link) }}"
                                        placeholder="رابط Drive">
                                </td>

                                <td>
                                    <input type="text" name="documents[{{ $document->id }}][notes]"
                                        class="form-control form-control-sm"
                                        value="{{ old('documents.' . $document->id . '.notes', $document->notes) }}"
                                        placeholder="ملاحظات">
                                </td>

                                <td class="text-center">
                                    <input type="checkbox" name="documents[{{ $document->id }}][clear_file]"
                                        value="1" class="form-check-input"
                                        onclick="return confirm('هل تريد مسح الملف الحالي فقط؟')">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    لا توجد مستندات مطلوبة
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($transaction->documents->isNotEmpty())
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="nk-btn-main">
                        <i class="bi bi-save"></i>
                        حفظ كل المستندات
                    </button>
                </div>
            @endif
        </form>
    </div>
</x-app-layout>
