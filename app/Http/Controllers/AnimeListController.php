<?php

namespace App\Http\Controllers;

use App\Models\AnimeList;
use App\Models\AnimeListItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnimeListController extends Controller
{
    /**
     * Muestra las listas del usuario actual (para profile.index)
     */
    public function myLists()
    {
        $user = Auth::user();

        $lists = $user->animeLists()
            ->with('items') // Incluye los items de cada lista
            ->get();

        return view('profile.index', compact('lists'));
    }

    /**
     * Añadir un anime a una lista del usuario (desde animes.show)
     */
    public function addAnimeToList(Request $request)
    {
        $validated = $request->validate([
            'anime_id' => 'required|integer',
            'list_name'  => 'required|string', // "Vistos" o "Pendientes"
            'anime_title' => 'required|string',
            'anime_image' => 'nullable|string',
        ]);

        $user = Auth::user();

        // Buscar la lista correspondiente del usuario
        $list = $user->animeLists()->where('name', $validated['list_name'])->firstOrFail();

        // Evitamos duplicados
        $exists = $list->items()->where('anime_id', $validated['anime_id'])->exists();

        if (!$exists) {
            $list->items()->create([
                'anime_id'    => $validated['anime_id'],
                'anime_title' => $validated['anime_title'],
                'anime_image' => $validated['anime_image'],
            ]);
        }

        return back()->with('success', "Anime añadido a la lista {$validated['list_name']}.");
    }

    /**
     * Eliminar un anime de una lista
     */
    public function removeFromList(Request $request)
    {
        $validated = $request->validate([
            'anime_id' => 'required|integer',
            'list_name'  => 'required|string',
        ]);

        $user = Auth::user();

        $list = $user->animeLists()->where('name', $validated['list_name'])->firstOrFail();

        $list->items()->where('anime_id', $validated['anime_id'])->delete();

        return redirect()->back()->with('success', 'Anime eliminado de la lista.');
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_public' => 'required|boolean',
        ]);

        $list = Auth::user()->animeLists()->create([
            'name' => $validated['name'],
            'is_public' => $validated['is_public'],
        ]);

        return redirect()->back()->with('success', 'Lista creada correctamente.');
    }
}
