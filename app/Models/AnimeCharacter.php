<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnimeCharacter extends Model
{
    protected $table = 'anime_characters';

    protected $fillable = [
        'anime_id',
        'character_id',
    ];
}