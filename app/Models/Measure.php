<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Measure extends Model
{
    use Auditable;

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
        'name',
        'clause',
        'objective',
        'input',
        'attributes',
        'model',
    ];

    // Return the domain associated to this measure
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class, 'domain_id');
    }

    // Return the controls associated to this measure
    public function controls(): BelongsToMany
    {
        return $this->belongsToMany(Control::class)
            ->whereNotNull('realisation_date')->orderBy('realisation_date');
    }

    // Check if there is an empty control associated with this measure
    public function isActive(): bool
    {
        return DB::table('controls')
            ->where('measure_id', $this->id)
            ->whereNull('realisation_date')
            ->exists();
    }

    // check if there is an empty control associated with this measure
    public function isDisabled(): bool
    {
        return DB::table('controls')
            ->where('measure_id', $this->id)
            ->exists()
        &&
            ! DB::table('controls')
                ->where('measure_id', $this->id)
                ->whereNull('realisation_date')
                ->exists();
    }
}
