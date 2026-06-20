<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('create transactions') ?? false;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'transaction_type_id' => ['required', 'exists:transaction_types,id'],
            'transaction_subtype_id' => ['nullable', 'exists:transaction_types,id'],

            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            'status' => ['required', 'string', 'max:255'],
            'internal_status' => ['nullable', 'string', 'max:255'],
            'priority' => ['nullable', 'string', 'max:255'],

            'project_name' => ['nullable', 'string', 'max:255'],
            'project_location' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:255'],

            'activity_type' => ['nullable', 'string', 'max:255'],
            'activity_code' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],

            'center_request_number' => ['nullable', 'string', 'max:255'],
            'authority_name' => ['nullable', 'string', 'max:255'],
            'authority_reference_number' => ['nullable', 'string', 'max:255'],

            'permit_number' => ['nullable', 'string', 'max:255'],
            'permit_issued_at' => ['nullable', 'date'],
            'permit_expires_at' => ['nullable', 'date'],

            'assigned_to' => ['nullable', 'exists:users,id'],
            'technical_manager_id' => ['nullable', 'exists:users,id'],
            'coordinator_id' => ['nullable', 'exists:users,id'],
            'financial_user_id' => ['nullable', 'exists:users,id'],

            'started_at' => ['nullable', 'date'],
            'expected_delivery_at' => ['nullable', 'date'],

            'main_drive_link' => ['nullable', 'url', 'max:255'],
            'meetings_drive_link' => ['nullable', 'url', 'max:255'],

            'notes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'client_id' => 'العميل',
            'transaction_type_id' => 'نوع المعاملة',
            'title' => 'عنوان المعاملة',
            'status' => 'حالة المعاملة',
            'project_name' => 'اسم المشروع',
            'city' => 'المدينة',
            'center_request_number' => 'رقم الطلب في المركز',
            'permit_number' => 'رقم التصريح',
        ];
    }
}