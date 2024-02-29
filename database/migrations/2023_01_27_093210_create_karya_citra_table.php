<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKaryaCitraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('karya_citra', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nama_karya')->nullable(false);
            $table->text('caption')->nullable(true);
            $table->string('karya')->nullable(false);
            $table->string('slug')->unique()->nullable(false);
            $table->string('excerpt')->nullable(true);
            $table->string('status')->nullable(true);
            $table->integer('jumlah_like')->default(0);
            $table->integer('id_siswa');
            $table->foreign('id_siswa')->references('id')->on('siswa')->onDelete('cascade');
            $table->integer('kategori_karya_citra_id');
            $table->foreign('kategori_karya_citra_id')->references('id')->on('kategori_karya_citra')->onDelete('cascade');
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
        Schema::dropIfExists('karya_citra');
    }
}
