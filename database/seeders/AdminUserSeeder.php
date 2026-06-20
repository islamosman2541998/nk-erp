<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@nk-erp.com'],
            [
                'name' => 'CEO Admin',
                'password' => Hash::make('password'),
            ]
        );

        $admin->syncRoles(['CEO']);
    }
}