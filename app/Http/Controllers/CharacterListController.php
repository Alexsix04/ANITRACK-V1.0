<?php

namespace App\Http\Controllers;

use App\Models\CharacterList;
use App\Models\CharacterListItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CharacterListController extends Controller
{
    /**
     * Muestra las listas del usuario actual (para profile.index)
     */
    public function myLists()
    {
        $user = Auth::user();

        $lists = $user->characterLists()
            ->with('items.character', 'items.anime')
            ->get();

        return view('profile.index', compact('lists'));
    }

    /**
     * Añadir un personaje a una lista del usuario
     */
    public function addCharacterToList(Request $request)
{
    $validated = $request->validate([
        'character_anilist_id' => 'required|integer',
        'character_name' => 'required|string',
        'character_image' => 'nullable|string',
        'anime_anilist_id' => 'nullable|integer',
        'anime_title' => 'nullable|string',
        'anime_image' => 'nullable|string',
        'list_name' => 'required|string',
        'score' => 'nullable|integer|min:0|max:10',
        'notes' => 'nullable|string|max:1000',
    ]);

    $user = Auth::user();

    // 1️⃣ Obtener o crear anime local
    if (!empty($validated['anime_anilist_id'])) {
        $anime = \App\Models\Anime::firstOrCreate(
            ['anilist_id' => $validated['anime_anilist_id']],
            ['title' => $validated['anime_title'] ?? '', 'cover_image' => $validated['anime_image'] ?? null]
        );
    }

    // 2️⃣ Obtener o crear personaje local
    $character = \App\Models\Character::firstOrCreate(
    ['anilist_id' => $validated['character_anilist_id']],
    [
        'name' => $validated['character_name'],
        'image_url' => $validated['character_image'] ?? null,
        'anime_id' => $anime->id ?? null,
        'anime_anilist_id' => $validated['anime_anilist_id'] ?? null, 
    ]
);


    // 3️⃣ Obtener lista destino
    $list = $user->characterLists()->where('name', $validated['list_name'])->firstOrFail();

    // 4️⃣ Evitar duplicados
    $exists = $list->items()->where('character_id', $character->id)->exists();
    if ($exists) {
        return redirect()->back()->with('warning', 'Este personaje ya está en la lista.');
    }

    // 5️⃣ Crear item en character_list_items
    $list->items()->create([
    'user_id'         => $user->id,
    'list_id'         => $list->id,
    'character_id'    => $character->id,
    'anime_id'        => $anime->id ?? null,
    'anime_anilist_id'=> $validated['anime_anilist_id'] ,
    'anilist_id'      => $validated['character_anilist_id'],
    'score'           => $validated['score'] ?? null,
    'notes'           => $validated['notes'] ?? null,
]);


    return redirect()->back()->with('success', 'Personaje añadido a tu lista correctamente.');
}

    /**
     * Actualizar los datos de un personaje dentro de una lista
     */
    public function updateCharacterInList(Request $request, CharacterListItem $item)
    {
        $validated = $request->validate([
            'score' => 'nullable|integer|min:0|max:10',
            'notes' => 'nullable|string|max:1000',
        ]);

        $item->update([
            'score' => $validated['score'] ?? $item->score,
            'notes' => $validated['notes'] ?? $item->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Personaje actualizado correctamente.',
            'item' => $item,
        ]);
    }

    /**
     * Eliminar un personaje de una lista
     */
    public function destroy($id)
    {
        $item = CharacterListItem::find($id);

        if (!$item) {
            return response()->json(['message' => '❌ El personaje no se encontró en tu lista.'], 404);
        }

        $item->delete();

        return response()->json(['message' => '✅ Personaje eliminado correctamente.']);
    }

    /**
     * Crear una nueva lista (formulario normal)
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_public' => 'required|boolean',
            'description' => 'nullable|string|max:1000',
        ]);

        Auth::user()->characterLists()->create($validated);

        return redirect()->back()->with('success', 'Lista creada correctamente.');
    }

    /**
     * Crear lista mediante AJAX
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_public' => 'required|boolean',
            'description' => 'nullable|string|max:1000',
        ]);

        $existing = CharacterList::where('user_id', auth()->id())
            ->where('name', $validated['name'])
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Ya tienes una lista con ese nombre.',
            ], 409);
        }

        $list = CharacterList::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
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