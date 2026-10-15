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
        // 1. Tambah object_id pada tabel geometri jika belum ada
        Schema::table('geometri', function (Blueprint $table) {
            if (!Schema::hasColumn('geometri', 'object_id')) {
                $table->string('object_id', 100)->nullable()->index()->after('id');
            }
        });

        // 2. Tambah kolom-kolom atribut dan properties JSON pada tabel data_lsd
        Schema::table('data_lsd', function (Blueprint $table) {
            if (!Schema::hasColumn('data_lsd', 'status_lsd')) {
                $table->string('status_lsd', 100)->nullable()->after('geometri_id');
            }
            if (!Schema::hasColumn('data_lsd', 'irigasi')) {
                $table->string('irigasi', 100)->nullable()->after('luas');
            }
            if (!Schema::hasColumn('data_lsd', 'kewenangan')) {
                $table->string('kewenangan', 100)->nullable()->after('irigasi');
            }
            if (!Schema::hasColumn('data_lsd', 'polru')) {
                $table->string('polru', 150)->nullable()->after('kewenangan');
            }
            if (!Schema::hasColumn('data_lsd', 'ip')) {
                $table->string('ip', 50)->nullable()->after('polru');
            }
            if (!Schema::hasColumn('data_lsd', 'prod')) {
                $table->string('prod', 50)->nullable()->after('ip');
            }
            if (!Schema::hasColumn('data_lsd', 'tipehak')) {
                $table->string('tipehak', 100)->nullable()->after('prod');
            }
            if (!Schema::hasColumn('data_lsd', 'investasi')) {
                $table->string('investasi', 100)->nullable()->after('tipehak');
            }
            if (!Schema::hasColumn('data_lsd', 'properties')) {
                $table->longText('properties')->nullable()->comment('Seluruh 33 atribut mentah GIS')->after('investasi');
            }
            if (!Schema::hasColumn('data_lsd', 'properties_hash')) {
                $table->string('properties_hash', 64)->nullable()->index()->after('properties');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_lsd', function (Blueprint $table) {
            $columns = [
                'status_lsd', 'irigasi', 'kewenangan', 'polru', 'ip',
                'prod', 'tipehak', 'investasi', 'properties', 'properties_hash'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('data_lsd', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('geometri', function (Blueprint $table) {
            if (Schema::hasColumn('geometri', 'object_id')) {
                $table->dropColumn('object_id');
            }
        });
    }
};
