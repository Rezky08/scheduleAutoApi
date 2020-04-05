<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcessLogDetail extends Model
{
    use SoftDeletes;
    protected $table = 'process_log_detail';
    protected $dates = ['deleted_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public $timestamps = true;
    public function process_log()
    {
        return $this->belongsTo(ProcessLog::class, 'process_log_id', 'id');
    }
}
