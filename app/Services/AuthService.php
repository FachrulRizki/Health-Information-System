<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Maximum failed login attempts before lockout.
     */
    protected int $maxFailedAttempts = 5;

    /**
     * Lockout duration in minutes.
     */
    protected int $lockoutMinutes = 15;

    /**
     * Attempt to log in a user with the given credentials.
     *
     * Returns an array with keys:
     *   - 'success' (bool)
     *   - 'user'    (User|null)
     *   - 'message' (string)
     *
     * @param string $username
     * @param string $password
     * @return array{success: bool, user: User|null, message: string}
     */
    public function login(string $username, string $password): array
    {
        $user = User::where('username', $username)->first();

        // Unknown user — generic message to avoid username enumeration
        if (! $user) {
            return [
                'success' => false,
                'user'    => null,
                'message' => 'Username atau password tidak valid.',
            ];
        }

        // Account inactive
        if (! $user->is_active) {
            return [
                'success' => false,
                'user'    => null,
                'message' => 'Akun Anda tidak aktif. Hubungi administrator.',
            ];
        }

        // Account locked
        if ($user->isLocked()) {
            $remaining = now()->diffInMinutes($user->locked_until, false);
            $remaining = max(1, (int) ceil($remaining));

            return [
                'success' => false,
                'user'    => null,
                'message' => "Akun Anda terkunci. Coba lagi dalam {$remaining} menit.",
            ];
        }

        // Wrong password
        if (! Hash::check($password, $user->password)) {
            $this->handleFailedAttempt($user);

            if ($user->isLocked()) {
                return [
                    'success' => false,
                    'user'    => null,
                    'message' => "Akun Anda terkunci selama {$this->lockoutMinutes} menit karena terlalu banyak percobaan login yang gagal.",
                ];
            }

            $remaining = $this->maxFailedAttempts - $user->failed_login_count;

            return [
                'success' => false,
                'user'    => null,
                'message' => "Username atau password tidak valid. Sisa percobaan: {$remaining}.",
            ];
        }

        // Credentials valid — log in and reset counters
        Auth::login($user);
        session(['last_activity' => time()]);

        $user->update([
            'failed_login_count' => 0,
            'locked_until'       => null,
        ]);

        return [
            'success' => true,
            'user'    => $user,
            'message' => 'Login berhasil.',
        ];
    }

    /**
     * Log out the currently authenticated user and invalidate the session.
     */
    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
    }

    /**
     * Increment failed_login_count and lock the account when the threshold is reached.
     */
    protected function handleFailedAttempt(User $user): void
    {
        $newCount = $user->failed_login_count + 1;

        $lockedUntil = null;
        if ($newCount >= $this->maxFailedAttempts) {
            $lockedUntil = now()->addMinutes($this->lockoutMinutes);
        }

        $user->update([
            'failed_login_count' => $newCount,
            'locked_until'       => $lockedUntil,
        ]);
    }
}
