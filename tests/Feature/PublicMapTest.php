<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicMapTest extends TestCase
{
    public function test_halaman_peta_publik_bisa_diakses()
    {
        // Mengirim permintaan (request) ke halaman utama situs Silila
        $response = $this->get('/');

        // Memastikan halaman berhasil dimuat dengan status HTTP 200 (OK)
        $response->assertStatus(200);
    }
}