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

    // Computer progress history of an action
    public function history()
    {
        $logs = AuditLog::where('subject_type', Action::class)
                        ->where('subject_id', $this->id)
                        ->orderBy('created_at', 'asc')
                        ->get();

        $history = [];
        $lastProgressDate = null;

        foreach ($logs as $log) {
            $properties = json_decode($log->properties[0], true);

            if (!isset($properties['progress'])) {
                continue;
            }

            $progress = (int) $properties['progress'];
            $progressDate = $log->created_at->toDateString();

            // Same day ?
            if ($progressDate == $lastProgressDate) {
                // replace last value
                end($history)['progress'] = $progress;
            }
            else {
                // add history
                $history[] = [
                    'date' => $progressDate,
                    'progress' => $progress,
                ];
                // save last date
                $lastProgressDate = $progressDate;
            }
        }

        return $history;
    }

}
