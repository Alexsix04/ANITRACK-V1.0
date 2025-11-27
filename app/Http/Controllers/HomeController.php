<?php

namespace App\Http\Controllers;

use App\Services\AniListService;

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
        $currentAnimes = $this->aniList->searchAnimeBySeason([
            'status' => 'RELEASING',
            'sort' => ['SCORE_DESC'],
            'seasonYear' => $year,
            'season' => $currentSeason,
            'page' => 1,
            'perPage' => 8,
        ]);

        // Más populares de la temporada
        $popularAnimes = $this->aniList->searchAnimeBySeason([
            'seasonYear' => $year,
            'season' => $currentSeason,
            'sort' => ['POPULARITY_DESC'],
            'page' => 1,
            'perPage' => 12,
        ]);

        // Estrenos
        $estrenos = $this->aniList->searchAnimeBySeason([
            'status' => 'RELEASING',
            'sort' => ['START_DATE_DESC'],
            'minScore' => 1,
            'page' => 1,
            'perPage' => 12,
        ]);

        // Mejor ranqueados
        $mejorRanqueados = $this->aniList->searchAnimeBySeason([
            'sort' => ['SCORE_DESC'],
            'page' => 1,
            'perPage' => 12,
        ]);

        // Próximos estrenos
        $proximosEstrenos = $this->aniList->searchAnimeBySeason([
            'status' => 'NOT_YET_RELEASED',
            'sort' => ['POPULARITY_DESC'],
            'seasonYear' => $year + 1,
            'page' => 1,
            'perPage' => 12,
        ]);

        // Organizar secciones
        $sections = [
            'Más populares de la temporada' => $popularAnimes,
            'Estrenos' => $estrenos,
            'Mejor ranqueados' => $mejorRanqueados,
            'Próximos estrenos (' . ($year + 1) . ')' => $proximosEstrenos,
        ];

        // Retornar la vista normalmente
        return view('home', compact('sections', 'currentAnimes'));
    }
}