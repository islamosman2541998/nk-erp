<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionTypesAndRequirementsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $types = [
            [
                'name' => 'فئة أولى (تضم خطة)',
                'description' => 'معاملات الفئة الأولى وتشمل إعداد الخطة المطلوبة.',
                'sort_order' => 1,
                'documents' => [
                    'السجل التجاري',
                    'الشهادة الضريبية',
                    'العنوان الوطني',

                    'الخطة البيئية',
                    'المخرج الفني',
                    'ملفات الموظفين',
                    'اللوائح والأنظمة',
                    'قوائم الفريق',
                ],
            ],
            [
                'name' => 'فئة تانية (تضم دراسة تدقيق، دراسة تقييم)',
                'description' => 'معاملات الفئة الثانية وتشمل دراسة تدقيق أو دراسة تقييم.',
                'sort_order' => 2,
                'documents' => [
                    'السجل التجاري',
                    'الشهادة الضريبية',
                    'العنوان الوطني',

                    'دراسة التدقيق',
                    'دراسة التقييم',
                    'المخرج الفني',
                    'ملفات الموظفين',
                    'اللوائح والأنظمة',
                    'قوائم الفريق',
                ],
            ],
            [
                'name' => 'فئة تالتة (تضم دراسة تدقيق، دراسة تقييم، نطاق)',
                'description' => 'معاملات الفئة الثالثة وتشمل دراسة تدقيق ودراسة تقييم ونطاق العمل.',
                'sort_order' => 3,
                'documents' => [
                    'السجل التجاري',
                    'الشهادة الضريبية',
                    'العنوان الوطني',
                    'نطاق العمل',
                    'دراسة التدقيق',
                    'دراسة التقييم',
                    'المخرج الفني',
                    'ملفات الموظفين',
                    'اللوائح والأنظمة',
                    'قوائم الفريق',
                ],
            ],
            [
                'name' => 'خدمات البصمة الكربونية',
                'description' => 'خدمات متعلقة بقياس وإعداد تقارير البصمة الكربونية.',
                'sort_order' => 4,
                'documents' => [
                    'السجل التجاري',
                    'الشهادة الضريبية',
                    'العنوان الوطني',
                    'بيانات النشاط',
                    'بيانات الاستهلاك',
                    'بيانات الانبعاثات',
                    'تقرير البصمة الكربونية',
                    'المخرج الفني',
                ],
            ],
            [
                'name' => 'خدمات الاستشارات التعدينية',
                'description' => 'خدمات استشارية مرتبطة بالقطاع التعديني.',
                'sort_order' => 5,
                'documents' => [
                    'السجل التجاري',
                    'الشهادة الضريبية',
                    'العنوان الوطني',
                    'بيانات الموقع',
                    'بيانات النشاط التعديني',
                    'مستندات الترخيص إن وجدت',
                    'التقرير الفني',
                    'المخرج الفني',
                ],
            ],
            [
                'name' => 'خدمات تنفيذ خطط إعادة التأهيل والمعالجة',
                'description' => 'تشمل خطة إعادة تأهيل، خطة معالجة مواقع متدهورة، وخطط تصحيحية.',
                'sort_order' => 6,
                'documents' => [
                    'السجل التجاري',
                    'الشهادة الضريبية',
                    'العنوان الوطني',
                    'بيانات الموقع',
                    'تقرير حالة الموقع',
                    'خطة إعادة تأهيل',
                    'خطة معالجة مواقع متدهورة',
                    'خطط تصحيحية',
                    'المخرج الفني',
                ],
            ],
            [
                'name' => 'الدراسات الفنية الأخرى',
                'description' => 'أي دراسات فنية لا تندرج تحت التصنيفات السابقة.',
                'sort_order' => 7,
                'documents' => [
                    'السجل التجاري',
                    'الشهادة الضريبية',
                    'العنوان الوطني',
                    'نطاق الدراسة',
                    'الدراسة الفنية',
                    'المخرج الفني',
                ],
            ],
            [
                'name' => 'قياسات بيئية ومختبرية (قياسات بيئية، قياسات / تقارير اختبارية)',
                'description' => 'تشمل القياسات البيئية والقياسات أو التقارير الاختبارية.',
                'sort_order' => 8,
                'documents' => [
                    'السجل التجاري',
                    'الشهادة الضريبية',
                    'العنوان الوطني',
                    'نطاق القياسات',
                    'موقع القياس',
                    'نتائج القياسات البيئية',
                    'تقارير اختبارية',
                    'المخرج الفني',
                ],
            ],
        ];

        foreach ($types as $type) {
            DB::table('transaction_types')->updateOrInsert(
                ['name' => $type['name']],
                [
                    'description' => $type['description'],
                    'sort_order' => $type['sort_order'],
                    'is_active' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );

            $transactionTypeId = DB::table('transaction_types')
                ->where('name', $type['name'])
                ->value('id');

            DB::table('transaction_type_document_requirements')
                ->where('transaction_type_id', $transactionTypeId)
                ->delete();

            foreach ($type['documents'] as $index => $documentName) {
                DB::table('transaction_type_document_requirements')->insert([
                    'transaction_type_id' => $transactionTypeId,
                    'name' => $documentName,
                    'description' => null,
                    'is_required' => true,
                    'is_active' => true,
                    'sort_order' => $index + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}