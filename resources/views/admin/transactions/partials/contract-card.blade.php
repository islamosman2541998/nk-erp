@canany(['view contracts', 'create contracts', 'edit contracts', 'delete contracts'])
    <div class="nk-card mt-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h5 class="fw-bold text-success mb-1">
                    العقد
                </h5>
                <p class="text-muted small mb-0">
                    إدارة عقد المعاملة وملف العقد ورابط Drive.
                </p>
            </div>

            @if($transaction->contract)
                <span class="badge bg-success-subtle text-success rounded-pill">
                    {{ $transaction->contract->status }}
                </span>
            @else
                <span class="badge bg-secondary-subtle text-secondary rounded-pill">
                    لا يوجد عقد
                </span>
            @endif
        </div>

        @if($transaction->contract)
            @php
                $contract = $transaction->contract;
            @endphp

            @can('view contracts')
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="nk-info-box">
                            <small>رقم العقد</small>
                            <strong>{{ $contract->contract_number ?? '-' }}</strong>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="nk-info-box">
                            <small>تاريخ العقد</small>
                            <strong>{{ $contract->contract_date?->format('Y-m-d') ?? '-' }}</strong>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="nk-info-box">
                            <small>قيمة العقد</small>
                            <strong>
                                @if($contract->contract_value)
                                    {{ number_format($contract->contract_value, 2) }} {{ $contract->currency }}
                                @else
                                    -
                                @endif
                            </strong>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="nk-info-box">
                            <small>حالة العقد</small>
                            <strong>{{ $contract->status }}</strong>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="nk-info-box">
                            <small>ملاحظات</small>
                            <strong>{{ $contract->notes ?? '-' }}</strong>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 flex-wrap mb-4">
                    @if($contract->file_path)
                        <a href="{{ asset('storage/' . $contract->file_path) }}"
                           target="_blank"
                           class="btn btn-sm btn-outline-success rounded-pill">
                            <i class="bi bi-file-earmark-text"></i>
                            فتح ملف العقد
                        </a>
                    @endif

                    @if($contract->drive_link)
                        <a href="{{ $contract->drive_link }}"
                           target="_blank"
                           class="btn btn-sm btn-outline-primary rounded-pill">
                            <i class="bi bi-cloud-arrow-up"></i>
                            فتح رابط Drive
                        </a>
                    @endif
                </div>
            @endcan

            @can('edit contracts')
                <hr>

                <h6 class="fw-bold text-success mb-3">
                    تعديل بيانات العقد
                </h6>

                <form method="POST"
                      action="{{ route('admin.contracts.update', $contract) }}"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">رقم العقد</label>
                            <input type="text"
                                   name="contract_number"
                                   class="form-control"
                                   value="{{ old('contract_number', $contract->contract_number) }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">تاريخ العقد</label>
                            <input type="date"
                                   name="contract_date"
                                   class="form-control"
                                   value="{{ old('contract_date', $contract->contract_date?->format('Y-m-d')) }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">قيمة العقد</label>
                            <input type="number"
                                   step="0.01"
                                   name="contract_value"
                                   class="form-control"
                                   value="{{ old('contract_value', $contract->contract_value) }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">العملة</label>
                            <input type="text"
                                   name="currency"
                                   class="form-control"
                                   value="{{ old('currency', $contract->currency) }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">حالة العقد</label>
                            <select name="status" class="form-select" required>
                                <option value="مسودة" @selected(old('status', $contract->status) === 'مسودة')>مسودة</option>
                                <option value="نشط" @selected(old('status', $contract->status) === 'نشط')>نشط</option>
                                <option value="منتهي" @selected(old('status', $contract->status) === 'منتهي')>منتهي</option>
                                <option value="ملغي" @selected(old('status', $contract->status) === 'ملغي')>ملغي</option>
                            </select>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label">رابط Drive</label>
                            <input type="url"
                                   name="drive_link"
                                   class="form-control"
                                   value="{{ old('drive_link', $contract->drive_link) }}"
                                   placeholder="https://drive.google.com/...">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">ملف العقد</label>
                            <input type="file"
                                   name="file"
                                   class="form-control"
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        </div>

                        @if($contract->file_path)
                            <div class="col-md-3 d-flex align-items-end">
                                <div class="form-check">
                                    <input type="checkbox"
                                           name="clear_file"
                                           value="1"
                                           class="form-check-input"
                                           id="clear_contract_file">

                                    <label class="form-check-label text-danger" for="clear_contract_file">
                                        حذف ملف العقد الحالي
                                    </label>
                                </div>
                            </div>
                        @endif

                        <div class="col-12">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes"
                                      class="form-control"
                                      rows="3">{{ old('notes', $contract->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="submit" class="nk-btn-main">
                            <i class="bi bi-save"></i>
                            حفظ تعديل العقد
                        </button>
                    </div>
                </form>
            @endcan

            @can('delete contracts')
                <div class="d-flex justify-content-end mt-3">
                    <form method="POST"
                          action="{{ route('admin.contracts.destroy', $contract) }}"
                          class="js-confirm-form"
                          data-title="حذف العقد"
                          data-text="هل أنت متأكد من حذف هذا العقد؟"
                          data-icon="warning"
                          data-confirm-text="نعم، احذف"
                          data-cancel-text="إلغاء"
                          data-confirm-color="#c0392b">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                            <i class="bi bi-trash"></i>
                            حذف العقد
                        </button>
                    </form>
                </div>
            @endcan
        @else
            @can('create contracts')
                <form method="POST"
                      action="{{ route('admin.transactions.contract.store', $transaction) }}"
                      enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">رقم العقد</label>
                            <input type="text"
                                   name="contract_number"
                                   class="form-control"
                                   value="{{ old('contract_number') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">تاريخ العقد</label>
                            <input type="date"
                                   name="contract_date"
                                   class="form-control"
                                   value="{{ old('contract_date') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">قيمة العقد</label>
                            <input type="number"
                                   step="0.01"
                                   name="contract_value"
                                   class="form-control"
                                   value="{{ old('contract_value') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">العملة</label>
                            <input type="text"
                                   name="currency"
                                   class="form-control"
                                   value="{{ old('currency', app(\App\Services\SettingService::class)->get('default_currency', 'SAR')) }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">حالة العقد</label>
                            <select name="status" class="form-select" required>
                                <option value="مسودة" @selected(old('status') === 'مسودة')>مسودة</option>
                                <option value="نشط" @selected(old('status', 'نشط') === 'نشط')>نشط</option>
                                <option value="منتهي" @selected(old('status') === 'منتهي')>منتهي</option>
                                <option value="ملغي" @selected(old('status') === 'ملغي')>ملغي</option>
                            </select>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label">رابط Drive</label>
                            <input type="url"
                                   name="drive_link"
                                   class="form-control"
                                   value="{{ old('drive_link') }}"
                                   placeholder="https://drive.google.com/...">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">ملف العقد</label>
                            <input type="file"
                                   name="file"
                                   class="form-control"
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        </div>

                        <div class="col-12">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes"
                                      class="form-control"
                                      rows="3">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="nk-btn-main">
                            <i class="bi bi-save"></i>
                            حفظ العقد
                        </button>
                    </div>
                </form>
            @else
                <div class="text-center text-muted py-4">
                    لا يوجد عقد لهذه المعاملة.
                </div>
            @endcan
        @endif
    </div>
@endcanany