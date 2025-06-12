<?php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'avatar', 'type'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'type' => 'string',
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function articleComments(): HasMany
    {
        return $this->hasMany(ArticleComment::class);
    }

    public function articleRatings(): HasMany
    {
        return $this->hasMany(ArticleRating::class);
    }

    public function communityPosts(): HasMany
    {
        return $this->hasMany(CommunityPost::class);
    }

    public function communityPostComments(): HasMany
    {
        return $this->hasMany(CommunityPostComment::class);
    }

    // Fix the certified researcher relationship
    public function certifiedResearcher(): HasOne
    {
        return $this->hasOne(CertifiedResearcher::class, 'user_id');
    }

    // Or if you want multiple applications (HasMany)
    public function certifiedResearchers(): HasMany
    {
        return $this->hasMany(CertifiedResearcher::class, 'user_id');
    }

    public function isAdmin(): bool
    {
        return $this->type === 'admin';
    }

    public function isResearcher(): bool
    {
        return $this->type === 'researcher';
    }

    public function isNormal(): bool
    {
        return $this->type === 'normal';
    }
}
