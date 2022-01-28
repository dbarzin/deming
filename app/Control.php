<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Control extends Model
{
    public static $searchable = [
        'name',
        'clause',
        'objective',
        'attributes',
        'model'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
    ];
    
}



