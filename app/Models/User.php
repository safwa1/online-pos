<?php

namespace App\Models;


use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    const ADMIN = 'ADMIN';
    const USER = 'USER';

    const Roles = [
        self::ADMIN => 'مدير',
        self::USER => 'مستخدم',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isAdmin() : bool
    {
        return $this->role === self::ADMIN;
    }

    public function isUser() : bool
    {
        return $this->role === self::USER;
    }

    public function isAdminOrUser() : bool
    {
        return $this->isAdmin() || $this->isUser();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->can('view-admin', User::class);
    }
}
