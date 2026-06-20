<x-app-layout>
    <div class="mb-4">
        <h1 class="nk-page-title">إضافة معاملة جديدة</h1>
        <p class="nk-page-subtitle">
            يتم إنشاء ملف معاملة متكامل وربطه بالعميل ونوع الخدمة، مع توليد المستندات المطلوبة تلقائيًا.
        </p>
    </div>

    <div class="nk-card">
        <form method="POST" action="{{ route('admin.transactions.store') }}">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">العميل <span class="text-danger">*</span></label>
                    <select name="client_id" class="form-select" required>
                        <option value="">اختر العميل</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}" @selected(old('client_id', request('client_id')) == $client->id)>
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
                            <option value="{{ $type->id }}" @selected(old('transaction_type_id') == $type->id)>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('transaction_type_id')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">عنوان المعاملة</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">حالة المعاملة</label>
                    <select name="status" class="form-select" required>
                        <option value="تحت الإجراء" @selected(old('status') == 'تحت الإجراء')>تحت الإجراء</option>
                        <option value="تم صدور التصريح" @selected(old('status') == 'تم صدور التصريح')>تم صدور التصريح</option>
                        <option value="أخرى" @selected(old('status') == 'أخرى')>أخرى</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">اسم المشروع</label>
                    <input type="text" name="project_name" class="form-control" value="{{ old('project_name') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">المدينة</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">المنطقة</label>
                    <input type="text" name="region" class="form-control" value="{{ old('region') }}">
                </div>

                <div class="col-md-12">
                    <label class="form-label">موقع المشروع</label>
                    <textarea name="project_location" class="form-control" rows="2">{{ old('project_location') }}</textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label">نوع النشاط</label>
                    <input type="text" name="activity_type" class="form-control" value="{{ old('activity_type') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">كود النشاط</label>
                    <input type="text" name="activity_code" class="form-control" value="{{ old('activity_code') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">التصنيف / الفئة</label>
                    <input type="text" name="category" class="form-control" value="{{ old('category') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">رقم الطلب في المركز</label>
                    <input type="text" name="center_request_number" class="form-control"
                        value="{{ old('center_request_number') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">اسم الجهة</label>
                    <input type="text" name="authority_name" class="form-control"
                        value="{{ old('authority_name') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">رقم مرجع الجهة</label>
                    <input type="text" name="authority_reference_number" class="form-control"
                        value="{{ old('authority_reference_number') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">المسؤول الرئيسي</label>
                    <select name="assigned_to" class="form-select">
                        <option value="">غير محدد</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected(old('assigned_to') == $user->id)>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">المدير الفني</label>
                    <select name="technical_manager_id" class="form-select">
                        <option value="">غير محدد</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected(old('technical_manager_id') == $user->id)>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">المنسق</label>
                    <select name="coordinator_id" class="form-select">
                        <option value="">غير محدد</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected(old('coordinator_id') == $user->id)>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">المسؤول المالي</label>
                    <select name="financial_user_id" class="form-select">
                        <option value="">غير محدد</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected(old('financial_user_id') == $user->id)>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">تاريخ البداية</label>
                    <input type="date" name="started_at" class="form-control" value="{{ old('started_at') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">تاريخ التسليم المتوقع</label>
                    <input type="date" name="expected_delivery_at" class="form-control"
                        value="{{ old('expected_delivery_at') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">رابط Drive الرئيسي</label>
                    <input type="url" name="main_drive_link" class="form-control"
                        value="{{ old('main_drive_link') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">رابط اجتماعات Drive</label>
                    <input type="url" name="meetings_drive_link" class="form-control"
                        value="{{ old('meetings_drive_link') }}">
                </div>

                <div class="col-12">
                    <label class="form-label">وصف / ملاحظات</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline-secondary rounded-pill">
                    رجوع
                </a>

                <button type="submit" class="nk-btn-main">
                    إنشاء المعاملة
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
