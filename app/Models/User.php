<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Champs autorisés pour l'assignation de masse
    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'role',
        'is_active',
        'last_login_at',
    ];

    // Champs à cacher (sensible)
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Cast automatique
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    /**
     * Hash le mot de passe automatiquement
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     * Relation : un utilisateur peut avoir plusieurs réservations
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Vérifie si l'utilisateur est admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Vérifie si l'utilisateur est manager
     */
    public function isManager()
    {
        return $this->role === 'manager';
    }

    /**
     * Vérifie si l'utilisateur est client
     */
    public function isCustomer()
    {
        return $this->role === 'customer';
    }
}
