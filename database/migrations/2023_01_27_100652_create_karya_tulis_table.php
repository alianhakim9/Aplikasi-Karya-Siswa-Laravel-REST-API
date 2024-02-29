<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKaryaTulisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('karya_tulis', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('judul_karya')->nullable(false);
            $table->text('konten_karya')->nullable(false);
            $table->string('sumber')->nullable(true);
            $table->string('slug')->unique()->nullable(false);
            $table->string('excerpt')->nullable(false);
            $table->integer('jumlah_like')->default(0);
            $table->integer('id_siswa');
            $table->foreign('id_siswa')->references('id')->on('siswa')->onDelete('cascade');
            $table->integer('kategori_karya_tulis_id');
            $table->foreign('kategori_karya_tulis_id')->references('id')->on('kategori_karya_tulis')->onDelete('cascade');
            $table->timestamps();
            // $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('karya_tulis');
    }
}
