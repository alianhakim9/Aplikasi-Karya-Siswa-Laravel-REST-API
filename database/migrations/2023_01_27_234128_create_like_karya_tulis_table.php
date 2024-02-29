<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikeKaryaTulisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('like_karya_tulis', function (Blueprint $table) {
            $table->integer('id', true);
            $table->boolean('is_like');
            $table->integer('user_id');
            $table->integer('karya_tulis_id');
            $table->foreign('user_id')->on('users')->references('id')->onDelete('cascade');
            $table->foreign('karya_tulis_id')->on('karya_tulis')->references('id')->onDelete('cascade');
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
        Schema::dropIfExists('like_karya_tulis');
    }
}
