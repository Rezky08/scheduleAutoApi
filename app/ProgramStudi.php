<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramStudi extends Model
{
    use SoftDeletes;
    protected $table = 'program_studi';
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $guarded = ['deleted_at'];
}
