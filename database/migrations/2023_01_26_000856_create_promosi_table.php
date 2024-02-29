<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromosiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promosi', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nama_promosi');
            $table->text('keterangan');
            $table->string('gambar');
            $table->date('tanggal_promosi');
            $table->enum('status', ['AKTIF', 'TIDAK AKTIF']);
            $table->integer('tim_ppdb_id');
            $table->foreign('tim_ppdb_id')->references('id')->on('tim_ppdb')->onDelete('cascade');
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
        Schema::dropIfExists('promosi');
    }
}
