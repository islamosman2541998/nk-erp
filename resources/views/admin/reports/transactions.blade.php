<x-app-layout>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="nk-page-title">تقرير المعاملات</h1>
            <p class="nk-page-subtitle">
                متابعة حالات المعاملات، التأخير، التسليم القريب، وتوزيع العمل حسب المسؤول.
            </p>
        </div>

        <a href="{{ route('admin.reports.transactions.export', request()->query()) }}"
           class="btn btn-outline-success rounded-pill">
            <i class="bi bi-file-earmark-excel"></i>
            تصدير Excel
        </a>
    </div>

    <div class="nk-card mb-4">
        <form method="GET" action="{{ route('admin.reports.transactions') }}" class="row g-3">
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

            <div class="col-md-3">
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

            <div class="col-md-3">
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
                <label class="form-label">الحالة</label>
                <select name="status" class="form-select">
                    <option value="">كل الحالات</option>
                    @foreach(['جديدة', 'بانتظار مستندات', 'تحت الإعداد', 'تم الرفع', 'وردت ملاحظات', 'مكتملة', 'مغلقة'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">المسؤول</label>
                <select name="assigned_to" class="form-select">
                    <option value="">الكل</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected(request('assigned_to') == $user->id)>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('admin.reports.transactions') }}"
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

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="nk-finance-box">
                <div class="nk-finance-icon bg-primary-subtle text-primary">
                    <i class="bi bi-folder2-open"></i>
                </div>
                <small>إجمالي المعاملات</small>
                <strong>{{ $summary['total'] }}</strong>
                <span>معاملة</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="nk-finance-box">
                <div class="nk-finance-icon bg-danger-subtle text-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <small>معاملات متأخرة</small>
                <strong>{{ $summary['late_count'] }}</strong>
                <span>تجاوزت تاريخ التسليم المتوقع</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="nk-finance-box">
                <div class="nk-finance-icon bg-warning-subtle text-warning">
                    <i class="bi bi-clock-history"></i>
                </div>
                <small>قريبة التسليم</small>
                <strong>{{ $summary['near_delivery_count'] }}</strong>
                <span>خلال 7 أيام</span>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="nk-card h-100">
                <h5 class="fw-bold text-success mb-3">حسب الحالة</h5>

                @forelse($summary['by_status'] as $status => $count)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <span>{{ $status }}</span>
                        <span class="badge bg-success-subtle text-success rounded-pill">{{ $count }}</span>
                    </div>
                @empty
                    <p class="text-muted mb-0">لا توجد بيانات</p>
                @endforelse
            </div>
        </div>

        <div class="col-lg-4">
            <div class="nk-card h-100">
                <h5 class="fw-bold text-success mb-3">حسب نوع المعاملة</h5>

                @forelse($summary['by_type'] as $type => $count)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <span>{{ $type }}</span>
                        <span class="badge bg-primary-subtle text-primary rounded-pill">{{ $count }}</span>
                    </div>
                @empty
                    <p class="text-muted mb-0">لا توجد بيانات</p>
                @endforelse
            </div>
        </div>

        <div class="col-lg-4">
            <div class="nk-card h-100">
              <h5 class="fw-bold text-success mb-3">حسب المسؤول الرئيسي</h5>

                @forelse($summary['by_assigned_user'] as $user => $count)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <span>{{ $user }}</span>
                        <span class="badge bg-warning-subtle text-warning rounded-pill">{{ $count }}</span>
                    </div>
                @empty
                    <p class="text-muted mb-0">لا توجد بيانات</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="nk-card mb-4">
        <h5 class="fw-bold text-danger mb-3">المعاملات المتأخرة</h5>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>رقم المعاملة</th>
                        <th>العميل</th>
                        <th>المشروع</th>
                        <th>الحالة</th>
                        <th>التسليم المتوقع</th>
                        <th>المسؤول</th>
                        <th>عرض</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($summary['late_transactions'] as $transaction)
                        <tr>
                            <td class="fw-bold">{{ $transaction->reference_number }}</td>
                            <td>{{ $transaction->client?->name ?? '-' }}</td>
                            <td>{{ $transaction->project_name ?? '-' }}</td>
                            <td>
                                <span class="badge bg-danger-subtle text-danger rounded-pill">
                                    {{ $transaction->status }}
                                </span>
                            </td>
                            <td>{{ $transaction->expected_delivery_at ? \Carbon\Carbon::parse($transaction->expected_delivery_at)->format('Y-m-d') : '-' }}</td>
                            <td>{{ $transaction->assignedUser?->name ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.transactions.show', $transaction) }}"
                                   class="btn btn-sm btn-outline-success rounded-pill">
                                    عرض
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                لا توجد معاملات متأخرة
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="nk-card">
        <h5 class="fw-bold text-warning mb-3">المعاملات قريبة التسليم</h5>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>رقم المعاملة</th>
                        <th>العميل</th>
                        <th>المشروع</th>
                        <th>الحالة</th>
                        <th>التسليم المتوقع</th>
                        <th>المسؤول</th>
                        <th>عرض</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($summary['near_delivery_transactions'] as $transaction)
                        <tr>
                            <td class="fw-bold">{{ $transaction->reference_number }}</td>
                            <td>{{ $transaction->client?->name ?? '-' }}</td>
                            <td>{{ $transaction->project_name ?? '-' }}</td>
                            <td>
                                <span class="badge bg-warning-subtle text-warning rounded-pill">
                                    {{ $transaction->status }}
                                </span>
                            </td>
                            <td>{{ $transaction->expected_delivery_at ? \Carbon\Carbon::parse($transaction->expected_delivery_at)->format('Y-m-d') : '-' }}</td>
                            <td>{{ $transaction->assignedUser?->name ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.transactions.show', $transaction) }}"
                                   class="btn btn-sm btn-outline-success rounded-pill">
                                    عرض
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                لا توجد معاملات قريبة التسليم
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>