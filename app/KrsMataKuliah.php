<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KrsMataKuliah extends Model
{
    use SoftDeletes;
    protected $table = "krs_mata_kuliah";
    protected $dates = ['created_at', 'updated_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function krs()
    {
        return $this->belongsTo('App\Krs', 'kode_krs', 'kode_krs');
    }
    public function matkul()
    {
        return $this->hasOne(Matakuliah::class, 'kode_matkul', 'kode_matkul');
    }
}
