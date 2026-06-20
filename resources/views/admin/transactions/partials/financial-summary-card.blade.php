@canany(['view contracts', 'view payments', 'view expenses', 'view commissions'])
    @php
        $contractValue = (float) ($transaction->contract?->contract_value ?? 0);

        $paidTotal = (float) ($transaction->payments ?? collect())
            ->where('status', 'مدفوعة')
            ->sum('amount');

        $expensesTotal = (float) ($transaction->expenses ?? collect())
            ->where('status', '!=', 'ملغي')
            ->sum('amount');

        $commissionsTotal = (float) ($transaction->commissions ?? collect())
            ->where('status', '!=', 'ملغية')
            ->sum('calculated_amount');

        $remainingAmount = max($contractValue - $paidTotal, 0);

        $netProfit = $paidTotal - $expensesTotal - $commissionsTotal;

        $collectionRate = $contractValue > 0
            ? min(($paidTotal / $contractValue) * 100, 100)
            : 0;

        $currency = $transaction->contract?->currency
            ?? app(\App\Services\SettingService::class)->get('default_currency', 'SAR');
    @endphp

    <div class="nk-card mt-4 mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h5 class="fw-bold text-success mb-1">
                    الملخص المالي
                </h5>
                <p class="text-muted small mb-0">
                    نظرة سريعة على قيمة العقد، الدفعات، المصروفات، العمولات، وصافي الربح.
                </p>
            </div>

            <span class="badge bg-success-subtle text-success rounded-pill">
                نسبة التحصيل {{ number_format($collectionRate, 1) }}%
            </span>
        </div>

        <div class="row g-3">
            @can('view contracts')
                <div class="col-md-4 col-xl-2">
                    <div class="nk-finance-box">
                        <div class="nk-finance-icon bg-success-subtle text-success">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <small>قيمة العقد</small>
                        <strong>{{ number_format($contractValue, 2) }}</strong>
                        <span>{{ $currency }}</span>
                    </div>
                </div>
            @endcan

            @can('view payments')
                <div class="col-md-4 col-xl-2">
                    <div class="nk-finance-box">
                        <div class="nk-finance-icon bg-primary-subtle text-primary">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <small>إجمالي المدفوع</small>
                        <strong>{{ number_format($paidTotal, 2) }}</strong>
                        <span>{{ $currency }}</span>
                    </div>
                </div>

                <div class="col-md-4 col-xl-2">
                    <div class="nk-finance-box">
                        <div class="nk-finance-icon bg-warning-subtle text-warning">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <small>المتبقي</small>
                        <strong>{{ number_format($remainingAmount, 2) }}</strong>
                        <span>{{ $currency }}</span>
                    </div>
                </div>
            @endcan

            @can('view expenses')
                <div class="col-md-4 col-xl-2">
                    <div class="nk-finance-box">
                        <div class="nk-finance-icon bg-danger-subtle text-danger">
                            <i class="bi bi-receipt"></i>
                        </div>
                        <small>المصروفات</small>
                        <strong>{{ number_format($expensesTotal, 2) }}</strong>
                        <span>{{ $currency }}</span>
                    </div>
                </div>
            @endcan

            @can('view commissions')
                <div class="col-md-4 col-xl-2">
                    <div class="nk-finance-box">
                        <div class="nk-finance-icon bg-secondary-subtle text-secondary">
                            <i class="bi bi-percent"></i>
                        </div>
                        <small>العمولات</small>
                        <strong>{{ number_format($commissionsTotal, 2) }}</strong>
                        <span>{{ $currency }}</span>
                    </div>
                </div>
            @endcan

            @canany(['view payments', 'view expenses', 'view commissions'])
                <div class="col-md-4 col-xl-2">
                    <div class="nk-finance-box {{ $netProfit < 0 ? 'nk-finance-loss' : '' }}">
                        <div class="nk-finance-icon {{ $netProfit < 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <small>صافي الربح</small>
                        <strong>{{ number_format($netProfit, 2) }}</strong>
                        <span>{{ $currency }}</span>
                    </div>
                </div>
            @endcanany
        </div>

        @can('view payments')
            <div class="mt-4">
                <div class="d-flex justify-content-between small text-muted mb-2">
                    <span>نسبة التحصيل من قيمة العقد</span>
                    <span>{{ number_format($collectionRate, 1) }}%</span>
                </div>

                <div class="progress rounded-pill" style="height: 10px;">
                    <div class="progress-bar bg-success"
                         role="progressbar"
                         style="width: {{ $collectionRate }}%;"
                         aria-valuenow="{{ $collectionRate }}"
                         aria-valuemin="0"
                         aria-valuemax="100">
                    </div>
                </div>
            </div>
        @endcan
    </div>
@endcanany