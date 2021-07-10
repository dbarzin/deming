<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
    
    // return the domain associated to this Control
    public function domain(int $id)    
    {
        return Domain::find($id);
    }

    // check if there is a measuremetn associated with this control

    public function isActive(int $id)
    {
        return DB::table('measurements')
            ->where('control_id', $id)
            ->whereNull('realisation_date')
            ->exists(); 
    }
}
