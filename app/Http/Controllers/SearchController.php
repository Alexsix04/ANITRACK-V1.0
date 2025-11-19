<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AnilistService;
use App\Models\User;
use App\Models\AnimeList;
use App\Models\CharacterList;

class SearchController extends Controller
{
    public function search(Request $request, AnilistService $anilist)
    {
        $query = trim($request->input('q'));

        if (!$query) {
            return response()->json([
                'animes'      => [],
                'characters'  => [],
                'users'       => [],
                'anime_lists' => [],
                'char_lists'  => [],
            ]);
        }

        // === API AniList ===
        $animes     = $anilist->searchAnimesByName($query);
        $characters = $anilist->searchCharactersByName($query); // ahora incluye 'anime_id'

        // === Modelos locales ===
        $users = User::where('name', 'LIKE', "%$query%")
            ->limit(8)
            ->get()
            ->map(function ($user) {
                return [
                    'id'     => $user->id,
                    'name'   => $user->name,
                    'avatar' => $user->avatar_url,  // URL absoluta del avatar
                ];
            })
            ->toArray();

        $animeLists = AnimeList::where('name', 'LIKE', "%$query%")
            ->limit(8)
            ->get(['id', 'name'])
            ->toArray();

        $charLists = CharacterList::where('name', 'LIKE', "%$query%")
            ->limit(8)
            ->get(['id', 'name'])
            ->toArray();

        return response()->json([
            'animes'      => $animes,
            'characters'  => $characters,
            'users'       => $users,
            'anime_lists' => $animeLists,
            'char_lists'  => $charLists,
        ]);
    }
}
