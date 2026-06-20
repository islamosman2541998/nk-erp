<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Transaction;
use App\Models\TransactionDocument;
use App\Models\TransactionType;
use App\Models\User;
use Illuminate\Database\Seeder;

class SampleClientsAndTransactionsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->first();

        $clients = [
            [
                'name' => 'مصنع الرياض للصناعات',
                'facility_name' => 'مصنع الرياض',
                'commercial_registration_number' => '1010001111',
                'tax_number' => '300111222333',
                'phone' => '0501112222',
                'email' => 'info@riyadh-factory.com',
                'contact_person_name' => 'أحمد سالم',
                'contact_person_phone' => '0505551111',
                'city' => 'الرياض',
                'region' => 'منطقة الرياض',
                'address' => 'المدينة الصناعية الثانية - الرياض',
                'notes' => 'عميل تجريبي لمعاملات التصاريح والسجلات البيئية.',
            ],
            [
                'name' => 'شركة الخليج للتعدين',
                'facility_name' => 'موقع الخليج التعديني',
                'commercial_registration_number' => '2050002222',
                'tax_number' => '300222333444',
                'phone' => '0552223333',
                'email' => 'contact@gulf-mining.com',
                'contact_person_name' => 'خالد منصور',
                'contact_person_phone' => '0557772222',
                'city' => 'الدمام',
                'region' => 'المنطقة الشرقية',
                'address' => 'طريق الظهران - الدمام',
                'notes' => 'عميل تجريبي لخدمات الاستشارات التعدينية.',
            ],
            [
                'name' => 'شركة المستقبل للمقاولات',
                'facility_name' => 'مشروع المستقبل',
                'commercial_registration_number' => '4030003333',
                'tax_number' => '300333444555',
                'phone' => '0563334444',
                'email' => 'info@future-contracting.com',
                'contact_person_name' => 'سارة فهد',
                'contact_person_phone' => '0568883333',
                'city' => 'جدة',
                'region' => 'منطقة مكة المكرمة',
                'address' => 'حي الروضة - جدة',
                'notes' => 'عميل تجريبي للدراسات الفنية والقياسات البيئية.',
            ],
            [
                'name' => 'مصنع النخبة للمنتجات الغذائية',
                'facility_name' => 'مصنع النخبة',
                'commercial_registration_number' => '1010004444',
                'tax_number' => '300444555666',
                'phone' => '0574445555',
                'email' => 'hello@elite-food.com',
                'contact_person_name' => 'محمد العتيبي',
                'contact_person_phone' => '0579994444',
                'city' => 'الخرج',
                'region' => 'منطقة الرياض',
                'address' => 'المنطقة الصناعية - الخرج',
                'notes' => 'عميل تجريبي لخدمات الفئة الأولى والثانية.',
            ],
        ];

        $createdClients = [];

        foreach ($clients as $clientData) {
            $client = Client::query()->firstOrCreate(
                [
                    'name' => $clientData['name'],
                    'facility_name' => $clientData['facility_name'],
                ],
                array_merge($clientData, [
                    'created_by' => $admin?->id,
                ])
            );

            $createdClients[] = $client;
        }

        $types = TransactionType::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->keyBy('name');

        $transactions = [
            [
                'reference_number' => 'NK-TRX-2026-0001',
                'client' => 'مصنع الرياض للصناعات',
                'type' => 'فئة أولى (تضم خطة)',
                'title' => 'إعداد خطة بيئية لمصنع الرياض',
                'status' => 'تحت الإجراء',
                'project_name' => 'خطة بيئية لمصنع الرياض',
                'city' => 'الرياض',
                'region' => 'منطقة الرياض',
                'project_location' => 'المدينة الصناعية الثانية - الرياض',
                'activity_type' => 'صناعة',
                'activity_code' => 'IND-001',
                'category' => 'فئة أولى',
                'center_request_number' => 'NCEC-2026-1001',
                'authority_name' => 'المركز الوطني للرقابة على الالتزام البيئي',
                'started_at' => now()->subDays(8)->toDateString(),
                'expected_delivery_at' => now()->addDays(14)->toDateString(),
                'notes' => 'معاملة تجريبية لاختبار خطة الفئة الأولى.',
            ],
            [
                'reference_number' => 'NK-TRX-2026-0002',
                'client' => 'مصنع النخبة للمنتجات الغذائية',
                'type' => 'فئة تانية (تضم دراسة تدقيق، دراسة تقييم)',
                'title' => 'دراسة تدقيق وتقييم بيئي لمصنع النخبة',
                'status' => 'تحت الإجراء',
                'project_name' => 'دراسة بيئية لمصنع النخبة',
                'city' => 'الخرج',
                'region' => 'منطقة الرياض',
                'project_location' => 'المنطقة الصناعية - الخرج',
                'activity_type' => 'منتجات غذائية',
                'activity_code' => 'FOOD-220',
                'category' => 'فئة تانية',
                'center_request_number' => 'NCEC-2026-1002',
                'authority_name' => 'المركز الوطني للرقابة على الالتزام البيئي',
                'started_at' => now()->subDays(5)->toDateString(),
                'expected_delivery_at' => now()->addDays(20)->toDateString(),
                'notes' => 'تجربة لمعاملة تضم دراسة تدقيق ودراسة تقييم.',
            ],
            [
                'reference_number' => 'NK-TRX-2026-0003',
                'client' => 'شركة المستقبل للمقاولات',
                'type' => 'فئة تالتة (تضم دراسة تدقيق، دراسة تقييم، نطاق)',
                'title' => 'نطاق ودراسة تقييم لمشروع المستقبل',
                'status' => 'تحت الإجراء',
                'project_name' => 'مشروع المستقبل للمقاولات',
                'city' => 'جدة',
                'region' => 'منطقة مكة المكرمة',
                'project_location' => 'حي الروضة - جدة',
                'activity_type' => 'مقاولات',
                'activity_code' => 'CON-330',
                'category' => 'فئة تالتة',
                'center_request_number' => 'NCEC-2026-1003',
                'authority_name' => 'المركز الوطني للرقابة على الالتزام البيئي',
                'started_at' => now()->subDays(3)->toDateString(),
                'expected_delivery_at' => now()->addDays(30)->toDateString(),
                'notes' => 'معاملة تجريبية للفئة الثالثة بنطاق عمل.',
            ],
            [
                'reference_number' => 'NK-TRX-2026-0004',
                'client' => 'شركة الخليج للتعدين',
                'type' => 'خدمات الاستشارات التعدينية',
                'title' => 'استشارة تعدين لموقع الخليج',
                'status' => 'تحت الإجراء',
                'project_name' => 'موقع الخليج التعديني',
                'city' => 'الدمام',
                'region' => 'المنطقة الشرقية',
                'project_location' => 'طريق الظهران - الدمام',
                'activity_type' => 'تعدين',
                'activity_code' => 'MIN-440',
                'category' => 'استشارات تعدين',
                'center_request_number' => null,
                'authority_name' => null,
                'started_at' => now()->subDays(1)->toDateString(),
                'expected_delivery_at' => now()->addDays(25)->toDateString(),
                'notes' => 'معاملة تجريبية لخدمات الاستشارات التعدينية.',
            ],
            [
                'reference_number' => 'NK-TRX-2026-0005',
                'client' => 'شركة المستقبل للمقاولات',
                'type' => 'قياسات بيئية ومختبرية (قياسات بيئية، قياسات / تقارير اختبارية)',
                'title' => 'قياسات بيئية لموقع المشروع',
                'status' => 'تم صدور التصريح',
                'project_name' => 'قياسات بيئية لمشروع المستقبل',
                'city' => 'جدة',
                'region' => 'منطقة مكة المكرمة',
                'project_location' => 'موقع المشروع - جدة',
                'activity_type' => 'قياسات بيئية',
                'activity_code' => 'ENV-MEAS-101',
                'category' => 'قياسات بيئية ومختبرية',
                'center_request_number' => 'NCEC-2026-1005',
                'authority_name' => 'المركز الوطني للرقابة على الالتزام البيئي',
                'permit_number' => 'PER-2026-5005',
                'permit_issued_at' => now()->subDays(2)->toDateString(),
                'permit_expires_at' => now()->addYear()->toDateString(),
                'permit_needs_renewal' => false,
                'started_at' => now()->subDays(20)->toDateString(),
                'expected_delivery_at' => now()->subDays(2)->toDateString(),
                'completed_at' => now()->subDays(2),
                'notes' => 'معاملة تجريبية مكتملة وصدر لها تصريح.',
            ],
        ];

        foreach ($transactions as $transactionData) {
            $client = collect($createdClients)->firstWhere('name', $transactionData['client']);
            $type = $types->get($transactionData['type']);

            if (! $client || ! $type) {
                continue;
            }

            $transaction = Transaction::query()->firstOrCreate(
                ['reference_number' => $transactionData['reference_number']],
                [
                    'client_id' => $client->id,
                    'transaction_type_id' => $type->id,
                    'title' => $transactionData['title'],
                    'status' => $transactionData['status'],
                    'project_name' => $transactionData['project_name'],
                    'city' => $transactionData['city'],
                    'region' => $transactionData['region'],
                    'project_location' => $transactionData['project_location'],
                    'activity_type' => $transactionData['activity_type'],
                    'activity_code' => $transactionData['activity_code'],
                    'category' => $transactionData['category'],
                    'center_request_number' => $transactionData['center_request_number'],
                    'authority_name' => $transactionData['authority_name'],
                    'permit_number' => $transactionData['permit_number'] ?? null,
                    'permit_issued_at' => $transactionData['permit_issued_at'] ?? null,
                    'permit_expires_at' => $transactionData['permit_expires_at'] ?? null,
                    'permit_needs_renewal' => $transactionData['permit_needs_renewal'] ?? false,
                    'assigned_to' => $admin?->id,
                    'technical_manager_id' => $admin?->id,
                    'coordinator_id' => $admin?->id,
                    'financial_user_id' => $admin?->id,
                    'started_at' => $transactionData['started_at'],
                    'expected_delivery_at' => $transactionData['expected_delivery_at'],
                    'completed_at' => $transactionData['completed_at'] ?? null,
                    'notes' => $transactionData['notes'],
                    'created_by' => $admin?->id,
                ]
            );

            $this->createDocumentsForTransaction($transaction);
        }
    }

    private function createDocumentsForTransaction(Transaction $transaction): void
    {
        $requirements = $transaction->transactionType
            ->documentRequirements()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        foreach ($requirements as $requirement) {
            TransactionDocument::query()->firstOrCreate(
                [
                    'transaction_id' => $transaction->id,
                    'document_requirement_id' => $requirement->id,
                ],
                [
                    'name' => $requirement->name,
                    'status' => 'ناقص',
                ]
            );
        }
    }
}