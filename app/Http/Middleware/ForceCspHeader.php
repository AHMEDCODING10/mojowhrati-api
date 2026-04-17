<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceCspHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Force a permissive CSP header to allow Alpine.js (via CDN) to work
        $csp = "default-src * 'self' 'unsafe-inline' 'unsafe-eval' data: gap: content: blob:;";
        
        // Apply to the headers
        $response->headers->set('Content-Security-Policy', $csp);
        $response->headers->set('X-Content-Security-Policy', $csp); // For older browsers

        return $response;
    }
}
