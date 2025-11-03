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
            ->with('items')
            ->get();

        return view('profile.index', compact('lists'));
    }

    /**
     * Añadir un anime a una lista del usuario (desde animes.show)
     * Ahora admite los nuevos campos (episodio, score, notas, etc.)
     */
    public function addAnimeToList(Request $request)
    {
        $validated = $request->validate([
            'anime_id'         => 'required|integer',
            'list_name'        => 'required|string', // Ej: "Vistos", "Pendientes", etc.
            'anime_title'      => 'required|string',
            'anime_image'      => 'nullable|string',
            'episode_progress' => 'nullable|integer|min:0',
            'score'            => 'nullable|integer|min:0|max:10',
            'status'           => 'nullable|string|in:watching,completed,on_hold,dropped,plan_to_watch',
            'notes'            => 'nullable|string|max:1000',
            'is_rewatch'       => 'boolean',
            'rewatch_count'    => 'nullable|integer|min:0',
        ]);

        $user = Auth::user();
        $animeId = $validated['anime_id'];
        $listName = $validated['list_name'];
        $isRewatch = filter_var($request->input('is_rewatch', false), FILTER_VALIDATE_BOOLEAN);

        // Lista destino
        $list = $user->animeLists()->where('name', $listName)->firstOrFail();

        // Excepción especial para "Pendientes"
        if ($listName === 'Pendientes') {
            $vistosList = $user->animeLists()->where('name', 'Vistos')->first();
            $existsInVistos = $vistosList ? $vistosList->items()->where('anime_id', $animeId)->exists() : false;

            if ($existsInVistos && !$isRewatch) {
                return back()->with('warning', 'Este anime ya está en "Vistos". Marca "Rewatch" para añadirlo a Pendientes.');
            }
        }

        // Evitar duplicados normales en la lista destino
        $existsInList = $list->items()->where('anime_id', $animeId)->exists();
        if ($existsInList) {
            return back()->with('warning', "Este anime ya está en la lista {$listName}.");
        }

        // Crear el registro del anime en la lista
        $list->items()->create([
            'anime_id'         => $animeId,
            'anime_title'      => $validated['anime_title'],
            'anime_image'      => $validated['anime_image'] ?? null,
            'episode_progress' => $validated['episode_progress'] ?? 0,
            'score'            => $validated['score'] ?? null,
            'status'           => $validated['status'] ?? 'plan_to_watch',
            'notes'            => $validated['notes'] ?? null,
            'is_rewatch'       => $isRewatch,
            'rewatch_count'    => $validated['rewatch_count'] ?? 0,
        ]);

        return back()->with('success', "Anime añadido correctamente a la lista {$listName}.");
    }
    /**
     * Actualizar los datos de un anime dentro de una lista
     */
    public function updateAnimeInList(Request $request, AnimeListItem $item)
    {
        $validated = $request->validate([
            'episode_progress' => 'nullable|integer|min:0',
            'score'            => 'nullable|integer|min:0|max:10',
            'status'           => 'nullable|string|in:watching,completed,on_hold,dropped,plan_to_watch',
            'notes'            => 'nullable|string|max:1000',
            'is_rewatch'       => 'boolean',
            'rewatch_count'    => 'nullable|integer|min:0',
        ]);

        $item->update([
            'episode_progress' => $validated['episode_progress'] ?? $item->episode_progress,
            'score'            => $validated['score'] ?? $item->score,
            'status'           => $validated['status'] ?? $item->status,
            'notes'            => $validated['notes'] ?? $item->notes,
            'is_rewatch'       => $request->boolean('is_rewatch'),
            'rewatch_count'    => $validated['rewatch_count'] ?? $item->rewatch_count,
        ]);

        return back()->with('success', 'Información de la lista actualizada correctamente.');
    }

    /**
     * Eliminar un anime de una lista
     */
    public function removeFromList(Request $request)
    {
        $validated = $request->validate([
            'anime_id'  => 'required|integer',
            'list_name' => 'required|string',
        ]);

        $user = Auth::user();

        $list = $user->animeLists()->where('name', $validated['list_name'])->firstOrFail();

        $list->items()->where('anime_id', $validated['anime_id'])->delete();

        return redirect()->back()->with('success', 'Anime eliminado de la lista.');
    }

    /**
     * Crear una nueva lista (desde formulario normal)
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_public' => 'required|boolean',
        ]);

        Auth::user()->animeLists()->create($validated);

        return redirect()->back()->with('success', 'Lista creada correctamente.');
    }

    /**
     * Crear lista mediante AJAX (usada por el submodal)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_public' => 'required|boolean',
        ]);

        // Evitar duplicados (misma lista del mismo usuario)
        $existing = AnimeList::where('user_id', auth()->id())
            ->where('name', $validated['name'])
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Ya tienes una lista con ese nombre.',
            ], 409);
        }

        $list = AnimeList::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'is_public' => $validated['is_public'],
        ]);

        return response()->json([
            'success' => true,
            'list' => [
                'id' => $list->id,
                'name' => $list->name,
            ],
        ]);
    }
}
