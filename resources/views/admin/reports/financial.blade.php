<x-app-layout>
   <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h1 class="nk-page-title">التقارير المالية</h1>
        <p class="nk-page-subtitle">
            ملخص مالي شامل للعقود، المدفوعات، المصروفات، العمولات، وصافي الربح.
        </p>
    </div>

    <a href="{{ route('admin.reports.financial.export', request()->query()) }}"
       class="btn btn-outline-success rounded-pill">
        <i class="bi bi-file-earmark-excel"></i>
        تصدير Excel
    </a>
</div>

    <div class="nk-card mb-4">
        <form method="GET" action="{{ route('admin.reports.financial') }}" class="row g-3">
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
                <a href="{{ route('admin.reports.financial') }}"
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
        <div class="col-md-4 col-xl-2">
            <div class="nk-finance-box">
                <div class="nk-finance-icon bg-primary-subtle text-primary">
                    <i class="bi bi-folder2-open"></i>
                </div>
                <small>عدد المعاملات</small>
                <strong>{{ $summary['transactions_count'] }}</strong>
                <span>معاملة</span>
            </div>
        </div>

        <div class="col-md-4 col-xl-2">
            <div class="nk-finance-box">
                <div class="nk-finance-icon bg-success-subtle text-success">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <small>إجمالي العقود</small>
                <strong>{{ number_format($summary['contracts_total'], 2) }}</strong>
                <span>ريال</span>
            </div>
        </div>

        <div class="col-md-4 col-xl-2">
            <div class="nk-finance-box">
                <div class="nk-finance-icon bg-success-subtle text-success">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <small>إجمالي المدفوع</small>
                <strong>{{ number_format($summary['paid_total'], 2) }}</strong>
                <span>ريال</span>
            </div>
        </div>

        <div class="col-md-4 col-xl-2">
            <div class="nk-finance-box">
                <div class="nk-finance-icon bg-warning-subtle text-warning">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <small>إجمالي المتبقي</small>
                <strong>{{ number_format($summary['remaining_total'], 2) }}</strong>
                <span>ريال</span>
            </div>
        </div>

        <div class="col-md-4 col-xl-2">
            <div class="nk-finance-box">
                <div class="nk-finance-icon bg-danger-subtle text-danger">
                    <i class="bi bi-receipt"></i>
                </div>
                <small>المصروفات</small>
                <strong>{{ number_format($summary['expenses_total'], 2) }}</strong>
                <span>ريال</span>
            </div>
        </div>

        <div class="col-md-4 col-xl-2">
            <div class="nk-finance-box {{ $summary['net_profit_total'] < 0 ? 'nk-finance-loss' : '' }}">
                <div class="nk-finance-icon {{ $summary['net_profit_total'] < 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <small>صافي الربح</small>
                <strong>{{ number_format($summary['net_profit_total'], 2) }}</strong>
                <span>ريال</span>
            </div>
        </div>
    </div>

    <div class="nk-card">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h5 class="fw-bold text-success mb-1">تفاصيل المعاملات</h5>
                <p class="text-muted small mb-0">
                    تفصيل مالي لكل معاملة حسب الفلاتر الحالية.
                </p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>رقم المعاملة</th>
                        <th>العميل</th>
                        <th>نوع المعاملة</th>
                        <th>قيمة العقد</th>
                        <th>المدفوع</th>
                        <th>المتبقي</th>
                        <th>المصروفات</th>
                        <th>العمولات</th>
                        <th>صافي الربح</th>
                        <th>عرض</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($financialRows as $row)
                        @php
                            $transaction = $row['transaction'];
                        @endphp

                        <tr>
                            <td class="fw-bold">
                                {{ $transaction->reference_number }}
                            </td>

                            <td>
                                {{ $transaction->client?->name ?? '-' }}
                            </td>

                            <td>
                                {{ $transaction->transactionType?->name ?? '-' }}
                            </td>

                            <td>
                                {{ number_format($row['contract_value'], 2) }}
                            </td>

                            <td class="text-success fw-bold">
                                {{ number_format($row['paid_total'], 2) }}
                            </td>

                            <td class="text-warning fw-bold">
                                {{ number_format($row['remaining_amount'], 2) }}
                            </td>

                            <td class="text-danger fw-bold">
                                {{ number_format($row['expenses_total'], 2) }}
                            </td>

                            <td>
                                {{ number_format($row['commissions_total'], 2) }}
                            </td>

                            <td class="{{ $row['net_profit'] < 0 ? 'text-danger' : 'text-success' }} fw-bold">
                                {{ number_format($row['net_profit'], 2) }}
                            </td>

                            <td>
                                <a href="{{ route('admin.transactions.show', $transaction) }}"
                                   class="btn btn-sm btn-outline-success rounded-pill">
                                    <i class="bi bi-eye"></i>
                                    عرض
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                لا توجد بيانات مالية حسب الفلاتر الحالية
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>