<?php

namespace App\Http\ViewComposers;

use App\Models\ApiSetting;
use App\Services\PermissionService;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class NavigationComposer
{
    public function __construct(
        private readonly PermissionService $permissionService
    ) {}

    /**
     * Share navigation data with all views:
     *  - $userPermissions : array of menu_keys (or ['*'] for admin)
     *  - $apiMode         : 'testing' | 'production' | null
     */
    public function compose(View $view): void
    {
        $user = auth()->user();

        if (! $user) {
            $view->with('userPermissions', []);
            $view->with('apiMode', null);

            return;
        }

        // Permissions
        if ($user->role === 'admin') {
            $view->with('userPermissions', ['*']);
        } else {
            $permissions = $this->permissionService->getUserPermissions($user->id);
            $view->with('userPermissions', $permissions);
        }

        // API mode indicator — cached for 10 minutes (matches design doc caching strategy)
        $apiMode = Cache::remember('nav:api_mode', 600, function () {
            $setting = ApiSetting::where('is_active', true)->first();

            return $setting?->mode;
        });

        $view->with('apiMode', $apiMode);
    }
}
