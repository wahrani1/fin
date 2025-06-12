<?php
// app/Http/Middleware/RoleMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Check if user has the required role
        if ($request->user()->type !== $role) {
            return response()->json(['error' => 'Forbidden. Insufficient permissions.'], 403);
        }

        return $next($request);
    }
}
