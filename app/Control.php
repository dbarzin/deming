<?php

namespace App;

use App\Measure;

use Illuminate\Database\Eloquent\Model;

class Control extends Model
{
    public static $searchable = [
        'name',
        'clause',
        'objective',
        'attributes',
        'model',
        'action_plan',
        'realisation_date'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'observations',
        'clause',
        'objective',
        'attributes',
        'indicator',
        'model',
        'note',
        'score'
    ];

    // Return the measure associated to this control
    public function measure()
    {
        return $this->belongsTo(Measure::class, 'measure_id');
    }    
}



