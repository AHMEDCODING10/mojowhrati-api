<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LatencyMiddleware
{
    /**
     * Handle an incoming request and store its latency in cache.
     */
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);
        $response = $next($request);
        $latency = (microtime(true) - $start) * 1000; // milliseconds
        // Cache for 5 minutes (value used by HealthController)
        cache()->put('last_request_latency', $latency, now()->addMinutes(5));
        return $response;
    }
}
