<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CharacterFavorite extends Model
{
    protected $fillable = [
        'user_id', 'character_id','anime_id', 'character_name', 'character_image'
    ];
    
    public function anime()
    {
        return $this->belongsTo(Anime::class, 'anime_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

