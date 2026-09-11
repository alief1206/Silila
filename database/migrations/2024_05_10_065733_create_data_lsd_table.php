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
        Schema::create('data_lsd', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('geometri_id')->unsigned();
            $table->string('hutan', 100);
            $table->string('luas', 100);
            $table->string('ba', 100);
            $table->string('luascea_hm', 100)->nullable();
            $table->timestamps();
            $table->foreign('geometri_id')->references('id')->on('geometri')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_lsd');
    }
};
