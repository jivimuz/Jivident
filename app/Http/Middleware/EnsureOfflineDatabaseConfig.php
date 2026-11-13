<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureOfflineDatabaseConfig
{
    /**
     * Handle an incoming request.
     * Ensure session uses file driver when accessing native-config to prevent DB crashes.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('native-config') || $request->is('native-config/*')) {
            config(['session.driver' => 'file']);
        }

        return $next($request);
    }
}
