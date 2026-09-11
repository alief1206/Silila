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
        Schema::create('data_lp2b', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('geometri_id')->unsigned();
            $table->string('kp2b', 50);
            $table->string('ket', 100)->nullable();
            $table->string('luas', 100);
            $table->timestamps();

            $table->foreign('geometri_id')->references('id')->on('geometri')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_lp2b');
    }
};
