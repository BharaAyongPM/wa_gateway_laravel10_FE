<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $key = $request->header('X-API-KEY');

        if (!$key || !\App\Models\Device::where('api_key', $key)->exists()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // inject session_id ke request
        $device = \App\Models\Device::where('api_key', $key)->first();
        $request->merge(['session' => $device->session_id]);

        return $next($request);
    }
}
