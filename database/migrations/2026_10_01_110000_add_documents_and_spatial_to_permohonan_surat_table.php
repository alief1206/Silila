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
        Schema::table('permohonan_surat', function (Blueprint $table) {
            $table->string('file_ktp')->nullable()->after('dokumen_pendukung');
            $table->string('file_petok_c')->nullable()->after('file_ktp');
            $table->string('file_skt_kades')->nullable()->after('file_petok_c');
            $table->string('file_penguasaan_fisik')->nullable()->after('file_skt_kades');
            $table->string('file_shp')->nullable()->after('file_penguasaan_fisik');
            $table->longText('geojson_polygon')->nullable()->after('file_shp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_surat', function (Blueprint $table) {
            $table->dropColumn([
                'file_ktp',
                'file_petok_c',
                'file_skt_kades',
                'file_penguasaan_fisik',
                'file_shp',
                'geojson_polygon'
            ]);
        });
    }
};
