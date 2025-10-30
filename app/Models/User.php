<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\AnimeCommentLike;
use App\Models\AnimeComment;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'bio',
    ];

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
            'password' => 'hashed',
        ];
    }

    public function getAvatarAttribute($value)
    {
        return $value ?? asset('images/avatars/default-avatar.png');
    }

    public function comments()
    {
        return $this->hasMany(AnimeComment::class);
    }

    public function animeCommentLikes()
    {
        return $this->hasMany(AnimeCommentLike::class);
    }

    // Helper: verificar si este usuario dio like a un comentario
    public function hasLiked(AnimeComment $comment): bool
    {
        return $this->animeCommentLikes()
            ->where('anime_comment_id', $comment->id)
            ->exists();
    }
    // === Accessor ===
    public function getAvatarUrlAttribute()
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : "https://ui-avatars.com/api/?name=" . urlencode($this->name) . '&background=0D8ABC&color=fff&bold=true';
    }
}
