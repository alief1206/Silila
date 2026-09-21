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
    }
}
