<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDosenMatkulUniqueColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dosen_mata_kuliah', function (Blueprint $table) {
            $table->unique(['kode_matkul', 'kode_dosen']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dosen_mata_kuliah', function (Blueprint $table) {
            $table->dropUnique('dosen_mata_kuliah_kode_matkul_kode_dosen_unique');
        });
    }
}
