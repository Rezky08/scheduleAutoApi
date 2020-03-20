<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KrsMataKuliah extends Model
{
    use SoftDeletes;
    protected $table = "krs_mata_kuliah";

    public function krs()
    {
        return $this->belongsTo('App\Krs','kode_krs','kode_krs');
    }
}
