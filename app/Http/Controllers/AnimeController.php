<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AnimeController extends Controller
{
    /**
     * Mostrar la página principal de animes con el buscador.
     */
    public function index(Request $request)
    {
        $perPage = 24; // Número de resultados por página
        $page = $request->input('page', 1);

        // Mínimo score que queremos mostrar
        $minScore = 60; // ejemplo: solo animes con promedio >=60

        // Consulta GraphQL
        $queryString = '
        query ($perPage: Int, $page: Int, $minScore: Int) {
            Page(page: $page, perPage: $perPage) {
                pageInfo {
                    total
                    currentPage
                    lastPage
                    hasNextPage
                }
                media(
                    type: ANIME, 
                    status_in: [RELEASING, FINISHED],
                    averageScore_greater: $minScore,
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

        $variables = [
            'page' => $page,
            'perPage' => $perPage,
            'minScore' => $minScore,
        ];

        $response = Http::post('https://graphql.anilist.co', [
            'query' => $queryString,
            'variables' => $variables,
        ]);

        $data = $response->json('data.Page');
        $animes = $data['media'] ?? [];
        $pageInfo = $data['pageInfo'] ?? [];

        // Filtrar géneros explícitos
        $excludedGenres = ['Ecchi', 'Hentai'];
        $animes = array_filter($animes, function ($anime) use ($excludedGenres) {
            return empty(array_intersect($anime['genres'], $excludedGenres));
        });

        // Mantener orden: RELEASING primero, luego FINISHED
        usort($animes, function ($a, $b) {
            if ($a['status'] === $b['status']) {
                // Dentro de mismo estado, ordenar por popularidad y luego por fecha
                return ($b['popularity'] ?? 0) - ($a['popularity'] ?? 0);
            }
            return ($a['status'] === 'RELEASING') ? -1 : 1;
        });

        // Obtener géneros para filtros (sin explícitos)
        $genresResponse = Http::post('https://graphql.anilist.co', [
            'query' => 'query { GenreCollection }',
        ]);
        $genres = array_diff($genresResponse->json('data.GenreCollection') ?? [], $excludedGenres);

        // Inicializamos filtros vacíos
        $selectedGenre = '';
        $selectedSeason = '';
        $seasonYear = '';
        $selectedFormat = '';
        $selectedStatus = '';

        return view('animes.index', compact(
            'animes',
            'genres',
            'pageInfo',
            'selectedGenre',
            'selectedSeason',
            'seasonYear',
            'selectedFormat',
            'selectedStatus'
        ));
    }
    /**
     * Buscar animes en la API de AniList con filtros.
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        $genre = $request->input('genre');
        $season = $request->input('season');
        $seasonYear = $request->input('seasonYear');
        $format = $request->input('format');
        $status = $request->input('status');
        $page = $request->input('page', 1); // Página actual, por defecto 1
        $perPage = 24; // Número de resultados por página

        // Construimos los filtros dinámicamente para GraphQL
        $filters = [];
        if ($genre) $filters['genre_in'] = [$genre];
        if ($season) $filters['season'] = $season;
        if ($seasonYear) $filters['seasonYear'] = (int)$seasonYear;
        if ($format) $filters['format'] = $format;
        if ($status) $filters['status'] = $status;

        $queryString = '
        query ($search: String, $perPage: Int, $page: Int, $genre_in: [String], $season: MediaSeason, $seasonYear: Int, $format: MediaFormat, $status: MediaStatus) {
            Page(page: $page, perPage: $perPage) {
                pageInfo {
                    total
                    currentPage
                    lastPage
                    hasNextPage
                }
                media(search: $search, type: ANIME, genre_in: $genre_in, season: $season, seasonYear: $seasonYear, format: $format, status: $status) {
                    id
                    title { romaji english native }
                    coverImage { large }
                    averageScore
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
        ], $filters);

        $response = Http::post('https://graphql.anilist.co', [
            'query' => $queryString,
            'variables' => $variables,
        ]);

        $data = $response->json('data.Page');
        $animes = $data['media'] ?? [];
        $pageInfo = $data['pageInfo'] ?? [];

        // Mantener filtros seleccionados
        $genresResponse = Http::post('https://graphql.anilist.co', [
            'query' => 'query { GenreCollection }',
        ]);
        $genres = $genresResponse->json('data.GenreCollection') ?? [];

        $selectedGenre = $genre;
        $selectedSeason = $season;
        $selectedYear = $seasonYear;
        $selectedFormat = $format;
        $selectedStatus = $status;

        return view('animes.index', compact(
            'animes',
            'query',
            'genres',
            'pageInfo',
            'selectedGenre',
            'selectedSeason',
            'seasonYear',
            'selectedFormat',
            'selectedStatus'
        ));
    }
}