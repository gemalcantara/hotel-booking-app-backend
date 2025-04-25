<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequestPerformanceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Record the start time
        $startTime = microtime(true);
        
        // Process the request
        $response = $next($request);
        
        // Calculate the request duration
        $duration = microtime(true) - $startTime;
        
        // Log the request performance data
        Log::info('API Request Performance', [
            'uri' => $request->getRequestUri(),
            'method' => $request->getMethod(),
            'duration' => round($duration * 1000, 2) . 'ms', // Convert to milliseconds
            'ip' => $request->ip(),
            'user_id' => $request->user() ? $request->user()->id : 'guest',
        ]);
        
        return $response;
    }
}
