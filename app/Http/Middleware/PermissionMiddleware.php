<?php

namespace App\Http\Middleware;

use App\Services\PermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function __construct(
        private readonly PermissionService $permissionService
    ) {}

    /**
     * Handle an incoming request.
     * Checks if the authenticated user has permission to access the requested menu/feature.
     * Admin role bypasses all permission checks.
     * - API requests: abort(403) if no permission
     * - Web requests: redirect back with error message if no permission
     */
    public function handle(Request $request, Closure $next, string $menuKey): Response
    {
        $user = $request->user();

        if (! $user) {
            if ($request->expectsJson()) {
                abort(401, 'Unauthenticated.');
            }

            return redirect()->route('login');
        }

        // Admin has full access — bypass all permission checks
        if ($user->role === 'admin') {
            return $next($request);
        }

        if (! $this->permissionService->hasPermission($user->id, $menuKey)) {
            if ($request->expectsJson()) {
                abort(403, 'Akses tidak diizinkan.');
            }

            return redirect()->back()->withErrors([
                'permission' => 'Anda tidak memiliki izin untuk mengakses fitur ini.',
            ]);
        }

        return $next($request);
    }
}
