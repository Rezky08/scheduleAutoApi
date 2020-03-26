<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JadwalDetail extends Model
{

    use SoftDeletes;
    protected $table = 'jadwal_detail';
    protected $dates = ['created_at', 'updated_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'kode_jadwal', 'kode_jadwal');
    }
}
