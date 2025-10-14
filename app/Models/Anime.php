<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anime extends Model
{

    protected $fillable = [
        'anilist_id',
        'title',
        'description',
        'cover_image',
        'banner_image',
        'format',
        'episodes',
        'season',
        'season_year',
        'status',
        'average_score',
        'genres',
        'studios',
        'tags',
    ];

    protected $casts = [
        'genres' => 'array',
        'studios' => 'array',
        'tags' => 'array',
    ];

    // Relación muchos a muchos con Character
    public function characters()
    {
        return $this->belongsToMany(Character::class, 'anime_character', 'anime_id', 'character_id');
    }
}