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
        if (!$this->avatar) {
            return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=0D8ABC&color=fff&bold=true';
        }

        // Si es una URL completa (por ejemplo, guardada con asset() o storage path)
        if (str_starts_with($this->avatar, 'http')) {
            return $this->avatar;
        }

        // Si es una ruta relativa (por ejemplo, 'avatars/user1.jpg')
        return asset('storage/' . $this->avatar);
    }

    public function characterCommentLikes()
    {
        return $this->hasMany(\App\Models\CharacterCommentLike::class);
    }

    public function hasLikedCharacter($comment)
    {
        return $this->characterCommentLikes()
            ->where('character_comment_id', $comment->id)
            ->exists();
    }
    // Relación con Animes Favoritos
    public function animeFavorites()
    {
        return $this->hasMany(AnimeFavorite::class);
    }

    //Relación con Personajes Favoritos
    public function characterFavorites()
    {
        return $this->hasMany(CharacterFavorite::class);
    }

    // Relación con listas de anime
    public function animeLists()
    {
        return $this->hasMany(AnimeList::class);
    }
    // Relación con listas de personajes
    public function characterLists()
    {
        return $this->hasMany(\App\Models\CharacterList::class);
    }
    // listas de anime guardadas
    public function savedAnimeLists()
    {
        return $this->belongsToMany(AnimeList::class, 'saved_anime_lists')
            ->withPivot('owner_id', 'owner_name')
            ->withTimestamps();
    }

    // listas de personajes guardadas
    public function savedCharacterLists()
    {
        return $this->belongsToMany(CharacterList::class, 'saved_character_lists')
            ->withPivot('owner_id', 'owner_name')
            ->withTimestamps();
    }
    // Likes de anime
    public function likedAnimeLists()
    {
        return $this->belongsToMany(AnimeList::class, 'anime_list_likes');
    }

    // Likes de personajes
    public function likedCharacterLists()
    {
        return $this->belongsToMany(CharacterList::class, 'character_list_likes');
    }

    // Relación con los likes de listas de anime
    public function animeListLikes()
    {
        return $this->hasMany(AnimeListLike::class);
    }
    // Relación con los likes de listas de personajes
    public function characterListLikes()
    {
        return $this->hasMany(CharacterListLike::class);
    }
}