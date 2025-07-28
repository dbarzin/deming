<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    use Auditable;

    public static $searchable = [
        'reference',
        'type',
        'scope',
        'cause',
        'remediation',
        'justification',
    ];

    protected $dates = [
        'creation_date',
        'due_date',
        'close_date',
    ];

    protected $fillable = [
        'reference',
        'type',
        'progress',
        'criticity',
        'scope',
        'cause',
        'remediation',
        'creation_date',
        'due_date',
        'close_date',
        'justification',
    ];

    // Control status :
    // O - Open
    // 1 - Closed
    // 2 - Rejected

    // Type
    // 1 - Major - red
    // 2 - Minor - organge
    // 3 - Observation - yellow
    // 4 - Opportunity - green

    public function owners()
    {
        return $this->belongsToMany(User::class, 'action_user', 'action_id')->orderBy('name');
    }

    public function measures()
    {
        return $this->belongsToMany(Measure::class, 'action_measure', 'action_id');
    }
}
