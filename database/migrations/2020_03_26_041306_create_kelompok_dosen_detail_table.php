<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKelompokDosenDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kelompok_dosen_detail', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('kelompok_dosen_id');
            $table->string('kode_matkul', 10);
            $table->string('kelompok', 5);
            $table->string('kode_dosen', 10);
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
        Schema::dropIfExists('kelompok_dosen_detail');
    }
}
