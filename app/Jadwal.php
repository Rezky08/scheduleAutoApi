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
    public $relatedModel = [
        'update' => [
            [JadwalDetail::class, 'jadwal_id', 'id']
        ],
        'delete' => [
            'peminat_detail', 'kelompok_dosen'
        ]
    ];

    public function jadwal_detail()
    {
        return $this->hasMany(JadwalDetail::class, 'jadwal_id', 'id');
    }
    public function semester_detail()
    {
        return $this->belongsTo(SemesterDetail::class, 'semester', 'semester');
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
