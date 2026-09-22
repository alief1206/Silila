<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. admin@silila.com (Super Admin)
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@silila.com'],
            [
                'nama' => 'Super Admin',
                'password' => bcrypt('password123'),
                'role' => '1',
                'nip' => '123456789012345678',
                'telp' => '081234567890',
            ]
        );

        // 2. superadmin@gmail.com (Super Administrator)
        \App\Models\User::updateOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'nama' => 'Super Administrator',
                'password' => bcrypt('password123'),
                'role' => '1',
                'nip' => '0000000000',
                'telp' => '0000000000',
            ]
        );

        // 3. admin_chat@silila.com (Admin Chat)
        \App\Models\User::updateOrCreate(
            ['email' => 'admin_chat@silila.com'],
            [
                'nama' => 'Admin Chat',
                'password' => bcrypt('password123'),
                'role' => '3',
                'nip' => 'admin',
                'telp' => '08123456789',
            ]
        );
    }
}
