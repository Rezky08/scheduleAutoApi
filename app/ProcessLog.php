<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcessLog extends Model
{
    use SoftDeletes;
    protected $table = 'process_log';
    protected $dates = ['deleted_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public $timestamps = true;
    public $relatedModel = [
        'update' => [
            [ProcessLogDetail::class, 'process_log_id', 'id'],
            [AlgenResultLog::class, 'process_log_id', 'id']
        ],
        'delete' => [
            'detail', 'algen_result'
        ]
    ];

    public function detail()
    {
        return $this->hasMany(ProcessLogDetail::class, 'process_log_id', 'id');
    }
    public function algen_result()
    {
        return $this->hasMany(AlgenResultLog::class, 'process_log_id', 'id');
    }
}
