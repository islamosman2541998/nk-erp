<x-app-layout>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="nk-page-title">لوحة التحكم</h1>
            <p class="nk-page-subtitle">
                نظرة عامة على المعاملات، العملاء، المستندات، والتصاريح.
            </p>
        </div>

        @can('create transactions')
            <a href="{{ route('admin.transactions.create') }}" class="nk-btn-main">
                <i class="bi bi-plus-circle"></i>
                إضافة معاملة
            </a>
        @endcan
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="nk-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="nk-stat-label">إجمالي المعاملات</div>
                        <div class="nk-stat-number">{{ $transactionsCount }}</div>
                    </div>

                    <div class="nk-stat-icon">
                        <i class="bi bi-folder2-open"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="nk-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="nk-stat-label">تحت الإجراء</div>
                        <div class="nk-stat-number">{{ $inProgressTransactionsCount }}</div>
                    </div>

                    <div class="nk-stat-icon">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="nk-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="nk-stat-label">المعاملات المكتملة</div>
                        <div class="nk-stat-number">{{ $completedTransactionsCount }}</div>
                    </div>

                    <div class="nk-stat-icon">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="nk-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="nk-stat-label">مستندات ناقصة</div>
                        <div class="nk-stat-number">{{ $missingDocumentsCount }}</div>
                    </div>

                    <div class="nk-stat-icon">
                        <i class="bi bi-paperclip"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="nk-card h-100">
                <div class="mb-3">
                    <h5 class="fw-bold text-success mb-1">المعاملات خلال آخر 6 شهور</h5>
                    <p class="text-muted small mb-0">متابعة حركة إنشاء المعاملات شهريًا.</p>
                </div>

                <div id="monthlyTransactionsChart" class="nk-chart"></div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="nk-card h-100">
                <div class="mb-3">
                    <h5 class="fw-bold text-success mb-1">توزيع الحالات</h5>
                    <p class="text-muted small mb-0">نسبة المعاملات حسب الحالة الحالية.</p>
                </div>

                <div id="statusChart" class="nk-chart"></div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="nk-card h-100">
                <div class="mb-3">
                    <h5 class="fw-bold text-success mb-1">أنواع المعاملات الأكثر استخدامًا</h5>
                    <p class="text-muted small mb-0">أعلى أنواع المعاملات من حيث العدد.</p>
                </div>

                <div id="transactionTypeChart" class="nk-chart"></div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="nk-card h-100">
                <div class="mb-3">
                    <h5 class="fw-bold text-success mb-1">حالة المستندات</h5>
                    <p class="text-muted small mb-0">توزيع المستندات حسب حالة المراجعة والرفع.</p>
                </div>

                <div id="documentsChart" class="nk-chart"></div>
            </div>
        </div>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="nk-card h-100">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <h5 class="fw-bold text-success mb-1">آخر المعاملات</h5>
                        <p class="text-muted mb-0 small">أحدث المعاملات المسجلة داخل النظام.</p>
                    </div>

                    <a href="{{ route('admin.transactions.index') }}"
                        class="btn btn-sm btn-outline-success rounded-pill">
                        عرض الكل
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>رقم المعاملة</th>
                                <th>العميل</th>
                                <th>نوع المعاملة</th>
                                <th>الحالة</th>
                                <th>المسؤول</th>
                                <th>إجراء</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($latestTransactions as $transaction)
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
                                        <span class="badge bg-success-subtle text-success rounded-pill">
                                            {{ $transaction->status }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $transaction->assignedUser?->name ?? '-' }}
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
                                    <td colspan="6" class="text-center text-muted py-4">
                                        لا توجد معاملات حتى الآن.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="nk-card h-100">
                <h5 class="fw-bold text-success mb-3">ملخص سريع</h5>

                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted">عدد العملاء</span>
                    <strong>{{ $clientsCount }}</strong>
                </div>

                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted">إجمالي المعاملات</span>
                    <strong>{{ $transactionsCount }}</strong>
                </div>

                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted">تحت الإجراء</span>
                    <strong>{{ $inProgressTransactionsCount }}</strong>
                </div>

                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted">مستندات ناقصة</span>
                    <strong>{{ $missingDocumentsCount }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="nk-card h-100">
                <h5 class="fw-bold text-success mb-3">تصاريح قرب الانتهاء</h5>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>المعاملة</th>
                                <th>العميل</th>
                                <th>تاريخ الانتهاء</th>
                                <th>إجراء</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($expiringPermits as $transaction)
                                <tr>
                                    <td class="fw-bold">
                                        {{ $transaction->reference_number }}
                                    </td>

                                    <td>
                                        {{ $transaction->client?->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $transaction->permit_expires_at?->format('Y-m-d') }}
                                    </td>

                                    <td>
                                        <a href="{{ route('admin.transactions.show', $transaction) }}"
                                            class="btn btn-sm btn-outline-success rounded-pill">
                                            عرض
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        لا توجد تصاريح قرب الانتهاء خلال 60 يوم.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="nk-card h-100">
                <h5 class="fw-bold text-success mb-3">معاملات متأخرة</h5>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>المعاملة</th>
                                <th>العميل</th>
                                <th>تاريخ التسليم</th>
                                <th>المسؤول</th>
                                <th>إجراء</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($lateTransactions as $transaction)
                                <tr>
                                    <td class="fw-bold">
                                        {{ $transaction->reference_number }}
                                    </td>

                                    <td>
                                        {{ $transaction->client?->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $transaction->expected_delivery_at?->format('Y-m-d') }}
                                    </td>

                                    <td>
                                        {{ $transaction->assignedUser?->name ?? '-' }}
                                    </td>

                                    <td>
                                        <a href="{{ route('admin.transactions.show', $transaction) }}"
                                            class="btn btn-sm btn-outline-danger rounded-pill">
                                            عرض
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        لا توجد معاملات متأخرة.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rootStyles = getComputedStyle(document.documentElement);

        const nkGreen = rootStyles.getPropertyValue('--nk-green').trim() || '#073f22';
        const nkGold = rootStyles.getPropertyValue('--nk-gold').trim() || '#c89b3c';
        const nkText = rootStyles.getPropertyValue('--nk-text').trim() || '#111827';
        const nkMuted = rootStyles.getPropertyValue('--nk-muted').trim() || '#6b7280';
        const nkBorder = rootStyles.getPropertyValue('--nk-border').trim() || '#e5e7eb';

        const monthlyTransactions = @json($monthlyTransactionsChartData);
        const statusData = @json($statusChartData);
        const transactionTypes = @json($transactionTypeChartData);
        const documentsData = @json($documentsChartData);

        const baseOptions = {
            chart: {
                fontFamily: 'IBM Plex Sans Arabic, sans-serif',
                toolbar: {
                    show: false
                }
            },
            dataLabels: {
                enabled: false
            },
            grid: {
                borderColor: nkBorder,
                strokeDashArray: 4
            },
            legend: {
                position: 'bottom',
                labels: {
                    colors: nkMuted
                }
            },
            tooltip: {
                theme: 'light'
            }
        };

        if (document.querySelector('#monthlyTransactionsChart')) {
            new ApexCharts(document.querySelector('#monthlyTransactionsChart'), {
                ...baseOptions,
                chart: {
                    ...baseOptions.chart,
                    type: 'area',
                    height: 320
                },
                series: [{
                    name: 'عدد المعاملات',
                    data: monthlyTransactions.map(item => item.value)
                }],
                xaxis: {
                    categories: monthlyTransactions.map(item => item.label),
                    labels: {
                        style: {
                            colors: nkMuted
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: nkMuted
                        }
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.25,
                        opacityTo: 0.04,
                        stops: [0, 90, 100]
                    }
                },
                colors: [nkGreen]
            }).render();
        }

        if (document.querySelector('#statusChart')) {
            new ApexCharts(document.querySelector('#statusChart'), {
                ...baseOptions,
                chart: {
                    ...baseOptions.chart,
                    type: 'donut',
                    height: 320
                },
                series: statusData.map(item => item.value),
                labels: statusData.map(item => item.label),
                stroke: {
                    width: 0
                },
                colors: [nkGreen, nkGold, '#105666', '#b42318', '#64748b']
            }).render();
        }

        if (document.querySelector('#transactionTypeChart')) {
            new ApexCharts(document.querySelector('#transactionTypeChart'), {
                ...baseOptions,
                chart: {
                    ...baseOptions.chart,
                    type: 'bar',
                    height: 330
                },
                series: [{
                    name: 'عدد المعاملات',
                    data: transactionTypes.map(item => item.value)
                }],
                xaxis: {
                    categories: transactionTypes.map(item => item.label),
                    labels: {
                        style: {
                            colors: nkMuted
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: nkMuted
                        }
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 8,
                        columnWidth: '42%'
                    }
                },
                colors: [nkGreen]
            }).render();
        }

        if (document.querySelector('#documentsChart')) {
            new ApexCharts(document.querySelector('#documentsChart'), {
                ...baseOptions,
                chart: {
                    ...baseOptions.chart,
                    type: 'bar',
                    height: 330
                },
                series: [{
                    name: 'عدد المستندات',
                    data: documentsData.map(item => item.value)
                }],
                xaxis: {
                    categories: documentsData.map(item => item.label),
                    labels: {
                        style: {
                            colors: nkMuted
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: nkMuted
                        }
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        borderRadius: 8
                    }
                },
                colors: ['#105666']
            }).render();
        }
    });
</script>
@endpush
</x-app-layout>
