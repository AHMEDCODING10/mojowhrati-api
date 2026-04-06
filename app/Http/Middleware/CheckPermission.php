<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Super admin bypasses all checks
        if ($user && $user->role === \App\Models\User::ROLE_SUPER_ADMIN) {
            return $next($request);
        }

        // Only enforce granular RBAC for staff (Moderators, Support, etc.)
        // Merchants and Customers are authorized via their primary role/controller logic.
        if ($user && !$user->isStaff()) {
            return $next($request);
        }

        $routeName = $request->route()->getName();
        if (!$routeName) {
            return $next($request);
        }

        // Mapping route segments to screens
        $map = [
            'dashboard' => ['dashboard', 'view'],
            'products' => 'products',
            'merchants' => 'merchants',
            'bookings' => 'bookings',
            'custom_designs' => 'custom_designs',
            'categories' => 'categories',
            'users' => 'users',
            'currencies' => 'currencies',
            'banners' => 'banners',
            'notifications' => 'notifications',
            'gold-prices' => 'gold_prices',
            'contacts' => 'contacts',
            'settings' => 'settings',
            'reports' => 'reports',
        ];

        $screen = null;
        $action = 'view';

        // Detect screen from route name prefix
        foreach ($map as $prefix => $target) {
            if (str_starts_with($routeName, $prefix)) {
                $screen = is_array($target) ? $target[0] : $target;
                if (is_array($target)) $action = $target[1];
                break;
            }
        }

        if (!$screen) {
            return $next($request);
        }

        // Refine action from route name suffix
        if (str_contains($routeName, '.create') || str_contains($routeName, '.store')) {
            $action = 'create';
        } elseif (str_contains($routeName, '.edit') || str_contains($routeName, '.update') || str_contains($routeName, '.approve') || str_contains($routeName, '.toggle')) {
            $action = 'edit';
        } elseif (str_contains($routeName, '.destroy') || str_contains($routeName, '.clear')) {
            $action = 'delete';
        }

        if (!$user || !$user->hasPermission($screen, $action)) {
            $message = __('الصلاحيات محدودة! يرجى التواصل مع المدير العام.');
            
            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 403);
            }

            // If it's an AJAX request but not expecting JSON (e.g., from some Alpine fetches)
            if ($request->ajax()) {
                return response($message, 403);
            }

            if ($routeName === 'dashboard') {
                abort(403, $message);
            }

            return redirect()->route('dashboard')->with('error', $message);
        }

        return $next($request);
    }
}
