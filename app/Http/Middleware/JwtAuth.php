<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Try to get token from multiple sources
            $token = $request->cookie('jwt_token') ??
                     $request->cookie('token') ??
                     $request->bearerToken() ??
                     $request->header('Authorization');

            // Remove 'Bearer ' prefix if present
            if ($token && strpos($token, 'Bearer ') === 0) {
                $token = substr($token, 7);
            }

            if (! $token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Token not found.',
                ], 401);
            }

            // Authenticate user from token
            $user = JWTAuth::setToken($token)->authenticate();

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired token',
                ], 401);
            }

            // Set authenticated user
            auth()->setUser($user);
            $request->merge(['user_id' => $user->id]);
            $request->merge(['user_role' => $user->role]);

            return $next($request);
        } catch (TokenExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token expired',
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token',
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token error',
            ], 401);
        }
    }
}
