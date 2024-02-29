<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiswaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nama_lengkap', 100);
            $table->string('nisn', 10)->unique()->max(10);
            $table->char('jenis_kelamin', 1);
            $table->string('agama', 50)->max(50);
            $table->string('foto_profil', 100);
            $table->date('ttl');
            $table->text('alamat');
            $table->integer('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('siswa');
    }
}
