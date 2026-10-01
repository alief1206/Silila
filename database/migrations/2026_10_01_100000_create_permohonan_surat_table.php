<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permohonan_surat', function (Blueprint $table) {
            $table->id();
            $table->string('kode_registrasi')->unique();
            $table->string('jenis_surat')->default('Surat Keterangan Kesesuaian Lahan (LP2B & LSD)');
            $table->string('nama_pemohon');
            $table->string('nik', 20);
            $table->string('no_hp', 25);
            $table->text('alamat_pemohon')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('desa')->nullable();
            $table->text('alamat_lahan')->nullable();
            $table->string('luas_lahan')->nullable();
            $table->string('koordinat')->nullable();
            $table->string('dokumen_pendukung')->nullable();
            $table->string('status')->default('Menunggu Verifikasi');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_surat');
    }
};
