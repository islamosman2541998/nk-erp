<x-app-layout>
    <div class="mb-4">
        <h1 class="nk-page-title">تعديل بيانات المعاملة</h1>
        <p class="nk-page-subtitle">
            يمكنك تعديل بيانات المعاملة رقم {{ $transaction->reference_number }} مع الاحتفاظ بالمستندات والمرفقات
            المرتبطة بها.
        </p>
    </div>

    <div class="nk-card">
        <form method="POST" action="{{ route('admin.transactions.update', $transaction) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">العميل <span class="text-danger">*</span></label>
                    <select name="client_id" class="form-select" required>
                        <option value="">اختر العميل</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}" @selected(old('client_id', $transaction->client_id) == $client->id)>
                                {{ $client->name }} {{ $client->facility_name ? ' - ' . $client->facility_name : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('client_id')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">نوع المعاملة <span class="text-danger">*</span></label>
                    <select name="transaction_type_id" class="form-select" required>
                        <option value="">اختر نوع المعاملة</option>

                        @foreach ($transactionTypes as $type)
                            <option value="{{ $type->id }}" @selected(old('transaction_type_id', $transaction->transaction_type_id) == $type->id)>
                                {{ $type->name }}
                                @if (!$type->is_active)
                                    - غير نشط
                                @endif
                            </option>
                        @endforeach
                    </select>

                    @error('transaction_type_id')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">عنوان المعاملة</label>
                    <input type="text" name="title" class="form-control"
                        value="{{ old('title', $transaction->title) }}">
                    @error('title')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">حالة المعاملة</label>
                    <select name="status" class="form-select" required>
                        <option value="تحت الإجراء" @selected(old('status', $transaction->status) == 'تحت الإجراء')>
                            تحت الإجراء
                        </option>
                        <option value="تم صدور التصريح" @selected(old('status', $transaction->status) == 'تم صدور التصريح')>
                            تم صدور التصريح
                        </option>
                        <option value="أخرى" @selected(old('status', $transaction->status) == 'أخرى')>
                            أخرى
                        </option>
                    </select>
                    @error('status')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">اسم المشروع</label>
                    <input type="text" name="project_name" class="form-control"
                        value="{{ old('project_name', $transaction->project_name) }}">
                    @error('project_name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">المدينة</label>
                    <input type="text" name="city" class="form-control"
                        value="{{ old('city', $transaction->city) }}">
                    @error('city')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">المنطقة</label>
                    <input type="text" name="region" class="form-control"
                        value="{{ old('region', $transaction->region) }}">
                    @error('region')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">موقع المشروع</label>
                    <textarea name="project_location" class="form-control" rows="2">{{ old('project_location', $transaction->project_location) }}</textarea>
                    @error('project_location')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">نوع النشاط</label>
                    <input type="text" name="activity_type" class="form-control"
                        value="{{ old('activity_type', $transaction->activity_type) }}">
                    @error('activity_type')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">كود النشاط</label>
                    <input type="text" name="activity_code" class="form-control"
                        value="{{ old('activity_code', $transaction->activity_code) }}">
                    @error('activity_code')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">التصنيف / الفئة</label>
                    <input type="text" name="category" class="form-control"
                        value="{{ old('category', $transaction->category) }}">
                    @error('category')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">رقم الطلب في المركز</label>
                    <input type="text" name="center_request_number" class="form-control"
                        value="{{ old('center_request_number', $transaction->center_request_number) }}">
                    @error('center_request_number')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">اسم الجهة</label>
                    <input type="text" name="authority_name" class="form-control"
                        value="{{ old('authority_name', $transaction->authority_name) }}">
                    @error('authority_name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">رقم مرجع الجهة</label>
                    <input type="text" name="authority_reference_number" class="form-control"
                        value="{{ old('authority_reference_number', $transaction->authority_reference_number) }}">
                    @error('authority_reference_number')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">رقم التصريح</label>
                    <input type="text" name="permit_number" class="form-control"
                        value="{{ old('permit_number', $transaction->permit_number) }}">
                    @error('permit_number')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">تاريخ إصدار التصريح</label>
                    <input type="date" name="permit_issued_at" class="form-control"
                        value="{{ old('permit_issued_at', $transaction->permit_issued_at ? \Carbon\Carbon::parse($transaction->permit_issued_at)->format('Y-m-d') : '') }}">
                    @error('permit_issued_at')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">تاريخ انتهاء التصريح</label>
                    <input type="date" name="permit_expires_at" class="form-control"
                        value="{{ old('permit_expires_at', $transaction->permit_expires_at ? \Carbon\Carbon::parse($transaction->permit_expires_at)->format('Y-m-d') : '') }}">
                    @error('permit_expires_at')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">المسؤول الرئيسي</label>
                    <select name="assigned_to" class="form-select">
                        <option value="">غير محدد</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected(old('assigned_to', $transaction->assigned_to) == $user->id)>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_to')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">المدير الفني</label>
                    <select name="technical_manager_id" class="form-select">
                        <option value="">غير محدد</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected(old('technical_manager_id', $transaction->technical_manager_id) == $user->id)>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('technical_manager_id')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">المنسق</label>
                    <select name="coordinator_id" class="form-select">
                        <option value="">غير محدد</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected(old('coordinator_id', $transaction->coordinator_id) == $user->id)>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('coordinator_id')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">المسؤول المالي</label>
                    <select name="financial_user_id" class="form-select">
                        <option value="">غير محدد</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected(old('financial_user_id', $transaction->financial_user_id) == $user->id)>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('financial_user_id')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">تاريخ البداية</label>
                    <input type="date" name="started_at" class="form-control"
                        value="{{ old('started_at', $transaction->started_at ? \Carbon\Carbon::parse($transaction->started_at)->format('Y-m-d') : '') }}">
                    @error('started_at')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">تاريخ التسليم المتوقع</label>
                    <input type="date" name="expected_delivery_at" class="form-control"
                        value="{{ old('expected_delivery_at', $transaction->expected_delivery_at ? \Carbon\Carbon::parse($transaction->expected_delivery_at)->format('Y-m-d') : '') }}">
                    @error('expected_delivery_at')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">رابط Drive الرئيسي</label>
                    <input type="url" name="main_drive_link" class="form-control"
                        value="{{ old('main_drive_link', $transaction->main_drive_link) }}">
                    @error('main_drive_link')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">رابط اجتماعات Drive</label>
                    <input type="url" name="meetings_drive_link" class="form-control"
                        value="{{ old('meetings_drive_link', $transaction->meetings_drive_link) }}">
                    @error('meetings_drive_link')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">وصف / ملاحظات</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $transaction->notes) }}</textarea>
                    @error('notes')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.transactions.show', $transaction) }}"
                    class="btn btn-outline-secondary rounded-pill">
                    رجوع
                </a>

                <button type="submit" class="nk-btn-main">
                    حفظ التعديلات
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
