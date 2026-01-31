<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Notifications\ApiResetPasswordNotification;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
    'name',
    'username',
    'email',
    'password',
    'role',
    'status',
    'membership',
];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified' => 'boolean',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /* =========================
       UUID Boot
    ========================= */

    protected static function booted()
    {
        static::creating(function ($user) {
            if (! $user->id) {
                $user->id = (string) Str::uuid();
            }
        });
    }

    /* =========================
       Password Reset
    ========================= */

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ApiResetPasswordNotification($token));
    }

    /* =========================
       Role Helpers
    ========================= */

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /* =========================
       Relationships
    ========================= */

    public function resumes()
    {
        return $this->hasMany(Resume::class);
    }


public function canManageUsers(): bool
{
    return $this->role === 'admin';
}

public function canSuspendUsers(): bool
{
    return $this->role === 'admin';
}

public function canDeleteUsers(): bool
{
    return $this->role === 'admin';
}


}
