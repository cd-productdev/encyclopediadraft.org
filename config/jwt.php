<?php

return [
    'secret' => env('JWT_SECRET', 'your-secret-key-change-this-in-production'),
    'expiration' => env('JWT_EXPIRATION', 60 * 24), // 24 hours in minutes
];
