<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CharacterList extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'description', 'is_public',];
    // Relación con el usuario que creó la lista
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // Relación con los ítems de la lista
    public function items()
    {
        return $this->hasMany(CharacterListItem::class, 'list_id');
    }
    // Relación con los usuarios que guardaron esta lista
    public function savedByUsers()
    {
        return $this->belongsToMany(User::class, 'saved_character_lists')->withTimestamps();
    }
    // Relación con los usuarios que le dieron like a esta lista
    public function likes()
    {
        return $this->belongsToMany(User::class, 'character_list_likes');
    }
}
