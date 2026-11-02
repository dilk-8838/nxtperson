<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // If not authenticated
        if (! $user) {
            // If in demo mode and demo_role session exists, resolve user for demo
            if (config('app.demo_mode', true)) {
                $demoRole = session('demo_role', 'farmer');
                $matchesDemo = false;
                foreach ($roles as $r) {
                    if ($demoRole === $r ||
                        ($r === 'supplier' && $demoRole === 'distributor') ||
                        ($r === 'distributor' && $demoRole === 'supplier') ||
                        ($r === 'fpo' && $demoRole === 'cooperative')) {
                        $matchesDemo = true;
                        break;
                    }
                }

                if ($matchesDemo) {
                    return $next($request);
                }
            }

            return redirect()->route('login')->with('warning', 'Please login to access this section.');
        }

        // Account status check
        if ($user->isSuspended()) {
            abort(403, 'Your account has been suspended by administration. Please contact support.');
        }

        // Strict role validation
        $hasAllowedRole = false;
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                $hasAllowedRole = true;
                break;
            }
        }

        if (! $hasAllowedRole) {
            $allowedList = implode(', ', $roles);
            abort(403, "Access restricted to [{$allowedList}] accounts only. Current account role is {$user->role}.");
        }

        return $next($request);
    }
}
