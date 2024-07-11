<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Control extends Model
{
    public static $searchable = [
        'name',
        'clause',
        'objective',
        'observations',
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

    // Control status :
    // O - Todo => relisation date null
    // 1 - Proposed by auditee => relisation date not null
    // 2 - Done => relisation date not null

    /*
    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_id');
    }

    // deprecated
    public function measure()
    {
        return $this->belongsTo(Measure::class, 'measure_id');
    }
    */
    public function measures()
    {
        return $this->belongsToMany(Measure::class)->orderBy('clause');
    }

    public function owners()
    {
        return $this->belongsToMany(User::class, 'control_user', 'control_id')->orderBy('name');
    }


    public static function clauses(int $id) {
        return DB::table('measures')
            ->select('measure_id','clause')
            ->join('control_measure', 'control_measure.control_id', $id)
            ->get();

    }

}
