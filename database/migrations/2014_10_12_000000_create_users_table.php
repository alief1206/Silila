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
        Schema::create('users', function (Blueprint $table) {
            $table->id('id',11);
            $table->string('email', 150)->unique();
            $table->string('password', 255);
            $table->string('nama', 150);
            $table->text('foto')->nullable();
            $table->string('nip', 18)->unique();
            $table->string('telp', 15);
            $table->tinyInteger('role')->comment('1: Admin, 2: User');
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
        Schema::dropIfExists('users');
    }
};
