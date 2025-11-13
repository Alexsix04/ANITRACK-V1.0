<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnimeListLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'anime_list_id',
    ];

    // Relación con el usuario que dio like
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con la lista de anime
    public function animeList()
    {
        return $this->belongsTo(AnimeList::class);
    }
}

