<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnimeListItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'anime_list_id',
        'anime_id',
        'anilist_id',
        'episode_progress',
        'score',
        'status',
        'notes',
        'is_rewatch',
        'rewatch_count',
    ];

    /**
     * Relación con la lista a la que pertenece este item.
     */
    public function list()
    {
        return $this->belongsTo(AnimeList::class, 'anime_list_id');
    }

    /**
     * Relación con el anime local (en la tabla animes).
     */
    public function anime()
    {
        return $this->belongsTo(Anime::class);
    }
}