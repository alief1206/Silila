<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('geometri', function (Blueprint $table) {
            $table->id('id',11);
            $table->longText('koordinat');
            $table->tinyInteger('tipe')->comment('1: LP2B, 2: LSD, 3: LBS');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('geometri');
    }
};
