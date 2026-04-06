<?php

namespace Tests\Unit\Services;

use App\Models\Permission;
use App\Models\User;
use App\Models\UserPermission;
use App\Services\PermissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PermissionServiceTest extends TestCase
{
    use RefreshDatabase;

    private PermissionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PermissionService();
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function createUser(string $role = 'dokter'): User
    {
        return User::create([
            'username'           => 'user_' . uniqid(),
            'password'           => Hash::make('Password123!'),
            'role'               => $role,
            'is_active'          => true,
            'failed_login_count' => 0,
            'locked_until'       => null,
        ]);
    }

    private function createPermission(string $menuKey): Permission
    {
        return Permission::create([
            'menu_key'   => $menuKey,
            'menu_label' => ucfirst($menuKey),
            'sort_order' => 0,
        ]);
    }

    private function grantPermission(User $user, Permission $permission): void
    {
        UserPermission::create([
            'user_id'       => $user->id,
            'permission_id' => $permission->id,
            'is_granted'    => true,
        ]);
    }

    // -------------------------------------------------------------------------
    // 1. Admin always has permission for any menu key
    // -------------------------------------------------------------------------

    public function test_admin_always_has_permission_for_any_menu_key(): void
    {
        $admin = $this->createUser('admin');

        $this->assertTrue($this->service->hasPermission($admin->id, 'some.menu'));
        $this->assertTrue($this->service->hasPermission($admin->id, 'another.menu'));
        $this->assertTrue($this->service->hasPermission($admin->id, 'nonexistent.menu'));
    }

    // -------------------------------------------------------------------------
    // 2. Non-admin without permission returns false
    // -------------------------------------------------------------------------

    public function test_non_admin_without_permission_returns_false(): void
    {
        $user = $this->createUser('dokter');
        $this->createPermission('lab.view');

        // No UserPermission record created — user has no access
        $this->assertFalse($this->service->hasPermission($user->id, 'lab.view'));
    }

    // -------------------------------------------------------------------------
    // 3. Non-admin with granted permission returns true
    // -------------------------------------------------------------------------

    public function test_non_admin_with_granted_permission_returns_true(): void
    {
        $user       = $this->createUser('dokter');
        $permission = $this->createPermission('rme.view');

        $this->grantPermission($user, $permission);

        $this->assertTrue($this->service->hasPermission($user->id, 'rme.view'));
    }

    // -------------------------------------------------------------------------
    // 4. getUserPermissions caches results
    // -------------------------------------------------------------------------

    public function test_get_user_permissions_caches_results(): void
    {
        $user       = $this->createUser('perawat');
        $permission = $this->createPermission('queue.view');

        $this->grantPermission($user, $permission);

        // First call — populates cache
        $first = $this->service->getUserPermissions($user->id);
        $this->assertContains('queue.view', $first);

        // Verify the cache key exists
        $cacheKey = 'permissions:user:' . $user->id;
        $this->assertTrue(Cache::has($cacheKey));

        // Second call — should return cached value (same result)
        $second = $this->service->getUserPermissions($user->id);
        $this->assertEquals($first, $second);
    }
}
