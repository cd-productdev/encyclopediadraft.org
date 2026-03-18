<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileHistory extends Model
{
    protected $fillable = [
        'upload_id',
        'user_id',
        'version',
        'original_filename',
        'destination_filename',
        'file_path',
        'file_size',
        'file_type',
        'mime_type',
        'description',
        'license',
        'change_summary',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'version' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Get the file this history belongs to
     */
    public function upload(): BelongsTo
    {
        return $this->belongsTo(Upload::class);
    }

    /**
     * Get the user who uploaded this version
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
