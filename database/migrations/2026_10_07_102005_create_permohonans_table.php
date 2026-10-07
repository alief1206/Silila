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
        Schema::create('permohonans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pemohon');
            $table->string('nik');
            $table->string('no_hp');
            $table->string('alamat_pemohon');
            $table->string('kecamatan');
            $table->string('desa');
            $table->string('alamat_lahan');
            $table->string('luas_lahan');
            $table->string('koordinat')->nullable();
            $table->string('file_surat_permohonan');
            $table->string('file_ktp');
            $table->string('file_petok_c');
            $table->string('file_skt_kades');
            $table->string('file_penguasaan_fisik');
            $table->string('file_shp')->nullable();
            $table->enum('status', ['pending', 'ditinjau', 'disetujui', 'ditolak'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonans');
    }
};
