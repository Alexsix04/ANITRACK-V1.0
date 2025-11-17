<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CharacterFavorite;
use App\Models\Character;
use App\Models\Anime;

class CharacterFavoriteController extends Controller
{
    /**
     * Añadir personaje a favoritos (modo tradicional con recarga)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'character_anilist_id' => 'required|integer',
            'character_name'       => 'required|string',
            'character_image'      => 'nullable|string',
            'anime_anilist_id'     => 'required|integer',
            'anime_name'           => 'required|string',
            'anime_image'          => 'nullable|string',
        ]);

        //  Buscar o crear anime en la DB local
        $anime = Anime::firstOrCreate(
            ['anilist_id' => $data['anime_anilist_id']],
            [
                'title'       => $data['anime_name'],
                'cover_image' => $data['anime_image'] ?? null,
            ]
        );

        //  Buscar o crear personaje en la DB local
        $character = Character::firstOrCreate(
            ['anilist_id' => $data['character_anilist_id']],
            [
                'name'            => $data['character_name'],
                'image_url'       => $data['character_image'] ?? null,
                'anime_id'        => $anime->id,
                'anime_anilist_id' => $anime->anilist_id,
            ]
        );

        //  Crear favorito si no existe
        Auth::user()->characterFavorites()->firstOrCreate(
            ['character_id' => $character->id],
            [
                'anilist_id'       => $character->anilist_id,
                'character_name'   => $character->name,
                'character_image'  => $character->image_url,
                'anime_id'         => $anime->id,
                'anime_anilist_id' => $anime->anilist_id,
            ]
        );

        return back()->with('success', 'Personaje añadido a favoritos');
    }

    /**
     * Eliminar personaje de favoritos (modo tradicional con recarga)
     */
    public function destroy($characterId)
    {
        Auth::user()->characterFavorites()
            ->where('character_id', $characterId)
            ->delete();

        return back()->with('success', 'Personaje eliminado de favoritos');
    }

    public function toggleCharacter(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'character_anilist_id' => 'required|integer',
            'character_name'       => 'required|string',
            'character_image'      => 'nullable|string',
            'anime_anilist_id'     => 'required|integer',
            'anime_name'           => 'required|string',
            'anime_image'          => 'nullable|string',
        ]);

        // Crear o buscar el anime
        $anime = Anime::firstOrCreate(
            ['anilist_id' => $data['anime_anilist_id']],
            [
                'title'       => $data['anime_name'],
                'cover_image' => $data['anime_image'] ?? null,
            ]
        );

        // Crear o buscar el personaje
        $character = Character::firstOrCreate(
            ['anilist_id' => $data['character_anilist_id']],
            [
                'name'             => $data['character_name'],
                'image_url'        => $data['character_image'] ?? null,
                'anime_id'         => $anime->id,
                'anime_anilist_id' => $anime->anilist_id,
            ]
        );

        // Verificar si ya está en favoritos
        $favorite = $user->characterFavorites()
            ->where('character_id', $character->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['status' => 'removed']);
        } else {
            $user->characterFavorites()->create([
                'character_id'     => $character->id,
                'anilist_id'       => $character->anilist_id,
                'character_name'   => $character->name,
                'character_image'  => $character->image_url,
                'anime_id'         => $anime->id,
                'anime_anilist_id' => $anime->anilist_id,
            ]);
            return response()->json(['status' => 'added']);
        }
    }
}
