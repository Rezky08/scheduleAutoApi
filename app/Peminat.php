<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Peminat extends Model
{
    use SoftDeletes;
    protected $table = 'peminat';
    protected $dates = ['deleted_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public $timestamps = true;

    public $relatedModel = [
        'update' => [
            [PeminatDetail::class, 'peminat_id', 'id'],
            [KelompokDosenDetail::class, 'peminat_id', 'id']
        ],
        'delete' => [
            'peminat_detail', 'kelompok_dosen'
        ]
    ];

    public function semester_detail()
    {
        return $this->belongsTo(SemesterDetail::class, 'semester', 'semester');
    }
    public function kelompok_dosen()
    {
        return $this->hasMany(KelompokDosen::class, 'peminat_id', 'id');
    }
    public function peminat_detail()
    {
        return $this->hasMany(PeminatDetail::class, 'peminat_id', 'id');
    }
}
