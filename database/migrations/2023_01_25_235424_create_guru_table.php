<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuruTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guru', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nama_lengkap', 100);
            $table->string('nuptk', 16)->unique();
            $table->char('jenis_kelamin', 1);
            $table->string('agama', 50);
            $table->string('foto_profil', 100);
            $table->date('ttl');
            $table->text('alamat');
            $table->string('gelar', 50);
            $table->string('jabatan', 100);
            $table->integer('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('guru');
    }
}
