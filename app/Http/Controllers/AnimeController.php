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

        // Filtro inicial desde home
        $filterFromHome = $request->input('filter');

        // Filtros del formulario
        $query = $request->input('query');
        $genre = $request->input('genre');
        $season = $request->input('season');
        $seasonYear = $request->input('seasonYear');
        $format = $request->input('format');
        $status = $request->input('status');

        // Construcción de filtros GraphQL
        $filters = [];
        if ($genre) $filters['genre_in'] = [$genre];
        if ($season) $filters['season'] = $season;
        if ($seasonYear) $filters['seasonYear'] = (int) $seasonYear;
        if ($format) $filters['format'] = $format;
        if ($status) $filters['status'] = $status;

        // Aplicar filter de home solo si no hay filtros manuales ni búsqueda
        $applyFilter = $filterFromHome && !$query && !$genre && !$season && !$seasonYear && !$format && !$status;
        $filter = $applyFilter ? $filterFromHome : null;

        $title = $applyFilter ? ucfirst(str_replace('-', ' ', $filterFromHome)) : 'Buscar Animes';

        // Filtros predefinidos según $filter
        if ($applyFilter && $filter) {
            switch ($filter) {
                case 'mas-populares-de-la-temporada':
                    $month = now()->month;
                    if ($month >= 3 && $month <= 5) $filters['season'] = 'SPRING';
                    elseif ($month >= 6 && $month <= 8) $filters['season'] = 'SUMMER';
                    elseif ($month >= 9 && $month <= 11) $filters['season'] = 'FALL';
                    else $filters['season'] = 'WINTER';
                    $filters['seasonYear'] = now()->year;
                    $filters['sort'] = ['POPULARITY_DESC'];
                    break;
                case 'estrenos':
                case 'en-emision':
                    $filters['status'] = 'RELEASING';
                    $filters['sort'] = ['START_DATE_DESC', 'POPULARITY_DESC'];
                    $minScore = 1;
                    break;
                case 'mejor-ranqueados':
                case 'mejor-valorados':
                    $filters['sort'] = ['SCORE_DESC'];
                    $filters['status'] = 'FINISHED';
                    break;
                case 'proximos-estrenos':
                case 'proximos-estrenos-2026':
                    $filters['status'] = 'NOT_YET_RELEASED';
                    $filters['sort'] = ['POPULARITY_DESC'];
                    $filters['seasonYear'] = now()->year + 1;
                    $minScore = 1;
                    break;
            }
        }

        // Ignorar filter si hay búsqueda manual o filtros activos
        if ($query || $genre || $season || $seasonYear || $format || $status) {
            $filter = null;
        }

        $sort = $filters['sort'] ?? ['POPULARITY_DESC', 'START_DATE_DESC'];

        // Consulta GraphQL
        $queryString = '
query ($search: String, $perPage: Int, $page: Int, $minScore: Int, 
       $genre_in: [String], $season: MediaSeason, $seasonYear: Int, 
       $format: MediaFormat, $status: MediaStatus, $sort: [MediaSort]) {
    Page(page: $page, perPage: $perPage) {
        pageInfo { total currentPage lastPage hasNextPage }
        media(
            search: $search,
            type: ANIME,
            averageScore_greater: $minScore,
            genre_in: $genre_in,
            season: $season,
            seasonYear: $seasonYear,
            format: $format,
            status: $status,
            sort: $sort
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
            //'minScore' => $minScore ?? 0,
            'sort' => $sort
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
        $animes = array_filter($animes, fn($a) => empty(array_intersect($a['genres'], $excludedGenres)));

        // Ordenamiento opcional
        if (in_array($filter, ['mejor-ranqueados', 'mejor-valorados'])) {
            usort($animes, fn($a, $b) => ($b['averageScore'] ?? 0) <=> ($a['averageScore'] ?? 0));
        } else {
            usort($animes, fn($a, $b) => ($b['popularity'] ?? 0) <=> ($a['popularity'] ?? 0));
        }

        // Respuesta AJAX
        if ($request->ajax()) {
            return response()->json([
                'animes' => array_values($animes),
                'pageInfo' => $pageInfo,
            ]);
        }

        // Vista Blade
        $genresResponse = Http::post('https://graphql.anilist.co', [
            'query' => 'query { GenreCollection }',
        ]);
        $genres = array_diff($genresResponse->json('data.GenreCollection') ?? [], $excludedGenres);

        return view('animes.index', compact(
            'animes',
            'genres',
            'filter',
            'title',
            'query',
            'genre',
            'season',
            'seasonYear',
            'format',
            'status',
            'pageInfo'
        ));
    }

   
    /**
     * Muestra los detalles de un anime específico.
     */
    public function show($id)
    {
        $queryString = '
    query ($id: Int) {
        Media(id: $id, type: ANIME) {
            id
            title { romaji english native }
            coverImage { large medium }
            bannerImage
            description
            averageScore
            popularity
            format
            episodes
            season
            seasonYear
            genres
            status
        }
    }';

        $variables = ['id' => (int)$id];

        $response = Http::post('https://graphql.anilist.co', [
            'query' => $queryString,
            'variables' => $variables,
        ]);

        $anime = $response->json('data.Media');

        if (!$anime) {
            abort(404, 'Anime no encontrado');
        }

        return view('animes.show', compact('anime'));
    }
}
