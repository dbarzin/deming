<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->orderBy('name');
    }

    public function controls()
    {
        return $this->belongsToMany(Control::class)->orderBy('plan_date');
    }
}
