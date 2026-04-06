<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

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
    // 1. Login with valid credentials succeeds
    // -------------------------------------------------------------------------

    public function test_login_with_valid_credentials_succeeds(): void
    {
        $this->createUser();

        $response = $this->post(route('login.post'), [
            'username' => 'testuser',
            'password' => 'Password123!',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    // -------------------------------------------------------------------------
    // 2. Login with invalid credentials fails
    // -------------------------------------------------------------------------

    public function test_login_with_invalid_credentials_fails(): void
    {
        $this->createUser();

        $response = $this->post(route('login.post'), [
            'username' => 'testuser',
            'password' => 'WrongPassword!',
        ]);

        $response->assertRedirect();
        $this->assertGuest();
    }

    // -------------------------------------------------------------------------
    // 3. Login with inactive account fails
    // -------------------------------------------------------------------------

    public function test_login_with_inactive_account_fails(): void
    {
        $this->createUser(['is_active' => false]);

        $response = $this->post(route('login.post'), [
            'username' => 'testuser',
            'password' => 'Password123!',
        ]);

        $response->assertRedirect();
        $this->assertGuest();
    }

    // -------------------------------------------------------------------------
    // 4. Account locks after 5 failed attempts
    // -------------------------------------------------------------------------

    public function test_account_locks_after_five_failed_attempts(): void
    {
        $user = $this->createUser();

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.post'), [
                'username' => 'testuser',
                'password' => 'WrongPassword!',
            ]);
        }

        $user->refresh();
        $this->assertNotNull($user->locked_until);
        $this->assertTrue($user->locked_until->isFuture());
    }

    // -------------------------------------------------------------------------
    // 5. Locked account cannot login
    // -------------------------------------------------------------------------

    public function test_locked_account_cannot_login(): void
    {
        $this->createUser([
            'locked_until' => now()->addMinutes(15),
        ]);

        $response = $this->post(route('login.post'), [
            'username' => 'testuser',
            'password' => 'Password123!',
        ]);

        $response->assertRedirect();
        $this->assertGuest();
    }

    // -------------------------------------------------------------------------
    // 6. Logout invalidates session
    // -------------------------------------------------------------------------

    public function test_logout_invalidates_session(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $this->assertAuthenticated();

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    // -------------------------------------------------------------------------
    // 7. Session timeout middleware redirects after 30 minutes of inactivity
    // -------------------------------------------------------------------------

    public function test_session_timeout_middleware_redirects_after_inactivity(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        // Simulate last_activity 31 minutes ago
        session(['last_activity' => time() - (31 * 60)]);

        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
