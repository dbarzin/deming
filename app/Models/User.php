<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'login', 'name', 'email', 'password', 'title', 'role', 'language',
    ];

    /* Roles :
        1 - Admin
        2 - User
        3 - Auditor
        4 - API
        5 - Auditee
    */

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
     * @var list<string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function groups()
    {
        return $this->belongsToMany(UserGroup::class)->orderBy('name');
    }

    public function controls()
    {
        return $this->belongsToMany(Control::class)->orderBy('name');
    }

    public function lastControls()
    {
        return $this->belongsToMany(Control::class)->whereNull('realisation_date')->orderBy('name');
    }

    public function initiales(): string
    {
        // On supprime les espaces en trop en début/fin et on remplace les multiples espaces internes par un seul
        $nom = trim(preg_replace('/\s+/', ' ', $this->name));

        // On découpe la chaîne en mots
        $mots = explode(' ', $nom);

        if (count($mots) === 1) {
            // Un seul mot : on renvoie les deux premières lettres
            return substr($mots[0], 0, 2);
        }
        // Deux mots ou plus : on renvoie la première lettre du premier et du deuxième mot
        return substr($mots[0], 0, 1) . substr($mots[1], 0, 1);
    }
}
