<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\User;
use App\Models\UserPermission;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PermissionController extends Controller
{
    public function __construct(private PermissionService $permissionService) {}

    public function index(): View
    {
        $roles = ['dokter', 'perawat', 'farmasi', 'kasir', 'petugas_pendaftaran', 'manajemen'];
        $permissions = Permission::orderBy('sort_order')->get();

        $roleStats = collect($roles)->mapWithKeys(function ($role) {
            return [$role => User::where('role', $role)->count()];
        });

        return view('master.permissions.index', compact('roles', 'permissions', 'roleStats'));
    }

    public function show(string $role): View
    {
        $permissions = Permission::orderBy('sort_order')->get();
        $users       = User::where('role', $role)->get();

        $grantedKeys = [];
        if ($users->isNotEmpty()) {
            $grantedKeys = UserPermission::where('user_id', $users->first()->id)
                ->where('is_granted', true)
                ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
                ->pluck('permissions.menu_key')
                ->toArray();
        }

        return view('master.permissions.show', compact('role', 'permissions', 'users', 'grantedKeys'));
    }

    public function update(Request $request, string $role): RedirectResponse
    {
        $grantedKeys    = $request->input('permissions', []);
        $users          = User::where('role', $role)->get();
        $allPermissions = Permission::all()->keyBy('menu_key');

        foreach ($users as $user) {
            foreach ($allPermissions as $key => $permission) {
                UserPermission::updateOrCreate(
                    ['user_id' => $user->id, 'permission_id' => $permission->id],
                    ['is_granted' => in_array($key, $grantedKeys)]
                );
            }
            $this->permissionService->clearUserPermissionsCache($user->id);
        }

        return redirect()->route('master.permissions.index')
            ->with('success', "Hak akses untuk role {$role} berhasil diperbarui.");
    }
}
