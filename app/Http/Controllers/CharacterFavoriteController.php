<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CharacterFavorite;

class CharacterFavoriteController extends Controller
{
    /**
     * Añadir personaje a favoritos (modo tradicional con recarga)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'character_id' => 'required|integer',
            'character_name' => 'required|string',
            'character_image' => 'nullable|string',
            'anime_id' => 'nullable|integer', // 👈 añadimos esto
        ]);

        // Crear el favorito si no existe
        Auth::user()->characterFavorites()->firstOrCreate(
            ['character_id' => $data['character_id']],
            [
                'character_name'  => $data['character_name'],
                'character_image' => $data['character_image'] ?? null,
                'anime_id'        => $data['anime_id'] ?? null, // 👈 incluimos anime_id
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

    /**
     * Alternar personaje en favoritos (modo AJAX sin recarga)
     */
    public function toggleCharacter(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'character_id' => 'required|integer',
            'character_name' => 'required|string',
            'character_image' => 'nullable|string',
            'anime_id' => 'nullable|integer', 
        ]);

        $favorite = $user->characterFavorites()
            ->where('character_id', $data['character_id'])
            ->first();

        if ($favorite) {
            // Si ya estaba en favoritos → eliminar
            $favorite->delete();

            return response()->json([
                'status' => 'removed',
                'message' => 'Personaje eliminado de favoritos',
            ]);
        } else {
            // Si no estaba → añadir
            $user->characterFavorites()->create([
                'character_id'   => $data['character_id'],
                'character_name' => $data['character_name'],
                'character_image'=> $data['character_image'] ?? null,
                'anime_id'       => $data['anime_id'] ?? null, 
            ]);

            return response()->json([
                'status' => 'added',
                'message' => 'Personaje añadido a favoritos',
            ]);
        }
    }
}