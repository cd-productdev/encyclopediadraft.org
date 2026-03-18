# MediaWiki Setup with Custom JWT Authentication

## Overview
Yeh guide MediaWiki ko install karna aur apne Laravel JWT authentication system se integrate karna sikhata hai.

---

## Step 1: MediaWiki Installation

### Requirements
- PHP 7.4+ (same as Laravel)
- MySQL/MariaDB (same database as Laravel)
- Apache/Nginx

### Installation Steps

1. **MediaWiki Download:**
```bash
cd /path/to/your/project
wget https://releases.wikimedia.org/mediawiki/1.41/mediawiki-1.41.0.tar.gz
tar -xzf mediawiki-1.41.0.tar.gz
mv mediawiki-1.41.0 mediawiki
```

2. **Web Server Configuration:**
```apache
# Apache Virtual Host
<VirtualHost *:80>
    ServerName wiki.yourdomain.com
    DocumentRoot /path/to/mediawiki
    
    <Directory /path/to/mediawiki>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

3. **Database Setup:**
- MediaWiki ke liye alag database banao (optional, same database use kar sakte ho)
- Database name: `wikiengine_mediawiki`

---

## Step 2: MediaWiki Configuration

### LocalSettings.php Configuration

```php
<?php
// MediaWiki LocalSettings.php

// Database Configuration (Same as Laravel)
$wgDBtype = "mysql";
$wgDBserver = "localhost";
$wgDBname = "wikiengine_mediawiki"; // Or use same database
$wgDBuser = "root";
$wgDBpassword = "";

// Site Configuration
$wgSitename = "WikiEngine";
$wgMetaNamespace = "WikiEngine";

// URL Configuration
$wgServer = "https://wiki.yourdomain.com";
$wgScriptPath = "";
$wgResourceBasePath = $wgScriptPath;

// Disable Default Authentication
$wgAuth = false;

// Enable PluggableAuth Extension
wfLoadExtension('PluggableAuth');

// Custom Authentication Plugin
wfLoadExtension('JwtAuth');

// Disable User Registration (we'll use Laravel)
$wgGroupPermissions['*']['createaccount'] = false;
$wgGroupPermissions['*']['autocreateaccount'] = true; // Auto-create from JWT

// Allow API access
$wgEnableAPI = true;
$wgEnableWriteAPI = true;

// CORS for Laravel API
$wgCrossSiteAJAXdomains = [
    'https://yourdomain.com',
    'https://api.yourdomain.com'
];
```

---

## Step 3: PluggableAuth Extension Install

```bash
cd /path/to/mediawiki/extensions
git clone https://gerrit.wikimedia.org/r/mediawiki/extensions/PluggableAuth.git
```

---

## Step 4: Custom JWT Authentication Extension

### Extension Structure

```
mediawiki/extensions/JwtAuth/
├── extension.json
├── JwtAuth.php
└── JwtAuthProvider.php
```

### extension.json

```json
{
    "name": "JwtAuth",
    "version": "1.0.0",
    "author": "Your Name",
    "url": "https://yourdomain.com",
    "description": "JWT Authentication from Laravel",
    "license-name": "MIT",
    "type": "other",
    "requires": {
        "MediaWiki": ">= 1.35.0",
        "PluggableAuth": "*"
    },
    "AutoloadNamespaces": {
        "MediaWiki\\Extension\\JwtAuth\\": "includes/"
    },
    "config": {
        "JwtAuthSecret": {
            "value": "",
            "description": "JWT Secret from Laravel"
        },
        "JwtAuthApiUrl": {
            "value": "https://api.yourdomain.com",
            "description": "Laravel API URL"
        }
    },
    "Hooks": {
        "PluggableAuthUserAuthorization": "MediaWiki\\Extension\\JwtAuth\\JwtAuthProvider::onPluggableAuthUserAuthorization"
    }
}
```

### JwtAuthProvider.php

```php
<?php

namespace MediaWiki\Extension\JwtAuth;

use PluggableAuth\PluggableAuth;
use User;
use RequestContext;

class JwtAuthProvider extends PluggableAuth {
    
    private $jwtSecret;
    private $apiUrl;
    
    public function __construct() {
        global $wgJwtAuthSecret, $wgJwtAuthApiUrl;
        $this->jwtSecret = $wgJwtAuthSecret;
        $this->apiUrl = $wgJwtAuthApiUrl;
    }
    
    /**
     * Authenticate user from JWT token
     */
    public function authenticate(&$id, &$username, &$realname, &$email, &$errorMessage) {
        // Get JWT token from cookie or header
        $token = $this->getJwtToken();
        
        if (!$token) {
            $errorMessage = "JWT token not found";
            return false;
        }
        
        // Validate token with Laravel API
        $userData = $this->validateToken($token);
        
        if (!$userData) {
            $errorMessage = "Invalid JWT token";
            return false;
        }
        
        // Get or create MediaWiki user
        $user = $this->getOrCreateUser($userData);
        
        if (!$user) {
            $errorMessage = "Failed to create user";
            return false;
        }
        
        $id = $user->getId();
        $username = $user->getName();
        $realname = $user->getRealName();
        $email = $user->getEmail();
        
        return true;
    }
    
