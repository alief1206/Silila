<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AdminRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_chat_bisa_login_dengan_nip_admin()
    {
        // 1. Panggil SuperAdminSeeder agar data admin & admin chat masuk ke database testing
        $this->seed(\Database\Seeders\SuperAdminSeeder::class);

        // 2. Simulasi login menggunakan kredensial Admin Chat dari seeder (NIP: 'admin')
        $response = $this->post('/login', [
            'nip' => 'admin',
            'password' => 'password123',
        ]);

        // 3. Pastikan sistem mengenali user tersebut terautentikasi
        $this->assertAuthenticated();

        // 4. Pastikan diarahkan ke dashboard setelah berhasil masuk
        $response->assertRedirect('/dashboard');
    }
}