<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    private AuthService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AuthService();
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function createUser(array $overrides = []): User
    {
        return User::create(array_merge([
            'username'           => 'testuser',
            'password'           => Hash::make('Password123!'),
            'role'               => 'admin',
            'is_active'          => true,
            'failed_login_count' => 0,
            'locked_until'       => null,
        ], $overrides));
    }

    // -------------------------------------------------------------------------
    // 1. login() returns success=true for valid credentials
    // -------------------------------------------------------------------------

    public function test_login_returns_success_true_for_valid_credentials(): void
    {
        $this->createUser();

        $result = $this->service->login('testuser', 'Password123!');

        $this->assertTrue($result['success']);
        $this->assertInstanceOf(User::class, $result['user']);
    }

    // -------------------------------------------------------------------------
    // 2. login() returns success=false for wrong password
    // -------------------------------------------------------------------------

    public function test_login_returns_success_false_for_wrong_password(): void
    {
        $this->createUser();

        $result = $this->service->login('testuser', 'WrongPassword!');

        $this->assertFalse($result['success']);
        $this->assertNull($result['user']);
    }

    // -------------------------------------------------------------------------
    // 3. login() increments failed_login_count on failure
    // -------------------------------------------------------------------------

    public function test_login_increments_failed_login_count_on_failure(): void
    {
        $user = $this->createUser();

        $this->service->login('testuser', 'WrongPassword!');

        $user->refresh();
        $this->assertEquals(1, $user->failed_login_count);

        $this->service->login('testuser', 'WrongPassword!');

        $user->refresh();
        $this->assertEquals(2, $user->failed_login_count);
    }

    // -------------------------------------------------------------------------
    // 4. login() sets locked_until after 5 failures
    // -------------------------------------------------------------------------

    public function test_login_sets_locked_until_after_five_failures(): void
    {
        $user = $this->createUser();

        for ($i = 0; $i < 5; $i++) {
            $this->service->login('testuser', 'WrongPassword!');
        }

        $user->refresh();
        $this->assertNotNull($user->locked_until);
        $this->assertTrue($user->locked_until->isFuture());
    }

    // -------------------------------------------------------------------------
    // 5. login() resets failed_login_count on success
    // -------------------------------------------------------------------------

    public function test_login_resets_failed_login_count_on_success(): void
    {
        $user = $this->createUser(['failed_login_count' => 3]);

        $result = $this->service->login('testuser', 'Password123!');

        $this->assertTrue($result['success']);
        $user->refresh();
        $this->assertEquals(0, $user->failed_login_count);
    }

    // -------------------------------------------------------------------------
    // 6. login() returns error for inactive user
    // -------------------------------------------------------------------------

    public function test_login_returns_error_for_inactive_user(): void
    {
        $this->createUser(['is_active' => false]);

        $result = $this->service->login('testuser', 'Password123!');

        $this->assertFalse($result['success']);
        $this->assertNull($result['user']);
        $this->assertStringContainsStringIgnoringCase('tidak aktif', $result['message']);
    }

    // -------------------------------------------------------------------------
    // 7. login() returns error for locked user
    // -------------------------------------------------------------------------

    public function test_login_returns_error_for_locked_user(): void
    {
        $this->createUser(['locked_until' => now()->addMinutes(15)]);

        $result = $this->service->login('testuser', 'Password123!');

        $this->assertFalse($result['success']);
        $this->assertNull($result['user']);
        $this->assertStringContainsStringIgnoringCase('terkunci', $result['message']);
    }
}
