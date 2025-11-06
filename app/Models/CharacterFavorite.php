<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CharacterFavorite extends Model
{
    protected $fillable = [
        'user_id',
        'character_id',
        'anilist_id',
        'anime_id',
        'anime_anilist_id',
        'character_name',
        'character_image'
    ];

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con el personaje
    public function character()
    {
        return $this->belongsTo(Character::class, 'character_id');
    }

    // Relación con el anime 
    public function anime()
    {
        return $this->belongsTo(Anime::class, 'anime_id');
    }
}
