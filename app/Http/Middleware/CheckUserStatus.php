<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->status === 'blocked') {
            $message = __('الحساب تم توقيفه من قبل الادمين يرجى التواصل بخدمة العملاء');
            
            \Illuminate\Support\Facades\Auth::logout();

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $message
                ], 403);
            }

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => $message,
            ]);
        }

        return $next($request);
    }
}
