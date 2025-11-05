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

    //  EXCEPCIÓN ESPECIAL PARA "Pendientes"
    $rewatchCount = $validated['rewatch_count'] ?? 0;

    if ($listName === 'Pendientes') {
        $vistosList = $user->animeLists()->where('name', 'Vistos')->first();

        if ($vistosList) {
            $vistosItem = $vistosList->items()->where('anime_id', $animeId)->first();

            if ($vistosItem) {
                //  Copiar el rewatch_count de Vistos si existe
                $rewatchCount = $vistosItem->rewatch_count ?? 0;

                //  Evitar duplicar sin marcar Rewatch
                if (!$isRewatch) {
                    return back()->with('warning', 'Este anime ya está en "Vistos". Marca "Rewatch" para añadirlo a Pendientes.');
                }
            }
        }
    }

    //  Evitar duplicados normales en la lista destino==
   
    $existsInList = $list->items()->where('anime_id', $animeId)->exists();
    if ($existsInList) {
        return back()->with('warning', "Este anime ya está en la lista {$listName}.");
    }
    
    // REGLA ESPECIAL PARA "Vistos": rewatch_count mínimo 1
    if ($listName === 'Vistos') {
        $rewatchCount = max(1, (int) $rewatchCount);
    }
    
    //  CREAR EL REGISTRO

    $list->items()->create([
        'anime_id'         => $animeId,
        'anime_title'      => $validated['anime_title'],
        'anime_image'      => $validated['anime_image'] ?? null,
        'episode_progress' => $validated['episode_progress'] ?? 0,
        'score'            => $validated['score'] ?? null,
        'status'           => $validated['status'] ?? 'plan_to_watch',
        'notes'            => $validated['notes'] ?? null,
        'is_rewatch'       => $isRewatch,
        'rewatch_count'    => $rewatchCount,
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

    $user = Auth::user();

    // Guardamos el estado anterior
    $oldStatus = $item->status;

    // Actualizamos los campos editados
    $item->update([
        'episode_progress' => $validated['episode_progress'] ?? $item->episode_progress,
        'score'            => $validated['score'] ?? $item->score,
        'status'           => $validated['status'] ?? $item->status,
        'notes'            => $validated['notes'] ?? $item->notes,
        'is_rewatch'       => $request->boolean('is_rewatch'),
        'rewatch_count'    => $validated['rewatch_count'] ?? $item->rewatch_count,
    ]);

    // Si el estado cambió a "completed" y pertenece a la lista Pendientes:
    if ($oldStatus !== 'completed' && $item->status === 'completed') {
        $pendientesList = $item->list;
        if ($pendientesList->name === 'Pendientes') {
            // Buscar la lista Vistos del mismo usuario
            $vistosList = $user->animeLists()->where('name', 'Vistos')->first();

            if ($vistosList) {
                $existingVisto = $vistosList->items()->where('anime_id', $item->anime_id)->first();

                if ($existingVisto) {
                    // Si ya existe, incrementamos rewatch_count y actualizamos score/notas si difieren
                    $changes = [];
                    $existingVisto->rewatch_count = $existingVisto->rewatch_count + 1;

                    if ($item->score != $existingVisto->score || $item->notes != $existingVisto->notes) {
                        $changes['score'] = $item->score;
                        $changes['notes'] = $item->notes;
                    }

                    $existingVisto->update(array_merge([
                        'rewatch_count' => $existingVisto->rewatch_count,
                    ], $changes));

                    // Eliminamos el registro de Pendientes
                    $item->delete();

                    return response()->json([
                        'success' => true,
                        'message' => 'El anime ya estaba en Vistos. Se actualizó y se eliminó de Pendientes.',
                        'action'  => 'moved_existing',
                        'rewatch_count' => $existingVisto->rewatch_count,
                        'changes' => $changes
                    ]);
                } else {
                    // Si no existe en Vistos, lo movemos creando una nueva entrada
                    $vistosList->items()->create([
                        'anime_id'         => $item->anime_id,
                        'anime_title'      => $item->anime_title,
                        'anime_image'      => $item->anime_image,
                        'episode_progress' => $item->episode_progress,
                        'score'            => $item->score,
                        'status'           => 'completed',
                        'notes'            => $item->notes,
                        'is_rewatch'       => true,
                        'rewatch_count'    => 1,
                    ]);

                    // Eliminamos de Pendientes
                    $item->delete();

                    return response()->json([
                        'success' => true,
                        'message' => 'El anime se movió automáticamente de Pendientes a Vistos.',
                        'action'  => 'moved_new'
                    ]);
                }
            }
        }
    }

    // Si no se movió ni cambió nada especial
    return response()->json([
        'success' => true,
        'message' => 'Información actualizada correctamente.',
        'item' => $item
    ]);
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
