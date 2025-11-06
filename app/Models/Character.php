<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    protected $table = 'characters';

    protected $fillable = [
        'anilist_id',
        'anime_id',
        'anime_anilist_id',
        'name',
        'native_name',
        'image',
        'description',
        'role',
        'gender',
        'age',
        'image_url',
    ];

    // Relación muchos a muchos con Anime
    public function animes()
    {
        return $this->belongsToMany(Anime::class, 'anime_character', 'character_id', 'anime_id');
    }
}