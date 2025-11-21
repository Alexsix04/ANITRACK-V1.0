<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AnimeList;
use App\Models\SavedAnimeList;

class AnimeRandomizerController extends Controller
{
    /**
     * Mostrar la vista del randomizer con las listas del usuario y las listas guardadas.
     */
    public function index()
    {
        $userLists = AnimeList::where('user_id', auth()->id())->get();
        $savedLists = SavedAnimeList::all(); // todas las listas guardadas

        return view('randomizer.index', compact('userLists', 'savedLists'));
    }

    /**
     * Obtener un anime aleatorio de una lista (propia o guardada).
     */
    public function getRandomAnime(Request $request)
    {
        $request->validate([
            'list_id' => 'required|integer',
            'list_type' => 'required|in:user,saved',
        ]);

        if ($request->list_type === 'user') {
            // Lista propia del usuario
            $list = AnimeList::where('id', $request->list_id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            $randomItem = $list->items()->with('anime')->inRandomOrder()->first();
        } else {
            // Lista guardada → obtenemos la lista original
            $savedList = SavedAnimeList::findOrFail($request->list_id);
            $list = $savedList->list; // relación con la lista original

            if (!$list) {
                return response()->json(['error' => 'La lista guardada no tiene lista asociada.'], 404);
            }

            $randomItem = $list->items()->with('anime')->inRandomOrder()->first();
        }

        if (!$randomItem || !$randomItem->anime) {
            return response()->json(['error' => 'La lista no tiene items.'], 404);
        }

        return response()->json([
            'title' => $randomItem->anime->title,
            'image' => $randomItem->anime->cover_image,
        ]);
    }
    public function getListAnimes(Request $request)
    {
        $request->validate([
            'list_id' => 'required',
            'list_type' => 'required|in:user,saved'
        ]);

        if ($request->list_type === 'user') {
            $list = AnimeList::with('items.anime')->find($request->list_id);
        } else {
            $saved = SavedAnimeList::with('list.items.anime')->find($request->list_id);
            $list = $saved ? $saved->list : null;
        }

        if (!$list) {
            return response()->json(['error' => 'Lista no encontrada'], 404);
        }

        $animes = $list->items->map(function ($item) {
            return [
                'title' => $item->anime->title,
                'image' => $item->anime->cover_image
            ];
        });

        return response()->json([
            'animes' => $animes
        ]);
    }
}
