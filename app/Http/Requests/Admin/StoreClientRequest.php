<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('create clients') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'facility_name' => ['nullable', 'string', 'max:255'],
            'commercial_registration_number' => ['nullable', 'string', 'max:255'],
            'tax_number' => ['nullable', 'string', 'max:255'],

            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],

            'contact_person_name' => ['nullable', 'string', 'max:255'],
            'contact_person_phone' => ['nullable', 'string', 'max:255'],
            'contact_person_email' => ['nullable', 'email', 'max:255'],

            'city' => ['nullable', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'اسم العميل',
            'facility_name' => 'اسم المنشأة',
            'commercial_registration_number' => 'رقم السجل التجاري',
            'tax_number' => 'الرقم الضريبي',
            'phone' => 'رقم الجوال',
            'email' => 'البريد الإلكتروني',
            'contact_person_name' => 'اسم شخص التواصل',
            'contact_person_phone' => 'رقم شخص التواصل',
            'contact_person_email' => 'بريد شخص التواصل',
            'city' => 'المدينة',
            'region' => 'المنطقة',
            'address' => 'العنوان',
            'notes' => 'ملاحظات',
        ];
    }
}