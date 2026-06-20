<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@nk-erp.com'],
            [
                'name' => 'CEO Admin',
                'password' => 'password',
            ]
        );

        $admin->assignRole('CEO');
    }
}