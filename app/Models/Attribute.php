<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Attribute extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name', 'values',
    ];

    /**
     * The dates used in this class
     *
     * @var list<string>
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
