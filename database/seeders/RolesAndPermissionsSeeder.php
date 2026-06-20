<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
  public function run(): void
{
    app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

    $permissions = [
        // Dashboard
        'view dashboard',

            // Users & Permissions
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage roles',
            'manage permissions',

            // Clients
            'view clients',
            'create clients',
            'edit clients',
            'delete clients',

            // Transactions
            'view transactions',
            'view assigned transactions',
            'create transactions',
            'edit transactions',
            'delete transactions',
            'close transactions',
            'change transaction status',

            // Transaction Documents & Attachments
            'view attachments',
            'upload attachments',
            'review attachments',
            'delete attachments',

            // Meetings & Team
            'view meetings',
            'create meetings',
            'edit meetings',
            'delete meetings',
            'manage transaction team',

            // Contracts
            'view contracts',
            'create contracts',
            'edit contracts',
            'delete contracts',

            // Payments / Revenue
            'view payments',
            'create payments',
            'edit payments',
            'delete payments',

            // Expenses
            'view expenses',
            'create expenses',
            'edit expenses',
            'delete expenses',
            'approve expenses',

            // Commissions
            'view commissions',
            'create commissions',
            'edit commissions',
            'approve commissions',
            'pay commissions',

            // Reports
            'view reports',
            'export reports',

            // Audit Log
            'view audit logs',

            // Settings
            'view settings',
            'edit settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $ceo = Role::firstOrCreate(['name' => 'CEO']);
        $technicalManager = Role::firstOrCreate(['name' => 'Technical Manager']);
        $technicalEmployee = Role::firstOrCreate(['name' => 'Technical Employee']);
        $financialManager = Role::firstOrCreate(['name' => 'Financial Manager']);
        $coordinator = Role::firstOrCreate(['name' => 'Coordinator']);
        $limitedEmployee = Role::firstOrCreate(['name' => 'Limited Employee']);

        // CEO بياخد كل الصلاحيات
        $ceo->syncPermissions(Permission::all());

        $technicalManager->syncPermissions([
            'view dashboard',
            'view clients',
            'view transactions',
            'create transactions',
            'edit transactions',
            'change transaction status',
            'view attachments',
            'upload attachments',
            'review attachments',
            'view meetings',
            'create meetings',
            'edit meetings',
            'manage transaction team',
            'view reports',
        ]);

        $technicalEmployee->syncPermissions([
            'view dashboard',
            'view clients',
            'view assigned transactions',
            'edit transactions',
            'view attachments',
            'upload attachments',
            'view meetings',
            'create meetings',
        ]);

        $financialManager->syncPermissions([
            'view dashboard',
            'view clients',
            'view transactions',
            'view contracts',
            'create contracts',
            'edit contracts',
            'view payments',
            'create payments',
            'edit payments',
            'view expenses',
            'create expenses',
            'edit expenses',
            'approve expenses',
            'view commissions',
            'approve commissions',
            'pay commissions',
            'view reports',
            'export reports',
        ]);

        $coordinator->syncPermissions([
            'view dashboard',
            'view clients',
            'create clients',
            'edit clients',
            'view transactions',
            'create transactions',
            'edit transactions',
            'change transaction status',
            'view attachments',
            'upload attachments',
            'view meetings',
            'create meetings',
            'edit meetings',
            'manage transaction team',
        ]);

        $limitedEmployee->syncPermissions([
            'view dashboard',
            'view assigned transactions',
            'view attachments',
            'upload attachments',
            'view meetings',
        ]);
    }
}