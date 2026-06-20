@canany(['view commissions', 'create commissions', 'edit commissions', 'delete commissions'])
    @php
        $commissions = $transaction->commissions ?? collect();

        $totalCommissions = $commissions
            ->where('status', '!=', 'ملغية')
            ->sum('calculated_amount');

        $paidCommissions = $commissions
            ->where('status', 'مدفوعة')
            ->sum('calculated_amount');

        $dueCommissions = $commissions
            ->where('status', 'مستحقة')
            ->sum('calculated_amount');

        $currency = $transaction->contract?->currency
            ?? app(\App\Services\SettingService::class)->get('default_currency', 'SAR');
    @endphp

    <div class="nk-card mt-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h5 class="fw-bold text-success mb-1">
                    العمولات
                </h5>
                <p class="text-muted small mb-0">
                    إدارة العمولات الداخلية والخارجية المرتبطة بالمعاملة.
                </p>
            </div>

            <span class="badge bg-success-subtle text-success rounded-pill">
                {{ number_format((float) $totalCommissions, 2) }} {{ $currency }}
            </span>
        </div>

        @can('view commissions')
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="nk-info-box">
                        <small>عدد العمولات</small>
                        <strong>{{ $commissions->count() }}</strong>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="nk-info-box">
                        <small>إجمالي العمولات</small>
                        <strong>{{ number_format((float) $totalCommissions, 2) }} {{ $currency }}</strong>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="nk-info-box">
                        <small>عمولات مدفوعة</small>
                        <strong>{{ number_format((float) $paidCommissions, 2) }} {{ $currency }}</strong>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="nk-info-box">
                        <small>عمولات مستحقة</small>
                        <strong>{{ number_format((float) $dueCommissions, 2) }} {{ $currency }}</strong>
                    </div>
                </div>
            </div>
        @endcan

        @can('create commissions')
            <div class="border rounded-4 p-3 mb-4 bg-light">
                <h6 class="fw-bold text-success mb-3">
                    إضافة عمولة جديدة
                </h6>

                <form method="POST"
                      action="{{ route('admin.transactions.commissions.store', $transaction) }}"
                      enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">رقم العمولة</label>
                            <input type="text"
                                   name="commission_number"
                                   class="form-control"
                                   value="{{ old('commission_number') }}"
                                   placeholder="مثال: COM-001">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">نوع العمولة</label>
                            <select name="commission_category" class="form-select" required>
                                <option value="داخلية" @selected(old('commission_category', 'داخلية') === 'داخلية')>داخلية</option>
                                <option value="خارجية" @selected(old('commission_category') === 'خارجية')>خارجية</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">موظف داخلي</label>
                            <select name="recipient_user_id" class="form-select">
                                <option value="">اختر الموظف</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected(old('recipient_user_id') == $user->id)>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">اسم المستفيد الخارجي</label>
                            <input type="text"
                                   name="recipient_name"
                                   class="form-control"
                                   value="{{ old('recipient_name') }}"
                                   placeholder="اسم الشخص / الجهة">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">هاتف المستفيد الخارجي</label>
                            <input type="text"
                                   name="recipient_phone"
                                   class="form-control"
                                   value="{{ old('recipient_phone') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">بريد المستفيد الخارجي</label>
                            <input type="email"
                                   name="recipient_email"
                                   class="form-control"
                                   value="{{ old('recipient_email') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">طريقة الحساب</label>
                            <select name="calculation_type" class="form-select" required>
                                <option value="نسبة" @selected(old('calculation_type', 'نسبة') === 'نسبة')>نسبة</option>
                                <option value="مبلغ ثابت" @selected(old('calculation_type') === 'مبلغ ثابت')>مبلغ ثابت</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">أساس الحساب</label>
                            <select name="base_type" class="form-select" required>
                                <option value="قيمة العقد" @selected(old('base_type', 'قيمة العقد') === 'قيمة العقد')>قيمة العقد</option>
                                <option value="إجمالي المدفوع" @selected(old('base_type') === 'إجمالي المدفوع')>إجمالي المدفوع</option>
                                <option value="صافي الربح" @selected(old('base_type') === 'صافي الربح')>صافي الربح</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">النسبة %</label>
                            <input type="number"
                                   step="0.01"
                                   name="percentage"
                                   class="form-control"
                                   value="{{ old('percentage') }}"
                                   placeholder="مثال: 5">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">المبلغ الثابت</label>
                            <input type="number"
                                   step="0.01"
                                   name="fixed_amount"
                                   class="form-control"
                                   value="{{ old('fixed_amount') }}">
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
                            <label class="form-label">حالة العمولة</label>
                            <select name="status" class="form-select" required>
                                <option value="مستحقة" @selected(old('status', 'مستحقة') === 'مستحقة')>مستحقة</option>
                                <option value="مدفوعة" @selected(old('status') === 'مدفوعة')>مدفوعة</option>
                                <option value="ملغية" @selected(old('status') === 'ملغية')>ملغية</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">إثبات سداد العمولة</label>
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

                    <div class="alert alert-info rounded-4 mt-3 mb-0">
                        يتم حساب مبلغ العمولة تلقائيًا بعد الحفظ بناءً على طريقة الحساب وأساس الحساب.
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="nk-btn-main">
                            <i class="bi bi-plus-circle"></i>
                            إضافة العمولة
                        </button>
                    </div>
                </form>
            </div>
        @endcan

        @can('view commissions')
            @if($commissions->count())
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>العمولة</th>
                                <th>المستفيد</th>
                                <th>طريقة الحساب</th>
                                <th>المبلغ المحسوب</th>
                                <th>الحالة</th>
                                <th>الإثبات</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($commissions as $commission)
                                <tr>
                                    <td>
                                        <div class="fw-bold">
                                            {{ $commission->commission_number ?? 'عمولة #' . $commission->id }}
                                        </div>

                                        <div class="text-muted small">
                                            {{ $commission->commission_category }}
                                        </div>
                                    </td>

                                    <td>
                                        @if($commission->commission_category === 'داخلية')
                                            {{ $commission->recipientUser?->name ?? '-' }}
                                        @else
                                            <div class="fw-bold">{{ $commission->recipient_name ?? '-' }}</div>
                                            <div class="text-muted small">
                                                {{ $commission->recipient_phone ?? '' }}
                                            </div>
                                        @endif
                                    </td>

                                    <td>
                                        <div>{{ $commission->calculation_type }}</div>
                                        <div class="text-muted small">
                                            {{ $commission->base_type }}
                                            @if($commission->calculation_type === 'نسبة')
                                                - {{ $commission->percentage }}%
                                            @else
                                                - {{ number_format((float) $commission->fixed_amount, 2) }} {{ $commission->currency }}
                                            @endif
                                        </div>
                                    </td>

                                    <td class="fw-bold">
                                        {{ number_format((float) $commission->calculated_amount, 2) }}
                                        {{ $commission->currency }}
                                    </td>

                                    <td>
                                        @php
                                            $statusClass = match($commission->status) {
                                                'مدفوعة' => 'bg-success-subtle text-success',
                                                'ملغية' => 'bg-secondary-subtle text-secondary',
                                                default => 'bg-warning-subtle text-warning',
                                            };
                                        @endphp

                                        <span class="badge {{ $statusClass }} rounded-pill">
                                            {{ $commission->status }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="d-flex gap-2 flex-wrap">
                                            @if($commission->proof_file_path)
                                                <a href="{{ asset('storage/' . $commission->proof_file_path) }}"
                                                   target="_blank"
                                                   class="btn btn-sm btn-outline-success rounded-pill">
                                                    <i class="bi bi-file-earmark-text"></i>
                                                    الملف
                                                </a>
                                            @endif

                                            @if($commission->drive_link)
                                                <a href="{{ $commission->drive_link }}"
                                                   target="_blank"
                                                   class="btn btn-sm btn-outline-primary rounded-pill">
                                                    <i class="bi bi-cloud-arrow-up"></i>
                                                    Drive
                                                </a>
                                            @endif

                                            @if(!$commission->proof_file_path && !$commission->drive_link)
                                                <span class="text-muted small">لا يوجد</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td>
                                        <div class="d-flex gap-2 flex-wrap">
                                            @can('edit commissions')
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-primary rounded-pill"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#edit-commission-{{ $commission->id }}">
                                                    <i class="bi bi-pencil-square"></i>
                                                    تعديل
                                                </button>
                                            @endcan

                                            @can('delete commissions')
                                                <form method="POST"
                                                      action="{{ route('admin.commissions.destroy', $commission) }}"
                                                      class="js-confirm-form"
                                                      data-title="حذف العمولة"
                                                      data-text="هل أنت متأكد من حذف هذه العمولة؟"
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

                                @can('edit commissions')
                                    <tr class="collapse" id="edit-commission-{{ $commission->id }}">
                                        <td colspan="7">
                                            <div class="border rounded-4 p-3 bg-light">
                                                <h6 class="fw-bold text-success mb-3">
                                                    تعديل العمولة
                                                </h6>

                                                <form method="POST"
                                                      action="{{ route('admin.commissions.update', $commission) }}"
                                                      enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')

                                                    <div class="row g-3">
                                                        <div class="col-md-3">
                                                            <label class="form-label">رقم العمولة</label>
                                                            <input type="text"
                                                                   name="commission_number"
                                                                   class="form-control"
                                                                   value="{{ old('commission_number', $commission->commission_number) }}">
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="form-label">نوع العمولة</label>
                                                            <select name="commission_category" class="form-select" required>
                                                                <option value="داخلية" @selected(old('commission_category', $commission->commission_category) === 'داخلية')>داخلية</option>
                                                                <option value="خارجية" @selected(old('commission_category', $commission->commission_category) === 'خارجية')>خارجية</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="form-label">موظف داخلي</label>
                                                            <select name="recipient_user_id" class="form-select">
                                                                <option value="">اختر الموظف</option>
                                                                @foreach($users as $user)
                                                                    <option value="{{ $user->id }}" @selected(old('recipient_user_id', $commission->recipient_user_id) == $user->id)>
                                                                        {{ $user->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="form-label">اسم المستفيد الخارجي</label>
                                                            <input type="text"
                                                                   name="recipient_name"
                                                                   class="form-control"
                                                                   value="{{ old('recipient_name', $commission->recipient_name) }}">
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="form-label">هاتف المستفيد الخارجي</label>
                                                            <input type="text"
                                                                   name="recipient_phone"
                                                                   class="form-control"
                                                                   value="{{ old('recipient_phone', $commission->recipient_phone) }}">
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="form-label">بريد المستفيد الخارجي</label>
                                                            <input type="email"
                                                                   name="recipient_email"
                                                                   class="form-control"
                                                                   value="{{ old('recipient_email', $commission->recipient_email) }}">
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="form-label">طريقة الحساب</label>
                                                            <select name="calculation_type" class="form-select" required>
                                                                <option value="نسبة" @selected(old('calculation_type', $commission->calculation_type) === 'نسبة')>نسبة</option>
                                                                <option value="مبلغ ثابت" @selected(old('calculation_type', $commission->calculation_type) === 'مبلغ ثابت')>مبلغ ثابت</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="form-label">أساس الحساب</label>
                                                            <select name="base_type" class="form-select" required>
                                                                <option value="قيمة العقد" @selected(old('base_type', $commission->base_type) === 'قيمة العقد')>قيمة العقد</option>
                                                                <option value="إجمالي المدفوع" @selected(old('base_type', $commission->base_type) === 'إجمالي المدفوع')>إجمالي المدفوع</option>
                                                                <option value="صافي الربح" @selected(old('base_type', $commission->base_type) === 'صافي الربح')>صافي الربح</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="form-label">النسبة %</label>
                                                            <input type="number"
                                                                   step="0.01"
                                                                   name="percentage"
                                                                   class="form-control"
                                                                   value="{{ old('percentage', $commission->percentage) }}">
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="form-label">المبلغ الثابت</label>
                                                            <input type="number"
                                                                   step="0.01"
                                                                   name="fixed_amount"
                                                                   class="form-control"
                                                                   value="{{ old('fixed_amount', $commission->fixed_amount) }}">
                                                        </div>

                                                        <div class="col-md-2">
                                                            <label class="form-label">العملة</label>
                                                            <input type="text"
                                                                   name="currency"
                                                                   class="form-control"
                                                                   value="{{ old('currency', $commission->currency) }}">
                                                        </div>

                                                        <div class="col-md-2">
                                                            <label class="form-label">تاريخ الاستحقاق</label>
                                                            <input type="date"
                                                                   name="due_date"
                                                                   class="form-control"
                                                                   value="{{ old('due_date', $commission->due_date?->format('Y-m-d')) }}">
                                                        </div>

                                                        <div class="col-md-2">
                                                            <label class="form-label">تاريخ السداد</label>
                                                            <input type="date"
                                                                   name="payment_date"
                                                                   class="form-control"
                                                                   value="{{ old('payment_date', $commission->payment_date?->format('Y-m-d')) }}">
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="form-label">حالة العمولة</label>
                                                            <select name="status" class="form-select" required>
                                                                <option value="مستحقة" @selected(old('status', $commission->status) === 'مستحقة')>مستحقة</option>
                                                                <option value="مدفوعة" @selected(old('status', $commission->status) === 'مدفوعة')>مدفوعة</option>
                                                                <option value="ملغية" @selected(old('status', $commission->status) === 'ملغية')>ملغية</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="form-label">إثبات السداد</label>
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
                                                                   value="{{ old('drive_link', $commission->drive_link) }}">
                                                        </div>

                                                        @if($commission->proof_file_path)
                                                            <div class="col-md-3 d-flex align-items-end">
                                                                <div class="form-check">
                                                                    <input type="checkbox"
                                                                           name="clear_file"
                                                                           value="1"
                                                                           class="form-check-input"
                                                                           id="clear_commission_file_{{ $commission->id }}">

                                                                    <label class="form-check-label text-danger"
                                                                           for="clear_commission_file_{{ $commission->id }}">
                                                                        حذف إثبات السداد الحالي
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <div class="col-12">
                                                            <label class="form-label">ملاحظات</label>
                                                            <textarea name="notes"
                                                                      class="form-control"
                                                                      rows="2">{{ old('notes', $commission->notes) }}</textarea>
                                                        </div>
                                                    </div>

                                                    <div class="alert alert-info rounded-4 mt-3 mb-0">
                                                        سيتم إعادة حساب مبلغ العمولة تلقائيًا بعد حفظ التعديل.
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
                    لا توجد عمولات لهذه المعاملة حتى الآن
                </div>
            @endif
        @endcan
    </div>
@endcanany