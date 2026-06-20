<x-app-layout>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="nk-page-title">المعاملات</h1>
            <p class="nk-page-subtitle">إدارة ملفات المعاملات وربطها بالعملاء والمستندات.</p>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            @can('view transactions')
                <a href="{{ route('admin.transactions.export', request()->query()) }}"
                    class="btn btn-outline-success rounded-pill">
                    <i class="bi bi-file-earmark-excel"></i>
                    تصدير Excel
                </a>
            @endcan

            @can('create transactions')
                <a href="{{ route('admin.transactions.create') }}" class="nk-btn-main">
                    <i class="bi bi-plus-circle"></i>
                    إضافة معاملة
                </a>
            @endcan
        </div>
    </div>



    <div class="nk-card mb-4">
        <form method="GET" action="{{ route('admin.transactions.index') }}" class="row g-3">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                    placeholder="بحث برقم المعاملة، العميل، المشروع...">
            </div>
            <div class="col-md-2">
                <button class="btn btn-success w-100 rounded-pill">
                    بحث
                </button>
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
                        <th>المشروع</th>
                        <th>الحالة</th>
                        <th>المسؤول</th>
                        <th>تاريخ الإنشاء</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td class="fw-bold">{{ $transaction->reference_number }}</td>
                            <td>{{ $transaction->client?->name }}</td>
                            <td>{{ $transaction->transactionType?->name }}</td>
                            <td>{{ $transaction->project_name ?? '-' }}</td>
                            <td>
                                <span class="badge bg-success-subtle text-success rounded-pill">
                                    {{ $transaction->status }}
                                </span>
                            </td>
                            <td>{{ $transaction->assignedUser?->name ?? '-' }}</td>
                            <td>{{ $transaction->created_at?->format('Y-m-d') }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.transactions.show', $transaction) }}"
                                        class="btn btn-sm btn-outline-success rounded-pill">
                                        <i class="bi bi-eye"></i>
                                        عرض
                                    </a>

                                    @can('edit transactions')
                                        <a href="{{ route('admin.transactions.edit', $transaction) }}"
                                            class="btn btn-sm btn-outline-primary rounded-pill">
                                            <i class="bi bi-pencil-square"></i>
                                            تعديل
                                        </a>
                                    @endcan

                                    @can('delete transactions')
                                        <form method="POST"
                                            action="{{ route('admin.transactions.destroy', $transaction) }}"
                                            class="js-confirm-form" data-title="حذف المعاملة"
                                            data-text="سيتم حذف المعاملة. هل أنت متأكد؟" data-icon="warning"
                                            data-confirm-text="نعم، احذف" data-cancel-text="إلغاء"
                                            data-confirm-color="#c0392b">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-outline-danger rounded-pill">
                                                <i class="bi bi-trash"></i>
                                                حذف
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                لا توجد معاملات حتى الآن
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
