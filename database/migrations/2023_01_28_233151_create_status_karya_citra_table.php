<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusKaryaCitraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status_karya_citra', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_karya_citra');
            $table->foreign('id_karya_citra')->references('id')->on('karya_citra')->onDelete('cascade');
            $table->text('keterangan');
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
        Schema::dropIfExists('status_karya_citra');
    }
}
