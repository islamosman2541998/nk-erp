<x-app-layout>
    <div class="mb-4">
        <h1 class="nk-page-title">إعدادات النظام</h1>
        <p class="nk-page-subtitle">
            التحكم في بيانات الشركة، اللوجو، شكل النظام، وإعدادات المعاملات.
        </p>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            @foreach ($settings as $groupName => $groupSettings)
                <div class="col-lg-6">
                    <div class="nk-card h-100">
                        <h5 class="fw-bold text-success mb-4">
                            @switch($groupName)
                                @case('general')
                                    الإعدادات العامة
                                @break

                                @case('transactions')
                                    إعدادات المعاملات
                                @break

                                @case('documents')
                                    إعدادات المستندات
                                @break

                                @case('financial')
                                    الإعدادات المالية
                                @break

                                @case('appearance')
                                    إعدادات الشكل
                                @break

                                @default
                                    {{ $groupName }}
                            @endswitch
                        </h5>

                        <div class="row g-3">
                            @foreach ($groupSettings as $setting)
                                <div class="col-12">
                                    <label class="form-label">
                                        {{ $setting->label ?? $setting->key }}
                                    </label>

                                    @if ($setting->type === 'textarea')
                                        <textarea name="{{ $setting->key }}" class="form-control" rows="3">{{ old($setting->key, $setting->value) }}</textarea>
                                    @elseif($setting->type === 'color')
                                        <input type="color" name="{{ $setting->key }}"
                                            class="form-control form-control-color"
                                            value="{{ old($setting->key, $setting->value) }}">
                                    @elseif($setting->type === 'file')
                                        @if ($setting->value)
                                            <div class="mb-3">
                                                <img src="{{ asset('storage/' . $setting->value) }}" alt="لوجو الشركة"
                                                    class="nk-settings-logo-preview">

                                                <div class="form-check mt-2">
                                                    <input type="checkbox" name="remove_company_logo" value="1"
                                                        class="form-check-input" id="remove_company_logo">

                                                    <label class="form-check-label text-danger"
                                                        for="remove_company_logo">
                                                        مسح اللوجو الحالي
                                                    </label>
                                                </div>
                                            </div>
                                        @endif

                                        <input type="file" name="{{ $setting->key }}" class="form-control"
                                            accept=".jpg,.jpeg,.png,.webp">

                                        <div class="form-text">
                                            الصيغ المسموحة: JPG, PNG, WEBP — الحد الأقصى 2MB
                                        </div>
                                    @else
                                        <input type="{{ $setting->type }}" name="{{ $setting->key }}"
                                            class="form-control" value="{{ old($setting->key, $setting->value ?: '#000000') }}">
                                    @endif

                                    @error($setting->key)
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="nk-btn-main">
                <i class="bi bi-save"></i>
                حفظ الإعدادات
            </button>
        </div>
    </form>
</x-app-layout>
