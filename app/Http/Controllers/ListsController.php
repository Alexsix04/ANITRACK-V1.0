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
    // Vista principal de listas
    public function index()
    {
        $userId = Auth::id();

        $publicAnimeLists = \App\Models\AnimeList::where('is_public', true)
            ->with(['user', 'items.anime'])
            ->latest()
            ->take(8)
            ->get()
            ->map(function ($list) use ($userId) {
                // Likes
                $list->is_liked = $userId
                    ? \App\Models\AnimeListLike::where('user_id', $userId)
                    ->where('anime_list_id', $list->id)
                    ->exists()
                    : false;

                $list->likes_count = \App\Models\AnimeListLike::where('anime_list_id', $list->id)->count();
                return $list;
            });

        $publicCharacterLists = \App\Models\CharacterList::where('is_public', true)
            ->with(['user', 'items.character'])
            ->latest()
            ->take(8)
            ->get()
            ->map(function ($list) use ($userId) {
                // Likes
                $list->is_liked = $userId
                    ? \App\Models\CharacterListLike::where('user_id', $userId)
                    ->where('character_list_id', $list->id)
                    ->exists()
                    : false;

                $list->likes_count = \App\Models\CharacterListLike::where('character_list_id', $list->id)->count();
                return $list;
            });

        // Guardados siguen igual
        $savedAnimeIds = [];
        $savedCharacterIds = [];

        if ($userId) {
            $savedAnimeIds = Auth::user()->savedAnimeLists()->pluck('anime_list_id')->toArray();
            $savedCharacterIds = Auth::user()->savedCharacterLists()->pluck('character_list_id')->toArray();
        }

        return view(
            'listas.index',
            compact('publicAnimeLists', 'publicCharacterLists', 'savedAnimeIds', 'savedCharacterIds')
        );
    }
    // === Anime ===
    public function publicAnimeLists()
    {
        $user = Auth::user();

        $publicAnimeLists = \App\Models\AnimeList::where('is_public', true)
            ->with(['user', 'items.anime'])
            ->get()
            ->map(function ($list) use ($user) {
                // Estado de guardado
                $list->is_saved = $user
                    ? \App\Models\SavedAnimeList::where('user_id', $user->id)
                    ->where('anime_list_id', $list->id)
                    ->exists()
                    : false;

                // Contador total de likes
                $list->likes_count = \App\Models\AnimeListLike::where('anime_list_id', $list->id)->count();

                // Estado de like para el usuario
                $list->is_liked = $user
                    ? \App\Models\AnimeListLike::where('user_id', $user->id)
                    ->where('anime_list_id', $list->id)
                    ->exists()
                    : false;

                return $list;
            });

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
    public function publicCharacterLists()
    {
        $user = Auth::user();
        $userId = $user ? $user->id : null;

        $publicCharacterLists = CharacterList::where('is_public', true)
            ->with(['user', 'items.character'])
            ->get()
            ->map(function ($list) use ($userId) {
                // Guardado
                $list->is_saved = $userId
                    ? $list->savedByUsers()->where('user_id', $userId)->exists()
                    : false;

                // Contador total de likes
                $list->likes_count = CharacterListLike::where('character_list_id', $list->id)->count();

                // Estado de like del usuario
                $list->is_liked = $userId
                    ? CharacterListLike::where('user_id', $userId)
                    ->where('character_list_id', $list->id)
                    ->exists()
                    : false;

                return $list;
            });

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
