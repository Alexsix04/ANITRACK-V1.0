<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CharacterListItem extends Model
{
    use HasFactory;

    protected $table = 'character_list_items';

    /**
     * Atributos asignables masivamente.
     */
    protected $fillable = [
        'user_id',
        'list_id',
        'character_id',
        'anime_id',
        'anime_anilist_id',
        'anilist_id',
        'score',
        'notes',
    ];

    /**
     * ============================
     *  RELACIONES
     * ============================
     */

    // Usuario propietario del ítem
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Lista a la que pertenece el ítem
    public function list()
    {
        return $this->belongsTo(CharacterList::class, 'list_id');
    }

    // Personaje local asociado
    public function character()
    {
        return $this->belongsTo(Character::class);
    }

    // Anime local asociado
    public function anime()
    {
        return $this->belongsTo(Anime::class);
    }

    /**
     * ============================
     *  MÉTODOS ÚTILES
     * ============================
     */

    // Devuelve la URL local al personaje
    public function getCharacterUrlAttribute(): string
    {
        return url("/animes/{$this->anime_anilist_id}/characters/{$this->anilist_id}");
    }

    // Devuelve la URL local al anime del personaje
    public function getAnimeUrlAttribute(): string
    {
        return url("/animes/{$this->anime_anilist_id}");
    }

    // Helper para mostrar el nombre del personaje sin repetir lógica
    public function getCharacterNameAttribute(): ?string
    {
        return $this->character->name ?? null;
    }

    // Helper para obtener la imagen del personaje
    public function getCharacterImageAttribute(): ?string
    {
        return $this->character->image ?? null;
    }
}
