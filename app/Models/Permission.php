<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// UserPermission is resolved via Eloquent's hasMany at runtime

class Permission extends Model
{
    protected $fillable = [
        'menu_key',
        'menu_label',
        'parent_key',
        'sort_order',
    ];

    /**
     * Get all user_permissions entries for this permission.
     */
    public function userPermissions(): HasMany
    {
        return $this->hasMany(UserPermission::class);
    }
}
