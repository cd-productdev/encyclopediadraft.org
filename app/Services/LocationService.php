<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocationService
{
    /**
     * Get location information from IP address
     */
    public function getLocationFromIp(string $ip): array
    {
        try {
            // Skip for local IPs
            if ($this->isLocalIp($ip)) {
                return [
                    'city' => 'Local',
                    'country' => 'Local',
                ];
            }

            // Using ip-api.com (free, no API key required)
            $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}", [
                'fields' => 'status,country,city',
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['status'] === 'success') {
                    return [
                        'city' => $data['city'] ?? 'Unknown',
                        'country' => $data['country'] ?? 'Unknown',
                    ];
                }
            }

            // Fallback: return unknown
            return [
                'city' => 'Unknown',
                'country' => 'Unknown',
            ];

        } catch (Exception $e) {
            Log::warning('Failed to get location from IP', [
                'ip' => $ip,
                'error' => $e->getMessage(),
            ]);

            return [
                'city' => 'Unknown',
                'country' => 'Unknown',
            ];
        }
    }

    /**
     * Get device name from user agent
     */
    public function getDeviceName(?string $userAgent): string
    {
        if (! $userAgent) {
            return 'Unknown Device';
        }

        // Detect device type
        if (preg_match('/Mobile|Android|iPhone|iPad/i', $userAgent)) {
            if (preg_match('/iPhone/i', $userAgent)) {
                return 'iPhone';
            } elseif (preg_match('/iPad/i', $userAgent)) {
                return 'iPad';
            } elseif (preg_match('/Android/i', $userAgent)) {
                return 'Android Device';
            }

            return 'Mobile Device';
        } elseif (preg_match('/Windows/i', $userAgent)) {
            return 'Windows PC';
        } elseif (preg_match('/Mac/i', $userAgent)) {
            return 'Mac';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            return 'Linux PC';
        }

        return 'Unknown Device';
    }

    /**
     * Check if IP is local
     */
    private function isLocalIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }
}
