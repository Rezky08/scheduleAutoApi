<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Matakuliah extends Model
{
    use SoftDeletes;
    protected $table = 'mata_kuliah';
    protected $dates = ['deleted_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function dosen_matkul()
    {
        return $this->hasMany(DosenMatakuliah::class, 'kode_matkul', 'kode_matkul');
    }
    public function peminat()
    {
        return $this->hasMany(PeminatDetail::class, 'kode_matkul', 'kode_matkul');
    }
}
