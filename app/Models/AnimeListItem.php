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
        'anime_title',
        'anime_image',
        'episode_progress',
        'score',
        'status',
        'notes',
        'is_rewatch',
        'rewatch_count',
    ];

    protected $casts = [
        'episode_progress' => 'integer',
        'score' => 'integer',
        'is_rewatch' => 'boolean',
        'rewatch_count' => 'integer',
    ];

    // Relaciones
    public function list()
    {
        return $this->belongsTo(AnimeList::class, 'anime_list_id');
    }
}