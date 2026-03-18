<?php

/**
 * Simple test script for check-session and refresh APIs
 * Usage: php test_apis.php
 */
$baseUrl = 'http://localhost/wikiengine/api';

// Test credentials (update these with your actual test user)
$testEmail = 'test@example.com';
$testPassword = 'password123';

echo "=== API Testing Script ===\n\n";

// Step 1: Login to get a token
echo "1. Testing Login...\n";
$loginData = json_encode([
    'email' => $testEmail,
    'password' => $testPassword,
]);

$ch = curl_init($baseUrl.'/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $loginData);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
]);
curl_setopt($ch, CURLOPT_HEADER, true); // Get headers to extract cookies

$loginResponse = curl_exec($ch);
$loginHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
curl_close($ch);

// Extract cookies from response
preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $loginResponse, $matches);
$cookies = [];
foreach ($matches[1] as $cookie) {
    $parts = explode('=', $cookie, 2);
    if (count($parts) === 2) {
        $cookies[$parts[0]] = $parts[1];
    }
}

// Extract response body
$loginBody = substr($loginResponse, $headerSize);
$loginData = json_decode($loginBody, true);

echo "   HTTP Code: $loginHttpCode\n";
if ($loginData) {
    echo '   Success: '.($loginData['success'] ?? $loginData['status'] ?? 'N/A')."\n";
    echo '   Message: '.($loginData['message'] ?? 'N/A')."\n";
}

// Get token from cookie or response
$token = $cookies['token'] ?? $cookies['jwt_token'] ?? ($loginData['token'] ?? null);

if (! $token) {
    echo "\n❌ ERROR: Could not get token from login. Cannot continue testing.\n";
    echo 'Response: '.$loginBody."\n";
    exit(1);
}

echo '   Token obtained: '.substr($token, 0, 20)."...\n\n";

// Step 2: Test check-session
echo "2. Testing Check Session...\n";
$ch = curl_init($baseUrl.'/check-session');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'Authorization: Bearer '.$token,
    'Cookie: token='.$token,
]);

$sessionResponse = curl_exec($ch);
$sessionHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$sessionData = json_decode($sessionResponse, true);
echo "   HTTP Code: $sessionHttpCode\n";
if ($sessionData) {
    echo '   Status: '.($sessionData['status'] ?? 'N/A')."\n";
    echo '   Message: '.($sessionData['message'] ?? 'N/A')."\n";
    if (isset($sessionData['user'])) {
        echo '   User: '.($sessionData['user']['name'] ?? 'N/A').' ('.($sessionData['user']['email'] ?? 'N/A').")\n";
    }
    if (isset($sessionData['session'])) {
        echo '   Remaining Minutes: '.($sessionData['session']['remaining_minutes'] ?? 'N/A')."\n";
    }
} else {
    echo "   Response: $sessionResponse\n";
}
echo "\n";

// Step 3: Test refresh
echo "3. Testing Refresh Token...\n";
$ch = curl_init($baseUrl.'/auth/refresh');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'Authorization: Bearer '.$token,
    'Cookie: token='.$token,
]);
curl_setopt($ch, CURLOPT_HEADER, true);

$refreshResponse = curl_exec($ch);
$refreshHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$refreshHeaderSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
curl_close($ch);

// Extract new cookies
preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $refreshResponse, $refreshMatches);
$newCookies = [];
foreach ($refreshMatches[1] as $cookie) {
    $parts = explode('=', $cookie, 2);
    if (count($parts) === 2) {
        $newCookies[$parts[0]] = $parts[1];
    }
}

$refreshBody = substr($refreshResponse, $refreshHeaderSize);
$refreshData = json_decode($refreshBody, true);

echo "   HTTP Code: $refreshHttpCode\n";
if ($refreshData) {
    echo '   Status: '.($refreshData['status'] ?? 'N/A')."\n";
    echo '   Message: '.($refreshData['message'] ?? 'N/A')."\n";
    if (isset($refreshData['user'])) {
        echo '   User: '.($refreshData['user']['name'] ?? 'N/A')."\n";
    }
} else {
    echo "   Response: $refreshBody\n";
}

$newToken = $newCookies['token'] ?? null;
if ($newToken && $newToken !== $token) {
    echo "   ✅ New token generated successfully!\n";
} else {
    echo "   ⚠️  Token may not have been refreshed\n";
}
echo "\n";

// Step 4: Test check-session again with new token (if available)
if ($newToken) {
    echo "4. Testing Check Session with New Token...\n";
    $ch = curl_init($baseUrl.'/check-session');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer '.$newToken,
        'Cookie: token='.$newToken,
    ]);

    $sessionResponse2 = curl_exec($ch);
    $sessionHttpCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $sessionData2 = json_decode($sessionResponse2, true);
    echo "   HTTP Code: $sessionHttpCode2\n";
    if ($sessionData2) {
        echo '   Status: '.($sessionData2['status'] ?? 'N/A')."\n";
        echo '   Message: '.($sessionData2['message'] ?? 'N/A')."\n";
    }
    echo "\n";
}

echo "=== Testing Complete ===\n";
