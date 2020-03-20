<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSemesterColumnToMataKuliah extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mata_kuliah', function (Blueprint $table) {
            $table->enum('semester', ['E', 'O'])->after('lab_matkul')->default('O');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mata_kuliah', function (Blueprint $table) {
            $table->dropColumn('semester');
        });
    }
}
