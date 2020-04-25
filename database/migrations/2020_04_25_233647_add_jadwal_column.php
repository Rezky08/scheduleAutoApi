<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJadwalColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jadwal_detail', function (Blueprint $table) {
            $table->string('nama_matkul', 100)->after('kode_matkul');
            $table->integer('sks_matkul')->after('nama_matkul');
            $table->string('nama_dosen', 100)->after('kode_dosen');
            $table->time('sesi_mulai')->after('ruang');
            $table->time('sesi_selesai')->after('sesi_mulai');
            $table->dropColumn('sesi');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jadwal_detail', function (Blueprint $table) {
            $table->dropColumn('nama_matkul');
            $table->dropColumn('sks_matkul');
            $table->dropColumn('nama_dosen');
            $table->dropColumn('sesi_mulai');
            $table->dropColumn('sesi_selesai');
            $table->string('sesi');
        });
    }
}
