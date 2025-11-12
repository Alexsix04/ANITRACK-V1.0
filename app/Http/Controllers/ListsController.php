<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AnimeList;
use App\Models\CharacterList;
use Illuminate\Support\Facades\Auth;

class ListsController extends Controller
{
    // Vista principal de listas
    public function index()
    {
        return view('listas.index');
    }
    // === Anime ===
    public function publicAnimeLists()
    {
        $publicAnimeLists = AnimeList::where('is_public', true)->with('user')->get();

        return view('listas.public_anime', compact('publicAnimeLists'));
    }


    public function saveAnimeList(AnimeList $list)
    {
        $user = Auth::user();
        $user->savedAnimeLists()->syncWithoutDetaching([$list->id]);
        return response()->json(['success' => true, 'message' => 'Lista guardada correctamente.']);
    }

    public function savedAnimeLists()
    {
        $lists = Auth::user()->savedAnimeLists()->with('items.anime')->get();
        return view('lists.saved_anime', compact('lists'));
    }

    // === Personajes ===
    public function publicCharacterLists()
    {
        $publicCharacterLists = CharacterList::where('is_public', true)->with('user')->get();
        return view('listas.public_characters', compact('publicCharacterLists'));
    }

    public function saveCharacterList(CharacterList $list)
    {
        $user = Auth::user();
        $user->savedCharacterLists()->syncWithoutDetaching([$list->id]);
        return response()->json(['success' => true, 'message' => 'Lista guardada correctamente.']);
    }

    public function savedCharacterLists()
    {
        $lists = Auth::user()->savedCharacterLists()->with('items.character')->get();
        return view('lists.saved_characters', compact('lists'));
    }
}
