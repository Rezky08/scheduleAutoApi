<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKrsMataKuliahTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('krs_mata_kuliah', function (Blueprint $table) {
            $table->id();
            $table->string('kode_krs', 10);
            $table->string('kode_matkul', 10);
            $table->integer('jumlah_krs');
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
        Schema::dropIfExists('krs_mata_kuliah');
    }
}
