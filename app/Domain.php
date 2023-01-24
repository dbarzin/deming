<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    public static $searchable = [
        'title',
        'description',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
    ];
}
