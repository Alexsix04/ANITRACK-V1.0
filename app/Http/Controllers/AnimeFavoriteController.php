<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anime; // 👈 Importamos el modelo Anime
use App\Models\AnimeFavorite;

class AnimeFavoriteController extends Controller
{
    /**
     * Añadir anime a favoritos (modo tradicional con recarga)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'anime_id' => 'required|integer', // anilist_id
            'anime_title' => 'required|string',
            'anime_image' => 'nullable|string',
        ]);

        // 1️⃣ Guardar o actualizar el anime en tabla 'animes'
        $anime = Anime::firstOrCreate(
            ['anilist_id' => $data['anime_id']],
            [
                'title' => $data['anime_title'],
                'cover_image' => $data['anime_image'],
            ]
        );

        // 2️⃣ Guardar favorito (referencia local)
        auth()->user()->animeFavorites()->firstOrCreate(
            ['anime_id' => $anime->id],
            [
                'anilist_id' => $anime->anilist_id,
                'anime_title' => $anime->title,
                'anime_image' => $anime->cover_image,
            ]
        );

        return back()->with('success', 'Anime añadido a favoritos');
    }

    /**
     * Eliminar anime de favoritos (modo tradicional con recarga)
     */
    public function destroy($animeId)
    {
        auth()->user()->animeFavorites()
            ->where('anime_id', $animeId)
            ->delete();

        return back()->with('success', 'Anime eliminado de favoritos');
    }

    /**
     * Método para alternar favoritos (modo AJAX sin recargar)
     */
    public function toggleAnime(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'anilist_id' => 'required|integer', // <-- cambiamos de anime_id a anilist_id
            'anime_title' => 'required|string',
            'anime_image' => 'nullable|string',
        ]);

        // 1️⃣ Buscar o crear el anime en la tabla local
        $anime = Anime::firstOrCreate(
            ['anilist_id' => $data['anilist_id']],
            [
                'title' => $data['anime_title'],
                'cover_image' => $data['anime_image'],
            ]
        );

        // 2️⃣ Verificar si ya está en favoritos
        $favorite = $user->animeFavorites()
            ->where('anime_id', $anime->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json([
                'status' => 'removed',
                'message' => 'Anime eliminado de favoritos',
            ]);
        } else {
            $user->animeFavorites()->create([
                'anime_id' => $anime->id,
                'anilist_id' => $anime->anilist_id,
                'anime_title' => $anime->title,
                'anime_image' => $anime->cover_image,
            ]);

            return response()->json([
                'status' => 'added',
                'message' => 'Anime añadido a favoritos',
            ]);
        }
    }
}
