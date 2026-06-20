<x-app-layout>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="nk-page-title">{{ $client->name }}</h1>
            <p class="nk-page-subtitle">ملف العميل وبياناته الأساسية.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary rounded-pill">
                رجوع
            </a>

            @can('edit clients')
                <a href="{{ route('admin.clients.edit', $client) }}" class="nk-btn-main">
                    <i class="bi bi-pencil-square"></i>
                    تعديل
                </a>
            @endcan
            @can('create transactions')
                <a href="{{ route('admin.transactions.create', ['client_id' => $client->id]) }}"
                    class="btn btn-outline-success rounded-pill">
                    <i class="bi bi-plus-circle"></i>
                    إنشاء معاملة
                </a>
            @endcan
        </div>
    </div>



    <div class="row g-4">
        <div class="col-lg-8">
            <div class="nk-card">
                <h5 class="fw-bold text-success mb-4">بيانات العميل</h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <small class="text-muted">اسم العميل</small>
                        <div class="fw-bold">{{ $client->name }}</div>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted">اسم المنشأة</small>
                        <div class="fw-bold">{{ $client->facility_name ?? '-' }}</div>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted">رقم السجل التجاري</small>
                        <div class="fw-bold">{{ $client->commercial_registration_number ?? '-' }}</div>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted">الرقم الضريبي</small>
                        <div class="fw-bold">{{ $client->tax_number ?? '-' }}</div>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted">رقم الجوال</small>
                        <div class="fw-bold">{{ $client->phone ?? '-' }}</div>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted">البريد الإلكتروني</small>
                        <div class="fw-bold">{{ $client->email ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">اسم شخص التواصل</small>
                        <div class="fw-bold">{{ $client->contact_person_name ?? '-' }}</div>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted">رقم شخص التواصل</small>
                        <div class="fw-bold">{{ $client->contact_person_phone ?? '-' }}</div>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted">بريد شخص التواصل</small>
                        <div class="fw-bold">{{ $client->contact_person_email ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">المدينة</small>
                        <div class="fw-bold">{{ $client->city ?? '-' }}</div>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted">المنطقة</small>
                        <div class="fw-bold">{{ $client->region ?? '-' }}</div>
                    </div>

                    <div class="col-12">
                        <small class="text-muted">العنوان</small>
                        <div class="fw-bold">{{ $client->address ?? '-' }}</div>
                    </div>

                    <div class="col-12">
                        <small class="text-muted">ملاحظات</small>
                        <div class="fw-bold">{{ $client->notes ?? '-' }}</div>
                    </div>
                </div>
            </div>
            <div class="nk-card mt-4">
                <h5 class="fw-bold text-success mb-4">آخر معاملات العميل</h5>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>رقم المعاملة</th>
                                <th>نوع المعاملة</th>
                                <th>الحالة</th>
                                <th>تاريخ الإنشاء</th>
                                <th>إجراء</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($latestTransactions as $transaction)
                                <tr>
                                    <td class="fw-bold">{{ $transaction->reference_number }}</td>
                                    <td>{{ $transaction->transactionType?->name ?? '-' }}</td>
                                    <td>{{ $transaction->status }}</td>
                                    <td>{{ $transaction->created_at?->format('Y-m-d') }}</td>
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
                                    <td colspan="5" class="text-center text-muted py-4">
                                        لا توجد معاملات لهذا العميل حتى الآن
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="nk-card">
                <h5 class="fw-bold text-success mb-3">ملخص العميل</h5>

                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted">عدد المعاملات</span>
                    <strong>{{ $client->transactions_count }}</strong>
                </div>

                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted">تاريخ الإضافة</span>
                    <strong>{{ $client->created_at?->format('Y-m-d') }}</strong>
                </div>

                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted">آخر تحديث</span>
                    <strong>{{ $client->updated_at?->format('Y-m-d') }}</strong>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
