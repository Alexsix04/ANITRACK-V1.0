<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    public function index()
    {
        $endpoint = 'https://graphql.anilist.co';

        $fetch = function ($query, $variables = []) use ($endpoint) {
            $response = Http::post($endpoint, [
                'query' => $query,
                'variables' => $variables,
            ]);
            return $response->json('data.Page.media') ?? [];
        };

        $query = '
        query ($page: Int, $perPage: Int, $status: MediaStatus, $sort: [MediaSort], $seasonYear: Int, $season: MediaSeason, $minScore: Int) {
        Page(page: $page, perPage: $perPage) {
        media(type: ANIME, status: $status, sort: $sort, seasonYear: $seasonYear, season: $season, averageScore_greater: $minScore) {
            id
            title { romaji }
            coverImage { large }
            averageScore
            season
            seasonYear
            }
        }
    }';


        $year = date('Y');
        $seasonMap = ['WINTER', 'SPRING', 'SUMMER', 'FALL'];
        $currentSeason = $seasonMap[floor((date('n') % 12) / 3)];

        $sections = [

            'Más populares de la temporada' => $fetch($query, [
                'page' => 1,
                'perPage' => 12,
                'seasonYear' => $year,
                'season' => $currentSeason,
                'sort' => ['POPULARITY_DESC'],
            ]),
            'Estrenos' => $fetch($query, [
                'page' => 1,
                'perPage' => 12,
                'status' => 'RELEASING',
                'sort' => ['START_DATE_DESC'],
                'minScore' => 1, // Solo animes con puntuación mayor a 0
            ]),
                'Mejor ranqueados' => $fetch($query, [
                'page' => 1,
                'perPage' => 12,
                'sort' => ['SCORE_DESC'],
            ]),
            'Estrenos' => $fetch($query, [
                'page' => 1,
                'perPage' => 12,
                'status' => 'RELEASING',
                'sort' => ['START_DATE_DESC'],
                'minScore' => 1, // Solo animes con puntuación mayor a 0
            ]),
            'Próximos estrenos (' . ($year + 1) . ')' => $fetch($query, [
                'page' => 1,
                'perPage' => 12,
                'status' => 'NOT_YET_RELEASED',
                'sort' => ['POPULARITY_DESC'], // Ordenados por popularidad
                'seasonYear' => $year + 1,
            ]),

        ];

        return view('home', compact('sections'));
    }
}
