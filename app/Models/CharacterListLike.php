<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CharacterListLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'character_list_id',
    ];

    // Relación con el usuario que dio like
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con la lista de personajes
    public function characterList()
    {
        return $this->belongsTo(CharacterList::class);
    }
}
