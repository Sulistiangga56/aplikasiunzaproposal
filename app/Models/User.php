<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    const ROLE_ADMINISTRATOR = 'ADMINISTRATOR';
    const ROLE_DIREKSI = 'DIREKSI';

    const ROLE_WAKIL = 'WAKIL';

    const ROLES = [
        self::ROLE_ADMINISTRATOR => 'Administrator',
        self::ROLE_DIREKSI => 'Direksi',
        self::ROLE_WAKIL => 'Wakil',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status_akun',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->status === 0) {
            return false;
        } elseif (!$this->exists) {
            return false;
        } else {
            return true;
        }
    }

    public function isAdministrator(): bool
    {
        return $this->role === self::ROLE_ADMINISTRATOR;
    }

    public function isDireksi(): bool
    {
        return $this->role === self::ROLE_DIREKSI;
    }

    public function isWakil(): bool
    {
        return $this->role === self::ROLE_WAKIL;
    }
}
