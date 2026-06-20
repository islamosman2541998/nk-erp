<x-app-layout>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="nk-page-title">العقود</h1>
            <p class="nk-page-subtitle">
                متابعة كل العقود المرتبطة بالمعاملات حسب الصلاحيات والمسؤوليات.
            </p>
        </div>

        <a href="{{ route('admin.contracts.export', request()->query()) }}"
           class="btn btn-outline-success rounded-pill">
            <i class="bi bi-file-earmark-excel"></i>
            تصدير Excel
        </a>
    </div>

    <div class="nk-card mb-4">
        <form method="GET" action="{{ route('admin.contracts.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">بحث</label>
                <input type="text"
                       name="search"
                       class="form-control"
                       value="{{ request('search') }}"
                       placeholder="رقم العقد، رقم المعاملة، العميل...">
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
                <label class="form-label">المسؤول</label>
                <select name="assigned_to" class="form-select">
                    <option value="">كل المسؤولين</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected(request('assigned_to') == $user->id)>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">حالة العقد</label>
                <select name="status" class="form-select">
                    <option value="">كل الحالات</option>
                    @foreach(['مسودة', 'نشط', 'منتهي', 'ملغي'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>
                            {{ $status }}
                        </option>
                    @endforeach
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
                <label class="form-label">من تاريخ</label>
                <input type="date"
                       name="date_from"
                       class="form-control"
                       value="{{ request('date_from') }}">
            </div>

            <div class="col-md-2">
                <label class="form-label">إلى تاريخ</label>
                <input type="date"
                       name="date_to"
                       class="form-control"
                       value="{{ request('date_to') }}">
            </div>

            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('admin.contracts.index') }}"
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
                        <th>رقم العقد</th>
                        <th>المعاملة</th>
                        <th>العميل</th>
                        <th>نوع المعاملة</th>
                        <th>قيمة العقد</th>
                        <th>تاريخ العقد</th>
                        <th>الحالة</th>
                        <th>المسؤول</th>
                        <th>المرفقات</th>
                        <th>عرض</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($contracts as $contract)
                        @php
                            $transaction = $contract->transaction;
                        @endphp

                        <tr>
                            <td class="fw-bold">
                                {{ $contract->contract_number ?? '-' }}
                            </td>

                            <td>
                                <div class="fw-bold">
                                    {{ $transaction?->reference_number ?? '-' }}
                                </div>
                                <div class="text-muted small">
                                    {{ $transaction?->project_name ?? '-' }}
                                </div>
                            </td>

                            <td>
                                {{ $transaction?->client?->name ?? '-' }}
                            </td>

                            <td>
                                {{ $transaction?->transactionType?->name ?? '-' }}
                            </td>

                            <td class="fw-bold">
                                @if($contract->contract_value)
                                    {{ number_format((float) $contract->contract_value, 2) }}
                                    {{ $contract->currency }}
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                {{ $contract->contract_date?->format('Y-m-d') ?? '-' }}
                            </td>

                            <td>
                                @php
                                    $statusClass = match($contract->status) {
                                        'نشط' => 'bg-success-subtle text-success',
                                        'منتهي' => 'bg-secondary-subtle text-secondary',
                                        'ملغي' => 'bg-danger-subtle text-danger',
                                        default => 'bg-warning-subtle text-warning',
                                    };
                                @endphp

                                <span class="badge {{ $statusClass }} rounded-pill">
                                    {{ $contract->status }}
                                </span>
                            </td>

                            <td>
                                <div class="fw-bold">
                                    {{ $transaction?->assignedUser?->name ?? '-' }}
                                </div>

                                @if($transaction?->technicalManager)
                                    <div class="text-muted small">
                                        فني: {{ $transaction->technicalManager->name }}
                                    </div>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex gap-2 flex-wrap">
                                    @if($contract->file_path)
                                        <a href="{{ asset('storage/' . $contract->file_path) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-success rounded-pill">
                                            ملف
                                        </a>
                                    @endif

                                    @if($contract->drive_link)
                                        <a href="{{ $contract->drive_link }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-primary rounded-pill">
                                            Drive
                                        </a>
                                    @endif

                                    @if(!$contract->file_path && !$contract->drive_link)
                                        <span class="text-muted small">لا يوجد</span>
                                    @endif
                                </div>
                            </td>

                            <td>
                                @if($transaction)
                                    <a href="{{ route('admin.transactions.show', $transaction) }}"
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
                                لا توجد عقود حسب الفلاتر الحالية
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $contracts->links() }}
        </div>
    </div>
</x-app-layout>