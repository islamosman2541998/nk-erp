<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            // Dashboard
            'view dashboard',

            // Users & Roles
            'manage users',
            'manage roles',

            // Clients
            'view clients',
            'create clients',
            'edit clients',
            'delete clients',

            // Transaction Types
            'view transaction types',
            'create transaction types',
            'edit transaction types',
            'delete transaction types',

            // Transactions
            'view transactions',
            'view assigned transactions',
            'create transactions',
            'edit transactions',
            'delete transactions',
            'close transactions',
            'change transaction status',

            // Attachments / Documents
            'view attachments',
            'upload attachments',
            'review attachments',
            'delete attachments',

            // Archive
            'view archive',
            'restore archive',

            // Contracts
            'view contracts',
            'create contracts',
            'edit contracts',
            'delete contracts',

            // Payments
            'view payments',
            'create payments',
            'edit payments',
            'delete payments',

            // Expenses
            'view expenses',
            'create expenses',
            'edit expenses',
            'delete expenses',

            // Commissions
            'view commissions',
            'create commissions',
            'edit commissions',
            'delete commissions',

            // Reports / Logs
            'view reports',
            'view audit logs',

            // Settings
            'edit settings',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $roles = [
            'CEO',
            'Technical Manager',
            'Technical Employee',
            'Financial Manager',
            'Coordinator',
            'Limited Employee',
        ];

        foreach ($roles as $role) {
            Role::findOrCreate($role, 'web');
        }

        /*
        |--------------------------------------------------------------------------
        | CEO - المدير العام
        |--------------------------------------------------------------------------
        | كل الصلاحيات
        */
        Role::findByName('CEO', 'web')
            ->syncPermissions(Permission::all());

        /*
        |--------------------------------------------------------------------------
        | Technical Manager - المدير الفني
        |--------------------------------------------------------------------------
        | يرى كل المعاملات الفنية، يراجع المستندات، يغير الحالة، ويغلق المعاملة.
        | لا نعطيه صلاحيات الماليات إلا عرض العقد فقط لو محتاج يعرف سياق المعاملة.
        */
        Role::findByName('Technical Manager', 'web')
            ->syncPermissions([
                'view dashboard',

                'view clients',

                'view transaction types',

                'view transactions',
                'create transactions',
                'edit transactions',
                'close transactions',
                'change transaction status',

                'view attachments',
                'upload attachments',
                'review attachments',
                'delete attachments',

                'view archive',
                'restore archive',

                'view contracts',

                'view reports',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Technical Employee - الموظف الفني
        |--------------------------------------------------------------------------
        | يرى المعاملات المسندة له فقط، ويرفع مستندات.
        | لا يرى الماليات ولا كل المعاملات.
        */
        Role::findByName('Technical Employee', 'web')
            ->syncPermissions([
                'view dashboard',

                'view clients',

                'view assigned transactions',

                'view attachments',
                'upload attachments',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Financial Manager - المدير المالي
        |--------------------------------------------------------------------------
        | يرى المعاملات والماليات: عقود، دفعات، مصروفات، عمولات، تقارير.
        */
        Role::findByName('Financial Manager', 'web')
            ->syncPermissions([
                'view dashboard',

                'view clients',

                'view transactions',

                'view contracts',
                'create contracts',
                'edit contracts',
                'delete contracts',

                'view payments',
                'create payments',
                'edit payments',
                'delete payments',

                'view expenses',
                'create expenses',
                'edit expenses',
                'delete expenses',

                'view commissions',
                'create commissions',
                'edit commissions',
                'delete commissions',

                'view attachments',

                'view archive',

                'view reports',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Coordinator - المنسق
        |--------------------------------------------------------------------------
        | ينشئ العملاء والمعاملات، يرفع المستندات، ويتابع التنفيذ.
        | لا يدير الماليات بالكامل.
        */
        Role::findByName('Coordinator', 'web')
            ->syncPermissions([
                'view dashboard',

                'view clients',
                'create clients',
                'edit clients',

                'view transaction types',

                'view transactions',
                'create transactions',
                'edit transactions',
                'change transaction status',

                'view attachments',
                'upload attachments',

                'view contracts',

                'view archive',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Limited Employee - موظف محدود
        |--------------------------------------------------------------------------
        | أقل صلاحيات: يرى المعاملات المسندة فقط ويرفع مستندات.
        */
        Role::findByName('Limited Employee', 'web')
            ->syncPermissions([
                'view dashboard',

                'view assigned transactions',

                'view attachments',
                'upload attachments',
            ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}