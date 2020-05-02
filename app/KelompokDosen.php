<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KelompokDosen extends Model
{
    use SoftDeletes;
    protected $table = 'kelompok_dosen';
    protected $dates = ['deleted_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public $timestamps = true;
    public $relatedModel = [
        'update' => [
            [KelompokDosenDetail::class, 'kelompok_dosen_id', 'id']
        ],
        'delete' => [
            'detail'
        ]
    ];

    public function peminat()
    {
        return $this->belongsTo(Peminat::class, 'peminat_id', 'id');
    }
    public function detail()
    {
        return $this->hasMany(KelompokDosenDetail::class, 'kelompok_dosen_id', 'id');
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
