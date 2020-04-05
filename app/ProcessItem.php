<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcessItem extends Model
{
    use SoftDeletes;
    protected $table = 'process_item';
    protected $dates = ['deleted_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public $timestamps = true;
    public $relatedModel = [
        'update' => [
            [ProcessLog::class, 'process_item_id', 'id']
        ],
        'delete' => [
            'process_log',
        ]
    ];

    public function process_log()
    {
        return $this->hasMany(ProcessLog::class, 'process_item_id', 'id');
    }
}
