<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_bisa_login_dengan_data_seeder()
    {
        // 1. Panggil seeder agar data admin masuk ke database pengujian
        $this->seed(\Database\Seeders\SuperAdminSeeder::class);

        // 2. Kirim data login menggunakan NIP (sesuai aturan Silila dan SuperAdminSeeder)
        $response = $this->post('/login', [
            'nip' => '123456789012345678',
            'password' => 'password123',
        ]);

        // 3. Pastikan user berhasil login
        $this->assertAuthenticated();

        // 4. Pastikan diarahkan ke dashboard
        $response->assertRedirect('/dashboard');
    }
}