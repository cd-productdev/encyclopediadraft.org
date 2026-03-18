<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Upload extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uploader_id',
        'original_filename',
        'destination_filename',
        'file_path',
        'file_type',
        'file_size',
        'description',
        'license',
        'watch',
        'ignore_warnings',
        'mime_type',
        'storage_disk',
        'width',
        'height',
        'duration',
        'file_hash',
        'usage_count',
        'thumbnail_path',
        'exif_data',
        'category',
        'version',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'watch' => 'boolean',
            'ignore_warnings' => 'boolean',
            'file_size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'duration' => 'integer',
            'usage_count' => 'integer',
            'version' => 'integer',
            'exif_data' => 'array',
        ];
    }

    /**
     * Get the user that uploaded the file.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }

    /**
     * Get the user who deleted the file
     */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Get file usage (which articles use this file)
     */
    public function usage()
    {
        return $this->hasMany(FileUsage::class);
    }

    /**
     * Get file history (versions)
     */
    public function history()
    {
        return $this->hasMany(FileHistory::class)->orderBy('version', 'desc');
    }

    /**
     * Get latest version
     */
    public function latestHistory()
    {
        return $this->hasOne(FileHistory::class)->latestOfMany('version');
    }
}
