@canany(['view payments', 'create payments', 'edit payments', 'delete payments'])
    @php
        $payments = $transaction->payments ?? collect();

        $paidTotal = $payments
            ->where('status', 'مدفوعة')
            ->sum('amount');

        $contractValue = $transaction->contract?->contract_value;
        $remainingAmount = $contractValue !== null
            ? max((float) $contractValue - (float) $paidTotal, 0)
            : null;

        $currency = $transaction->contract?->currency
            ?? app(\App\Services\SettingService::class)->get('default_currency', 'SAR');
    @endphp

    <div class="nk-card mt-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h5 class="fw-bold text-success mb-1">
                    الدفعات
                </h5>
                <p class="text-muted small mb-0">
                    إدارة دفعات المعاملة وإثباتات السداد الخاصة بكل دفعة.
                </p>
            </div>

            <span class="badge bg-success-subtle text-success rounded-pill">
                {{ $payments->count() }} دفعة
            </span>
        </div>

        @can('view payments')
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="nk-info-box">
                        <small>قيمة العقد</small>
                        <strong>
                            @if($contractValue !== null)
                                {{ number_format((float) $contractValue, 2) }} {{ $currency }}
                            @else
                                -
                            @endif
                        </strong>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="nk-info-box">
                        <small>إجمالي المدفوع</small>
                        <strong>
                            {{ number_format((float) $paidTotal, 2) }} {{ $currency }}
                        </strong>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="nk-info-box">
                        <small>المتبقي</small>
                        <strong>
                            @if($remainingAmount !== null)
                                {{ number_format((float) $remainingAmount, 2) }} {{ $currency }}
                            @else
                                -
                            @endif
                        </strong>
                    </div>
                </div>
            </div>
        @endcan

        @can('create payments')
            <div class="border rounded-4 p-3 mb-4 bg-light">
                <h6 class="fw-bold text-success mb-3">
                    إضافة دفعة جديدة
                </h6>

                <form method="POST"
                      action="{{ route('admin.transactions.payments.store', $transaction) }}"
                      enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">رقم الدفعة</label>
                            <input type="text"
                                   name="payment_number"
                                   class="form-control"
                                   value="{{ old('payment_number') }}"
                                   placeholder="مثال: الدفعة الأولى">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">
                                مبلغ الدفعة <span class="text-danger">*</span>
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

                        <div class="col-md-2">
                            <label class="form-label">تاريخ الاستحقاق</label>
                            <input type="date"
                                   name="due_date"
                                   class="form-control"
                                   value="{{ old('due_date') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">تاريخ السداد</label>
                            <input type="date"
                                   name="payment_date"
                                   class="form-control"
                                   value="{{ old('payment_date') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">طريقة السداد</label>
                            <input type="text"
                                   name="payment_method"
                                   class="form-control"
                                   value="{{ old('payment_method') }}"
                                   placeholder="تحويل / كاش / شيك">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">حالة الدفعة</label>
                            <select name="status" class="form-select" required>
                                <option value="مستحقة" @selected(old('status', 'مستحقة') === 'مستحقة')>مستحقة</option>
                                <option value="مدفوعة" @selected(old('status') === 'مدفوعة')>مدفوعة</option>
                                <option value="متأخرة" @selected(old('status') === 'متأخرة')>متأخرة</option>
                                <option value="ملغية" @selected(old('status') === 'ملغية')>ملغية</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">إثبات الدفع</label>
                            <input type="file"
                                   name="proof_file"
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
                            إضافة الدفعة
                        </button>
                    </div>
                </form>
            </div>
        @endcan

        @can('view payments')
            @if($payments->count())
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>رقم الدفعة</th>
                                <th>المبلغ</th>
                                <th>الاستحقاق</th>
                                <th>السداد</th>
                                <th>الحالة</th>
                                <th>إثبات الدفع</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td class="fw-bold">
                                        {{ $payment->payment_number ?? 'دفعة #' . $payment->id }}
                                    </td>

                                    <td>
                                        {{ number_format((float) $payment->amount, 2) }} {{ $payment->currency }}
                                    </td>

                                    <td>
                                        {{ $payment->due_date?->format('Y-m-d') ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $payment->payment_date?->format('Y-m-d') ?? '-' }}
                                    </td>

                                    <td>
                                        @php
                                            $statusClass = match($payment->status) {
                                                'مدفوعة' => 'bg-success-subtle text-success',
                                                'متأخرة' => 'bg-danger-subtle text-danger',
                                                'ملغية' => 'bg-secondary-subtle text-secondary',
                                                default => 'bg-warning-subtle text-warning',
                                            };
                                        @endphp

                                        <span class="badge {{ $statusClass }} rounded-pill">
                                            {{ $payment->status }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="d-flex gap-2 flex-wrap">
                                            @if($payment->proof_file_path)
                                                <a href="{{ asset('storage/' . $payment->proof_file_path) }}"
                                                   target="_blank"
                                                   class="btn btn-sm btn-outline-success rounded-pill">
                                                    <i class="bi bi-file-earmark-text"></i>
                                                    الملف
                                                </a>
                                            @endif

                                            @if($payment->drive_link)
                                                <a href="{{ $payment->drive_link }}"
                                                   target="_blank"
                                                   class="btn btn-sm btn-outline-primary rounded-pill">
                                                    <i class="bi bi-cloud-arrow-up"></i>
                                                    Drive
                                                </a>
                                            @endif

                                            @if(!$payment->proof_file_path && !$payment->drive_link)
                                                <span class="text-muted small">لا يوجد</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td>
                                        <div class="d-flex gap-2 flex-wrap">
                                            @can('edit payments')
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-primary rounded-pill"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#edit-payment-{{ $payment->id }}">
                                                    <i class="bi bi-pencil-square"></i>
                                                    تعديل
                                                </button>
                                            @endcan

                                            @can('delete payments')
                                                <form method="POST"
                                                      action="{{ route('admin.payments.destroy', $payment) }}"
                                                      class="js-confirm-form"
                                                      data-title="حذف الدفعة"
                                                      data-text="هل أنت متأكد من حذف هذه الدفعة؟"
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

                                @can('edit payments')
                                    <tr class="collapse" id="edit-payment-{{ $payment->id }}">
                                        <td colspan="7">
                                            <div class="border rounded-4 p-3 bg-light">
                                                <h6 class="fw-bold text-success mb-3">
                                                    تعديل الدفعة
                                                </h6>

                                                <form method="POST"
                                                      action="{{ route('admin.payments.update', $payment) }}"
                                                      enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')

                                                    <div class="row g-3">
                                                        <div class="col-md-3">
                                                            <label class="form-label">رقم الدفعة</label>
                                                            <input type="text"
                                                                   name="payment_number"
                                                                   class="form-control"
                                                                   value="{{ old('payment_number', $payment->payment_number) }}">
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="form-label">المبلغ</label>
                                                            <input type="number"
                                                                   step="0.01"
                                                                   name="amount"
                                                                   class="form-control"
                                                                   value="{{ old('amount', $payment->amount) }}"
                                                                   required>
                                                        </div>

                                                        <div class="col-md-2">
                                                            <label class="form-label">العملة</label>
                                                            <input type="text"
                                                                   name="currency"
                                                                   class="form-control"
                                                                   value="{{ old('currency', $payment->currency) }}">
                                                        </div>

                                                        <div class="col-md-2">
                                                            <label class="form-label">تاريخ الاستحقاق</label>
                                                            <input type="date"
                                                                   name="due_date"
                                                                   class="form-control"
                                                                   value="{{ old('due_date', $payment->due_date?->format('Y-m-d')) }}">
                                                        </div>

                                                        <div class="col-md-2">
                                                            <label class="form-label">تاريخ السداد</label>
                                                            <input type="date"
                                                                   name="payment_date"
                                                                   class="form-control"
                                                                   value="{{ old('payment_date', $payment->payment_date?->format('Y-m-d')) }}">
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="form-label">طريقة السداد</label>
                                                            <input type="text"
                                                                   name="payment_method"
                                                                   class="form-control"
                                                                   value="{{ old('payment_method', $payment->payment_method) }}">
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="form-label">حالة الدفعة</label>
                                                            <select name="status" class="form-select" required>
                                                                <option value="مستحقة" @selected(old('status', $payment->status) === 'مستحقة')>مستحقة</option>
                                                                <option value="مدفوعة" @selected(old('status', $payment->status) === 'مدفوعة')>مدفوعة</option>
                                                                <option value="متأخرة" @selected(old('status', $payment->status) === 'متأخرة')>متأخرة</option>
                                                                <option value="ملغية" @selected(old('status', $payment->status) === 'ملغية')>ملغية</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="form-label">إثبات الدفع</label>
                                                            <input type="file"
                                                                   name="proof_file"
                                                                   class="form-control"
                                                                   accept=".pdf,.jpg,.jpeg,.png,.webp">
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="form-label">رابط Drive</label>
                                                            <input type="url"
                                                                   name="drive_link"
                                                                   class="form-control"
                                                                   value="{{ old('drive_link', $payment->drive_link) }}">
                                                        </div>

                                                        @if($payment->proof_file_path)
                                                            <div class="col-md-3 d-flex align-items-end">
                                                                <div class="form-check">
                                                                    <input type="checkbox"
                                                                           name="clear_file"
                                                                           value="1"
                                                                           class="form-check-input"
                                                                           id="clear_payment_file_{{ $payment->id }}">

                                                                    <label class="form-check-label text-danger"
                                                                           for="clear_payment_file_{{ $payment->id }}">
                                                                        حذف إثبات الدفع الحالي
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <div class="col-12">
                                                            <label class="form-label">ملاحظات</label>
                                                            <textarea name="notes"
                                                                      class="form-control"
                                                                      rows="2">{{ old('notes', $payment->notes) }}</textarea>
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
                    لا توجد دفعات لهذه المعاملة حتى الآن
                </div>
            @endif
        @endcan
    </div>
@endcanany