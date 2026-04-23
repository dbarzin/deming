<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle Exception (issue #590 – gestion des exceptions)
 *
 * @property int         $id
 * @property int|null    $measure_id
 * @property string      $name
 * @property string|null $description
 * @property string|null $justification
 * @property string|null $compensating_controls
 * @property \Carbon\Carbon|null $start_date
 * @property \Carbon\Carbon|null $end_date
 * @property int         $status
 * @property int|null    $created_by
 * @property int|null    $submitted_by
 * @property \Carbon\Carbon|null $submitted_at
 * @property int|null    $approved_by
 * @property \Carbon\Carbon|null $approved_at
 * @property string|null $approval_comment
 */
class Exception extends Model
{
    // ── Statuts du workflow ───────────────────────────────────────────────────
    public const STATUS_DRAFT     = 0;
    public const STATUS_SUBMITTED = 1;
    public const STATUS_APPROVED  = 2;
    public const STATUS_REJECTED  = 3;
    public const STATUS_EXPIRED   = 4;

    /** Labels traduits (clé = constante, valeur = clé de traduction) */
    public const STATUS_LABELS = [
        self::STATUS_DRAFT     => 'Brouillon',
        self::STATUS_SUBMITTED => 'Soumise',
        self::STATUS_APPROVED  => 'Approuvée',
        self::STATUS_REJECTED  => 'Refusée',
        self::STATUS_EXPIRED   => 'Expirée',
    ];

    // ── Fillable ──────────────────────────────────────────────────────────────
    protected $fillable = [
        'measure_id',
        'name',
        'description',
        'justification',
        'compensating_controls',
        'start_date',
        'end_date',
        'status',
        'created_by',
        'submitted_by',
        'submitted_at',
        'approved_by',
        'approved_at',
        'approval_comment',
    ];

    protected $casts = [
        'start_date'   => 'date',
        'end_date'     => 'date',
        'submitted_at' => 'datetime',
        'approved_at'  => 'datetime',
        'status'       => 'integer',
    ];

    // ── Relations ─────────────────────────────────────────────────────────────
    public function measure(): BelongsTo
    {
        return $this->belongsTo(Measure::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ── Accesseurs ────────────────────────────────────────────────────────────

    /** Libellé du statut courant */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? '?';
    }

    /** Vraie si la date de fin est dépassée et que l'exception est approuvée */
    public function getIsExpiredAttribute(): bool
    {
        return $this->end_date !== null
            && $this->end_date->isPast()
            && $this->status === self::STATUS_APPROVED;
    }

    // ── Business rules ────────────────────────────────────────────────────────

    /** L'exception peut-elle être soumise ? */
    public function canSubmit(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /** L'exception peut-elle être approuvée / refusée ? */
    public function canReview(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    /** L'exception peut-elle être éditée ? */
    public function canEdit(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REJECTED]);
    }
}
