<x-app-layout>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="nk-page-title">أرشيف المعاملات</h1>
            <p class="nk-page-subtitle">
                المعاملات التي تم أرشفتها مع الاحتفاظ بكل بياناتها ومرفقاتها.
            </p>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.transactions.archived.export', request()->query()) }}"
                class="btn btn-outline-success rounded-pill">
                <i class="bi bi-file-earmark-excel"></i>
                تصدير Excel
            </a>

            <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline-secondary rounded-pill">
                المعاملات النشطة
            </a>
        </div>
    </div>

    <div class="nk-card mb-4">
        <form method="GET" action="{{ route('admin.transactions.archived') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label class="form-label">بحث في الأرشيف</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                        placeholder="رقم المعاملة، العميل، اسم المشروع، رقم التصريح...">
                </div>

                <div class="col-md-2">
                    <label class="form-label">الحالة</label>
                    <select name="status" class="form-select">
                        <option value="">كل الحالات</option>
                        <option value="تحت الإجراء" @selected(request('status') == 'تحت الإجراء')>تحت الإجراء</option>
                        <option value="تم صدور التصريح" @selected(request('status') == 'تم صدور التصريح')>تم صدور التصريح</option>
                        <option value="أخرى" @selected(request('status') == 'أخرى')>أخرى</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="nk-btn-main w-100">
                        بحث
                    </button>

                    <a href="{{ route('admin.transactions.archived') }}" class="btn btn-outline-secondary rounded-pill">
                        مسح
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="nk-card">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>رقم المعاملة</th>
                        <th>العميل</th>
                        <th>نوع المعاملة</th>
                        <th>الحالة</th>
                        <th>عدد المستندات</th>
                        <th>تمت الأرشفة بواسطة</th>
                        <th>تاريخ الأرشفة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($transactions as $transaction)
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
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill">
                                    {{ $transaction->status }}
                                </span>
                            </td>

                            <td>
                                {{ $transaction->documents->count() }}
                            </td>

                            <td>
                                {{ $transaction->archivedBy?->name ?? '-' }}
                            </td>

                            <td>
                                {{ $transaction->archived_at?->format('Y-m-d') }}
                            </td>

                            <td>
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="{{ route('admin.transactions.show', $transaction) }}"
                                        class="btn btn-sm btn-outline-success rounded-pill">
                                        <i class="bi bi-eye"></i>
                                        عرض
                                    </a>

                                    @can('close transactions')
                                        <form method="POST"
                                            action="{{ route('admin.transactions.unarchive', $transaction) }}"
                                            class="js-confirm-form" data-title="إلغاء الأرشفة"
                                            data-text="هل تريد إعادة هذه المعاملة إلى المعاملات النشطة؟" data-icon="info"
                                            data-confirm-text="نعم، أعدها" data-cancel-text="إلغاء"
                                            data-confirm-color="#0b5c32">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit" class="btn btn-outline-primary rounded-pill">
                                                إلغاء الأرشفة
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                لا توجد معاملات مؤرشفة حتى الآن
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $transactions->links() }}
        </div>
    </div>
</x-app-layout>
