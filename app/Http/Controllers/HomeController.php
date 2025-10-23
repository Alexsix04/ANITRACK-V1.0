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

        // Carrusel: animes en emisión y populares
        $currentAnimes = $this->aniList->searchAnimeBySeason([
            'status' => 'RELEASING',
            'sort' => ['SCORE_DESC'],
            'seasonYear' => $year,
            'season' => $currentSeason,
            'page' => 1,
            'perPage' => 8,
        ]);

        // Secciones
        $sections = [
            'Más populares de la temporada' => $this->aniList->searchAnimeBySeason([
                'seasonYear' => $year,
                'season' => $currentSeason,
                'sort' => ['POPULARITY_DESC'],
                'page' => 1,
                'perPage' => 12,
            ]),
            'Estrenos' => $this->aniList->searchAnimeBySeason([
                'status' => 'RELEASING',
                'sort' => ['START_DATE_DESC'],
                'minScore' => 1,
                'page' => 1,
                'perPage' => 12,
            ]),
            'Mejor ranqueados' => $this->aniList->searchAnimeBySeason([
                'sort' => ['SCORE_DESC'],
                'page' => 1,
                'perPage' => 12,
            ]),
            'Próximos estrenos (' . ($year + 1) . ')' => $this->aniList->searchAnimeBySeason([
                'status' => 'NOT_YET_RELEASED',
                'sort' => ['POPULARITY_DESC'],
                'seasonYear' => $year + 1,
                'page' => 1,
                'perPage' => 12,
            ]),
        ];

        return view('home', compact('sections', 'currentAnimes'));
    }
}