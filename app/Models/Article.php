<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Article extends Model
{
    use SoftDeletes;

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_PUBLISHED = 'published';
    const STATUS_REJECTED = 'rejected';

    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'references' => 'array',
            'submitted_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                // Generate unique base64-encoded slug
                $randomString = uniqid($article->title, true);
                $article->slug = rtrim(base64_encode($randomString), '=');
            }
        });

        static::updating(function ($article) {
            if ($article->isDirty('title') && empty($article->slug)) {
                $randomString = uniqid($article->title, true);
                $article->slug = rtrim(base64_encode($randomString), '=');
            }
        });
    }

    /**
     * Get the user that created the article.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who reviewed the article.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function attributes()
    {
        return $this->hasMany(ArticleAttribute::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function occupation()
    {
        return $this->hasOne(Occupation::class);
    }

    public function politician()
    {
        return $this->hasOne(Politician::class);
    }

    public function family()
    {
        return $this->hasOne(Family::class);
    }

    public function socialLinks()
    {
        return $this->hasMany(SocialLink::class);
    }

    /**
     * Scope for full-text search (semantic search)
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $searchTerm
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $searchTerm)
    {
        if (empty($searchTerm)) {
            return $query;
        }

        // Use MySQL FULLTEXT search for semantic-like matching
        return $query->whereRaw(
            "MATCH(title, content, summary) AGAINST(? IN NATURAL LANGUAGE MODE)",
            [$searchTerm]
        )->selectRaw(
            "*, MATCH(title, content, summary) AGAINST(? IN NATURAL LANGUAGE MODE) as relevance_score",
            [$searchTerm]
        )->orderByDesc('relevance_score');
    }

    /**
     * Get the user who deleted the article
     */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Get article protection
     */
    public function protection()
    {
        return $this->hasOne(ArticleProtection::class)->where('is_active', true);
    }

    /**
     * Check if article is protected
     */
    public function isProtected(): bool
    {
        $protection = $this->protection;
        if (! $protection) {
            return false;
        }

        // Check if protection has expired
        if ($protection->expires_at && $protection->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Check if article is published
     */
    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    /**
     * Check if article is draft
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Check if article is pending review
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if article is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if article can be submitted by a specific user
     */
    public function canBeSubmittedBy(int $userId): bool
    {
        // Only the creator can submit their own draft or rejected articles
        if ($this->created_by !== $userId) {
            return false;
        }

        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REJECTED]);
    }

    /**
     * Check if article can be reviewed by a specific role
     */
    public function canBeReviewedBy(string $role): bool
    {
        // Only admins and moderators can review
        if (!in_array($role, ['admin', 'moderator'])) {
            return false;
        }

        // Can only review pending articles
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if user can edit this article
     */
    public function canBeEditedBy(?int $userId, ?string $userRole = null): bool
    {
        if (! $this->isProtected()) {
            return true;
        }

        // Admins can always edit
        if ($userRole === 'admin') {
            return true;
        }

        // For semi-protected, only admins and moderators can edit
        $protection = $this->protection;
        if ($protection && $protection->protection_level === 'semi-protected') {
            return in_array($userRole, ['admin', 'moderator']);
        }

        // Fully protected - only admins
        return $userRole === 'admin';
    }

    /**
     * Get redirects for this article
     */
    public function redirects()
    {
        return $this->hasMany(Redirect::class)->where('is_active', true);
    }

    /**
     * Get files used in this article
     */
    public function fileUsage()
    {
        return $this->hasMany(FileUsage::class);
    }

    /**
     * Get translations of this article (other language versions)
     */
    public function translations()
    {
        return $this->hasMany(ArticleTranslation::class, 'article_id')
            ->where('is_active', true)
            ->with('translatedArticle');
    }

    /**
     * Get the language this article is in
     */
    public function language()
    {
        return $this->belongsTo(Language::class, 'language_code', 'code');
    }

    /**
     * Get available language versions of this article
     */
    public function getAvailableLanguages()
    {
        $languages = [];

        // Add current article's language
        $currentLang = Language::where('code', $this->language_code)->first();
        if ($currentLang) {
            $languages[] = [
                'code' => $currentLang->code,
                'name' => $currentLang->name,
                'native_name' => $currentLang->native_name,
                'is_current' => true,
                'article_id' => $this->id,
            ];
        }

        // Add translated versions
        foreach ($this->translations as $translation) {
            $lang = Language::where('code', $translation->language_code)->first();
            if ($lang && $translation->translatedArticle) {
                $languages[] = [
                    'code' => $lang->code,
                    'name' => $lang->name,
                    'native_name' => $lang->native_name,
                    'is_current' => false,
                    'article_id' => $translation->translated_article_id,
                ];
            }
        }

        return $languages;
    }
}
