<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKrsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('krs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_krs', 10);
            $table->enum('semester', ['E', 'O'])->default('O');
            $table->string('tahun_ajar', 10);
            $table->timestamps();
            $table->softDeletes();
            $table->unique('kode_krs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('krs');
    }
}
