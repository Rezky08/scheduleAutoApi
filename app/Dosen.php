<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dosen extends Model
{
    use SoftDeletes;
    protected $table = 'dosen';
    protected $dates = ['created_at', 'updated_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function dosen_matkul()
    {
        return $this->hasMany(DosenMatakuliah::class, 'kode_dosen', 'kode_dosen');
    }
}
