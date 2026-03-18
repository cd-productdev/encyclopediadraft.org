<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileUsage extends Model
{
    protected $fillable = [
        'upload_id',
        'article_id',
        'usage_type',
        'context',
    ];

    /**
     * Get the file being used
     */
    public function upload(): BelongsTo
    {
        return $this->belongsTo(Upload::class);
    }

    /**
     * Get the article using the file
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
