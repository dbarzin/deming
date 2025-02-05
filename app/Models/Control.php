<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Control extends Model
{
    use Auditable;

    public static $searchable = [
        'name',
        'objective',
        'observations',
        'input',
        'attributes',
        'model',
        'action_plan',
        'plan_date'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'objective',
        'observations',
        'input',
        'attributes',
        'model',
        'action_plan',
        'realisation_date',
        'plan_date',
        'periodicity'
    ];

    // Control status :
    // O - Todo => relisation date null
    // 1 - Proposed by auditee => relisation date not null
    // 2 - Done => relisation date not null

    public function measures()
    {
        return $this->belongsToMany(Measure::class)->orderBy('clause');
    }

    public function actionPlan()
    {
        return DB::table('actions')->select('id')->where("control_id",'=',$this->id)->get();
    }

    public function owners()
    {
        return $this->belongsToMany(User::class, 'control_user', 'control_id')->orderBy('name');
    }

    public static function clauses(int $id)
    {
        return DB::table('measures')
            ->select('measure_id', 'clause')
            ->join('control_measure', 'control_measure.control_id', $id)
            ->get();
    }
}
