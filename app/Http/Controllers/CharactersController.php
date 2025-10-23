<?php

namespace App\Http\Controllers;

use App\Services\AniListService;
use Illuminate\Http\Request;

class CharactersController extends Controller
{
    protected $aniList;

    public function __construct(AniListService $aniList)
    {
        $this->aniList = $aniList;
    }

    // Lista de personajes de un anime
    public function index($animeId)
    {
        $anime = $this->aniList->getAnimeById((int)$animeId);
        if (!$anime) abort(404, 'Anime no encontrado');

        $characters = $this->aniList->getCharactersByAnime((int)$animeId)['edges'] ?? [];

        // Filtrar MAIN primero
        $characters = collect($characters)
            ->sortByDesc(fn($c) => $c['role'] === 'MAIN');

        return view('animes.characters.index', compact('anime', 'characters'));
    }

    // Detalle de un personaje
    public function show($animeId, $characterId)
    {
        // Depuramos los IDs que llegan a la función
        dd([
            'animeId' => $animeId,
            'characterId' => $characterId,
        ]);

        $character = $this->aniList->getCharacterById((int)$characterId);
        if (!$character) abort(404, 'Personaje no encontrado');

        $anime = $this->aniList->getAnimeById((int)$animeId);

        return view('animes.characters.show', compact('anime', 'character'));
    }
}
