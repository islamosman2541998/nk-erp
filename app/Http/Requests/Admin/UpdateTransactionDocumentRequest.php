<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTransactionDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('upload attachments')
            || auth()->user()?->can('review attachments');
    }

    public function rules(): array
    {
        return [
            'status' => [
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

            'file' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx',
                'max:10240',
            ],

            'drive_link' => ['nullable', 'url', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'status' => 'حالة المستند',
            'file' => 'الملف',
            'drive_link' => 'رابط Drive',
            'notes' => 'الملاحظات',
        ];
    }
}