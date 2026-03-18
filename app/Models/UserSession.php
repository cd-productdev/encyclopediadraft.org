<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'device_name',
        'user_agent',
        'city',
        'country',
        'token',
        'login_at',
        'last_login_at',
        'last_active_at',
        'logout_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'login_at' => 'datetime',
            'last_login_at' => 'datetime',
            'last_active_at' => 'datetime',
            'logout_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
