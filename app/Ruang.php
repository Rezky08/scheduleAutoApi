<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ruang extends Model
{
    use SoftDeletes;
    protected $table = 'ruang';
    protected $dates = ['deleted_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
