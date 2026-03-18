<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleAttribute extends Model
{
    // Allow mass assignment for all fields
    protected $guarded = [];

    // Optional: If you use created_at / updated_at timestamps
    public $timestamps = true;

    // Optional: Cast JSON values automatically
    protected $casts = [
        'value' => 'string', // or 'array' if you store JSON
    ];

    /**
     * Relation to the parent article
     */
    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
