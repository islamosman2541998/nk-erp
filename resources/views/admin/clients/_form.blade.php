@csrf

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">اسم العميل <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $client->name ?? '') }}" required>
        @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">اسم المنشأة</label>
        <input type="text" name="facility_name" class="form-control" value="{{ old('facility_name', $client->facility_name ?? '') }}">
        @error('facility_name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">رقم السجل التجاري</label>
        <input type="text" name="commercial_registration_number" class="form-control" value="{{ old('commercial_registration_number', $client->commercial_registration_number ?? '') }}">
        @error('commercial_registration_number') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">الرقم الضريبي</label>
        <input type="text" name="tax_number" class="form-control" value="{{ old('tax_number', $client->tax_number ?? '') }}">
        @error('tax_number') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">رقم الجوال</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $client->phone ?? '') }}">
        @error('phone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">البريد الإلكتروني</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $client->email ?? '') }}">
        @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">المدينة</label>
        <input type="text" name="city" class="form-control" value="{{ old('city', $client->city ?? '') }}">
        @error('city') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">المنطقة</label>
        <input type="text" name="region" class="form-control" value="{{ old('region', $client->region ?? '') }}">
        @error('region') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">اسم شخص التواصل</label>
        <input type="text" name="contact_person_name" class="form-control" value="{{ old('contact_person_name', $client->contact_person_name ?? '') }}">
        @error('contact_person_name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">رقم شخص التواصل</label>
        <input type="text" name="contact_person_phone" class="form-control" value="{{ old('contact_person_phone', $client->contact_person_phone ?? '') }}">
        @error('contact_person_phone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">بريد شخص التواصل</label>
        <input type="email" name="contact_person_email" class="form-control" value="{{ old('contact_person_email', $client->contact_person_email ?? '') }}">
        @error('contact_person_email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">العنوان</label>
        <textarea name="address" class="form-control" rows="3">{{ old('address', $client->address ?? '') }}</textarea>
        @error('address') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label class="form-label">ملاحظات</label>
        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $client->notes ?? '') }}</textarea>
        @error('notes') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary rounded-pill">
        رجوع
    </a>

    <button type="submit" class="nk-btn-main">
        حفظ البيانات
    </button>
</div>