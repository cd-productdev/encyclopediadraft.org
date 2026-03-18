<?php

namespace App\Http\Middleware;

use App\Models\UserSession;
use App\Services\JwtService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastActive
{
    protected JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Update last_active_at if user is authenticated
        try {
            $token = $request->cookie('jwt_token');

            if ($token) {
                $payload = $this->jwtService->validateToken($token);

                if ($payload && isset($payload['user_id'])) {
                    $hashedToken = hash('sha256', $token);

                    UserSession::where('user_id', $payload['user_id'])
                        ->where('token', $hashedToken)
                        ->where('is_active', true)
                        ->update([
                            'last_active_at' => now(),
                        ]);
                }
            }
        } catch (\Exception $e) {
            // Silently fail - don't interrupt the request
        }

        return $response;
    }
}
