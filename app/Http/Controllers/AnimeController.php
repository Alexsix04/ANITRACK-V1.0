<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AnimeController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 100;
        $page = $request->input('page', 1);

        // Filtros
        $query = $request->input('query');
        $genre = $request->input('genre');
        $season = $request->input('season');
        $seasonYear = $request->input('seasonYear');
        $format = $request->input('format');
        $status = $request->input('status');

        $minScore = 60;

        // Construcción dinámica de filtros GraphQL
        $filters = [];
        if ($genre) $filters['genre_in'] = [$genre];
        if ($season) $filters['season'] = $season;
        if ($seasonYear) $filters['seasonYear'] = (int) $seasonYear;
        if ($format) $filters['format'] = $format;
        if ($status) $filters['status'] = $status;

        // Consulta GraphQL
        $queryString = '
        query ($search: String, $perPage: Int, $page: Int, $minScore: Int, 
               $genre_in: [String], $season: MediaSeason, $seasonYear: Int, 
               $format: MediaFormat, $status: MediaStatus) {
            Page(page: $page, perPage: $perPage) {
                pageInfo {
                    total
                    currentPage
                    lastPage
                    hasNextPage
                }
                media(
                    search: $search,
                    type: ANIME, 
                    status_in: [RELEASING, FINISHED],
                    averageScore_greater: $minScore,
                    genre_in: $genre_in,
                    season: $season,
                    seasonYear: $seasonYear,
                    format: $format,
                    status: $status,
                    sort: [POPULARITY_DESC, START_DATE_DESC]
                ) {
                    id
                    title { romaji english native }
                    coverImage { large }
                    averageScore
                    popularity
                    format
                    episodes
                    season
                    seasonYear
                    genres
                    status
                }
            }
        }';

        $variables = array_merge([
            'search' => $query,
            'page' => $page,
            'perPage' => $perPage,
            'minScore' => $minScore,
        ], $filters);

        $response = Http::post('https://graphql.anilist.co', [
            'query' => $queryString,
            'variables' => $variables,
        ]);

        $data = $response->json('data.Page');
        $animes = $data['media'] ?? [];
        $pageInfo = $data['pageInfo'] ?? [];

        // Excluir géneros explícitos
        $excludedGenres = ['Ecchi', 'Hentai'];
        $animes = array_filter($animes, function ($anime) use ($excludedGenres) {
            return empty(array_intersect($anime['genres'], $excludedGenres));
        });

        // Rellenar si faltan (por haber filtrado)
        $animes = array_slice($animes, 0, $perPage);

        // Ordenar RELEASING primero
        usort($animes, function ($a, $b) {
            if ($a['status'] === $b['status']) {
                return ($b['popularity'] ?? 0) - ($a['popularity'] ?? 0);
            }
            return ($a['status'] === 'RELEASING') ? -1 : 1;
        });

        // Obtener géneros válidos
        $genresResponse = Http::post('https://graphql.anilist.co', [
            'query' => 'query { GenreCollection }',
        ]);
        $genres = array_diff($genresResponse->json('data.GenreCollection') ?? [], $excludedGenres);

        // Si el request es AJAX → devolver JSON
        if ($request->ajax()) {
            return response()->json([
                'animes' => array_values($animes),
                'pageInfo' => $pageInfo,
            ]);
        }

        // Si es una carga normal → vista completa
        return view('animes.index', compact(
            'animes',
            'genres',
            'pageInfo',
            'query',
            'genre',
            'season',
            'seasonYear',
            'format',
            'status'
        ));
    }
}