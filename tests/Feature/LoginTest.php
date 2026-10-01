<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_halaman_login_admin_bisa_diakses()
    {
        // Mengirim permintaan ke halaman login
        $response = $this->get('/login');

        // Memastikan halaman login berhasil dimuat dengan status 200
        $response->assertStatus(200);
    }
}