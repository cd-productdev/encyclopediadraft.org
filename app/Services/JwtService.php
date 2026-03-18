<?php

namespace App\Services;

use Carbon\Carbon;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    private string $secretKey;

    private int $expirationTime;

    public function __construct()
    {
        $this->secretKey = config('jwt.secret', env('JWT_SECRET', 'your-secret-key-change-this-in-production'));
        $this->expirationTime = config('jwt.expiration', env('JWT_EXPIRATION', 60 * 24)); // 24 hours in minutes
    }

    public function generateToken(array $payload): string
    {
        $issuedAt = Carbon::now()->timestamp;
        $expiration = Carbon::now()->addMinutes($this->expirationTime)->timestamp;

        $tokenPayload = [
            'iss' => config('app.url'),
            'iat' => $issuedAt,
            'exp' => $expiration,
            'data' => $payload,
        ];

        return JWT::encode($tokenPayload, $this->secretKey, 'HS256');
    }

    public function validateToken(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));

            return (array) $decoded->data;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getExpirationTime(): int
    {
        return $this->expirationTime;
    }

    public function decodeToken(string $token): ?object
    {
        try {
            return JWT::decode($token, new Key($this->secretKey, 'HS256'));
        } catch (\Exception $e) {
            return null;
        }
    }
}
