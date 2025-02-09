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
     * @var array
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
     * @var array
     */
    protected $hidden = [
        'password1', 'password2', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function controls()
    {
        return $this->belongsToMany(Control::class)->orderBy('name');
    }

    public function lastControls()
    {
        return $this->belongsToMany(Control::class)->whereNull('realisation_date')->orderBy('name');
    }
}
