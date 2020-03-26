<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jadwal extends Model
{
    use SoftDeletes;
    protected $table = 'jadwal';
    protected $dates = ['created_at', 'updated_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public function jadwal_detail()
    {
        return $this->hasMany(JadwalDetail::class, 'kode_jadwal', 'kode_jadwal');
    }
}
