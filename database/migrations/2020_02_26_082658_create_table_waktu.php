<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableWaktu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('waktu', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('kode_hari')->unsigned()->nullable()->default(12);
            $table->integer('kode_jam')->unsigned()->nullable()->default(12);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('waktu');
    }
}
