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
}
