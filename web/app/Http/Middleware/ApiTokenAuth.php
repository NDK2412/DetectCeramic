<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiTokenAuth
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $user = \App\Models\User::where('api_token', hash('sha256', $token))->first();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        auth()->login($user);

        return $next($request);
    }
}