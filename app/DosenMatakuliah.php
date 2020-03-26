<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DosenMatakuliah extends Model
{
    use SoftDeletes;
    protected $table = 'dosen_mata_kuliah';
    protected $dates = ['created_at', 'updated_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'kode_dosen', 'kode_dosen');
    }
    public function matkul()
    {
        return $this->belongsTo(Matakuliah::class, 'kode_matkul', 'kode_matkul');
    }
}
