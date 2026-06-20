<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkUpdateTransactionDocumentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('upload attachments')
            || auth()->user()?->can('review attachments');
    }

    public function rules(): array
    {
        return [
            'documents' => ['required', 'array'],

            'documents.*.status' => [
                'required',
                'string',
                Rule::in([
                    'ناقص',
                    'تم الرفع',
                    'مرفوض',
                    'تمت المراجعة',
                    'معتمد',
                ]),
            ],

            'documents.*.file' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx',
                'max:10240',
            ],

            'documents.*.drive_link' => ['nullable', 'url', 'max:255'],
            'documents.*.notes' => ['nullable', 'string'],
            'documents.*.clear_file' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'documents.*.status' => 'حالة المستند',
            'documents.*.file' => 'ملف المستند',
            'documents.*.drive_link' => 'رابط Drive',
            'documents.*.notes' => 'الملاحظات',
        ];
    }
}