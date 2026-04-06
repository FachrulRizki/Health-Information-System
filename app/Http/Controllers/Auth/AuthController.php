<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService)
    {
    }

    /**
     * Show the login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $result = $this->authService->login(
            $request->input('username'),
            $request->input('password')
        );

        if ($result['success']) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withErrors(['username' => $result['message']])
            ->onlyInput('username');
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        $this->authService->logout();

        return redirect()->route('login');
    }
}
