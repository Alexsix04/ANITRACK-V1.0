<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AnimeList;
use App\Models\SavedAnimeList;
use App\Models\CharacterList;
use Illuminate\Support\Facades\Auth;

class ListsController extends Controller
{
    // Vista principal de listas
    public function index()
    {
        $publicAnimeLists = \App\Models\AnimeList::where('is_public', true)
            ->with(['user', 'items.anime'])
            ->latest()
            ->take(8)
            ->get();

        $publicCharacterLists = \App\Models\CharacterList::where('is_public', true)
            ->with(['user', 'items.character'])
            ->latest()
            ->take(8)
            ->get();

        // Traer listas guardadas del usuario para marcar botones
        $savedAnimeIds = [];
        $savedCharacterIds = [];

        if (Auth::check()) {
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
                $list->is_saved = $user
                    ? SavedAnimeList::where('user_id', $user->id)
                    ->where('anime_list_id', $list->id)
                    ->exists()
                    : false;
                return $list;
            });

        return view('listas.public_anime', compact('publicAnimeLists'));
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
        $userId = Auth::id();

        // Traemos listas públicas y añadimos info de si el usuario ya las guardó
        $publicCharacterLists = CharacterList::where('is_public', true)
            ->with('user')
            ->get()
            ->map(function ($list) use ($userId) {
                $list->is_saved = $userId ? $list->savedByUsers()->where('user_id', $userId)->exists() : false;
                return $list;
            });

        return view('listas.public_characters', compact('publicCharacterLists'));
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
