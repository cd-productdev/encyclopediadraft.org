<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'email' => $this->email ?? null,
            'role' => $this->role ?? 'user',
        ];
    }

    /**
     * Get the watchlist entries for the user.
     */
    public function watchlists()
    {
        return $this->hasMany(Watchlist::class);
    }

    /**
     * Get notifications for the user
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get unread notifications
     */
    public function unreadNotifications()
    {
        return $this->notifications()->where('is_read', false);
    }

    /**
     * Get talk page messages for the user
     */
    public function talkPageMessages()
    {
        return $this->hasMany(UserTalkPage::class, 'user_id')->orderBy('created_at', 'desc');
    }

    /**
     * Get unread talk page messages
     */
    public function unreadTalkPageMessages()
    {
        return $this->talkPageMessages()->where('is_read', false);
    }

    /**
     * Get articles created by this user
     */
    public function createdArticles()
    {
        return $this->hasMany(Article::class, 'created_by');
    }
}
