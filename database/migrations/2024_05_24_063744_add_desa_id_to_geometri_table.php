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
        Schema::table('geometri', function (Blueprint $table) {
            $table->bigInteger('desa_id')->after('id')->unsigned()->nullable();

            $table->foreign('desa_id')->references('id')->on('desa')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('geometri', function (Blueprint $table) {
            $table->dropForeign('geometri_desa_id_foreign');
            $table->dropColumn('desa_id');
        });
    }
};
