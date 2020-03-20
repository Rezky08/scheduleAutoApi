<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Krs extends Model
{
    use SoftDeletes;
    protected $table = "krs";
    protected $dates = ['created_at','updated_at'];

    public function krs_matkul()
    {
        return $this->hasMany('App\KrsMataKuliah','kode_krs','kode_krs');
    }
}
