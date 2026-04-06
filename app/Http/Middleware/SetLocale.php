<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Default to 'ar' if no locale is in session and user is in the admin/dashboard
        if (!session()->has('locale') && $request->is('dashboard*', 'products*', 'users*', 'merchants*', 'bookings*', 'settings*', 'reports*', 'categories*')) {
            session(['locale' => 'ar']);
        }

        if (session()->has('locale')) {
            app()->setLocale(session('locale'));
        }

        return $next($request);
    }
}
