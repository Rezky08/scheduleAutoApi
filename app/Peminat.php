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

    public function semester_detail()
    {
        return $this->belongsTo(SemesterDetail::class, 'semester', 'semester');
    }
}
