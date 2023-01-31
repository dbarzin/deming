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

    // Return the measure associated to this control
    public function measure()
    {
        return $this->belongsTo(Measure::class, 'measure_id');
    }
}
