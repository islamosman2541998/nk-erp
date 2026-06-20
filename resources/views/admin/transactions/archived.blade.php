<x-app-layout>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="nk-page-title">أرشيف المعاملات</h1>
            <p class="nk-page-subtitle">
                متابعة المعاملات المؤرشفة، أسباب الأرشفة، وإمكانية الاسترجاع عند الحاجة.
            </p>
        </div>

        @canany(['view archive', 'view transactions'])
            <a href="{{ route('admin.transactions.archived.export', request()->query()) }}"
               class="btn btn-outline-success rounded-pill">
                <i class="bi bi-file-earmark-excel"></i>
                تصدير Excel
            </a>
        @endcanany
    </div>

    <div class="nk-card mb-4">
        <form method="GET" action="{{ route('admin.transactions.archived') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">بحث</label>
                <input type="text"
                       name="search"
                       class="form-control"
                       value="{{ request('search') }}"
                       placeholder="رقم المعاملة، العميل، المشروع...">
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

            <div class="col-md-2">
                <label class="form-label">العميل</label>
                <select name="client_id" class="form-select">
                    <option value="">كل العملاء</option>
                    @foreach(\App\Models\Client::query()->orderBy('name')->get() as $client)
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
                    @foreach(\App\Models\TransactionType::query()->whereNull('parent_id')->orderBy('sort_order')->orderBy('name')->get() as $type)
                        <option value="{{ $type->id }}" @selected(request('transaction_type_id') == $type->id)>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">المسؤول</label>
                <select name="assigned_to" class="form-select">
                    <option value="">كل المسؤولين</option>
                    @foreach(\App\Models\User::query()->orderBy('name')->get() as $user)
                        <option value="{{ $user->id }}" @selected(request('assigned_to') == $user->id)>
                            {{ $user->name }}
                        </option>
                    @endforeach
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
                <a href="{{ route('admin.transactions.archived') }}"
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
                        <th>رقم المعاملة</th>
                        <th>العميل</th>
                        <th>نوع المعاملة</th>
                        <th>المشروع</th>
                        <th>الحالة</th>
                        <th>أرشف بواسطة</th>
                        <th>تاريخ الأرشفة</th>
                        <th>سبب الأرشفة</th>
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
                                {{ $transaction->project_name ?? '-' }}
                            </td>

                            <td>
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill">
                                    {{ $transaction->status }}
                                </span>
                            </td>

                            <td>
                                {{ $transaction->archivedBy?->name ?? '-' }}
                            </td>

                            <td>
                                {{ $transaction->archived_at?->format('Y-m-d H:i') ?? '-' }}
                            </td>

                            <td style="min-width: 220px;">
                                {{ $transaction->archive_notes ?? '-' }}
                            </td>

                            <td>
                                <div class="d-flex gap-2 flex-wrap flex-nowrap">
                                    <a href="{{ route('admin.transactions.show', $transaction) }}"
                                       class="btn btn-sm btn-outline-success rounded-pill">
                                        <i class="bi bi-eye"></i>
                                        عرض
                                    </a>

                                    @canany(['restore archive', 'close transactions'])
                                        <form method="POST"
                                              action="{{ route('admin.transactions.unarchive', $transaction) }}"
                                              class="js-confirm-form"
                                              data-title="استرجاع المعاملة"
                                              data-text="هل تريد إلغاء أرشفة هذه المعاملة وإعادتها للقائمة الرئيسية؟"
                                              data-icon="question"
                                              data-confirm-text="نعم، استرجاع"
                                              data-cancel-text="إلغاء"
                                              data-confirm-color="#0A3323">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-primary rounded-pill">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                                استرجاع
                                            </button>
                                        </form>
                                    @endcanany
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                لا توجد معاملات مؤرشفة حسب الفلاتر الحالية
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