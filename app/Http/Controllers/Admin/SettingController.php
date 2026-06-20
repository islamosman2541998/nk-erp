<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        abort_unless(auth()->user()->can('edit settings'), 403);

        $settings = Setting::query()
            ->orderBy('group_name')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group_name');

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request, SettingService $settingService)
    {
        abort_unless(auth()->user()->can('edit settings'), 403);

        $data = $request->validate([
            'system_name' => ['nullable', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],

            'company_logo' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'company_phone' => ['nullable', 'string', 'max:255'],
            'company_email' => ['nullable', 'email', 'max:255'],
            'company_address' => ['nullable', 'string'],
            'timezone' => ['nullable', 'string', 'max:255'],

            'transaction_reference_prefix' => ['nullable', 'string', 'max:50'],
            'transaction_reference_digits' => ['nullable', 'integer', 'min:1', 'max:10'],
            'default_transaction_status' => ['nullable', 'string', 'max:255'],

            'default_document_status' => ['nullable', 'string', 'max:255'],

            'default_currency' => ['nullable', 'string', 'max:10'],

            'primary_color' => ['nullable', 'string', 'max:20'],
            'sidebar_color' => ['nullable', 'string', 'max:20'],
            'accent_color' => ['nullable', 'string', 'max:20'],
            'background_color' => ['nullable', 'string', 'max:20'],

            'remove_company_logo' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('remove_company_logo')) {
            $oldLogo = $settingService->get('company_logo');

            $settingService->deleteFileIfExists($oldLogo);

            $data['company_logo'] = null;
        } elseif ($request->hasFile('company_logo')) {
            $oldLogo = $settingService->get('company_logo');

            $settingService->deleteFileIfExists($oldLogo);

            $data['company_logo'] = $request->file('company_logo')
                ->store('settings/logos', 'public');
        } else {
            unset($data['company_logo']);
        }

        unset($data['remove_company_logo']);

        $settingService->setMany($data);

        return back()->with('success', 'تم حفظ إعدادات النظام بنجاح');
    }
}