@canany(['view expenses', 'create expenses', 'edit expenses', 'delete expenses'])
    @php
        $expenses = $transaction->expenses ?? collect();

        $totalExpenses = $expenses
            ->where('status', '!=', 'ملغي')
            ->sum('amount');

        $currency = $transaction->contract?->currency
            ?? app(\App\Services\SettingService::class)->get('default_currency', 'SAR');

        $expenseCategories = [
            'رسوم حكومية',
            'قياسات بيئية',
            'تقارير اختبارية',
            'انتقالات',
            'معدات',
            'عمالة',
            'مصاريف تشغيلية',
            'أخرى',
        ];
    @endphp

    <div class="nk-card mt-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h5 class="fw-bold text-success mb-1">
                    المصروفات
                </h5>
                <p class="text-muted small mb-0">
                    تسجيل ومتابعة مصروفات المعاملة وإيصالات الصرف.
                </p>
            </div>

            <span class="badge bg-danger-subtle text-danger rounded-pill">
                {{ number_format((float) $totalExpenses, 2) }} {{ $currency }}
            </span>
        </div>

        @can('view expenses')
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="nk-info-box">
                        <small>عدد المصروفات</small>
                        <strong>{{ $expenses->count() }}</strong>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="nk-info-box">
                        <small>إجمالي المصروفات</small>
                        <strong>{{ number_format((float) $totalExpenses, 2) }} {{ $currency }}</strong>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="nk-info-box">
                        <small>آخر مصروف</small>
                        <strong>
                            {{ $expenses->first()?->expense_date?->format('Y-m-d') ?? '-' }}
                        </strong>
                    </div>
                </div>
            </div>
        @endcan

        @can('create expenses')
            <div class="border rounded-4 p-3 mb-4 bg-light">
                <h6 class="fw-bold text-success mb-3">
                    إضافة مصروف جديد
                </h6>

                <form method="POST"
                      action="{{ route('admin.transactions.expenses.store', $transaction) }}"
                      enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">رقم المصروف</label>
                            <input type="text"
                                   name="expense_number"
                                   class="form-control"
                                   value="{{ old('expense_number') }}"
                                   placeholder="مثال: EXP-001">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">تصنيف المصروف</label>
                            <select name="category" class="form-select">
                                <option value="">اختر التصنيف</option>
                                @foreach($expenseCategories as $category)
                                    <option value="{{ $category }}" @selected(old('category') === $category)>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                عنوان المصروف <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="title"
                                   class="form-control"
                                   value="{{ old('title') }}"
                                   placeholder="مثال: رسوم قياسات بيئية"
                                   required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">
                                المبلغ <span class="text-danger">*</span>
                            </label>
                            <input type="number"
                                   step="0.01"
                                   name="amount"
                                   class="form-control"
                                   value="{{ old('amount') }}"
                                   required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">العملة</label>
                            <input type="text"
                                   name="currency"
                                   class="form-control"
                                   value="{{ old('currency', $currency) }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">تاريخ المصروف</label>
                            <input type="date"
                                   name="expense_date"
                                   class="form-control"
                                   value="{{ old('expense_date') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">تم الدفع إلى</label>
                            <input type="text"
                                   name="paid_to"
                                   class="form-control"
                                   value="{{ old('paid_to') }}"
                                   placeholder="اسم الجهة / المورد">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">طريقة الدفع</label>
                            <input type="text"
                                   name="payment_method"
                                   class="form-control"
                                   value="{{ old('payment_method') }}"
                                   placeholder="تحويل / كاش / شيك">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">حالة المصروف</label>
                            <select name="status" class="form-select" required>
                                <option value="مدفوع" @selected(old('status', 'مدفوع') === 'مدفوع')>مدفوع</option>
                                <option value="مستحق" @selected(old('status') === 'مستحق')>مستحق</option>
                                <option value="ملغي" @selected(old('status') === 'ملغي')>ملغي</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">إيصال المصروف</label>
                            <input type="file"
                                   name="receipt_file"
                                   class="form-control"
                                   accept=".pdf,.jpg,.jpeg,.png,.webp">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">رابط Drive</label>
                            <input type="url"
                                   name="drive_link"
                                   class="form-control"
                                   value="{{ old('drive_link') }}"
                                   placeholder="https://drive.google.com/...">
                        </div>

                        <div class="col-12">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes"
                                      class="form-control"
                                      rows="2">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="nk-btn-main">
                            <i class="bi bi-plus-circle"></i>
                            إضافة المصروف
                        </button>
                    </div>
                </form>
            </div>
        @endcan

        @can('view expenses')
            @if($expenses->count())
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>المصروف</th>
                                <th>التصنيف</th>
                                <th>المبلغ</th>
                                <th>التاريخ</th>
                                <th>الحالة</th>
                                <th>الإيصال</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($expenses as $expense)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $expense->title }}</div>
                                        <div class="text-muted small">
                                            {{ $expense->expense_number ?? 'مصروف #' . $expense->id }}
                                        </div>
                                    </td>

                                    <td>{{ $expense->category ?? '-' }}</td>

                                    <td>
                                        {{ number_format((float) $expense->amount, 2) }} {{ $expense->currency }}
                                    </td>

                                    <td>
                                        {{ $expense->expense_date?->format('Y-m-d') ?? '-' }}
                                    </td>

                                    <td>
                                        @php
                                            $statusClass = match($expense->status) {
                                                'مدفوع' => 'bg-success-subtle text-success',
                                                'مستحق' => 'bg-warning-subtle text-warning',
                                                'ملغي' => 'bg-secondary-subtle text-secondary',
                                                default => 'bg-light text-muted',
                                            };
                                        @endphp

                                        <span class="badge {{ $statusClass }} rounded-pill">
                                            {{ $expense->status }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="d-flex gap-2 flex-wrap">
                                            @if($expense->receipt_file_path)
                                                <a href="{{ asset('storage/' . $expense->receipt_file_path) }}"
                                                   target="_blank"
                                                   class="btn btn-sm btn-outline-success rounded-pill">
                                                    <i class="bi bi-file-earmark-text"></i>
                                                    الإيصال
                                                </a>
                                            @endif

                                            @if($expense->drive_link)
                                                <a href="{{ $expense->drive_link }}"
                                                   target="_blank"
                                                   class="btn btn-sm btn-outline-primary rounded-pill">
                                                    <i class="bi bi-cloud-arrow-up"></i>
                                                    Drive
                                                </a>
                                            @endif

                                            @if(!$expense->receipt_file_path && !$expense->drive_link)
                                                <span class="text-muted small">لا يوجد</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td>
                                        <div class="d-flex gap-2 flex-wrap">
                                            @can('edit expenses')
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-primary rounded-pill"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#edit-expense-{{ $expense->id }}">
                                                    <i class="bi bi-pencil-square"></i>
                                                    تعديل
                                                </button>
                                            @endcan

                                            @can('delete expenses')
                                                <form method="POST"
                                                      action="{{ route('admin.expenses.destroy', $expense) }}"
                                                      class="js-confirm-form"
                                                      data-title="حذف المصروف"
                                                      data-text="هل أنت متأكد من حذف هذا المصروف؟"
                                                      data-icon="warning"
                                                      data-confirm-text="نعم، احذف"
                                                      data-cancel-text="إلغاء"
                                                      data-confirm-color="#c0392b">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-danger rounded-pill">
                                                        <i class="bi bi-trash"></i>
                                                        حذف
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>

                                @can('edit expenses')
                                    <tr class="collapse" id="edit-expense-{{ $expense->id }}">
                                        <td colspan="7">
                                            <div class="border rounded-4 p-3 bg-light">
                                                <h6 class="fw-bold text-success mb-3">
                                                    تعديل المصروف
                                                </h6>

                                                <form method="POST"
                                                      action="{{ route('admin.expenses.update', $expense) }}"
                                                      enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')

                                                    <div class="row g-3">
                                                        <div class="col-md-3">
                                                            <label class="form-label">رقم المصروف</label>
                                                            <input type="text"
                                                                   name="expense_number"
                                                                   class="form-control"
                                                                   value="{{ old('expense_number', $expense->expense_number) }}">
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="form-label">تصنيف المصروف</label>
                                                            <select name="category" class="form-select">
                                                                <option value="">اختر التصنيف</option>
                                                                @foreach($expenseCategories as $category)
                                                                    <option value="{{ $category }}" @selected(old('category', $expense->category) === $category)>
                                                                        {{ $category }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-label">عنوان المصروف</label>
                                                            <input type="text"
                                                                   name="title"
                                                                   class="form-control"
                                                                   value="{{ old('title', $expense->title) }}"
                                                                   required>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="form-label">المبلغ</label>
                                                            <input type="number"
                                                                   step="0.01"
                                                                   name="amount"
                                                                   class="form-control"
                                                                   value="{{ old('amount', $expense->amount) }}"
                                                                   required>
                                                        </div>

                                                        <div class="col-md-2">
                                                            <label class="form-label">العملة</label>
                                                            <input type="text"
                                                                   name="currency"
                                                                   class="form-control"
                                                                   value="{{ old('currency', $expense->currency) }}">
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="form-label">تاريخ المصروف</label>
                                                            <input type="date"
                                                                   name="expense_date"
                                                                   class="form-control"
                                                                   value="{{ old('expense_date', $expense->expense_date?->format('Y-m-d')) }}">
                                                        </div>

                                                        <div class="col-md-4">
                                                            <label class="form-label">تم الدفع إلى</label>
                                                            <input type="text"
                                                                   name="paid_to"
                                                                   class="form-control"
                                                                   value="{{ old('paid_to', $expense->paid_to) }}">
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="form-label">طريقة الدفع</label>
                                                            <input type="text"
                                                                   name="payment_method"
                                                                   class="form-control"
                                                                   value="{{ old('payment_method', $expense->payment_method) }}">
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="form-label">حالة المصروف</label>
                                                            <select name="status" class="form-select" required>
                                                                <option value="مدفوع" @selected(old('status', $expense->status) === 'مدفوع')>مدفوع</option>
                                                                <option value="مستحق" @selected(old('status', $expense->status) === 'مستحق')>مستحق</option>
                                                                <option value="ملغي" @selected(old('status', $expense->status) === 'ملغي')>ملغي</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="form-label">إيصال المصروف</label>
                                                            <input type="file"
                                                                   name="receipt_file"
                                                                   class="form-control"
                                                                   accept=".pdf,.jpg,.jpeg,.png,.webp">
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="form-label">رابط Drive</label>
                                                            <input type="url"
                                                                   name="drive_link"
                                                                   class="form-control"
                                                                   value="{{ old('drive_link', $expense->drive_link) }}">
                                                        </div>

                                                        @if($expense->receipt_file_path)
                                                            <div class="col-md-3 d-flex align-items-end">
                                                                <div class="form-check">
                                                                    <input type="checkbox"
                                                                           name="clear_file"
                                                                           value="1"
                                                                           class="form-check-input"
                                                                           id="clear_expense_file_{{ $expense->id }}">

                                                                    <label class="form-check-label text-danger"
                                                                           for="clear_expense_file_{{ $expense->id }}">
                                                                        حذف الإيصال الحالي
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <div class="col-12">
                                                            <label class="form-label">ملاحظات</label>
                                                            <textarea name="notes"
                                                                      class="form-control"
                                                                      rows="2">{{ old('notes', $expense->notes) }}</textarea>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex justify-content-end mt-3">
                                                        <button type="submit" class="nk-btn-main">
                                                            <i class="bi bi-save"></i>
                                                            حفظ التعديل
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endcan
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-4">
                    لا توجد مصروفات لهذه المعاملة حتى الآن
                </div>
            @endif
        @endcan
    </div>
@endcanany