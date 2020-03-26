<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJadwalDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jadwal_detail', function (Blueprint $table) {
            $table->id();
            $table->string('kode_matkul', 10);
            $table->string('kelompok', 5);
            $table->string('kode_dosen', 10);
            $table->string('ruang', 100);
            $table->string('hari', 100);
            $table->string('sesi', 100);
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
        Schema::dropIfExists('jadwal_detail');
    }
}
