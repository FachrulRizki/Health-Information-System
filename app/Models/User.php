<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'username',
        'password',
        'role',
        'is_active',
        'locked_until',
        'failed_login_count',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'locked_until' => 'datetime',
            'failed_login_count' => 'integer',
        ];
    }

    /**
     * Check if the user has permission to access a given menu key.
     * Admin role always returns true.
     * Delegates to PermissionService for non-admin users.
     */
    public function hasPermission(string $menuKey): bool
    {
        /** @var \App\Services\PermissionService $service */
        $service = app(\App\Services\PermissionService::class);

        return $service->hasPermission($this->id, $menuKey);
    }

    /**
     * Check if the account is currently locked.
     */
    public function isLocked(): bool
    {
        return $this->locked_until !== null && $this->locked_until->isFuture();
    }

    /**
     * Get all user_permissions entries for this user.
     */
    public function userPermissions(): HasMany
    {
        return $this->hasMany(UserPermission::class);
    }

    /**
     * Get all permissions granted to this user.
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(UserPermission::class)->where('is_granted', true);
    }
}
