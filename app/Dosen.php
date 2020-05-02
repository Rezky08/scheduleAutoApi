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
    public $relatedModel = [
        'update' => [
            [DosenMatakuliah::class, 'kode_dosen'],
            [KelompokDosenDetail::class, 'kode_dosen']
        ],
        'delete' => [
            'dosen_matkul', 'kelompok_dosen'
        ]
    ];


    public function dosen_matkul()
    {
        return $this->hasMany(DosenMatakuliah::class, 'kode_dosen', 'kode_dosen');
    }
    public function kelompok_dosen()
    {
        return $this->hasMany(KelompokDosenDetail::class, 'kode_dosen', 'kode_dosen');
    }

    public function getTableColumns()
    {
        $columns = $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
        $columns = collect($columns)->except($this->hidden);
        $columns = $columns->filter(function ($item, $key) {
            if (!in_array($item, $this->hidden)) {
                return $item;
            }
        })->toArray();
        return $columns;
    }
}
