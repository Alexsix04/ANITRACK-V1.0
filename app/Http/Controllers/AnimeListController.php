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
        // 1️⃣ Validar los datos del formulario
        $validated = $request->validate([
            'anime_id'         => 'nullable|integer', // ID local, puede venir vacío si no existe
            'anilist_id'       => 'required|integer', // ID original de AniList
            'list_name'        => 'required|string',  // Nombre de la lista
            'anime_title'      => 'required|string',  // Título del anime
            'anime_image'      => 'nullable|string',  // Imagen de portada
            'episode_progress' => 'nullable|integer|min:0',
            'score'            => 'nullable|integer|min:0|max:10',
            'status'           => 'nullable|string|in:watching,completed,on_hold,dropped,plan_to_watch',
            'notes'            => 'nullable|string|max:1000',
            'is_rewatch'       => 'boolean',
            'rewatch_count'    => 'nullable|integer|min:0',
        ]);

        $user = Auth::user();
        $animeIdLocal = $validated['anime_id'] ?? null; // Puede ser nulo
        $anilistId = $validated['anilist_id'];
        $listName = $validated['list_name'];
        $isRewatch = filter_var($validated['is_rewatch'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $rewatchCount = $validated['rewatch_count'] ?? 0;

        // 2️⃣ Obtener o crear/actualizar el anime en la base de datos local
        // Si ya tenemos ID local, buscamos por él, si no, buscamos por AniList
        if ($animeIdLocal) {
            $anime = \App\Models\Anime::find($animeIdLocal);
        } else {
            $anime = \App\Models\Anime::where('anilist_id', $anilistId)->first();
        }

        // Obtener datos desde la API para crear o actualizar
        $animeData = app(\App\Services\AnilistService::class)->getAnimeById($anilistId);

        // Crear o actualizar el anime localmente
        $anime = \App\Models\Anime::updateOrCreate(
            ['anilist_id' => $anilistId], // Condición única para evitar duplicados
            [
                'title'       => $animeData['title']['romaji'] ?? $animeData['title']['english'] ?? $validated['anime_title'],
                'cover_image' => $animeData['coverImage']['large'] ?? $validated['anime_image'],
                'description' => $animeData['description'] ?? null,
                'episodes'    => $animeData['episodes'] ?? null,
                'status'      => $animeData['status'] ?? null,
                'season'      => $animeData['season'] ?? null,
            ]
        );


        // 3️⃣ Obtener la lista destino del usuario
        $list = $user->animeLists()->where('name', $listName)->firstOrFail();

        // 4️⃣ Reglas especiales para "Pendientes"
        if ($listName === 'Pendientes') {
            $vistosList = $user->animeLists()->where('name', 'Vistos')->first();
            if ($vistosList) {
                $vistosItem = $vistosList->items()->where('anime_id', $anime->id)->first();
                if ($vistosItem) {
                    $rewatchCount = $vistosItem->rewatch_count ?? 0;
                    if (!$isRewatch) {
                        return back()->with('warning', 'Este anime ya está en "Vistos". Marca "Rewatch" para añadirlo a Pendientes.');
                    }
                }
            }
        }

        // 5️⃣ Evitar duplicados en la lista destino
        $existsInList = $list->items()->where('anime_id', $anime->id)->exists();
        if ($existsInList) {
            return back()->with('warning', "Este anime ya está en la lista {$listName}.");
        }

        // 6️⃣ Regla especial para "Vistos": mínimo rewatch_count = 1
        if ($listName === 'Vistos') {
            $rewatchCount = max(1, (int)$rewatchCount);
        }

        // 7️⃣ Crear el registro en 'anime_list_items'
        $list->items()->create([
            'anime_id'         => $anime->id,          // ID local
            'anilist_id'       => $anime->anilist_id,  // ID de AniList
            'episode_progress' => $validated['episode_progress'] ?? 0,
            'score'            => $validated['score'] ?? null,
            'status'           => $validated['status'] ?? 'plan_to_watch',
            'notes'            => $validated['notes'] ?? null,
            'is_rewatch'       => $isRewatch,
            'rewatch_count'    => $rewatchCount,
        ]);

        // 8️⃣ Retornar con éxito
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

        $user = Auth::user();

        $oldStatus = $item->status;
        $isRewatch = filter_var($validated['is_rewatch'] ?? $item->is_rewatch, FILTER_VALIDATE_BOOLEAN);
        $rewatchCount = $validated['rewatch_count'] ?? $item->rewatch_count;

        if ($item->list->name === 'Vistos') {
            $rewatchCount = max(1, (int)$rewatchCount);
        }

        $item->update([
            'episode_progress' => $validated['episode_progress'] ?? $item->episode_progress,
            'score'            => $validated['score'] ?? $item->score,
            'status'           => $validated['status'] ?? $item->status,
            'notes'            => $validated['notes'] ?? $item->notes,
            'is_rewatch'       => $isRewatch,
            'rewatch_count'    => $rewatchCount,
        ]);

        // Manejo especial solo para "Pendientes" → "Vistos"
        if ($oldStatus !== 'completed' && $item->status === 'completed' && $item->list->name === 'Pendientes') {
            $vistosList = $user->animeLists()->where('name', 'Vistos')->first();

            if ($vistosList) {
                $existingVisto = $vistosList->items()->where('anime_id', $item->anime_id)->first();

                if ($existingVisto) {
                    $changes = [];
                    $existingVisto->rewatch_count += 1;

                    if ($existingVisto->score != $item->score) $changes['score'] = $item->score;
                    if ($existingVisto->notes != $item->notes) $changes['notes'] = $item->notes;

                    $existingVisto->update(array_merge(['rewatch_count' => $existingVisto->rewatch_count], $changes));
                    $item->delete();

                    return response()->json([
                        'success' => true,
                        'message' => 'El anime ya estaba en Vistos. Se actualizó y se eliminó de Pendientes.',
                        'action' => 'moved_existing',
                        'rewatch_count' => $existingVisto->rewatch_count,
                        'changes' => $changes
                    ]);
                } else {
                    $vistosList->items()->create([
                        'anime_id'         => $item->anime_id,
                        'anilist_id'       => $item->anilist_id,
                        'episode_progress' => $item->episode_progress,
                        'score'            => $item->score,
                        'status'           => 'completed',
                        'notes'            => $item->notes,
                        'is_rewatch'       => true,
                        'rewatch_count'    => 1,
                    ]);

                    $item->delete();

                    return response()->json([
                        'success' => true,
                        'message' => 'El anime se movió automáticamente de Pendientes a Vistos.',
                        'action' => 'moved_new'
                    ]);
                }
            }
        }

        // Cualquier otra actualización normal
        return response()->json([
            'success' => true,
            'message' => 'Anime actualizado correctamente.',
            'action' => 'updated'
        ]);
    }


    /**
     * Eliminar un anime de una lista
     */
    public function destroy($id)
    {
        $item = AnimeListItem::find($id);

        if (!$item) {
            return response()->json(['message' => '❌ El anime no se encontró en tu lista.'], 404);
        }

        $item->delete();

        return response()->json(['message' => '✅ Anime eliminado de tu lista correctamente.']);
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
    /**
     * Actualizar nombre, descripción y visibilidad de una lista de anime
     */
    public function update(Request $request, AnimeList $list)
    {
        // Bloquear si no pertenece al usuario
        if ($list->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para modificar esta lista.'
            ], 403);
        }

        // Bloquear renombrar "Vistos" y "Pendientes"
        $isDefault = in_array($list->name, ['Vistos', 'Pendientes']);

        // Validación
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_public' => 'required|boolean',
        ]);

        // Actualización condicional
        $list->update([
            'name' => $isDefault ? $list->name : ($validated['name'] ?? $list->name),
            'description' => $validated['description'] ?? $list->description,
            'is_public' => $validated['is_public'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lista actualizada correctamente.',
            'data' => $list,
        ]);
    }

    /**
     * Eliminar una lista (y todos sus items asociados)
     */
    public function delete(AnimeList $list)
    {
        // 🚫 Bloquear listas predeterminadas
        if (in_array($list->name, ['Vistos', 'Pendientes'])) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes eliminar las listas predeterminadas.'
            ], 403);
        }

        // 🗑 Eliminar todos los items relacionados
        $list->items()->delete();

        // 🧹 Eliminar la lista
        $list->delete();

        return response()->json([
            'success' => true,
            'message' => '✅ Lista eliminada correctamente.'
        ]);
    }
}
