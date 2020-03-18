<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProgramStudi extends Model
{
    protected $table = 'program_studi';
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $guarded = ['deleted_at'];
}
