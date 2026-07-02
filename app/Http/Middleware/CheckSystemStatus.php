<?php

namespace App\Http\Middleware;

use App\Models\SystemStatus;
use Closure;
use Illuminate\Http\Request;

class CheckSystemStatus
{
    public function handle(Request $request, Closure $next)
    {
        // Always allow: public pages, login, register, forgot password, OTP
        if ($request->routeIs(
            'home', 'login', 'logout',
            'register', 'register.*',
            'password.*',
            'account.reactivate', 'account.cancel-reactivate'
        )) {
            return $next($request);
        }

        if (SystemStatus::isDown('cs')) {
            $user = auth()->user();

            // Admin and Super Admin bypass maintenance
            if ($user && in_array($user->role, ['admin', 'superadmin'])) {
                return $next($request);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'The Consumable Management System is currently under maintenance.'
                ], 503);
            }

            $status = SystemStatus::current('cs');
            return response()->view('maintenance', compact('status'), 503);
        }

        return $next($request);
    }
}