<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTalkPage extends Model
{
    protected $fillable = [
        'user_id',
        'from_user_id',
        'message',
        'subject',
        'is_read',
        'read_at',
        'reply_to',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the user who owns this talk page
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who left the message
     */
    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    /**
     * Get the parent message (if this is a reply)
     */
    public function parentMessage(): BelongsTo
    {
        return $this->belongsTo(UserTalkPage::class, 'reply_to');
    }

    /**
     * Get replies to this message
     */
    public function replies()
    {
        return $this->hasMany(UserTalkPage::class, 'reply_to');
    }

    /**
     * Mark message as read
     */
    public function markAsRead(): void
    {
        if (! $this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }
}
