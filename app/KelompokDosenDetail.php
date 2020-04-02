<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KelompokDosenDetail extends Model
{
    use SoftDeletes;
    protected $table = 'kelompok_dosen_detail';
    protected $dates = ['deleted_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public $timestamps = true;
    public function kelompok_dosen()
    {
        return $this->belongsTo(KelompokDosen::class, 'kelompok_dosen_id', 'id');
    }
    public function mata_kuliah()
    {
        return $this->belongsTo(Matakuliah::class, 'kode_matkul', 'kode_matkul');
    }
    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'kode_dosen', 'kode_dosen');
    }
}
