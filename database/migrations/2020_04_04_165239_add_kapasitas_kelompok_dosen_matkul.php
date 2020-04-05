<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKapasitasKelompokDosenMatkul extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kelompok_dosen_detail', function (Blueprint $table) {
            $table->integer('kapasitas')->after('kelompok');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kelompok_dosen_detail', function (Blueprint $table) {
            $table->dropColumn('kapasitas');
        });
    }
}
