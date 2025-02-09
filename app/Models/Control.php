<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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
        'plan_date',
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
        'periodicity',
    ];

    private $owners = null;

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
        return DB::table('actions')->select('id')->where('control_id', '=', $this->id)->get();
    }

    public function owners()
    {
        if ($this->owners === null) {
            $this->owners = $this->belongsToMany(User::class, 'control_user', 'control_id')->orderBy('name');
        }
        return $this->owners;
    }

    public function canMake()
    {
        if ($this->status !== 0) {
            return false;
        }

        // user or admin
        if ((Auth::User()->role === 1) || (Auth::User()->role === 2)) {
            return true;
        }

        // auditor or auditee
        if ((Auth::User()->role === 3) || (Auth::User()->role === 5)) {
            foreach ($this->owners()->get() as $owner) {
                if ($owner->id === Auth::User()->id) {
                    return true;
                }
            }
        }

        return false;
    }

    public function clauses(int $id)
    {
        return DB::table('measures')
            ->select('measure_id', 'clause')
            ->join('control_measure', 'control_measure.control_id', $id)
            ->get();
    }
}
