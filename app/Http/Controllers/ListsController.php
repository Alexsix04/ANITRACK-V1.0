<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AnimeList;
use App\Models\SavedAnimeList;
use App\Models\AnimeListLike;
use App\Models\CharacterList;
use App\Models\CharacterListLike;
use Illuminate\Support\Facades\Auth;

class ListsController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // === LISTAS DE ANIME ===
        $publicAnimeLists = \App\Models\AnimeList::where('is_public', true)
            ->whereHas('items')
            ->with(['user', 'items.anime'])
            ->withCount('likes') // ← CREA likes_count AUTOMÁTICAMENTE
            ->orderBy('likes_count', 'desc') // ← ORDENA POR MÁS LIKES
            ->latest() // (opcional: puedes quitarlo si solo quieres ordenar por likes)
            ->take(8)
            ->get()
            ->map(function ($list) use ($userId) {

                // Estado de like del usuario
                $list->is_liked = $userId
                    ? \App\Models\AnimeListLike::where('user_id', $userId)
                    ->where('anime_list_id', $list->id)
                    ->exists()
                    : false;

                return $list;
            });

        // === LISTAS DE PERSONAJES ===
        $publicCharacterLists = \App\Models\CharacterList::where('is_public', true)
            ->whereHas('items')
            ->with(['user', 'items.character'])
            ->withCount('likes') // ← CREA likes_count AUTOMÁTICAMENTE
            ->orderBy('likes_count', 'desc') // ← ORDENA POR MÁS LIKES
            ->latest()
            ->take(8)
            ->get()
            ->map(function ($list) use ($userId) {

                // Estado de like del usuario
                $list->is_liked = $userId
                    ? \App\Models\CharacterListLike::where('user_id', $userId)
                    ->where('character_list_id', $list->id)
                    ->exists()
                    : false;

                return $list;
            });

        // Guardados siguen igual
        $savedAnimeIds = $userId ? Auth::user()->savedAnimeLists()->pluck('anime_list_id')->toArray() : [];
        $savedCharacterIds = $userId ? Auth::user()->savedCharacterLists()->pluck('character_list_id')->toArray() : [];

        return view(
            'listas.index',
            compact('publicAnimeLists', 'publicCharacterLists', 'savedAnimeIds', 'savedCharacterIds')
        );
    }
    // === Anime ===
    public function publicAnimeLists(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        $query = \App\Models\AnimeList::where('is_public', true)
            ->whereHas('items') // Oculta listas vacías
            ->with(['user', 'items.anime'])
            ->withCount('likes') // Genera automáticamente likes_count
            ->orderBy('likes_count', 'desc');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $publicAnimeLists = $query->get()
            ->map(function ($list) use ($user) {
                $list->is_saved = $user
                    ? \App\Models\SavedAnimeList::where('user_id', $user->id)
                    ->where('anime_list_id', $list->id)
                    ->exists()
                    : false;

                $list->is_liked = $user
                    ? \App\Models\AnimeListLike::where('user_id', $user->id)
                    ->where('anime_list_id', $list->id)
                    ->exists()
                    : false;

                return $list;
            });

        // Si es AJAX, devolvemos HTML listo para reemplazar
        if ($request->ajax()) {
            $html = '';
            if ($publicAnimeLists->isEmpty()) {
                $html .= '<p>No se encontraron listas.</p>';
            } else {
                foreach ($publicAnimeLists as $list) {
                    $html .= '<div class="anime-list-card border p-3 mb-3">';
                    $html .= '<h3>' . $list->name . '</h3>';
                    $html .= '<p>Likes: ' . $list->likes_count . '</p>';
                    $html .= '<p>Creado por: ' . $list->user->name . '</p>';
                    $html .= '</div>';
                }
            }
            return $html;
        }

        return view('listas.public_anime', compact('publicAnimeLists'));
    }

    public function likeAnimeList(AnimeList $list)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $existing = AnimeListLike::where('user_id', $user->id)
            ->where('anime_list_id', $list->id)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            AnimeListLike::create([
                'user_id' => $user->id,
                'anime_list_id' => $list->id,
            ]);
        }

        $likesCount = AnimeListLike::where('anime_list_id', $list->id)->count();

        return response()->json([
            'liked' => !$existing,
            'likes_count' => $likesCount,
        ]);
    }

    public function saveAnimeList(AnimeList $list)
    {
        $user = Auth::user();

        $existing = SavedAnimeList::where('user_id', $user->id)
            ->where('anime_list_id', $list->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['saved' => false]);
        }

        SavedAnimeList::create([
            'user_id' => $user->id,
            'anime_list_id' => $list->id,
            'owner_id' => $list->user_id,
            'owner_name' => $list->user->name ?? 'Desconocido',
        ]);

        return response()->json(['saved' => true]);
    }

    public function savedAnimeLists()
    {
        $lists = Auth::user()->savedAnimeLists()->with('items.anime')->get();
        return view('lists.saved_anime', compact('lists'));
    }

    // === Personajes ===
    public function publicCharacterLists(Request $request)
    {
        $user = Auth::user();
        $userId = $user ? $user->id : null;
        $search = $request->input('search');

        $query = \App\Models\CharacterList::where('is_public', true)
            ->whereHas('items') // Oculta listas vacías
            ->with(['user', 'items.character'])
            ->withCount('likes') // Genera likes_count automáticamente
            ->orderBy('likes_count', 'desc');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $publicCharacterLists = $query->get()
            ->map(function ($list) use ($userId) {
                // Guardado
                $list->is_saved = $userId
                    ? $list->savedByUsers()->where('user_id', $userId)->exists()
                    : false;

                // Estado de like del usuario
                $list->is_liked = $userId
                    ? \App\Models\CharacterListLike::where('user_id', $userId)
                    ->where('character_list_id', $list->id)
                    ->exists()
                    : false;

                return $list;
            });

        // Si es AJAX, devolvemos HTML resumido para reemplazar
        if ($request->ajax()) {
            $html = '';
            if ($publicCharacterLists->isEmpty()) {
                $html .= '<p class="text-gray-500 text-center py-10">No se encontraron listas.</p>';
            } else {
                foreach ($publicCharacterLists as $list) {
                    $html .= '<div class="character-list-card border p-3 mb-3">';
                    $html .= '<h3>' . $list->name . '</h3>';
                    $html .= '<p>Likes: ' . $list->likes_count . '</p>';
                    $html .= '<p>Creado por: ' . ($list->user->name ?? 'Desconocido') . '</p>';
                    $html .= '</div>';
                }
            }
            return $html;
        }

        return view('listas.public_characters', compact('publicCharacterLists'));
    }

    public function likeCharacterList(CharacterList $list)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $existing = CharacterListLike::where('user_id', $user->id)
            ->where('character_list_id', $list->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            CharacterListLike::create([
                'user_id' => $user->id,
                'character_list_id' => $list->id,
            ]);
            $liked = true;
        }

        $likesCount = CharacterListLike::where('character_list_id', $list->id)->count();

        return response()->json([
            'liked' => $liked,
            'likes_count' => $likesCount,
        ]);
    }

    public function saveCharacterList(CharacterList $list)
    {
        $user = Auth::user();

        if ($user->savedCharacterLists()->where('character_list_id', $list->id)->exists()) {
            // Ya estaba guardada → eliminar
            $user->savedCharacterLists()->detach($list->id);
            $saved = false;
        } else {
            // No estaba guardada → agregar
            $user->savedCharacterLists()->attach($list->id, [
                'owner_id' => $list->user_id,
                'owner_name' => $list->user->name
            ]);
            $saved = true;
        }

        return response()->json([
            'success' => true,
            'saved' => $saved
        ]);
    }

    public function savedCharacterLists()
    {
        $lists = Auth::user()->savedCharacterLists()->with('items.character')->get();
        return view('lists.saved_characters', compact('lists'));
    }
}
