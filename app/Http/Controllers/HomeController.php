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

        // Función interna para asegurar la cantidad deseada
        $fetchAnimes = function (array $params, int $desired) {
            $page = 1;
            $animes = [];

            while (count($animes) < $desired) {
                $result = $this->aniList->searchAnimeBySeason(array_merge($params, [
                    'page' => $page,
                ]));

                if (empty($result)) break;

                $animes = array_merge($animes, $result);
                $page++;
            }

            return array_slice($animes, 0, $desired);
        };

        // Carrusel: animes en emisión y ordenados por score (8)
        $currentAnimes = $fetchAnimes([
            'status' => 'RELEASING',
            'sort' => ['SCORE_DESC'],
            'seasonYear' => $year,
            'perPage' => 8,
        ], 8);

        //dd($currentAnimes);

        // Más populares de la temporada (12)
        $popularAnimes = $fetchAnimes([
            'seasonYear' => $year,
            'season' => $currentSeason,
            'sort' => ['POPULARITY_DESC'],
            'perPage' => 12,
        ], 12);

        // Estrenos (12)
        $estrenos = $fetchAnimes([
            'status' => 'RELEASING',
            'sort' => ['START_DATE_DESC'],
            'minScore' => 1,
            'perPage' => 12,
        ], 12);

        // Mejor ranqueados (12)
        $mejorRanqueados = $fetchAnimes([
            'sort' => ['SCORE_DESC'],
            'perPage' => 12,
        ], 12);

        // Próximos estrenos (12)
        $proximosEstrenos = $fetchAnimes([
            'status' => 'NOT_YET_RELEASED',
            'sort' => ['POPULARITY_DESC'],
            'seasonYear' => $year + 1,
            'perPage' => 12,
        ], 12);

        // Organizar secciones
        $sections = [
            'Más populares de la temporada' => $popularAnimes,
            'Estrenos' => $estrenos,
            'Mejor ranqueados' => $mejorRanqueados,
            'Próximos estrenos (' . ($year + 1) . ')' => $proximosEstrenos,
        ];

        return view('home', compact('sections', 'currentAnimes'));
    }
}