    /**
     * Get JWT token from request
     */
    private function getJwtToken() {
        $request = RequestContext::getMain()->getRequest();
        
        // Try cookie first
        $token = $request->getCookie('token');
        if ($token) {
            return $token;
        }
        
        // Try Authorization header
        $authHeader = $request->getHeader('Authorization');
        if ($authHeader && strpos($authHeader, 'Bearer ') === 0) {
            return substr($authHeader, 7);
        }
        
        return null;
    }
    
    /**
     * Validate JWT token with Laravel API
     */
    private function validateToken($token) {
        $ch = curl_init($this->apiUrl . '/api/auth/validate-token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $data = json_decode($response, true);
            return $data['user'] ?? null;
        }
        
        return null;
    }
    
    /**
     * Get or create MediaWiki user from Laravel user data
     */
    private function getOrCreateUser($userData) {
        $username = $userData['name'] ?? $userData['email'];
        
        // Sanitize username for MediaWiki
        $username = str_replace(' ', '_', $username);
        $username = preg_replace('/[^a-zA-Z0-9_]/', '', $username);
        
        $user = User::newFromName($username);
        
        if (!$user || $user->getId() === 0) {
            // Create new user
            $user = User::createNew($username, [
                'email' => $userData['email'] ?? '',
                'real_name' => $userData['name'] ?? '',
            ]);
            
            if (!$user) {
                return null;
            }
        } else {
            // Update existing user
            $user->setEmail($userData['email'] ?? '');
            $user->setRealName($userData['name'] ?? '');
            $user->saveSettings();
        }
        
        return $user;
    }
    
    /**
     * Save extra attributes (not used but required)
     */
    public function saveExtraAttributes($id, $attributes) {
        // Not needed for JWT auth
    }
    
    /**
     * Deauthenticate (logout)
     */
    public function deauthenticate(User &$user) {
        // Clear JWT token cookie
        $request = RequestContext::getMain()->getRequest();
        $response = RequestContext::getMain()->getOutput();
        
        // Redirect to Laravel logout
        $response->redirect($this->apiUrl . '/api/auth/logout');
    }
}
```

---

## Step 5: Laravel API Endpoint for Token Validation

### Add to AuthController.php

```php
/**
 * Validate JWT token for MediaWiki
 */
public function validateToken(Request $request): JsonResponse
{
    try {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token',
            ], 401);
        }
        
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Token validation failed',
        ], 401);
    }
}
```

### Add Route

```php
Route::get('auth/validate-token', [AuthController::class, 'validateToken'])
    ->middleware('jwt');
```

---

## Step 6: User Sync System

### Create MediaWiki User Sync Service

```php
// app/Services/MediaWikiUserSync.php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MediaWikiUserSync
{
    private $mediaWikiUrl;
    private $apiToken;
    
    public function __construct()
    {
        $this->mediaWikiUrl = config('mediawiki.url');
        $this->apiToken = config('mediawiki.api_token');
    }
    
    /**
     * Sync Laravel user to MediaWiki
     */
    public function syncUser(User $user): bool
    {
        try {
            // Create MediaWiki user via API
            $response = Http::post("{$this->mediaWikiUrl}/w/api.php", [
                'action' => 'createaccount',
                'username' => $this->sanitizeUsername($user->name),
                'email' => $user->email,
                'realname' => $user->name,
                'token' => $this->apiToken,
                'format' => 'json',
            ]);
            
            if ($response->successful()) {
                Log::info('User synced to MediaWiki', [
                    'user_id' => $user->id,
                    'username' => $user->name,
                ]);
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('Failed to sync user to MediaWiki', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
    
    /**
     * Sanitize username for MediaWiki
     */
    private function sanitizeUsername(string $name): string
    {
        $username = str_replace(' ', '_', $name);
        $username = preg_replace('/[^a-zA-Z0-9_]/', '', $username);
        return $username;
    }
}
```

---

## Step 7: Configuration Files

### config/mediawiki.php

```php
<?php

return [
    'url' => env('MEDIAWIKI_URL', 'https://wiki.yourdomain.com'),
    'api_token' => env('MEDIAWIKI_API_TOKEN', ''),
    'jwt_secret' => env('JWT_SECRET'),
];
```

### .env additions

```env
MEDIAWIKI_URL=https://wiki.yourdomain.com
MEDIAWIKI_API_TOKEN=your_api_token_here
```

---

## Step 8: Testing

1. **Test JWT Authentication:**
   - Laravel se login karo
   - JWT token mil jayega
   - MediaWiki mein same token se access karo

2. **Test User Sync:**
   - New user register karo Laravel mein
   - MediaWiki mein automatically create ho jayega

---

## Important Notes

1. **Same Database Use Kar Sakte Ho:**
   - MediaWiki aur Laravel same database use kar sakte hain
   - Different table prefixes use karo

2. **Security:**
   - JWT secret same hona chahiye dono systems mein
   - HTTPS use karo production mein

3. **User Data:**
   - Users ka data Laravel database mein rahega
   - MediaWiki sirf reference ke liye user create karega

---

## Troubleshooting

1. **Token not working:**
   - Check JWT secret same hai dono systems mein
   - Check CORS settings

2. **User not created:**
   - Check MediaWiki logs
   - Check Laravel API response

3. **Permission issues:**
   - Check MediaWiki user permissions
   - Check API token permissions
