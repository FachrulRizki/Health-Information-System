<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeoutMiddleware
{
    /**
     * Session timeout in minutes (30 minutes per requirement 1.8).
     */
    protected int $timeout = 30;

    /**
     * Handle an incoming request.
     * Terminates session after 30 minutes of inactivity.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $lastActivity = session('last_activity');

            if ($lastActivity && (time() - $lastActivity) > ($this->timeout * 60)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', 'Sesi Anda telah berakhir karena tidak aktif selama 30 menit. Silakan login kembali.');
            }

            session(['last_activity' => time()]);
        }

        return $next($request);
    }
}
