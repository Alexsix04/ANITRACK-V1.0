<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CharacterFavorite;

class CharacterFavoriteController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();

        CharacterFavorite::firstOrCreate([
            'user_id' => $user->id,
            'character_id' => $request->character_id,
        ], [
            'character_name' => $request->character_name,
            'character_image' => $request->character_image,
        ]);

        return back()->with('success', 'Personaje añadido a favoritos');
    }

    public function destroy($character_id)
    {
        $favorite = CharacterFavorite::where('user_id', Auth::id())
            ->where('character_id', $character_id)
            ->first();

        if ($favorite) {
            $favorite->delete();
        }

        return back()->with('success', 'Personaje eliminado de favoritos');
    }
}