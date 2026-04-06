<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Visitor;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitors
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only track guests (unauthenticated users)
        if (!auth()->check()) {
            Visitor::updateOrCreate(
                [
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ],
                [
                    'last_active_at' => now(),
                ]
            );
        } else {
            // Optional: If user logs in, remove their guest record if you want it to be "strictly guest"
            // For now, if they are logged in, we let the 'User Count' handle them.
        }

        return $next($request);
    }
}
