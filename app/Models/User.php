<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Auditable;

    /**
     * User role constants
     */
    public const ROLE_ADMIN = 1;
    public const ROLE_USER = 2;
    public const ROLE_AUDITOR = 3;
    public const ROLE_API = 4;
    public const ROLE_AUDITEE = 5;

    /**
     * Available roles with their labels
     *
     * @var array<int, string>
     */
    public const ROLES = [
        self::ROLE_ADMIN => 'Administrator',
        self::ROLE_USER => 'User',
        self::ROLE_AUDITOR => 'Auditor',
        self::ROLE_API => 'API',
        self::ROLE_AUDITEE => 'Auditee',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'login', 'name', 'email', 'password', 'title', 'role', 'language',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password1', 'password2', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relations
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(UserGroup::class)->orderBy('name');
    }

    public function controls(): BelongsToMany
    {
        return $this->belongsToMany(Control::class)->orderBy('name');
    }

    public function lastControls(): BelongsToMany
    {
        return $this->belongsToMany(Control::class)->whereNull('realisation_date')->orderBy('name');
    }

    /**
     * Helpers
     */

    /**
     * Get user initials from their name
     * - Single word: returns first two letters
     * - Multiple words: returns first letter of first and second word
     */
    public function initiales(): string
    {
        // Remove extra spaces at beginning/end and replace multiple internal spaces with a single one
        $nom = trim(preg_replace('/\s+/', ' ', $this->name));

        // Split the string into words
        $mots = explode(' ', $nom);

        if (count($mots) === 1) {
            // Single word: return the first two letters
            return strtoupper(substr($mots[0], 0, 2));
        }
        // Two or more words: return the first letter of the first and second word
        return strtoupper(substr($mots[0], 0, 1) . substr($mots[1], 0, 1));
    }

    /**
     * Get the role label for the user
     */
    public function getRoleLabel(): string
    {
        return self::ROLES[$this->role] ?? 'Unknown';
    }

    /**
     * Role checks
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    public function isAuditor(): bool
    {
        return $this->role === self::ROLE_AUDITOR;
    }

    public function isAPI(): bool
    {
        return $this->role === self::ROLE_API;
    }

    public function isAuditee(): bool
    {
        return $this->role === self::ROLE_AUDITEE;
    }

    /**
     * Check if the user has a specific role
     */
    public function hasRole(int $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if the user has any of the specified roles
     *
     * @param array<int> $roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    /**
     * Check if the user has at least the equivalent access level
     * (Admin > User > Auditee > Auditor according to Deming hierarchy)
     */
    public function hasMinimumRole(int $minimumRole): bool
    {
        $hierarchy = [
            self::ROLE_ADMIN => 4,
            self::ROLE_USER => 3,
            self::ROLE_AUDITEE => 2,
            self::ROLE_AUDITOR => 1,
            self::ROLE_API => 0,
        ];

        return ($hierarchy[$this->role] ?? 0) >= ($hierarchy[$minimumRole] ?? 0);
    }
}