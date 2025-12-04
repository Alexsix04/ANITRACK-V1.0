<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AnimeThemesService;
use App\Services\AnilistService;

class AnimeThemesController extends Controller
{
    protected AnimeThemesService $animeThemesService;
    protected AnilistService $aniList;

    public function __construct(AnimeThemesService $animeThemesService, AnilistService $aniList)
    {
        $this->animeThemesService = $animeThemesService;
        $this->aniList = $aniList;
    }

    /**
     * Mostrar los temas de un anime junto con información básica del anime
     *
     * @param int $animeId AniList ID
     * @return \Illuminate\View\View
     */
    public function index(int $animeId)
    {
        // 1️⃣ Obtener información del anime desde AniList
        $anime = $this->aniList->getAnimeById($animeId);
        if (!$anime) {
            abort(404, 'Anime no encontrado');
        }

        // 2️⃣ Obtener los temas del anime desde AnimeThemes
        $themes = $this->animeThemesService->getThemesByAnilistId($animeId);
        // Debe devolver array de animethemes con: type, sequence y song (title, id)

        // 3️⃣ Pasar todo a la vista
        return view('animes.themes.index', [
            'anime' => $anime,       // Información completa del anime
            'themes' => $themes,     // Temas del anime
            'anilistId' => $animeId, // Para mostrar en la cabecera
        ]);
    }
}
