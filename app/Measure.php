<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Measure extends Model
{
    public static $searchable = [
        'name',
        'clause',
        'objective',
        'input',
        'attributes',
        'model',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
    ];

    // Return the domain associated to this measure
    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_id');
    }

    // check if there is an empty control associated with this measure
    public function isActive(int $id)
    {
        return DB::table('controls')
            ->where('measure_id', $id)
            ->whereNull('realisation_date')
            ->exists();
    }
}
