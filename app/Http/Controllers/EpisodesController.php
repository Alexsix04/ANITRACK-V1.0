<?php

namespace App\Http\Controllers;

use App\Services\AniListService;
use Illuminate\Http\Request;

class EpisodesController extends Controller
{
    protected $aniList;

    public function __construct(AniListService $aniList)
    {
        $this->aniList = $aniList;
    }

    /**
     * Lista los episodios de un anime
     */
    public function index($animeId)
    {
        $anime = $this->aniList->getAnimeById((int)$animeId);
        if (!$anime) abort(404, 'Anime no encontrado');

        // Obtener el número total de episodios
        $episodes = $this->aniList->getEpisodesByAnime((int)$animeId);

        return view('animes.episodes.index', compact('anime', 'episodes'));
    }
}
