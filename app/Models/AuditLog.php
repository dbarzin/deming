<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * App\AuditLog
 */
class AuditLog extends Model
{
    public $table = 'audit_logs';

    protected $fillable = [
        'description',
        'subject_id',
        'subject_type',
        'user_id',
        'properties',
        'host',
    ];

    protected $casts = [
        'properties' => 'collection',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subjectURL()
    {
        // Trouver la dernière occurrence de "\"
        $position = strrpos($this->subject_type, '\\');

        // Extraire ce qui suit si "\" est trouvé
        return $position !== false ? substr($this->subject_type, $position + 1) : $this->subject_type;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
