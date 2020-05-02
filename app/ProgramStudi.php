<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ProgramStudi extends Model
{
    use SoftDeletes;
    protected $table = 'program_studi';
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $guarded = ['deleted_at'];
    public $relatedModel = [
        'update' => [
            [Matakuliah::class, 'kode_prodi', 'kode_prodi']
        ],
        'delete' => [
            'mata_kuliah'
        ]
    ];
    public function mata_kuliah()
    {
        return $this->hasMany(Matakuliah::class, 'kode_prodi', 'kode_prodi');
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
