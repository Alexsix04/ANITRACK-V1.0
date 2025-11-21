<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnimeList extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'is_public', 'description'];
    // Relación con el usuario que creó la lista
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // Relación con los ítems de la lista
    public function items()
    {
        return $this->hasMany(AnimeListItem::class, 'anime_list_id');
    }
    // Relación con los usuarios que guardaron esta lista
    public function savedByUsers()
    {
        return $this->belongsToMany(User::class, 'saved_anime_lists')->withTimestamps();
    }
    // Relación con los likes de la lista
    public function likes()
    {
        return $this->belongsToMany(User::class, 'anime_list_likes');
    }
    // Verificar si un usuario ha dado like a esta lista
    public function likedBy(User $user)
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }
    // Obtener un anime Random
    public function getRandomItem()
{
    return $this->items()->inRandomOrder()->first();
}
}
