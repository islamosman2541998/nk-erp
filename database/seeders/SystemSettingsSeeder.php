<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SystemSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'system_name',
                'value' => 'NK ERP',
                'label' => 'اسم النظام',
                'group_name' => 'general',
                'type' => 'text',
                'sort_order' => 1,
            ],
            [
                'key' => 'company_name',
                'value' => 'شركة نابت وخليفة للاستشارات التعدينية والبيئية',
                'label' => 'اسم الشركة',
                'group_name' => 'general',
                'type' => 'text',
                'sort_order' => 2,
            ],
            [
                'key' => 'company_logo',
                'value' => null,
                'label' => 'لوجو الشركة',
                'group_name' => 'general',
                'type' => 'file',
                'sort_order' => 3,
            ],
            [
                'key' => 'company_phone',
                'value' => '',
                'label' => 'رقم الهاتف',
                'group_name' => 'general',
                'type' => 'text',
                'sort_order' => 4,
            ],
            [
                'key' => 'company_email',
                'value' => '',
                'label' => 'البريد الإلكتروني',
                'group_name' => 'general',
                'type' => 'email',
                'sort_order' => 5,
            ],
            [
                'key' => 'company_address',
                'value' => '',
                'label' => 'العنوان',
                'group_name' => 'general',
                'type' => 'textarea',
                'sort_order' => 6,
            ],
            [
                'key' => 'timezone',
                'value' => 'Asia/Riyadh',
                'label' => 'المنطقة الزمنية',
                'group_name' => 'general',
                'type' => 'text',
                'sort_order' => 7,
            ],
            [
                'key' => 'transaction_reference_prefix',
                'value' => 'NK-TRX',
                'label' => 'بادئة رقم المعاملة',
                'group_name' => 'transactions',
                'type' => 'text',
                'sort_order' => 1,
            ],
            [
                'key' => 'transaction_reference_digits',
                'value' => '4',
                'label' => 'عدد أرقام التسلسل',
                'group_name' => 'transactions',
                'type' => 'number',
                'sort_order' => 2,
            ],
            [
                'key' => 'default_transaction_status',
                'value' => 'تحت الإجراء',
                'label' => 'الحالة الافتراضية للمعاملة',
                'group_name' => 'transactions',
                'type' => 'text',
                'sort_order' => 3,
            ],
            [
                'key' => 'default_document_status',
                'value' => 'ناقص',
                'label' => 'الحالة الافتراضية للمستند',
                'group_name' => 'documents',
                'type' => 'text',
                'sort_order' => 1,
            ],
            [
                'key' => 'default_currency',
                'value' => 'SAR',
                'label' => 'العملة الافتراضية',
                'group_name' => 'financial',
                'type' => 'text',
                'sort_order' => 1,
            ],
            [
                'key' => 'primary_color',
                'value' => '#073f22',
                'label' => 'اللون الأساسي',
                'group_name' => 'appearance',
                'type' => 'color',
                'sort_order' => 1,
            ],
            [
                'key' => 'sidebar_color',
                'value' => '#052f19',
                'label' => 'لون القائمة الجانبية',
                'group_name' => 'appearance',
                'type' => 'color',
                'sort_order' => 2,
            ],
            [
                'key' => 'accent_color',
                'value' => '#c89b3c',
                'label' => 'لون التمييز / الأيقونات',
                'group_name' => 'appearance',
                'type' => 'color',
                'sort_order' => 3,
            ],
            [
                'key' => 'background_color',
                'value' => '#f5f7f3',
                'label' => 'لون خلفية النظام',
                'group_name' => 'appearance',
                'type' => 'color',
                'sort_order' => 4,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::query()->updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}