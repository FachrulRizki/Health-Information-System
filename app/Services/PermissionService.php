<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Support\Facades\Cache;

class PermissionService
{
    private const CACHE_TTL = 300; // 5 minutes
    private const CACHE_KEY_PREFIX = 'permissions:user:';

    /**
     * Get all granted permission menu_keys for a user.
     * Results are cached per user for 5 minutes.
     */
    public function getUserPermissions(int $userId): array
    {
        $cacheKey = self::CACHE_KEY_PREFIX . $userId;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId) {
            return UserPermission::query()
                ->where('user_id', $userId)
                ->where('is_granted', true)
                ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
                ->pluck('permissions.menu_key')
                ->toArray();
        });
    }

    /**
     * Check if a user has permission to access a given menu key.
     * Admin role always returns true without checking the DB.
     */
    public function hasPermission(int $userId, string $menuKey): bool
    {
        $user = User::find($userId);

        if (! $user) {
            return false;
        }

        // Admin always has full access
        if ($user->role === 'admin') {
            return true;
        }

        $permissions = $this->getUserPermissions($userId);

        return in_array($menuKey, $permissions, true);
    }

    /**
     * Clear the cached permissions for a specific user.
     * Should be called whenever a user's permissions are updated.
     */
    public function clearUserPermissionsCache(int $userId): void
    {
        Cache::forget(self::CACHE_KEY_PREFIX . $userId);
    }

    /**
     * Get all permissions from the DB (for admin UI).
     */
    public function getAllMenuPermissions(): array
    {
        return Permission::orderBy('sort_order')
            ->orderBy('parent_key')
            ->orderBy('menu_label')
            ->get()
            ->toArray();
    }
}
