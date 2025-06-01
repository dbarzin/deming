<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function (Model $model) {
            static::audit('created', $model);
        });

        static::updated(function (Model $model) {
            static::audit('updated', $model);
        });

        static::deleted(function (Model $model) {
            static::audit('deleted', $model);
        });
    }

    protected static function audit($description, $model)
    {
        AuditLog::create([
            'description' => $description,
            'subject_id' => $model->id ?? null,
            'subject_type' => $model::class ?? null,
            'user_id' => auth()->id() ?? null,
            'properties' => substr($model instanceof Model ? $model->toJson() : '', 0, 65534),
            'host' => request()->ip() ?? null,
        ]);
    }
}
