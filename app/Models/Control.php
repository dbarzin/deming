<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

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

    private $groups = null;
    private $users = null;

    // Control status :

    // O - Todo => relisation date null
    // 1 - Proposed by auditee => relisation date not null
    // 2 - Done => relisation date not null

    public function measures(): BelongsToMany
    {
        // return $this->belongsToMany(Measure::class,'control_measure','control_id')->orderBy('clause');
        return $this->belongsToMany(Measure::class)->orderBy('clause');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(Action::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function users(): BelongsToMany
    {
        if ($this->users === null) {
            $this->users = $this->belongsToMany(User::class, 'control_user', 'control_id')->orderBy('name');
        }
        return $this->users;
    }

    public function groups()
    {
        if ($this->groups === null) {
            $this->groups = $this->belongsToMany(UserGroup::class)->orderBy('name');
        }
        return $this->groups;
    }

    public function canMake(): bool
    {
        if ($this->status !== 0) {
            return false;
        }

        $user = Auth::user();

        if ($this->isAdminOrUser($user)) {
            return true;
        }

        if ($this->isAuditorOrAuditeeWithAccess($user)) {
            return true;
        }

        return false;
    }

    public function canValidate(): bool
    {
        if ($this->status !== 1) {
            return false;
        }

        $user = Auth::user();

        if ($this->isAdminOrUser($user)) {
            return true;
        }

        return false;
    }

    public function clauses(int $id)
    {
        return DB::table('measures')
            ->select('measure_id', 'clause')
            ->join('control_measure', 'control_measure.control_id', strval($id))
            ->get();
    }

    public static function cleanup(string $startDate, bool $dryRun)
    {
        // Initialise counters
        $documentCount = 0;
        $controlCount = 0;
        $logCount = 0;

        // Remove logs
        $logCount = AuditLog::where('created_at', '<', $startDate)->count();
        if (! $dryRun) {
            AuditLog::where('created_at', '<', $startDate)->delete();
        }

        // Get conctrols
        $oldControls = Control::whereNotNull('realisation_date')
            ->where('realisation_date', '<', $startDate)
            ->get();

        foreach ($oldControls as $control) {
            DB::transaction(function () use ($dryRun, $control, &$documentCount, &$controlCount) {
                // Supprimer les documents associés
                $documents = Document::where('control_id', $control->id)->get();

                foreach ($documents as $document) {
                    // Supprimer le fichier physique s'il existe
                    if (! $dryRun) {
                        $filePath = storage_path('docs/' . $document->id);
                        if (File::exists($filePath)) {
                            File::delete($filePath);
                        }
                        // Supprimer l'enregistrement du document
                        $document->delete();
                    }
                    $documentCount++;
                }

                // Supprimer le contrôle lui-même
                if (! $dryRun) {
                    // Supprimer les liens dans control_measure
                    DB::table('control_measure')->where('control_id', $control->id)->delete();

                    // Supprimer les plans d'action
                    DB::table('actions')->where('control_id', $control->id)->delete();

                    // Remove next_id link
                    Control::where('next_id', $control->id)->update(['next_id' => null]);

                    // delete control
                    $control->delete();
                }
                $controlCount++;
            });
        }

        return [
            'documentCount' => $documentCount,
            'controlCount' => $controlCount,
            'logCount' => $logCount,
        ];
    }

    private function isAdminOrUser($user): bool
    {
        return in_array($user->role, [1, 2]);
    }

    private function isAuditorOrAuditeeWithAccess($user): bool
    {
        if (! in_array($user->role, [3, 5])) {
            return false;
        }

        return $this->isDirectlyAssignedToUser($user) || $this->isAssignedViaGroup($user);
    }

    private function isDirectlyAssignedToUser($user): bool
    {
        return DB::table('control_user')
            ->where('control_id', $this->id)
            ->where('user_id', $user->id)
            ->exists();
    }

    private function isAssignedViaGroup($user): bool
    {
        return DB::table('control_user_group')
            ->join('user_user_group', 'control_user_group.user_group_id', '=', 'user_user_group.user_group_id')
            ->where('control_user_group.control_id', $this->id)
            ->where('user_user_group.user_id', $user->id)
            ->exists();
    }
}
