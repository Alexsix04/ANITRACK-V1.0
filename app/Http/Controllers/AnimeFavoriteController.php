<?php

// app/Http/Controllers/AnimeFavoriteController.php
namespace App\Http\Controllers;

use App\Models\AnimeFavorite;
use Illuminate\Http\Request;

class AnimeFavoriteController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'anime_id' => 'required|integer',
            'anime_title' => 'required|string',
            'anime_image' => 'nullable|string',
        ]);

        auth()->user()->animeFavorites()->firstOrCreate([
            'anime_id' => $data['anime_id'],
        ], $data);

        return back()->with('success', 'Anime añadido a favoritos');
    }

    public function destroy($animeId)
    {
        auth()->user()->animeFavorites()
            ->where('anime_id', $animeId)
            ->delete();

        return back()->with('success', 'Anime eliminado de favoritos');
    }
}