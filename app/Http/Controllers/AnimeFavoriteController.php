<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AnimeFavoriteController extends Controller
{
    /**
     * Añadir anime a favoritos (modo tradicional con recarga)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'anime_id' => 'required|integer',
            'anime_title' => 'required|string',
            'anime_image' => 'nullable|string',
        ]);

        auth()->user()->animeFavorites()->firstOrCreate(
            ['anime_id' => $data['anime_id']],
            $data
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
            'anime_id' => 'required|integer',
            'anime_title' => 'required|string',
            'anime_image' => 'nullable|string',
        ]);

        // Verifica si ya está en favoritos
        $favorite = $user->animeFavorites()->where('anime_id', $data['anime_id'])->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json([
                'status' => 'removed',
                'message' => 'Anime eliminado de favoritos'
            ]);
        } else {
            $user->animeFavorites()->create($data);
            return response()->json([
                'status' => 'added',
                'message' => 'Anime añadido a favoritos'
            ]);
        }
    }
}