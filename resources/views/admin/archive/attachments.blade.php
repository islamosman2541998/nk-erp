<x-app-layout>
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h1 class="nk-page-title">أرشيف المرفقات</h1>
        <p class="nk-page-subtitle">
            متابعة كل مستندات ومرفقات المعاملات النشطة والمؤرشفة.
        </p>
    </div>

    <a href="{{ route('admin.archive.attachments.export', request()->query()) }}"
       class="btn btn-outline-success rounded-pill">
        <i class="bi bi-file-earmark-excel"></i>
        تصدير Excel
    </a>
</div>

    <div class="nk-card mb-4">
        <form method="GET" action="{{ route('admin.archive.attachments') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">بحث</label>
                <input type="text"
                       name="search"
                       class="form-control"
                       value="{{ request('search') }}"
                       placeholder="رقم المعاملة، اسم المستند، العميل...">
            </div>

            <div class="col-md-2">
                <label class="form-label">العميل</label>
                <select name="client_id" class="form-select">
                    <option value="">كل العملاء</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" @selected(request('client_id') == $client->id)>
                            {{ $client->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">نوع المعاملة</label>
                <select name="transaction_type_id" class="form-select">
                    <option value="">كل الأنواع</option>
                    @foreach($transactionTypes as $type)
                        <option value="{{ $type->id }}" @selected(request('transaction_type_id') == $type->id)>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">حالة المستند</label>
                <select name="status" class="form-select">
                    <option value="">كل الحالات</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">نوع المرفق</label>
                <select name="attachment_type" class="form-select">
                    <option value="">الكل</option>
                    <option value="file" @selected(request('attachment_type') === 'file')>ملف مرفوع</option>
                    <option value="drive" @selected(request('attachment_type') === 'drive')>رابط Drive</option>
                    <option value="missing" @selected(request('attachment_type') === 'missing')>بدون مرفق</option>
                </select>
            </div>

            <div class="col-md-1">
                <label class="form-label">الأرشفة</label>
                <select name="archive_status" class="form-select">
                    <option value="">الكل</option>
                    <option value="active" @selected(request('archive_status') === 'active')>نشطة</option>
                    <option value="archived" @selected(request('archive_status') === 'archived')>مؤرشفة</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">رفع من</label>
                <input type="date"
                       name="uploaded_from"
                       class="form-control"
                       value="{{ request('uploaded_from') }}">
            </div>

            <div class="col-md-2">
                <label class="form-label">رفع إلى</label>
                <input type="date"
                       name="uploaded_to"
                       class="form-control"
                       value="{{ request('uploaded_to') }}">
            </div>

            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('admin.archive.attachments') }}"
                   class="btn btn-outline-secondary rounded-pill">
                    إعادة ضبط
                </a>

                <button type="submit" class="nk-btn-main">
                    <i class="bi bi-funnel"></i>
                    تطبيق الفلتر
                </button>
            </div>
        </form>
    </div>

    <div class="nk-card">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>المستند</th>
                        <th>المعاملة</th>
                        <th>العميل</th>
                        <th>نوع المعاملة</th>
                        <th>الحالة</th>
                        <th>المرفق</th>
                        <th>رفع بواسطة</th>
                        <th>تاريخ الرفع</th>
                        <th>الأرشفة</th>
                        <th>عرض</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($documents as $document)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $document->name }}</div>
                                @if($document->notes)
                                    <div class="text-muted small">{{ $document->notes }}</div>
                                @endif
                            </td>

                            <td class="fw-bold">
                                {{ $document->transaction?->reference_number ?? '-' }}
                            </td>

                            <td>
                                {{ $document->transaction?->client?->name ?? '-' }}
                            </td>

                            <td>
                                {{ $document->transaction?->transactionType?->name ?? '-' }}
                            </td>

                            <td>
                                @php
                                    $statusClass = match($document->status) {
                                        'معتمد', 'تمت المراجعة' => 'bg-success-subtle text-success',
                                        'تم الرفع' => 'bg-primary-subtle text-primary',
                                        'مرفوض' => 'bg-danger-subtle text-danger',
                                        default => 'bg-warning-subtle text-warning',
                                    };
                                @endphp

                                <span class="badge {{ $statusClass }} rounded-pill">
                                    {{ $document->status }}
                                </span>
                            </td>

                            <td>
                                <div class="d-flex gap-2 flex-wrap">
                                    @if($document->file_path)
                                        <a href="{{ asset('storage/' . $document->file_path) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-success rounded-pill">
                                            <i class="bi bi-file-earmark-text"></i>
                                            ملف
                                        </a>
                                    @endif

                                    @if($document->drive_link)
                                        <a href="{{ $document->drive_link }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-primary rounded-pill">
                                            <i class="bi bi-cloud-arrow-up"></i>
                                            Drive
                                        </a>
                                    @endif

                                    @if(!$document->file_path && !$document->drive_link)
                                        <span class="text-muted small">لا يوجد</span>
                                    @endif
                                </div>
                            </td>

                            <td>
                                {{ $document->uploadedBy?->name ?? '-' }}
                            </td>

                            <td>
                                {{ $document->uploaded_at?->format('Y-m-d') ?? '-' }}
                            </td>

                            <td>
                                @if($document->transaction?->archived_at)
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill">
                                        مؤرشفة
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success rounded-pill">
                                        نشطة
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if($document->transaction)
                                    <a href="{{ route('admin.transactions.show', $document->transaction) }}"
                                       class="btn btn-sm btn-outline-success rounded-pill">
                                        <i class="bi bi-eye"></i>
                                        عرض
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                لا توجد مرفقات حسب الفلاتر الحالية
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $documents->links() }}
        </div>
    </div>
</x-app-layout>