<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PeminatDetail extends Model
{
    use SoftDeletes;
    protected $table = 'peminat_detail';
    protected $dates = ['deleted_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = ['kode_matkul', 'jumlah_peminat'];
    public $timestamps = true;
    protected $casts = [
        'jumlah_peminat' => 'integer'
    ];
    public function mata_kuliah()
    {
        return $this->BelongsTo(Matakuliah::class, 'kode_matkul', 'kode_matkul');
    }
    public function peminat()
    {
        return $this->belongsTo(Peminat::class, 'peminat_id', 'id');
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
