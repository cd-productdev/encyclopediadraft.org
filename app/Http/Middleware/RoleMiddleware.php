<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $userRole = $request->get('user_role');

        if (! $userRole) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        if (! in_array($userRole, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Insufficient permissions.',
            ], 403);
        }

        return $next($request);
    }
}
