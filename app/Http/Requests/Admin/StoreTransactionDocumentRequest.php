<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('upload attachments') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'status' => ['nullable', 'string', 'in:ناقص,تم الرفع,مرفوض,تمت المراجعة,معتمد'],

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
            'name' => 'اسم المستند',
            'status' => 'حالة المستند',
            'file' => 'الملف',
            'drive_link' => 'رابط Drive',
            'notes' => 'الملاحظات',
        ];
    }
}