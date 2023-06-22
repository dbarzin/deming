<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Control extends Model
{
    public static $searchable = [
        'name',
        'clause',
        'objective',
        'input',
        'attributes',
        'model',
        'action_plan',
        'realisation_date',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_id');
    }

    public function measure()
    {
        return $this->belongsTo(Measure::class, 'measure_id');
    }

    public function owners()
    {
        return $this->belongsToMany(User::class,'control_user', 'control_id')->orderBy('name');
    }
}
