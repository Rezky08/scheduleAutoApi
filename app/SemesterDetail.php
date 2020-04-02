<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SemesterDetail extends Model
{
    use SoftDeletes;
    protected $table = 'semester_detail';
    protected $dates = ['deleted_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public $timestamps = true;
    public $relatedModel = [
        'update' => [
            [Peminat::class, 'semester']
        ],
        'delete' => [
            'semester'
        ]
    ];
    public function semester()
    {
        return $this->hasMany(Peminat::class, 'semester', 'semester');
    }
}
