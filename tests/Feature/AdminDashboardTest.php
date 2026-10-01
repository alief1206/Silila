<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_tamu_tidak_bisa_akses_dashboard_dan_diarahkan_ke_login()
    {
        // Mencoba akses dashboard tanpa login (sebagai tamu)
        $response = $this->get('/dashboard');

        // Sesuai aplikasi Silila, tamu diarahkan ke halaman utama (/)
        $response->assertRedirect('/');
    }

    public function test_admin_yang_sudah_login_bisa_akses_dashboard()
    {
        // Ambil data seeder admin
        $this->seed(\Database\Seeders\SuperAdminSeeder::class);
        $admin = User::where('nip', '123456789012345678')->first();

        // Simulasi login menggunakan actingAs, lalu akses dashboard
        $response = $this->actingAs($admin)->get('/dashboard');

        // Pastikan halaman dashboard berhasil dimuat (status 200)
        $response->assertStatus(200);
    }
}