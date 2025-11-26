<?php

namespace App\Http\Controllers;

use App\Services\AniListService;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    protected $aniList;

    public function __construct(AniListService $aniList)
    {
        $this->aniList = $aniList;
    }

    public function index()
    {
        $year = date('Y');
        $seasonMap = ['WINTER', 'SPRING', 'SUMMER', 'FALL'];
        $currentSeason = $seasonMap[floor((date('n') % 12) / 3)];

        // Carrusel: animes en emisión y ordenados por score
        $currentAnimes = Cache::remember("anime_carrusel_{$year}_{$currentSeason}", 3600, function () use ($year, $currentSeason) {
            return $this->aniList->searchAnimeBySeason([
                'status' => 'RELEASING',
                'sort' => ['SCORE_DESC'],
                'seasonYear' => $year,
                'season' => $currentSeason,
                'page' => 1,
                'perPage' => 8,
            ]);
        });

        // Más populares de la temporada
        $popularAnimes = Cache::remember("anime_populares_{$year}_{$currentSeason}", 3600, function () use ($year, $currentSeason) {
            return $this->aniList->searchAnimeBySeason([
                'seasonYear' => $year,
                'season' => $currentSeason,
                'sort' => ['POPULARITY_DESC'],
                'page' => 1,
                'perPage' => 12,
            ]);
        });

        // Estrenos
        $estrenos = Cache::remember("anime_estrenos", 3600, function () {
            return $this->aniList->searchAnimeBySeason([
                'status' => 'RELEASING',
                'sort' => ['START_DATE_DESC'],
                'minScore' => 1,
                'page' => 1,
                'perPage' => 12,
            ]);
        });

        // Mejor ranqueados
        $mejorRanqueados = Cache::remember("anime_mejor_ranqueados", 3600, function () {
            return $this->aniList->searchAnimeBySeason([
                'sort' => ['SCORE_DESC'],
                'page' => 1,
                'perPage' => 12,
            ]);
        });

        // Próximos estrenos
        $proximosEstrenos = Cache::remember("anime_proximos_" . ($year + 1), 3600, function () use ($year) {
            return $this->aniList->searchAnimeBySeason([
                'status' => 'NOT_YET_RELEASED',
                'sort' => ['POPULARITY_DESC'],
                'seasonYear' => $year + 1,
                'page' => 1,
                'perPage' => 12,
            ]);
        });

        // Organizar secciones
        $sections = [
            'Más populares de la temporada' => $popularAnimes,
            'Estrenos' => $estrenos,
            'Mejor ranqueados' => $mejorRanqueados,
            'Próximos estrenos (' . ($year + 1) . ')' => $proximosEstrenos,
        ];

        // Retornar vista con cache en navegador (1 hora)
        return response()
            ->view('home', compact('sections', 'currentAnimes'))
            ->header('Cache-Control', 'public, max-age=3600');
    }
}